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
SUGAR.email2 = {
    cache : new Object(),
    o : null, // holder for reference to AjaxObject's return object (used in composeDraft())
    reGUID : new RegExp(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/i),
    templates : {},
    tinyInstances : {
        currentHtmleditor : ''
    },

    /**
     * preserves hits from email server
     */ 
    _setDetailCache : function(ret) {
        if(ret.meta) {
            var compKey = ret.meta.mbox + ret.meta.uid;

            if(!SUGAR.email2.cache[compKey]) {
                SUGAR.email2.cache[compKey] = ret;
            }
        }
    },

    autoSetLayout : function() {
    	var c = document.getElementById('container');
        var tHeight = YAHOO.util.Dom.getViewportHeight() - YAHOO.util.Dom.getY(c) - 35;
        c.style.height = tHeight + "px";
        SUGAR.email2.complexLayout.set('height', tHeight);
        SUGAR.email2.complexLayout.set('width', YAHOO.util.Dom.getViewportWidth() - 40);
        SUGAR.email2.complexLayout.render();
        SUGAR.email2.listViewLayout.resizePreview();
        //Resize preview frame
        
    }
};

(function() {
	var sw = YAHOO.SUGAR,
		Event = YAHOO.util.Event,
		Connect = YAHOO.util.Connect,
	    Dom = YAHOO.util.Dom
	    SE = SUGAR.email2;

///////////////////////////////////////////////////////////////////////////////
////    EMAIL ACCOUNTS
SE.accounts = {
    outboundDialog : null,
    errorStyle : 'input-error',
    normalStyle : '',

    /**
     * makes async call to retrieve an outbound instance for editting
     */
     //EXT111
    editOutbound : function() {
        var sel = document.getElementById("outbound_email");
        var obi = sel.options[sel.selectedIndex].value;
        var obt = sel.options[sel.selectedIndex].text;

        if(obt.match(/^(add|line|sendmail|system - sendmail)+/)) {
            alert('Invalid Operation');
        } else {
            AjaxObject.startRequest(AjaxObject.accounts.callbackEditOutbound, urlStandard + "&emailUIAction=editOutbound&outbound_email=" + obi);
        }
    },
    deleteOutbound : function() {
        var sel = document.getElementById("outbound_email");
        var obi = sel.options[sel.selectedIndex].value;

        if(obi.match(/^(add|line|sendmail)+/)) {
            alert('Invalid Operation');
        } else {
        	overlay(app_strings.LBL_EMAIL_DELETING_OUTBOUND, app_strings.LBL_EMAIL_ONE_MOMENT);
            AjaxObject.startRequest(AjaxObject.accounts.callbackDeleteOutbound, urlStandard + "&emailUIAction=deleteOutbound&outbound_email=" + obi);
        }
    },
    //EXT111
    getReplyAddress : function() {
        var primary = '';

        for(var i=0; i<SE.userPrefs.current_user.emailAddresses.length; i++) {
            var addy = SE.userPrefs.current_user.emailAddresses[i];

            if(addy.primary_address == "1") {
                primary = addy.email_address;
            }

            if(addy.reply_to == "1") {
                return addy.email_address;
            }
        }

        return primary;
    },

    /**
     * Handles change to 'add'
     *///EXT111
    handleOutboundSelectChange : function() {
        var select = document.getElementById("outbound_email");
        document.getElementById("outbound_email_edit_button").style.display = 'none';
        document.getElementById("outbound_email_delete_button").style.display = 'none';

        if(select.value == 'add') {
            this.showAddSmtp();
        } else if(select.value != 'sendmail' && select.value != 'none' && select.value != 'line' && 
        	select.options[select.selectedIndex].text.search(/system - /) == -1) {
            document.getElementById("outbound_email_edit_button").style.display = '';
            document.getElementById("outbound_email_delete_button").style.display = '';
        }
    },

    /**
     * Called on "Accounts" tab activation event
     */
    lazyLoad : function() {
        // below called with FQ names, wrapped by event handler
        SE.accounts.rebuildAccountList();
        //SE.accounts.rebuildShowAccountList();
        SE.accounts.rebuildMailerOptions();
        SE.accounts.addNewAccount();
    },

    /**
     * Displays a modal diaglogue to add a SMTP server
     */
    showAddSmtp : function() {
        // lazy load dialogue
        if(!this.outboundDialog) {
        	this.outboundDialog = new YAHOO.widget.Dialog("outboundDialog", {
                modal:true,
				visible:true,
            	fixedcenter:true,
            	constraintoviewport: true,
                width	: 600,
				height	: 350
            });
            this.outboundDialog.setHeader(app_strings.LBL_EMAIL_ACCOUNTS_OUTBOUND);
            this.outboundDialog.render();
            Dom.removeClass("outboundDialog", "yui-hidden");
        } // end lazy load
        
        // clear out form
        var form = document.getElementById('outboundEmailForm');
        for(i=0; i<form.elements.length; i++) {
            if(form.elements[i].name == 'mail_smtpport') {
                form.elements[i].value = 25;
            } else if(form.elements[i].type != 'button') {
                form.elements[i].value = '';
            } else if(form.elements[i].type == 'checkbox') {
                form.elements[i].checked = false;
            }
        }

        this.outboundDialog.show();
    },

    /**
     * Accounts' Advanced Settings view toggle
     */
    toggleAdv : function() {
        var adv = document.getElementById("ie_adv");
        if(adv.style.display == 'none') {
            adv.style.display = "";
        } else {
            adv.style.display = 'none';
        }
    },

    /**
     * Presets default values for Gmail
     */
    fillGmailDefaults : function() {
        document.getElementById("mail_smtpserver").value = 'smtp.gmail.com';
        document.getElementById("mail_smtpport").value = '465';
        document.getElementById("mail_smtpauth_req").checked = true;
        document.getElementById("mail_smtpssl").checked = true;
    },

    /**
     * Sets Port field to selected protocol and SSL settings defaults
     */
    setPortDefault : function() {
        var prot    = document.getElementById('protocol');
        var ssl        = document.getElementById('ssl');
        var port    = document.getElementById('port');
        var stdPorts= new Array("110", "143", "993", "995");
        var stdBool    = new Boolean(false);
        var mailboxdiv = document.getElementById("mailboxdiv");
        var trashFolderdiv = document.getElementById("trashFolderdiv");
        var sentFolderdiv = document.getElementById("sentFolderdiv");
		var monitoredFolder = document.getElementById("subscribeFolderButton");
        if(port.value == '') {
            stdBool.value = true;
        } else {
            for(i=0; i<stdPorts.length; i++) {
                if(stdPorts[i] == port.value) {
                    stdBool.value = true;
                }
            }
        }

        if(stdBool.value == true) {
            if(prot.value == 'imap' && ssl.checked == false) { // IMAP
                port.value = "143";
            } else if(prot.value == 'imap' && ssl.checked == true) { // IMAP-SSL
                port.value = '993';
            } else if(prot.value == 'pop3' && ssl.checked == false) { // POP3
                port.value = '110';
            } else if(prot.value == 'pop3' && ssl.checked == true) { // POP3-SSL
                port.value = '995';
            }
        }
        
        if (prot.value == 'imap') {
        	mailboxdiv.style.display = "";
        	trashFolderdiv.style.display = "";
        	sentFolderdiv.style.display = "";
        	monitoredFolder.style.display = "";
        	if (document.getElementById('mailbox').value == "") {
        		document.getElementById('mailbox').value = "INBOX";
        	}
        } else {
        	mailboxdiv.style.display = "none";
        	trashFolderdiv.style.display = "none";
        	sentFolderdiv.style.display = "none";
			monitoredFolder.style.display = "none";
        	document.getElementById('mailbox').value = "";
        } // else
    },

    /**
     * Draws/removes red boxes around required fields.
     */
    ieAccountError : function(style) {
        document.getElementById('server_url').className = style;
        document.getElementById('email_user').className = style;
        document.getElementById('email_password').className = style;
        document.getElementById('protocol').className = style;
        document.getElementById('port').className = style;
    },
    /**
     * Empties all the fields in the accounts edit view
     */
    addNewAccount:function() {
        document.getElementById('ie_id').value = '';
        document.getElementById('ie_name').value = '';
        document.getElementById('from_name').value = SE.userPrefs.current_user.full_name;
        document.getElementById('from_addr').value = this.getReplyAddress();
        document.getElementById('server_url').value = '';
        document.getElementById('email_user').value = '';
        document.getElementById('email_password').value = '';
        document.getElementById('port').value = '';
        document.getElementById('deleteButton').style.display = 'none';

        document.getElementById('protocol').options[0].selected = true;
        // handle SSL
        document.getElementById('ssl').checked = false;
    },

    /**
     * Populates an account's fields in Settings->Accounts
     */
    fillIeAccount:function(jsonstr) {
        var o = JSON.parse(jsonstr);

        document.getElementById('ie_id').value = o.id;
        document.getElementById('ie_name').value = o.name;
        if (o.stored_options != null) {
        	document.getElementById('from_name').value = o.stored_options.from_name;
        	document.getElementById('from_addr').value = o.stored_options.from_addr;
        	if (o.stored_options.trashFolder != null) {
        		document.getElementById('trashFolder').value = o.stored_options.trashFolder;
        	}
        	if (o.stored_options.sentFolder != null) {
        		document.getElementById('sentFolder').value = o.stored_options.sentFolder;
        	}
        }
        document.getElementById('server_url').value = o.server_url;
        document.getElementById('email_user').value = o.email_user;
        document.getElementById('email_password').value = o.email_password;
        document.getElementById('port').value = o.port;
        document.getElementById('group_id').value = o.group_id;
        document.getElementById('mailbox').value = o.mailbox;

        document.getElementById('deleteButton').style.display = 'inline';

        var i = 0;

        // handle SSL
        if(typeof(o.service[2]) != 'undefined') {
            document.getElementById('ssl').checked = true;
        }

        // handle protocol
        if(document.getElementById('protocol').value != o.protocol) {
            var prot = document.getElementById('protocol');
            for(i=0; i<prot.options.length; i++) {
                if(prot.options[i].value == o.service[3]) {
                    prot.options[i].selected = true;
                    this.setPortDefault();
                }
            }
        }

        // handle SMTP selection
        if(o.stored_options != null && typeof(o.stored_options.outbound_email) != 'undefined') {
            var opts = document.getElementById('outbound_email').options;
            for(i=0; i<opts.length; i++) {
                if(opts[i].value == o.stored_options.outbound_email) {
                    opts[i].selected = true;
                }
            }
        }

        SE.accounts.handleOutboundSelectChange();
    },

    deleteIeAccount : function() {
        if(confirm(app_strings.LBL_EMAIL_IE_DELETE_CONFIRM)) {
            overlay(app_strings.LBL_EMAIL_IE_DELETE, app_strings.LBL_EMAIL_ONE_MOMENT);

            var formObject = document.getElementById('ieAccount');
            YAHOO.util.Connect.setForm(formObject);

            AjaxObject.target = 'frameFlex';
            AjaxObject.startRequest(callbackAccountDelete, urlStandard + '&emailUIAction=deleteIeAccount');
        }
    },

    /**
     * Saves Outbound email settings
     */
    saveOutboundSettings : function() {
        YAHOO.util.Connect.setForm(document.getElementById("outboundEmailForm"));
        AjaxObject.startRequest(callbackOutboundSave, urlStandard + "&emailUIAction=saveOutbound");
    },

    saveIeAccount : function() {
        if(SE.accounts.checkIeCreds(true)) {
            document.getElementById('saveButton').disabled = true;

            overlay(app_strings.LBL_EMAIL_IE_SAVE, app_strings.LBL_EMAIL_ONE_MOMENT);

            var formObject = document.getElementById('ieAccount');
            YAHOO.util.Connect.setForm(formObject);

            AjaxObject._reset();
            AjaxObject.target = 'frameFlex';
            AjaxObject.startRequest(callbackAccount, urlStandard + '&emailUIAction=saveIeAccount');
        }
    },

    testSettings : function() {
        form = document.getElementById('ieAccount');

        if(SE.accounts.checkIeCreds()) {
            ie_test_open_popup_with_submit("InboundEmail", "Popup", "Popup", 400, 300, form.server_url.value, form.protocol.value, form.port.value, form.email_user.value, Rot13.write(form.email_password.value), form.mailbox.value, form.ssl.checked, true);
        }
    },

    getFoldersListForInboundAccountForEmail2 : function() {
        form = document.getElementById('ieAccount');
        if(SE.accounts.checkIeCreds()) {
        	var mailBoxValue = form.mailbox.value;
        	if (form.searchField.value.length > 0) {
        		mailBoxValue = "";
        	} // if
            getFoldersListForInboundAccount("InboundEmail", "ShowInboundFoldersList", "Popup", 400, 300, form.server_url.value, form.protocol.value, form.port.value, form.email_user.value, Rot13.write(form.email_password.value), mailBoxValue, form.ssl.checked, true, form.searchField.value);
        } // if
    	
    },
    
    checkIeCreds : function(valiateTrash) {
        var errors = new Array();
        var out = new String();

        var ie_name = document.getElementById('ie_name').value;
        var fromAddress = document.getElementById('from_addr').value;
        var server_url = document.getElementById('server_url').value;
        var email_user = document.getElementById('email_user').value;
        var email_password = document.getElementById('email_password').value;
        var protocol = document.getElementById('protocol').value;
        var port = document.getElementById('port').value;

        if(trim(ie_name) == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_NAME);
        }
        if(trim(fromAddress) == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_FROM_ADDRESS);
        }
        if(trim(server_url) == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_SERVER);
        }
        if(trim(email_user) == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_USER);
        }
        if(trim(email_password) == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_PASSWORD);
        }
        if(protocol == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_PROTOCOL);
        }
        if (protocol == 'imap') {
        	var mailbox = document.getElementById('mailbox').value;
        	if (trim(mailbox) == "") {
        		errors.push(app_strings.LBL_EMAIL_ERROR_MONITORED_FOLDER);
        	} // if
        	if (valiateTrash != null && valiateTrash) {
	        	var trashFolder = document.getElementById('trashFolder').value;
	        	if (trim(trashFolder) == "") {
	        		errors.push(app_strings.LBL_EMAIL_ERROR_TRASH_FOLDER);
	        	} // if
			} // if
        }
        if(port == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_PORT);
        }

        if(errors.length > 0) {
            out = app_strings.LBL_EMAIL_ERROR_DESC;
            for(i=0; i<errors.length; i++) {
                if(out != "") {
                    out += "\n";
                }
                out += errors[i];
            }

            alert(out);
            return false;
        } else {

            return true;
        }
    },

    getIeAccount : function(ieId) {
        if(ieId == '')
            return;

        overlay(app_strings.LBL_EMAIL_SETTINGS_RETRIEVING_ACCOUNT, app_strings.LBL_EMAIL_ONE_MOMENT);

        var formObject = document.getElementById('ieSelect');
        formObject.emailUIAction.value = 'getIeAccount';

        YAHOO.util.Connect.setForm(formObject);

        AjaxObject.startRequest(callbackIeAccountRetrieve, null);
    },

    /**
     * Iterates through TreeView nodes to apply styles dependent nature of node
     */
    renderTree:function() {
        SE.util.cascadeNodes(SE.tree.getRoot(), SE.accounts.setNodeStyle);
        SE.tree.render();
    },
    
    //Sets the style for any nodes that need it.
    setNodeStyle : function(node) {
       //Set unread
       if (typeof(node.data.unseen) != 'undefined') {
           if (!node.data.origText) {
                  node.data.origText = node.data.text;
           }
           if (node.data.unseen > 0) {
               node.setUpLabel('<b>' + node.data.origText + '(' + node.data.unseen + ')<b>');
           }
           else {
               node.setUpLabel(node.data.origText);
           }
       }
       SE.accounts.setupDDTarget(node);
    },
    
    setupDDTarget : function(node) {
    	if (node.ddTarget) {
    		node.ddTarget.removeFromGroup();
    		delete node.ddTarget;
        }
    	node.ddTarget = new YAHOO.util.DDTarget(node.getElId());
    },

    /**
     * selects or creates the IE element in the multi-select
     */
    focusOrCreateIeEl : function(jsonstr) {
        var o = JSON.parse(jsonstr);

        var ms = document.getElementById('ieAccountList');
        var found = false;

        for(i=0; i<ms.options.length; i++) {
            if(ms.options[i].value == o.id) {
                found = true;
                var newOpt = new Option(o.name, o.id);
                document.ieSelect.ieId.options[i] = newOpt;
                newOpt.selected = true;
            } else {
                ms.options[i].selected = false;
            }
        }


        if(found == false) {
            var newO = new Option(o.name, o.id);
            document.ieSelect.ieId.options[i] = newO;
        }

        // rebuild
        this.rebuildAccountList();
    },

    /**
     * Rebuilds the drop-down selector for available email accounts
     */
    rebuildAccountList:function() {
        var ms = document.getElementById('ieAccountList');

        for(j=0; j<ms.options.length; j++) {
            var newOpt = new Option(ms.options[j].text, ms.options[j].value);
            if(ms.options[j].disabled == true)
                newOpt.disabled = true;
            document.ieSelect.ieId.options[j] = newOpt;
        }
        this.rebuildShowAccountList();
    },

    /**
     * rebuilds the select options for mailer options
     */
    rebuildMailerOptions : function() {
        var select = document.forms['ieAccount'].elements['outbound_email'];

        SE.util.emptySelectOptions(select);

        for(var key in SUGAR.mailers) {
            var display = SUGAR.mailers[key].name;
            var opt = new Option(display, key);
            select.options.add(opt);
        }
    },

    /**
     * rebuilds the multiselect list of "active" or viewed I-E accounts in the Options->Accounts screen
     */
    rebuildShowAccountList:function() {
        var formObject = document.getElementById('ieSelect');
        YAHOO.util.Connect.setForm(formObject);
        SE.accounts.setPortDefault();

        AjaxObject.startRequest(callbackRebuildShowAccountList, urlStandard + '&emailUIAction=rebuildShowAccount');
    },

    /**
     * Async call to rebuild the folder list.  After a folder delete or account delete
     */
    rebuildFolderList : function() {
        overlay(app_strings.LBL_EMAIL_REBUILDING_FOLDERS, app_strings.LBL_EMAIL_ONE_MOMENT);
        AjaxObject.startRequest(callbackFolders, urlStandard + '&emailUIAction=rebuildFolders');
    },
    
    /**
     * Returns the number of remote accounts the user has active.
     */
    getAccountCount : function() {
        var tree = SE.tree;
        var count = 0;
        for(i=0; i<tree._nodes.length; i++) {
            var node = tree._nodes[i];

            if(typeof(node) != 'undefined' && node.data.ieId) {
                count++;
            }
        }
        return count;
    }
};
////    END ACCOUNTS
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
////    ADDRESS BOOK
SE.addressBook = {
    _contactCache : new Array(), // cache of contacts
    _dd : new Array(), // filtered list, same format as _contactCache
    _ddLists : new Array(), // list of Lists
    _dd_mlUsed : new Array(), // contacts in mailing list edit view column1
    _dd_mlAvailable : new Array(), // contacts in mailing list edit view column2
    clickBubble : true, // hack to get around onclick event bubbling






    itemSpacing : 'white-space:nowrap; padding:2px;',
    reGUID : SE.reGUID,

    /**
     * sets up the async call to add a Person to the address book
     * @param String elId
     */
    addContact : function(elId) {
        var form = document.getElementById(elId);
        var get = "&bean_module=" + form.bean_module.value + "&bean_id=" + form.bean_id.value;
        AjaxObject.startRequest(callbackGetUserContacts, urlStandard + "&emailUIAction=addContact" + get);
    },





































































    
    /**
     * takes an array of data (usually from an async call) and builds the SugarContacts list
     */
    buildContactList : function(contactCache) {
    	//Destory any old view
    	//var target = document.getElementById("contacts");
		//target.innerHTML = "";
		
		var contactsData = [];
        for (var i in contactCache) {
            var person = contactCache[i];
            var innerHTML = "<img src='themes/default/images/" + person.module + ".gif' class='img' align='absmiddle'>"
                          + "&nbsp;" + person.name;
            //Create the collapsed node
            contactsData.push([i, 'address-contact', "", innerHTML]);
            //Create the expanded node
            contactsData.push(['ex' + i, 'address-exp-contact', "none", "<i>" + innerHTML + "</i>"]);
            for (var j in person.email) {
                if (person.email[j].email_address) {
                    var emailHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&lt;" + person.email[j].email_address + "&gt;";
                    var emailPrimary = "";
                    if (person.email[j].primary_address == "1") {emailPrimary = " address-primary";}
                    contactsData.push([person.email[j].id, 'address-email' + emailPrimary, "none",  emailHTML]);
                }
            }
        }
        var ds = new YAHOO.util.LocalDataSource(contactsData);
        if (!SE.contactView) {
        	var contactTemplate = SE.addressBook.contactTemplate = new YAHOO.SUGAR.Template(
        		"<div id='{id}' item='true' class='{cls}' nowrap style='cursor:pointer; white-space: nowrap;' unselectable='on'>" + 
                "{innerHTML}</div>"
            );
        	var formatCell = function (el, oRecord, oColumn, value) {
        		var data = oRecord.getData();
        		data = {id: data[0], cls:data[1], disp: data[2], innerHTML: data[3]};
        		el.innerHTML =  contactTemplate.exec(data);
        	}
        	SE.contactView = new YAHOO.SUGAR.SelectionGrid("contacts", 
    			[{
	        		label: "Address Book",
	                width: 174,
	                formatter: formatCell,
	                key: "innerHTML"
    			}], 
    			ds, 
    			{
	            	MSG_EMPTY: "&nbsp;", //SUGAR.language.get("Emails", "LBL_EMPTY_FOLDER"),
	        		width:  "180px",
	        		height: "200px",
	        		formatRow: function(el, row) {
	        			Dom.addClass(el, "rowStyle" + row.getData()[2]);
	        			Dom.addClass(el, row.getData()[1]);
        				return true;
	        		}
    			}
            );
        	//Expand/Collapsed nodes on double-click
        	SE.contactView.on("rowDblclickEvent", function(o) {
        		this.clearTextSelection();
        		var el = o.target;
        		//Expand
        		if (Dom.hasClass(el, "address-contact")) {
        			Dom.addClass(el, "rowStylenone");
        			el = this.getNextTrEl(el);
        			Dom.removeClass(el, "rowStylenone");
        			el = this.getNextTrEl(el);
        			while (Dom.hasClass(el, "address-email")) {
        				Dom.removeClass(el, "rowStylenone");
        				el = this.getNextTrEl(el);
        			}
        		} 
        		//Collapse
        		else if (Dom.hasClass(el, "address-exp-contact")) {
        			Dom.removeClass(this.getPreviousTrEl(el), "rowStylenone");
        			Dom.addClass(el, "rowStylenone");
        			el = this.getNextTrEl(el);
        			while (Dom.hasClass(el, "address-email")) {
        				Dom.addClass(el, "rowStylenone");
        				el = this.getNextTrEl(el);
        			}
        		}
        	}, SE.contactView, true);
        	
        	var DDRow = SE.contactView.DDRow = function(oDataTable, oRecord, elTr) {
        		SE.contactView.DDRow.superclass.constructor.call(this, oDataTable, oRecord, elTr);
        		this.addToGroup("addressBookDD");
        	}
        	
        	YAHOO.extend(DDRow, SUGAR.email2.grid.DDRow, {
        		_resizeProxy: function() {
	        		var dragEl = this.getDragEl(),
		            el = this.getEl();
			        var xy = Dom.getXY(el);
			        
			        Dom.setStyle(dragEl, 'height', this.rowEl.offsetHeight + "px");
			        Dom.setStyle(dragEl, 'width',  this.rowEl.offsetWidth + 'px');
			        Dom.setXY(dragEl, [xy[0] - 100, xy[1] - 20] );
			        Dom.setStyle(dragEl, 'display', "");
        		},
        		
        		startDrag: function(x, y) {
        			SE.contactView.DDRow.superclass.startDrag.call(this, x, y);
        			var rowsToKeep = [];
        			for(var i in this.rows) {
        				var el = this.ddtable.getTrEl(this.rows[i]);
        				//Skip hidden and expanded nodes
        				if (Dom.hasClass(el, "rowStylenone") || Dom.hasClass(el, "address-exp-contact"))
        					continue;
        				rowsToKeep.push(this.rows[i]);
        			}
        			this.rows = rowsToKeep;
        		},
        		
        		onDragOver: function(ev, id) {
        			this.target = id;
	    	    },
	    	    onDragOut: function(e, id) {
	    	    	
	    	    },
	    	    endDrag: function(e) {
	    	    	var DDT = YAHOO.util.DragDropMgr.getDDById(this.target);
	    	    	if (DDT && DDT.notifyDrop)
	    	    		DDT.notifyDrop(this.ddtable, e, this.rows, this.target);
	    	    	
	    	    	Dom.setStyle(this.getEl(), "opacity", "");
	    	    	Dom.setStyle(this.getDragEl(), "display", "none"); 
	    	    	this.rows = null;
	    	    	
	    	    }
        	});
        	SE.contactView.convertDDRows = function() {
        		var rowEl = this.getFirstTrEl();
				while (rowEl != null) {
					new this.DDRow(this, this.getRecord(rowEl), rowEl);
					rowEl = this.getNextTrEl(rowEl);
				}
			}
        	SE.contactView.on("postRenderEvent", function() {this.convertDDRows()}, null, SE.contactView);
        	SE.contactView.render();
        	SUGAR.email2.contextMenus.initContactsMenu();
        }
        //Update the contactView store and refresh the grid
        else {
        	SE.contactView.deleteRows(0, SE.contactView.getRecordSet().getLength());
        	SE.contactView.addRows(ds.liveData);
        }
    },

    cancelEdit : function() {
        if(this.editContactDialog)
            this.editContactDialog.hide();
        if(this.editMailingListDialog)
            this.editMailingListDialog.hide();
    },

    /**
     * Clears filter form
     */
    clear : function() {
        var t = document.getElementById('contactsFilter');
        t.value = '';
        this.filter(t);
    },

    /**
     * handle context-menu Compose-to call
     * @param string type 'contacts' or 'lists'
     */
    composeTo : function(type, waited) {
        var activePanel = SUGAR.email2.innerLayout.get("activeTab").get("id")
        if (activePanel.substring(0, 10) != "composeTab") {
            SE.composeLayout.c0_composeNewEmail();
            setTimeout("SE.addressBook.composeTo('" + type + "', true);");
	        SE.contextMenus.contactsContextMenu.hide();



            return;
        }
        var idx = activePanel.substring(10);
        var rows = [ ];
        var id = '';
        // determine if we have a selection to work with
        if(type == 'contacts') {
            var ids = SE.contactView.getSelectedRows();
            for (var i in ids) {
            	rows[i] = SE.contactView.getRecord(ids[i]);
            }
            removeHiddenNodes(rows, SE.contactView);
        } 









		else { return; }

        if(rows.length > 0) {
            SE.composeLayout.handleDrop(
                (type == 'contacts') ? SE.contactView : SE.emailListsView, 
                null, rows, 'addressTo' + idx );
        } else {
            alert(app_strings.LBL_EMAIL_MENU_MAKE_SELECTION);
        }
    },

    editContact : function() {
        SE.contextMenus.contactsContextMenu.hide();
        var element = SE.contactView.getSelectedNodes()[0];
        var elementId = "";
        if (element.className.indexOf('address-contact') > -1) {
            elementId = element.id;
        } else if (element.className.indexOf('address-exp-contact') > -1) {
            elementId = element.id.substring(2);
        }

      /*  if(elementId != "") {
            // verify that it is a Sugar Contact
            var contact = SE.addressBook._contactCache[elementId];

            if(contact.module == 'Contacts') {
                // lazy load settings
                if(!SE.addressBook.editContactDialog) {
                    SE.addressBook.editContactDialog = new Ext.Window({
                        modal:true,
                        width:600,
                        height:400,
                        floating: true,
                        minWidth:300,
                        minHeight:300,
                        contentEl : 'editContact',
                        autoScroll:true,
                        closeAction : 'hide'
                    });
                } // end lazy load

                AjaxObject.startRequest(AjaxObject.addressBook.callback.editContact, urlStandard + "&emailUIAction=editContact&id=" + elementId);
            } else {
                overlay(app_strings.LBL_EMAIL_ERROR_DESC, app_strings.LBL_EMAIL_ADDRESS_BOOK_ERR_NOT_CONTACT, 'alert');
            }
        } else {



            alert(app_strings.LBL_EMAIL_MENU_MAKE_SELECTION);
        }*/
    },
    

    /**
     * Filters contact entries based on user input
     */
    filter : function(inputEl) {
        var ret = new Object();
        var re = new RegExp(inputEl.value, "gi");

        for(var i in this._contactCache) {
            if(this._contactCache[i].name.match(re)) {
                ret[i] = this._contactCache[i];
            }
        }

        this.buildContactList(ret);
    },

    fullForm : function(id, module) {
        document.location = "index.php?return_module=Emails&return_action=index&module=" + module + "&action=EditView&record=" + id;
    },

    /**
     * returns a formatted email address from the addressBook cache
     */
    getFormattedAddress : function(id) {
        var o = this._contactCache[id];
        var primaryEmail = '';

        for(var i=0; i<o.email.length; i++) {
            var currentEmail = o.email[i].email_address;

            if(o.email[i].primary_address == 1) {
                primaryEmail = o.email[i].email_address;
            }
        }

        var finalEmail = (primaryEmail == "") ? currentEmail : primaryEmail;
        var name = new String(o.name);
        var finalName = name.replace(/(<([^>]+)>)/ig, "");
        var ret = finalName + " <" + finalEmail + ">";

        return ret;
    },


    /**
     * Generates the listView form that unites users, contacts, leads, and prospects
     * @return string HTML
     */
    getPeopleListView : function() {
    	return document.getElementById('contactsDialogueHTML').innerHTML;
    },

    /**
     * Parses through Contact object and returns the primary email address
     * @param object
     * @return string
     */
    getPrimaryEmailFromContact : function(contact) {
        var emails = contact.email;
        
        for(var i=0; i<emails.length; i++) {
            if(emails[i].primary_address == '1') {
                return emails[i].email_address;
            }
        }
        return '';
    },
    
    getPrimaryEmailObject : function(contact) {
       for(j in contact.email) {
            if (contact.email[j].id && contact.email[j].primary_address == "1") {
                return contact.email[j];
            }
        }
        return null;
    },
    
    getContactFromEmail : function(emailId) {
    	var contacts = this._contactCache;
        for(i in contacts) {
            var contact = contacts[i];
            for(j in contact.email) {
                if (contact.email[j].id && contact.email[j].id == emailId) {
                    return contact;
                }
            }
        }
        return null;
    },

    /**
     * Async call to get user's contacts & groups
     */
    getUserContacts : function() {
    	if(SE.addressBook._contactCache.length < 1) {
        	AjaxObject.startRequest(callbackGetUserContacts, urlStandard + "&emailUIAction=getUserContacts");
        }
    },

    removeContact : function() {
        SE.contextMenus.contactsContextMenu.hide();

        if(confirm(app_strings.LBL_EMAIL_CONFIRM_DELETE)) {
            var str = '';
            var selectedItems = SE.contactView.getSelectedNodes();
            removeHiddenNodes(selectedItems);
            for(var i=0; i < selectedItems.length; i++) {
                var node = selectedItems[i];
                if (node.className.indexOf('address-contact') > -1) {
                    if(str != '') {
                        str += "::";
                    }
                    str += node.id;
                } else if (node.className.indexOf('address-exp-contact') > -1) {
                    if(str != '') {
                        str += "::";
                    }
                    str += node.id.substring(2);
                }
            }

            if(str != "") {
                AjaxObject.startRequest(callbackGetUserContacts, urlStandard + '&emailUIAction=removeContact&ids=' + str);
            }
        }
    },































































    /**
     * commits changes to a contact record from Email 2.0
     * @return bool false on failure
     */
    saveContact : function() {
        var form = document.getElementById('editContactForm');
        var errors = new Array();
        var emailElements = new Array();

        if(form.contact_last_name.value == "") {
            errors.push(app_strings.LBL_EMAIL_ERROR_CONTACT_NAME)
        }

        if(errors.length > 0) {
            var out = new String();
            out = app_strings.LBL_EMAIL_ERROR_DESC;
            for(i=0; i<errors.length; i++) {
                if(out != "") {
                    out += "\n";
                }
                out += errors[i];
            }

            alert(out);
            return false;
        } else {
            var send = new Object();
            send.invalid = new Array();
            send.optOut = new Array();
            send.primary = '';

            // get values and save
            for(var i=0; i<form.elements.length; i++) {
                var el = form.elements[i];

                if(el.type == 'text' || el.type == 'hidden') {
                    send[el.name] = el.value;

                    // get id if is address field
                    if(el.name.match(/emailAddress[0-9]/) && el.value != "") {
                        emailElements.push(el.name);
                    }
                }
            }

            // handle multi-value (PHP array) values
            for(var j=0; j<emailElements.length; j++) {
                var indexNumber = emailElements[j].substr(12);
                var optOut = document.getElementById("emailAddressOptOutFlag" + indexNumber);
                var invalid = document.getElementById("emailAddressInvalidFlag" + indexNumber);
                var primary = document.getElementById("emailAddressPrimaryFlag" + indexNumber);

                if(optOut && optOut.checked) {
                    send.optOut.push(optOut.value);
                }

                if(invalid && invalid.checked) {
                    send.invalid.push(invalid.value);
                }

                if(primary && primary.checked) {
                    send.primary = primary.value;
                }
            }

            var args = JSON.stringifyNoSecurity(send);
            AjaxObject.startRequest(callbackGetUserContacts, urlStandard + "&emailUIAction=saveContactEdit&args=" + args);
        }
    },

    /**
     * Sets up async call to query for matching contacts, users, etc.
     */
    searchContacts : function() {
        var fn = document.getElementById('input_searchNameFirst').value;
        var ln = document.getElementById('input_searchNameLast').value;
        var em = document.getElementById('input_searchEmail').value;
        var pe = document.getElementById('input_searchPerson').value;
        this.addressBookDataModel.params['first_name'] = fn;
        this.addressBookDataModel.params['last_name'] = ln;
        this.addressBookDataModel.params['email_address'] = em;
        this.addressBookDataModel.params['person'] = pe;
        this.addressBookDataModel.params['emailUIAction'] = 'getAddressSearchResults';
        //SE.addressBook.grid.toggleSelectAll(false);
        this.grid.getDataSource().sendRequest(SUGAR.util.paramsToUrl(this.addressBookDataModel.params),  this.grid.onDataReturnInitializeTable, this.grid);
        //this.grid.getStore().load({params: {start:0, limit: 25}});
        //SE.addressBook.addressBookDataModel = this.grid.getStore();
    },
    
    getAddressBookPanel : function() {
        grid = SE.addressBook.grid;
        grid.reconfigure(SE.addressBook.addressBookDataModel, grid.getColumnModel());
        document.getElementById('addressBookGridFooterDiv').style.display = "";



    },
    














    /**
     * Opens modal select window to add contacts to addressbook
     */
    selectContactsDialogue : function(destId) {
        if(!this.contactsDialogue) {
        	var dlg = this.contactsDialogue = new YAHOO.widget.Dialog("contactsDialogue", {
            	modal:true,
            	visible:false,
            	draggable: false,
            	constraintoviewport: true,
                width   : 950,
                height  : 225,
                buttons : [{
                	text: app_strings.LBL_EMAIL_ADDRESS_BOOK_ADD, isDefault: true, handler: this.addMultipleContacts
                }, {
                	text: app_strings.LBL_EMAIL_CLOSE, handler: function() {
                		SUGAR.email2.addressBook.contactsDialogue.hide();
                    }
                }]
            });
        	dlg.setHeader(app_strings.LBL_EMAIL_ADDRESS_BOOK_SELECT_TITLE);
        	
        	var body = SUGAR.util.getAndRemove("contactsDialogueHTML");
        	dlg.setBody(body.innerHTML);
        	dlg.renderEvent.subscribe(function() {
            	var iev = YAHOO.util.Dom.get("contactsDialogueBody");
            	if (iev) {
            		this.body.style.height = (iev.clientHeight + 10) + "px";
            		this.body.style.width = "950px";
            	}
            }, dlg);
        	dlg.beforeRenderEvent.subscribe(function() { 
        		var dd = new YAHOO.util.DDProxy(dlg.element); 
        		dd.setHandleElId(dlg.header); 
        		dd.on('endDragEvent', function() { 
        			dlg.show(); 
        		}); 
        	}, dlg, true); 
        	dlg.render();
        	
        	var tp = new YAHOO.widget.TabView("contactsSearchTabs");
			
        	var tabContent = SUGAR.util.getAndRemove("searchForm");
        	tp.addTab(new YAHOO.widget.Tab({
				label: app_strings.LBL_EMAIL_ADDRESS_BOOK_TITLE,
				scroll : true,
				content : tabContent.innerHTML,
				id : "addressSearchTab",
				active : true
			}));
        	tabContent = SUGAR.util.getAndRemove("reportsDialog");
        	tp.addTab(new YAHOO.widget.Tab({
				label: app_strings.LBL_EMAIL_REPORTS_TITLE,
				scroll : true,
				content : tabContent.innerHTML,
				id : "reportsSearchTab"
			}));
        	
        	tp.appendTo(Dom.get("addressBookTabsDiv"));
        	
        	if (!SE.addressBook.grid) {
                AddressSearchGridInit();
            }
        	this.contactsDialogue.render();
        	dlg.center();
        }
    	Event.removeListener(this.contactsDialogue.getButtons()[0], "click"); 
        if (destId) {
        	Event.addListener(this.contactsDialogue.getButtons()[0], "click", this.insertContactToField); 
        	this.contactsDialogue.target = destId;
        } else {
        	Event.addListener(this.contactsDialogue.getButtons()[0], "click", this.addMultipleContacts);
        }
        this.contactsDialogue.show();
    },
    
    addMultipleContacts : function() {
        var rows = SE.addressBook.grid.getSelectedRows();
        var contacts = [];
        for (var i = 0; i < rows.length; i++) {
            var data = SE.addressBook.grid.getRecord(rows[i]).getData();
            contacts.push({id : data.bean_id, module : data.bean_module});
        }
        var contactData = JSON.stringifyNoSecurity(contacts);
        SE.addressBook.grid.toggleSelectAll(false);
        AjaxObject.startRequest(callbackGetUserContacts, urlStandard + "&emailUIAction=addContactsMultiple&contactData=" + contactData);
    },
    	
    insertContactToField : function() {
        var contactsDialogue = SE.addressBook.contactsDialogue;
        var target = document.getElementById(contactsDialogue.target);
        var contacts = SE.addressBook.grid.getSelectedRows();
        for (var i=0; i < contacts.length; i++) {
            var data = SE.addressBook.grid.getRecord(contacts[i]).getData();
            target.value = SE.addressBook.smartAddEmailAddressToComposeField(target.value, data.name + ' <' + data.email + '>');
        }
        SE.addressBook.grid.toggleSelectAll(false);
    },

































































































































































    /**
     * adds an email address to a string, but first checks if it exists
     * @param string concat The string we are appending email addresses to
     * @param string addr Email address to add
     * @return string
     */
    smartAddEmailAddressToComposeField : function(concat, addr) {
        var re = new RegExp(addr);

        if(!concat.match(re)) {
            if(concat != "") {
                concat += "; " + addr;
            } else {
                concat = addr;
            }
        }

        return concat;
    }
};
////    END ADDRESS BOOK
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
////    AUTOCOMPLETE
/**
 * Auto-complete object
 */
