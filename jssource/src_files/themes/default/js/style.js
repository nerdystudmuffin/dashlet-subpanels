/**
 * style.js javascript file
 *
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
 */
 


/**
 * Handles changing the sub menu items when using grouptabs
 */
YAHOO.util.Event.onAvailable('subModuleList',IKEADEBUG);
function IKEADEBUG()
{
    var moduleLinks = document.getElementById('moduleList').getElementsByTagName("a");
    moduleLinkMouseOver = function() 
        {
            var matches      = /grouptab_([0-9]+)/i.exec(this.id);
            var tabNum       = matches[1];
            var moduleGroups = document.getElementById('subModuleList').getElementsByTagName("span"); 
            for (var i = 0; i < moduleGroups.length; i++) { 
                if ( i == tabNum ) {
                    moduleGroups[i].style.display = "inline";
                }
                else {
                    moduleGroups[i].style.display = "none";
                }
            }
            
            var groupList = document.getElementById('moduleList').getElementsByTagName("li");
            for (var i = 0; i < groupList.length; i++) {
                var aElem = groupList[i].getElementsByTagName("a")[0];
                if ( aElem == null ) {
                    // This is the blank <li> tag at the start of some themes, skip it
                    continue;
                }
                // notCurrentTabLeft, notCurrentTabRight, notCurrentTab
                var classStarter = 'notC';
                if ( aElem.id == "grouptab_"+tabNum ) {
                    // currentTabLeft, currentTabRight, currentTab
                    classStarter = 'c';
                }
                var spanTags = groupList[i].getElementsByTagName("span");
                for (var ii = 0 ; ii < spanTags.length; ii++ ) {
                    if ( spanTags[ii].className == null ) { continue; }
                    var oldClass = spanTags[ii].className.match(/urrentTab.*/);
                    spanTags[ii].className = classStarter + oldClass;
                }
            }
        };
    for (var i = 0; i < moduleLinks.length; i++) {
        moduleLinks[i].onmouseover = moduleLinkMouseOver;
    }
};

/**
 * Handles loading the sitemap popup
 */
YAHOO.util.Event.onAvailable('sitemapLinkSpan',function()
{
    document.getElementById('sitemapLinkSpan').onclick = function()
    {
        ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_LOADING_PAGE'));
    
        var smMarkup = '';
        var callback = {
             success:function(r) {     
                 ajaxStatus.hideStatus();
                 document.getElementById('sm_holder').innerHTML = r.responseText;
                 with ( document.getElementById('sitemap').style ) {
                     display = "block";
                     position = "absolute";
                     right = 0;
                     top = 80;
                 }
                 document.getElementById('sitemapClose').onclick = function()
                 {
                     document.getElementById('sitemap').style.display = "none";
                 }
             } 
        } 
        postData = 'module=Home&action=sitemap&GetSiteMap=now&sugar_body_only=true';    
        YAHOO.util.Connect.asyncRequest('POST', 'index.php', callback, postData);
    }
});

/**
 * Hides a part of the left column 
 */
YAHOO.util.Event.onAvailable('lastviewicon',function()
{
 if (document.getElementById('lastviewicon').style.display != 'none') {
 	document.getElementById('lastviewicon').onclick = function(){
 		if (document.getElementById('ul_lastview').style.display == 'none') {
 			document.getElementById('ul_lastview').style.display = 'inline';
 		}
 		else {
 			document.getElementById('ul_lastview').style.display = 'none';
 		}
 	}
 	document.getElementById('newrecordicon').onclick = function(){
 		if (document.getElementById('form_SideQuickCreate_Contacts').style.display == 'none') {
 			document.getElementById('form_SideQuickCreate_Contacts').style.display = 'inline';
 		}
 		else {
 			document.getElementById('form_SideQuickCreate_Contacts').style.display = 'none';
 		}
 	}
 	if (document.getElementById('shortcuts_img') != null) {
		document.getElementById('shortcuts_img').onclick = function(){
			if (document.getElementById('ul_shortcuts').style.display == 'none') {
				document.getElementById('ul_shortcuts').style.display = 'inline';
			}
			else {
				document.getElementById('ul_shortcuts').style.display = 'none';
			}
		}
	}
 }
});

YAHOO.util.Event.onAvailable('lastviewicon_1',function() {
if (document.getElementById('lastviewicon_1').style.display != 'none') {
	document.getElementById('lastviewicon_1').onclick = function(){
		if (document.getElementById('ul_lastview').style.display == 'none') {
			document.getElementById('ul_lastview').style.display = 'inline';
		}
		else {
			document.getElementById('ul_lastview').style.display = 'none';
		}
	}
	document.getElementById('newrecordicon_1').onclick = function(){
		if (document.getElementById('form_SideQuickCreate_Contacts').style.display == 'none') {
			document.getElementById('form_SideQuickCreate_Contacts').style.display = 'inline';
		}
		else {
			document.getElementById('form_SideQuickCreate_Contacts').style.display = 'none';
		}
	}
	if (document.getElementById('shortcuts_img_1') != null) {
		document.getElementById('shortcuts_img_1').onclick = function(){
			if (document.getElementById('ul_shortcuts').style.display == 'none') {
				document.getElementById('ul_shortcuts').style.display = 'inline';
			}
			else {
				document.getElementById('ul_shortcuts').style.display = 'none';
			}
		}
	}
}	
});

/**
 * hides and shows the left column menu, as well as shows mouseover popup when the menu isn't being shown
 */
