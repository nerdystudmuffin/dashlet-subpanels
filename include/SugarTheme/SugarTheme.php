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

/*********************************************************************************

 * Description:  Contains a variety of utility functions used to display UI
 * components such as form headers and footers.  Intended to be modified on a per
 * theme basis.
 ********************************************************************************/

if(!defined('JSMIN_AS_LIB'))
    define('JSMIN_AS_LIB', true);

require_once("include/SugarTheme/cssmin.php");
require_once("jssource/jsmin.php");
require_once('include/utils/sugar_file_utils.php');
 
/**
 * Class that provides tools for working with a theme.
 */
class SugarTheme
{
    /**
     * Theme name
     *
     * @var string
     */
    protected $name;
    
    /**
     * Theme description
     *
     * @var string
     */
    protected $description;
    
    /**
     * Theme directory name
     *
     * @var string
     */
    protected $dirName;
    
    /**
     * Parent theme name
     * 
     * @var string
     */
    protected $parentTheme;
    
    /**
     * Colors sets provided by the theme
     *
     * @var array
     */
    protected $colors = array();
    
    /**
     * Font sets provided by the theme
     *
     * @var array
     */
    protected $fonts  = array();
    
    /**
     * Max number of tabs shown at the top of the screen supported
     *
     * @var int
     */
    protected $maxTabs = 12;
    
    /**
     * Colors used in bar charts
     *
     * @var array
     */
    protected $barChartColors = array(
        "docBorder"             => "0xffffff",
        "docBg1"                => "0xffffff",
        "docBg2"                => "0xffffff",
        "xText"                 => "0x33485c",
        "yText"                 => "0x33485c",
        "title"                 => "0x333333",
        "misc"                  => "0x999999",
        "altBorder"             => "0xffffff",
        "altBg"                 => "0xffffff",
        "altText"               => "0x666666",
        "graphBorder"           => "0xcccccc",
        "graphBg1"              => "0xf6f6f6",
        "graphBg2"              => "0xf6f6f6",
        "graphLines"            => "0xcccccc",
        "graphText"             => "0x333333",
        "graphTextShadow"       => "0xf9f9f9",
        "barBorder"             => "0xeeeeee",
        "barBorderHilite"       => "0x333333",
        "legendBorder"          => "0xffffff",
        "legendBg1"             => "0xffffff",
        "legendBg2"             => "0xffffff",
        "legendText"            => "0x444444",
        "legendColorKeyBorder"  => "0x777777",
        "scrollBar"             => "0xcccccc",
        "scrollBarBorder"       => "0xeeeeee",
        "scrollBarTrack"        => "0xeeeeee",
        "scrollBarTrackBorder"  => "0xcccccc",
        );

    /**
     * Colors used in pie charts
     *
     * @var array
     */
    protected $pieChartColors = array(
        "docBorder"             => "0xffffff",
        "docBg1"                => "0xffffff",
        "docBg2"                => "0xffffff",
        "title"                 => "0x333333",
        "subtitle"              => "0x666666",
        "misc"                  => "0x999999",
        "altBorder"             => "0xffffff",
        "altBg"                 => "0xffffff",
        "altText"               => "0x666666",
        "graphText"             => "0x33485c",
        "graphTextShadow"       => "0xf9f9f9",
        "pieBorder"             => "0xffffff",
        "pieBorderHilite"       => "0x333333",
        "legendBorder"          => "0xffffff",
        "legendBg1"             => "0xffffff",
        "legendBg2"             => "0xffffff",
        "legendText"            => "0x444444",
        "legendColorKeyBorder"  => "0x777777",
        "scrollBar"             => "0xdfdfdf",
        "scrollBarBorder"       => "0xfafafa",
        "scrollBarTrack"        => "0xeeeeee",
        "scrollBarTrackBorder"  => "0xcccccc",
        );
    
    /**
     * Cache built of all css files locations
     *
     * @var array
     */
    private $_cssCache = array();
    
    /**
     * Cache built of all image files locations
     *
     * @var array
     */
    private $_imageCache = array();
    
    /**
     * Cache built of all javascript files locations
     *
     * @var array
     */
    private $_jsCache = array();
    
