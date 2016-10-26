<?php

namespace xmlSitemapGenerator;

include_once 'settingsModels.php';
include_once 'upgrader.php';
 
	define ( "XSG_PLUGIN_VERSION" , "1.3.0");
	define ( "XSG_PLUGIN_NAME" , "www-xml-sitemap-generator-org"); 
	define ( "XSG_RULES_VERSION" , "0003"); // increment this if the rewrite rules ever change.
	define ( "XSG_RULES_OPTION_NAME" , "wpXSG_rewrite_done"); 
 
// settings for general operation and rendering


	
class core {
	
	public static function pluginFilename() {
		return plugin_basename(myPluginFile());
	}

	public static function pluginVersion() {
		// getting version from file was causing issues.
		return XSG_PLUGIN_VERSION;
	}
 
 
	
	public static function activatePlugin(){
		
		self::upgradeDatabase();

		self::addRewriteHooks();
		self::activateRewriteRules();
		flush_rewrite_rules();
		
		add_option( "wpXSG_MapId", uniqid("",true) );
		update_option( "xmsg_LastPing", 0 );
		
		self::updateStatistics("Plugin","Activate", "");
		
		core::doPing();
	}
	
	public static function deactivatePlugin() {
		delete_option("wpXMSG_rewrite_done");
		self::updateStatistics("Plugin","Deactivate","");
	}
	public static function initialisePlugin() {

	
	// NB Network activation will not upgrade a site
	// do upgrade will check current upgrade script version and apply as necessary
		upgrader::checkUpgrade();
	
	// 2 is required for $file to be populated
		add_filter('plugin_row_meta', array(__CLASS__, 'filter_plugin_row_meta'),10,2);
		add_action('do_robots', array(__CLASS__, 'addRobotLinks'), 100, 0);
		add_action('wp_head', array(__CLASS__, 'addRssLink'),100);

		// only include admin files when necessary.
		if (is_admin() && !is_network_admin()) 
		{
			include_once 'settings.php';
			include_once 'postMetaData.php';
			include_once 'categoryMetaData.php';
			
			settings::addHooks();
			categoryMetaData::addHooks();
			postMetaData::addHooks();	
	
		}

	
		
		if (!wp_get_schedule('xmsg_ping')) 
		{
			// ping in 2 hours from when setup.
			wp_schedule_event(time() + 60*60*2 , 'daily', 'xmsg_ping');
		}

		add_action('xmsg_ping', array(__CLASS__, 'doPing'));
		
		// NB Network activation will not have set up the rules for the site.
		// Check if they exist and then reactivate.
		if (get_option(XSG_RULES_OPTION_NAME, null) != XSG_RULES_VERSION) 
		{
			add_action('wp_loaded', array(__CLASS__, 'activateRewriteRules'), 99999, 1);
		}
		
	}
	
	static function doPing()
	{
		include_once 'pinger.php';
		$globalSettings =   get_option( "wpXSG_global"   , new globalSettings()  );
		
		if ($globalSettings->pingSitemap == true)
		{		
			$sitemapDefaults =  get_option( "wpXSG_sitemapDefaults"   , new sitemapDefaults()  );$sitemapDefaults =  get_option( "wpXSG_sitemapDefaults"   , new sitemapDefaults()  );
			pinger::doAutoPings($sitemapDefaults->dateField);
		}
		
	}
	
	static function upgradeDatabase()
	{
		try 
		{
			include_once 'dataAccess.php';
			dataAccess::createMetaTable();
			update_option( "wpXSG_databaseUpgraded" ,  1 , false);
		} 
			catch (Exception $e) 
		{

		}
	}
	public static function addQueryVariableHooks(){ 
		add_filter('query_vars', array(__CLASS__, 'addQueryVariables'), 1, 1);
		add_filter('template_redirect', array(__CLASS__, 'templateRedirect'), 1, 0);
	}
	public static function addQueryVariables($vars) {
		array_push($vars, 'xml-sitemap');
		return $vars;
	}
	public static function templateRedirect() {
		global $wp_query;
		if(!empty($wp_query->query_vars["xml-sitemap"])) {
		
			$wp_query->is_404 = false;
			$wp_query->is_feed = false;
			include_once 'sitemapBuilder.php';
			$builder = new sitemapBuilder();
			$builder->render($wp_query->query_vars["xml-sitemap"]); //$wp_query->query_vars["xml-sitemap"]
		}
	}