SE.autoComplete = {
    config : {
        delimChar : [";", ","],
        useShadow :    false,
        useIFrame : false,
        typeAhead : true,
        prehighlightClassName : "yui-ac-prehighlight",
        queryDelay : 0
    },
    instances : new Array(),

    /**
     * Parses an addressBook entry looking for primary address.  If not found, it will return the last found address.
     * @param object Contact from AddressBook
     * @return string
     */
    getPrimaryAddress : function(contact) {
        var address = app_strings.LBL_EMAIL_ADDRESS_BOOK_NOT_FOUND;

        for(var eIndex in contact.email) {
            address = contact.email[eIndex].email_address;
            if(contact.email[eIndex].primary_address == 1) {
                return contact.email[eIndex].email_address;
            }
        }
        return address;
    },
    

    /**
     * initializes autocomplete widgets for a given compose view
     * @param int idx
     */
    init : function(idx) {
        var ds = new YAHOO.widget.DS_JSArray(this.returnDataSource(SE.addressBook._contactCache), {
            "queryMatchContains" : false,
            "queryMatchSubset" : true
        });

        this.instances[idx] = {
            to : null,
            cc : null,
            bcc : null
        };

   
        // instantiate the autoComplete widgets
        this.instances[idx]['to'] = new YAHOO.widget.AutoComplete('addressTo'+idx, "addressToAC"+idx, ds, this.config);
        this.instances[idx]['cc'] = new YAHOO.widget.AutoComplete('addressCC'+idx, "addressCcAC"+idx, ds, this.config);
        this.instances[idx]['bcc'] = new YAHOO.widget.AutoComplete('addressBCC'+idx, "addressBccAC"+idx, ds, this.config);

        // enable hiding of interfering textareas
        this.instances[idx]['to'].containerExpandEvent.subscribe(SE.autoComplete.toggleTextareaHide);
        this.instances[idx]['cc'].containerExpandEvent.subscribe(SE.autoComplete.toggleTextareaHide);
        this.instances[idx]['bcc'].containerExpandEvent.subscribe(SE.autoComplete.toggleTextareaHide);

        // enable reshowing of hidden textareas
        this.instances[idx]['to'].containerCollapseEvent.subscribe(SE.autoComplete.toggleTextareaShow);
        this.instances[idx]['cc'].containerCollapseEvent.subscribe(SE.autoComplete.toggleTextareaShow);
        this.instances[idx]['bcc'].containerCollapseEvent.subscribe(SE.autoComplete.toggleTextareaShow);

        // enable refreshes of contact lists
        this.instances[idx]['to'].textboxFocusEvent.subscribe(SE.autoComplete.refreshDataSource);
        this.instances[idx]['cc'].textboxFocusEvent.subscribe(SE.autoComplete.refreshDataSource);
        this.instances[idx]['bcc'].textboxFocusEvent.subscribe(SE.autoComplete.refreshDataSource);
    },

    refreshDataSource : function(sType, aArgs) {
        var textBoxId = aArgs[0].getInputEl().id; // "addressTo0"
        var idx;
        var refresh = SE.autoComplete.returnDataSource(SE.addressBook._contactCache);

        if(textBoxId.indexOf("addressTo") > -1 || textBoxId.indexOf("addressCC") > -1) {
            idx = textBoxId.substr(9);
        } else {
            idx = textBoxId.substr(10);
        }

        SE.autoComplete.instances[idx]['to'].dataSource.data = refresh;
        SE.autoComplete.instances[idx]['cc'].dataSource.data = refresh;
        SE.autoComplete.instances[idx]['bcc'].dataSource.data = refresh;
    },

    /**
     * Parses AddressBook entries to return an appropriate DataSource array for YUI.autoComplete
     */
    returnDataSource : function(contacts) {
        var ret = new Array();
        for(var id in contacts) {
            if (contacts[id].name) {
	            var primary = this.getPrimaryAddress(contacts[id]);
	
	            ret[ret.length] = contacts[id].name.replace(/<[\/]*b>/gi, '') + " <" + primary + ">";
	            //ret[ret.length] = contacts[id].name + " <" + primary + ">";
	
	            for(var emailIndex in contacts[id].email) {
	                ret[ret.length] = contacts[id].email[emailIndex].email_address;
	            }
            }
        }

        return ret;
    },

    /**
     * Hides address textareas to prevent autocomplete dropdown from being obscured
     */
    toggleTextareaHide : function(sType, aArgs) {
        var textBoxId = aArgs[0]._oTextbox.id; // "addressTo0"
        var type = "";
        var idx = -1;

        if(textBoxId.indexOf("addressTo") > -1) {
            type = "to";
        } else if(textBoxId.indexOf("addressCC") > -1) {
            type = "cc";
        }
        idx = textBoxId.substr(9);

        // follow through if not BCC
        if(type != "") {
            var cc = document.getElementById("addressCC" + idx);
            var bcc = document.getElementById("addressBCC" + idx);

            switch(type) {
                case "to":
                    cc.style.visibility = 'hidden';
                case "cc":
                    bcc.style.visibility = 'hidden';
                break;
            }
        }
    },

    /**
     * Redisplays the textareas after an address is commited
     */
    toggleTextareaShow : function(sType, aArgs) {
        var textBoxId = aArgs[0]._oTextbox.id; // "addressTo0"
        var type = "";
        var idx = -1;

        if(textBoxId.indexOf("addressTo") > -1) {
            type = "to";
        } else if(textBoxId.indexOf("addressCC") > -1) {
            type = "cc";
        }
        idx = textBoxId.substr(9);

        // follow through if not BCC
        if(type != "") {
            document.getElementById("addressCC" + idx).style.visibility = 'visible';
            document.getElementById("addressBCC" + idx).style.visibility = 'visible';
        }
    }
};

