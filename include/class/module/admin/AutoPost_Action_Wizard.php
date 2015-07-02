<?php
/**
 * Creates wizard pages for the 'Auto Post' action.
 * 
 * @package      Auto Post
 * @copyright    Copyright (c) 2014-2015, Michael Uno
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 * @since        1.0.0
 */

final class AutoPost_Action_Wizard extends TaskScheduler_Wizard_Action_Base {

    /**
     * User constructor.
     */
    public function construct() {}

    /**
     * Returns the field definition arrays.
     * 
     * @remark        The field definition structure must follows the specification of Admin Page Framework v3.
     */ 
    public function getFields() {

        // The call to get_currentuserinfo() places the current user's info into $current_user.
        global $current_user;
        get_currentuserinfo();    
    
        return array(
            array(    
                'field_id'  => 'auto_post_post_type',
                'title'     => __( 'Post Type', 'auto-post' ),
                'type'      => 'select',
                'label'     => TaskScheduler_WPUtility::getRegisteredPostTypeLabels(),
            ),              
            array(      
                'field_id'  => 'auto_post_post_status',
                'title'     => __( 'Post Status', 'auto-post' ),
                'type'      => 'radio',
                'label'     => $this->_getPostStatusLabels(),
                'default'   => 'publish',           
            ),            
            array(      
                'field_id'      => 'auto_post_author',
                'title'         => __( 'Author', 'auto-post' ),
                'type'          => 'autocomplete',
                'settings'      => add_query_arg( 
                    array( 
                        'request'   => 'autocomplete', 
                        'type'      => 'user', // Note that the argument key is not 'post_type'
                    ) + $_GET,
                    admin_url( isset( $GLOBALS['pagenow'] ) ? $GLOBALS['pagenow'] : '' )
                ),                
                'settings2'     =>  array(
                    'theme'             => 'admin_page_framework',
                    'tokenLimit'        => 1,
                    'preventDuplicates' => true,
                    'searchDelay'       => 5, // 50 milliseconds. Default: 300                    
                    'hintText' => __( 'Type a user name.', 'auto-post' ),
                    'prePopulate' => array(
                        array( 'id' =>  $current_user->ID, 'name' => $current_user->display_name ),
                    )                        
                ),                   
            ),                
        );
        
    }    
        private function _getPostStatusLabels()  {
            
            $_aLabels = TaskScheduler_WPUtility::getRegisteredPostStatusLabels();
            unset( $_aLabels['trash'] );
            return $_aLabels;
            
        }

    public function validateSettings( $aInput, $aOldInput, $oAdminPage ) { 

        $_bIsValid = true;
        $_aErrors = array();        
    
        if ( '[]' === $aInput['auto_post_author'] || ! $aInput['auto_post_author']  ) {
            
            // $aVariable[ 'sectioni_id' ]['field_id']
            $_aErrors[ $this->_sSectionID ][ 'auto_post_author' ] = __( 'An author needs to be set.', 'auto-post' );
            $_bIsValid = false;            
            
        }
        
        if ( ! $_bIsValid ) {

            // Set the error array for the input fields.
            $oAdminPage->setFieldErrors( $_aErrors );        
            $oAdminPage->setSettingNotice( __( 'Please try again.', 'auto-post' ) );
            
        }                    
        
        return $aInput;

    }
    
    
}