    /**
     * Cache built of all template files locations
     *
     * @var array
     */
    private $_templateCache = array();
    
    /**
     * Size of the caches after the are initialized in the constructor
     *
     * @var array
     */
    private $_initialCacheSize = array(
        'cssCache'      => 0,
        'imageCache'    => 0,
        'jsCache'       => 0,
        'templateCache' => 0,
        );
    
    /**
     * Controls whether or not to clear the cache on destroy; defaults to false
     */
    private $_clearCacheOnDestroy = false;
    
    /**
     * Constructor
     *
     * Sets the theme properties from the defaults passed to it, and loads the file path cache from an external cache
     *
     * @param  $defaults string defaults for the current theme
     */
    public function __construct(
        $defaults
        )
    {
        // apply parent theme's properties first
        if ( isset($defaults['parentTheme']) ) {
            $themedef = array();
            include("themes/{$defaults['parentTheme']}/themedef.php");
            foreach ( $themedef as $key => $value ) {
                if ( property_exists(__CLASS__,$key) ) {
                    if ( is_array($this->$key) )
                        $this->$key = array_merge($this->$key,$value);
                    else
                        $this->$key = $value;
                }
            }
        }
        foreach ( $defaults as $key => $value ) {
            if ( property_exists(__CLASS__,$key) ) {
                if ( is_array($this->$key) )
                    $this->$key = array_merge($this->$key,$value);
                else
                    $this->$key = $value;
            }
        }
        if ( !inDeveloperMode() ) {
            // load stored theme cache from sugar cache if it's there
            if ( isset($GLOBALS['external_cache_object']) 
                    && $GLOBALS['external_cache_type'] != 'base-in-memory' ) {
                $this->_jsCache       = sugar_cache_retrieve('theme_'.$this->dirName.'_jsCache');
                $this->_cssCache      = sugar_cache_retrieve('theme_'.$this->dirName.'_cssCache');
                $this->_imageCache    = sugar_cache_retrieve('theme_'.$this->dirName.'_imageCache');
                $this->_templateCache = sugar_cache_retrieve('theme_'.$this->dirName.'_templateCache');
            }
            // otherwise, see if we serialized them to a file
            elseif ( sugar_is_file($GLOBALS['sugar_config']['cache_dir'].$this->getFilePath().'/pathCache.php') ) {
                $caches = unserialize(file_get_contents($GLOBALS['sugar_config']['cache_dir'].$this->getFilePath().'/pathCache.php'));
                if ( isset($caches['jsCache']) )
                    $this->_jsCache       = $caches['jsCache'];
                if ( isset($caches['cssCache']) )
                    $this->_cssCache      = $caches['cssCache'];
                if ( isset($caches['imageCache']) )
                    $this->_imageCache    = $caches['imageCache'];
                if ( isset($caches['templateCache']) )
                    $this->_templateCache = $caches['templateCache'];
            }
        }
        $this->_initialCacheSize = array(
            'jsCache'       => count($this->_jsCache),
            'cssCache'      => count($this->_cssCache),
            'imageCache'    => count($this->_imageCache),
            'templateCache' => count($this->_templateCache),
            );
    }
    
