<?php
/**
 * Admin Page Framework
 * 
 * http://en.michaeluno.jp/admin-page-framework/
 * Copyright (c) 2013-2015 Michael Uno; Licensed MIT
 * 
 */

/**
 * Provides methods to retrieve field type resources.
 
 * @package     AdminPageFramework
 * @subpackage  Form
 * @since       DEVVER
 */
class AdminPageFramework_Form_Model___FieldTypeRegistration extends AdminPageFramework_WPUtility {
    
    /**
     * Initializes the field type.
     * @since       DEVVER
     */    
    public function __construct( array $aFieldTypeDefinition, $sStructureType ) {
       
        $this->_initialize( 
            $aFieldTypeDefinition,
            $sStructureType
        );
        
    }
  
        /**
         * Runs the initializer the given field type.
         * 
         * @since       3.5.3
         * @since       DEVVER  Moved from `AdminPageFramework_FieldTypeRegistration`. Changed it not static. Chaned the name from `_initializeFieldType()`.
         * @return      void
         */
        private function _initialize( $aFieldTypeDefinition, $sStructureType ) {
                
            if ( is_callable( $aFieldTypeDefinition[ 'hfFieldSetTypeSetter' ] ) ) {
                call_user_func_array( 
                    $aFieldTypeDefinition[ 'hfFieldSetTypeSetter' ], 
                    array( $sStructureType ) 
               );
            }
            
            if ( is_callable( $aFieldTypeDefinition[ 'hfFieldLoader' ] ) ) {
                call_user_func_array( 
                    $aFieldTypeDefinition[ 'hfFieldLoader' ], 
                    array()
                );
            }
                       
        }    
   
}