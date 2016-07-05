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

        // Sets the `$current_user` if not set.
        global $current_user;
        wp_get_current_user();
    
        return array(
            array(    
                'field_id'      => 'auto_post_post_type',
                'title'         => __( 'Post Type', 'auto-post' ),
                'type'          => 'select',
                'label'         => TaskScheduler_WPUtility::getRegisteredPostTypeLabels(),
            ),
            array(    
                'field_id'      => 'auto_post_post_type_custom_slug',
                'title'         => __( 'Post Type Slug', 'auto-post' ) . ' (' . __( 'optional', 'auto-post' ) . ')',
                'type'          => 'text',
                'default'       => '',
                'description'   => array(
                    __( 'If you the post type you desire is not listed above, enter the post type slug here. Leave this empty to apply the one listed above.', 'auto-post' ),
                ),
                'attributes'    => array(
                    'size'  => 20,
                ),
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
    
        $aInput[ 'auto_post_post_type_custom_slug' ] = trim( $this->_getSanitizedURLQueryKey( $aInput[ 'auto_post_post_type_custom_slug' ] ) );
        if ( 20 < strlen( $aInput[ 'auto_post_post_type_custom_slug' ] ) ) {

            // $aVariable[ 'sectioni_id' ]['field_id']
            $_aErrors[ $this->_sSectionID ][ 'auto_post_post_type_custom_slug' ] = __( 'The length of the post type slug cannot be more than 20.', 'auto-post' );
            $_bIsValid = false;            
            
        }
        
        if ( '[]' === $aInput[ 'auto_post_author' ] || ! $aInput[ 'auto_post_author' ]  ) {
            
            // $aVariable[ 'sectioni_id' ]['field_id']
            $_aErrors[ $this->_sSectionID ][ 'auto_post_author' ] = __( 'An author needs to be set.', 'auto-post' );
            $_bIsValid = false;            
            
        }
        
        if ( ! $_bIsValid ) {

            // Set the error array for the input fields.
            $oAdminPage->setFieldErrors( $_aErrors );        
            $oAdminPage->setSettingNotice( __( 'Please try again.', 'auto-post' ) );
            
        }                    
    
        $aInput[ 'auto_post_post_type' ] = $aInput[ 'auto_post_post_type_custom_slug' ]
            ? $aInput[ 'auto_post_post_type_custom_slug' ] 
            : $aInput[ 'auto_post_post_type' ];
    
        return $aInput;

    }
    
    
    /**
     * Converts characters not supported to be used in the URL query key to underscore.
     * 
     * @see         http://stackoverflow.com/questions/68651/can-i-get-php-to-stop-replacing-characters-in-get-or-post-arrays
     * @return      string      The sanitized string.
     */
    public function _getSanitizedURLQueryKey( $sString ) {

        $_aSearch = array( chr( 32 ), chr( 46 ), chr( 91 ) );
        for ( $_i=128; $_i <= 159; $_i++ ) {
            array_push( $_aSearch, chr( $_i ) );
        }
        return str_replace ( $_aSearch , '_', $sString );
        
    }        
    
}