    /**
     * Destructor
     * Here we'll write out the internal file path caches to an external cache of some sort.
     */
    public function __destruct()
    {
        // Bug 28309 - Set the current directory to one which we expect it to be (i.e. the root directory of the install
        set_include_path(realpath(dirname(__FILE__) . '/../..') . PATH_SEPARATOR . get_include_path());
        chdir(realpath(dirname(__FILE__) . '/../..'));
        
        // clear out the cache on destroy if we are asked to
        if ( $this->_clearCacheOnDestroy ) {
            if (is_file($GLOBALS['sugar_config']['cache_dir'].$this->getFilePath().'/pathCache.php'))
                unlink($GLOBALS['sugar_config']['cache_dir'].$this->getFilePath().'/pathCache.php');
            if ( isset($GLOBALS['external_cache_object']) 
                    && $GLOBALS['external_cache_type'] != 'base-in-memory' ) {
                sugar_cache_clear('theme_'.$this->dirName.'_jsCache');
                sugar_cache_clear('theme_'.$this->dirName.'_cssCache');
                sugar_cache_clear('theme_'.$this->dirName.'_imageCache');
                sugar_cache_clear('theme_'.$this->dirName.'_templateCache');
            }
        }
        elseif ( !inDeveloperMode() ) {
            // push our cache into the sugar cache
            if ( isset($GLOBALS['external_cache_object']) 
                    && $GLOBALS['external_cache_type'] != 'base-in-memory' ) {
                // only update the caches if they have been changed in this request
                if ( count($this->_jsCache) != $this->_initialCacheSize['jsCache'] )
                    sugar_cache_put('theme_'.$this->dirName.'_jsCache',$this->_jsCache);
                if ( count($this->_cssCache) != $this->_initialCacheSize['cssCache'] )
                    sugar_cache_put('theme_'.$this->dirName.'_cssCache',$this->_cssCache);
                if ( count($this->_imageCache) != $this->_initialCacheSize['imageCache'] )
                    sugar_cache_put('theme_'.$this->dirName.'_imageCache',$this->_imageCache);
                if ( count($this->_templateCache) != $this->_initialCacheSize['templateCache'] )
                    sugar_cache_put('theme_'.$this->dirName.'_templateCache',$this->_templateCache);
            }
            // fallback in case there is no useful external caching available
            // only update the caches if they have been changed in this request
            elseif ( count($this->_jsCache) != $this->_initialCacheSize['jsCache'] 
                    || count($this->_cssCache) != $this->_initialCacheSize['cssCache']
                    || count($this->_imageCache) != $this->_initialCacheSize['imageCache']
                    || count($this->_templateCache) != $this->_initialCacheSize['templateCache']
                ) {
                sugar_file_put_contents(
                    create_cache_directory($this->getFilePath().'/pathCache.php'),
                    serialize(
                        array(
                            'jsCache'       => $this->_jsCache,
                            'cssCache'      => $this->_cssCache,
                            'imageCache'    => $this->_imageCache,
                            'templateCache' => $this->_templateCache,
                            )
                        )
                    );
                
            }
        }
        // clear out the cache if we are in developerMode 
        // ( so it will be freshly rebuilt for the next load )
        elseif ( isset($GLOBALS['external_cache_object']) ) {
            sugar_cache_clear('theme_'.$this->dirName.'_jsCache');
            sugar_cache_clear('theme_'.$this->dirName.'_cssCache');
            sugar_cache_clear('theme_'.$this->dirName.'_imageCache');
            sugar_cache_clear('theme_'.$this->dirName.'_templateCache');
        }
    }
    
    /**
     * Specifies what is returned when the object is cast to a string, in this case it will be the
     * theme directory name.
     *
     * @return string theme directory name
     */
    public function __toString() 
    {
        return $this->dirName;
    }
    
    /**
     * Generic public accessor method for all the properties of the theme ( which are kept protected )
     *
     * @return string
     */
    public function __get(
        $key
        )
    {
        if ( isset($this->$key) )
            return $this->$key;
    }
    
    /**
     * Clears out the caches used for this themes
     */
    public function clearCache()
    {
        $this->_clearCacheOnDestroy = true;
    }
    
    /**
     * Return array of all valid fields that can be specified in the themedef.php file
     * 
     * @return array
     */
    public static function getThemeDefFields()
    {
        return array(
            'name',
            'description',
            'dirName',
            'parentTheme',
            'colors',
            'fonts',
            'maxTabs',
            'pngSupport',
            'barChartColors',
            'pieChartColors',
            );
    }
    
    /**
     * Returns the file path of the current theme
     *
     * @return string
     */
    public function getFilePath()
    {
        return 'themes/'.$this->dirName;
    }
    
    /**
     * Returns the image path of the current theme
     *
     * @return string
     */
    public function getImagePath()
    {
        return $this->getFilePath().'/images';
    }
    
    /**
     * Returns the css path of the current theme
     *
     * @return string
     */
    public function getCSSPath()
    {
        return $this->getFilePath().'/css';
    }
    
    /**
     * Returns the javascript path of the current theme
     *
     * @return string
     */
    public function getJSPath()
    {
        return $this->getFilePath().'/js';
    }
    
