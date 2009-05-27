<?php
/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2009 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 * *******************************************************************************/
/**
 * SugarFieldBase translates and displays fields from a vardef definition into different formats
 * including DetailView, ListView, EditView. It also provides Search Inputs and database queries 
 * to handle searching
 * 
 */
class SugarFieldBase {
    var $ss; // Sugar Smarty Object
    var $hasButton = false;
    function SugarFieldBase($type) {
    	$this->type = $type;
        $this->ss = new Sugar_Smarty();
    }
    function fetch($path){
    	$additional = '';
    	if(!$this->hasButton && !empty($this->button)){
    		$additional .= '<input type="button" class="button" ' . $this->button . '>';	
    	}
        if(!empty($this->buttons)){
            foreach($this->buttons as $v){
                $additional .= ' <input type="button" class="button" ' . $v . '>'; 
            }
               
        }
        if(!empty($this->image)){
            $additional .= ' <img ' . $this->image . '>';    
        }
    	return $this->ss->fetch($path) . $additional;	
    }
    
    function getSmartyView($parentFieldArray, $vardef, $displayParams, $tabindex = -1, $view){
    	$this->setup($parentFieldArray, $vardef, $displayParams, $tabindex);
    	$tpl = 'include/SugarFields/Fields/' . $this->type . '/' . $view . '.tpl';
		$customTpl = 'custom/' . $tpl;
		if(file_exists($customTpl)){
    		return $this->fetch($customTpl);
    	}
    	if(file_exists($tpl)){
    		return $this->fetch($tpl);
    	}
		
    
    	return $this->fetch('include/SugarFields/Fields/Base/' . $view . '.tpl');
    }  
    











    
    function ListView($vardef) {
        $this->ss->assign('vardef', $vardef);
        
        return $this->fetch('include/SugarFields/Fields/SugarFieldBase/SugarFieldBaseListView.tpl');
    }
    
    /**
     * Returns a smarty template for the DetailViews
     * 
     * @param parentFieldArray string name of the variable in the parent template for the bean's data
     * @param vardef vardef field defintion
     * @param displayParam parameters for display
     *      available paramters are:
     *      * labelSpan - column span for the label
     *      * fieldSpan - column span for the field 
     */
    function getDetailViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex) {
        return $this->getSmartyView($parentFieldArray, $vardef, $displayParams, $tabindex, 'DetailView');
    }
    





    
    function getEditViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex) {
    	if(!empty($vardef['function']['returns']) && $vardef['function']['returns'] == 'html'){
    		$type = $this->type;
    		$this->type = 'Base';
    		$result= $this->getDetailViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex);
    		$this->type = $type;
    		return $result;
    	}
       return $this->getSmartyView($parentFieldArray, $vardef, $displayParams, $tabindex, 'EditView');
    }
    













    
    
    function getSearchViewSmarty($parentFieldArray, $vardef, $displayParams, $tabindex) {
		if(!empty($vardef['auto_increment']))$vardef['len']=255;
    	return $this->getSmartyView($parentFieldArray, $vardef, $displayParams, $tabindex, 'EditView');    
    }
    
    function getEditView() {
    }
    
    function getSearchInput() {
    }
    
    function getQueryLike() { 
    }
    
    function getQueryIn() {
    }
    
    /**
     * Setup function to assign values to the smarty template, should be called before every display function
     */
    function setup($parentFieldArray, $vardef, $displayParams, $tabindex, $twopass=true) {
    	$this->button = '';
    	$this->buttons = '';
    	$this->image = '';
    	if ($twopass){
	        $this->ss->left_delimiter = '{{';
	        $this->ss->right_delimiter = '}}';
    	}
        $this->ss->assign('parentFieldArray', $parentFieldArray);
        $this->ss->assign('vardef', $vardef);
        $this->ss->assign('tabindex', $tabindex);
        
        //for adding attributes to the field

        if(!empty($displayParams['field'])){
        	$plusField = '';
        	foreach($displayParams['field'] as $key=>$value){
        		$plusField .= ' ' . $key . '="' . $value . '"';//bug 27381
        	}
        	$displayParams['field'] = $plusField;
        }
        //for adding attributes to the button
    	if(!empty($displayParams['button'])){
        	$plusField = '';
        	foreach($displayParams['button'] as $key=>$value){
        		$plusField .= ' ' . $key . '="' . $value . '"';
        	}
        	$displayParams['button'] = $plusField;
        	$this->button = $displayParams['button'];
        }
        if(!empty($displayParams['buttons'])){
            $plusField = '';
            foreach($displayParams['buttons'] as $keys=>$values){
                foreach($values as $key=>$value){
                    $plusField[$keys] .= ' ' . $key . '="' . $value . '"';
                }
            }
            $displayParams['buttons'] = $plusField;
            $this->buttons = $displayParams['buttons'];
        }
        if(!empty($displayParams['image'])){
            $plusField = '';
            foreach($displayParams['image'] as $key=>$value){
                $plusField .= ' ' . $key . '="' . $value . '"';
            }
            $displayParams['image'] = $plusField;
            $this->image = $displayParams['image'];
        }
        $this->ss->assign('displayParams', $displayParams);
        
       
    }
	
	     /**
     * This should be called when the bean is saved. The bean itself will be passed by reference
     * @param SugarBean bean - the bean performing the save
     * @param array params - an array of paramester relevant to the save, most likely will be $_REQUEST
     */
     public function save(&$bean, $params, $field, $properties){}
    
}
?>
