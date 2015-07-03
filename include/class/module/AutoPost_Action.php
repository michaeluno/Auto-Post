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
  * 
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
  
        if ( 
            ! isset(    
                $_aRoutineMeta[ $this->sSlug ],
                $_aRoutineArguments[ 'auto_post_content' ],
                $_aRoutineArguments[ 'auto_post_subject' ],
                $_aRoutineArguments[ 'auto_post_post_type' ],
                $_aRoutineArguments[ 'auto_post_post_status' ],
                $_aRoutineArguments[ 'auto_post_author' ]
                // $_aRoutineArguments[ 'auto_post_term_ids' ], // not necessary
            ) 
        ) {                 
            return 0;    // failed
        }

        // the value looks like this: [{"id":1,"name":"admin"}]
        $_aAuthor   = json_decode( 
            $_aRoutineArguments[ 'auto_post_author' ], 
            true 
        );
        $_iAuthorID = isset( $_aAuthor[ 0 ][ 'id' ] )
            ? $_aAuthor[ 0 ][ 'id' ]
            : 1;
                
        $_iPostID = wp_insert_post(
            array(
                'post_title'    => $_aRoutineArguments[ 'auto_post_subject' ],
                'post_content'  => $_aRoutineArguments[ 'auto_post_content' ],
                'post_status'   => $_aRoutineArguments[ 'auto_post_post_status' ],
                'post_author'   => $_iAuthorID,
                'post_type'     => $_aRoutineArguments[ 'auto_post_post_type' ],
                
                // Do not set any taxonomy terms. Note that still 'uncategorized' gets assigned automatically.
                'tax_input'     => array(),
                'post_category' => array(),
            )
        );
    
        // For some reasons, the 'tax_input' argument of wp_insert_post() does not take effect when multiple terms are passed.        
        if ( $_iPostID && ! empty( $_aRoutineArguments['auto_post_term_ids'] ) ) {
            $_iIndex = 0;
            foreach( $_aRoutineArguments['auto_post_term_ids'] as $_sTaxonomySlug => $_aTermIDs ) {
                wp_set_object_terms( 
                    $_iPostID, 
                    array_keys( array_filter( $_aTermIDs ) ),   // drop non-true elements and then extract keys.
                    $_sTaxonomySlug, 
                    $_iIndex ? true : false    // whether to append or not - for the first iteration pass 'false' to remove any existing assigned terms such as 'Uncategorized' of the built-in 'post' post type.
                );
                $_iIndex++;
            }
        }
        
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