    /**
     * Returns the tpl path of the current theme
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return $this->getFilePath().'/tpls';
    }
    
    /**
     * Returns the file path of the theme defaults
     *
     * @return string
     */
    public final function getDefaultFilePath()
    {
        return 'themes/default';
    }
    
    /**
     * Returns the image path of the theme defaults
     *
     * @return string
     */
    public final function getDefaultImagePath()
    {
        return $this->getDefaultFilePath().'/images';
    }
    
    /**
     * Returns the css path of the theme defaults
     *
     * @return string
     */
    public final function getDefaultCSSPath()
    {
        return $this->getDefaultFilePath().'/css';
    }
    
    /**
     * Returns the template path of the theme defaults
     *
     * @return string
     */
    public final function getDefaultTemplatePath()
    {
        return $this->getDefaultFilePath().'/tpls';
    }
    
    /**
     * Returns the javascript path of the theme defaults
     *
     * @return string
     */
    public final function getDefaultJSPath()
    {
        return $this->getDefaultFilePath().'/js';
    }
    
    /**
     * Returns CSS for the current theme.
     * 
     * @param  $color string optional, specifies the css color file to use if the theme supports it; defaults to cookie value or theme default
     * @param  $font  string optional, specifies the css font file to use if the theme supports it; defaults to cookie value or theme default
     * @return string HTML code
     */
    public function getCSS(
        $color = null,
        $font = null
        )
    {
        // include style.css file
        $html = '<link rel="stylesheet" type="text/css" href="'.$this->getCSSURL('style.css').'" />';
        
        if ( !empty($this->colors) ) {
            // build cache of all css files
            foreach ( $this->colors as $colorcss )
                $this->getCSSURL('colors.'.$colorcss.'.css');
            if ( !isset($color) || !in_array($color,$this->colors) ) {
                if ( isset($_SESSION['authenticated_user_theme_color']) && in_array($_SESSION['authenticated_user_theme_color'], $this->colors))
                    $color = $_SESSION['authenticated_user_theme_color'];
                else
                    $color = $this->colors[0];
            }
            $html .= '<link rel="stylesheet" type="text/css" href="'.$this->getCSSURL('colors.'.$color.'.css').'" id="current_color_style" />';
        }
        
        if ( !empty($this->fonts) ) {
            // build cache of all css files
            foreach ( $this->fonts as $fontcss )
                $this->getCSSURL('fonts.'.$fontcss.'.css');
            if ( !isset($font) || !in_array($font,$this->fonts) ) {
                if ( isset($_SESSION['authenticated_user_theme_font']) && in_array($_SESSION['authenticated_user_theme_font'], $this->fonts))
                    $font = $_SESSION['authenticated_user_theme_font'];
                else
                    $font = $this->fonts[0];
            }
            $html .= '<link rel="stylesheet" type="text/css" href="'.$this->getCSSURL('fonts.'.$font.'.css').'" id="current_font_style" />';
        }
        
        return $html;
    }
    
    /**
     * Returns javascript for the current theme
     * 
     * @return string HTML code
     */
    public function getJS()
    {
        $styleJS = $this->getJSURL('style.js');
        
        return <<<EOHTML
<script type="text/javascript" src="$styleJS"></script>
EOHTML;
    }
    
    /**
     * Returns the path for the tpl file in the current theme. If not found in the current theme, will revert
     * to looking in the base theme.
     * 
     * @param  string $templateName tpl file name
     * @return string path of tpl file to include
     */
    public function getTemplate(
        $templateName
        )
    {
        if ( isset($this->_templateCache[$templateName]) )
            return $this->_templateCache[$templateName];
        
        $templatePath = '';
        if (sugar_is_file('custom/'.$this->getTemplatePath().'/'.$templateName))
            $templatePath = 'custom/'.$this->getTemplatePath().'/'.$templateName;
        elseif (sugar_is_file($this->getTemplatePath().'/'.$templateName)) 
            $templatePath = $this->getTemplatePath().'/'.$templateName;
        elseif (isset($this->parentTheme) 
                && SugarThemeRegistry::get($this->parentTheme) instanceOf SugarTheme
                && ($filename = SugarThemeRegistry::get($this->parentTheme)->getTemplate($templateName)) != '')
            $templatePath = $filename;
        elseif (sugar_is_file('custom/'.$this->getDefaultTemplatePath().'/'.$templateName))
            $templatePath = 'custom/'.$this->getDefaultTemplatePath().'/'.$templateName;
        elseif (sugar_is_file($this->getDefaultTemplatePath().'/'.$templateName))
            $templatePath = $this->getDefaultTemplatePath().'/'.$templateName;
        else {
            $GLOBALS['log']->warn("Template $templateName not found");
            return;
        }
        
        $this->_imageCache[$templateName] = $templatePath;
        
        return $templatePath;
    }
    
