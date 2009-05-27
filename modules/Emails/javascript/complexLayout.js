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
/**
  Complex layout init
 */
function complexLayoutInit() {
	var se = SUGAR.email2;
	se.e2Layout = {
    	getInnerLayout : function(rows) {
        	se.listViewLayout = new YAHOO.widget.Layout('listViewDiv', {
            	parent: se.complexLayout,  
	    		border:true,
	            hideOnLayout: true,
	            height: 400,
				units: [{
					position: "center",
				    scroll:false, // grid should autoScroll itself
				    split:true,
				    body: "<div id='emailGrid'></div><div id='dt-pag-nav'></div> "
				},{
					position: "bottom",
				    scroll:true,
				    collapse: false,
				    resize: true,
				    useShim:true,
				    height:'250',
				    body: "<div id='listBottom' />"
				},{
				    position: "right",
				    scroll:true,
				    collapse: false,
				    resize: true,
				    useShim:true,
				    width:'250',
				    body: "<div id='listRight' />",
				    titlebar: false //,header: "right"
				}]
            });
        	se.complexLayout.on("render", function(){
        		var height = SUGAR.email2.innerLayout.get("element").clientHeight - 30;
				SUGAR.email2.innerLayout.get("activeTab").get("contentEl").parentNode.style.height = height + "px";
				SUGAR.email2.listViewLayout.set("height", height);
				SUGAR.email2.listViewLayout.render();
        	});
            se.listViewLayout.render();
            //CSS hack for now
            se.listViewLayout.get("element").parentNode.parentNode.style.padding = "0px"
            var rp = se.listViewLayout.resizePreview = function() {
            	var pre = YAHOO.util.Dom.get("displayEmailFramePreview");
            	if (pre) {
            		var parent = YAHOO.util.Dom.getAncestorByClassName(pre, "yui-layout-bd");
            		pre.style.height = (parent.clientHeight - pre.offsetTop) + "px";
            	}
            };
            se.listViewLayout.getUnitByPosition("bottom").on("heightChange", se.autoSetLayout);
            se.listViewLayout.getUnitByPosition("right").on("endResize", se.autoSetLayout);
            se.e2Layout.setPreviewPanel(rows);
            se.previewLayout = se.listViewLayout;
            return se.listViewLayout;
        },
        
        getInnerLayout2Rows : function() {
            return this.getInnerLayout(true);
        },
        getInnerLayout2Columns : function() {
            return this.getInnerLayout(false);
        },
        
        init : function(){
            // initialize state manager, we will use cookies
//                Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
        	var viewHeight = document.documentElement ? document.documentElement.clientHeight : self.innerHeight;
        	se.complexLayout = new YAHOO.widget.Layout("container", {
        		border:true,
                hideOnLayout: true,
                height: viewHeight - (document.getElementById('header').clientHeight ) - 65,
                units: [{
                	position: "center",
                    scroll:false,
                    body: "<div id='emailtabs'></div>"
                },
                {
                	position: "left",
                	scroll: true,
                	body: "<div id='lefttabs'></div>",
                    collapse: true,
                    width: 210,
                    minWidth: 100,
                    resize:true,
                    useShim:true,
                    titlebar: true,
                    header: "&nbsp;"
                },
                {
                    header: YAHOO.util.Dom.get('footerLinks').innerHTML,
					position: 'bottom',
					id: 'mbfooter',
					height: 30,
					border: false
                }]
            });
        	se.complexLayout.render();
        	var tp = se.innerLayout = new YAHOO.widget.TabView("emailtabs");
			tp.addTab(new YAHOO.widget.Tab({ 
				label: "Inbox",
				scroll : true,
				content : "<div id='listViewDiv'/>",
				id : "center",
				active : true
			}));
        	var centerEl = se.complexLayout.getUnitByPosition('center').get('wrap');
			tp.appendTo(centerEl);
			//CSS hack for now
			tp.get("element").style.borderRight = "1px solid #666"
			
			var listV =  se.userPrefs.emailSettings.layoutStyle == '2rows' ? this.getInnerLayout2Rows() : this.getInnerLayout2Columns();
			listV.set("height", tp.get("element").clientHeight - 25);
			listV.render();
                
            se.leftTabs = new YAHOO.widget.TabView("lefttabs");
            var folderTab = new YAHOO.widget.Tab({ 
				label: app_strings.LBL_EMAIL_FOLDERS_SHORT,
				scroll : true,
				content : "<div id='emailtree'/>",
				id : "tree",
				active : true
			});
            folderTab.on("activeChange", function(o){ 
            	if (o.newValue) {
            		se.complexLayout.getUnitByPosition("left").set("header", app_strings.LBL_EMAIL_FOLDERS);
            	}
            });
            se.leftTabs.addTab(folderTab);
            
            var tabContent = SUGAR.util.getAndRemove("searchTab");
            var searchTab = new YAHOO.widget.Tab({ 
				label: app_strings.LBL_EMAIL_SEARCH_SHORT,
				scroll : true,
				content : tabContent.innerHTML,
				id : tabContent.id
			});
            searchTab.on("activeChange", function(o){ 
            	if (o.newValue) {
            		se.search.updateSearchTab(); 
            		se.complexLayout.getUnitByPosition("left").set("header", app_strings.LBL_EMAIL_SEARCH);
            	}
            });
            se.leftTabs.addTab(searchTab);
            
        	tabContent = SUGAR.util.getAndRemove("contactsTab");
        	var contactTab = new YAHOO.widget.Tab({ 
				label: app_strings.LBL_EMAIL_ADDRESS_BOOK_TITLE_ICON_SHORT,
				scroll : true,
				content : tabContent.innerHTML,
				id : tabContent.id
			});
        	contactTab.on("activeChange", function(o){
        		if (o.newValue){
        			se.addressBook.getUserContacts();
        			SUGAR.email2.contextMenus.initContactsMenu();
        			se.complexLayout.getUnitByPosition("left").set("header", app_strings.LBL_EMAIL_ADDRESS_BOOK_TITLE_ICON);
        		}
        	});
        	se.leftTabs.addTab(contactTab);
            
            var resizeTabBody = function() {
            	var height = SUGAR.email2.leftTabs.get("element").clientHeight - 30;
				SUGAR.email2.leftTabs.get("activeTab").get("contentEl").parentNode.style.height = height + "px";
            }
            resizeTabBody();
            se.complexLayout.on("render", resizeTabBody);
            se.leftTabs.on("activeTabChange", resizeTabBody);
          
        },
        setPreviewPanel: function(rows) {
        	if (rows) {
            	SUGAR.email2.listViewLayout.getUnitByPosition("right").set("width", 0);
            	SUGAR.email2.listViewLayout.getUnitByPosition("bottom").set("height", 250);
            	YAHOO.util.Dom.get("listRight").innerHTML = "";
            	YAHOO.util.Dom.get("listBottom").innerHTML = "<div id='_blank' />";
            } else {
            	SUGAR.email2.listViewLayout.getUnitByPosition("bottom").set("height", 0);
            	SUGAR.email2.listViewLayout.getUnitByPosition("right").set("width", 250);
            	YAHOO.util.Dom.get("listBottom").innerHTML = "";
            	YAHOO.util.Dom.get("listRight").innerHTML = "<div id='_blank' />";
            }
        }
    };
	se.e2Layout.init();
    
    SUGAR.email2.getComposeLayout = function() {
        var idx = se.composeLayout.currentInstanceId;
        se.composeLayout[idx] = new YAHOO.widget.Layout('htmleditordiv' + idx, {
        	parent: se.complexLayout,
        	border:true,
            hideOnLayout: true,
            height: 400,
			units: [{
				position: "center",
                animate: false,
                scroll: false,
                split:true,
                body: 	SE.composeLayout.composeTemplate.exec({
	                        'app_strings':app_strings,
	                        'mod_strings':mod_strings,
	                        'theme': theme,
	                        'linkbeans_options' : linkBeans,
	                        'idx' : SE.composeLayout.currentInstanceId
                    	})
            },{
            	position: "right",
			    scroll:true,
			    collapse: true,
			    collapsed: true,
			    resize: true,
			    border:true,
			    width:'200',
			    body: "<div id='composeRightTabs" + idx + "'/>",
			    titlebar: true,
			    split: true,
			    header: app_strings.LBL_EMAIL_OPTIONS
            }]
        });
        se.composeLayout[idx].render();
        
        var cTabs = new YAHOO.widget.TabView("composeRightTabs" + idx);
        cTabs.addTab(new YAHOO.widget.Tab({ 
			label: app_strings.LBL_EMAIL_ATTACHMENT,
			scroll : true,
			content : SUGAR.util.getAndRemove("divAttachments" + idx).innerHTML,
			id : "divAttachments" + idx,
			active : true
		}));
        cTabs.addTab(new YAHOO.widget.Tab({ 
			label: app_strings.LBL_EMAIL_OPTIONS,
			scroll : true,
			content : SUGAR.util.getAndRemove("divOptions" + idx).innerHTML,
			id : "divOptions" + idx,
			active : false
		}));
        se.composeLayout[idx].autoSize = function() {
        	var pEl = this.get("element").parentNode.parentNode.parentNode;
        	this.set("height", pEl.clientHeight-30);
        	this.render();
        }
        
        se.composeLayout[idx].rightTabs = cTabs;
        
        return se.composeLayout[idx];
     //   se.composeLayout[idx].add('east', new Ext.ContentPanel('divAttachments' + idx, {title: app_strings.LBL_EMAIL_ATTACHMENT, closable: false}));
      //  se.composeLayout[idx].add('east', new Ext.ContentPanel('divOptions' + idx, {title: app_strings.LBL_EMAIL_OPTIONS, closable: false}));
       // se.composeLayout[idx].add('center', new Ext.ContentPanel('composeOverFrame' + idx));
        //Convert selects to combo boxes for IE compatibility with panels

        
//        se.composeLayout[idx].regions.east.expand();
//        se.composeLayout[idx].regions.east.collapse();
    };


    /*SUGAR.email2.complexLayout.toggleArea = function(id){
        var area = SUGAR.email2.complexLayout.getRegion(id);
        if(area.isVisible()){
            area.hide();
        } else {
            area.show();
        }
    }

    SUGAR.email2.innerLayout.toggleArea = function(id){
        var area = SUGAR.email2.innerLayout.getRegion(id);
        if(area.isVisible()){
            area.hide();
        } else {
            area.show();
        }
    }
    
    SUGAR.email2.autoSetLayout();*/
}

var myBufferedListenerObject = new Object();
myBufferedListenerObject.refit = function() {
    if(SUGAR.email2.grid) {
        SUGAR.email2.grid.autoSize();
    }
}
