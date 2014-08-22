<?php
/* 
	Plugin Name:    Auto Post
	Plugin URI:     http://en.michaeluno.jp/
	Description:    Creates posts automatically serving as a Task Scheduler module.
	Author:         miunosoft (Michael Uno)
	Author URI:     http://michaeluno.jp
	Version:        1.0.0
*/

/* 1. Define the base registry class. */
/**
 * The base class of the registry class which provides basic plugin information.
 * 
 * The minifier script and the inclusion script also refer to the constants. 
 */
class AutoPost_Registry_Base {

	const Version        = '1.0.0';    // <--- DON'T FORGET TO CHANGE THIS AS WELL!!
	const Name           = 'Auto Post';
	const Description    = 'Creates posts automatically serving as a Task Scheduler module.';
	const URI            = 'http://en.michaeluno.jp/';
	const Author         = 'miunosoft (Michael Uno)';
	const AuthorURI      = 'http://en.michaeluno.jp/';
	const Copyright      = 'Copyright (c) 2014, Michael Uno';
	const License        = 'GPL v2 or later';
	const Contributors   = '';
	
}

/* 2. Define the registry class. */
/**
 * Provides plugin information.
 */
final class AutoPost_Registry extends AutoPost_Registry_Base {
	        
	// const OptionKey                 = 'autopost_option';
	const TransientPrefix           = 'AP_';    // Up to 8 characters as transient name allows 45 characters or less ( 40 for site transients ) so that md5 (32 characters) can be added
	// const AdminPage_Root            = 'AutoPost_AdminPage';    // the root menu page slug
	const TextDomain                = 'auto-post';
	const TextDomainPath            = './language';
	// const PostType                  = 'autopost';        // up to 20 characters
	// const Taxonomy_SystemLabel      = 'task_scheduler_system_label';
	const RequiredPHPVersion        = '5.2.1';
	const RequiredWordPressVersion  = '3.7';
	    
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