////    END AUTOCOMPLETE
///////////////////////////////////////////////////////////////////////////////































///////////////////////////////////////////////////////////////////////////////
////    COMPOSE & SEND
/**
 * expands the options sidebar
 */
SE.composeLayout = {
    currentInstanceId : 0,
    tinyConfig : "code,bold,italic,underline,strikethrough,separator,justifyleft,justifycenter,justifyright,justifyfull," +
                 "separator,bullist,numlist,outdent,indent,separator,forecolor,backcolor,fontselect,fontsizeselect",

    ///////////////////////////////////////////////////////////////////////////
    ////    COMPOSE FLOW
    /**
     * Prepare bucket DIV and yui-ext tab panels
     */
    _0_yui : function() {
        var idx = this.currentInstanceId;

        var composeTab = new YAHOO.SUGAR.ClosableTab({
        		label: "Compose Email",
				scroll : true,
				content : "<div id='htmleditordiv" + idx + "'/>",
				id : "composeTab" + idx,
				closeMsg: app_strings.LBL_EMAIL_CONFIRM_CLOSE,
				active : true
        }, SE.innerLayout);
        SE.innerLayout.addTab(composeTab);
        
        // get template engine with template
        if (!SE.composeLayout.composeTemplate) {
        	SE.composeLayout.composeTemplate = new YAHOO.SUGAR.Template(SE.templates['compose']);
        }
        
        // create Tab inner layout
        var composePanel =  SE.getComposeLayout();
        composePanel.getUnitByPosition("right").collapse();
        
        //composeTab.on("activeChange", function(o){if (o.newValue) this.autoSize()}, null, composePanel);
        composePanel.autoSize();
       
        
        
        //composePanel.getUnitByPosition("right").collapse();

        // work-around to hide sliding panels in IE
        //SE.composeLayout[idx].regions.east.expand();
        //SE.composeLayout[idx].regions.east.collapse();
    },

    isParentTypeValid : function(idx) {
		var parentTypeValue = document.getElementById('data_parent_type' + idx).value;
		var parentNameValue = document.getElementById('data_parent_name' + idx).value;
		if (trim(parentTypeValue) == ""){
			alert(mod_strings.LBL_ERROR_SELECT_MODULE);
			return false;
		} // if
		return true;
    },
    
    isParentTypeAndNameValid : function(idx) {
		var parentTypeValue = document.getElementById('data_parent_type' + idx).value;
		var parentNameValue = document.getElementById('data_parent_name' + idx).value;
		var parentIdValue = document.getElementById('data_parent_id' + idx).value;
		if ((trim(parentTypeValue) != "" && trim(parentNameValue) == "") || 
			(trim(parentTypeValue) != "" && trim(parentNameValue) != "" && parentIdValue == "")){
				alert(mod_strings.LBL_ERROR_SELECT_MODULE_SELECT);
			return false;
		} // if
		return true;
    },

    callopenpopupForEmail2 : function(idx) {
		var parentTypeValue = document.getElementById('data_parent_type' + idx).value;
		var parentNameValue = document.getElementById('data_parent_name' + idx).value;
		if (!SE.composeLayout.isParentTypeValid(idx)) {
			return;
		} // if
		open_popup(document.getElementById('data_parent_type' + idx).value,600,400,'&tree=ProductsProd',true,false,{"call_back_function":"set_return","form_name":'emailCompose' + idx,"field_to_name_array":{"id":'data_parent_id' + idx,"name":'data_parent_name' + idx}}); 	
	},    
    /**
     * Prepare TinyMCE
     */
    _1_tiny : function() {
        var idx = SE.composeLayout.currentInstanceId;
        var elId = SE.tinyInstances.currentHtmleditor = 'htmleditor' + idx;
        SE.tinyInstances[elId] = { };
        SE.tinyInstances[elId].ready = false;

        var t = tinyMCE.getInstanceById(elId);

        if(typeof(t) == 'undefined')  {
            tinyMCE.execCommand('mceAddControl', false, elId);
            YAHOO.util.Event.onAvailable(elId + "_parent", function() {
            	var cof = document.getElementById('composeOverFrame' + idx);
                var head = document.getElementById('composeHeaderTable' + idx);
                var targetHeight = cof.clientHeight - head.clientHeight;
            	var instance =  tinyMCE.get(SE.tinyInstances.currentHtmleditor);                
            	
            	var tableEl = document.getElementById(instance.editorId + '_parent').firstChild;
                var toolbar = document.getElementById(instance.editorId + '_toolbar');
                var contentEl  = instance.contentAreaContainer;
                var iFrame = contentEl.firstChild;
                iFrame.style.height = (targetHeight - contentEl.offsetHeight) + "px";
                iFrame.style.position = "relative";
                iFrame.style.top = "-5px";
                tableEl.style.height = targetHeight + "px";
                setTimeout("SUGAR.email2.composeLayout.setSignature('" + idx + "')", 1000);
            }, this);
        }
    },

    /**
     * Initializes d&d, auto-complete, email templates
     */
    _2_final : function() {
        var idx = SE.composeLayout.currentInstanceId;

        if(this.emailTemplates) {
            this.setComposeOptions(idx);
        } else {
            //populate email template cache
            AjaxObject.target = '';
            AjaxObject.startRequest(callbackComposeCache, urlStandard + "&emailUIAction=fillComposeCache");
        }

        // handle drop targets for addressBook
       var to =  new YAHOO.util.DDTarget('addressTo' +idx, 'addressBookDD', {notifyDrop:this.handleDrop});
       var cc =  new YAHOO.util.DDTarget('addressCC' +idx, 'addressBookDD', {notifyDrop:this.handleDrop});
       var bcc = new YAHOO.util.DDTarget('addressBCC'+idx, 'addressBookDD', {notifyDrop:this.handleDrop});
       to.notifyDrop = cc.notifyDrop = bcc.notifyDrop = this.handleDrop;

        // auto-complete setup
        SE.autoComplete.init(idx);

        // set focus on to:
        document.getElementById("addressTo" + idx).focus();
    },



    c1_composeEmail : function(isReplyForward, retry) {
        if (!retry) {
            this._0_yui();
        }
        if (typeof(tinyMCE) == 'undefined' || typeof(tinyMCE.settings) == 'undefined'){
            setTimeout("SE.composeLayout.c1_composeEmail(" + isReplyForward + ", true);", 500);
        } else {
	        this._1_tiny();
	        this._2_final();
	
	        if(isReplyForward) {
	            this.replyForwardEmailStage2();
	        }
        }
    },

    /**
     * takes draft info and prepopulates
     */
    c0_composeDraft : function() {
        this.getNewInstanceId();
        inCompose = true;
        document.getElementById('_blank').innerHTML = '';

        var idx = SE.composeLayout.currentInstanceId;
        SE.composeLayout.currentInstanceId = idx;
        SE.tinyInstances.currentHtmleditor = 'htmleditor' + SE.composeLayout.currentInstanceId;
        SE.tinyInstances[SE.tinyInstances.currentHtmleditor] = new Object();
        SE.tinyInstances[SE.tinyInstances.currentHtmleditor].ready = false;

        SE.composeLayout._0_yui();
        SE.composeLayout._1_tiny();
        // hack to hide "hidden" option/attach panel content in both IE and FF
        //SE.composeLayout[idx].regions.east.expand();
        //SE.composeLayout[idx].regions.east.collapse();

        // final touches
        SE.composeLayout._2_final();

        /* Draft-specific final processing. Need a delay to allow Tiny to render before calling setText() */
        setTimeout("AjaxObject.handleReplyForwardForDraft(SE.o);", 1000);
    },

    /**
     * Strip & Prep editor hidden fields
     */
    c0_composeNewEmail : function() {
        this.getNewInstanceId();
        this.c1_composeEmail(false);
    },

    /**
     * Sends async request to get the compose view.
     * Requests come from "reply" or "forwards"
     */
    c0_replyForwardEmail : function(ieId, uid, mbox, type) {
        SE.composeLayout.replyForwardObj = new Object();
        SE.composeLayout.replyForwardObj.ieId = ieId;
        SE.composeLayout.replyForwardObj.uid = uid;
        SE.composeLayout.replyForwardObj.mbox = mbox;
        SE.composeLayout.replyForwardObj.type = type;

        if(mbox == 'sugar::Emails') {
            SE.composeLayout.replyForwardObj.sugarEmail = true;
        }

        SE.composeLayout.getNewInstanceId();
        SE.composeLayout.c1_composeEmail(true);
    },
    ////    END COMPOSE FLOW
    ///////////////////////////////////////////////////////////////////////////

    /**
     * Called when a contact, email, or mailinglist is dropped
     * into one of the compose fields.
     */
    handleDrop : function (source, event, data, target) {
        var nodes;
        if (!target) {
            target = event.getTarget();
            if (data.single) {
                data.nodes = [data.nodes];
            }
            nodes = data.nodes;
        } else {
            target = document.getElementById(target);
            nodes = data;
        }
        


























        if (target.id.indexOf('address') > -1) {
            // dropped onto email to/cc/bcc field
            for(var i in nodes) {
            	var node = nodes[i].getData();
            	var email = "";
                if (node[1].indexOf('contact') > -1) {
                    email = SE.addressBook.getFormattedAddress(node[0]);
                } else if (node[1].indexOf('address-email') > -1){
                    email = node[3].replace(/&nbsp;/gi, '');
                    email = email.replace('&lt;', '<').replace('&gt;', '>');
                    var tr = source.getTrEl(nodes[i]);
                    while (tr && !Dom.hasClass(tr, "address-contact")) {
                    	tr = source.getPreviousTrEl(tr);
                    }
                    var CID = source.getRecord(tr).getData()[0];
                    var o = SE.addressBook._contactCache[CID];
                    var name = new String(o.name);
                    var finalName = name.replace(/(<([^>]+)>)/ig, "");
                    email = finalName + email;
                }
                target.value = SE.addressBook.smartAddEmailAddressToComposeField(target.value, email);              
            }
        }
    },


    /*/////////////////////////////////////////////////////////////////////////////
    ///    EMAIL TEMPLATE CODE
     */
    applyEmailTemplate : function (idx, id) {
        // id is selected index of email template drop-down

        if(id == '' || id == "0") {
            return;
        }
        
        //bug #20680
        var box_title = SUGAR.language.get('Emails', 'LBL_EMAILTEMPLATE_MESSAGE_SHOW_TITLE');
		var box_msg = SUGAR.language.get('Emails', 'LBL_EMAILTEMPLATE_MESSAGE_SHOW_MSG');
	
		YAHOO.SUGAR.MessageBox.show({
           title:box_title,
           msg: box_msg,
           type: 'confirm',
           fn: function(btn){
           		if(btn=='no'){return;};
           		SUGAR.email2.composeLayout.processResult(idx, id);},
           modal:true,
           scope:this
       });
    },
	
	processResult : function(idx , id){
        call_json_method('EmailTemplates','retrieve','record='+id,'email_template_object', this.appendEmailTemplateJSON);

        // get attachments if any
        AjaxObject.target = '';
        AjaxObject.startRequest(callbackLoadAttachments, urlStandard + "&emailUIAction=getTemplateAttachments&parent_id=" + id);
    },

    appendEmailTemplateJSON : function() {
        var idx = SE.composeLayout.currentInstanceId; // post increment

        // query based on template, contact_id0,related_to
        //jchi 09/10/2008 refix #7743
        if(json_objects['email_template_object']['fields']['subject'] != '' ) { // cn: bug 7743, don't stomp populated Subject Line
            document.getElementById('emailSubject' + idx).value = decodeURI(encodeURI(json_objects['email_template_object']['fields']['subject']));
        }

        var text = decodeURI(encodeURI(json_objects['email_template_object']['fields']['body_html'])).replace(/<BR>/ig, '\n').replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"');

        // cn: bug 14361 - text-only templates don't fill compose screen
        if(text == '') {
            text = decodeURI(encodeURI(json_objects['email_template_object']['fields']['body'])).replace(/<BR>/ig, '\n').replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"').replace(/\r\n/gi,"<br/>");
        }

        var tiny = SE.util.getTiny('htmleditor' + idx);
        var tinyHTML = tiny.getContent();
        var openTag = '<div><span><span>';
        var closeTag = '</span></span></div>';
        var htmllow = tinyHTML.toLowerCase();
        var start = htmllow.indexOf(openTag);
		if (start > -1) {
	        var htmlPart2 = tinyHTML.substr(start);
	        tinyHTML = text + htmlPart2;
	        tiny.setContent(tinyHTML);
		} else {
        	tiny.setContent(text);
		}
    },

    /**
     * Writes out the signature in the email editor
     */
    setSignature : function(idx) {
        if (!tinyMCE)
            return false;
        //wait for signatures to load before trying to set them
        if (!SE.composeLayout.signatures) {
            setTimeout("SE.composeLayout.setSignature(" + idx + ");", 1000);
        }
            
        if(idx) {
            var sel = document.getElementById('signatures' + idx);
        } else {
            var sel = document.getElementById('signature_id');
            idx = SE.tinyInstances.currentHtmleditor;
        }

        var signature = '';

        try {
            signature = sel.options[sel.selectedIndex].value;
        } catch(e) {

        }

        var openTag = '<div><span><span>';
        var closeTag = '</span></span></div>';
        var t = SE.util.getTiny('htmleditor' + idx);
        //IE 6 Hack
        t.contentDocument = t.contentWindow.document;
        
        var html = t.getContent();
        var htmllow = html.toLowerCase();
        var start = htmllow.indexOf(openTag);
        var end = htmllow.indexOf(closeTag) + closeTag.length;

        // selected "none" - remove signature from email
        if(signature == '') {
            if (start > -1) {
                var htmlPart1 = html.substr(0, start);
                var htmlPart2 = html.substr(end, html.length);
    
                html = htmlPart1 + htmlPart2;
                t.setContent(html);
            }
            SE.signatures.lastAttemptedLoad = '';
            return false;
        }

        if(!SE.signatures.lastAttemptedLoad) // lazy load place holder
            SE.signatures.lastAttemptedLoad = '';

        SE.signatures.lastAttemptedLoad = signature;

        if(typeof(SE.signatures[signature]) == 'undefined') {
            //lazy load
            SE.signatures.lastAttemptedLoad = ''; // reset this flag for recursion
            SE.signatures.targetInstance = (idx) ? idx : "";
            AjaxObject.target = '';
            AjaxObject.startRequest(callbackLoadSignature, urlStandard + "&emailUIAction=getSignature&id="+signature);
        } else {
            var newSignature = this.prepareSignature(SE.signatures[signature]);

            // clear out old signature
            if(SE.signatures.lastAttemptedLoad && start > -1) {
                var htmlPart1 = html.substr(0, start);
                var htmlPart2 = html.substr(end, html.length);

                html = htmlPart1 + htmlPart2;
            }
            
            // [pre|ap]pend
			start = html.indexOf('<div><hr></div>');
            if(SE.userPrefs.signatures.signature_prepend == 'true' && start > -1) {
				var htmlPart1 = html.substr(0, start);
				var htmlPart2 = html.substr(start, html.length);
                var newHtml = htmlPart1 + openTag + newSignature + closeTag + htmlPart2;
            } else {
                var newHtml = html + openTag + newSignature + closeTag;
            }
            //tinyMCE.setContent(newHtml);
            t.setContent(newHtml);
        }
    },

    prepareSignature : function(str) {
        var signature = new String(str);

        signature = signature.replace(/&lt;/gi, '<');
        signature = signature.replace(/&gt;/gi, '>');

        /*
        // the following is specific to tinyMCE's idiosyncracies
        signature = signature.replace(/&quot;/gi, '"');
        signature = signature.replace(/;/gi, ''); // removes semi-colons?!?!
        */

        return signature;
    },


    showAttachmentPanel : function(idx) {
    	var east = SE.composeLayout[idx].getUnitByPosition("right");
    	var tabs = SE.composeLayout[idx].rightTabs;
    	east.expand();
        tabs.set("activeTab", tabs.getTab(0));
    },

    /**
     * expands sidebar and displays options panel
     */
    showOptionsPanel : function(idx) {
    	var east = SE.composeLayout[idx].getUnitByPosition("right");
    	var tabs = SE.composeLayout[idx].rightTabs;
    	east.expand();
        tabs.set("activeTab", tabs.getTab(1));
    },

    /**
     * Selects the Contacts tab
     */
    showContactsPanel : function() {
        SE.complexLayout.regions.west.showPanel("contactsTab");
    },

    /**
     * Generates fields for Select Document
     */
    addDocumentField : function(idx) {
        var basket = document.getElementById('addedDocuments' + idx);
        if(basket) {
            var index = (basket.childNodes.length / 7) - 1;
            if(index < 0)
                index = 0;
        } else {
            index = 0;
        }

        var test = document.getElementById('documentId' + idx + index);

        while(test != null) {
            index++;
            test = document.getElementById('documentId' + idx + index);
        }
        
        var out = 
            "<div id='documentCup" + idx + index + "'>" +
                // document field
                "<input type='hidden' name='document" + idx + index + "' id='document" + idx + index + "' />" +
                // document id field
                "<input type='hidden' name='documentId" + idx + index + "' id='documentId" + idx + index + "' />" +
                // document name field
                "<input disabled='true' size=20 type='text' name='documentName" + idx + index + "' id='documentName" + idx + index + "' />" +
                // select button
                "<input class='button' type='button' name='documentSelect" + idx + index + "' id='documentSelect" + idx + index + "'" + 
                    "onclick='SE.composeLayout.selectDocument(\"" + index + "\");' value='" + app_strings.LBL_EMAIL_SELECT + "'/>" +
                // remove button
                "<input class='button' type='button' name='documentRemove" + idx + index + "' id='documentRemove" + idx + index + "'" + 
                    "onclick='SE.composeLayout.deleteDocumentField(\"documentCup" + idx + index + "\");' value='" + app_strings.LBL_EMAIL_REMOVE + "'/>" +    
                "<br/>" +
            "</div>";
        
        basket.innerHTML += out;
        return index;
    },

    /**
     * Makes async call to save a draft of the email
     * @param int Instance index
     */
    saveDraft : function(tinyInstance) {
        this.sendEmail(tinyInstance, true);
    },

    selectDocument : function(target) {
        URL="index.php?module=Emails&action=PopupDocuments&to_pdf=true&target=" + target;
        windowName = 'selectDocument';
        windowFeatures = 'width=800' + ',height=600' + ',resizable=1,scrollbars=1';

        win = window.open(URL, windowName, windowFeatures);
        if(window.focus) {
            // put the focus on the popup if the browser supports the focus() method
            win.focus();
        }
    },

    /**
     * Modal popup for file attachment dialogue
     */
    addFileField : function() {
    	if(!SE.addFileDialog){ // lazy initialize the dialog and only create it once
            SE.addFileDialog = new YAHOO.widget.Dialog("addFileDialog", {
            	modal:true,
            	visible:false,
            	fixedcenter:true,
            	constraintoviewport: true,
                width   : 600,
                height  : 225,
                scroll: true,
                keylisteners : new YAHOO.util.KeyListener(document, { keys:27 }, { 
                	fn:function(){SE.addFileDialog.hide();}
                })
            });
            SE.addFileDialog.setHeader(app_strings.LBL_EMAIL_ATTACHMENTS);
            SE.addFileDialog.render();
           // SE.addFileDialog.addKeyListener(27, , SE.addFileDialog);
        }
    	Dom.removeClass("addFileDialog", "yui-hidden");
        
        SE.addFileDialog.show();
    },

    /**
     * Async upload of file to temp dir
     */
    uploadAttachment : function() {
        if(document.getElementById('email_attachment').value != "") {
            var formObject = document.getElementById('uploadAttachment');
            YAHOO.util.Connect.setForm(formObject, true, true);
            AjaxObject.target = '';
            AjaxObject.startRequest(callbackUploadAttachment, null);
        } else {
            alert(app_strings.LBL_EMAIL_ERROR_NO_FILE);
        }
    },

    /**
     * Adds a SugarDocument to an outbound email.  Action occurs in a popup window displaying a ListView from the Documents module
     * @param string target in focus compose layout
     */
    setDocument : function(idx, target, documentId, documentName, docRevId) {
        // fields are named/id'd [fieldName][instanceId][index]
        var addedDocs = document.getElementById("addedDocuments" + idx);
        var docId = document.getElementById('documentId' + idx + target);
        var docName = document.getElementById('documentName' + idx + target);
        var docRevisionId = document.getElementById('document' + idx + target);
        docId.value = documentId;
        docName.value = documentName;
        docRevisionId.value = docRevId;
    },

    /**
     * Removes the bucket div containing the document input fields
     */
    deleteDocumentField : function(documentCup) {
        var f0 = document.getElementById(documentCup);
        f0.parentNode.removeChild(f0);
    },

    /**
     * Removes a Template Attachment field
     * @param int
     * @param int
     */
    deleteTemplateAttachmentField : function(idx, index) {
        // create not-in-array values for removal filtering
        var r = document.getElementById("templateAttachmentsRemove" + idx).value;

        if(r != "") {
            r += "::";
        }

        r += document.getElementById('templateAttachmentId' + idx + index).value;
        document.getElementById("templateAttachmentsRemove" + idx).value = r;

        var target = 'templateAttachmentCup' + idx + index;
        d =  document.getElementById(target);
        d.parentNode.removeChild(d);
    },

    /**
     * Async removal of uploaded temp file
     * @param string index Should be a concatenation of idx and index
     * @param string
     */
    deleteUploadAttachment : function(index, file) {
        var d = document.getElementById('email_attachment_bucket' + index);
        d.parentNode.removeChild(d);

        // make async call to delete cached file
        AjaxObject.target = '';
        AjaxObject.startRequest(null, urlStandard + "&emailUIAction=removeUploadedAttachment&file="+file);
    },

    /**
     * Attaches files coming from Email Templates
     */
    addTemplateAttachmentField : function(idx) {
        // expose title
        document.getElementById('templateAttachmentsTitle' + idx).style.display = 'block';

        var basket = document.getElementById('addedTemplateAttachments' + idx);

        if(basket) {
            var index = basket.childNodes.length;
            if(index < 0)
                index = 0;
        } else {
            index = 0;
        }

        var out = "<div id='templateAttachmentCup" + idx + index + "'>" +
				// remove button	
				"<img src='index.php?entryPoint=getImage&themeName=" + SUGAR.themes.theme_name + "&imageName=minus.gif' " +
					"style='cursor:pointer' align='absmiddle' onclick='SUGAR.email2.composeLayout.deleteTemplateAttachmentField(\"" + 
					idx + "\",\"" + index + "\");'/>" +
				// file icon
				"<img src='index.php?entryPoint=getImage&themeName=" + SUGAR.themes.theme_name + "&imageName=attachment.gif' " + "align='absmiddle' />" +
				// templateAttachment field
				"<input type='hidden' value='" + "' name='templateAttachment" + idx + index + "' id='templateAttachment" + idx + index + "' />" +
				// docId field
				"<input type='hidden' value='" + "' name='templateAttachmentId" + idx + index + "' id='templateAttachmentId" + idx + index + "' />" +
				// file name
				"<span id='templateAttachmentName"  + idx + index + "'" + ">&nbsp;</span>" + 
				"<br id='br" + index + "></br>" + 
				"<br id='brdoc" + index + "></br>" + 
			"</div>";
		basket.innerHTML = basket.innerHTML + out;
        // holder div - to allow quick removal
        /*Ext.DomHelper.append(basket, {
            tag:'div',
            id:'templateAttachmentCup' + idx + index
        });

        var d = document.getElementById('templateAttachmentCup' + idx + index);

        // remove button
        Ext.DomHelper.append(d, {
            tag:'img',
            src: 'themes/Sugar/images/minus.gif',
            cls: 'image',
            style: 'cursor:pointer',
            align: 'absmiddle',
            onclick: "SE.composeLayout.deleteTemplateAttachmentField('" + idx + "', '" +index +"');"
        });
        // file icon
        Ext.DomHelper.append(d, {
            tag:'img',
            src: 'themes/default/images/attachment.gif',
            align: 'absmiddle',
            cls: 'image'
        });
        // templateAttachment field
        Ext.DomHelper.append(d, {
            tag:'input',
            type:'hidden',
            name:'templateAttachment' + idx + index,
            id:'templateAttachment' + idx + index
        });
        // docId field
        Ext.DomHelper.append(d, {
            tag:'input',
            type:'hidden',
            name:'templateAttachmentId' + idx + index,
            id:'templateAttachmentId' + idx + index
        });
        // file name
        Ext.DomHelper.append(d, {
            tag    : 'span',
            id    : 'templateAttachmentName' + idx + index,
            html: "&nbsp;"
        });
        // br
        Ext.DomHelper.append(d, {
            tag:'br',
            id:'br' + index
        });
        // br tag
        Ext.DomHelper.append(d, {tag:'br', id:'brdoc' + index});*/

        return index;
    },

    /**
     * Sends one email via async call
     * @param int idx Editor instance ID
     * @param bool isDraft
     */
    sendEmail : function(idx, isDraft) {
        var form = document.getElementById('emailCompose' + idx);
        var t = SE.util.getTiny(SE.tinyInstances.currentHtmleditor);
        var html = t.getContent();
        var subj = document.getElementById('emailSubject' + idx).value;
        var to = trim(document.getElementById('addressTo' + idx).value);
        var cc = trim(document.getElementById('addressCC' + idx).value);
        var bcc = trim(document.getElementById('addressBCC' + idx).value);
        var email_id = document.getElementById('email_id' + idx).value;
        var composeType = document.getElementById('composeType').value;
        var parent_type = document.getElementById("parent_type").value;
        var parent_id = document.getElementById("parent_id").value;
        if (!isValidEmail(to) || !isValidEmail(cc) || !isValidEmail(bcc)) {
			alert(app_strings.LBL_EMAIL_COMPOSE_INVALID_ADDRESS);
        	return false;
        }

        if (!SE.composeLayout.isParentTypeAndNameValid(idx)) {
        	return;
        } // if
		var parentTypeValue = document.getElementById('data_parent_type' + idx).value;
		var parentIdValue = document.getElementById('data_parent_id' + idx).value;
        parent_id = parentIdValue;
        parent_type = parentTypeValue;

        var in_draft = (document.getElementById('type' + idx).value == 'draft') ? true : false;
        // baseline viability check

        if(to == "" && cc == '' && bcc == '' && !isDraft) {
            alert(app_strings.LBL_EMAIL_COMPOSE_ERR_NO_RECIPIENTS);
            return false;
        } else if(subj == '' && !isDraft) {
            if(!confirm(app_strings.LBL_EMAIL_COMPOSE_NO_SUBJECT)) {
                return false;
            } else {
                subj = app_strings.LBL_EMAIL_COMPOSE_NO_SUBJECT_LITERAL;
            }
        } else if(html == '' && !isDraft) {
            if(!confirm(app_strings.LBL_EMAIL_COMPOSE_NO_BODY)) {
                return false; 
            }
        }

        SE.util.clearHiddenFieldValues('emailCompose' + idx);

        var title = (isDraft) ? app_strings.LBL_EMAIL_SAVE_DRAFT : app_strings.LBL_EMAIL_SENDING_EMAIL;
        overlay(title, app_strings.LBL_EMAIL_ONE_MOMENT);
        html = html.replace(/&lt;/ig, "sugarLessThan");       
        html = html.replace(/&gt;/ig, "sugarGreaterThan");
        
        form.sendDescription.value = html;
        form.sendSubject.value = subj;
        form.sendTo.value = to;
        form.sendCc.value = cc;
        form.sendBcc.value = bcc;
        form.email_id.value = email_id;
        form.composeType.value = composeType;
        form.composeLayoutId.value = 'composeLayout' + idx;
        form.setEditor.value = (document.getElementById('setEditor' + idx).checked == true) ? 1 : 0;
        form.sendCharset.value = document.getElementById('charsetOptions' + idx).value;
        form.saveToSugar.value = (document.getElementById('saveOutbound' + idx).checked == true) ? 1 : 0;
        form.fromAccount.value = document.getElementById('addressFrom' + idx).value;
        form.parent_type.value = parent_type;
        form.parent_id.value = parent_id;




        // email attachments
        var addedFiles = document.getElementById('addedFiles' + idx);
        if(addedFiles) {
            for(i=0; i<addedFiles.childNodes.length; i++) {
                var bucket = addedFiles.childNodes[i];

                for(j=0; j<bucket.childNodes.length; j++) {
                    var node = bucket.childNodes[j];
                    var nName = new String(node.name);

                    if(node.type == 'hidden' && nName.match(/email_attachment/)) {
                        if(form.attachments.value != '') {
                            form.attachments.value += "::";
                        }
                        form.attachments.value += node.value;
                    }
                }
            }
        }

        // sugar documents
        var addedDocs = document.getElementById('addedDocuments' + idx);
        if(addedDocs) {
            for(i=0; i<addedDocs.childNodes.length; i++) {
                var cNode = addedDocs.childNodes[i];
                for(j=0; j<cNode.childNodes.length; j++) {
                    var node = cNode.childNodes[j];
                    var nName = new String(node.name);
                    if(node.type == 'hidden' && nName.match(/documentId/)) {
                        if(form.documents.value != '') {
                            form.documents.value += "::";
                        }
                        form.documents.value += node.value;
                    }
                }
            }
        }

        // template attachments
        var addedTemplateAttachments = document.getElementById('addedTemplateAttachments' + idx);
        if(addedTemplateAttachments) {
            for(i=0; i<addedTemplateAttachments.childNodes.length; i++) {
                var cNode = addedTemplateAttachments.childNodes[i];
                for(j=0; j<cNode.childNodes.length; j++) {
                    var node = cNode.childNodes[j];
                    var nName = new String(node.name);
                    if(node.type == 'hidden' && nName.match(/templateAttachmentId/)) {
                        if(form.templateAttachments.value != "") {
                            form.templateAttachments.value += "::";
                        }
                        form.templateAttachments.value += node.value;
                    }
                }
            }
        }

        // remove attachments
        form.templateAttachmentsRemove.value = document.getElementById("templateAttachmentsRemove" + idx).value;

        YAHOO.util.Connect.setForm(form);

        AjaxObject.target = 'frameFlex';

        // sending a draft email
        if(!isDraft && in_draft) {
            // remove row
            SE.listView.removeRowByUid(email_id);
        }

        var sendCallback = (isDraft) ? AjaxObject.composeLayout.callback.saveDraft : callbackSendEmail;
        var emailUiAction = (isDraft) ? "&emailUIAction=sendEmail&saveDraft=true" : "&emailUIAction=sendEmail";

        AjaxObject.startRequest(sendCallback, urlStandard + emailUiAction);
    },

    /**
     * Handles clicking the email address link from a given view
     */
    composePackage : function() {
        if(composePackage != null) {
            SE.composeLayout.c0_composeNewEmail();

            if(composePackage.to_email_addrs) {
                document.getElementById("addressTo" + SE.composeLayout.currentInstanceId).value = composePackage.to_email_addrs;
            } // if
            if (composePackage.subject != null && composePackage.subject.length > 0) {
            	document.getElementById("emailSubject" + SE.composeLayout.currentInstanceId).value = composePackage.subject;
            }
            
            if(composePackage.parent_type) {
                document.getElementById("parent_type").value = composePackage.parent_type;
                document.getElementById('data_parent_type' + SE.composeLayout.currentInstanceId).value = composePackage.parent_type;
            } // if
            if(composePackage.parent_id) {
                document.getElementById("parent_id").value = composePackage.parent_id;
                document.getElementById('data_parent_id' + SE.composeLayout.currentInstanceId).value = composePackage.parent_id;
            } // if
            if(composePackage.parent_name) {
                document.getElementById('data_parent_name' + SE.composeLayout.currentInstanceId).value = composePackage.parent_name;
            } // if
            if(composePackage.email_id != null && composePackage.email_id.length > 0) {
                document.getElementById("email_id" + SE.composeLayout.currentInstanceId).value = composePackage.email_id;
            } // if
            if (composePackage.body != null && composePackage.body.length > 0) {
		        var tiny = SE.util.getTiny('htmleditor' + SE.composeLayout.currentInstanceId);
        		setTimeout("SE.composeLayout.setContentOnThisTiny();", 3000);
            } // if
            if (composePackage.attachments != null) {
				SE.composeLayout.loadAttachments(composePackage.attachments);            	
            } // if
            
            if (composePackage.fromAccounts != null && composePackage.fromAccounts.status) {
				var addressFrom = document.getElementById('addressFrom' + SE.composeLayout.currentInstanceId);
		        SE.util.emptySelectOptions(addressFrom);
		        var fromAccountOpts = composePackage.fromAccounts.data;
		        for(i=0; i<fromAccountOpts.length; i++) {
		              var key = fromAccountOpts[i].value;
		              var display = fromAccountOpts[i].text;
		              var opt = new Option(display, key);
		              if (fromAccountOpts[i].selected) {
		              	opt.selected = true;
		              }
		              addressFrom.options.add(opt);
		        }			
            	
            } // if
        } // if
    },

    setContentOnThisTiny : function() {
    	var tiny = SE.util.getTiny('htmleditor' + SE.composeLayout.currentInstanceId);
        var tinyHTML = tiny.getContent();
        composePackage.body = decodeURI(encodeURI(composePackage.body));
        // cn: bug 14361 - text-only templates don't fill compose screen
        if(composePackage.body == '') {
            composePackage.body = decodeURI(encodeURI(composePackage.body)).replace(/<BR>/ig, '\n').replace(/<br>/gi, "\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"');
        } // if
        SE.composeLayout.tinyHTML = tinyHTML + composePackage.body;		        
    	tiny.setContent(SE.composeLayout.tinyHTML);
    },
    /**
     * Confirms closure of a compose screen if "x" is clicked
     */
    confirmClose : function(panel) {
        if(confirm(app_strings.LBL_EMAIL_CONFIRM_CLOSE)) {
            SE.composeLayout.closeCompose(panel.id);
            return true;
        } else {
            return false;
        }
    },

    /**
     * forces close of a compose screen
     */
    forceCloseCompose : function(id) {
    	SE.composeLayout.closeCompose(id);
    	
    	// handle flow back to originating view
        if(composePackage) {
            // check if it's a module we need to return to
            if(composePackage.return_module && composePackage.return_action && composePackage.return_id) {
                if(confirm(app_strings.LBL_EMAIL_RETURN_TO_VIEW)) {
                    var url = "index.php?module=" + composePackage.return_module + "&action=" + composePackage.return_action + "&record=" + composePackage.return_id;
                    window.location = url;
                }
            }
        }
    },

    /**
     * closes the editor that just sent email
     * @param string id ID of composeLayout tab
     */
    closeCompose : function(id) {
        // destroy tinyMCE instance
        var idx = id.substr(13, id.length);
        var instanceId = "htmleditor" + idx;
        tinyMCE.execCommand('mceRemoveControl', false, instanceId);

        // nullify DOM and namespace values.
        inCompose = false;
        SE.composeLayout[idx] = null;
        SE.tinyInstances[instanceId] = null;
        SE.innerLayout.getTab(idx).close();
    },

    /**
     * Returns a new instance ID, 0-index
     */
    getNewInstanceId : function() {
        this.currentInstanceId = this.currentInstanceId + 1;
        return this.currentInstanceId;
    },

    /**
     * Takes an array of objects that contain the filename and GUID of a Note (attachment or Sugar Document) and applies the values to the compose screen.  Valid use-cases are applying an EmailTemplate or resuming a Draft Email.
     */
    loadAttachments : function(result) {
        var idx = SE.composeLayout.currentInstanceId;

        if(typeof(result) == 'object') {
        	//jchi #20680. Clean the former template attachments;
        	var basket = document.getElementById('addedTemplateAttachments' + idx);
			if(basket.innerHTML != ''){
				confirm(mod_strings.LBL_CHECK_ATTACHMENTS, mod_strings.LBL_HAS_ATTACHMENTS, function(btn){
					if (btn != 'yes'){
						basket.innerHTML = '';
					}
				});
			}
            for(i in result) {
                if(typeof result[i] == 'object') {
                    var index = SE.composeLayout.addTemplateAttachmentField(idx);
                    var bean = result[i];
                    document.getElementById('templateAttachmentId' + idx + index).value = bean['id'];
                    document.getElementById('templateAttachmentName' + idx + index).innerHTML += bean['filename'];
                }
            }
        }
    },

    /**
     * fills drop-down values for email templates and signatures
     */
    setComposeOptions : function(idx) {
        // send from accounts
        var addressFrom = document.getElementById('addressFrom' + idx);
        
        if (addressFrom.options.length <= 0) {
        	SE.util.emptySelectOptions(addressFrom);
	        var fromAccountOpts = SE.composeLayout.fromAccounts;
	        for (id = 0 ; id < fromAccountOpts.length ; id++) {
	              var key = fromAccountOpts[id].value;
	              var display = fromAccountOpts[id].text;
	              var opt = new Option(display, key);
	              addressFrom.options.add(opt);
	        }
        }

        // email templates
        var et = document.getElementById('email_template' + idx);
        SE.util.emptySelectOptions(et);

        for(var key in this.emailTemplates) { // iterate through assoc array
            var display = this.emailTemplates[key];
            var opt = new Option(display, key);
            et.options.add(opt);
        }














        // signatures
        var sigs = document.getElementById('signatures' + idx);
        SE.util.emptySelectOptions(sigs);

        for(var key in this.signatures) { // iterate through assoc array
            var display = this.signatures[key];
            var opt = new Option(display, key);

            if(key == SE.userPrefs.signatures.signature_default) {
                opt.selected = true;
            }

            sigs.options.add(opt);
        }

        // character set
        var charset = document.getElementById('charsetOptions' + idx);
        for(var key in this.charsets) { // iterate through assoc array
            var display = this.charsets[key];
            var opt = new Option(display, key);

            if(key == SE.userPrefs.emailSettings.defaultOutboundCharset) {
                opt.selected = true;
            }

            charset.options.add(opt);
        }

        // html/plain email?
        var htmlEmail = document.getElementById('setEditor' + idx);
        if(SE.userPrefs.emailSettings.sendPlainText != 1) {
            htmlEmail.checked = true;
        }

        // save sent email on Sugar?
        var saveOnSugar = document.getElementById('saveOutbound' + idx);
        if(SE.userPrefs.emailSettings.alwaysSaveOutbound == "1") {
            saveOnSugar.checked = true;
        }

        SE.tinyInstances[SE.tinyInstances.currentHtmleditor].ready = true;
    },

    /**
     * After compose screen is rendered, async call to get email body from Sugar
     */
    replyForwardEmailStage2 : function() {
        SE.util.clearHiddenFieldValues('emailUIForm');
        overlay(app_strings.LBL_EMAIL_RETRIEVING_MESSAGE, app_strings.LBL_EMAIL_ONE_MOMENT);

        var ieId = SE.composeLayout.replyForwardObj.ieId;
        var uid = SE.composeLayout.replyForwardObj.uid;
        var mbox = SE.composeLayout.replyForwardObj.mbox;
        var type = SE.composeLayout.replyForwardObj.type;
        var idx = SE.composeLayout.currentInstanceId;

        var sugarEmail = (SE.composeLayout.replyForwardObj.sugarEmail) ? '&sugarEmail=true' : "";

        document.getElementById('emailSubject' + idx).value = type;
        document.getElementById('emailUIAction').value = 'composeEmail';
        document.getElementById('composeType').value = type;
        document.getElementById('ieId').value = ieId;
        document.getElementById('uid').value = uid;
        document.getElementById('mbox').value = mbox;

        var formObject = document.getElementById('emailUIForm');
        YAHOO.util.Connect.setForm(formObject);

        var sendType = type;
        AjaxObject.startRequest(callbackReplyForward, urlStandard + "&composeType=" + type + sugarEmail);
    }
};

////    END SE.composeLayout
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////    CONTEXT MENU CALLS
SE.contextMenus = {
    /**
     * Archives from context menu
     * @param Array uids
     * @param string ieId
     * @param string folder
     */
    _archiveToSugar : function(uids, ieId, folder) {
        var ser = '';

        for(var i=0; i<uids.length; i++) { // using 1 index b/c getSelectedRowIds doubles the first row's id
            if(ser != "") ser += app_strings.LBL_EMAIL_DELIMITER;
            ser += uids[i];
        }
        AjaxObject.startRequest(callbackImportOneEmail, urlStandard + '&emailUIAction=getImportForm&uid=' + ser + "&ieId=" + ieId + "&mbox=" + folder);
    },

    /**
     * Archives from context menu
     */
    archiveToSugar : function(menuItem) {
        SE.contextMenus.emailListContextMenu.hide();
        
        var rows = SE.grid.getSelections();
        var uids = [];
        /* iterate through available rows JIC a row is deleted - use first available */
        for(var i=0; i<rows.length; i++) {
            uids[i] = rows[i].data.uid;
        }
        SE.contextMenus._archiveToSugar(uids, rows[0].data.ieId, rows[0].data.mbox);
    },
    
    /**
     * Popup the printable version and start system's print function.
     */
    viewPrintable : function(menuItem) {
    	var rows = SE.grid.getSelections();
    	SE.detailView.viewPrintable(rows[0].data.ieId, rows[0].data.uid, rows[0].data.mbox);
    },

    /**
     * Marks email flagged on mail server
     */
    markRead : function(type, contextMenuId) {
        SE.contextMenus.markEmail('read');
    },

    /**
     * Assign this emails to people based on assignment rules
     */
    assignEmailsTo : function(type, contextMenuId) {
        SE.contextMenus.showAssignmentDialog();
    },
    
    /**
     * Marks email flagged on mail server
     */
    markFlagged : function(contextMenuId) {
        SE.contextMenus.markEmail('flagged');
    },

    /**
     * Marks email unflagged on mail server
     */
    markUnflagged : function(contextMenuId) {
        SE.contextMenus.markEmail('unflagged');
    },

    /**
     * Marks email unread on mail server
     */
    markUnread : function() {
        SE.contextMenus.markEmail('unread');
    },

    /**
     * Deletes an email from context menu
     */
    markDeleted : function() {
    	if(confirm(app_strings.LBL_EMAIL_DELETE_CONFIRM)) {
        	document.getElementById('_blank').innerHTML = "";
        	SE.contextMenus.markEmail('deleted');
    	}
    },

    /**
     * generic call API to apply a flag to emails on the server and on sugar
     * @param string type "read" | "unread" | "flagged" | "deleted"
     */
    markEmail : function(type) {
        SE.contextMenus.emailListContextMenu.hide();
        overlay(app_strings.LBL_EMAIL_PERFORMING_TASK, app_strings.LBL_EMAIL_ONE_MOMENT);

        //var dm = SE.grid.getStore();
        //var uids = SE.grid.getSelectedRowIds();
        //var indexes = SE.grid.getSelectedRowIndexes();
        var rows = SE.grid.getSelectedRows();
        if (rows.length == 0)
        	rows = [SE.contextMenus.currentRow];
        var ser = [ ];
        
        for(var i=0; i<rows.length; i++) {
            ser.push(SE.grid.getRecord(rows[i]).getData().uid);
        }

        ser = JSON.stringifyNoSecurity(ser);
            
        var ieId = SE.grid.getRecord(rows[0]).getData().ieId;
        var folder = SE.grid.getRecord(rows[0]).getData().mbox;


        var count = 0;
        if(type == 'read' || type == 'deleted') {
            // mark read
            for(var j=0; j<rows.length; j++) {
                if(SE.grid.getRecord(rows[j]).getData().seen == '0') { 
                    count = count + 1;
                    SE.grid.getRecord(rows[j]).setData("seen", "1");
                }
            }

            var node = SE.folders.getNodeFromIeIdAndMailbox(ieId, folder);
            var unseenCount = node.data.unseen;
            var finalCount = parseInt(unseenCount) - count;
            node.data.unseen = finalCount;

            SE.accounts.renderTree();
        } else if(type == 'unread') {
            // mark unread
            for(var j=0; j<rows.length; j++) {
                if(SE.grid.getRecord(rows[j]).getData().seen == '1') { // index [9] is the seen flag
                    count = count + 1;
                }
            }

            var node = SE.folders.getNodeFromIeIdAndMailbox(ieId, folder);
            var unseenCount = node.data.unseen;
            var finalCount = parseInt(unseenCount) + count;
            node.data.unseen = finalCount;
            SE.accounts.renderTree();
        }

        if (type == 'unread') {
	        for(var i=0; i<rows.length; i++) {
	            SE.cache[folder + SE.grid.getRecord(rows[i]).getData().uid] = null;
	        } // for
        }
        AjaxObject.startRequest(callbackContextmenus.markUnread, urlStandard + '&emailUIAction=markEmail&type=' + type + '&uids=' + ser + "&ieId=" + ieId + "&folder=" + folder);
    },

    /**
     * refreshes the ListView to show changes to cache
     */
    markEmailCleanup : function() {
        SE.accounts.renderTree();
        hideOverlay();
        SE.grid.getDataSource().sendRequest(SUGAR.util.paramsToUrl(SE.grid.params),  SE.grid.onDataReturnInitializeTable, SE.grid);
    },

	showAssignmentDialog : function() {
		if (SE.contextMenus.assignmentDialog == null) {
			AjaxObject.startRequest(callbackAssignmentDialog, urlStandard + '&emailUIAction=getAssignmentDialogContent');
		} else {
			SE.contextMenus.assignmentDialog.show();
		} // else
	},
	
	/**
     * shows the import dialog with only relate visible.
     */
    relateTo : function() {
        SE.contextMenus.emailListContextMenu.hide();
        
        var rows = SE.grid.getSelectedRows();
        var data = SE.grid.getRecord(rows[0]).getData();
        var ieId = data.ieId;
        var folder = data.mbox;
        var uids = [];
        /* iterate through available rows JIC a row is deleted - use first available */
        for(var i=0; i<rows.length; i++) {
            uids[i] = SE.grid.getRecord(rows[i]).getData().uid;
        }
        var ser = JSON.stringifyNoSecurity(uids);
        
        AjaxObject.startRequest(callbackRelateEmail, urlStandard + '&emailUIAction=getRelateForm&uid=' + ser + "&ieId=" + ieId + "&mbox=" + folder);
    },

	/**
     * shows the import dialog with only relate visible.
     */
    showDetailView : function() {
        SE.contextMenus.emailListContextMenu.hide();
        var rows = SE.grid.getSelections();
        if (rows.length > 1) {
        	alert(app_strings.LBL_EMAIL_SELECT_ONE_RECORD);
        	return;
        }
        var ieId = rows[0].data.ieId;
        var folder = rows[0].data.mbox;
        /* iterate through available rows JIC a row is deleted - use first available */
        var uid = rows[0].data.uid;
        SE.contextMenus.showEmailDetailViewInPopup(ieId, uid, folder);
    },
    
    /**
     *
     */
    showEmailDetailViewInPopup : function(ieId,uid, folder) {
        overlay(app_strings.LBL_EMAIL_RETRIEVING_RECORD, app_strings.LBL_EMAIL_ONE_MOMENT);
        AjaxObject.startRequest(callbackEmailDetailView, urlStandard + '&emailUIAction=getEmail2DetailView&uid=' + uid + "&ieId=" + ieId + "&mbox=" + folder + "&record=" + uid);
    },
    
    /**
     * Opens multiple messages from ListView context click
     */
    openMultiple : function() {
        SE.contextMenus.emailListContextMenu.hide();

        var rows = SE.grid.getSelections();
        var uids = SE.listView.getUidsFromSelection();

        if(uids.length > 0) {
            var mbox = rows[0].data.mbox;
            var ieId = rows[0].data.ieId;
            SE.detailView.populateDetailViewMultiple(uids, mbox, ieId, true);
        }
    },

    /**
     * Replies/forwards email
     */
    replyForwardEmailContext : function(menuItem) {
        SE.contextMenus.emailListContextMenu.hide();

        var indexes = SE.grid.getSelections();
        //var dm = SE.grid.getDataModel();
        var type = menuItem.id;

        for(var i=0; i<indexes.length; i++) {
            var row = indexes[i].data;
            SE.composeLayout.c0_replyForwardEmail(row.ieId, row.uid, row.mbox, type);
        }
    },
    
    //show menu functions
    showEmailsListMenu : function(grid, rowIndex, event) {
       event.stopEvent();
       var row = grid.getStore().getAt(rowIndex);
       SE.contextMenus.currentRow = row;
       var draft = (row.data.type == "draft");
       var menu = SE.contextMenus.emailListContextMenu;
       var folderNode;
       if (row.data.mbox == 'sugar::Emails') {
       	folderNode = SE.folders.getNodeFromIeIdAndMailbox('folder', row.data.ieId);
       } else {
       	folderNode = SE.folders.getNodeFromIeIdAndMailbox(row.data.ieId, row.data.mbox);
       }
       if (folderNode != null && 
          ((foldernode.data.is_group != null) && 
          (foldernode.data.is_group == 'true')) ||
          (foldernode.data.isGroup != null && foldernode.data.isGroup == "true")){
           menu.items.items[9]['show']();
       } else {
           menu.items.items[9]['hide']();
       }
       
       menu.items.items[2][draft ? 'hide' : 'show']();
       menu.items.items[3][draft ? 'hide' : 'show']();
       menu.items.items[4][draft ? 'hide' : 'show']();
       menu.items.items[5][draft ? 'hide' : 'show']();
       menu.items.items[8][draft ? 'hide' : 'show']();
       
       if (row.data.mbox == "sugar::Emails") {
           menu.items.items[2].hide();
           menu.items.items[0].show();
           menu.items.items[10].show();
       } else {
           menu.items.items[0].hide();
           menu.items.items[10].hide();
       }
       var coords = event.getXY();
       SE.contextMenus.emailListContextMenu.showAt([coords[0], coords[1]]);
    },
    
    showFolderMenu : function(grid, rowIndex, event) {
       event.stopEvent();
       var coords = event.getXY();
       SE.contextMenus.emailListContextMenu.showAt([coords[0], coords[1]]);
    }
};

SE.contextMenus.dv = {
    archiveToSugar : function(contextMenuId) {

        SE.contextMenus._archiveToSugar(uids, ieId, folder);
    },

    replyForwardEmailContext : function(all) {
        SE.contextMenus.detailViewContextMenu.hide();
    }

};





////    END SE.contextMenus
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
////    DETAIL VIEW
SE.detailView = {
    consumeMetaDetail : function(ret) {
        // handling if the Email drafts
        if(ret.type == 'draft') {
            SE.composeLayout.c0_composeDraft(); 
            return;
        }
        

        // cache contents browser-side
        SE._setDetailCache(ret);

        var displayTemplate = new YAHOO.SUGAR.Template(SE.templates['displayOneEmail']);
        // 2 below must be in global context
        meta = ret.meta;
        meta['panelId'] = SE.util.getPanelId();

        email = ret.meta.email;
        var out = displayTemplate.exec({
            'app_strings' : app_strings,
            'theme' : theme,
            'idx' : targetDiv.id,
            'meta' : meta,
            'email' : meta.email,
            'linkBeans' : linkBeans
        });
        targetDiv.set("label", meta.email.name);
        targetDiv.set("content", out);
        
        var displayFrame = document.getElementById('displayEmailFrame' + targetDiv.id);
        displayFrame.contentWindow.document.write(email.description);
        displayFrame.contentWindow.document.close();
        
        // hide archive links
        if(ret.meta.is_sugarEmail) {
			document.getElementById("archiveEmail" + targetDiv.id).innerHTML = "&nbsp;";
			if (viewRawEmail == 'false') {
				document.getElementById("viewMenuSpan" + meta['panelId']).innerHTML = "&nbsp";
			}
        } else {
            if (document.getElementById("showDeialViewForEmail" + targetDiv.id))
            	document.getElementById("showDeialViewForEmail" + targetDiv.id).innerHTML = "&nbsp;";
        } // else
        
    },

    consumeMetaPreview : function(ret) {
        // cache contents browser-side
        SE._setDetailCache(ret);
        
        
        
        var currrow = SE.grid.getLastSelectedRecord();
        currrow = SE.grid.getRecord(currrow);
        if (!currrow) {
            document.getElementById('_blank').innerHTML = '';
            return;
        }
        // handling if the Email drafts
        if(ret.type == 'draft'){
            if (currrow.getData().uid == ret.uid) {
                SE.composeLayout.c0_composeDraft();
            }
            return;
        }
        
        if (currrow.getData().uid != ret.meta.uid) {
           return;
        }
        
        // remove loading sprite
        document.getElementById('_blank').innerHTML = '<iframe id="displayEmailFramePreview"/>';
        var displayTemplate = new YAHOO.SUGAR.Template(SE.templates['displayOneEmail']);
        meta = ret.meta;
        meta['panelId'] = SE.util.getPanelId();
        email = ret.meta.email;

        document.getElementById('_blank').innerHTML = displayTemplate.exec({
            'app_strings' : app_strings,
            'theme' : theme,
            'idx' : 'Preview',
            'meta' : meta,
            'email' :meta.email,
            'linkBeans' : linkBeans
        });
       // document.getElementById('_blank').innerHTML = meta.email;
       /* displayTemplate.append('_blank', {
            'app_strings' : app_strings,
            'theme' : theme,
            'idx' : 'Preview',
            'meta' : meta,
            'email' :meta.email,
            'linkBeans' : linkBeans
        });*/
        
        var displayFrame = document.getElementById('displayEmailFramePreview');
        displayFrame.contentWindow.document.write(email.description);
        displayFrame.contentWindow.document.close();
        
        SE.listViewLayout.resizePreview();

        // hide archive links
        if(ret.meta.is_sugarEmail) {
            document.getElementById("archiveEmailPreview").innerHTML = "&nbsp;";
			if (viewRawEmail == 'false') {
				document.getElementById("archiveEmail" + meta['panelId']).style.display = "none";
			}
        } else {
          //mark email as read
         //   document.getElementById("showDetialViewForEmail" + meta['panelId']).innerHTML = "&nbsp;";
        }
    },

    /**
     * wraps emailDelete() for single messages, comes from preview or tab
     */
    emailDeleteSingle : function(ieId, uid, mbox) {
        if(confirm(app_strings.LBL_EMAIL_DELETE_CONFIRM)) {
            // find active panel
            var activePanel = SE.innerLayout.getActiveTab();

            if(activePanel != SE.listViewLayout) {
                SE.innerLayout.remove(activePanel);
            }
            document.getElementById('_blank').innerHTML = "";
	        var ser = [ ];
			ser.push(uid);
	        uid = JSON.stringifyNoSecurity(ser);
            this.emailDelete(ieId, uid, mbox);
        }
    },

    /**
     * Sends async call to delete a given message
     * @param
     */
    emailDelete : function(ieId, uid, mbox) {
       overlay(app_strings.LBL_EMAIL_DELETING_MESSAGE, app_strings.LBL_EMAIL_ONE_MOMENT);
       AjaxObject.startRequest(callbackContextmenus.markUnread, urlStandard + '&emailUIAction=markEmail&type=deleted&uids=' + 
           uid + "&ieId=" + ieId + "&folder=" + mbox);
    },

    /**
     * retrieves one email to display in the preview pane.
     */
    getEmailPreview : function() {
    	var row = SUGAR.email2.listView.currentRow;
    	var data = row.getData();
	    if (data /*&& !(!SUGAR.email2.contextMenus.emailListContextMenu.hidden && data.type =='draft')*/) {
	       var setRead = (data['seen'] == 0) ? true : false;
		   SUGAR.email2.listView.markRead(SUGAR.email2.listView.currentRowIndex, row);
		   SUGAR.email2.detailView.populateDetailView(data['uid'], data['mbox'], data['ieId'], setRead, SUGAR.email2.previewLayout);
	    }
    },

    /**
     * Imports one email into Sugar
     */
    importEmail : function(ieId, uid, mbox) {
        SE.util.clearHiddenFieldValues('emailUIForm');

        overlay(app_strings.LBL_EMAIL_IMPORTING_EMAIL, app_strings.LBL_EMAIL_ONE_MOMENT);

        var vars = "&ieId=" + ieId + "&uid=" + uid + "&mbox=" + mbox;
        AjaxObject.target = '';
        AjaxObject.startRequest(callbackImportOneEmail, urlStandard + '&emailUIAction=getImportForm' + vars);
    },

    /**
     * Populates the frameFlex div with the contents of an email
     */
    populateDetailView : function(uid, mbox, ieId, setRead, destination) {
    	SUGAR.email2.util.clearHiddenFieldValues('emailUIForm');

        var mboxStr = new String(mbox);
        var compKey = mbox + uid;

        if(mboxStr.substring(0,7) == 'sugar::') {
            // display an email from Sugar
            document.getElementById('emailUIAction').value = 'getSingleMessageFromSugar';
        } else {
            // display an email from an email server
            document.getElementById('emailUIAction').value = 'getSingleMessage';
        }
        document.getElementById('mbox').value = mbox;
        document.getElementById('ieId').value = ieId;
        document.getElementById('uid').value = uid;

        YAHOO.util.Connect.setForm(document.getElementById('emailUIForm'));

        AjaxObject.forceAbort = true;
        AjaxObject.target = '_blank';

        if(setRead == true) {
        	//SE.grid.getDataSource().getById(uid).set('seen', '1');
        	SE.listView.boldUnreadRows()
        	//SE.folders.decrementUnreadCount(ieId, mbox, 1);
        }

        if(destination == SE.innerLayout) {
        	/*
             * loading email into a tab, peer with ListView
             * targetDiv must remain in the global namespace as it is used by AjaxObject
             */
        	
        	//Check if we already have a tab of the email open
        	var tabs = SE.innerLayout.get("tabs");
        	for (var t in tabs) {
        		if (tabs[t].id && tabs[t].id == uid) {
        			SE.innerLayout.set("activeTab", tabs[t]);
        			return;
        		}
        	}
        	
        	targetDiv = new YAHOO.SUGAR.ClosableTab({
	        		label: loadingSprite,
					scroll : true,
					content : "",
					active : true
	        }, SE.innerLayout);
        	targetDiv.id = uid;
        	SE.innerLayout.addTab(targetDiv);
	    
            // use cache if available
            if(SE.cache[compKey]) {
            	SE.detailView.consumeMetaDetail(SE.cache[compKey]);
            } else {
                AjaxObject.startRequest(AjaxObject.detailView.callback.emailDetail, null); // open email as peer-tab to listView
            }
        } else {
            // loading email into preview pane
            document.getElementById('_blank').innerHTML = loadingSprite;

            // use cache if available
            if(SE.cache[compKey]) {
                SE.detailView.consumeMetaPreview(SE.cache[compKey]);
            } else {
                AjaxObject.forceAbort = true;
                AjaxObject.startRequest(AjaxObject.detailView.callback.emailPreview, null); // open in preview window
            }
        }
    },

    /**
     * Retrieves multiple emails for DetailView
     */
    populateDetailViewMultiple : function(uids, mbox, ieId, setRead) {
        overlay(app_strings.LBL_EMAIL_RETRIEVING_MESSAGE, app_strings.LBL_EMAIL_ONE_MOMENT);
        SE.util.clearHiddenFieldValues('emailUIForm');

        var mboxStr = new String(mbox);

        uids = SE.util.cleanUids(uids);

        if(mboxStr.substring(0,7) == 'sugar::') {
            // display an email from Sugar
            document.getElementById('emailUIAction').value = 'getMultipleMessagesFromSugar';
            document.getElementById('uid').value = uids;
        } else {
            // display an email from an email server
            document.getElementById('emailUIAction').value = 'getMultipleMessages';
            document.getElementById('mbox').value = mbox;
            document.getElementById('ieId').value = ieId;
            document.getElementById('uid').value = uids;
        }

        var formObject = document.getElementById('emailUIForm');
        YAHOO.util.Connect.setForm(formObject);

        AjaxObject.target = 'frameFlex';
        AjaxObject.startRequest(callbackEmailDetailMultiple, null);

        if(setRead == true) {
            var c = uids.split(",");
            SE.folders.decrementUnreadCount(ieId, mbox, c.length);
        }
    },

    /**
     * Makes async call to get QuickCreate form
     * Renders a modal edit view for a given module
     */
    quickCreate : function(module, ieId, uid, mailbox) {
        var get = "&qc_module=" + module + "&ieId=" + ieId + "&uid=" + uid + "&mailbox=" + mailbox;

        if(ieId == null || ieId == "null" || mailbox == 'sugar::Emails') {
            get += "&sugarEmail=true";
        }
        
        AjaxObject.startRequest(callbackQuickCreate, urlStandard + '&emailUIAction=getQuickCreateForm' + get);
    },

    /**
     * Makes async call to save a quick create
     * @param bool
     */
    saveQuickCreate : function(action) {
        var qcd = SE.detailView.quickCreateDialog;
        if (check_form('form_EmailQCView_' + qcd.qcmodule)) {
	        var formObject = document.getElementById('form_EmailQCView_' + qcd.qcmodule);
	        var theCallback = callbackQuickCreateSave;
	        var accountType = '&sugarEmail=true';
	        if (qcd.ieId != 'null' && qcd.mbox != 'sugar::Emails') {
	           accountType = '&ieId=' + qcd.ieId;
	        }
	        
            if (action == 'reply') {
	           theCallback = callbackQuickCreateSaveAndReply;
	        } else if (action == true) {
	            theCallback = callbackQuickCreateSaveAndAddToAddressBook;
	        }
	        formObject.action.value = 'EmailUIAjax';
	        YAHOO.util.Connect.setForm(formObject);
	        overlay('Saving', app_strings.LBL_EMAIL_ONE_MOMENT);
	        AjaxObject.startRequest(theCallback, "to_pdf=true&emailUIAction=saveQuickCreate&qcmodule=" + qcd.qcmodule + '&uid=' + qcd.uid +
	                               accountType + '&mbox=' + qcd.mbox);
        }
    },

    /**
     * Code to show/hide long list of email address in DetailView
     */
    showCroppedEmailList : function(el) {
        el.style.display = 'none';
        el.previousSibling.style.display = 'inline'
    },
    showFullEmailList : function(el) {
        el.style.display = 'none';
        el.nextSibling.style.display = 'inline';
    },

    /**
     * Shows the QuickCreate overlay
     * @param string ieId
     * @param string uid
     * @param string mailbox
     */
    showQuickCreate : function(ieId, uid, mailbox) {
        var panelId = SE.util.getPanelId();
        var context = document.getElementById("quickCreateSpan" + panelId);
        
        if (!SE.detailView.cqMenus)
        	SE.detailView.cqMenus = {};
        
        if (SE.detailView.cqMenus[context]) 
        	SE.detailView.cqMenus[context].destroy();
        
	    var menu = SE.detailView.cqMenus[context] = new YAHOO.widget.Menu("qcMenuDiv" + panelId, {
    		lazyload:true,
    		context: ["quickCreateSpan" + panelId, "tr","br", ["beforeShow", "windowResize"]]
        });
	    
	    for (var i=0; i < this.qcmodules.length; i++) {
            var module = this.qcmodules[i];
            menu.addItem({
                text:   app_strings['LBL_EMAIL_QC_' + module.toUpperCase()],
                modulename: module,
                value: module,
                onclick: { fn: function() {
            			SE.detailView.quickCreate(this.value, ieId, uid, mailbox);
            		}
            	}
            });
        }
		
		menu.render(document.body);
		menu.show();
    },

    /**
     * Displays the "View" submenu in the detailView
     * @param string ieId
     * @param string uid
     * @param string mailbox
     */
    showViewMenu : function(ieId, uid, mailbox) {
        var panelId = SE.util.getPanelId();
        var context = "btnEmailView" + panelId;
        if (!SE.detailView.viewMenus)
        	SE.detailView.viewMenus = {};
        
        if (SE.detailView.viewMenus[context]) 
        	SE.detailView.viewMenus[context].destroy();
        
	    var menu = SE.detailView.viewMenus[context] = new YAHOO.widget.Menu("menuDiv" + panelId, {
    		lazyload:true,
    		context: ["btnEmailView" + panelId, "tl","bl", ["beforeShow", "windowResize"]],
    		clicktohide: true
        });
		menu.addItems(
				(ieId == 'null' || ieId == null) ? 
			//No ieId - Sugar Email
			[{
				text: app_strings.LBL_EMAIL_VIEW_RAW,
				onclick: { fn: function() {SE.detailView.viewRaw(ieId, uid, mailbox);} }
            }]
			:
			//IeID exists, on a remote server
			[{
                text: app_strings.LBL_EMAIL_VIEW_HEADERS,
                onclick: { fn: function() {SE.detailView.viewHeaders(ieId, uid, mailbox);}}
            },{
                text: app_strings.LBL_EMAIL_VIEW_RAW,
                onclick: { fn: function() {SE.detailView.viewRaw(ieId, uid, mailbox);}}
            }]
        );
		menu.render(document.body);
		menu.show();
		

        /*
        //#23108 jchi@07/17/2008
        menu.render('quickCreateSpan'+ panelId);*/
        //this.viewMenu = menu;
        //this.viewMenu.show(context);
    },
    /**
     * Makes async call to get an email's headers
     */
    viewHeaders : function(ieId, uid, mailbox) {
        var get = "&type=headers&ieId=" + ieId + "&uid=" + uid + "&mailbox=" + mailbox;
        AjaxObject.startRequest(AjaxObject.detailView.callback.viewRaw, urlStandard + "&emailUIAction=displayView" + get);
    },

    /**
     * Makes async call to get a printable version
     */
    viewPrintable : function(ieId, uid, mailbox) {
    	if(mailbox == 'sugar::Emails') {
            // display an email from Sugar
            var emailUIAction = '&emailUIAction=getSingleMessageFromSugar';
        } else {
            // display an email from an email server
            var emailUIAction = '&emailUIAction=getSingleMessage';
        }

        var get = "&type=printable&ieId=" + ieId + "&uid=" + uid + "&mbox=" + mailbox;
        AjaxObject.startRequest(AjaxObject.detailView.callback.viewPrint, urlStandard + emailUIAction + get);
    },

    /**
     * Makes async call to get an email's raw source
     */
    viewRaw : function(ieId, uid, mailbox) {
    	var get = "&type=raw&ieId=" + ieId + "&uid=" + uid + "&mailbox=" + mailbox;
        AjaxObject.startRequest(AjaxObject.detailView.callback.viewRaw, urlStandard + "&emailUIAction=displayView" + get);
    }
};
////    END SE.detailView
///////////////////////////////////////////////////////////////////////////////



///////////////////////////////////////////////////////////////////////////////
////    SE.folders
SE.folders = {
    contextMenuFocus : new Object(),

    /**
     * Generates a standardized identifier that allows reconstruction of I-E ID-folder strings or
     * SugarFolder ID - folder strings
     */
    _createFolderId : function(node) {
        var ret = '';

        if(!node.data.id)
            return ret;

        if(node.data.ieId) {
            /* we have a local Sugar folder */
            if(node.data.ieId == 'folder') {
                ret = "sugar::" + node.data.id; // FYI: folder_id is also stored in mbox field
            } else if(node.data.ieId.match(SE.reGUID)) {
                ret = "remote::" + node.data.ieId + "::" + node.data.mbox.substr(node.data.mbox.indexOf("INBOX"), node.data.mbox.length);
            }
        } else {
            ret = node.data.id;
        }

        return ret;
    },

    addChildNode : function(parentNode, childNode) {
        var is_group = (childNode.properties.is_group == 'true') ? 1 : 0;
        var is_dynamic = (childNode.properties.is_dynamic == 'true') ? 1 : 0;
        var node = this.buildTreeViewNode(childNode.label, childNode.properties.id, is_group, is_dynamic, childNode.properties.unseen, parentNode, childNode.expanded);

        if(childNode.nodes) {
            if(childNode.nodes.length > 0) {
                for(j=0; j<childNode.nodes.length; j++) {
                    var newChildNode = childNode.nodes[j];
                    this.addChildNode(node, newChildNode);
                }
            }
        }
    },

    /**
     * Handles Group Folder adds via Settings->Folders (only admins)
     */
    addNewGroupFolder : function() {
        if(document.getElementById('groupFolderAddName').value == "") {
            alert(app_strings.LBL_EMAIL_ERROR_ADD_GROUP_FOLDER);
            return false;
        }

        if(this.isUniqueFolderName(document.getElementById('groupFolderAddName').value)) {
            overlay(app_strings.LBL_EMAIL_SETTINGS_GROUP_FOLDERS_CREATE, app_strings.LBL_EMAIL_ONE_MOMENT);
            var get = '';
            var groupFolderAddName = document.getElementById('groupFolderAddName').value;
            var groupFoldersAdd = document.getElementById('groupFoldersAdd').value;
            var get = "&name=" + groupFolderAddName + "&parent_folder=" + groupFoldersAdd + "&group_id=" + '';




            AjaxObject.startRequest(callbackAddGroupFolderFrom, urlStandard + '&emailUIAction=addGroupFolder' + get);
        } else {
            alert(app_strings.LBL_EMAIL_ERROR_DUPE_FOLDER_NAME);
            document.getElementById('groupFolderAddName').focus();
            return;
        }
    },

    saveGroupFolder : function() {
        if(document.getElementById('groupFolderAddName').value == "") {
            alert(app_strings.LBL_EMAIL_ERROR_ADD_GROUP_FOLDER);
            return false;
        }
        overlay(app_strings.LBL_EMAIL_SETTINGS_GROUP_FOLDERS_Save, app_strings.LBL_EMAIL_ONE_MOMENT);
        var get = '';
        var groupFolderAddName = document.getElementById('groupFolderAddName').value;
        var groupFoldersAdd = document.getElementById('groupFoldersAdd').value;
        var get = "&name=" + groupFolderAddName + "&parent_folder=" + groupFoldersAdd + "&group_id=" + '';



        var editGroupFolderList = document.getElementById('editGroupFolderList');
        var groupFolderIndex = editGroupFolderList.selectedIndex;
		get += "&record=" + editGroupFolderList.options[groupFolderIndex].value;
        AjaxObject.startRequest(callbackSaveGroupFolderFrom, urlStandard + '&emailUIAction=saveGroupFolder' + get);
    	
    },
    /**
     * Handles Editing of a Group Folder
     */
    editGroupFolder : function(folderId) {
        if(folderId == '') {
            document.getElementById('groupFolderAddName').value = '';
            document.getElementById('editGroupFolderList').options[0].selected = true;
            var groupFoldersAdd = document.getElementById('groupFoldersAdd');
			SE.util.emptySelectOptions(groupFoldersAdd);
			var grp = document.getElementById('groupFolders');
			for(i=0; i<grp.options.length; i++) {
				groupFoldersAdd.options.add(new Option(grp.options[i].text, grp.options[i].value));
			}
            groupFoldersAdd.options[0].selected = true;
			document.getElementById('addNewFolders').style.display = '';
			document.getElementById('saveGroupFolder').style.display = 'none';
			document.getElementById('cancelEditGroupFolder').style.display = 'none';
            
        } else {
			document.getElementById('addNewFolders').style.display = 'none';
			document.getElementById('saveGroupFolder').style.display = '';
			document.getElementById('cancelEditGroupFolder').style.display = '';
	        overlay(app_strings.LBL_EMAIL_SETTINGS_RETRIEVING_GROUP, app_strings.LBL_EMAIL_ONE_MOMENT);
	        get = "&folderId=" + folderId;
            AjaxObject.startRequest(callbackEditGroupFolder, urlStandard + '&emailUIAction=getGroupFolder' + get);
	
	        //var formObject = document.getElementById('ieSelect');
	        //formObject.emailUIAction.value = 'getIeAccount';
	
	        //YAHOO.util.Connect.setForm(formObject);
	
	        //AjaxObject.startRequest(callbackIeAccountRetrieve, null);
        } // else
    },
    /**
     * Builds and returns a new TreeView Node
     * @param string name
     * @param string id
     * @param int is_group
     * @return object
     */
    buildTreeViewNode : function(name, id, is_group, is_dynamic, unseen, parentNode, expanded) {
        var node = new YAHOO.widget.TextNode(name, parentNode, true);

        //node.href = " SE.listView.populateListFrameSugarFolder(YAHOO.namespace('frameFolders').selectednode, '" + id + "', 'false');";
        node.expanded = expanded;
        node.data = new Object;
        node.data['id'] = id;
        node.data['mbox'] = id; // to support DD imports into BRAND NEW folders
        node.data['label'] = name;
        node.data['ieId'] = 'folder';
        node.data['isGroup'] = (is_group == 1) ? 'true' : 'false';
        node.data['isDynamic'] = (is_dynamic == 1) ? 'true' : 'false';
        node.data['unseen'] = unseen;
        return node;
    },

    /**
     * ensures that a new folder has a valid name
     */
    checkFolderName : function(name) {
        if(name == "")
            return false;

        this.folderAdd(name);
    },

    /**
     * Pings email servers for new email - forces refresh of folder pane
     */
    checkEmailAccounts : function() {
        this.checkEmailAccountsSilent(true);
    },

    checkEmailAccountsSilent : function(showOverlay) {
        if(typeof(SE.folders.checkingMail)) {
            clearTimeout(SE.folders.checkingMail);
        }

        // don't stomp an on-going request
        if(AjaxObject.currentRequestObject.conn == null) {
            if(showOverlay) {
                overlay(app_strings.LBL_EMAIL_CHECKING_NEW,
                      app_strings.LBL_EMAIL_ONE_MOMENT + "<br>&nbsp;<br><i>" + app_strings.LBL_EMAIL_CHECKING_DESC + "</i>");
            }
            AjaxObject.startRequest(AjaxObject.folders.callback.checkMail, urlStandard + '&emailUIAction=checkEmail&all=true');
        } else {
            // wait 5 secs before trying again.
            SE.folders.checkingMail = setTimeout("SE.folders.checkEmailAccountsSilent(false);", 5000);
        }
    },
    
    /**
     * Starts check of all email Accounts using a loading bar for large POP accounts
     */
    startEmailAccountCheck : function() {
        // don't do two checks at the same time
       if(!AjaxObject.requestInProgress()) {
            overlay(app_strings.LBL_EMAIL_ONE_MOMENT, app_strings.LBL_EMAIL_CHECKING_NEW, 'progress');
            SE.accounts.ieIds = SE.folders.getIeIds();
            if (SE.accounts.ieIds.length > 0) {
            	AjaxObject.startRequest(AjaxObject.accounts.callbackCheckMailProgress, urlStandard + 
                                '&emailUIAction=checkEmailProgress&ieId=' + SE.accounts.ieIds[0] + "&currentCount=0");
            } else {
               hideOverlay();
            }
        } else {
            // wait 5 secs before trying again.
            SE.folders.checkingMail = setTimeout("SE.folders.startEmailAccountCheck();", 5000);
        }
    },
    
    /**
     * Checks a single Account check based on passed ieId
     */
     startEmailCheckOneAccount : function(ieId, synch) {
            if (synch) {
                synch = true;
            } else {
                synch = false;
            }
            var mbox = "";
            var node = SE.clickedFolderNode;
            if (node && !synch) {
            	mbox = node.data.mbox;
            } // if
            overlay(app_strings.LBL_EMAIL_ONE_MOMENT, app_strings.LBL_EMAIL_CHECKING_DESC, 'progress');
            SE.accounts.ieIds = [ieId];
            AjaxObject.startRequest(AjaxObject.accounts.callbackCheckMailProgress, urlStandard + 
                                '&emailUIAction=checkEmailProgress&mbox=' + mbox + '&ieId=' + ieId + "&currentCount=0&synch=" + synch);
      },


    /**
     * Empties trash for subscribed accounts
     */
    emptyTrash : function() {
        SE.contextMenus.frameFoldersContextMenu.hide();
        overlay(app_strings.LBL_EMAIL_EMPTYING_TRASH, app_strings.LBL_EMAIL_ONE_MOMENT);
        AjaxObject.startRequest(callbackEmptyTrash, urlStandard + '&emailUIAction=emptyTrash');
    },
    
    /**
     * Clears Cache files of the inboundemail account
     */
    clearCacheFiles : function(ieId) {
        SE.contextMenus.frameFoldersContextMenu.hide();
        overlay(app_strings.LBL_EMAIL_CLEARING_CACHE_FILES, app_strings.LBL_EMAIL_ONE_MOMENT);
        AjaxObject.startRequest(callbackClearCacheFiles, urlStandard + '&ieId=' + ieId + '&emailUIAction=clearInboundAccountCache');
    },
    
    
    /**
     * Returns an array of all the active accounts in the folder view
     */
    getIeIds : function() {
         var ieIds = [];
         var root = SE.tree.getRoot();
         for(i=0; i < root.children.length; i++) {
           if ((root.children[i].data.cls == "ieFolder" && root.children[i].children.length > 0) ||
           		(root.children[i].data.isGroup != null && root.children[i].data.isGroup == "true" && root.children[i].children.length > 0)) {
               ieIds.push(root.children[i].children[0].data.ieId);
           }
         }
         return ieIds;
     },

    /**
     * loads folder lists in Settings->Folders
     */
    lazyLoadSettings : function() {
        AjaxObject.timeout = 300000; // 5 min timeout for long checks
        AjaxObject.startRequest(callbackSettingsFolderRefresh, urlStandard + '&emailUIAction=getFoldersForSettings');
    },

    /**
     * After the add new folder is done via folders tab on seetings, this function should get called
     * It will refresh the folder list after inserting an entry on the UI to update the new folder list
     */
    loadSettingFolder : function() {
        AjaxObject.timeout = 300000; // 5 min timeout for long checks
        AjaxObject.startRequest(callbackLoadSettingFolder, urlStandard + '&emailUIAction=getFoldersForSettings');
    },
    
    /**
     * Recursively removes nodes from the TreeView of type Sugar (data.ieId = 'folder')
     */
    removeSugarFolders : function() {
        var tree = SE.tree;
        var root = tree.getRoot();
        var folder = SE.util.findChildNode(root, "ieId", "folder");
        while(folder) {
            root.removeChild(folder);
            folder = SE.util.findChildNode(root, "ieId", "folder");
        }
        if (!root.childrenRendered) {
        	root.childrenRendered = true;
        }
    },
    
    rebuildFolders : function(silent) {
      if (!silent) overlay(app_strings.LBL_EMAIL_REBUILDING_FOLDERS, app_strings.LBL_EMAIL_ONE_MOMENT);
       AjaxObject.startRequest(callbackFolders, urlStandard + '&emailUIAction=getAllFoldersTree');
    },

    /**
     * Updates TreeView with Sugar Folders
     */
    setSugarFolders : function(delay) {
        /*if (delay) {
			if (typeof(SE.folders.setSugarFoldersTask) == 'undefined') {
				SE.folders.setSugarFoldersTask = new Ext.util.DelayedTask(SE.folders.setSugarFolders, this); 
			}
			SE.folders.setSugarFoldersTask.delay(delay);
		} else {*/
			this.removeSugarFolders();
			AjaxObject.forceAbort = true;
			AjaxObject.startRequest(callbackRefreshSugarFolders, urlStandard + "&emailUIAction=refreshSugarFolders");
		//}
    },

    /**
     * Takes async data object and creates the sugar folders in TreeView
     */
    setSugarFoldersEnd : function(o) {
        var root = SE.tree.getRoot();
        addChildNodes(root, {nodes: o});
        SE.accounts.renderTree();
    },

    startCheckTimer : function() {
        if(SE.userPrefs.emailSettings.emailCheckInterval && SE.userPrefs.emailSettings.emailCheckInterval != -1) {
            var ms = SE.userPrefs.emailSettings.emailCheckInterval * 60 * 1000;

            if(typeof(SE.folders.checkTimer) != 'undefined') {
                clearTimeout(SE.folders.checkTimer);
            }

            SE.folders.checkTimer = setTimeout("SE.folders.checkEmailAccountsSilent(false);", ms);
        }
    },

    /**
     * makes an async call to save user preference and refresh folder view
     * @param object SELECT list object
     */
    setFolderSelection : function() {
        overlay(app_strings.LBL_EMAIL_REBUILDING_FOLDERS, app_strings.LBL_EMAIL_ONE_MOMENT);
		SE.search.markSearchAccountListDirty();

        document.getElementById('emailUIAction2').value = 'setFolderViewSelection';

        var formObject = document.getElementById('ieSubscribe');
        YAHOO.util.Connect.setForm(formObject);

        AjaxObject.startRequest(callbackFolders, null);
    },

    /**
     * makes async call to save user preference for a given node's open state
     * @param object node YUI TextNode object
     */
    setOpenState : function(node) {
        SE.util.clearHiddenFieldValues('emailUIForm');
        var nodePath = node.data.id;
        var nodeParent = node.parent;

        while(nodeParent != null) {
            // root node has no ID param
            if(nodeParent.data != null) {
                nodePath = nodeParent.data.id + "::" + nodePath;
            }

            var nodeParent = nodeParent.parent;
        }

        document.getElementById('emailUIAction').value = 'setFolderOpenState';
        document.getElementById('focusFolder').value = nodePath;

        if(node.expanded == true) {
            document.getElementById('focusFolderOpen').value = 'open';
        } else {
            document.getElementById('focusFolderOpen').value = 'closed';
        }

        var formObject = document.getElementById('emailUIForm');
        YAHOO.util.Connect.setForm(formObject);

        AjaxObject.startRequest(null, null);
    },

    getNodeFromMboxPath : function(path) {
        var tree = YAHOO.widget.TreeView.getTree('frameFolders');
        var a = JSON.parse(path);

        var node = tree.getRoot();

        var i = 0;
        while(i < a.length) {
            node = this.getChildNodeFromLabel(node, a[i]);
            i++;
        }

        return node;
    },

    getChildNodeFromLabel : function(node, nodeLabel) {
        for(i=0; i<node.children.length; i++) {
            if(node.children[i].data.id == nodeLabel) {
                return node.children[i];
            }
        }
    },

    /**
     * returns the node that presumably under the user's right-click
     */
    getNodeFromContextMenuFocus : function() {
        //// get the target(parent) node
        var tree = YAHOO.widget.TreeView.trees.frameFolders;
        var index = -1;
        var target = SE.contextMenus.frameFoldersContextMenu.contextEventTarget;

        // filter local folders
        if(target.className == 'localFolder' || target.className == 'groupInbox') {
            while(target && (target.className == 'localFolder' || target.className == 'groupInbox')) {
                if(target.id == '') {
                    target = target.parentNode;
                } else {
                    break;
                }
            }
        }

        var targetNode = document.getElementById(target.id);
        re = new RegExp(/ygtv[a-z]*(\d+)/i);

        try {
            var matches = re.exec(targetNode.id);
        } catch(ex) {
            return document.getElementById(ygtvlabelel1);
        }

        if(matches) {
            index = matches[1];
        } else {
            // usually parent node
            matches = re.exec(targetNode.parentNode.id);

            if(matches) {
                index = matches[1];
            }
        }

        var parentNode = (index == -1) ? tree.getNodeByProperty('id', 'Home') : tree.getNodeByIndex(index);
        parentNode.expand();

        return parentNode;
    },

    /**
     * Decrements the Unread Email count in folder text
     * @param string ieId ID to look for
     * @param string mailbox name
     * @param count how many to decrement
     */
    decrementUnreadCount : function(ieId, mbox, count) {
        if(mbox.indexOf("sugar::") === 0) {
            var node = this.getNodeFromId(ieId);
        } else {
            var node = this.getNodeFromIeIdAndMailbox(ieId, mbox);
        }
        if(node) {
            var unseen = node.data.unseen;
            if(unseen > 0) {
                var check = unseen - count;
                var finalCount = (check >= 0) ? check : 0;
                node.data.unseen = finalCount;
            }
            SE.accounts.renderTree();
        }
    },

    /**
     * gets the TreeView node with a given ID/ieId
     * @param string id ID to look for
     * @return object Node
     */
    getNodeFromId : function(id) {
        SE.folders.focusNode = null;
        SE.util.cascadeNodes(SE.tree.getRoot(), function(ieId) {
            if ((this.data.id && this.data.id == ieId) || (this.data.ieId && this.data.ieId == ieId)) {
                SE.folders.focusNode = this;
                return false;
            }
        }, null, [id]);
        return SE.folders.focusNode;
    },

    /**
     * Uses ieId and mailbox to try to find a node in the tree
     */
    getNodeFromIeIdAndMailbox : function(id, mbox) {
		SE.folders.focusNode = SE.folders.getNodeFromId(id);
		if (SE.folders.focusNode) {
			return SE.folders.focusNode;
		}
        if (mbox == "sugar::Emails") {        
        	mbox = id;
        	id = "folder";
        } // if
    	SE.util.cascadeNodes(SE.tree.getRoot(), function(varsarray) {
    		if (varsarray instanceof Array) {
            if (this.attributes.ieId && this.attributes.ieId == varsarray[0] 
                    && this.attributes.mbox == varsarray[1]) {
                SE.folders.focusNode = this;
                return false;
            } }
    		else {
    			if (this.attributes.ieId && this.attributes.ieId == varsarray) {
    				SE.folders.focusNode = this;
                    return false;
    			}
    		}
        }, null, [[id, mbox]]);
        return SE.folders.focusNode;
    },
    
    unhighliteAll : function() {
    	SE.util.cascadeNodes(SE.tree.getRoot(), function(){this.unhighlight()});
    },

    /**
     * Displays a short form
     */
    folderAdd : function() {
        SE.contextMenus.frameFoldersContextMenu.hide();

        var node = SE.clickedFolderNode;

        if(node != null && node.data) {
            overlay(app_strings.LBL_EMAIL_FOLDERS_ADD_DIALOG_TITLE, 
                    app_strings.LBL_EMAIL_SETTINGS_NAME, 
                    'prompt', {fn:SE.folders.folderAddXmlCall});
        } else {
            alert(app_strings.LBL_EMAIL_FOLDERS_NO_VALID_NODE);
        }
    },

    folderAddXmlCall : function(name) {
        var post = '';
        var type = 'sugar';

        var parentNode = SE.clickedFolderNode;
        
        this.contextMenuFocus = parentNode;

        if(parentNode.data.ieId) {
            if(parentNode.data.ieId != 'folder' && parentNode.data.ieId.match(SE.reGUID)) {
                type = 'imap';
            }
        }
        if(type == 'imap') {
        	// make an IMAP folder
            post = "&newFolderName=" + name + "&mbox=" + parentNode.data.mbox + "&ieId=" + parentNode.data.ieId;
            AjaxObject.startRequest(callbackFolderUpdate, urlStandard + '&emailUIAction=saveNewFolder&folderType=imap' + post);
        } else if(type == 'sugar') {
            // make a Sugar folder
            if(SE.folders.isUniqueFolderName(name)) {
                post = "&parentId=" + parentNode.data.id + "&nodeLabel=" + name;
                AjaxObject.startRequest(callbackFolderSave, urlStandard + '&emailUIAction=saveNewFolder&folderType=sugar&' + post);
            } else {
                alert(app_strings.LBL_EMAIL_ERROR_DUPE_FOLDER_NAME);
                SE.folders.folderAdd();
                return;
            }
        } else {
            alert(app_strings.LBL_EMAIL_ERROR_CANNOT_FIND_NODE);
        }

        // hide add-folder diaglogue
        SE.e2overlay.hide();
    },

    /**
     * Removes either an IMAP folder or a Sugar Folder
     */
    folderDelete : function() {
        SE.contextMenus.frameFoldersContextMenu.hide();
        
        if(confirm(app_strings.LBL_EMAIL_FOLDERS_DELETE_CONFIRM)) {
            var post = '';
            var parentNode = SE.clickedFolderNode;

            if(parentNode != null && parentNode.data) {
                if(parentNode.data.mbox == 'INBOX' || parentNode.data.id == 'Home') {
                    overlay(app_strings.LBL_EMAIL_ERROR_GENERAL_TITLE, app_strings.LBL_EMAIL_FOLDERS_CHANGE_HOME, 'alert');
                    return;
                }

                AjaxObject.target = 'frameFlex';

                if(parentNode.data.ieId != 'folder') {
                    // delete an IMAP folder
                    post = "&folderType=imap&mbox=" + parentNode.data.mbox + "&ieId=" + parentNode.data.ieId;
                } else {
                    // delete a sugar folder
                    post = "&folderType=sugar&folder_id=" + parentNode.data.id;
                }
                overlay("Deleting folder", app_strings.LBL_EMAIL_ONE_MOMENT);
                AjaxObject.startRequest(callbackFolderDelete, urlStandard + '&emailUIAction=deleteFolder' + post);
            } else {
                alert(app_strings.LBL_EMAIL_ERROR_CANNOT_FIND_NODE);
            }
        }
    },

    /**
     * Rename folder form
     */
     //EXT111
    folderRename : function() {
        SE.contextMenus.frameFoldersContextMenu.hide();
        var node = SE.clickedFolderNode;

        if(node != null) {
            if(node.id == 'Home' || !node.data || node.data.mbox == 'INBOX') {
                overlay(app_strings.LBL_EMAIL_ERROR_GENERAL_TITLE, app_strings.LBL_EMAIL_FOLDERS_CHANGE_HOME, 'alert');
                return;
            }
            SE.tree.folderEditor.editNode = node;
            SE.tree.folderEditor.enable();
            SE.tree.folderEditor.startEdit(node.ui.textNode);
        } else {
            alert(app_strings.LBL_EMAIL_FOLDERS_NO_VALID_NODE);
        }
    },

    /**
     * fills an Object with key-value pairs of available folders
     */
    getAvailableFoldersObject : function() {
        var ret = new Object();
        var tree = SE.tree.root;

        if(tree.children) {
            for(var i=0; i<tree.children.length; i++) {
                ret = this.getFolderFromChild(ret, tree.children[i], '', app_strings.LBL_EMAIL_SPACER_MAIL_SERVER);
            }
        } else {
            ret['none'] = app_strings.LBL_NONE;
        }

        return ret;
    },

    /**
     * Fills in key-value pairs for dependent dropdowns
     * @param object ret Associative array
     * @param object node TreeView node in focus
     * @param string currentPath Built up path thus far
     * @param string spacer Defined in app_strings, visual separator b/t Sugar and Remote folders
     */
    getFolderFromChild : function(ret, node, currentPath, spacer) {
        if(node.data != null && node.depth > 0) {
            /* handle visual separtors differentiating b/t mailserver and local */
            if(node.data.ieId && node.data.ieId == 'folder') {
                spacer = app_strings.LBL_EMAIL_SPACER_LOCAL_FOLDER;
            }

            if(!ret.spacer0) {
                ret['spacer0'] = spacer;
            } else if(ret.spacer0 != spacer) {
                ret['spacer1'] = spacer
            }

            var theLabel = node.data.label.replace(/<[^>]+[\w\/]+[^=>]*>/gi, '');
            var depthMarker = currentPath;
            var retIndex = SE.folders._createFolderId(node);
            ret[retIndex] = depthMarker + theLabel;
        }

        if(node.children != null) {
            if(theLabel) {
                currentPath += theLabel + "/";
            }

            for(var i=0; i<node.children.length; i++) {
                ret = this.getFolderFromChild(ret, node.children[i], currentPath, spacer);
            }
        }

        return ret;
    },

    /**
     * Wrapper to refresh folders tree
     */
    getFolders : function() {
        SE.accounts.rebuildFolderList();
    },

    /**
     * handles events around folder-rename input field changes
     * @param object YUI event object
     */
    handleEnter : function(e) {
        switch(e.browserEvent.type) {
            case 'click':
                e.preventDefault(); // click in text field
            break;

            case 'blur':
                SE.folders.submitFolderRename(e);
            break;

            case 'keypress':
                var kc = e.browserEvent.keyCode;
                switch(kc) {
                    case 13: // enter
                        e.preventDefault();
                        SE.folders.submitFolderRename(e);
                    break;

                    case 27: // esc
                        e.preventDefault(e);
                        SE.folders.cancelFolderRename(e);
                    break;
                }
            break;
        }
    },
    /**
    * Called when a node is clicked on in the folder tree
    * @param node, The node clicked on
    * @param e, The click event
    */
    handleClick : function(o) {
    	var node = o.node;
        //If the click was on a sugar folder
    	if (node.data.ieId == "folder") {
            SE.listView.populateListFrameSugarFolder(node, node.id, false);
        }
        else {
            SE.listView.populateListFrame(node, node.data.ieId, false);
        }
       //eval(node.data.click);
       //debugger;
    },
    
    /**
    * Called when a node is right-clicked on in the folder tree
    */
    handleRightClick : function(e) {
    	YAHOO.util.Event.preventDefault(e);
		//Get the Tree Node
		var node = SUGAR.email2.tree.getNodeByElement(YAHOO.util.Event.getTarget(e));
		var menu = SUGAR.email2.contextMenus.frameFoldersContextMenu;
		
		//If the click was on a sugar folder
        SE.clickedFolderNode = node;
        var inbound = (node.data.ieId && node.data.ieId != 'folder');
		var disableNew = (inbound && (typeof(node.data.mbox) == 'undefined'));
		menu.getItem(0).cfg.setProperty("disabled", !inbound);
		menu.getItem(1).cfg.setProperty("disabled", !inbound);
		menu.getItem(2).cfg.setProperty("disabled", disableNew);
		menu.getItem(3).cfg.setProperty("disabled", false);
		menu.getItem(4).cfg.setProperty("disabled", false);
		menu.getItem(5).cfg.setProperty("disabled", false);
		menu.getItem(6).cfg.setProperty("disabled", true);
		//Group folder
		if (inbound && node.data.isGroup != null && node.data.isGroup == "true") {
			menu.getItem(0).cfg.setProperty("disabled", true);
			menu.getItem(1).cfg.setProperty("disabled", true);
			menu.getItem(2).cfg.setProperty("disabled", true);
			menu.getItem(3).cfg.setProperty("disabled", true);
			menu.getItem(4).cfg.setProperty("disabled", true);
		}
        if (node.data.protocol != null) {
        	menu.getItem(6).cfg.setProperty("disabled", false);
        }
		if (node.data.folder_type != null && (node.data.folder_type == "inbound" ||
				node.data.folder_type == "sent" || node.data.folder_type == "draft")) {
			//Sent or Draft folders
			menu.getItem(3).cfg.setProperty("disabled", true);
			menu.getItem(4).cfg.setProperty("disabled", true);
			menu.getItem(5).cfg.setProperty("disabled", true);
		}
		menu.cfg.setProperty("xy", YAHOO.util.Event.getXY(e));
		menu.show();
    },
    
    /**
    * Called when a row is dropped on a node
    */
    handleDrop : function(rows, targetFolder) {
        var rowData = rows[0].getData();
        if (rowData.mbox != targetFolder.data.mbox) {
            var srcIeId = rowData.ieId;
            var srcFolder = rowData.mbox;
            var destIeId = targetFolder.data.ieId;
            var destFolder = targetFolder.data.mbox;
            var uids = [];
            for(var i=0; i<rows.length; i++) {
                uids[i] = rows[i].getData().uid;
            }
            SE.listView.moveEmails(srcIeId, srcFolder, destIeId, destFolder, uids, rows);
        }
    },
    
    /**
    * Called when something is dragged over a Folder Node
    */
    dragOver : function(dragObject) {
       return true;
    },
    
    /**
     * Determines if a folder name is unique to the folder tree
     * @param string name
     */
    isUniqueFolderName : function(name) {
        uniqueFolder = true;
        var root = SE.tree.getRoot();
        SE.util.cascadeNodes(SE.tree.getRoot(), function(name) {
            if (this.attributes && this.attributes.ieId == "folder") {
                if (this.attributes.text == name) {
                    uniqueFolder = false;
                    return false;
                }
            }
        }, null, [name]);
        return uniqueFolder;
    },

    /**
     * Makes async call to rename folder in focus
     * @param object e Event Object
     */
    submitFolderRename : function(editor, newName, origName) {
        if (SE.tree.folderEditor.disabled) {
        	return;
        }
    	SE.tree.folderEditor.disable();
        //Ignore no change
        if (newName == origName) {
            return true;
        }
        if(SE.folders.isUniqueFolderName(newName, editor.editNode)) {
            overlay(app_strings.LBL_EMAIL_MENU_RENAMING_FOLDER, app_strings.LBL_EMAIL_ONE_MOMENT);
        	node = editor.editNode;
            if (node.data.ieId == "folder") {
                //Sugar Folder
                AjaxObject.startRequest(callbackFolderRename, urlStandard + "&emailUIAction=renameFolder&folderId=" + node.id + "&newFolderName=" + newName);
            }
            else {
                //IMAP folder or POP mailbox
                var nodePath = node.data.mbox.substring(0, node.data.mbox.lastIndexOf(".") + 1);
                AjaxObject.startRequest(callbackFolderRename, urlStandard + "&emailUIAction=renameFolder&ieId=" 
                    + node.data.ieId + "&oldFolderName=" + node.data.mbox + "&newFolderName=" + nodePath + newName);
            }
            return true;
        } else {
            alert(app_strings.LBL_EMAIL_ERROR_DUPE_FOLDER_NAME);
            return false;
        }
    },

    /**
     * makes async call to do a full synchronization of all accounts
     */
    synchronizeAccounts : function() {
        if(confirm(app_strings.LBL_EMAIL_SETTINGS_FULL_SYNC_WARN)) {
            overlayModal(app_strings.LBL_EMAIL_SETTINGS_FULL_SYNC, app_strings.LBL_EMAIL_ONE_MOMENT + "<br>&nbsp;<br>" + app_strings.LBL_EMAIL_COFFEE_BREAK);
            AjaxObject.startRequest(callbackFullSync, urlStandard + '&emailUIAction=synchronizeEmail');
        }
    },

    /**
     * Updates user's folder subscriptsion (Sugar only)
     * @param object SELECT DOM object in focus
     * @param string type of Folder selection
     */
    updateSubscriptions : function() {
        var active = "";

        process = new Array();
        process[0] = document.getElementById('userFolders');
        process[1] = document.getElementById('groupFolders');

        for(p=0; p<2; p++) {
            var select = process[p];

            for(i=0; i<select.options.length; i++) {
                var opt = select.options[i];
                if(opt.selected && opt.value != "") {
                    if(active != "") {
                        active += "::";
                    }
                    active += opt.value;
                }
            }
        }

        AjaxObject.startRequest(callbackFolderSubscriptions, urlStandard + '&emailUIAction=updateSubscriptions&subscriptions=' + active);
    }

};

SE.folders.checkEmail2 = function() {
    AjaxObject.startRequest(callbackCheckEmail2, urlStandard + "&emailUIAction=checkEmail2");
}
////    END FOLDERS OBJECT
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
////    SE.keys
/**
 * Keypress Event capture and processing
 */
SE.keys = {
    overall : function(e) {





        switch(e.charCode) {
            case 119: // "w"
                if(e.ctrlKey || e.altKey) {
                    var focusRegion = SE.innerLayout.regions.center;
                    if(focusRegion.activePanel.closable == true) {
                        focusRegion.remove(focusRegion.activePanel);
                    }
                }
            break;
        }
    }
};
////    END SE.keys
///////////////////////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////////////////////
////    SE.listView
/**
 * ListView object methods and attributes
 */
SE.listView = {
    currentRowId : -1,

    /**
     * Fills the ListView pane with detected messages.
     */
    populateListFrame : function(node, ieId, forceRefresh) {
        SE.innerLayout.selectTab(0);
		YAHOO.util.Connect.abort(AjaxObject.currentRequestObject, null, false);

        Dom.get('_blank').innerHTML = "";
        SE.grid.params['emailUIAction'] = 'getMessageListXML';
        SE.grid.params['mbox'] = node.data.mbox;
        SE.grid.params['ieId'] = ieId;
        forcePreview = true; // loads the preview pane with first item in grid
        //SE.grid.colModel.setHidden(5, true);
        SE.grid.getDataSource().sendRequest(SUGAR.util.paramsToUrl(SE.grid.params),  SE.grid.onDataReturnInitializeTable, SE.grid);
    },

    /**
     * Like populateListFrame(), but specifically for SugarFolders since the API is radically different
     */
    populateListFrameSugarFolder : function(node, folderId, forceRefresh) {
        SE.innerLayout.selectTab(0);
        Dom.get('_blank').innerHTML = "";
        SE.grid.params['emailUIAction'] = 'getMessageListSugarFoldersXML';
        SE.grid.params['ieId'] = node.data.id;
        SE.grid.params['mbox'] = node.data.origText ? node.data.origText : node.data.text;
        /*if (node.data.folder_type != null && node.data.folder_type == 'sent') {
        	SE.grid.colModel.setHidden(5, false);
        } else {
        	SE.grid.colModel.setHidden(5, true);
        }*/
        //SE.grid.getStore().load({params:{start:0, limit:SE.userPrefs.emailSettings.showNumInList}});
        SE.grid.getDataSource().sendRequest(SUGAR.util.paramsToUrl(SE.grid.params),  SE.grid.onDataReturnInitializeTable, SE.grid);
    },

    /**
     * Sets sort order as user preference
     * @param
     */
    saveListViewSortOrder : function(sortBy, focusFolderPassed, ieIdPassed, ieNamePassed) {
        ieId = ieIdPassed;
        ieName = ieNamePassed;
        focusFolder = focusFolderPassed;

        SE.util.clearHiddenFieldValues('emailUIForm');
        var previousSort = document.getElementById('sortBy').value;

        document.getElementById('sortBy').value = sortBy;
        document.getElementById('emailUIAction').value = 'saveListViewSortOrder';
        document.getElementById('focusFolder').value = focusFolder;
        document.getElementById('ieId').value = ieId;

        if(sortBy == previousSort) {
            document.getElementById('reverse').value = '1';
        }

        var formObject = document.getElementById('emailUIForm');
        YAHOO.util.Connect.setForm(formObject);

        AjaxObject.startRequest(callbackListViewSortOrderChange, null);
    },


    /**
     * Enables click/arrow select of grid items which then populate the preview pane.
     */
    selectFirstRow : function() {
        SE.grid.selModel.selectFirstRow();
    },

    selectLastRow : function() {
        SE.grid.selModel.selectRow(SE.grid.dataSource.data.getCount() - 1);
    },

    setEmailListStyles : function() {
    	SE.listView.boldUnreadRows();
    	return;
        var ds = SE.grid.getStore();
        if (SE.grid.getSelections().length == 0) {
            document.getElementById('_blank').innerHTML = '';
        }

        var acctMbox = '';
        if(typeof(ds.baseParams.mbox) != 'undefined') {
            acctMbox = (ds.baseParams.acct) ? ds.baseParams.acct + " " + ds.baseParams.mbox : ds.baseParams.mbox;
            var cm = SE.grid.getColumnModel();
            if (ds.baseParams.mbox == mod_strings.LBL_LIST_FORM_SENT_TITLE) {
                cm.setColumnHeader(4, mod_strings.LBL_LIST_DATE_SENT);
                //SE.grid.render();
            } else if (cm.config[4].header != app_strings.LBL_EMAIL_DATE_RECEIVED){
                cm.setColumnHeader(4, app_strings.LBL_EMAIL_DATE_RECEIVED);
                //SE.grid.render();
            }
        }
        var total = (typeof(ds.totalLength) != "undefined") ? " (" + ds.totalLength +" " + app_strings.LBL_EMAIL_MESSAGES +") " : "";
        SE.listViewLayout.setTitle(acctMbox + total);// + toggleRead + manualFit);


        // 4/20/2007 added to hide overlay after search
        //hideOverlay();
        if (ds.reader.xmlData.getElementsByTagName('UnreadCount').length > 0){
            var unread = ds.reader.xmlData.getElementsByTagName('UnreadCount')[0].childNodes[0].data;
            var node = SE.folders.getNodeFromIeIdAndMailbox(ds.baseParams.ieId, ds.baseParams.mbox);
            if (node) node.data.unseen = unread;
        }
        SE.accounts.renderTree();

        
        // bug 15035 perhaps a heavy handed solution to stopping the loading spinner.
        if(forcePreview && ds.totalCount > 0) {
            SE.detailView.getEmailPreview();
            forcePreview = false;
        }
    },

    /**
     * Removes a row if found via its UID
     */
    removeRowByUid : function(uid) {
        uid = new String(uid);
        uids = uid.split(',');
        var ds = SE.grid.getStore();

        for(j=0; j<uids.length; j++) {
            var theUid = uids[j];
            var r = ds.getById(uids[j]);
            ds.remove(r);
        }
    },

    displaySelectedEmails : function(rows) {
        var dm = SE.grid.getDataModel();
        var uids = '';

        for(i=0; i<rows.length; i++) {
            var rowIndex = rows[i].rowIndex;
            var metadata = dm.data[rowIndex];

            if(uids != "") {
                uids += ",";
            }
            uids += metadata[5];

            // unbold unseen email
            this.unboldRow(rowIndex);
        }

        SE.detailView.populateDetailViewMultiple(uids, metadata[6], metadata[7], metadata[8], false);
    },

    /**
     * exception handler for data load failures
     */
    loadException : function(dataModel, ex, response) {
        //debugger;
    },

    /**
     * Moves email(s) from a folder to another, from IMAP/POP3 to Sugar and vice-versa
     * @param string sourceIeId Email's source I-E id
     * @param string sourceFolder Email's current folder
     * @param destinationIeId Destination I-E id
     * @param destinationFolder Destination folder in format [root::IE::INBOX::etc]
     *
     * @param array emailUids Array of email's UIDs
     */
    moveEmails : function(sourceIeId, sourceFolder, destinationIeId, destinationFolder, emailUids, selectedRows) {
        if(destinationIeId != 'folder' && sourceIeId != destinationIeId) {
            overlay(app_strings.LBL_EMAIL_ERROR_MOVE_TITLE, app_strings.LBL_EMAIL_ERROR_MOVE);
        } else {
            overlay("Moving Email(s)", app_strings.LBL_EMAIL_ONE_MOMENT);
            // remove rows from visibility
            for(row in selectedRows) {
                //SE.grid.getStore().remove(row);
            }

            var baseUrl =    '&sourceIeId=' + sourceIeId +
                            '&sourceFolder=' + sourceFolder +
                            '&destinationIeId=' + destinationIeId +
                            '&destinationFolder=' + destinationFolder;
            var uids = '';

            for(i=0; i<emailUids.length; i++) {
                if(uids != '') {
                    uids += app_strings.LBL_EMAIL_DELIMITER;
                }
                uids += emailUids[i];
            }
            if (destinationIeId == 'folder' && sourceFolder != 'sugar::Emails') {
            	AjaxObject.startRequest(callbackImportOneEmail, urlStandard + '&emailUIAction=moveEmails&emailUids=' + uids + baseUrl);
            } else {
            	AjaxObject.startRequest(callbackMoveEmails, urlStandard + '&emailUIAction=moveEmails&emailUids=' + uids + baseUrl);
            }
        }
    },
    
    /**
     * Unbolds text in the grid view to denote read status
     */
    markRead : function(index, record) {
        // unbold unseen email
        if (typeof (index) == 'number') {
            var rowEl = SUGAR.email2.grid.getTrEl(index);
            rowEl.style.fontWeight = "normal";
            var data = record.getData();
            data['seen'] = 1;
        }
    },

    /**
     * grid row output, bolding unread emails
     */
    boldUnreadRows : function() {
        // bold unread emails
    	var trEl = SE.grid.getFirstTrEl();
    	while(trEl != null) {
    		if(SE.grid.getRecord(trEl).getData().seen == "0")
    			trEl.style.fontWeight = "bold";
    		else
    			trEl.style.fontWeight = "";
    		trEl = SE.grid.getNextTrEl(trEl);
    	}
    },

    /**
     * Show preview for an email if 1 and only 1 is selected
     * ---- all references must be fully qual'd since this gets wrapped by the YUI event handler
     */
    handleRowSelect : function(e) {
        if(e.selectedRows.length == 1) {
            SE.detailView.getEmailPreview();
        }
    },

    handleDrop : function(e, dd, targetId, e2) {
        switch(targetId) {
            case 'htmleditordiv':
                var rows = SE.grid.getSelectedRows();
                if(rows.length > 0) {
                    SE.listView.displaySelectedEmails(rows);
                }
            break;

            default:
                var targetElId = new String(targetId);
                var targetIndex = targetElId.replace('ygtvlabelel',"");
                var targetNode = SE.tree.getNodeByIndex(targetIndex);
                var dm = SE.grid.getDataModel();
                var emailUids = new Array();
                var destinationIeId = targetNode.data.ieId;
                var destinationFolder = SE.util.generateMboxPath(targetNode.data.mbox);


                var rows = SE.grid.getSelectedRows();
                // iterate through dragged rows
                for(i=0; i<rows.length; i++) {
                    //var rowIndex = e.selModel.selectedRows[i].rowIndex;
                    var rowIndex = rows[i].rowIndex;
                    var dataModelRow = dm.data[rowIndex];
                    var sourceIeId = dataModelRow[7];
                    var sourceFolder = dataModelRow[6];
                    emailUids[i] = dataModelRow[5];
                }

                // event wrapped call - need FQ
                overlay(app_strings.LBL_EMAIL_PERFORMING_TASK, app_strings.LBL_EMAIL_ONE_MOMENT);
                SE.listView.moveEmails(sourceIeId, sourceFolder, destinationIeId, destinationFolder, emailUids, e.selModel.selectedRows);
            break;
        }
    },

    /**
     * Hack-around to get double-click and single clicks to work on the grid
     * ---- all references must be fully qual'd since this gets wrapped by the YUI event handler
     */
    handleClick : function(o) {
    	SUGAR.email2.grid.clearTextSelection();
    	SUGAR.email2.listView.currentRow = o.record;
    	SUGAR.email2.listView.currentRowIndex = SUGAR.email2.grid.getRecordIndex(o.record);
    	clearTimeout(SUGAR.email2.detailView.previewTimer);
    	SUGAR.email2.detailView.previewTimer = setTimeout("SUGAR.email2.detailView.getEmailPreview();", 500);
    },

    /**
     * Custom handler for double-click/enter
     * ---- all references must be fully qual'd since this gets wrapped by the YUI event handler
     */
    getEmail : function(e) {
        var rows = SE.grid.getSelectedRows();
    	var row = SE.grid.getRecord(rows[0]).getData();
        
        clearTimeout(SE.detailView.previewTimer);
        document.getElementById("_blank").innerHTML = "";

        if(row.type != "draft") {
            SE.detailView.populateDetailView(row.uid, row.mbox, row.ieId, 'true', SE.innerLayout);
        } else {
            // circumventing yui-ext tab generation, let callback handler build new view
            SE.util.clearHiddenFieldValues('emailUIForm');
            //function(uid, mbox, ieId, setRead, destination) {
            document.getElementById('emailUIAction').value = 'getSingleMessageFromSugar';
            document.getElementById('uid').value = row.uid; // uid;
            document.getElementById('mbox').value = row.mbox; // mbox;
            document.getElementById('ieId').value = row.ieId; // ieId;

            YAHOO.util.Connect.setForm(document.getElementById('emailUIForm'));
            AjaxObject.target = '_blank';
            AjaxObject.startRequest(AjaxObject.detailView.callback.emailDetail, null);
        }
    },

    /**
     * Retrieves a row if found via its UID
     * @param string
     * @return int
     */
    getRowIndexByUid : function(uid) {
        uid = new String(uid);
        uids = uid.split(',');

        for(j=0; j<uids.length; j++) {
            var theUid = uids[j];

            for(i=0; i<SE.grid.getStore().data.length; i++) {
                if(SE.grid.getStore().data[i].id == theUid) {
                    return i;
                }
            }
        }
    },
    
    /**
     * Returns the UID's of the seleted rows
     *
     */
     getUidsFromSelection : function() {
         var rows = SE.grid.getSelectedRows();
         var uids = [];
         /* iterate through available rows JIC a row is deleted - use first available */
         for(var i=0; i<rows.length; i++) {
        	 uids[i] = SE.grid.getRecord(rows[i]).getData().uid;
         }
         return uids;
     }
    
};
////    END SE.listView
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////    SEARCH
SE.search = {
    /**
     * sends search criteria
     * @param reference element search field
     */
    search : function(el) {
        var searchCriteria = new String(el.value);

        if(searchCriteria == '') {
            alert(app_strings.LBL_EMAIL_ERROR_EMPTY);
            return false;
        }

        var safeCriteria = escape(searchCriteria);

        var accountListSearch = document.getElementById('accountListSearch');
        //overlay(app_strings.LBL_EMAIL_SEARCHING,app_strings.LBL_EMAIL_ONE_MOMENT);

        SE.grid.getStore().baseParams['emailUIAction'] = 'search';
        SE.grid.getStore().baseParams['mbox'] = app_strings.LBL_EMAIL_SEARCH_RESULTS_TITLE;
        SE.grid.getStore().baseParams['subject'] = safeCriteria;
        SE.grid.getStore().baseParams['ieId'] = accountListSearch.options[accountListSearch.selectedIndex].value;
        SE.grid.getStore().load({params:{start:0, limit:SE.userPrefs.emailSettings.showNumInList}});
        
    },

    /**
     * sends advanced search criteria
     */
    searchAdvanced : function() {
        var formObject = document.getElementById('advancedSearchForm');
        var search = false;

        for(i=0; i<formObject.elements.length; i++) {
            if(formObject.elements[i].type != 'button' && formObject.elements[i].value != "") {
                search = true;
            }
            if(formObject.elements[i].type == 'text') {
                SE.grid.params[formObject.elements[i].name] = formObject.elements[i].value;
            }
        }

        if(search) {
        	SE.grid.params['emailUIAction'] = 'searchAdvanced';
        	SE.grid.params['mbox'] = app_strings.LBL_EMAIL_SEARCH_RESULTS_TITLE;
        	var accountListSearch = document.getElementById('accountListSearch');
        	SE.grid.params['ieId'] = accountListSearch.options[accountListSearch.selectedIndex].value;
        	SE.grid.getDataSource().sendRequest(SUGAR.util.paramsToUrl(SE.grid.params),  SE.grid.onDataReturnInitializeTable, SE.grid);
        } else {
            alert(app_strings.LBL_EMAIL_ERROR_EMPTY);
        }
    },

    /**
     * clears adv search form fields
     */
    searchClearAdvanced : function() {
        var form = document.getElementById('advancedSearchForm');

        for(i=0; i<form.elements.length; i++) {
            if(form.elements[i].type != 'button') {
                form.elements[i].value = '';
            }
        }
    },
    
    updateSearchTab : function() {
    	var accountListSearch = document.getElementById("accountListSearch");
    	if (accountListSearch.isLoaded == null || accountListSearch.isLoaded == false) {
    		// load data
        	AjaxObject.startRequest(callbackRebuildShowAccountListForSearch, urlStandard + '&emailUIAction=rebuildShowAccountForSearch');
    		accountListSearch.isLoaded = true;
    	}
    },
    
    accountListSearchChange : function(accountListSearch) {
    	var searchIndex = accountListSearch.selectedIndex;
    	if (accountListSearch.selectedIndex == 0) {
    		document.getElementById("advancedSearchButton").disabled = true;
    	} else {
    		
    		if (accountListSearch.options[searchIndex].protocol == 'pop3') {
    			document.getElementById('searchBodyDiv').style.display = "none";
    		} else {
    			document.getElementById('searchBodyDiv').style.display = "";	
    		} //else
    		document.getElementById("advancedSearchButton").disabled = false;
    	}
    },
    
    markSearchAccountListDirty : function() {
    	var accountListSearch = document.getElementById("accountListSearch");
    	accountListSearch.isLoaded = false;
    }
};
////    END SE.search
//////////////////////////////////////////////////////////////////////////////


//////////////////////////////////////////////////////////////////////////////
////    SE.settings
SE.settings = {
    /******************************************************************************
     * USER SIGNATURES calls stolen from Users module
     *****************************************************************************/
    createSignature : function(record, the_user_id) {
        var URL = "index.php?module=Users&action=PopupSignature&sugar_body_only=true";
        if(record != "") {
            URL += "&record="+record;
        }
        if(the_user_id != "") {
            URL += "&the_user_id="+the_user_id;
        }
        var windowName = 'email_signature';
        var windowFeatures = 'width=800,height=600,resizable=1,scrollbars=1';

        var win = window.open(URL, windowName, windowFeatures);
        if(win && win.focus) {
            // put the focus on the popup if the browser supports the focus() method
            win.focus();
        }
    },

    deleteSignature : function() {
        if(confirm(app_strings.LBL_EMAIL_CONFIRM_DELETE_SIGNATURE)) {
            overlay(app_strings.LBL_EMAIL_IE_DELETE_SIGNATURE, app_strings.LBL_EMAIL_ONE_MOMENT);
    		var singature_id = document.getElementById('signature_id').value;
        	AjaxObject.startRequest(callbackDeleteSignature, urlStandard + '&emailUIAction=deleteSignature&id=' + singature_id);
        } // if
    },
    
    saveOptionsGeneral :  function(displayMessage) {
        var formObject = document.getElementById('formEmailSettingsGeneral');
        YAHOO.util.Connect.setForm(formObject);
        SE.composeLayout.emailTemplates = null;

        AjaxObject.target = 'frameFlex';
        AjaxObject.startRequest(callbackSettings, urlStandard + '&emailUIAction=saveSettingsGeneral');

        if(displayMessage)
            alert(app_strings.LBL_EMAIL_SETTINGS_SAVED);
    },

    /**
     * switches UI to selected view
     */
    changeView : function(view) {
    	SE.e2Layout.setPreviewPanel(view == 'rows'); 
    },

    /**
     * Shows settings container screen
     */
    showSettings : function() {
        if(!SE.settings.settingsDialog) {
    		var dlg = SE.settings.settingsDialog = new YAHOO.widget.Dialog("settingsDialog", {
            	modal:true,
            	visible:false,
            	//fixedcenter:true,
            	draggable: false,
            	width:"800px",
                height:"560px",
				constraintoviewport: true
            });
        	dlg.setHeader(app_strings.LBL_EMAIL_SETTINGS);
        	dlg.setBody('<div id="settingsTabDiv"/>');
        	dlg.beforeRenderEvent.subscribe(function() { 
        		var dd = new YAHOO.util.DDProxy(dlg.element); 
        		dd.setHandleElId(dlg.header); 
        		dd.on('endDragEvent', function() { 
        			dlg.show(); 
        		}); 
        	}, dlg, true); 
        	dlg.render();
        	var tp = SE.settings.settingsTabs = new YAHOO.widget.TabView("settingsTabDiv");
			var tabContent = Dom.get("tab_general");
        	tp.addTab(new YAHOO.widget.Tab({
				label: app_strings.LBL_EMAIL_SETTINGS_GENERAL,
				scroll : true,
				content : tabContent.innerHTML,
				id : "generalSettings",
				active : true
			}));
        	tabContent.parentNode.removeChild(tabContent);
        	tabContent = Dom.get("tab_accounts");
        	var accountTab = new YAHOO.widget.Tab({
				label: app_strings.LBL_EMAIL_SETTINGS_ACCOUNTS,
				scroll : true,
				content : tabContent.innerHTML,
				id : "accountSettings"
			});
        	accountTab.on("activeChange", function(o){ if (o.newValue) SE.accounts.lazyLoad();});
        	tp.addTab(accountTab);
        	tabContent.parentNode.removeChild(tabContent);
        	tabContent = Dom.get("tab_folders");
        	var foldersTab = new YAHOO.widget.Tab({
				label: app_strings.LBL_EMAIL_SETTINGS_FOLDERS,
				scroll : true,
				content : tabContent.innerHTML,
				id : "folderSettings"
			})
        	foldersTab.on("activeChange", function(o){ if (o.newValue) SE.folders.lazyLoadSettings();});
        	tp.addTab(foldersTab);
        	tabContent.parentNode.removeChild(tabContent);
			tp.appendTo(dlg.body);
        }
        SE.settings.settingsDialog.show();
    },

    lazyLoadRules : function() {
        if(false/*!SE.settings.rules*/) {
            AjaxObject.startRequest(callbackLoadRules, urlStandard + "&emailUIAction=loadRulesForSettings");
        }

    },

    toggleFullScreen : function(el) {
        var h = document.getElementById('header');

        if(h != null) {
            if(el.checked == false) {
                h.style.display = '';
            } else {
                h.style.display = 'none';
            }
        } else {
            alert(SUGAR.language.get("app_strings", "ERR_NO_HEADER_ID"));
        }
        SE.autoSetLayout();
    },

    toggleFullScreenQuick : function() {
        var h = document.getElementById('header');

        if(h != null) {
            if(h.style.display == 'none') {
                h.style.display = '';
            } else {
                h.style.display = 'none';
            }
        } else {
            alert(SUGAR.language.get("app_strings", "ERR_NO_HEADER_ID"));
        }
        SE.autoSetLayout();
    }
};
////    END SE.settings
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
////    SE.util
SE.util = {
    /**
     * Cleans serialized UID lists of duplicates
     * @param string
     * @return string
     */
    cleanUids : function(str) {
        var seen = new Object();
        var clean = "";
        var arr = new String(str).split(",");

        for(var i=0; i<arr.length; i++) {
            if(seen[arr[i]]) {
                continue;
            }

            clean += (clean != "") ? "," : "";
            clean += arr[i];
            seen[arr[i]] = true;
        }

        return clean;
    },

    /**
     * Clears hidden field values
     * @param string id ID of form element to clear
     */
    clearHiddenFieldValues : function(id) {
        form = document.getElementById(id);

        for(i=0; i<form.elements.length; i++) {
            if(form.elements[i].type == 'hidden') {
                var e = form.elements[i];
                if(e.name != 'action' && e.name != 'module' && e.name != 'to_pdf') {
                    e.value = '';
                }
            }
        }
    },

    /**
     * Reduces a SELECT drop-down to 0 items to prepare for new ones
     */
    emptySelectOptions : function(el) {
        if(el) {
            for(i=el.childNodes.length - 1; i >= 0; i--) {
                if(el.childNodes[i]) {
                    el.removeChild(el.childNodes[i]);
                }
            }
        }
    },

    /**
     * Returns the MBOX path in the manner php_imap expects:
     * ie: INBOX.DEBUG.test
     * @param string str Current serialized value, Home.personal.test.INBOX.DEBUG.test
     */
    generateMboxPath : function(str) {
        var ex = str.split("::");

        /* we have a serialized MBOX path */
        if(ex.length > 1) {
            var start = false;
            var ret = '';
            for(var i=0; i<ex.length; i++) {
                if(ex[i] == 'INBOX') {
                    start = true;
                }

                if(start == true) {
                    if(ret != "") {
                        ret += ".";
                    }
                    ret += ex[i];
                }
            }
        } else {
            /* we have a Sugar folder GUID - do nothing */
            return str;
        }

        return ret;
    },

    /**
     * returns a SUGAR GUID by navigating the DOM tree a few moves backwards
     * @param HTMLElement el
     * @return string GUID of found element or empty on failure
     */
    getGuidFromElement : function(el) {
        var GUID = '';
        var iterations = 4;
        var passedEl = el;

        // upwards
        for(var i=0; i<iterations; i++) {
            if(el) {
                if(el.id.match(SE.reGUID)) {
                    return el.id;
                } else {
                    el = el.parentNode;
                }
            }
        }

        return GUID;
    },

    /**
     * Returns the ID value for the current in-focus, active panel (in the innerLayout, not complexLayout)
     * @return string
     */
    getPanelId : function() {
        return SE.innerLayout.get("activeTab").id ? SE.innerLayout.get("activeTab").id : "Preview";
    },
    
    /**
     * wrapper to handle weirdness with IE
     * @param string instanceId
     * @return tinyMCE Controller object
     */
    getTiny : function(instanceId) {
        if(instanceId == '') {



            return null;
        }

        var t = tinyMCE.getInstanceById(instanceId);

        if(this.isIe()) {
            this.sleep(200);
            YAHOO.util.Event.onContentReady(instanceId, function(t) { return t; });
        }
        return t;
    },

    /**
     * Simple check for MSIE browser
     * @return bool
     */
    isIe : function() {
        var nav = new String(navigator.appVersion);
        if(nav.match(/MSIE/)) {
            return true;
        }
        return false;
    },

    /**
     * Recursively removes an element from the DOM
     * @param HTMLElement
     */
    removeElementRecursive : function(el) {
        this.emptySelectOptions(el);
    },
    
    /**
     * Fakes a sleep
     * @param int
     */
    sleep : function(secs) {
        setTimeout("void(0);", secs);
    },
    
    /**
     * Converts a <select> element to an Ext.form.combobox
     */
     convertSelect : function(select) {
       alert('in convertSelect');
       if (typeof(select) == "string") {
           select = document.getElementById(select);
       }
     },
     
     findChildNode : function (parent, property, value) {
    	 for (i in parent.children) {
    		 var child = parent.children[i];
    		 if (child.data[property] && child.data[property] == value || child[property] && child[property] == value)
    			 return child;
    		 var searchChild = SE.util.findChildNode(child, property, value);
    		 if (searchChild) 
    			 return searchChild;
    	 }
    	 return false;
     },
     
     cascadeNodes : function (parent, fn, scope, args) {
    	 for (i in parent.children) {
    		 var child = parent.children[i];
    		 var s = scope ? scope : child;
    		 var a = args ? args : child;
        	 fn.call(s, a);
    		 SE.util.cascadeNodes(child, fn, scope, args);
    	 }
     }
};

})();
////    END UTIL
///////////////////////////////////////////////////////////////////////////////


