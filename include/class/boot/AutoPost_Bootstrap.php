<?php
/**
 * Handles the initial set-up for the plugin.
 *    
 * @package      Auto Post
 * @copyright    Copyright (c) 2014-2015, Michael Uno
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
        
        
        
        // 7. Check requirements.
        add_action( 'admin_init', array( $this, '_replyToCheckRequirements' ) );
        
        // Add the 'Add New' link in the plugin table. 
        if ( isset( $GLOBALS['pagenow'] ) && 'plugins.php' === $GLOBALS['pagenow'] ) {
            add_filter( "plugin_action_links_" .  plugin_basename( $this->_sFilePath ), array( $this, '_replyToInsertAddNewLink' ) );
        }
        
        // 8. Schedule to load plugin specific components.
        add_action( 'task_scheduler_action_after_loading_plugin', array( $this, '_replyToLoadPluginComponents' ) );
                        
    }    
        public function _replyToInsertAddNewLink( $aLinks ) {
            
            if ( ! class_exists( 'TaskScheduler_Registry' ) ) {
                return $aLinks;
            }
            $_sHref = add_query_arg( 
                array( 
                    'page' => TaskScheduler_Registry::AdminPage_AddNew,
                ), 
                admin_url( 'admin.php' ) 
            );
            
            $_sLink = "<a href='{$_sHref}'>" . __( 'Add New', 'auto-post' ) . "</a>";
            array_unshift( $aLinks, $_sLink ); 
            return $aLinks;            
            
        }
        
    /**
     * 
     * @since            1.0.0
     */
    public function _replyToCheckRequirements() {
        
        if ( isset( $GLOBALS['pagenow'] ) && 'plugins.php' !== $GLOBALS['pagenow'] ) {
            return;
        }
        if ( ! class_exists( 'TaskScheduler_Registry' ) ) {
            add_action( 'admin_notices', array( $this, '_replyToShowAdminNotice' ) );
        }
        
    }
        /**
         * Prints an admin warning message.
         */
        public function _replyToShowAdminNotice() {
            
            echo "<div class='error'>"
                    . "<p>"
                        . '<strong>Auto Post</strong>: ' 
                        . sprintf( __( 'This plugin requires the <a href="%1$s">Task Scheduler</a> plugin.', 'auto-post' ), 'http://wordpress.org/plugins/task-scheduler/' )
                    . "</p>"
                . "</div>";
            
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
            AutoPost_Registry::TEXT_DOMAIN, 
            false, 
            dirname( plugin_basename( $this->_sFilePath ) ) . '/' . AutoPost_Registry::TEXT_DOMAIN_PATH
        );        
        
    }        
    
    /**
     * Loads the plugin specific components. 
     * 
     */
    public function _replyToLoadPluginComponents() {

        // Necessary Files
        $this->_loadClasses( $this->_sFilePath );

        // Localization
        $this->_localize();
        
        // Action Module
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