<?php 
/**
	Admin Page Framework v3.8.16 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/admin-page-framework>
	Copyright (c) 2013-2017, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
abstract class AdminPageFramework_Input_Base extends AdminPageFramework_FrameworkUtility {
    public $aField = array();
    public $aAttributes = array();
    public $aOptions = array();
    public $aStructureOptions = array('input_container_tag' => 'span', 'input_container_attributes' => array('class' => 'admin-page-framework-input-container',), 'label_container_tag' => 'span', 'label_container_attributes' => array('class' => 'admin-page-framework-input-label-string',),);
    public function __construct(array $aAttributes, array $aOptions = array()) {
        $this->aAttributes = $this->getElementAsArray($aAttributes, 'attributes', $aAttributes);
        $this->aOptions = $aOptions + $this->aStructureOptions;
        $this->aField = $aAttributes;
        $this->construct();
    }
    protected function construct() {
    }
    public function get() {
    }
    public function getAttribute() {
        $_aParams = func_get_args() + array(0 => null, 1 => null,);
        return isset($_aParams[0]) ? $this->getElement($this->aAttributes, $_aParams[0], $_aParams[1]) : $this->aAttributes();
    }
    public function addClass() {
        foreach (func_get_args() as $_asSelectors) {
            $this->aAttributes['class'] = $this->getClassAttribute($this->aAttributes['class'], $_asSelectors);
        }
        return $this->aAttributes['class'];
    }
    public function setAttribute() {
        $_aParams = func_get_args() + array(0 => null, 1 => null,);
        $this->setMultiDimensionalArray($this->aAttributes, $this->getElementAsArray($_aParams, 0), $_aParams[1]);
    }
    public function setAttributesByKey($sKey) {
        $this->aAttributes = $this->getAttributesByKey($sKey);
    }
    public function getAttributesByKey() {
        return array();
    }
    public function getAttributeArray() {
        $_aParams = func_get_args();
        return call_user_func_array(array($this, 'getAttributesByKey'), $_aParams);
    }
}
class AdminPageFramework_Input_checkbox extends AdminPageFramework_Input_Base {
    public $aOptions = array('save_unchecked' => true,);
    public function get() {
        $_aParams = func_get_args() + array(0 => '', 1 => array());
        $_sLabel = $_aParams[0];
        $_aAttributes = $this->uniteArrays($this->getElementAsArray($_aParams, 1, array()), $this->aAttributes);
        return "<{$this->aOptions['input_container_tag']} " . $this->getAttributes($this->aOptions['input_container_attributes']) . ">" . $this->_getInputElements($_aAttributes, $this->aOptions) . "</{$this->aOptions['input_container_tag']}>" . "<{$this->aOptions['label_container_tag']} " . $this->getAttributes($this->aOptions['label_container_attributes']) . ">" . $_sLabel . "</{$this->aOptions['label_container_tag']}>";
    }
    private function _getInputElements($aAttributes, $aOptions) {
        $_sOutput = $this->aOptions['save_unchecked'] ? "<input " . $this->getAttributes(array('type' => 'hidden', 'class' => $aAttributes['class'], 'name' => $aAttributes['name'], 'value' => '0',)) . " />" : '';
        $_sOutput.= "<input " . $this->getAttributes($aAttributes) . " />";
        return $_sOutput;
    }
    public function getAttributesByKey() {
        $_aParams = func_get_args() + array(0 => '',);
        $_sKey = $_aParams[0];
        $_bIsMultiple = '' !== $_sKey;
        return $this->getElement($this->aAttributes, $_sKey, array()) + array('type' => 'checkbox', 'id' => $this->aAttributes['id'] . '_' . $_sKey, 'checked' => $this->_getCheckedAttributeValue($_sKey), 'value' => 1, 'name' => $_bIsMultiple ? "{$this->aAttributes['name']}[{$_sKey}]" : $this->aAttributes['name'], 'data-id' => $this->aAttributes['id'],) + $this->aAttributes;
    }
    private function _getCheckedAttributeValue($_sKey) {
        $_aValueDimension = '' === $_sKey ? array('value') : array('value', $_sKey);
        return $this->getElement($this->aAttributes, $_aValueDimension) ? 'checked' : null;
    }
}
