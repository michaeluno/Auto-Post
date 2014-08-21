<?php
/**
 * Handles the initial set-up for the plugin.
 *    
 * @package      Auto Post
 * @copyright    Copyright (c) 2014, <Michael Uno>
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 * @since        1.0.0
 * 
 */

/**
 * 
 */
final class AutoPost_Bootstrap {
    
    function __construct( $sPluginFilePath ) {
        
        // 0. The class properties.
        $this->_sFilePath = $sPluginFilePath;
        $this->_bIsAdmin = is_admin();
        
        // 4. Set up activation hook.
        // register_activation_hook( $this->_sFilePath, array( $this, '_replyToDoWhenPluginActivates' ) );
        
        // 5. Set up deactivation hook.
        // register_deactivation_hook( $this->_sFilePath, array( $this, '_replyToDoWhenPluginDeactivates' ) );
        
        // 6. Set up localization.
        // $this->_localize();
        
        // 7. Check requirements.
        // add_action( 'admin_init', array( $this, '_replyToCheckRequirements' ) );
        
        // 8. Schedule to load plugin specific components.
        add_action( 'task_scheduler_action_after_loading_plugin', array( $this, '_replyToLoadPluginComponents' ) );
                        
    }    

    /**
     * 
     * @since            1.0.0
     */
    public function _replyToCheckRequirements() {

        new TaskScheduler_Requirements( 
            $this->_sFilePath,
            array(
                'php' => array(
                    'version'    =>    AutoPost_Registry::RequiredPHPVersion,
                    'error'        =>    __( 'The plugin requires the PHP version %1$s or higher.', 'task-scheduler' ),
                ),
                'wordpress' => array(
                    'version'    =>    AutoPost_Registry::RequiredWordPressVersion,
                    'error'        =>    __( 'The plugin requires the WordPress version %1$s or higher.', 'task-scheduler' ),
                ),
                // 'mysql'    =>    array(
                    // 'version'    =>    '5.5.24',
                    // 'error' => __( 'The plugin requires the MySQL version %1$s or higher.', 'task-scheduler' ),
                // ),
                'functions' => array(
                    'curl_version' => sprintf( __( 'The plugin requires the %1$s to be installed.', 'task-scheduler' ), 'the cURL library' ),
                ),
                // 'classes' => array(
                    // 'DOMDocument' => sprintf( __( 'The plugin requires the <a href="%1$s">libxml</a> extension to be activated.', 'pseudo-image' ), 'http://www.php.net/manual/en/book.libxml.php' ),
                // ),
                'constants'    => array(),
            )
        );    
        
    }

    /**
     * The plugin activation callback method.
     */    
    public function _replyToDoWhenPluginActivates() {}

    /**
     * The plugin deactivation callback method.
     */
    public function _replyToDoWhenPluginDeactivates() {}    
    
    /**
     * Load localization files.
     */
    private function _localize() {
        
        load_plugin_textdomain( 
            AutoPost_Registry::TextDomain, 
            false, 
            dirname( plugin_basename( $this->_sFilePath ) ) . '/language/'
        );
        
        if ( $this->_bIsAdmin ) {
            load_plugin_textdomain( 
                'admin-page-framework', 
                false, 
                dirname( plugin_basename( $this->_sFilePath ) ) . '/language/'
            );        
        }
        
    }        
    
    /**
     * Loads the plugin specific components. 
     * 
     */
    public function _replyToLoadPluginComponents() {

        // 1. Include files.
        $this->_loadClasses( $this->_sFilePath );
        
        // 2. Load the plugin action module
        new AutoPost_Action( 
            'auto_post_action_module',    // action slug
            array(  // wizard class names
                'AutoPost_Action_Wizard',          
                'AutoPost_Action_Wizard_2',
            ) 
        );
        
    }
    
    /**
     * Register classes to be auto-loaded.
     * 
     */
    private function _loadClasses( $sFilePath ) {
                
        // Include the include lists and register classes.
        $_aClassFiles        = array();
        include( dirname( $sFilePath ) . '/include/auto-post-include-class-file-list.php' );
        new TaskScheduler_AutoLoad( array(), array(), $_aClassFiles );    
                        
    }  

        
}