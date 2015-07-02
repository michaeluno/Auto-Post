<?php
/* 
	Plugin Name:    Auto Post
	Plugin URI:     http://en.michaeluno.jp/
	Description:    Creates posts automatically serving as a Task Scheduler module.
	Author:         miunosoft (Michael Uno)
	Author URI:     http://michaeluno.jp
	Version:        1.0.1b01
*/

/**
 * The base class of the registry class which provides basic plugin information.
 * 
 */
class AutoPost_Registry_Base {

	const VERSION        = '1.0.1b01';    // <--- DON'T FORGET TO CHANGE THIS AS WELL!!
	const NAME           = 'Auto Post';
	const DESCRIPTION    = 'Creates posts automatically serving as a Task Scheduler module.';
	const URI            = 'http://en.michaeluno.jp/';
	const AUTHOR         = 'miunosoft (Michael Uno)';
	const AUTHOR_URI     = 'http://en.michaeluno.jp/';
	const COPYRIGHT      = 'Copyright (c) 2014-2015, Michael Uno';
	const LICENSE        = 'GPL v2 or later';
	const CONTRIBUTORS   = '';
	
}

/* 2. Define the registry class. */
/**
 * Provides plugin information.
 */
final class AutoPost_Registry extends AutoPost_Registry_Base {
	        
	const TEXT_DOMAIN               = 'auto-post';
	const TEXT_DOMAIN_PATH          = '/language';            
          
    /**
     * The transient prefix. 
     * 
     * @remark      This is also accessed from uninstall.php so do not remove.
     * @remark      Up to 8 characters as transient name allows 45 characters or less ( 40 for site transients ) so that md5 (32 characters) can be added
     */    
	const TRANSIENT_PREFIX          = 'AP_';
    	    
	// These properties will be defined in the setUp() method.
	static public $sFilePath = '';
	static public $sDirPath  = '';
	
	/**
	 * Sets up static properties.
	 */
	static function setUp( $sPluginFilePath=null ) {
		self::$sFilePath = $sPluginFilePath ? $sPluginFilePath : __FILE__;
		self::$sDirPath  = dirname( self::$sFilePath );
	}    
	
	/**
	 * Returns the URL with the given relative path to the plugin path.
	 * 
	 * Example:  AutoPost_Registry::getPluginURL( 'asset/css/meta_box.css' );
	 */
	public static function getPluginURL( $sRelativePath='' ) {
		return plugins_url( $sRelativePath, self::$sFilePath );
	}

}
 
// Return if accessed directly. Do not exit as the header class for the inclusion script need to access the registry class.
if ( ! defined( 'ABSPATH' ) ) { return; }
AutoPost_Registry::setUp( __FILE__ );

/* 3. Perform the bootstrap. */
include( dirname( __FILE__ ) . '/include/class/boot/AutoPost_Bootstrap.php' );    
new AutoPost_Bootstrap( __FILE__ );