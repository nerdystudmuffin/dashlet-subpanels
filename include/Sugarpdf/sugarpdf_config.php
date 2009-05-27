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

// set alternative config file
if (!defined('K_TCPDF_EXTERNAL_CONFIG')) {
    
    /*
     *  Installation path of TCPDF
     */
    define ("K_PATH_MAIN", "include/tcpdf/");
    /**
     * URL path to tcpdf installation folder
     */
    define ("K_PATH_URL", "include/tcpdf/");
    /**
     * path for PDF fonts
     * use K_PATH_MAIN.'fonts/old/' for old non-UTF8 fonts
     */
    define ("K_PATH_FONTS", K_PATH_MAIN."fonts/");
    /**
     * cache directory for temporary files (full path)
     */
    define ("K_PATH_CACHE", K_PATH_MAIN."cache/");
    /**
     * cache directory for temporary files (url path)
     */
    define ("K_PATH_URL_CACHE", K_PATH_URL."cache/");
    /*
     * Default path for images
     */
     
    define ("K_PATH_IMAGES", "themes/default/images/");
    /*
     * Blank image
     */
    define ("K_BLANK_IMAGE", K_PATH_IMAGES."_blank.png");
    /*
     * The format used for pages.
     * It can be either one of the following values (case insensitive)
     * or a custom format in the form of a two-element array containing
     * the width and the height (expressed in the unit given by unit).
     * 4A0, 2A0, A0, A1, A2, A3, A4 (default), A5, A6, A7, A8, A9, A10, 
     * B0, B1, B2, B3, B4, B5, B6, B7, B8, B9, B10, C0, C1, C2, C3, C4, 
     * C5, C6, C7, C8, C9, C10, RA0, RA1, RA2, RA3, RA4, SRA0, SRA1, 
     * SRA2, SRA3, SRA4, LETTER, LEGAL, EXECUTIVE, FOLIO.
     */
    defineFromUserPreference ("PDF_PAGE_FORMAT", "A4");
    define("PDF_PAGE_FORMAT_LIST", implode(",",array("4A0", "2A0", "A0", "A1", "A2", "A3", "A4", "A5", "A6", "A7", "A8", "A9", "A10", 
                                        "B0", "B1", "B2", "B3", "B4", "B5", "B6", "B7", "B8", "B9", "B10", 
                                        "C0", "C1", "C2", "C3", "C4", "C5", "C6", "C7", "C8", "C9", "C10", 
                                        "RA0", "RA1", "RA2", "RA3", "RA4", "SRA0", "SRA1", "SRA2", "SRA3", "SRA4", 
                                        "LETTER", "LEGAL", "EXECUTIVE", "FOLIO")));
    /*
     * page orientation. Possible values are (case insensitive):P or Portrait (default), L or Landscape.
     */
    defineFromUserPreference ("PDF_PAGE_ORIENTATION", "P");
    define("PDF_PAGE_ORIENTATION_LIST", implode(",",array("P"=>"P", "L"=>"L")));
    /*
     * Defines the creator of the document. This is typically the name of the application that generates the PDF.
     */
    defineFromConfig("PDF_CREATOR", "SugarCRM");
    /*
     * Defines the author of the document.
     */
    defineFromConfig("PDF_AUTHOR", "SugarCRM");
     /**
     * header title
     */
    defineFromConfig("PDF_HEADER_TITLE", "SugarCRM");
     /**
     * header description string
     */
    defineFromConfig("PDF_HEADER_STRING", "TCPDF for SugarCRM");
    /**
     * image logo for the default Header
     */
    defineFromConfig("PDF_HEADER_LOGO", "company_logo.png");
    /**
     * header logo image width [mm]
     */
    defineFromConfig("PDF_HEADER_LOGO_WIDTH", 60);
    
    /**
     *  document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch]
     */
    defineFromConfig('PDF_UNIT', 'mm');
    
    /**
     * header margin
     */
    defineFromUserPreference ('PDF_MARGIN_HEADER', 5);
    
    /**
     * footer margin
     */
    defineFromUserPreference ('PDF_MARGIN_FOOTER', 10);
    
    /**
     * top margin
     */
    defineFromUserPreference ('PDF_MARGIN_TOP', 27);
    
    /**
     * bottom margin
     */
    defineFromUserPreference ('PDF_MARGIN_BOTTOM', 25);
    
    /**
     * left margin
     */
    defineFromUserPreference ('PDF_MARGIN_LEFT', 15);
    
    /**
     * right margin
     */
    defineFromUserPreference ('PDF_MARGIN_RIGHT', 15);
    
    /**
     * main font name
     */
    defineFromUserPreference ('PDF_FONT_NAME_MAIN', 'helvetica');
    
    /**
     * main font size
     */
    defineFromUserPreference ("PDF_FONT_SIZE_MAIN", 8);
    /**
     * data font name
     */
    defineFromUserPreference ('PDF_FONT_NAME_DATA', 'helvetica');
    
    /**
     * data font size
     */
    defineFromUserPreference ('PDF_FONT_SIZE_DATA', 8);
    
    /**
     * Ratio used to scale the images
     */
    defineFromConfig('PDF_IMAGE_SCALE_RATIO', 4);
    
    /**
     * magnification factor for titles
     */
    defineFromConfig('HEAD_MAGNIFICATION', 1.1);
    
    /**
     * height of cell repect font height
     */
    defineFromConfig('K_CELL_HEIGHT_RATIO', 1.25);
    
    /**
     * title magnification respect main font size
     */
    defineFromConfig('K_TITLE_MAGNIFICATION', 1.3);
    
    /**
     * reduction factor for small font
     */
    defineFromConfig('K_SMALL_RATIO', 2/3);

}
// Sugarpdf define
/**
 * 
 */