YAHOO.util.Event.onContentReady('HideHandle',function()
{
    document.getElementById('HideHandle').onclick = function()
    {
        document.getElementById('HideMenu').style.visibility = 'hidden';
        if (document.getElementById("leftColumn").style.display == 'none') {
            document.getElementById("leftColumn").style.display = 'inline';
            document.getElementById("content").className = '';
            Set_Cookie('showLeftCol','true',30,'/','','');
            document['HideHandle'].src = SUGAR.themes.hide_image;
        }
        else {
            document.getElementById("leftColumn").style.display='none';
            document.getElementById("content").className = 'noLeftColumn';
            Set_Cookie('showLeftCol','false',30,'/','','');
            document['HideHandle'].src = SUGAR.themes.show_image;
        }
    }
    
    document.getElementById('HideHandle').onmouseover = function()
    {
        if(document.getElementById("leftColumn").style.display=='none'){
            tbButtonMouseOver('HideHandle',135,'',10);
        }
    }
});

/**
 * shows mouseover popup moduleTabExtraMenu
 */
if (isIE6 = /msie|MSIE 6/.test(navigator.userAgent)) {
    YAHOO.util.Event.onContentReady('moduleTabExtraMenu', function(){
        SUGAR.hideMenuTimer = null;
        document.getElementById('moduleTabExtraMenu').onmouseover = function(){
            if (SUGAR.hideMenuTimer != null) {
                clearTimeout(SUGAR.hideMenuTimer);
            }
            document.getElementById("cssmenu").style.visibility = 'visible';
        }
        document.getElementById('moduleTabExtraMenu').onmouseout = function(){
            if (SUGAR.hideMenuTimer != null) {
                clearTimeout(SUGAR.hideMenuTimer);
            }
            SUGAR.hideMenuTimer = window.setTimeout("document.getElementById('cssmenu').style.visibility = 'hidden'", 100);
        }
    });
}
/**
 * Checks on load if we should show the left column or not based on the cookie values
 */
YAHOO.util.Event.onContentReady('content',function()
{
   	if (!Get_Cookie('showLeftCol')) {
        Set_Cookie('showLeftCol','true',30,'/','','');
    }
    else {
        if ( Get_Cookie('showLeftCol') == 'false' && document.getElementById('HideHandle') != null) {
            document.getElementById('HideHandle').onclick();
        }
    }
});

/**
 * Hides the left column, but does not reset the cookie. Used when we need the extra screen space
 */
SUGAR.themes.tempHideLeftCol = function()
{
    document.getElementById('HideMenu').style.visibility = 'hidden';
    document.getElementById("leftColumn").style.display='none';
    document.getElementById("content").className = 'noLeftColumn';
    document['HideHandle'].src = SUGAR.themes.show_image;
}


SUGAR.themes.changeColor = function(colorName){
    return SUGAR.themes.changeStyle('color', colorName);
}

SUGAR.themes.changeFont = function(fontName){
    return SUGAR.themes.changeStyle('font', fontName);
}

SUGAR.themes.changeStyle = function(styleType, newStyle){
    if(styleType == 'color'){
        document.getElementById('current_color_style').href = "themes/"+ SUGAR.themes.theme_name +"/css/colors."+newStyle+".css";
    }
    if(styleType == 'font'){
        document.getElementById('current_font_style').href = "themes/"+ SUGAR.themes.theme_name +"/css/fonts."+newStyle+".css";
    }
}
/**
 * handles the changing theme picker
 */
/*YAHOO.util.Event.onAvailable('usertheme',function()
{
    document.getElementById('usertheme').onchange = function()
    {
        var themeToUse = SUGAR.themes.allThemes[this.options[this.selectedIndex].value];
        
        document.getElementById('themeName').innerHTML = themeToUse.name;
        document.getElementById('themeDescription').innerHTML = themeToUse.description;
        document.getElementById('themePreviewImage').src = themeToUse.previewImage;
        document.getElementById('themeColors').innerHTML = themeToUse.colorSelect;
        document.getElementById('themeFonts').innerHTML = themeToUse.fontSelect;
    }
})*/

;

/**
 * Handles loading the theme picker popup
 */
YAHOO.util.Event.onDOMReady(function()
{
    SUGAR.themes.themepickerDialog = new YAHOO.widget.Dialog("themepickerDialog", { 
        width: "250px",
        //constraintoviewport: true,
        visible: false, 
        draggable: false,
        modal: true
        });
                                                                             
    SUGAR.themes.themepickerDialog.callback = 
    {
        success: function()
        {
            document.getElementById('themepickerDialogForm').submit();
        }, 
        failure: function()
        {
            SUGAR.themes.themepickerDialog.hide();
        } 
    };
    SUGAR.themes.themepickerDialog.cfg.queueProperty("buttons", [
        {text:SUGAR.language.get('app_strings', 'LBL_SUBMIT_BUTTON_LABEL'), handler:SUGAR.themes.themepickerDialog.callback.success, isDefault:true },
        {text:SUGAR.language.get('app_strings', 'LBL_CANCEL_BUTTON_LABEL'), handler:SUGAR.themes.themepickerDialog.callback.failure } 
        ]);
    
    document.getElementById('themepickerDialog').style.display = '';                                            
    SUGAR.themes.themepickerDialog.render();
    
    document.getElementById('themepickerLinkSpan').onclick = function()
    {
        SUGAR.themes.themepickerDialog.show();        	
        SUGAR.themes.themepickerDialog.configFixedCenter(null, false) ;
    }
});
