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

final class AutoPost_Action_Wizard_2 extends TaskScheduler_Wizard_Action_Base {

    /**
     * Returns the field definition arrays.
     * 
     * @remark        The field definition structure must follows the specification of Admin Page Framework v3.
     */ 
    public function getFields() {

        $_aWizardOptions = apply_filters( 'task_scheduler_admin_filter_get_wizard_options', array(), $this->sSlug );
        $_aTaxonomySlugs = array_keys( 
            TaskScheduler_WPUtility::getTaxonomiesByPostTypeSlug( 
                isset( $_aWizardOptions['auto_post_post_type'] ) 
                    ? $_aWizardOptions['auto_post_post_type'] 
                    : null 
            )
        );
        $_sPostTypeSlug  = isset( $_aWizardOptions[ 'auto_post_post_type' ] ) 
            ? $_aWizardOptions[ 'auto_post_post_type' ] 
            : null;
        $_sPostTypeLabel = TaskScheduler_WPUtility::getPostTypeLabel( 
            $_sPostTypeSlug
        );
        return array(
            array(    
                'field_id'          => 'auto_post_post_type_label',
                'title'             => __( 'Post Type', 'auto-post' ),
                'type'              => 'text',
                'attributes'        => array(
                    'readonly'  => 'readonly',
                    'name'      => '',    // dummy
                ),
                'value'             => $_sPostTypeLabel
                    ? $_sPostTypeLabel
                    : $_sPostTypeSlug,
            ),            
            array(    
                'field_id'          => 'auto_post_post_status_label',
                'title'             => __( 'Post Status', 'auto-post' ),
                'type'              => 'text',
                'attributes'        => array(
                    'readonly'  => 'readonly',
                    'name'      => '',    // dummy
                ),                
                'value'             => $this->_getPostStatusLabel( isset( $_aWizardOptions['auto_post_post_status'] ) ? $_aWizardOptions['auto_post_post_status'] : null ),
            ),                
            array(
                'field_id'          => 'auto_post_term_ids',
                'title'             => __( 'Terms', 'auto-post' ),
                'type'              => 'taxonomy',
                'taxonomy_slugs'    => $_aTaxonomySlugs,
                'if'                => count( $_aTaxonomySlugs ),
            ),         
            array(    
                'field_id'          => 'auto_post_subject',
                'title'             => __( 'Subject', 'auto-post' ),
                'type'              => 'text',
                'description'       =>  __( 'The title of the post.', 'auto-post' ),
                'attributes'        =>  array(
                    'size'  =>  60,
                ),
            ),                        
            array(    
                'field_id'          => 'auto_post_content',
                'title'             => __( 'Post Content', 'auto-post' ),
                'type'              => 'textarea',
                'rich'              => true,
            ),                                                
        );
        
    }    
        private function _getPostStatusLabel( $sLabelSlug ) {
            
            $_aLabels = TaskScheduler_WPUtility::getRegisteredPostStatusLabels();
            return isset( $_aLabels[ $sLabelSlug ] )
                ? $_aLabels[ $sLabelSlug ]
                : $sLabelSlug;
            
        }
        
    public function validateSettings( $aInput, $aOldInput, $oAdminPage ) { 

        // The Admin Page Framework inserts some keys into the $aInput array that it thinks 
        // the keys may be of higher capability users. So here ensure these keys won't be sent.
        unset( 
            $aInput['auto_post_post_type_label'],
            $aInput['auto_post_post_status_label'],
            $aInput['submit']
            // $aInput['auto_post_post_type']
        );
        $aInput = $aInput + array(
            // The taxonomy ids field can be not set depending on the previous user input.
            // However, make sure here that the key exists.
            'auto_post_term_ids' => null, 
        );

        $_bIsValid = true;
        $_bIsValid = false;
        $_aErrors  = array();        
            
        if ( ! $_bIsValid ) {

            // Set the error array for the input fields.
            $oAdminPage->setFieldErrors( $_aErrors );        
            $oAdminPage->setSettingNotice( __( 'Please try again.', 'auto-post' ) );
            
        }                            
        
        return $aInput;         

    }
        
}