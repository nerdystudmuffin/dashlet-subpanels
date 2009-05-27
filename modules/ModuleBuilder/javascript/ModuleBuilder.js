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
function treeinit() {}

if(typeof('console') == 'undefined'){
console = {
	log: function(message){

	}}
}
(function() {
	var sw = YAHOO.SUGAR,
		Event = YAHOO.util.Event,
		Connect = YAHOO.util.Connect,
	    Dom = YAHOO.util.Dom;
	
function createTreePanel(treeData, params) {
	var tree = new YAHOO.widget.TreeView(params.id);
	var root = tree.getRoot();
	addChildNodes(root, treeData);
	
	return tree;
}

function addChildNodes(parentNode, parentData) {
	var nodes = parentData.nodes || parentData.children;
	for (i in nodes) {
		if (typeof(nodes[i]) == 'object') {
			nodes[i].data.href = '#';
			var node = new YAHOO.widget.TextNode(nodes[i].data, parentNode)
			node.action = nodes[i].data.action;
			if (typeof(nodes[i].nodes) == 'object') {
				addChildNodes(node, nodes[i]);
			}
		}
	}
}

if (typeof(ModuleBuilder) == 'undefined') {
	ModuleBuilder = {
	    init: function(){
			
			//Setup the basic ajax request settings
			Connect.extraParams = {
				to_pdf: true
			};
			Connect.url = 'index.php?to_pdf=1&sugar_body_only=1';
			Connect.method = 'POST';
			Connect.timeout = 300000; 
			
			//Setup and read cookie settings
			//Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
			
			SUGAR.themes.tempHideLeftCol();
			
			//Setup the main layout
			var tp = ModuleBuilder.tabPanel = new YAHOO.widget.TabView("mbtabs");
			tp.addTab(new YAHOO.widget.Tab({ 
				label: SUGAR.language.get('ModuleBuilder', 'LBL_SECTION_MAIN'),
				scroll : true,
				content : "<div> </div>",
				id : "center",
				active : true
			}));

			var viewHeight = document.documentElement ? document.documentElement.clientHeight : self.innerHeight;
			var mp = ModuleBuilder.mainPanel = new YAHOO.widget.Layout('mblayout', {
				border: false,
				height: viewHeight - (document.getElementById('header').clientHeight ) - 40,
				//autoHeight: true
				//frame: true,
				units: [//ModuleBuilder.tree, ModuleBuilder.tabPanel,
				{
					position: 'center',
					body : 'mbcenter',
					scroll : true
				},{
					position: "left",
					header: "Tree",
					collapse: true,
					width: 230,
					minWidth: 100,
					resize: true,
					scroll : true,
					body : "<div id='mbTree'/>"
				},{
					id: 'help',
					header: SUGAR.language.get('ModuleBuilder', 'LBL_SECTION_HELP'),
					position:'right',
					body: 'mbhelp',
					scroll: true,
					width: 250,
					minWidth: 200,
					resize: true,
					collapse: true
				},{
					header: SUGAR.util.getAndRemove("footerHTML").innerHTML,
					position: 'bottom',
					id: 'mbfooter',
					height: 30,
					border: false
				}]
			});
			mp.render();
			
			var centerEl = mp.getUnitByPosition('center').get('wrap');
			tp.appendTo(centerEl);
			
			//YUI does not take the resizers into account when calculating panel size.
			var correctW = function(){
				var w = (this.body.clientWidth - 5) + "px";
				this.body.style.width = w;
				this.header.style.width = w;
			};
			mp.getUnitByPosition('right').on("resize", correctW); 
			mp.getUnitByPosition('left').on("resize", correctW);
			mp.resize(true);
			Event.on(window, 'resize', ModuleBuilder.autoSetLayout, this, true);
			
			var tree = ModuleBuilder.tree = createTreePanel(TREE_DATA, {
				id: 'mbTree'
			});
			tree.setCollapseAnim("TVSlideOut");
			tree.setExpandAnim("TVSlideIn");
			//tree.subscribe("labelClick", ModuleBuilder.handleTreeClick);
			tree.subscribe("clickEvent", ModuleBuilder.handleTreeClick);
			//We have to recalc the size of expanded nodes once the expanded nodes have rendered.
			tree.subscribe("animComplete", function(node, type) {ModuleBuilder.tree.render();});
			tree.render();
			//Setup Browser History
			
			//var mbContent = Ext.ux.History.getBookmarkedState('mbContent');

			var mbContent = false;
			if (ModuleBuilder.mode == 'mb') {
				mp.getUnitByPosition('left').header.firstChild.innerHTML = SUGAR.language.get('ModuleBuilder', 'LBL_SECTION_PACKAGES');
				//Ext.getCmp('west').setTitle();
				mbContent = mbContent ? mbContent : 'module=ModuleBuilder&action=package&package=';
			}
			else if (ModuleBuilder.mode == 'studio') {
				ModuleBuilder.MBpackage = ''; // set to empty so other views can recognize that dealing with an deployed, rather than undeployed, module
				mp.getUnitByPosition('left').header.firstChild.innerHTML = SUGAR.language.get('ModuleBuilder', 'LBL_SECTION_MODULES');
				mbContent = mbContent ? mbContent :'module=ModuleBuilder&action=wizard';
			}
			else if (ModuleBuilder.mode == 'sugarportal') {
				mp.getUnitByPosition('left').header.firstChild.innerHTML =SUGAR.language.get('ModuleBuilder', 'LBL_SECTION_PORTAL');
				mbContent = mbContent ? mbContent : 'module=ModuleBuilder&action=wizard&portal=1';
			}
			else if (ModuleBuilder.mode == 'dropdowns') {
				mp.getUnitByPosition('left').header.firstChild.innerHTML = SUGAR.language.get('ModuleBuilder', 'LBL_SECTION_DROPDOWNS');
				mbContent = mbContent ? mbContent : 'module=ModuleBuilder&action=dropdowns';
			}
			else {
				mp.getUnitByPosition('left').collapse(false);
				mbContent = mbContent ? mbContent : 'module=ModuleBuilder&action=home';
			}

			/*Ext.ux.History.register('mbContent', mbContent, ModuleBuilder.navigate);
			Ext.ux.History.initialize();*/
			ModuleBuilder.getContent(mbContent);

			/*if (Ext.get('left')) {Ext.get('left').setStyle({display:'none'})}
			if (Ext.get('leftCol')) {Ext.get('leftCol').setStyle({display:'none'})}
			Ext.getCmp('mbfooter').body.setStyle({display:'none'});*/
			
			if (SUGAR.themes.tempHideLeftCol) SUGAR.themes.tempHideLeftCol();
			ModuleBuilder.autoSetLayout();
			ModuleBuilder.tabPanel.on('tabchange', function(panel, tab) {
				if (tab.isVisible())
					ModuleBuilder.helpLoad( tab.getId() ) ;
			});
			
			if (Dom.get("HideHandle")) Dom.get("HideHandle").style.display = "none";

		},
		//Empty layout manager
		layoutValidation: {
			popup_window: null,
			popup: function(){
				ModuleBuilder.layoutValidation.popup_window = new YAHOO.widget.SimpleDialog("emptyLayout", {
					width: "400px",
					draggable: true,
					constraintoviewport: true,
					modal: true,
					fixedcenter: true,
					text: SUGAR.language.get('ModuleBuilder', 'ERROR_MINIMUM_FIELDS'),
					bodyStyle: "padding:5px",
					buttons: [{
						text: SUGAR.language.get('ModuleBuilder', 'LBL_BTN_CLOSE'),
						isDefault:true,
						handler: function(){
							ModuleBuilder.layoutValidation.popup_window.hide()
						}
					},]
				});
				ModuleBuilder.layoutValidation.popup_window.render(document.body);
				ModuleBuilder.layoutValidation.popup_window.show();
			}
		},
		//Layout history manager
		history: {
			popup_window: false,
			reverted: false,
			params: { },
			browse: function(module, layout, subpanel){
				subpanel = subpanel ? subpanel : "";
				if (!module && ModuleBuilder.module != "undefined") {
					module = ModuleBuilder.module;
				}
				if (!ModuleBuilder.history.popup_window) {
					ModuleBuilder.history.popup_window = new YAHOO.SUGAR.AsyncPanel('histWindow', {
						width: 300,
						draggable: true,
						dragOnly: true,
						close: true,
						constraintoviewport: true
					});
				}
				ModuleBuilder.history.popup_window.setHeader( module + ' : ' + SUGAR.language.get('ModuleBuilder', 'LBL_' + layout.toUpperCase()) + " history");
				ModuleBuilder.history.popup_window.setBody("test");
				ModuleBuilder.history.popup_window.render(document.body);
				ModuleBuilder.history.params = {
					module: 'ModuleBuilder',
					histAction: 'browse',
					action: 'history',
					view_package: ModuleBuilder.MBpackage,
					view_module: module,
					view: layout,
					subpanel: subpanel
				};
				ModuleBuilder.history.popup_window.load(ModuleBuilder.paramsToUrl(ModuleBuilder.history.params));
				ModuleBuilder.history.popup_window.show();
			},
			preview: function(module, layout, id, subpanel) {
				var prevPanel =  ModuleBuilder.findTabById('preview:' + id);
				if (!prevPanel) {
					ModuleBuilder.history.params = {
						module: 'ModuleBuilder',
						histAction: 'preview',
						action: 'history',
						view_package: ModuleBuilder.MBpackage,
						view_module: module,
						view: layout,
						sid: id,
						subpanel: subpanel
					};
					prevPanel = new YAHOO.SUGAR.ClosableTab({
						dataSrc: Connect.url + "&" + ModuleBuilder.paramsToUrl(ModuleBuilder.history.params),
						label: SUGAR.language.get("ModuleBuilder", "LBL_MB_PREVIEW"),
						id: 'preview:' + id,
						scroll: true,
						cacheData: true,
						active :true
					}, ModuleBuilder.tabPanel);
					prevPanel.closable = true;
					ModuleBuilder.tabPanel.addTab(prevPanel);
				} else {
					ModuleBuilder.tabPanel.set("activeTab", prevPanel);
				}
				
			},
			revert: function(module, layout, id, subpanel){
				var prevTab = ModuleBuilder.tabPanel.getTabIndex("preview:" + id);
				if(prevTab) ModuleBuilder.tabPanel.removeTab(prevTab);
				
				ModuleBuilder.history.params = {
					module: 'ModuleBuilder',
					histAction: 'restore',
					action: 'history',
					view_package: ModuleBuilder.MBpackage,
					view_module: module,
					view: layout,
					sid: id,
					subpanel: subpanel
				}
				ModuleBuilder.asyncRequest(ModuleBuilder.history.params, function(){
					ModuleBuilder.history.reverted = true;
					ModuleBuilder.getContent(ModuleBuilder.contentURL);
					ModuleBuilder.state.isDirty = true;
				});
			},
			cleanup: function() {
				if (ModuleBuilder.history.reverted && ModuleBuilder.history.params.histAction) {
					ModuleBuilder.history.params.histAction = 'unrestore';
					ModuleBuilder.asyncRequest({params: ModuleBuilder.history.params});
				}
				ModuleBuilder.history.params = { };
				ModuleBuilder.history.reverted = false;
			},
			update: function() {
				if (ModuleBuilder.history.popup_window && ModuleBuilder.history.popup_window.isVisible()) {
					var historyButton = YAHOO.util.Dom.get('historyBtn');
					if (historyButton) {
						historyButton.onclick();
					} else {
						ModuleBuilder.history.popup_window.hide();
					}
				}
			}
		},
		state: {
			isDirty: false,
			saving: false,
			intended_view: {
				url: null,
				successCall: null
			},
			current_view: {
				url: null,
				successCall: null
			},
			save_url_for_current_view: null,
			popup_window: null,
			setupState: function(){
				//ModuleBuilder.state.popup();
				document.body.setAttribute("onclose", "ModuleBuilder.state.popup(); ModuleBuilder.state.popup_window.show()");
				return;
			},
			onSaveClick: function(){
				//set dirty = false
				//call the save method of the current view.
				//call the intended action.
				ModuleBuilder.state.isDirty = false;
				var saveBtn = document.getElementById("saveBtn");
				if (!saveBtn) {
					var mbForm = document.forms[1];
					if (mbForm)
						var mbButtons = mbForm.getElementsByTagName("input");
					if (mbButtons) {
						for (var button = 0; button < mbButtons.length; button++) {
							var name = mbButtons[button].getAttribute("name");
							if (name && (name.toUpperCase() == "SAVEBTN" || name.toUpperCase() == "LSAVEBTN")) {
								saveBtn = mbButtons[button];
								break;
							}
						}
					}
					else {
						alert("Could not find the save action for this view.");
					}
				}
				if (saveBtn) {
					//After the save call completes, load the next page
					ModuleBuilder.state.saving = true;
					eval(saveBtn.getAttributeNode('onclick').value);
				}
				ModuleBuilder.state.popup_window.hide();
			},
			onDontSaveClick: function(){
				//set dirty to false
				//call the intended action.
				ModuleBuilder.state.isDirty = false;
				ModuleBuilder.history.cleanup();
				ModuleBuilder.getContent(ModuleBuilder.state.intended_view.url, ModuleBuilder.state.intended_view.successCall);
				ModuleBuilder.state.popup_window.hide();
			},
			loadOnSaveComplete: function() {
				ModuleBuilder.state.saving = false;
				ModuleBuilder.getContent(ModuleBuilder.state.intended_view.url, ModuleBuilder.state.intended_view.successCall);
			},
			popup: function(){
				ModuleBuilder.state.popup_window = new YAHOO.widget.SimpleDialog("confirmUnsaved", {
					width: "400px",
					draggable: true,
					constraintoviewport: true,
					modal: true,
					fixedcenter: true,
					text: SUGAR.language.get('ModuleBuilder', 'LBL_CONFIRM_DONT_SAVE'),
					bodyStyle: "padding:5px",
					buttons: [{
						text: SUGAR.language.get('ModuleBuilder', 'LBL_BTN_DONT_SAVE'),
						handler: ModuleBuilder.state.onDontSaveClick
					}, {
						text: SUGAR.language.get('ModuleBuilder', 'LBL_BTN_CANCEL'),
						isDefault:true,
						handler: function(){
							ModuleBuilder.state.popup_window.hide()
						}
					},{
						text: SUGAR.language.get('ModuleBuilder', 'LBL_BTN_SAVE_CHANGES'),
						handler: ModuleBuilder.state.onSaveClick
					}]
				});
				ModuleBuilder.state.popup_window.setHeader(SUGAR.language.get('ModuleBuilder', 'LBL_CONFIRM_DONT_SAVE_TITLE'));
				ModuleBuilder.state.popup_window.render(document.body);
			}
		},
		//AJAX Navigation Functions
		navigate : function(url) {
			//Check if we are just registering the url
			if (url != ModuleBuilder.contentURL) {
				ModuleBuilder.getContent(url);
			}
		},
		getContent: function(url, successCall){
			if (url.substring(0, 11) == "javascript:")
			{
				eval(url.substring(11));
				return;
			}
			//save a pointer to intended action
			ModuleBuilder.state.intended_view.url = url;
			ModuleBuilder.state.intended_view.successCall = successCall;
			if(ModuleBuilder.state.isDirty){ //prompt to save current data.
				//check if we are editing a property of the current view (such views open up in new tabs)
				//if so we leave the state dirty and return
				temp_url = url.toLowerCase();
				if(null == temp_url.match(/&action=editproperty/)){
					ModuleBuilder.state.popup();
					ModuleBuilder.state.popup_window.show();
					return;
				}
				//use current_view
				//open a yui panel popup.
				//set the save method to call the this.state.save_action_for_current_view.
				//collect user response.
				//set state.isDirty = false.
				//call this.getContent for intended_action.

			}else{
				ModuleBuilder.state.current_view.url = url;
				ModuleBuilder.state.current_view.successCall = successCall;
			}

			/*if (Connect.isLoading()) {
				//ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_TIME_DEPENDENT'));
				return;
			}*/
			ModuleBuilder.contentURL =  url;
			if (typeof(successCall) != 'function') {
				successCall = ModuleBuilder.updateContent;
			}
			ModuleBuilder.asyncRequest(url, successCall);
		},
		updateContent: function(o){
			//Check if a save action was called and now we need to move-on
			if (ModuleBuilder.state.saving) {
				ModuleBuilder.state.loadOnSaveComplete();
				return;
			}
			ajaxStatus.flashStatus(SUGAR.language.get('app_strings', 'LBL_REQUEST_PROCESSED'), 2000);
			var ajaxResponse = YAHOO.lang.JSON.parse((o.responseText));
			//Randomplaying around code
			if (ajaxResponse.tpl){
				var t = new YAHOO.SUGAR.Template(ajaxResponse.tpl);
				ModuleBuilder.ajaxData = ajaxResponse.data;
				ModuleBuilder.tabPanel.getTab(0).set(t.exec(ajaxResponse.data));
				SUGAR.util.evalScript(t.exec(ajaxResponse.data));
				//Ext.ux.History.navigate('mbContent', ModuleBuilder.contentURL);
				return true;
			}
			for (var maj in ajaxResponse) {
				var name = 'mb' + maj;
				var comp = ModuleBuilder.mainPanel.getUnitById(maj);
				if (!comp) {
					var tabs = ModuleBuilder.tabPanel.get("tabs");
					for (i in tabs) {
						if (tabs[i].get && tabs[i].get("id") == maj)
						comp = tabs[i];
					}
				}
				
				if (name == 'mbwest') { //refresh package_tree!
					var root = ModuleBuilder.tree.root;
					while (root.hasChildren())
						ModuleBuilder.tree.removeNode(root.children[0], false);
					addChildNodes(root, ajaxResponse.west.content.tree_data);
					ModuleBuilder.tree.render();
				}
				else {
					if (!comp) {
						if(ajaxResponse[maj].action == 'deactivate') continue;
						comp = new YAHOO.SUGAR.ClosableTab({
							content: "<div class='bodywrapper'>" + ((maj == 'center') ? "<div>" + ajaxResponse[maj].crumb + "</div>" :"")
								 + ajaxResponse[maj].content + "</div>",
							label: ajaxResponse[maj].title,
							id: maj,
							scroll: true,
							closable: true,
							active :true
						}, ModuleBuilder.tabPanel);
						comp.closable = true;
						//if we don't unregister, IE will complain.
						comp.on('beforedestroy', function(panel){
							if(tinyMCE && tinyMCE.activeEditor) {
								tinyMCE.activeEditor.remove()
							}
						});
						ModuleBuilder.tabPanel.addTab(comp);
						//ModuleBuilder.tabPanel.showTab(ModuleBuilder.tabPanel.getTabIndex(comp));
						if (SUGAR.isIE)
							SUGAR.util.evalScript(ajaxResponse[maj].content);
					} else {
						//Store Center pane changes in browser history
						if (name == 'mbcenter') {
							ModuleBuilder.closeAllTabs();
							comp = ModuleBuilder.tabPanel.getTab(0);
						}
						
						ModuleBuilder.tabPanel.set("activeTab", comp);
						/*if (!comp.body) {
							ModuleBuilder.tabPanel.showTab(ModuleBuilder.tabPanel.getTabIndex(comp));
						}*/
						//var el = comp.get('contentEl');
						comp.set('content', "<div class='bodywrapper'><div>" + ajaxResponse[maj].crumb + "</div>" + ajaxResponse[maj].content + "</div>");
						//el.innerHTML = "<div class='bodywrapper'><div>" + ajaxResponse[maj].crumb + "</div>" + ajaxResponse[maj].content + "</div>";
						SUGAR.util.evalScript(ajaxResponse[maj].content);	
					}
				}
				ModuleBuilder.history.update();
			}
		},
		submitForm: function(formname, successCall){
/*			if (Connect.isLoading()) {
				ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_TIME_DEPENDENT'));
				return;
			}*/
			ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_LOADING'));
			if (typeof(successCall) == 'undefined') {
				successCall = ModuleBuilder.updateContent;
			}
			else {
				ModuleBuilder.callLock = true;
			}
			Connect.setForm(document.getElementById(formname) || document.forms[formname]);
			Connect.asyncRequest(
			    Connect.method, 
			    Connect.url, 
			    {success: successCall, failure: ModuleBuilder.failed}
			);
		},
		setMode: function(reqMode){
			ModuleBuilder.mode = reqMode;
		},
		main: function(type){
			document.location.href = 'index.php?module=ModuleBuilder&action=index&type=' + type;
		},
		failed: function(o){
			ajaxStatus.flashStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_FAILED_DATA'), 2000);
		},
		//Wizard Functions
		buttonDown: function(button, name, list){
			if (typeof(name) != 'undefined') {
				for (i in ModuleBuilder.buttons[list]) {
					ModuleBuilder.buttons[list][i].className = 'wizardButton';
				}
				ModuleBuilder.buttonSelect(name, list);
			}
			button.className = 'wizardButtonDown';

		},
		buttonOver: function(button){
			button.className = 'button';
		},
		buttonOut: function(button, name, list){
			if (typeof(name) != 'undefined') {
				if (ModuleBuilder.buttonGetSelected(list) != name) {
					button.className = 'wizardButton'
				}
			}
			else {
				button.className = 'wizardButton'
			}

		},
		buttonAdd: function(id, name, list){
			if (typeof(ModuleBuilder.buttons[list]) == 'undefined') {
				ModuleBuilder.buttons[list] = {};

			}
			ModuleBuilder.buttons[list][name] = document.getElementById(id);

		},
		buttonGetSelected: function(list){
			if (typeof(ModuleBuilder.selected[list]) == 'undefined') {
				return false;
			}
			return ModuleBuilder.selected[list];
		},
		buttonSelect: function(name, list){
			ModuleBuilder.selected[list] = name;
		},
		buttonToForm: function(form, field, list){
			var theField = eval('document.' + form + '.' + field);
			theField.value = ModuleBuilder.buttonGetSelected(list);
		},

		getTitle: function(title, breadCrumb){
			return "<h2>" + title + "</h2><br>" + breadCrumb;
		},
		closeAllTabs: function() {
			var tabs = ModuleBuilder.tabPanel.get('tabs');
			for (var i = tabs.length - 1; i > -1; i--) {
				var tab = tabs[i];
				if (tab.closable) {
					ModuleBuilder.tabPanel.removeTab(tab);
				}
			}
		},
		//Help Functions
		helpRegister: function(name){
			var formname = 'document.' + name;
			var form = eval(formname);
			var i = 0;
			for (i = 0; i < form.elements.length; i++) {
				if (typeof(form.elements[i].type) != 'undefined' && typeof(form.elements[i].name) != 'undefined' && form.elements[i].type != 'hidden') {
					form.elements[i].onmouseover = function(){
						ModuleBuilder.helpToggle(this.name)
					};
					form.elements[i].onmouseout = function(){
						ModuleBuilder.helpToggle('default')
					};

				}
			}
		},
		helpUnregisterByID: function (id){
			var elm = document.getElementById(id);
			elm.onmouseover = function() {};
			elm.onmouseout = function() {};
			return;
		},
		helpRegisterByID: function(name, tag){
			var parent = document.getElementById(name);
			var children = parent.getElementsByTagName(tag);
			for (var i = 0; i < children.length; i++) {
				if (children[i].id != 'undefined') {
					children[i].onmouseover = function(){
						ModuleBuilder.helpToggle(this.id)
					};
					//children[i].onmouseover = function(){alert(this.id)};
					children[i].onmouseout = function(){
						ModuleBuilder.helpToggle('default')
					};
				}
			}
		},
		helpSetup: function(group, def, panel){
			if (!ModuleBuilder.panelHelp) ModuleBuilder.panelHelp = [];
			
			// setup the linkage between this tab/panel and the relevant help
			var id = ModuleBuilder.tabPanel.get("activeTab").get("id")  ;
			ModuleBuilder.panelHelp [ id ] = { lang: group , def: def } ;
			 
			// get the help text if required
			if ( ! ModuleBuilder.AllHelpLang ) ModuleBuilder.AllHelpLang = SUGAR.language.get('ModuleBuilder', 'help');

			if (group && def) {
				ModuleBuilder.helpLang = ModuleBuilder.AllHelpLang[group];
				ModuleBuilder.helpDefault = def;
			} 
			
			ModuleBuilder.helpToggle('default');
		},
		helpLoad: function(panelId){
			if ( ! ModuleBuilder.AllHelpLang ) ModuleBuilder.AllHelpLang = SUGAR.language.get('ModuleBuilder', 'help');
			
			if ( ModuleBuilder.panelHelp [ panelId ] )
			{
				ModuleBuilder.helpLang = ModuleBuilder.AllHelpLang[ ModuleBuilder.panelHelp [ panelId ].lang ];
				ModuleBuilder.helpDefault = ModuleBuilder.panelHelp [ panelId ].def ;
				ModuleBuilder.helpToggle('default');
			}
		},
		helpToggle: function(name){
			if (name == 'default')
				name = ModuleBuilder.helpDefault;
			if (ModuleBuilder.helpLang != null && typeof(ModuleBuilder.helpLang[name]) != 'undefined') {
				document.getElementById('mbhelp').innerHTML = ModuleBuilder.helpLang[name];
			}
		},
		handleSave: function(form, callBack){
			if (check_form(form)) {
				ModuleBuilder.state.isDirty=false;
				ModuleBuilder.submitForm(form, callBack);
			}
		},
		//Tree Functions
		handleTreeClick: function(o) {
			var node = o.node;
			ModuleBuilder.getContent(node.data.action);
			return false;
		},
		treeSubscribe:function(tree){
			tree.subscribe("labelClick", ModuleBuilder.treeLabelClick);
		},
		treeRefresh:function(type){
			ModuleBuilder.getContent('module=ModuleBuilder&action=ViewTree&tree=' + type);
		},
		//MB Specific
		addModule: function(MBpackage){
			ModuleBuilder.getContent('module=ModuleBuilder&action=module&view_package=' + MBpackage);
		},
		viewModule: function(MBpackage, module){
			ModuleBuilder.getContent('module=ModuleBuilder&action=module&view_package=' + MBpackage + '&view_module=' + module);
		},
		packageDelete: function(MBpackage){
			ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_DELETING'));
			if (confirm(SUGAR.language.get('ModuleBuilder', 'LBL_JS_REMOVE_PACKAGE'))) {
				ModuleBuilder.getContent('module=ModuleBuilder&action=DeletePackage&package=' + MBpackage);
				var node = ModuleBuilder.tree.getNodeByProperty('id', 'package_tree/' + MBpackage);
				if (node) ModuleBuilder.tree.removeNode(node, true);
			}
		},
		packagePublish: function(form){
			if (check_form(form)) {
				ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_BUILDPROGRESS'));
				ModuleBuilder.submitForm(form, ModuleBuilder.packageBuild);
			}
		},
		packageBuild: function(o){
			//make sure no user changes were made
			document.CreatePackage.action.value = 'BuildPackage';
			document.CreatePackage.submit();
			ModuleBuilder.callLock = false;
		},
		packageDeploy: function(form){
			if (check_form(form)) {
				ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_DEPLOYPROGRESS'));
				ModuleBuilder.submitForm(form, ModuleBuilder.packageInstall);
			}
		},
		packageInstall: function(o){
			//make sure no user changes were made
			document.CreatePackage.action.value = 'displaydeploy';
			ModuleBuilder.callLock = false;
			ModuleBuilder.submitForm('CreatePackage');
		},
		beginDeploy: function(p){
			ModuleBuilder.asyncRequest('module=ModuleBuilder&action=DeployPackage&package=' + p, ModuleBuilder.deployComplete);
		},
		deployComplete: function(data){
			ModuleBuilder.getContent('module=ModuleBuilder&action=package&package=');
			ModuleBuilder.asyncRequest(
			    'module=Administration&action=RebuildRelationship&silent=true',
				function(){}
			);
			ModuleBuilder.asyncRequest(
				'module=Administration&action=RebuildDashlets&silent=true',
				function(){}			
			);
		},
		packageExport: function(form){
			if (check_form(form)) {
				ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_BUILDPROGRESS'));
				ModuleBuilder.submitForm(form, ModuleBuilder.packageExportProject);
			}
		},
		packageExportProject: function(o){
			//make sure no user changes were made
			document.CreatePackage.action.value = 'ExportPackage';
			document.CreatePackage.submit();
			ModuleBuilder.callLock = false;
		},
		moduleDelete: function(MBpackage, module){
			ajaxStatus.showStatus(SUGAR.language.get('ModuleBuilder', 'LBL_AJAX_DELETING'));
			if (confirm(SUGAR.language.get('ModuleBuilder', 'LBL_JS_REMOVE_PACKAGE'))) {
				ModuleBuilder.getContent('module=ModuleBuilder&action=DeleteModule&package=' + MBpackage + '&view_module=' + module);
				var node = ModuleBuilder.tree.getNodeByProperty('id', 'package_tree/' + MBpackage + '/' + module);
				if (node) ModuleBuilder.tree.removeNode(node, true);
			}
		},
		moduleViewFields: function(o){

			ModuleBuilder.callLock = false;

			ModuleBuilder.getContent('module=ModuleBuilder&action=modulefields&view_package=' + ModuleBuilder.MBpackage + 
				'&view_module=' + ModuleBuilder.module);
		},
		moduleLoadField: function(name, type){
			if (typeof(type) == 'undefined')
				type = 0;
			if (typeof(formsWithFieldLogic) != 'undefined')
				formsWithFieldLogic = 'undefined';
			ModuleBuilder.getContent('module=ModuleBuilder&action=modulefield&view_package=' + ModuleBuilder.MBpackage + 
				'&view_module=' + ModuleBuilder.module + '&field=' + name + '&type=' + type);
		},
		moduleLoadLabels: function(type){
			if (typeof(type) == 'undefined')
				type = 0;
			else
				if (type == "studio") {
					ModuleBuilder.getContent('module=ModuleBuilder&action=editLabels&view_module=' + ModuleBuilder.module);
				}
				else
					if (type == "mb") {
						ModuleBuilder.getContent('module=ModuleBuilder&action=modulelabels&view_package=' + ModuleBuilder.MBpackage + '&view_module=' + ModuleBuilder.module + '&type=' + type);
					}
		},
		moduleViewRelationships: function(o){
			ModuleBuilder.callLock = false;
			ModuleBuilder.getContent('module=ModuleBuilder&action=relationships&view_package=' + ModuleBuilder.MBpackage + '&view_module=' + ModuleBuilder.module);
		},
		moduleLoadRelationship2: function(name, resetLabel) {
			if (resetLabel && Dom.get('rhs_label')) {
				Dom.get('rhs_label').value = "";
			}
			var panel = ModuleBuilder.findTabById('relEditor');
			if (!panel) {
				panel = new YAHOO.SUGAR.ClosableTab({
					label: SUGAR.language.get("ModuleBuilder", "LBL_RELATIONSHIP_EDIT"),
					id: 'relEditor',
					scroll: true,
					cacheData: true,
					active :true
				}, ModuleBuilder.tabPanel);
				ModuleBuilder.tabPanel.addTab(panel);
			}
			var rtField = Dom.get('relationship_type_field');
			var relType = rtField ? rtField.options[rtField.selectedIndex].value: "";
			if (name == "") {
				name = Dom.get('rel_name_id') ? Dom.get('rel_name_id').value : "";
			}
			var params = {
				module: 'ModuleBuilder',
				action: 'relationship',
				view_package: ModuleBuilder.MBpackage,
				view_module: ModuleBuilder.module,
				relationship_name: name,
				relationship_type: relType,
				lhs_module: Dom.get('lhs_mod_field') ? Dom.get('lhs_mod_field').value : document.forms.relform ? document.forms.relform.lhs_module.value : "",
				rhs_module: Dom.get('rhs_mod_field') ? Dom.get('rhs_mod_field').value : "",
				lhs_label:  Dom.get('lhs_label')     ? Dom.get('lhs_label').value     : "",
				rhs_label:  Dom.get('rhs_label')     ? Dom.get('rhs_label').value     : "",
				json: false,
				id:'relEditor'
			};
			ModuleBuilder.asyncRequest(params, function(o) {
				ajaxStatus.hideStatus();
				var tab = ModuleBuilder.findTabById('relEditor');
				tab.set("content", o.responseText);
				SUGAR.util.evalScript(tab);
			});
		},
		moduleDropDown: function(name, field){
			ModuleBuilder.getContent('module=ModuleBuilder&action=dropdown&view_package=' + ModuleBuilder.MBpackage + '&view_module=' + ModuleBuilder.module + '&dropdown_name=' + name + '&field=' + field);
		},
		moduleViewLayouts: function(o){
			ModuleBuilder.callLock = false;
			ModuleBuilder.getContent('module=ModuleBuilder&MB=1&action=wizard&view_package=' + ModuleBuilder.MBpackage + '&view_module=' + ModuleBuilder.module);
		},
		findTabById : function(id) {
			var tabs = ModuleBuilder.tabPanel.get("tabs");
			for (var i = 0; i < tabs.length; i++) {
				if (tabs[i].get("id") == id)
					return tabs[i];
			}
			return null;
		}, 
		autoSetLayout: function(){
			var mp = ModuleBuilder.mainPanel;
			var c = Dom.get("mblayout");
			mp.set("height", Dom.getViewportHeight() - Dom.getY(c) - 30);
			mp.set("width", Dom.getViewportWidth() - 40);
			mp.resize(true);
			//Resize editor layouts
			if (document.getElementById('toolbox')) Studio2.resizeDivs();
			if (document.getElementById('edittabs')) resizeDDLists();
		},
		paramsToUrl : function (params) {
			url = "";
			for (i in params) {
				url += i + "=" + params[i] + "&";
			}
			return url;
		},
		asyncRequest : function(params, callback) {
			var url;
			if (typeof params == "object") {
				url = ModuleBuilder.paramsToUrl(params);
			} else {
				url = params;
			}
			ajaxStatus.showStatus(SUGAR.language.get('app_strings', 'LBL_LOADING_PAGE'));
			Connect.asyncRequest(
			    Connect.method, 
			    Connect.url + '&' + url, 
			    {success: callback, failure: ModuleBuilder.failed}
			);
		},
		refreshGlobalDropDown: function(o){
			// just clear the callLock; the convention is that this is done in a handler rather than in updateContent
			ModuleBuilder.callLock = false;
			ModuleBuilder.updateContent(o);
		},
		refreshDropDown: function(){
			ModuleBuilder.callLock = false;
			document.popup_form.action.value = 'RefreshField';
			document.popup_form.new_dropdown.value = ModuleBuilder.refreshDD_name;
			SimpleList.refreshDD_name = '';
			ModuleBuilder.submitForm("popup_form");
		},
		dropdownChanged: function(value){
			var select = document.getElementById('default[]').options;
			while(select.length > 0) {
				select[0] = null;
			}
			ModuleBuilder.asyncRequest(
				'module=ModuleBuilder&action=get_app_list_string&key=' + value +
				'&view_package=' + ModuleBuilder.MBpackage + '&view_module=' + ModuleBuilder.module,
				ModuleBuilder.dropdownChangedCallback
			);
		},
		dropdownChangedCallback : function(o) {
			var ajaxResponse = eval(o.responseText);
			var select = document.getElementById('default[]').options;
			var count = 0;
			for (var key in ajaxResponse) {
				select[count] = new Option(ajaxResponse[key], key);
				count++;
			}
		}
	};
	ModuleBuilder.buttons = {};
	ModuleBuilder.selected = {};
	ModuleBuilder.callLock = false;
}
})();
