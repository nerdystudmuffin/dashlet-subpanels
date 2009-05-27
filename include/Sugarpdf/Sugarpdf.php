<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
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
 ********************************************************************************/


if(file_exists('custom/include/Sugarpdf/sugarpdf_config.php')){
    require_once('custom/include/Sugarpdf/sugarpdf_config.php');
} else {
    require_once('include/Sugarpdf/sugarpdf_config.php');
}

require_once('include/tcpdf/tcpdf.php');
require_once('include/Sugarpdf/SugarpdfHelper.php');

class Sugarpdf extends TCPDF
{

    /**
     * This array is meant to hold an objects/data that we would like to pass between
     * the controller and the view.  The bean will automatically be set for us, but this
     * is meant to hold anything else.
     */
    var $sugarpdf_object_map = array();
    /**
     * The name of the current module.
     */
    var $module = '';
    /**
     * The name of the current action.
     */
    var $action = '';
    /**
     */
    var $bean = null;
     /**
     * Any errors that occured this can either be set by the view or the controller or the model
     */
    var $errors = array();
    /**
     * Use to set the filename of the output pdf file.
     */
    var $fileName = PDF_FILENAME;
    /**
     * Use for the ACL access.
     */
    var $aclAction = PDF_ACL_ACCESS;
    /**
     * Constructor which will peform the setup.
     */
    
   
    function __construct($bean = null, $sugarpdf_object_map = array(),$orientation=PDF_PAGE_ORIENTATION, $unit=PDF_UNIT, $format=PDF_PAGE_FORMAT, $unicode=true, $encoding='UTF-8', $diskcache=false){
        global $locale;
        $encoding = $locale->getExportCharset();
        if(empty($encoding)){
            $encoding = "UTF-8";
        }
        parent::__construct($orientation,$unit,$format,$unicode,$encoding,$diskcache);
        $this->module = $GLOBALS['module'];
        $this->bean = &$bean;
        $this->sugarpdf_object_map = $sugarpdf_object_map;
        if(!empty($_REQUEST["sugarpdf"])){
            $this->action = $_REQUEST["sugarpdf"];
        }
    }

    /**
     * This method will be called from the controller and is not meant to be overridden.
     */
    function process(){
        //$this->buildModuleList();
        $this->preDisplay();
        //$this->displayErrors();
        $this->display();

    }

    /**
     * This method will display the errors on the page.
     */
    function displayErrors(){
        foreach($this->errors as $error) {
            echo '<span class="error">' . $error . '</span><br>';
        }
    }

    /**
     * [OVERRIDE] - This method is meant to overidden in a subclass. The purpose of this method is
     * to allow a view to do some preprocessing before the display method is called. This becomes
     * useful when you have a view defined at the application level and then within a module
     * have a sub-view that extends from this application level view.  The application level
     * view can do the setup in preDisplay() that is common to itself and any subviews
     * and then the subview can just override display(). If it so desires, can also override
     * preDisplay().
     */
    function preDisplay(){
        // set document information
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor(PDF_AUTHOR);
        $this->SetTitle(PDF_TITLE);
        $this->SetSubject(PDF_SUBJECT);
        $this->SetKeywords(PDF_KEYWORDS);
        
        // set other properties
        $compression=false;
        if(PDF_COMPRESSION == "on"){
            $compression=true;
        }
        $this->SetCompression($compression);
        $protection=array();
        if(PDF_PROTECTION != ""){
            $protection=explode(",",PDF_PROTECTION);
        }

        $this->SetProtection($protection,blowfishDecode(blowfishGetKey('sugarpdf_pdf_user_password'), PDF_USER_PASSWORD),blowfishDecode(blowfishGetKey('sugarpdf_pdf_owner_password'), PDF_OWNER_PASSWORD));
        $this->setCellHeightRatio(K_CELL_HEIGHT_RATIO);
        $this->setJPEGQuality(intval(PDF_JPEG_QUALITY));
        $this->setPDFVersion(PDF_PDF_VERSION);
        
        // set default header data
        $this->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        
        // set header and footer fonts
        $this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
        //set margins
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        //set auto page breaks
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        //set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO); 
        
        //set some language-dependent strings
        //$this->setLanguageArray($l); 
        
        // ---------------------------------------------------------        
        
    }

    /**
     * [OVERRIDE] - This method is meant to overidden in a subclass.
     */
    function display(){
        $this->AddPage();
        $this->SetFont(PDF_FONT_NAME_MAIN,'B',16);
        $this->MultiCell(0,0,'Tcpdf class for this module and action has not been implemented.',0,'C');
        $this->Info();


    }