    /**
     * Returns an image tag for the given image.
     *
     * @param  string $image image name
     * @param  string $other_attributes optional, other attributes to add to the image tag, not cached
     * @param  string $width optional, defaults to the actual image's width
     * @param  string $height optional, defaults to the actual image's height
     * @return string HTML image tag
     */
    public function getImage(
        $imageName,
        $other_attributes = '',
        $width = null,
        $height = null
        )
    {
        static $cached_results = array();

        if(!empty($cached_results[$imageName]))
            return $cached_results[$imageName]."$other_attributes>";
    
        $imageName .= '.gif';
        $imageURL = $this->getImageURL($imageName,false);
        if ( empty($imageURL) )
            return false;
        
        $size = getimagesize($imageURL);
        if ( is_null($width) ) 
            $width = $size[0];
        if ( is_null($height) ) 
            $height = $size[1];
        
        // Cache everything but the other attributes....
        $cached_results[$imageName] = "<img src=\"". getJSPath($imageURL) ."\" width=\"$width\" height=\"$height\" ";
        
        return $cached_results[$imageName] . "$other_attributes />";
    }
    
    /**
     * Returns the URL for an image in the current theme. If not found in the current theme, will revert
     * to looking in the base theme.
     * 
     * @param  string $imageName image file name
     * @param  bool   $addJSPath call getJSPath() with the results to add some unique image tracking support
     * @return string path to image
     */
    public function getImageURL(
        $imageName,
        $addJSPath = true
        )
    {
        if ( isset($this->_imageCache[$imageName]) )
            return $this->_imageCache[$imageName];
        
        $imagePath = '';
        if (($filename = $this->_getImageFileName('custom/'.$this->getImagePath().'/'.$imageName)) != '')
            $imagePath = $filename;
        elseif (($filename = $this->_getImageFileName($this->getImagePath().'/'.$imageName)) != '')
            $imagePath = $filename;
        elseif (isset($this->parentTheme) 
                && SugarThemeRegistry::get($this->parentTheme) instanceOf SugarTheme
                && ($filename = SugarThemeRegistry::get($this->parentTheme)->getImageURL($imageName,false)) != '')
            $imagePath = $filename;
        elseif (($filename = $this->_getImageFileName('custom/'.$this->getDefaultImagePath().'/'.$imageName)) != '')
            $imagePath = $filename;
        elseif (($filename = $this->_getImageFileName($this->getDefaultImagePath().'/'.$imageName)) != '')
            $imagePath = $filename;
        else {
            $GLOBALS['log']->warn("Image $imageName not found");
            return;
        }
        
        $this->_imageCache[$imageName] = $imagePath;
        
        if ( $addJSPath )
            return getJSPath($imagePath);
        
        return $imagePath;
    }
    
    /**
     * Checks for an image using all of the accepted image extensions
     *
     * @param  string $imageName image file name
     * @return string path to image
     */
    protected function _getImageFileName(
        $imageName
        )
    {
        // return now if the extension matches that of which we are looking for
        if ( sugar_is_file($imageName) ) 
            return $imageName;
        
        $extensions = array(
            'gif',
            'png',
            'jpg',
            'tif',
            'bmp',
            );
        
        $pathParts = pathinfo($imageName);
        foreach ( $extensions as $extension )
        if ( ( $extension != $pathParts['extension'] )
                && sugar_is_file($pathParts['dirname'].'/'.$pathParts['filename'].'.'.$extension) )
            return $pathParts['dirname'].'/'.$pathParts['filename'].'.'.$extension;
        
        return '';
    }
    