defineFromConfig("PDF_CLASS", "TCPDF");
/**
 * Default file name for the generated pdf file.
 */
defineFromConfig("PDF_FILENAME", "output.pdf");

/**
 * 
 */
defineFromConfig("PDF_TITLE", "SugarCRM");

/**
 * 
 */
defineFromConfig("PDF_KEYWORDS", "SugarCRM");

/**
 * 
 */
defineFromConfig("PDF_SUBJECT", "SugarCRM");

/**
 * 
 */
defineFromConfig("PDF_COMPRESSION", "true");

/**
 * 
 */
defineFromConfig("PDF_JPEG_QUALITY", "75");

/**
 * 
 */
defineFromConfig("PDF_PDF_VERSION", "1.7");

/**
 * 
 */
defineFromConfig("PDF_PROTECTION", "");

/**
 * 
 */
defineFromConfig("PDF_USER_PASSWORD", "");

/**
 * 
 */
defineFromConfig("PDF_OWNER_PASSWORD", "");

/**
 * 
 */
defineFromConfig("PDF_ACL_ACCESS", "detail");



define("K_TCPDF_EXTERNAL_CONFIG", true);

/**
 * Function to define a sugarpdf seeting from the admin application settings (config table).
 * This function use the default value if there is nothing in the table.
 * @param $value    settings to search
 * @param $default  default value
 */
function defineFromConfig($value, $default){
    $lowerValue = strtolower($value);
   require_once("modules/Administration/Administration.php");
    $focus = new Administration();
    $focus->retrieveSettings();
    if(isset($focus->settings["sugarpdf_".$lowerValue])){
        define($value, $focus->settings["sugarpdf_".$lowerValue]);
    }else{
        define($value, $default);
    }
}

/**
 * This function define a Sugarpdf setting from the user preferences.
 * This function use the default value if there is no preference.
 * If SUGARPDF_USE_DEFAULT_SETTINGS is define the function will always
 * use the default value.
 * SUGARPDF_USE_FOCUS is use to load the preference of the none current user. To use
 * this constant you have to define a global variable $focus_user.
 * 
 * @param $value    settings to search
 * @param $default  default value
 */
function defineFromUserPreference($value, $default){
    global $focus_user, $current_user;
    $lowerValue = strtolower($value);
    if(defined('SUGARPDF_USE_FOCUS')){
        $pref = $focus_user->getPreference("sugarpdf_".$lowerValue);
    }else{
        $pref = $current_user->getPreference("sugarpdf_".$lowerValue);
    }
    if(!empty($pref) && !defined('SUGARPDF_USE_DEFAULT_SETTINGS')){
        define($value, $pref);
    }else{
        define($value, $default);
    }
    
}