    function Info(){
        
        $this->SetFont(PDF_FONT_NAME_MAIN,'',12);
        $this->MultiCell(0,0,'---',0,'L');
        $this->MultiCell(0,0,'Class: '.get_class($this),0,'L');
        $this->MultiCell(0,0,'Extends: '.get_parent_class($this),0,'L');
        $this->MultiCell(0,0,'---',0,'L');
        $this->MultiCell(0,0,'Module: '.$this->module,0,'L');
        $this->MultiCell(0,0,'Tcpdf Action: '.$this->action,0,'L');
        $this->MultiCell(0,0,'Bean ID: '.$this->bean->getFieldValue('id'),0,'L');
        $this->SetFont(PDF_FONT_NAME_MAIN,'',12);
        $this->MultiCell(0,0,'---',0,'L');

    }
    
    /**
     * [OVERRIDE] Cell method in tcpdf library.
     * Handle charset conversion and HTML entity decode.
     * This method override the regular Cell() method to apply the prepare_string() function to
     * the string to print in the PDF.
     * The cell method is used by all the methods which print text (Write, MultiCell).
     * @see include/tcpdf/TCPDF#Cell()
     */
    public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0) {
        parent::Cell($w, $h, prepare_string($txt), $border, $ln, $align, $fill, $link, $stretch);
    }
    
    /**
     * The regular Ln() method print a line break which has the height of the last printed cell.
     * This Ln1() method will always print a line break of one line.
     */
    public function Ln1() {
        parent::Ln($this->FontSize * $this->cell_height_ratio + 2 * $this->cMargin, false);
    }
    
    
    /**
     * This method allow printing a table using the MultiCell method with a formatted options array in parameter
     * Options : 
     * header options override the regular options for the header's cells - $options['header']
     * cell options override the regular options for the specific cell - Array[line number (0 to x)][cell header]['options']
     * @param $item Array[line number (0 to x)][cell header] = Cell content OR 
     *              Array[line number (0 to x)][cell header]['value'] = Cell content AND 
     *              Array[line number (0 to x)][cell header]['options'] = Array[cell properties] = values
     * @param $options Array which can contain : width (array 'column name'=>'width value + % OR nothing'), isheader (bool), header (array), fill (string: HTML color), ishtml (bool) default: false, border (0: no border (defaul), 1: frame or all of the following characters: L ,T ,R ,B), align (L: left align, C: center, R: right align, J: justification)
     * @see MultiCell()
     */
    public function writeCellTable($item, $options=NULL){
        // Save initial font values
        $fontFamily = $this->getFontFamily();
        $fontSize = $this->getFontSizePt();
        $fontStyle = $this->getFontStyle();
        $this->SetTextColor(0, 0, 0);
        
        $options = $this->initOptionsForWriteCellTable($options, $item);
        
        // HEADER
        if(!isset($options['isheader']) || $options['isheader'] == true){
            $headerOptions = $options;
            if(!empty($options['header']) && is_array($options['header'])){
                $headerOptions = $this->initOptionsForWriteCellTable($options['header'], $item);
            }
            foreach($item[0] as $k => $v){
                $header[$k]=$k;
            }
            $h = $this->getLineHeightFromArray($header, $options["width"]);
            foreach($header as $v){
                $value = $v;
                if(is_array($v)){
                    $value = $v['value'];
                    if(!empty($v['options']) && is_array($v['options'])){
                        $headerOptions = $this->initOptionsForWriteCellTable($v['options'], $item);
                    }
                }
                $this->MultiCell($options["width"][$v],$h,$value,$headerOptions['border'],$headerOptions['align'],$headerOptions['fillstate'],0,'','',true,0,$headerOptions['ishtml']);
            }
            $this->SetFillColorArray($this->convertHTMLColorToDec($options['fill']));
            $this->Ln();
        }
        
        // MAIN
        // default font
        $this->SetFont($fontFamily,$fontStyle,$fontSize);
        $this->SetTextColor(0, 0, 0);
        $even=true;
        // LINES
        foreach($item as $k=>$line){
            $even=!$even;
            $h = $this->getLineHeightFromArray($line, $options["width"]);
            //CELLS
            foreach($line as $kk=>$cell){
                $cellOptions = $options;
                $value = $cell;
                
                if(is_array($cell)){
                    $value = $cell['value'];
                    if(!empty($cell['options']) && is_array($cell['options'])){
                        $cellOptions = $this->initOptionsForWriteCellTable($cell['options'], $item);
                    }
                }
                
                if($even && !empty($options['evencolor'])){
                    $this->SetFillColorArray($this->convertHTMLColorToDec($options['evencolor']));
                    $cellOptions['fillstate']=1;
                }else if(!$even && !empty($options['oddcolor'])){
                    $this->SetFillColorArray($this->convertHTMLColorToDec($options['oddcolor']));
                    $cellOptions['fillstate']=1;
                }
                
                $this->MultiCell($options["width"][$kk],$h,$value,$cellOptions['border'],$cellOptions['align'],$cellOptions['fillstate'],0,'','',true,0,$cellOptions['ishtml']);
                
                $this->SetFillColorArray($this->convertHTMLColorToDec($options['fill']));
            }
            $this->Ln();
        }
        $this->SetFont($fontFamily,$fontStyle,$fontSize);
        $this->SetTextColor(0, 0, 0);
    }
    
    /**
     * This method allow printing a table using the writeHTML method with a formatted array in parameter
     * This method can also return the table as HTML code
     * @param $item Array[line number (0 to x)][cell header] = Cell content OR 
     *              Array[line number (0 to x)][cell header]['value'] = Cell content AND 
     *              Array[line number (0 to x)][cell header]['options'] = Array[cell properties] = values
     * @param $returnHtml (bool) Return the table as HTML code instead of printing the HTML table
     * @param $options Array which can contain : table (array of "HTML proprty"=>"value"),td (array of "HTML proprty"=>"value"), tr (array of "HTML proprty"=>"value"), isheader(bool), header (array of "HTML proprty"=>"value"), width (array 'column name'=>'width value + unit OR nothing')
     * @return the HTML code if $returnHtml set to true
     */
    public function writeHTMLTable($item, $returnHtml=false, $options=NULL){
        //TODO ISSUE - width in % for the td have to be multiply by the number of column.
        //     ex: for a width of 20% in a table of 6 columns the width will have to be 120% (20*6).
        $html="";
        $line="";
        if(!empty($options)){
            foreach($options as $k=>$v){
                $tmp[strtolower($k)]=$v;
            }
            $options=$tmp;
        }else{
            $options=array();
        }
        if(!isset($options["isheader"]) || $options["isheader"] == true){
            if(!empty($options["header"])){
                foreach($options["header"] as $k=>$v){
                    $tmp[strtolower($k)]=$v;
                }
                $options["header"]=$tmp;
            }else{
                $options["header"]=array("tr"=>array("bgcolor"=>"#DCDCDC"),"td"=>array());
            }
            
            foreach($item[0] as $k => $v){
                if(!empty($options["width"]))$options["header"]["td"]["width"]=$options["width"][$k];
                $line.=$this->wrap("td", $k, $options["header"]);
            }
            $html.=$this->wrap("tr", $line, $options["header"]);
        }
        foreach ($item as $k=>$v){
            $line="";
            foreach($v as $kk => $vv){
                if(!empty($options["width"]))$options["td"]["width"]=$options["width"][$kk];
                $line.=$this->wrap("td", $vv, $options);
            }
            $html.=$this->wrap("tr", $line, $options);
        }
        $html=$this->wrap("table", $html, $options);
        if($returnHtml){
            return $html;
        }else{
            $this->writeHTML($html);
        }
    }
    
    /**
     * return the HTML code of the value wrap with the tag $tag. This method handle options (general and specific)
     * @param $tag
     * @param $value
     * @param $options
     * @return the HTML wrapped code
     */
    private function wrap($tag, $value, $options){
        if(empty($options[$tag])){
            $options[$tag] = array();
        }
        if(is_array($value)){
            if(isset($value["options"])){
                // The options of a specific entity overwrite the general options 
                $options[$tag] = $value["options"];
            }
            if(isset($value["value"])){
                $value = $value["value"];
            }else{
                $value = "";
            }
        }
        return wrapTag($tag, $value, $options[$tag]);
    }
    
    /**
     * Return the heigth of a line depending of the width, the font and the content
     * @param $line Array containing the data of all the cells of the line
     * @param $width Array containing the width of all the cells of the line
     * @return The heigth of the line
     */
    private function getLineHeightFromArray($line, $width){
        $h=0;
        foreach($line as $kk=>$cell){
            $cellValue = $cell;
            if(is_array($cellValue)){
                $tmp = $cellValue['value'];
                $cellValue = $tmp;
            }
            if($h<$this->getNumLines($cellValue, $width[$kk])){
                $h=$this->getNumLines($cellValue, $width[$kk]);
            }
        }
        return $h * $this->FontSize * $this->cell_height_ratio + 2 * $this->cMargin;
    }
    
    /**
     * Private method for writeCellTable which format and initialize the options array.
     * @param $options array
     * @param $item array
     * @return $options array
     */
    private function initOptionsForWriteCellTable($options, $item){
       if(!empty($options)){
            foreach($options as $k=>$v){
                $tmp[strtolower($k)]=$v;
            }
            $options=$tmp;
        }else{
            $options=array();
        }
        // set to default if empty
        if(empty($options["width"]) || !is_array($options["width"])){
            $colNum = count($item[0]);
            $defaultWidth = $this->getRemainingWidth()/$colNum;
            foreach($item[0] as $k => $v){
                $options["width"][$k]=$defaultWidth;
            }
        }else{
            foreach($options["width"] as $k => $v){
                $options["width"][$k] = $this->getHTMLUnitToUnits($v, $this->getRemainingWidth());
            }
            
        }
        
        if(empty($options["border"])){
            $options["border"]=0;
        }
        
        if(empty($options["align"])){
            $options["align"]="L";
        }
        
        if(empty($options['ishtml'])){
            $options['ishtml'] = false;
        }
        if(empty($options['border'])){
            $options['border'] = 0;
        }
        
        if(!empty($options['fill'])){
            $this->SetFillColorArray($this->convertHTMLColorToDec($options['fill']));
            $options['fillstate']=1;
        }else{
            $options['fill']="#FFFFFF";//white
            $options['fillstate']=0;
        }
        
        if(!empty($options['fontfamily'])){
            $fontFamily = $options['fontfamily'];
        }else{
            $fontFamily = $this->getFontFamily();
        }
        if(!empty($options['fontsize'])){
            $fontSize = $options['fontsize'];
        }else{
            $fontSize = $this->getFontSizePt();
        }
        if(!empty($options['fontstyle'])){
            $fontStyle = $options['fontstyle'];
        }else{
            $fontStyle = $this->getFontStyle();
        }
        if(!empty($options['textcolor'])){
            $this->SetTextColorArray($this->convertHTMLColorToDec($options['textcolor']));
        }else{
            $this->SetTextColor(0, 0, 0);//black
        }

        $this->SetFont($fontFamily, $fontStyle, $fontSize);
        
        return $options;
    }
    
    /**
    * This is method is fix for a better handling of the count. This method now handle the line break
    * between words.
    * This method returns the estimated number of lines required to print the text.
    * @param string $txt text to print
    * @param float $w width of cell. If 0, they extend up to the right margin of the page.
    * @return int Return the estimated number of lines.
    * @access public
    * @since 4.5.011
    * @OVERRIDE
    */
    public function getNumLines($txt, $w=0) {
        $lines = 0;
        if (empty($w) OR ($w <= 0)) {
            if ($this->rtl) {
                $w = $this->x - $this->lMargin;
            } else {
                $w = $this->w - $this->rMargin - $this->x;
            }
        }
        // max column width
        $wmax = $w - (2 * $this->cMargin);
        // remove carriage returns
        $txt = str_replace("\r", '', $txt);
        // divide text in blocks
        $txtblocks = explode("\n", $txt);
        // for each block;
        foreach ($txtblocks as $block) {
            // estimate the number of lines
            if(empty($block)){
                $lines++;
            // If the block is in more than one line
            }else if(ceil($this->GetStringWidth($block) / $wmax)>1){
                //devide in words
                $words = explode(" ", $block);
                //TODO explode with space is not the best things to do...
                $wordBlock = "";
                $first=true;
                $lastNum = 0;
                $run = false;
                
                for($i=0; $i<count($words); $i++){
                    if($first){
                        $wordBlock = $words[$i];
                    }else{
                        $wordBlock .= " ".$words[$i];
                    }
                    if(ceil($this->GetStringWidth($wordBlock) / $wmax)>1){
                        if($first){
                            $lastNum = ceil($this->GetStringWidth($wordBlock) / $wmax);
                            $run = true;
                            $first = false;
                        }else{
                            if($run && $lastNum == ceil($this->GetStringWidth($wordBlock) / $wmax)){
                                // save the number of line if it is the last loop
                                if($i+1 == count($words)){
                                    $lines += ceil($this->GetStringWidth($wordBlock) / $wmax);
                                }
                                continue;
                            }else{
                                $first = true;
                                $lines += ceil($this->GetStringWidth( substr($wordBlock, 0, (strlen($wordBlock) - strlen(" ".$words[$i]))) ) / $wmax);
                                $i--;
                                $lastNum = 0;
                                $run = false;
                            }
                        }

                    }else{
                        $first = false;
                    }
                    // save the number of line if it is the last loop
                    if($i+1 == count($words)){
                        $lines += ceil($this->GetStringWidth($wordBlock) / $wmax);
                    }
                }
                
            }else{
                $lines++;
            }
        }
        return $lines;
    }
}