    /**
     * Returns the URL for the css file in the current theme. If not found in the current theme, will revert
     * to looking in the base theme.
     * 
     * @param  string $cssFileName css file name
     * @param  bool   $addJSPath call getJSPath() with the results to add some unique image tracking support
     * @return string path of css file to include
     */
    public function getCSSURL(
        $cssFileName,
        $addJSPath = true
        )
    {
        if ( isset($this->_cssCache[$cssFileName]) )
            return $this->_cssCache[$cssFileName];
        
        $cssFileContents = '';
        if (isset($this->parentTheme) 
                && SugarThemeRegistry::get($this->parentTheme) instanceOf SugarTheme
                && ($filename = SugarThemeRegistry::get($this->parentTheme)->getCSSURL($cssFileName,false)) != '')
            $cssFileContents .= file_get_contents($filename);
        else {
            if (sugar_is_file($this->getDefaultCSSPath().'/'.$cssFileName))
                $cssFileContents .= file_get_contents($this->getDefaultCSSPath().'/'.$cssFileName);
            if (sugar_is_file('custom/'.$this->getDefaultCSSPath().'/'.$cssFileName))
                $cssFileContents .= file_get_contents('custom/'.$this->getDefaultCSSPath().'/'.$cssFileName);
        }
        if (sugar_is_file($this->getCSSPath().'/'.$cssFileName))
            $cssFileContents .= file_get_contents($this->getCSSPath().'/'.$cssFileName);
        if (sugar_is_file('custom/'.$this->getCSSPath().'/'.$cssFileName))
            $cssFileContents .= file_get_contents('custom/'.$this->getCSSPath().'/'.$cssFileName);
        if (empty($cssFileContents)) {
            $GLOBALS['log']->warn("CSS File $cssFileName not found");
            return;
        }
        
        // fix any image references that may be defined in css files
        $cssFileContents = str_ireplace("entryPoint=getImage&",
            "entryPoint=getImage&themeName={$this->dirName}&",
            $cssFileContents);
        
        // create the cached file location
        $cssFilePath = create_cache_directory($this->getCSSPath()."/$cssFileName");
        
        // if this is the style.css file, prepend the base.css and calendar-win2k-cold-1.css 
        // files before the theme styles
        if ( $cssFileName == 'style.css' && !isset($this->parentTheme) ) {
            $cssFileContents = file_get_contents('jscalendar/calendar-win2k-cold-1.css') . $cssFileContents;
            if ( inDeveloperMode() )
                $cssFileContents = file_get_contents('include/javascript/yui/build/base/base.css') . $cssFileContents;
            else
                $cssFileContents = file_get_contents('include/javascript/yui/build/base/base-min.css') . $cssFileContents;
            $cssFileContents = file_get_contents($this->getCSSURL('yui.css',false)) . $cssFileContents;
        }
        
        // minify the css
        if ( !inDeveloperMode() ) {
            $cssFileContents = cssmin::minify($cssFileContents);
        }
        
        // now write the css to cache
        sugar_file_put_contents($cssFilePath,$cssFileContents);
        
        $this->_cssCache[$cssFileName] = $cssFilePath;
        
        if ( $addJSPath )
            return getJSPath($cssFilePath);
        
        return $cssFilePath;
    }
    