/******************************************************************************
 * UTILITIES
 *****************************************************************************/
/**
 * Shows overlay progress message
 */
function overlayModal(title, body) {
    overlay(title, body);
}
function overlay(reqtitle, body, type, additconfig) {
    var config = { };
    if (typeof(additconfig) == "object") {
        var config = additconfig;
    }
    config.type = type;
    config.title = reqtitle;
    config.msg = body;
    YAHOO.SUGAR.MessageBox.show(config);
};

function hideOverlay() {
	YAHOO.SUGAR.MessageBox.hide();
};

function removeHiddenNodes(nodes, grid) {
    var el;
	for(var i = nodes.length - 1; i > -1; i--) {
        el = grid ? grid.getTrEl(nodes[i]) : nodes[i];
    	if (YAHOO.util.Dom.hasClass(el, 'rowStylenone')) {
    		nodes.splice(i,1);
       }
    }
}

function strpad(val) {
    return (!isNaN(val) && val.toString().length==1)?"0"+val:val;
};

function refreshTodos() {
    SUGAR.email2.util.clearHiddenFieldValues('emailUIForm');
    AjaxObject.target = 'todo';
    AjaxObject.startRequest(callback, urlStandard + '&emailUIAction=refreshTodos');
};

/******************************************************************************
 * MUST STAY IN GLOBAL NAMESPACE
 *****************************************************************************/