	static function safeRead($object,$property)
	{
		return ( isset( $object->{$property} ) ?  $object->{$property}  : "" );
	}
	public static function addRobotLinks() 
	{
		$globalSettings =   get_option( "wpXSG_global"   , new globalSettings()  );
	 	if($globalSettings->addToRobots == true) 
	 	{
			$base = trailingslashit( get_bloginfo( 'url' ) );
			echo "\nSitemap: " . $base . "xmlsitemap.xml\n";
			echo "\nAllow: /rsssitemap.xml";
			echo "\nAllow: /htmlsitemap.htm";
	 	}
		echo "\n\n";
		echo self::safeRead($globalSettings,"robotEntries");
		
	}	
	public static function addRssLink() 
	{
		$globalSettings =   get_option( "wpXSG_global"   , new globalSettings()  );
	 	if($globalSettings->addRssToHead == true) 
	 	{
			$base = trailingslashit( get_bloginfo( 'url' ) );
			$url = $base . "rsslatest.xml";
			$link = '<link rel="alternate" type="application/rss+xml" title="RSS" href="' .  $url . '" />';
			echo $link;
		}
	}	
	public static function addRewriteHooks() {
		add_filter('rewrite_rules_array', array(__CLASS__, 'getRewriteRules'), 1, 1);
	}

	public static function getRewriteRules($originalRules) {
	
		$newRules = array();
		$newRules['xmlsitemap\.xml$'] = 'index.php?xml-sitemap=xml';  
		 $newRules['rsssitemap\.xml$'] = 'index.php?xml-sitemap=rss';  
		  $newRules['rsslatest\.xml$'] = 'index.php?xml-sitemap=rssnew';  
		 $newRules['htmlsitemap\.htm$'] = 'index.php?xml-sitemap=html';  
		 $newRules['xmlsitemap\.xsl$'] = 'index.php?xml-sitemap=xsl';  
		return array_merge($newRules,$originalRules);
	}
	
	
	public static function activateRewriteRules() {
		/** @var $wp_rewrite WP_Rewrite */
		global $wp_rewrite;
		$wp_rewrite->flush_rules(false);
		update_option(XSG_RULES_OPTION_NAME, XSG_RULES_VERSION);
	}
	
	static function filter_plugin_row_meta($links, $file) {
		$plugin  = self::pluginFilename();
		if ($file == $plugin)
		{
			$new_links = array(
						'<a href="options-general.php?page=' .  XSG_PLUGIN_NAME . '">Settings</a>'
					);
			
			$links = array_merge( $links, $new_links );
		}
		return $links;
	}

	static function getStatusHtml()
	{
		$array = get_option('xmsg_Log',"");
		
		if (is_array($array))
		{
			return implode("<br />", $array);
		}
		else
		{ return "Log empty";}
	}
	static function statusUpdate(  $statusMessage)
	{	
	
		$statusMessage = strip_tags($statusMessage);
		
		$array  = get_option('xmsg_Log',"");
		if (!is_array($array)) {$array = array();}
		$array = array_slice($array, 0, 19);		
		$newLine = gmdate("M d Y H:i:s", time()) . " - <strong>" . $statusMessage . "</strong>"  ;
		array_unshift($array , $newLine);	

		update_option('xmsg_Log', $array);
	}
	  static function doRequest($url) {

		$response = wp_remote_get($url );

		if(is_wp_error($response)) {
			$error = $response->get_error_messages();
			$error = substr(htmlspecialchars(implode('; ', $error)),0,150);
			return $error;
		}
		return substr($response['body'],0,200);
	}
	
	public static function getTimeBand($startTime)
	{
		$time = microtime(true) - $startTime;
		$timeLabel =  round($time) . "s";	
		return $timeLabel;
	}
	public static function updateStatistics($eventCategory, $eventAction, $timeBand) {
		
		$globalSettings =   get_option( "wpXSG_global"   , new globalSettings()  );
		
		if ($globalSettings->sendStats)
		{
			global $wp_version;
			$postCountLabel = dataAccess::getPostCountBand();

			$postData = array(
				'v' => 1,
				'tid' => 'UA-679276-7',
				'cid' => get_option('wpXSG_MapId'),
				't' => 'event',
				'ec' => $eventCategory,
				'ea' => $eventAction,
				'ev' => 1,
				'cd1' => get_bloginfo( 'url' ),
				'cd2' => $wp_version,
				'cd3' => self::pluginVersion(),
				'cd4' => PHP_VERSION,
				'cd5' => $postCountLabel,
				'cd6' => $timeBand
			);

			$url = 'https://ssl.google-analytics.com/collect';
			
			try
			{
				$response = wp_remote_post($url,
					array(
						'method' => 'POST',
						'body' => $postData
						));				
			} 
				catch (Exception $e) 
			{
				statusUpdate("sendStats : " . $e->getMessage());
			}
			

		}
	}
}




?>