    /**
     * Returns the URL for an image in the current theme. If not found in the current theme, will revert
     * to looking in the base theme.
     * 
     * @param  string $jsFileName js file name
     * @param  bool   $addJSPath call getJSPath() with the results to add some unique image tracking support
     * @return string path to js file
     */
    public function getJSURL(
        $jsFileName,
        $addJSPath = true
        )
    {
        if ( isset($this->_jsCache[$jsFileName]) )
            return $this->_jsCache[$jsFileName];
        
        $jsFileContents = '';
        
        if (isset($this->parentTheme) 
                && SugarThemeRegistry::get($this->parentTheme) instanceOf SugarTheme
                && ($filename = SugarThemeRegistry::get($this->parentTheme)->getJSURL($jsFileName,false)) != '')
            $jsFileContents .= file_get_contents($filename);
        else {
            if (sugar_is_file($this->getDefaultJSPath().'/'.$jsFileName))
                $jsFileContents .= file_get_contents($this->getDefaultJSPath().'/'.$jsFileName);
            if (sugar_is_file('custom/'.$this->getDefaultJSPath().'/'.$jsFileName))
                $jsFileContents .= file_get_contents('custom/'.$this->getDefaultJSPath().'/'.$jsFileName);
        }
        if (sugar_is_file($this->getJSPath().'/'.$jsFileName))
            $jsFileContents .= file_get_contents($this->getJSPath().'/'.$jsFileName);
        if (sugar_is_file('custom/'.$this->getJSPath().'/'.$jsFileName))
            $jsFileContents .= file_get_contents('custom/'.$this->getJSPath().'/'.$jsFileName);
        if (empty($jsFileContents)) {
            $GLOBALS['log']->warn("Javascript File $jsFileName not found");
            return;
        }
        
        // create the cached file location
        $jsFilePath = create_cache_directory($this->getJSPath()."/$jsFileName");
        
        // now write the js to cache
        sugar_file_put_contents($jsFilePath,$jsFileContents);
        
        // minify the js
        if ( !inDeveloperMode() ) {
            $jsFilePathMin = str_replace('.js','-min.js',$jsFilePath);
            $jMin = new JSMin($jsFilePath,$jsFilePathMin);
            $jMin->minify();
            $jsFilePath = $jsFilePathMin;
        }
        
        $this->_jsCache[$jsFileName] = $jsFilePath;
        
        if ( $addJSPath )
            return getJSPath($jsFilePath);
        
        return $jsFilePath;
    }
    
    /**
     * Returns the base URL for the instance
     */
    public function getImageServerURL()
    {
        $image_server = '';
        if(defined('TEMPLATE_URL'))
            $image_server = TEMPLATE_URL . '/';
        
        return $image_server;
    }
    
    /**
     * Returns an array of all of the images available for the current theme
     *
     * @return array
     */
    public function getAllImages()
    {
        // first, lets get all the paths of where to look
        $pathsToSearch = array($this->getImagePath());
        $theme = $this;
        while (isset($theme->parentTheme) && SugarThemeRegistry::get($theme->parentTheme) instanceOf SugarTheme ) {
            $theme = SugarThemeRegistry::get($theme->parentTheme);
            $pathsToSearch[] = $theme->getImagePath();
        }
        $pathsToSearch[] = $this->getDefaultImagePath();
        
        // now build the array
        $imageArray = array();
        foreach ( $pathsToSearch as $path )
        {
            if (!sugar_is_dir($path)) $path = "custom/$path";
            if (sugar_is_dir($path) && $dir = opendir($path)) {
                while (($file = readdir($dir)) !== false) {
                    if ($file == ".." 
                            || $file == "."
                            || $file == ".svn"
                            || $file == "CVS" 
                            || $file == "Attic"
                            )
                        continue;
                    if ( !isset($imageArray[$file]) )
                        $imageArray[$file] = $this->getImageURL($file,false);
                }
                closedir($dir);
            }
        }
        
        ksort($imageArray);
        
        return $imageArray;
    }

}

/**
 * Registry for all the current classes in the system
 */
class SugarThemeRegistry
{
    /**
     * Array of all themes and thier object
     *
     * @var array
     */
    private static $_themes = array();
    
    /**
     * Name of the current theme; corresponds to an index key in SugarThemeRegistry::$_themes
     *
     * @var string
     */
    private static $_currentTheme;
    
    /**
     * Disable the constructor since this will be a singleton
     */
    private function __construct() {}
    
    /**
     * Adds a new theme to the registry
     *
     * @param $themedef array
     */
    public static function add(
        array $themedef
        )
    {
        $theme = new SugarTheme($themedef);
        self::$_themes[$theme->dirName] = $theme;
    }
    
    /**
     * Removes a new theme from the registry
     *
     * @param $themeName string
     */
    public static function remove(
        $themeName
        )
    {
        if ( self::exists($themeName) )
            unset(self::$_themes[$themeName]);
    }
    
    /**
     * Returns a theme object in the registry specified by the given $themeName
     *
     * @param $themeName string
     */
    public static function get(
        $themeName
        )
    {
        if ( isset(self::$_themes[$themeName]) )
            return self::$_themes[$themeName];
    }
    
