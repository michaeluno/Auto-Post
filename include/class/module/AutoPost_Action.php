<?php
/**
 * The class that defines the action of Delete Posts for the Task Scheduler plugin.
 * 
 * @package      Task Scheduler
 * @copyright    Copyright (c) 2014-2015, Michael Uno
 * @author       Michael Uno
 * @authorurl    http://michaeluno.jp
 * @since        1.0.0
 */

/**
 * Defines the auto post action module.
 */
class AutoPost_Action extends TaskScheduler_Action_Base {
        
    /**
     * The user constructor.
     * 
     * This method is automatically called at the end of the class constructor.
     */
    public function construct() {}

    /**
     * Returns the readable label of this action.
     * 
     * This will be called when displaying the action in an pull-down select option, task listing table, or notification email message.
     */
    public function getLabel( $sLabel ) {
        return __( 'Auto Post', 'auto-post' );
    }

    /**
     * Returns the description of the module.
     */
    public function getDescription( $sDescription ) {
        return __( 'Creates posts automatically.', 'auto-post' );
    }    
    
    /**
     * Defines the behavior of the task action.
     * 
     * Required arguments: 
     * 
     */
    public function doAction( $sExitCode, $oRoutine ) {
        
        $_aRoutineMeta        = $oRoutine->getMeta();
        $_aRoutineArguments   = isset( $_aRoutineMeta[ $this->sSlug ] ) 
            ? $_aRoutineMeta[ $this->sSlug ]
            : array();    // the task specific options(arguments)

        if ( ! $this->_shouldProceed( $_aRoutineArguments ) ) {
            return 0;
        }
   
        // Author ID
        $_iAuthorID = $this->_getAuthorID( $_aRoutineArguments );

        // Create post
        $_iPostID = $this->_createPost( $_iAuthorID, $_aRoutineArguments );

        // Add taxonomy terms
        $this->_addTaxonomyTerms( $_iPostID, $_aRoutineArguments );
                
        // Post meta
        if ( 
            isset( $_aRoutineArguments[ 'auto_post_post_meta' ] ) 
            && is_array( $_aRoutineArguments[ 'auto_post_post_meta' ] )
        ) {
            $this->_insertPostMeta( 
                $_iPostID, 
                $_aRoutineArguments[ 'auto_post_post_meta' ]
            );
        }
        
        // Exit code.
        return $_iPostID 
            ? 1 
            : 0;
        
    }
    
        /**
         * @return      boolean
         * @since       1.2.0
         */
        private function _shouldProceed( array $aArguments ) {

            if ( 
                ! isset(    
                    $aArguments[ 'auto_post_content' ],
                    $aArguments[ 'auto_post_subject' ],
                    $aArguments[ 'auto_post_post_type' ],
                    $aArguments[ 'auto_post_post_status' ],
                    $aArguments[ 'auto_post_author' ]
                    // $aArguments[ 'auto_post_term_ids' ], // not necessary
                ) 
            ) {                 
                return false;    // failed
            }    
            return true;
            
        }    
    
        /**
         * @return      integer
         * @since       1.2.0
         */
        private function _getAuthorID( array $aArguments ) {

            // the value looks like this: [{"id":1,"name":"admin"}]
            $_aAuthor   = json_decode( 
                $aArguments[ 'auto_post_author' ], 
                true 
            );
            $_iAuthorID = isset( $_aAuthor[ 0 ][ 'id' ] )
                ? $_aAuthor[ 0 ][ 'id' ]
                : 1;
            return $_iAuthorID;
        
        }    
        /**
         * @since       1.2.0
         * @return      integer
         */
        private function _createPost( $iAuthorID, array $aArguments ) {
            
            return ( integer ) wp_insert_post(
                array(
                
                    'post_title'    => $aArguments[ 'auto_post_subject' ],
                    'post_content'  => $aArguments[ 'auto_post_content' ],
                    'post_status'   => $aArguments[ 'auto_post_post_status' ],
                    'post_author'   => $iAuthorID,
                    'post_type'     => $aArguments[ 'auto_post_post_type' ],
                    
                    // Do not set any taxonomy terms. Note that still 'uncategorized' gets assigned automatically.
                    'tax_input'     => array(),
                    'post_category' => array(),
                )
            );
            
        }
               
        /**
         * Insets taxonomy terms
         * For some reasons, the 'tax_input' argument of wp_insert_post() does not take effect when multiple terms are passed.        
         * 
         * @since       1.2.0
         * @return      void
         */
        private function _addTaxonomyTerms( $iPostID, array $aArguments ) {
            
            if ( ! $iPostID ) {
                return;
            }
            if ( empty( $aArguments[ 'auto_post_term_ids' ] ) ) {
                return;
            }
            
            $_iIndex = 0;
            foreach( $aArguments[ 'auto_post_term_ids' ] as $_sTaxonomySlug => $_aTermIDs ) {
                wp_set_object_terms( 
                    $iPostID, 
                    array_keys( array_filter( $_aTermIDs ) ),   // drop non-true elements and then extract keys.
                    $_sTaxonomySlug, 
                    $_iIndex ? true : false    // whether to append or not - for the first iteration pass 'false' to remove any existing assigned terms such as 'Uncategorized' of the built-in 'post' post type.
                );
                $_iIndex++;
            }
            
        }        
    
        /**
         * Updates post meta data.
         * 
         * @since       1.1.0
         */
        private function _insertPostMeta( $iPostID, array $aPostData ) {
            foreach( $aPostData as $_aKeyValue ) {
                if ( 
                    ! isset( 
                        $_aKeyValue[ 'key' ],
                        $_aKeyValue[ 'value' ]
                    )
                ) {
                    continue;
                }
                update_post_meta( 
                    $iPostID, 
                    $_aKeyValue[ 'key' ],  
                    $_aKeyValue[ 'value' ]
                );
            }
        }
            
}