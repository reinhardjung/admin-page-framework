<?php
/**
 * Admin Page Framework
 *
 * http://admin-page-framework.michaeluno.jp/
 * Copyright (c) 2013-2020, Michael Uno; Licensed MIT
 *
 */

/**
 * Provides JavaScript utility scripts.
 *
 * @since       3.0.0
 * @since       3.3.0      Extends `AdminPageFramework_Form_View___Script_Base`.
 * @package     AdminPageFramework/Common/Form/View/JavaScript
 * @internal
 */
class AdminPageFramework_Form_View___Script_RepeatableSection extends AdminPageFramework_Form_View___Script_Base {

    /**
     * Returns an inline JavaScript script.
     *
     * @since       3.2.0
     * @since       3.3.0       Changed the name from `getjQueryPlugin()`.
     * @param       $oMsg       object      The message object.
     * @return      string      The inline JavaScript script.
     */
    static public function getScript( /* $oMsg */ ) {

        $_aParams           = func_get_args() + array( null );
        $_oMsg              = $_aParams[ 0 ];
        $sCannotAddMore     = $_oMsg->get( 'allowed_maximum_number_of_sections' );
        $sCannotRemoveMore  = $_oMsg->get( 'allowed_minimum_number_of_sections' );

        return <<<JAVASCRIPTS
( function( $ ) {
    
    /**
     * 
     * @remark      This method can be from a sections container or a cloned section container.
     * @since       unknown
     * @since       3.6.0       Changed the name from `updateAPFRepeatableSections`.
     * @todo        Change the selector name 'repeatable-section-add-button' to something else to avoid apf version conflict.
     */
    $.fn.updateAdminPageFrameworkRepeatableSections = function( aSettings ) {
    
        var _oThis                = this;
        var _sSectionsContainerID = _oThis.find( '.repeatable-section-add-button' ).first().closest( '.admin-page-framework-sections' ).attr( 'id' );
    
        // Store the sections specific options in an array.
        if ( ! $.fn.aAdminPageFrameworkRepeatableSectionsOptions ) {
            $.fn.aAdminPageFrameworkRepeatableSectionsOptions = [];
        }
        if ( ! $.fn.aAdminPageFrameworkRepeatableSectionsOptions.hasOwnProperty( _sSectionsContainerID ) ) {
            $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ _sSectionsContainerID ] = $.extend(
                {    
                    max: 0, // These are the defaults.
                    min: 0,
                    fadein: 500,
                    fadeout: 500,  
                    disabled: 0,
                    preserve_values: 0
                }, 
                aSettings 
            );
        }
        var _aOptions = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ _sSectionsContainerID ];

        // The Add button behavior - if the tag id is given, multiple buttons will be selected. 
        // Otherwise, a section node is given and single button will be selected.
        $( _oThis ).find( '.repeatable-section-add-button' ).click( function() {
        
            // 3.8.13+ 
            if ( $( this ).parent().data( 'disabled' ) ) {
                var _aDisabled = $( this ).parent().data( 'disabled' );
                tb_show( _aDisabled[ 'caption' ], $( this ).attr( 'href' ) );    
                return false;
            }
        
            $( this ).addAdminPageFrameworkRepeatableSection();
            return false; // will not click after that
        });
        
        // The Remove button behavior 
        $( _oThis ).find( '.repeatable-section-remove-button' ).click( function() {
            $( this ).removeAdminPageFrameworkRepeatableSection();
            return false; // will not click after that
        });     
        
        // If the number of sections is less than the set minimum value, add sections. 
        var _sSectionID           = _oThis.find( '.repeatable-section-add-button' ).first().closest( '.admin-page-framework-section' ).attr( 'id' );
        var _nCurrentSectionCount = jQuery( '#' + _sSectionsContainerID ).find( '.admin-page-framework-section' ).length;
        if ( _aOptions[ 'min' ] > 0 && _nCurrentSectionCount > 0 ) {
            if ( ( _aOptions[ 'min' ] - _nCurrentSectionCount ) > 0 ) {     
                $( '#' + _sSectionID ).addAdminPageFrameworkRepeatableSection( _sSectionID );  
            }
        }
        
    };
    
    /**
     * Adds a repeatable section.
     * 
     * @remark      Gets triggered when the user presses the repeatable `+` section button.
     */    
    $.fn.addAdminPageFrameworkRepeatableSection = function( sSectionContainerID ) {
        
        // Local variables
        if ( 'undefined' === typeof sSectionContainerID ) {
            var sSectionContainerID = $( this ).closest( '.admin-page-framework-section' ).attr( 'id' );
        }
        var nodeSectionContainer    = $( '#' + sSectionContainerID );
        var nodeNewSection          = nodeSectionContainer.clone(); // clone without bind events.
        var nodeSectionsContainer   = nodeSectionContainer.closest( '.admin-page-framework-sections' );
        var sSectionsContainerID    = nodeSectionsContainer.attr( 'id' );        
        var nodeTabsContainer       = $( this ).closest( '.admin-page-framework-section-tabs-contents' )
            .children( '.admin-page-framework-section-tabs' )
            .first();
            
        var _iSectionIndex          = nodeSectionsContainer.attr( 'data-largest_index' );
        
        var _iFadein                = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ sSectionsContainerID ][ 'fadein' ];
        var _iFadeout               = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ sSectionsContainerID ][ 'fadeout' ];
        var _bPreserveValues        = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ sSectionsContainerID ][ 'preserve_values' ];
        
        // If the set maximum number of sections already exists, do not add.
        var _sMaxNumberOfSections   = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ sSectionsContainerID ][ 'max' ];
        if ( _sMaxNumberOfSections != 0 && nodeSectionsContainer.find( '.admin-page-framework-section' ).length >= _sMaxNumberOfSections ) {
            var _nodeLastRepeaterButtons = nodeSectionContainer.find( '.admin-page-framework-repeatable-section-buttons' ).last();
            var _sMessage                = $( this ).formatPrintText( '{$sCannotAddMore}', _sMaxNumberOfSections );
            var _nodeMessage             = $( '<span class=\"repeatable-section-error\" id=\"repeatable-section-error-' + sSectionsContainerID + '\">' + _sMessage + '</span>' );
            if ( nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).length > 0 ) {
                nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).replaceWith( _nodeMessage );
            } else {
                _nodeLastRepeaterButtons.before( _nodeMessage );
            }
            _nodeMessage.delay( 2000 ).fadeOut( _iFadeout );
            return;     
        }
        
        // Empty the values.
        if ( ! _bPreserveValues ) {
            nodeNewSection.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' );
        }      
        nodeNewSection.find( '.repeatable-section-error' ).remove(); // remove error messages.
        
        // If this is not for tabbed sections, do not show the title.
        var _sSectionTabSlug = nodeNewSection.find( '.admin-page-framework-section-caption' ).first().attr( 'data-section_tab' );
        if ( ! _sSectionTabSlug || _sSectionTabSlug === '_default' ) {
            nodeNewSection.find( '.admin-page-framework-section-title' ).not( '.admin-page-framework-collapsible-section-title' ).hide();
        }
        // Bind the click event to the collapsible section(s) bar. If a collapsible section is not added, the jQuery plugin is not added.
        if( 'function' === typeof nodeNewSection.enableAdminPageFrameworkCollapsibleButton ){ 
            nodeNewSection.find( '.admin-page-framework-collapsible-sections-title, .admin-page-framework-collapsible-section-title' ).enableAdminPageFrameworkCollapsibleButton();
        }
                        
        // Add the cloned new field element.  
        nodeNewSection.hide().insertAfter( nodeSectionContainer );
        /// For non tabbed sections, show it.
        if ( ! nodeTabsContainer.length || nodeSectionContainer.hasClass( 'is_subsection_collapsible' ) ) {
            nodeNewSection.delay( 100 ).fadeIn( _iFadein );
        }
         
        // 3.6.0+ Increment the id and name attributes of the newly cloned section.
        _incrementAttributes( nodeNewSection, _iSectionIndex, nodeSectionsContainer );

        // It seems radio buttons of the original field need to be reassigned. Otherwise, the checked items will be gone. 
        nodeSectionContainer.find( 'input[type=radio][checked=checked]' ).prop( 'checked', true );    

        // Iterate each field one by one.
        $( nodeNewSection ).find( '.admin-page-framework-field' ).each( function( iFieldIndex ) {
        
            // Rebind the click event to the repeatable field buttons - important to update AFTER inserting the clone to the document node since the update method need to count fields.
            // @todo examine whether this is needed any longer.
            $( this ).updateAdminPageFrameworkRepeatableFields();
                                        
            // Callback the registered callback functions.
            
            // @deprecated 3.8.8 Kept for backward compatibility.
            $( this ).trigger( 
                'admin-page-framework_added_repeatable_field', 
                [
                    $( this ).data( 'type' ), // field type slug
                    $( this ).attr( 'id' ), // element tag id
                    1, // call type, 0: repeatable fields, 1: repeatable sections, (not implemented yet - 2: parent fields, 3: parent sections)
                    _iSectionIndex, 
                    iFieldIndex 
                ]
            );  

            // 3.8.8 
            $( this ).trigger( 
                'admin-page-framework_repeated_field', 
                [
                    1, // call type, 0: repeatable fields, 1: repeatable sections, 
                    jQuery( nodeNewSection ).closest( '.admin-page-framework-sections' )    // model container
                ]
            );              
            
        });     
        
        // Rebind the click event to the repeatable sections buttons - important to update AFTER inserting the clone to the document node since the update method need to count sections. 
        // Also do this after updating the attributes since the script needs to check the last added id for repeatable section options such as 'min'.
        nodeNewSection.updateAdminPageFrameworkRepeatableSections();    
        
        // Rebind sortable fields - iterate sortable fields containers.
        nodeNewSection.find( '.admin-page-framework-fields.sortable' ).each( function() {
            $( this ).enableAdminPageFrameworkSortableFields();
        });
        
        // For tabbed sections - add the title tab list.
        if ( nodeTabsContainer.length > 0 && ! nodeSectionContainer.hasClass( 'is_subsection_collapsible' ) ) {
            
            // The clicked (copy source) section tab.
            var nodeTab     = nodeTabsContainer.find( '#section_tab-' + sSectionContainerID );
            var nodeNewTab  = nodeTab.clone();
            
            nodeNewTab.removeClass( 'active' );
            if ( ! _bPreserveValues ) {            
                nodeNewTab.find( 'input:not([type=radio], [type=checkbox], [type=submit], [type=hidden]),textarea' ).val( '' ); // empty the value
            }
        
            // Add the cloned new field tab.           
            nodeNewTab
                .hide()
                .insertAfter( nodeTab )
                .delay( 10 )
                .fadeIn( _iFadein );
                
            _incrementAttributes( nodeNewTab, _iSectionIndex, nodeSectionsContainer );
                        
            nodeTabsContainer.closest( '.admin-page-framework-section-tabs-contents' ).createTabs( 'refresh' );
            
        }     
        
        // Increment the largest index attribute.
        nodeSectionsContainer.attr( 'data-largest_index', Number( _iSectionIndex ) + 1 );        
        
        // If more than one sections are created, show the Remove button.
        var _nodeRemoveButtons =  nodeSectionsContainer.find( '.repeatable-section-remove-button' );
        if ( _nodeRemoveButtons.length > 1 ) {
            _nodeRemoveButtons.show();     
        }
     
        // Return the newly created element.
        return nodeNewSection;    
        
    };    
        // Local function literal
        /**
         * 
         */
        var _incrementAttributes = function( oElement, iSectionsCount, oSectionsContainer ) {
            
            var _sSectionIDModel        = oSectionsContainer.attr( 'data-section_id_model' );
            var _sSectionNameModel      = oSectionsContainer.attr( 'data-section_name_model' );
            var _sSectionFlatNameModel  = oSectionsContainer.attr( 'data-flat_section_name_model' );
             
            $( oElement ).incrementAttribute(
                'id', // attribute name
                iSectionsCount, // increment from
                _sSectionIDModel // digit model
            );            
            $( oElement ).find( 'tr.admin-page-framework-fieldrow, .admin-page-framework-fieldset, .admin-page-framework-fields, .admin-page-framework-field, table.form-table, input,textarea,select,option' )
                .incrementAttribute( 
                    'id', 
                    iSectionsCount,
                    _sSectionIDModel
                );
                
            $( oElement ).find( '.admin-page-framework-fields' ).incrementAttribute( 
                'data-field_tag_id_model',
                iSectionsCount,
                _sSectionIDModel
            );
            $( oElement ).find( '.admin-page-framework-fields' ).incrementAttributes( 
                [ 'data-field_name_model' ],
                iSectionsCount,
                _sSectionNameModel
            );
            $( oElement ).find( '.admin-page-framework-fields' ).incrementAttributes( 
                [ 'data-field_name_flat', 'data-field_name_flat_model', 'data-field_address', 'data-field_address_model' ],
                iSectionsCount,
                _sSectionFlatNameModel
            );            
            
            // For checkbox, select, and radio input types
            $( oElement ).find( 'input[type=radio][data-id],input[type=checkbox][data-id],select[data-id]' ).incrementAttribute(
                'data-id', // attribute name
                iSectionsCount, // increment from
                _sSectionIDModel // digit model
            );              
            
        // @todo this may be able to be removed
            $( oElement ).find( '.admin-page-framework-fieldset' ).incrementAttribute( 
                'data-field_id',
                iSectionsCount,
                _sSectionIDModel
            );
            
            // holds the fields container ID referred by the repeater field script.
            $( oElement ).find( '.repeatable-field-add-button' ).incrementAttribute( 
                'data-id',
                iSectionsCount,
                _sSectionIDModel
            );
            $( oElement ).find( 'label' ).incrementAttribute( 
                'for',
                iSectionsCount,
                _sSectionIDModel
            );
            $( oElement ).find( 'input:not(.element-address),textarea,select' ).incrementAttribute( 
                'name',
                iSectionsCount,
                _sSectionNameModel
            );            
            
            // Section Tabs
            $( oElement ).find( 'a.anchor' ).incrementAttribute(
                'href', // attribute names - this elements contains id values in the 'name' attribute.
                iSectionsCount,
                _sSectionIDModel // digit model - this is
            );            
             
            // Update the hidden input elements that contain dynamic field names for nested elements.
            $( oElement ).find( 'input[type=hidden].element-address' ).incrementAttributes(
                [ 'name', 'value', 'data-field_address_model' ], // attribute names - this elements contains id values in the 'name' attribute.
                iSectionsCount,
                _sSectionFlatNameModel // digit model - this is
            );            
            
        }
        
    /**
     * Removes a repeatable section.
     * @remark  Triggered when the user presses the repeatable `-` section button.
     */
    $.fn.removeAdminPageFrameworkRepeatableSection = function() {
        
        // Local variables - preparing to remove the sections container element.
        var nodeSectionContainer    = $( this ).closest( '.admin-page-framework-section' );
        var sSectionContainerID     = nodeSectionContainer.attr( 'id' );
        var nodeSectionsContainer   = $( this ).closest( '.admin-page-framework-sections' );
        var sSectionsContainerID    = nodeSectionsContainer.attr( 'id' );        
        var nodeTabsContainer       = $( this ).closest( '.admin-page-framework-section-tabs-contents' )
            .children( '.admin-page-framework-section-tabs' )
            .first();
        var nodeTabs                = nodeTabsContainer.children( '.admin-page-framework-section-tab' );
        
        var _iSectionIndex          = nodeSectionsContainer.attr( 'data-largest_index' );
        
        var _iFadein                = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ sSectionsContainerID ][ 'fadein' ];
        var _iFadeout               = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ sSectionsContainerID ][ 'fadeout' ];
        
        // If the set minimum number of sections already exists, do not remove.
        var _sMinNumberOfSections = $.fn.aAdminPageFrameworkRepeatableSectionsOptions[ sSectionsContainerID ][ 'min' ];
        if ( _sMinNumberOfSections != 0 && nodeSectionsContainer.find( '.admin-page-framework-section' ).length <= _sMinNumberOfSections ) {
            var _nodeLastRepeaterButtons = nodeSectionContainer.find( '.admin-page-framework-repeatable-section-buttons' ).last();
            var _sMessage                = $( this ).formatPrintText( '{$sCannotRemoveMore}', _sMinNumberOfSections );
            var _nodeMessage             = $( '<span class=\"repeatable-section-error\" id=\"repeatable-section-error-' + sSectionsContainerID + '\">' + _sMessage + '</span>' );
            if ( nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).length > 0 ) {
                nodeSectionsContainer.find( '#repeatable-section-error-' + sSectionsContainerID ).replaceWith( _nodeMessage );
            } else {                
                _nodeLastRepeaterButtons.before( _nodeMessage );
            }
            _nodeMessage.delay( 2000 ).fadeOut( _iFadeout );
            return;     
        }     
        
        /** 
         * Call the registered callback functions
         * 
         * @since 3.0.0
         * @since 3.1.6 Changed it to do after removing the element.
         */                
        var _oNextAllSections           = nodeSectionContainer.nextAll();
        var _bIsSubsectionCollapsible   = nodeSectionContainer.hasClass( 'is_subsection_collapsible' );
        
        // Remove the section 
        // nodeSectionContainer.remove(); // @deprecated    3.6.0
        nodeSectionContainer.fadeOut( _iFadeout, function() { 
        
            $( this ).remove(); 
            
            // Count the remaining Remove buttons and if it is one, disable the visibility of it.
            var _nodeRemoveButtons = nodeSectionsContainer.find( '.repeatable-section-remove-button' );
            if ( 1 === _nodeRemoveButtons.length ) {
                _nodeRemoveButtons.css( 'display', 'none' );
                
                // Also, if this is not for tabbed sections, do show the title.
                var _sSectionTabSlug = nodeSectionsContainer.find( '.admin-page-framework-section-caption' ).first().attr( 'data-section_tab' );
                if ( ! _sSectionTabSlug || '_default' === _sSectionTabSlug ) {
                    nodeSectionsContainer.find( '.admin-page-framework-section-title' ).first().show();
                }                
            }            
            
        } );        
        
        
        // Decrement the names and ids of the next following siblings. 
        _oNextAllSections.each( function( _iIterationIndex ) {

// @todo set the section index            
var _iSectionIndex = _iIterationIndex;
            
            // Call the registered callback functions.
            // @deprecated  3.6.0
            // $( this ).find( '.admin-page-framework-field' ).each( function( iFieldIndex ) {    
                // $( this ).callBackRemoveRepeatableField( $( this ).data( 'type' ), $( this ).attr( 'id' ), 1, _iSectionIndex, iFieldIndex );
            // });     
        });
        
        // For tabbed sections - remove the title tab list.
        if ( nodeTabsContainer.length > 0 && nodeTabs.length > 1 && ! _bIsSubsectionCollapsible ) {
            var _oSelectionTab = nodeTabsContainer.find( '#section_tab-' + sSectionContainerID );
            
            if ( _oSelectionTab.prev().length ) {                
                _oSelectionTab.prev().addClass( 'active' );
            } else {
                _oSelectionTab.next().addClass( 'active' );
            }
                
            _oSelectionTab.fadeOut( _iFadeout, function() {
                $( this ).delay( 100 ).remove();
            } );
            nodeTabsContainer.closest( '.admin-page-framework-section-tabs-contents' ).createTabs( 'refresh' );
            
        }
            
    };
       
    
}( jQuery ));
JAVASCRIPTS;
    }

    /**
     * Returns the enabler script of repeatable sections.
     * @since       3.0.0
     * @since       3.4.0       Moved from `AdminPageFramework_FormPart_Table`.
     * @since       3.6.0       Moved from `AdminPageFramework_FormPart_Table_Base`.
     * @return      string
     */
    static public function getEnabler( $sContainerTagID, $iSectionCount, $asArguments, $oMsg ) {

        if ( empty( $asArguments ) ) {
            return '';
        }
        if ( self::hasBeenCalled( 'repeatable_section_' . $sContainerTagID ) ) {
            return '';
        }

        new self( $oMsg );
        $_oFormatter    = new AdminPageFramework_Form_Model___Format_RepeatableSection( $asArguments, $oMsg );
        $_aArguments    = $_oFormatter->get();
        $_sButtons      = self::___getRepeatableSectionButtons( $_aArguments, $oMsg, $sContainerTagID, $iSectionCount );
        $_sButtonsHTML  = '"' . $_sButtons . '"';
        $_aJSArray      = json_encode( $_aArguments );
        $_sScript       = <<<JAVASCRIPTS
jQuery( document ).ready( function() {

    // Adds the buttons
    jQuery( '#{$sContainerTagID} .admin-page-framework-section-caption' ).each( function(){
        
        jQuery( this ).show();
        
        var _oButtons = jQuery( $_sButtonsHTML );
        if ( jQuery( this ).children( '.admin-page-framework-collapsible-section-title' ).children( 'fieldset' ).length > 0 ) {
            _oButtons.addClass( 'section_title_field_sibling' );
        }
        var _oCollapsibleSectionTitle = jQuery( this ).find( '.admin-page-framework-collapsible-section-title' );
        if ( _oCollapsibleSectionTitle.length ) {
            _oButtons.find( '.repeatable-section-button' ).removeClass( 'button-large' );
            _oCollapsibleSectionTitle.append( _oButtons );
        } else {
            jQuery( this ).prepend( _oButtons );
        }
        
    } );
    // Update the fields     
    jQuery( '#{$sContainerTagID}' ).updateAdminPageFrameworkRepeatableSections( $_aJSArray ); 
});            
JAVASCRIPTS;
        return "<script type='text/javascript' class='admin-page-framework-section-repeatable-script'>"
                . '/* <![CDATA[ */'
                . $_sScript
                . '/* ]]> */'
            . "</script>";

    }
        /**
         * @return string
         * @since   3.8.22
         */
        static private function ___getRepeatableSectionButtons( $_aArguments, $oMsg, $sContainerTagID, $iSectionCount ) {
            $_sIconRemove   = '-';
            $_sIconAdd      = '+';
            if ( version_compare( $GLOBALS[ 'wp_version' ], '5.3', '>=' ) ) {
                $_sIconRemove   = "<span class='dashicons dashicons-minus'></span>";
                $_sIconAdd      = "<span class='dashicons dashicons-plus-alt2'></span>";
            }
            return "<div class='admin-page-framework-repeatable-section-buttons-outer-container'>"
                . "<div " . self::___getContainerAttributes( $_aArguments, $oMsg ) . ' >'
                    . "<a " . self::___getRemoveButtonAttributes( $sContainerTagID, $oMsg, $iSectionCount ) . ">"
                        . $_sIconRemove
                    . "</a>"
                    . "<a " . self::___getAddButtonAttributes( $sContainerTagID, $oMsg, $_aArguments ) . ">"
                        . $_sIconAdd
                    . "</a>"
                . "</div>"
            . "</div>"
            . AdminPageFramework_Form_Utility::getModalForDisabledRepeatableElement(
                    'repeatable_section_disabled_' . $sContainerTagID,
                    $_aArguments[ 'disabled' ]
                );
        }
        /**
         * @param   $aArguments
         * @return  string
         * @since   3.8.13
         */
        static private function ___getContainerAttributes( array $aArguments, $oMsg ) {
            $_aAttriubtes = array(
                'class' => self::getClassAttribute(
                    'admin-page-framework-repeatable-section-buttons',
                    empty( $aArguments[ 'disabled' ] ) ? '' : 'disabled'
                ),
            );
            unset( $aArguments[ 'disabled' ][ 'message' ] );    // this element can contain HTML tags.
            // Needs to remove it if it is empty as its data attribute will be checked in the JavaScript script.
            if ( empty( $aArguments[ 'disabled' ] ) ) {
                unset( $aArguments[ 'disabled' ] );
            }
            return self::getAttributes( $_aAttriubtes ) . ' ' . self::getDataAttributes( $aArguments );
        }
        /**
         * @return  string
         * @sicne   3.8.13
         */
        static private function ___getRemoveButtonAttributes( $sContainerTagID, $oMsg, $iSectionCount ) {
            return self::getAttributes(
                    array(
                    'class'     => 'repeatable-section-remove-button button-secondary '
                                   . 'repeatable-section-button button button-large',
                    'title'     => $oMsg->get( 'remove_section' ),
                    'style'     => $iSectionCount <= 1
                        ? 'display:none'
                        : null,
                    'data-id'   => $sContainerTagID,
                )
            );
        }

        /**
         * @since       3.8.13
         * @return string
         */
        static private function ___getAddButtonAttributes( $sContainerTagID, $oMsg, $aArguments ) {
            return self::getAttributes(
                array(
                    'class'     => 'repeatable-section-add-button button-secondary '
                        . 'repeatable-section-button button button-large',
                    'title'     => $oMsg->get( 'add_section' ),
                    'data-id'   => $sContainerTagID,
                    'href'      => ! empty( $aArguments[ 'disabled' ] )
                        ? '#TB_inline?width=' . $aArguments[ 'disabled' ][ 'box_width' ]
                            . '&height=' . $aArguments[ 'disabled' ][ 'box_height' ]
                            . '&inlineId=' . 'repeatable_section_disabled_' . $sContainerTagID
                        : null,
                )
            );
        }

}