    /**
     * Returns the current theme object
     *
     */
    public static function current()
    {
        if ( !isset(self::$_currentTheme) )
            self::buildRegistry();
        
        return self::$_themes[self::$_currentTheme];
    }
    
    /**
     * Returns true if a theme object specified by the given $themeName exists in the registry
     *
     * @param  $themeName string
     * @return bool
     */
    public static function exists(
        $themeName
        )
    {
        return (self::get($themeName) !== null);
    }
    
    /**
     * Sets the given $themeName to be the current theme
     *
     * @param  $themeName string
     */
    public static function set(
        $themeName
        )
    {
        if ( !self::exists($themeName) ) 
            return false;
        
        self::$_currentTheme = $themeName;
        
        // set some of the expected globals
        $GLOBALS['barChartColors'] = self::current()->barChartColors;
        $GLOBALS['pieChartColors'] = self::current()->pieChartColors;
    }
    
    /**
     * Builds the theme registry
     */
    public static function buildRegistry()
    {
        self::$_themes = array();
        $dirs = array("themes/","custom/themes/");
        
        foreach ($dirs as $dirPath ) {
            if (sugar_is_dir('./'.$dirPath) && $dir = opendir('./'.$dirPath)) {
                while (($file = readdir($dir)) !== false) {
                    if ($file == ".." 
                            || $file == "."
                            || $file == ".svn"
                            || $file == "CVS" 
                            || $file == "Attic" 
                            || !sugar_is_dir("./$dirPath".$file)
                            || !sugar_is_file("./{$dirPath}{$file}/themedef.php")
                            )
                        continue;
                    $themedef = array();
                    require("./{$dirPath}{$file}/themedef.php");
                    $themedef['dirName'] = $file;
                    // check for theme already existing in the registry
                    // if so, then it will override the current one
                    if ( self::exists($themedef['dirName']) ) {
                        $existingTheme = self::get($themedef['dirName']);
                        foreach ( SugarTheme::getThemeDefFields() as $field )
                            if ( !isset($themedef[$field]) )
                                $themedef[$field] = $existingTheme->$field;
                        self::remove($themedef['dirName']);
                    }
                    if ( isset($themedef['name']) ) {
                        self::add($themedef);
                    }
                }
                closedir($dir);
            }
        }
        
        // default to setting the default theme as the current theme
        self::set($GLOBALS['sugar_config']['default_theme']);
    }
    
    /**
     * Returns an array of available themes. Designed to be absorbed into get_select_options_with_id()
     *
     * @return array
     */
    public static function availableThemes()
    {
        $themelist = array();
        $disabledThemes = array();
        if ( isset($GLOBALS['sugar_config']['disabled_themes']) )
            $disabledThemes = explode(',',$GLOBALS['sugar_config']['disabled_themes']);
        
        foreach ( self::$_themes as $themename => $themeobject ) {
            if ( in_array($themename,$disabledThemes) )
                continue;
            $themelist[$themeobject->dirName] = $themeobject->name;
        }
        
        return $themelist;
    }
    
    /**
     * Returns an array of un-available themes. Designed used with the theme selector in the admin panel
     *
     * @return array
     */
    public static function unAvailableThemes()
    {
        $themelist = array();
        $disabledThemes = array();
        if ( isset($GLOBALS['sugar_config']['disabled_themes']) )
            $disabledThemes = explode(',',$GLOBALS['sugar_config']['disabled_themes']);
        
        foreach ( self::$_themes as $themename => $themeobject ) {
            if ( in_array($themename,$disabledThemes) )
                $themelist[$themeobject->dirName] = $themeobject->name;
        }
        
        return $themelist;
    }
    
    /**
     * Returns an array of all themes found in the current installation
     *
     * @return array
     */
    public static function allThemes()
    {
        $themelist = array();
        
        foreach ( self::$_themes as $themename => $themeobject )
            $themelist[$themeobject->dirName] = $themeobject->name;
        
        return $themelist;
    }
    
    /**
     * Clears out the cached path locations for all themes
     */
    public static function clearAllCaches()
    {
        foreach ( self::$_themes as $themeobject ) {
            $themeobject->clearCache();
        }
    }
}