function refresh_signature_list(signature_id, signature_name) {
    var field=document.getElementById('signature_id');
    var bfound=0;
    for (var i=0; i < field.options.length; i++) {
            if (field.options[i].value == signature_id) {
                if (field.options[i].selected==false) {
                    field.options[i].selected=true;
                }
                bfound=1;
            }
    }
    //add item to selection list.
    if (bfound == 0) {
        var newElement=document.createElement('option');
        newElement.text=signature_name;
        newElement.value=signature_id;
        field.options.add(newElement);
        newElement.selected=true;
    }

    //enable the edit button.
    var field1=document.getElementById('edit_sig');
    field1.style.visibility="visible";
    var deleteButt = document.getElementById('delete_sig');
    deleteButt.style.visibility="visible";
};

function setDefaultSigId(id) {
    var checkbox = document.getElementById("signature_default");
    var default_sig = document.getElementById("signatureDefault");

    if(checkbox.checked) {
        default_sig.value = id;
    } else {
        default_sig.value = "";
    }
};

function setSigEditButtonVisibility() {
    var field = document.getElementById('signature_id');
    var editButt = document.getElementById('edit_sig');
    var deleteButt = document.getElementById('delete_sig');
    if(field.value != '') {
        editButt.style.visibility = "visible";
        deleteButt.style.visibility = "visible";
    } else {
        editButt.style.visibility = "hidden";
        deleteButt.style.visibility = "hidden";
    }
}

// The reason to add this function because the original function in the gugar_3.js doesn't work in IE -7
// document.forms[form_name].elements is always null - Don't know why 
function set_return(popup_reply_data)
{
	from_popup_return = true;
	var form_name = popup_reply_data.form_name;
	var name_to_value_array = popup_reply_data.name_to_value_array;
	for (var the_key in name_to_value_array)
	{
		if(the_key == 'toJSON')
		{
			/* just ignore */
		}
		else
		{
			var displayValue=name_to_value_array[the_key].replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"');;
			// begin andopes change: support for enum fields (SELECT)
			if(document.getElementById(the_key).tagName == 'SELECT') {
				var selectField = document.getElementById(the_key);
				for(var i = 0; i < selectField.options.length; i++) {
					if(selectField.options[i].text == displayValue) {
						selectField.options[i].selected = true;
						break;
					}
				}
			} else {
				document.getElementById(the_key).value = displayValue;
			}
			// end andopes change: support for enum fields (SELECT)
		}
	}
}
