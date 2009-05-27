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

if (typeof console == "undefined") 
	console = { log: function(o) {alert(o)} };

var AjaxObject = {
	ret : '',
	currentRequestObject : null,
	//timeout : 30000, // 30 second timeout default
	timeout : 9999999999, // 30 second timeout default
	forceAbort : false,
	trail : new Array(),

	/**
	 */
	_reset : function() {
		this.timeout = 30000;
		this.forceAbort = false;
	},

	folderRenameCleanup : function() {
		SUGAR.email2.folders.setSugarFolders();
	},

	fullSyncCleanup : function(o) {
		this.folders.checkMailCleanup(o);
		SUGAR.email2.settings.settingsDialog.hide();
	},

	/**
	 */
	composeCache : function(o) {
		var idx = SUGAR.email2.composeLayout.currentInstanceId; // post instance increment
		// get email templates and user signatures
		var ret = JSON.parse(o.responseText);

		SUGAR.email2.composeLayout.emailTemplates = ret.emailTemplates;
		SUGAR.email2.composeLayout.signatures = ret.signatures;
		SUGAR.email2.composeLayout.fromAccounts = ret.fromAccounts;

		SUGAR.email2.composeLayout.setComposeOptions(idx);
	},


	handleDeleteSignature : function(o) {
		hideOverlay();
		var ret = JSON.parse(o.responseText);
		SUGAR.email2.composeLayout.signatures = ret.signatures;
    	var field = document.getElementById('signature_id');
		SUGAR.email2.util.emptySelectOptions(field);

		for(var i in ret.signatures) { // iterate through assoc array
			var opt = new Option(ret.signatures[i], i);
			field.options.add(opt);
		}
		setSigEditButtonVisibility();
	},
	
	/**
	 */
	handleDeleteReturn : function(o) {
		// force refresh ListView
		hideOverlay();
		if(document.getElementById('focusEmailMbox')) {
			YAHOO.namespace('frameFolders').selectednode = SUGAR.email2.folders.getNodeFromMboxPath(document.getElementById('focusEmailMbox').innerHTML);
		}

		// need to display success message before calling next async call?
		document.getElementById(this.target).innerHTML = o.responseText;
	},

	/**
	 */
    handleFailure : function(o) {
		// Failure handler
		overlay('Exception occurred...', o.statusText, 'alert');



	},

	handleReplyForward : function(o) {
		var a = JSON.parse(o.responseText);
		globalA = a;
		var idx = SUGAR.email2.composeLayout.currentInstanceId;

		document.getElementById('email_id' + idx).value = a.uid;
		document.getElementById('emailSubject' + idx).value = a.name;
		document.getElementById('addressTo' + idx).value = a.from;

		if(a.cc) {
			document.getElementById('addressCC' + idx).value = a.cc;
		}

		if(a.type) {
			document.getElementById('type' + idx).value = a.type;
		}

		// apply attachment values
		SUGAR.email2.composeLayout.loadAttachments(a.attachments);

		setTimeout("callbackReplyForward.finish(globalA);", 500);
	},

	handleReplyForwardForDraft : function(o) {
		var a = JSON.parse(o.responseText);
		globalA = a;
		var idx = SUGAR.email2.composeLayout.currentInstanceId;

		document.getElementById('email_id' + idx).value = a.uid;
		document.getElementById('emailSubject' + idx).value = a.name;
		document.getElementById('addressTo' + idx).value = a.to;

		if(a.cc) {
			document.getElementById('addressCC' + idx).value = a.cc;
		}

		if(a.bcc) {
			document.getElementById('addressBCC' + idx).value = a.bcc;
		}

		
		if(a.type) {
			document.getElementById('type' + idx).value = a.type;
		}

		// apply attachment values
		SUGAR.email2.composeLayout.loadAttachments(a.attachments);

		setTimeout("callbackReplyForward.finish(globalA);", 500);
	},
		
	/**
	 */
	handleSuccess : function(o) {
		document.getElementById(this.target).innerHTML = o.responseText;
		hideOverlay();
	},

	/**
	 */
	ieDeleteSuccess : function(o) {
		hideOverlay();
		SUGAR.email2.accounts.addNewAccount();

		var ret = JSON.parse(o.responseText);
		var ms = document.getElementById('ieAccountList');
		var ms2 = document.getElementById('ieAccountListShow');

		for(i=0; i<ms.options.length; i++) {
			if(ret.id == ms.options[i].value) {
				document.ieSelect.ieId.options[i] = null;
			}
		}

		for(j=0; j<ms2.options.length; j++) {
			if(ret.id == ms2.options[j].value) {
				ms2.options[j] = null;
			}
		}

		SUGAR.email2.accounts.rebuildAccountList();
		alert(app_strings.LBL_EMAIL_IE_DELETE_SUCCESSFUL);
		SUGAR.email2.accounts.rebuildFolderList();
		SUGAR.email2.search.markSearchAccountListDirty();
		
	},

	/**
	 */
	ieSaveSuccess : function(o) {
		document.getElementById('saveButton').disabled = false;
		var a = JSON.parse(o.responseText);
		if (a) {
			if(a.error) {
				overlay(app_strings.LBL_EMAIL_ERROR_DESC, app_strings.LBL_EMAIL_ERROR_CHECK_IE_SETTINGS, 'alert');
				SUGAR.email2.accounts.ieAccountError(SUGAR.email2.accounts.errorStyle);
			} else {
				resp = JSON.parse(o.responseText);
				SUGAR.email2.search.markSearchAccountListDirty();
				SUGAR.email2.accounts.fillIeAccount(o.responseText);
				SUGAR.email2.accounts.focusOrCreateIeEl(o.responseText);
				SUGAR.email2.folders.startEmailCheckOneAccount(resp.id, true);
			}
		} else {
		     hideOverlay();
		     overlay(app_strings.LBL_EMAIL_ERROR_DESC, app_strings.LBL_EMAIL_ERROR_SAVE_ACCOUNT, 'alert'); 
		}
		
	},

	/**
	 */
	loadAttachments : function(o) {
		var result = JSON.parse(o.responseText);

		SUGAR.email2.composeLayout.loadAttachments(result);
	},

	/**
	 */
	loadSignature : function(o) {
		var ret = JSON.parse(o.responseText);
		SUGAR.email2.signatures[ret.id] = ret.signature_html;
		SUGAR.email2.composeLayout.setSignature(SUGAR.email2.signatures.targetInstance);
	},

	/**
	 * Follow up to mark email read|unread|flagged
	 */
	markEmailCleanup : function(o) {
		var ret = JSON.parse(o.responseText);
		if (!ret['status']) {
        	hideOverlay();
			overlay(app_strings.LBL_EMAIL_ERROR_DESC, ret['message'], 'alert');			
		} else {
			SUGAR.email2.contextMenus.markEmailCleanup();
		} // else
	},

	/**
	 */
	rebuildShowFolders : function(o) {
		var t = JSON.parse(o.responseText);
		var show = document.getElementById('ieAccountListShow');

		SUGAR.email2.util.emptySelectOptions(show);

		for(i=0; i<t.length; i++) { // iterate through assoc array
			var opt = new Option(t[i].text, t[i].value, t[i].selected);
			opt.selected = t[i].selected;
			show.options.add(opt);
		}

		SUGAR.email2.search.markSearchAccountListDirty();
		SUGAR.email2.accounts.renderTree();
	},

	/**
	 */
	rebuildShowFoldersForSearch : function(o) {
		var t = JSON.parse(o.responseText);
		var show = document.getElementById('accountListSearch');

		SUGAR.email2.util.emptySelectOptions(show);

		for(i=0; i<t.length; i++) { // iterate through assoc array
			var opt = new Option(t[i].text, t[i].value);
			opt.protocol = t[i].protocol;
			show.options.add(opt);
		}


		SUGAR.email2.search.accountListSearchChange(show);
	},

	/**
	 */
	saveListViewSortOrderPart2 : function() {
		// create the JSON string the func expects
		focusFolderPath = '[ "Home", "' + ieName + '"';

		var f = new String(focusFolder);
		var fEx = f.split('.');

		for(i=0; i<fEx.length; i++) {
			focusFolderPath += ', "' + fEx[i] +'"'
		}

		focusFolderPath += ']';

		YAHOO.namespace('frameFolders').selectednode = SUGAR.email2.folders.getNodeFromMboxPath(focusFolderPath);
		SUGAR.email2.listView.populateListFrame(YAHOO.namespace('frameFolders').selectednode, ieId, 'true');
	},

	/**
	 *
	 */
	sendEmailCleanUp : function(o) {
		hideOverlay();
		var ret = JSON.parse(o.responseText);
		if (ret) {
		  SUGAR.email2.composeLayout.forceCloseCompose(ret.composeLayoutId);
		  //SUGAR.email2.addressBook.showContactMatches(ret.possibleMatches);
		} else if (o.responseText) {
		  overlay(app_strings.LBL_EMAIL_ERROR_GENERAL_TITLE, o.responseText, 'alert');
		}
		SUGAR.email2.grid.getDataSource().sendRequest(SUGAR.util.paramsToUrl(SUGAR.email2.grid.params));
		//Disabled while address book is disabled
		
	},

	/**
	 */
	settingsFolderRefresh : function(o) {
		//SUGAR.email2.accounts.rebuildFolderList(); // refresh frameFolder
		var ret = JSON.parse(o.responseText);
		var user = document.getElementById('userFolders');
		var grp = document.getElementById('groupFolders');
		var grpAdd = document.getElementById('groupFoldersAdd');
		var editGroupFolderList = document.getElementById('editGroupFolderList');
		if (document.getElementById('groupFolderAddName') != null) {
			document.getElementById('groupFolderAddName').value = '';
		} // if
		if (document.getElementById('addNewFolders') != null) {
			document.getElementById('addNewFolders').style.display = '';
		}
		if (document.getElementById('saveGroupFolder') != null) {
			document.getElementById('saveGroupFolder').style.display = 'none';
		}
		if (document.getElementById('cancelEditGroupFolder') != null) {
			document.getElementById('cancelEditGroupFolder').style.display = 'none';
		} // if

		SUGAR.email2.util.emptySelectOptions(user);
		SUGAR.email2.util.emptySelectOptions(grp);
		SUGAR.email2.util.emptySelectOptions(grpAdd);
		SUGAR.email2.util.emptySelectOptions(editGroupFolderList);

		for(i=0; i<ret.userFolders.length; i++) {
			var display = ret.userFolders[i].name;
			var value = ret.userFolders[i].id;
			var selected = (ret.userFolders[i].selected != "") ? true : false;
			var opt = new Option(display, value, selected);
			opt.selected = selected;
			user.options.add(opt);
		}

		for(i=0; i<ret.groupFolders.length; i++) {
			var display = ret.groupFolders[i].name;
			var value = ret.groupFolders[i].id;
			var selected = (ret.groupFolders[i].selected != "") ? true : false;
			var opt = new Option(display, value, selected);
			var optAdd = new Option(display, value, selected);
			opt.selected = selected;
			grp.options.add(opt);

			if(grpAdd) {
				grpAdd.options.add(new Option(display, value));
			}
			if (editGroupFolderList) {
				editGroupFolderList.options.add(new Option(display, value));
			}
		}
	},

	/**
	 */
	startRequest : function(callback, args, forceAbort) {
		if(this.currentRequestObject != null) {
			if(this.forceAbort == true) {
				YAHOO.util.Connect.abort(this.currentRequestObject, null, false);
			}
		}



		this.currentRequestObject = YAHOO.util.Connect.asyncRequest('POST', "./index.php", callback, args);
		this._reset();
	},
	
	requestInProgress : function() {
		return (YAHOO.util.Connect.isCallInProgress(this.currentRequestObject));
	},

	/**
	 */
	updateFolderSubscriptions : function() {
		SUGAR.email2.folders.lazyLoadSettings(); // refresh view in Settings overlay
		SUGAR.email2.folders.setSugarFolders(1000);// refresh view in TreeView
	},

	/**
	 */
	updateFrameFolder : function() {
		SUGAR.email2.folders.checkEmailAccounts();
	},

	/**
	 */
	updateUserPrefs : function(o) {
		SUGAR.email2.userPrefs = JSON.parse(o.responseText);



		SUGAR.email2.folders.startCheckTimer(); // starts the auto-check interval
	},

	/**
	 */
	uploadAttachmentSuccessful : function(o) {
		// clear out field
		document.getElementById('email_attachment').value = '';

		var ret = JSON.parse(o.responseText);
		var idx = SUGAR.email2.composeLayout.currentInstanceId;
		var overall = document.getElementById('addedFiles' + idx);
		var index = overall.childNodes.length;
		var out = 
			"<div id='email_attachment_bucket" + idx + index + "'>" +
				// remove button	
				"<img src='index.php?entryPoint=getImage&themeName=" + SUGAR.themes.theme_name + "&imageName=minus.gif' " +
					"style='cursor:pointer' align='absmiddle' onclick='SUGAR.email2.composeLayout.deleteUploadAttachment(\"" + 
					idx + index + "\",\"" + ret.guid + ret.name + "\");'/>" +
				// file icon
				"<img src='index.php?entryPoint=getImage&themeName=" + SUGAR.themes.theme_name + "&imageName=attachment.gif' " +
					"id='email_attachmentImage'" + idx + index + "align='absmiddle' />" +
				// hidden id field
				"<input type='hidden' value='" + ret.guid + ret.name + "' name='email_attachment" + index + "' id='email_attachment" + idx + index + "' />" +
				// file name
				((ret.nameForDisplay != null) ? ret.nameForDisplay + "&nbsp;" : ret.name + "&nbsp;") + 
				"<br/>" +
			"</div>";
		overall.innerHTML += out;
		if(SUGAR.email2.util.isIe()) {
			document.getElementById('addedFiles' + idx).innerHTML = document.getElementById('addedFiles' + idx).innerHTML;
		}

		// hide popup
		SUGAR.email2.addFileDialog.hide();
		// focus attachments
		SUGAR.email2.composeLayout.showAttachmentPanel(idx);
	}
};


///////////////////////////////////////////////////////////////////////////
////	PER MODULE CALLBACK OBJECTS
AjaxObject.accounts = {
	saveOutboundCleanup : function(o) {
		var ret = JSON.parse(o.responseText);

		SUGAR.mailers = ret.mailers;
		SUGAR.email2.accounts.rebuildMailerOptions();
		SUGAR.email2.accounts.outboundDialog.hide();

		/*// clear out form
		var form = document.getElementById('outboundEmailForm');

		for(i=0; i<form.elements.length; i++) {
			if(form.elements[i].name == 'mail_smtpport') {
				form.elements[i].value = 25;
			} else if(form.elements[i].type != 'button') {
				form.elements[i].value = '';
			} else if(form.elements[i].type == 'checkbox') {
				form.elements[i].checked = false;
			}
		}*/

		// preselect new SMTP server
		var sel = document.forms['ieAccount'].elements['outbound_email'];

		for(i=0; i<sel.options.length; i++) {
			if(sel.options[i].value == ret.newId) {
				sel.options[i].selected = true;
				return;
			}
		}
	},

	callbackDeleteOutbound : {
		success	: function(o) {
			var ret = JSON.parse(o.responseText);
			hideOverlay();
			SUGAR.mailers = ret;
			SUGAR.email2.accounts.rebuildMailerOptions();
		},
		
		failure	: AjaxObject.handleFailure,
		timeout	: AjaxObject.timeout,
		scope	: AjaxObject
	},
	
	callbackEditOutbound : {
		success	: function(o) {
			var ret = JSON.parse(o.responseText);
			// show overlay
			SUGAR.email2.accounts.showAddSmtp();

			// fill values
			document.getElementById("mail_id").value = ret.id;
			document.getElementById("mail_sendtype").value = ret.mail_sendtype;
			document.getElementById("mail_name").value = ret.name;
			document.getElementById("mail_smtpserver").value = ret.mail_smtpserver;
			document.getElementById("mail_smtpport").value = ret.mail_smtpport;
			document.getElementById("mail_smtpuser").value = ret.mail_smtpuser;
			document.getElementById("mail_smtppass").value = ret.mail_smtppass;
			document.getElementById("mail_smtpauth_req").checked = (ret.mail_smtpauth_req == 1) ? true : false;
			document.getElementById("mail_smtpssl").options[ret.mail_smtpssl].selected = true;
		},
		failure	: AjaxObject.handleFailure,
		timeout	: AjaxObject.timeout,
		scope	: AjaxObject
	},
	callbackDeleteOutbound : {
		success	: function(o) {
			var ret = JSON.parse(o.responseText);
			hideOverlay();
			SUGAR.mailers = ret;
			SUGAR.email2.accounts.rebuildMailerOptions();
		},
		
		failure	: AjaxObject.handleFailure,
		timeout	: AjaxObject.timeout,
		scope	: AjaxObject
	},
		
	callbackCheckMailProgress : {
	   success : function(o) {
	       if (typeof(SUGAR.email2.accounts.totalMsgCount) == "undefined") {
	           SUGAR.email2.accounts.totalMsgCount = -1;
	       }
	       
	       //Check for server timeout / errors
	       var ret = JSON.parse(o.responseText);
	       var done = false;
		   
	       if (typeof(o.responseText) == 'undefined' || o.responseText == "" || ret == false) {
	           hideOverlay();
	           overlay(app_strings.LBL_EMAIL_ERROR_DESC, app_strings.LBL_EMAIL_ERROR_TIMEOUT, 'alert');
	           SUGAR.email2.accounts.totalMsgCount = -1;
               //SUGAR.email2.folders.rebuildFolders();
               done = true;
	       } 
	       
	       var currIeId = ret['ieid'];
	       
	      
	       var serverCount = ret.count;
	       
	       if (ret['status'] == 'done') {
	           for(i=0; i < SUGAR.email2.accounts.ieIds.length; i++) {
	               if (i == SUGAR.email2.accounts.ieIds.length - 1) {
	                   //We are all done
	                   done = true;
	                   break;
	               } else if (SUGAR.email2.accounts.ieIds[i] == currIeId) {
	                   //Go to next account
	                   currIeId = SUGAR.email2.accounts.ieIds[i+1];
	                   ret.count = 0;
	                   SUGAR.email2.accounts.totalMsgCount = -1;
	                   break;
	               }
	           }
	       } 
	       else if (ret.mbox && ret.totalcount && ret.count) {
	           SUGAR.email2.accounts.totalMsgCount = ret.totalcount;
	           if (ret.count >= ret.totalcount) {
	               serverCount = 0;
	           }
	       } else if (SUGAR.email2.accounts.totalMsgCount < 0 && ret.totalcount) {
	           SUGAR.email2.accounts.totalMsgCount = ret.totalcount;
	       } else {
		       hideOverlay();
               overlay(app_strings.LBL_EMAIL_ERROR_DESC, app_strings.LBL_EMAIL_ERROR_TIMEOUT, 'alert');
               SUGAR.email2.accounts.totalMsgCount = -1;
               done = true;
		   }
	       
	       if (done) {
	           SUGAR.email2.accounts.totalMsgCount = -1;
	           hideOverlay();
	           SUGAR.email2.folders.rebuildFolders();
	           SUGAR.email2.grid.getDataSource().sendRequest(SUGAR.util.paramsToUrl(SUGAR.email2.grid.params));
	       } else if (SUGAR.email2.accounts.totalMsgCount < 0) {
               YAHOO.SUGAR.MessageBox.updateProgress(0, "Checking Account " + (i + 2) + " of " + SUGAR.email2.accounts.ieIds.length);
               AjaxObject.startRequest(AjaxObject.accounts.callbackCheckMailProgress, urlStandard + 
                                '&emailUIAction=checkEmailProgress&ieId=' + currIeId + "&currentCount=0&synch=" + ret.synch);
           } else {
               YAHOO.SUGAR.MessageBox.updateProgress((ret.count / SUGAR.email2.accounts.totalMsgCount) * 100,
                   app_strings.LBL_EMAIL_DOWNLOAD_STATUS.replace(/\[\[count\]\]/, ret.count).replace(/\[\[total\]\]/, SUGAR.email2.accounts.totalMsgCount));
	           AjaxObject.startRequest(AjaxObject.accounts.callbackCheckMailProgress, urlStandard + 
                   '&emailUIAction=checkEmailProgress&ieId=' + currIeId + "&currentCount=" + serverCount + 
                   '&mbox=' + ret.mbox + '&synch=' + ret.synch);
	       }
	   },
	   failure : AjaxObject.handleFailure,
       timeout : AjaxObject.timeout,
       scope   : AjaxObject
	}
};

AjaxObject.addressBook = {
	/**
	 * displays search results for a user querying on what contacts, users, leads, prospects they have access to
	 */
	displaySearchResults : function(o) {
		// prep header
		var header = {
			tag : 'table',
			id : 'peopleTable',
			cellpadding : 0,
			cellspacing : 0,
			border : 0,
			width : "100%",
			cls : 'tabForm'
		};

		if(document.getElementById("peopleTable")) {
			//SUGAR.email2.util.removeElementRecursive(document.getElementById("peopleTable"));
			document.getElementById("peopleTable").innerHTML = "";
		}
		
		var ret = JSON.parse(o.responseText);

		for(var i in ret) {
            if (i > -1) {
			    SUGAR.email2.addressBook.displaySearchResultRow(i, ret[i]);
            }
		}
		// cn: bug 13912 - IE's dom is fubar.  fix with IE-hack
		if(SUGAR.email2.util.isIe()) {
			document.getElementById('contactsDialogueHTML').innerHTML = document.getElementById('contactsDialogueHTML').innerHTML;
		}
	},













	/**
	 * Retrieves Contacts (assigned and hand-selected) for the current user
	 */
	getUserContacts : function(o) {
		var ret = JSON.parse(o.responseText);
		SUGAR.email2.addressBook._contactCache = ret;
		SUGAR.email2.addressBook.buildContactList(ret);

		if(SUGAR.email2.addressBook.editContactDialog && SUGAR.email2.addressBook.editContactDialog.isVisible()) {
			SUGAR.email2.addressBook.editContactDialog.hide();
		}




	},

	/**
	 * sets the edit contact mini-form in HTML
	 */
	setEditContactForm : function(o) {
		var ret = JSON.parse(o.responseText);
		var layout = SUGAR.email2.addressBook.editContactDialog.getLayout();
		/*var panel = new Ext.ContentPanel('editContactTab', {title: ret.contactName});
		//Prepare global variables to be read by the edit form
        module='Contacts';
        panel.setContent(ret.form);

		SUGAR.email2.addressBook.editContactDialog.beginUpdate();
		SUGAR.email2.addressBook.editContactDialog.setTitle(app_strings.LBL_EMAIL_EDIT_CONTACT);
		layout.add('center', panel);

		prefillEmailAddresses(ret.form, ret.prefillData);

		SUGAR.email2.addressBook.editContactDialog.endUpdate();
		SUGAR.email2.addressBook.editContactDialog.show();
		SUGAR.email2.addressBook.editContactDialog.resizeTo(600, 400);*/
	}
};
/**
 * AddressBook callbacks
 */
AjaxObject.addressBook.callback = {
	editContact : {
		success	: AjaxObject.addressBook.setEditContactForm,
		failure	: AjaxObject.handleFailure,
		timeout	: AjaxObject.timeout,
		scope	: AjaxObject
	},






















	search : {
		success	: AjaxObject.addressBook.displaySearchResults,
		failure	: AjaxObject.handleFailure,
		timeout	: AjaxObject.timeout,
		scope	: AjaxObject
	}
};

///////////////////////////////////////////////////////////////////////////////
////	COMPOSE LAYOUT
AjaxObject.composeLayout = {
	/**
	 * Populates the record id
	 */
	saveDraftCleanup : function(o) {
		hideOverlay();
		var ret = JSON.parse(o.responseText);
		SUGAR.email2.composeLayout.forceCloseCompose(ret.composeLayoutId);
	}
};

AjaxObject.composeLayout.callback = {
	saveDraft : {
		success	: AjaxObject.composeLayout.saveDraftCleanup,
		failure	: AjaxObject.handleFailure,
		timeout	: AjaxObject.timeout,
		scope	: AjaxObject
	}
};

AjaxObject.detailView = {
	/**
	 * Pops-up a printable view of an email
	 */
	displayPrintable : function(o) {
		var ret = JSON.parse(o.responseText);
		var displayTemplate = new YAHOO.SUGAR.Template(SUGAR.email2.templates['viewPrintable']);
		// 2 below must be in global context
		meta = ret.meta;
		meta['panelId'] = SUGAR.email2.util.getPanelId();
		email = ret.meta.email;
		if (typeof(email.cc) == 'undefined') {
		  email.cc = "";
		}
		
		var out = displayTemplate.exec({
			'app_strings'	: app_strings,
			'theme'			: theme,
			'idx'			: 'Preview',
			'meta'			: meta,
			'email'			: meta.email
		});

		// open popup window
		var popup = window.open('modules/Emails/templates/_blank.html', 'printwin' , 
		    'scrollbars=yes,menubar=no,height=600,width=800,resizable=yes,toolbar=no,location=no,status=no');

		popup.document.write(out);
		popup.document.close();
	},

	/**
	 * Takes formatted response and creates a modal pop-over containing a title and content
	 */
	displayView : function(o) {
		var SED = SUGAR.email2.detailView;
		var ret = JSON.parse(o.responseText);

		if(!SED.viewDialog) {
			SED.viewDialog = new YAHOO.widget.Dialog("viewDialog", {
				modal:true,
            	visible:true,
            	fixedcenter:true,
            	constraintoviewport: true,
                width	: 800,
				height	: 450,
				shadow	: true
			});
            SED.viewDialog.renderEvent.subscribe(function() {
            	var content = this.body.firstChild;
            	var view = SUGAR.util.getViewPortSize();
            	if (content) {
            		this.body.style.overflow = "auto";
            		this.body.style.width = "800px";
            		this.body.style.height = (view.y - 75 > content.clientHeight ? (content.clientHeight) : (view.y - 75)) + "px";
            	}
            }, SED.viewDialog);
		} // end lazy load
		SED.viewDialog.setHeader(ret.title);
		SED.viewDialog.setBody(ret.html);
		SED.viewDialog.render();
		SED.viewDialog.show();	
	},

	/**
	 * Generates a modal popup to populate with the contents of bean's full EditView
	 */
	showQuickCreateForm : function(o) {
		var SED = SUGAR.email2.detailView;
		var ret = JSON.parse(o.responseText);

		if(!SED.quickCreateDialog) {
			SED.quickCreateDialog = new YAHOO.widget.Dialog("quickCreate", {
				modal:true,
				visible:true,
            	fixedcenter:true,
            	constraintoviewport: true,
                width	: 800,
				height	: 450,
				shadow	: true
			});
			
            SED.quickCreateDialog.renderEvent.subscribe(function() {
            	var viewH = YAHOO.util.Dom.getViewportHeight();
            	var contH = 0;
            	for (var i in this.body.childNodes) {
            		if (this.body.childNodes[i].clientHeight)
            			contH += this.body.childNodes[i].clientHeight;
            	}
        		this.body.style.overflow = "auto";
        		this.body.style.width = "800px";
        		this.body.style.height = (viewH - 75 > contH ? (contH + 10) : (viewH - 75)) + "px";
            }, SED.quickCreateDialog);
            
            SED.quickCreateDialog.hideEvent.subscribe(function(){
				var qsFields = YAHOO.util.Dom.getElementsByClassName('.sqsEnabled', null, this.body);
				/*for(var qsField in qsFields){
					if (typeof QSFieldsArray[qsFields[qsField].id] != 'undefined')
					Ext.getCmp('combobox_'+qsFields[qsField].id).destroy();
				}*/
			});
            SED.quickCreateDialog.setHeader(app_strings.LBL_EMAIL_QUICK_CREATE);
		} // end lazy load
		SED.quickCreateDialog.setBody(ret.html ? ret.html : "&nbsp;");
		SED.quickCreateDialog.render();
		SUGAR.util.evalScript(ret.html + '<script language="javascript">enableQS(true);</script>');
		
		SED.quickCreateDialog.ieId = ret.ieId;
		SED.quickCreateDialog.uid = ret.uid;
        SED.quickCreateDialog.mbox = ret.mbox;
        SED.quickCreateDialog.qcmodule = ret.module;
        
        SED.quickCreateDialog.show();

		var editForm = document.getElementById('form_EmailQCView_' + ret.module);
		if (editForm) {
		  editForm.module.value = 'Emails';
		  prefillEmailAddresses(ret.form, ret.emailAddress);
		}
	},

	saveQuickCreateForm : function(o) {
	    hideOverlay();
		SUGAR.email2.detailView.quickCreateDialog.hide();
		validate['EditView'] = [ ];
	},

	saveQuickCreateFormAndReply : function(o) {
	    hideOverlay();
	    var ret = JSON.parse(o.responseText);
        SUGAR.email2.detailView.quickCreateDialog.hide();
        var qcd = SUGAR.email2.detailView.quickCreateDialog;
        var type = (qcd.qcmodule == 'Cases') ? 'replyCase' : 'reply';
        if (ret) {
            var emailID = ret.id;
            SUGAR.email2.composeLayout.c0_replyForwardEmail(null, ret.id, 'sugar::Emails', type);
        } else {
            SUGAR.email2.composeLayout.c0_replyForwardEmail(qcd.ieId, qcd.uid, qcd.mbox, type);
        }
        //Cean the validate cache to prevent errors on the next call
        validate['EditView'] = [ ];
    },

	saveQuickCreateFormAndAddToAddressBook : function(o) {
	   hideOverlay();
		SUGAR.email2.detailView.quickCreateDialog.hide();
		SUGAR.email2.complexLayout.findPanel('contactsTab').show();
		validate['EditView'] = [ ];
	},
	
	handleAssignmentDialogAssignAction : function() {
		var select = document.getElementById("userSelect");
		var dist = document.getElementById("dm").value;
		var users = false;
		var userIds = "";
		var rules = false;
		var warn1 = mod_strings.LBL_WARN_NO_USERS;
		var warn2 = "";
	
		for(i=0; i<select.options.length; i++) {
			if(select.options[i].selected == true) {
				userIds = userIds + select.options[i].id + ",";
				users = true;
				warn1 = "";
			}
		}
		
		userIds = userIds.substring(0, (userIds.length - 1));
		if(dist != "") {
			rules = true;
		} else {
			warn2 = mod_strings.LBL_WARN_NO_DIST;
		}
		
		if(users && rules) {
			
			// TO pass list of UIDS/emailIds
			//var uids = SUGAR.email2.listView.getUidsFromSelection();
            var emailUids = SUGAR.email2.listView.getUidsFromSelection();
            var uids = "";
            for(i=0; i<emailUids.length; i++) {
                if(uids != '') {
                    uids += app_strings.LBL_EMAIL_DELIMITER;
                }
                uids += emailUids[i];
            }
			
			var row = SUGAR.email2.grid.getSelectedRows()[0];
	        var data = SUGAR.email2.grid.getRecord(row).getData();
			var ieid = data.ieId;
		    var mbox = data.mbox;
            AjaxObject.startRequest(callbackAssignmentAction, urlStandard + '&emailUIAction=' + "doAssignmentAssign&uids=" + uids + "&ieId=" + ieid + "&folder=" + mbox + "&distribute_method=" + dist + "&users=" +userIds);
            SUGAR.email2.contextMenus.assignmentDialog.hide();
			overlay('Assignment', app_strings.LBL_EMAIL_ONE_MOMENT);
			
		} else {
			alert(mod_strings.LBL_ASSIGN_WARN + "\n" + warn1 + "\n" + warn2);
		}
		
	},
	
	handleAssignmentDialogDeleteAction : function() {
		// TO pass list of UIDS/emailIds
		var uids = SUGAR.email2.listView.getUidsFromSelection();
		var row = SUGAR.email2.grid.getSelections()[0];
		var ieid = row.data.ieId;
	    var mbox = row.data.mbox;
        AjaxObject.startRequest(callbackAssignmentAction, urlStandard + '&emailUIAction=' + "doAssignmentDelete&uids=" + uids + "&ieId=" + ieId + "&folder=" + mbox);
        SUGAR.email2.contextMenus.assignmentDialog.hide();
		overlay(app_strings.LBL_EMAIL_PERFORMING_TASK, app_strings.LBL_EMAIL_ONE_MOMENT);

		// AJAX Call
		
	},
	
	showEmailDetailView : function(o) {
        hideOverlay();
        var SED = SUGAR.email2.detailView;
		var ret = JSON.parse(o.responseText);

		if(!SED.quickCreateDialog) {
			SED.quickCreateDialog = new YAHOO.widget.Dialog("emailDetailDialog", {
				modal:true,
				visible:true,
            	fixedcenter:true,
            	constraintoviewport: true,
                width	: 800,
				height	: 450,
				shadow	: true
			});
			SED.quickCreateDialog.renderEvent.subscribe(function() {
            	var view = SUGAR.util.getViewPortSize();
            	var contH = 0;
            	for (var i in this.body.childNodes) {
            		if (this.body.childNodes[i].clientHeight)
            			contH += this.body.childNodes[i].clientHeight;
            	}
            	if (content) {
            		this.body.style.overflow = "auto";
            		this.body.style.width = "800px";
            		this.body.style.height = (view.y - 75 > contH ? (contH + 10) : (view.y - 75)) + "px";
            	}
            }, SED.quickCreateDialog);
		}
		SED.quickCreateDialog.setHeader(app_strings.LBL_EMAIL_RECORD);
		SED.quickCreateDialog.setBody(ret.html);
		SED.quickCreateDialog.render();
        SUGAR.util.evalScript(ret.html);      
        SED.quickCreateDialog.show();		
	},
	
	showAssignmentDialogWithData : function(o) {
        var SEC = SUGAR.email2.contextMenus;
		hideOverlay();
        var ret = JSON.parse(o.responseText);
        if (!SEC.assignmentDialog) {
	        SEC.assignmentDialog = new YAHOO.widget.Dialog("assignmentDialog", {
	        	visible:false,
            	fixedcenter:true,
            	constraintoviewport: true,
	        	modal   : true,
	            width   : 700,
	            height  : 350
	        });
	        SEC.assignmentDialog.setBody("");
	        SEC.assignmentDialog.setHeader(app_strings.LBL_EMAIL_ASSIGNMENT);
	        SEC.assignmentDialog.renderEvent.subscribe(function() {
            	var iev = YAHOO.util.Dom.get("Distribute");
            	if (iev) {
            		this.body.style.height = (iev.offsetHeight) + "px";
            		this.body.style.width = "700px";
            	}
            },  SEC.assignmentDialog);
	        SEC.assignmentDialog.render();
        }
        SEC.assignmentDialog.setBody(ret);
        SEC.assignmentDialog.render();
        validate = [];
        SEC.assignmentDialog.show();
        SUGAR.util.evalScript(ret);
	},
	
	showImportForm : function(o) {
		var SED = SUGAR.email2.detailView;
		var ret = JSON.parse(o.responseText);
        
        document.getElementById('quickCreateContent').innerHTML = "";
        hideOverlay();
        if (!ret) {
            return false;
        }

        if(!SED.importDialog) {
            SED.importDialog = new YAHOO.widget.Dialog("importDialog", {
            	modal:true,
            	visible:false,
            	fixedcenter:true,
            	constraintoviewport: true,
                width   : 600,
                height  : 225,
                buttons : [{
                	text: app_strings.LBL_EMAIL_ARCHIVE_TO_SUGAR, isDefault: true, handler: function(){
                		AjaxObject.detailView.getImportAction(SED.importDialog.ret); }
                }, {
                	text: app_strings.LBL_EMAIL_CANCEL, handler: function() {
                        SED.importDialog.hide()
                        SED.importDialog.setBody("");
                    }
                }]//,
                //scroll : true
            });
            SED.importDialog.setHeader(app_strings.LBL_EMAIL_IMPORT_SETTINGS);
            SED.importDialog.setBody("");
            SED.importDialog.renderEvent.subscribe(function() {
            	var iev = YAHOO.util.Dom.get("ImportEditView");
            	if (iev) {
            		this.body.style.height = (iev.clientHeight + 10) + "px";
            		this.body.style.width = "600px";
            	}
            }, SED.importDialog);
            SED.importDialog.render();
        } // end lazy load
        SED.importDialog.setBody(ret.html);
        SED.importDialog.ret = ret;
        SED.importDialog.render();
        validate = [];
        SED.importDialog.show();

    },
    getImportAction : function(ret) {
        if (!check_form('ImportEditView')) return false;
		var get = "";
        var editView = document.getElementById('ImportEditView');






        if (editView.assigned_user_id != null) {
            get = get + "&user_id=" + editView.assigned_user_id.value
            //var user_id = editView.assigned_user_id.value;
        }
        var parent_id = editView.parent_id.value;
        var parent_type = editView.parent_type.value;
        var row = SUGAR.email2.grid.getSelectedRows()[0];
        row = SUGAR.email2.grid.getRecord(row);
        var data = row.getData();
        var ieId = data.ieId; 
        var mbox = data.mbox; 
        var serverDelete = editView.serverDelete.checked;
        var emailUids = SUGAR.email2.listView.getUidsFromSelection();
        var uids = "";
        for(i=0; i<emailUids.length; i++) {
            if(uids != '') {
                uids += app_strings.LBL_EMAIL_DELIMITER;
            }
            uids += emailUids[i];
        }
        
        var action = 'importEmail&uid=';
        if (ret.move) {
            action = 'moveEmails';
            action = action + '&sourceFolder=' + ret['srcFolder'];
            action = action + '&sourceIeId=' + ret['srcIeId'];
            action = action + '&destinationFolder=' + ret['dstFolder'];
            action = action + '&destinationIeId=' + ret['dstIeId'];
            action = action + '&emailUids=';
        } 
        if (action.search(/importEmail/) != -1) {
            overlay(app_strings.LBL_EMAIL_IMPORTING_EMAIL, app_strings.LBL_EMAIL_ONE_MOMENT);
        } else {
            overlay("Moving Email(s)", app_strings.LBL_EMAIL_ONE_MOMENT);
        }
        
        AjaxObject.startRequest(callbackStatusForImport, urlStandard + '&emailUIAction=' + action + uids + "&ieId=" + ieId + "&mbox=" + mbox + 
        get + "&parent_id=" + parent_id + "&parent_type=" + parent_type + '&delete=' + serverDelete);
        SUGAR.email2.detailView.importDialog.hide();
        document.getElementById('importDialogContent').innerHTML = "";
        
    },
    showRelateForm : function(o) {
        var SED = SUGAR.email2.detailView;
    	var ret = JSON.parse(o.responseText);
        document.getElementById('quickCreateContent').innerHTML = "";
        hideOverlay();
        if (!ret) {
            return false;
        }
        if(!SED.relateDialog) {
			SED.relateDialog = new YAHOO.widget.Dialog("relateDialog", {
				modal:true,
				visible:true,
            	fixedcenter:true,
            	constraintoviewport: true,
                width	: 800,
				height	: 450,
				buttons : [{
                	text: app_strings.LBL_EMAIL_RELATE_TO, isDefault: true, handler: function(){
    					if (!check_form('ImportEditView')) return false;
    					var get = "";
    	                var editView = document.getElementById('ImportEditView');
    	                var parent_id = editView.parent_id.value;
    	                var parent_type = editView.parent_type.value;
    	                var row = SUGAR.email2.grid.getSelectedRows()[0];
    	                row  = SUGAR.email2.grid.getRecord(row);
    	                var ieId = row.getData().ieId; 
    	                var mbox = row.getData().mbox; 
    	                var emailUids = SUGAR.email2.listView.getUidsFromSelection();
    	                var uids = "";
    	                for(i=0; i<emailUids.length; i++) {
    	                    if(uids != '') {
    	                        uids += app_strings.LBL_EMAIL_DELIMITER;
    	                    }
    	                    uids += emailUids[i];
    	                }
    	                overlay(app_strings.LBL_EMAIL_PERFORMING_TASK, app_strings.LBL_EMAIL_ONE_MOMENT);
    	                AjaxObject.startRequest(callbackStatusForImport, urlStandard + '&emailUIAction=relateEmails&uid=' + uids 
    	                    + "&ieId=" + ieId + "&mbox=" + mbox + "&parent_id=" + parent_id + "&parent_type=" + parent_type);
    	                SUGAR.email2.detailView.relateDialog.hide();
    	                document.getElementById('relateDialogContent').innerHTML = "";
	                }
                }, {
                	text: app_strings.LBL_EMAIL_CANCEL, handler: function() {
                	 	SUGAR.email2.detailView.relateDialog.hide()
                	 	document.getElementById('relateDialogContent').innerHTML = "";
                    }
                }]
			});
			
            SED.relateDialog.renderEvent.subscribe(function() {
            	var view = SUGAR.util.getViewPortSize();
            	var contH = 0;
            	for (var i in this.body.childNodes) {
            		if (this.body.childNodes[i].clientHeight)
            			contH += this.body.childNodes[i].clientHeight;
            	}
            	if (content) {
            		this.body.style.overflow = "auto";
            		this.body.style.width = "800px";
            		this.body.style.height = (view.y - 75 > contH ? (contH + 10) : (view.y - 75)) + "px";
            	}
            }, SED.relateDialog);
            SED.relateDialog.setHeader(app_strings.LBL_EMAIL_RELATE_EMAIL);
		} // end lazy load
        SED.relateDialog.setBody(ret.html);
        SED.relateDialog.render();
        SUGAR.util.evalScript(ret.html);
        SED.relateDialog.show();
    }
};
/**
 * DetailView callbacks
 */
AjaxObject.detailView.callback = {
	emailDetail : {
		success	: function(o) {
			SUGAR.email2.o = o;
			var ret = JSON.parse(o.responseText);
			SUGAR.email2.detailView.consumeMetaDetail(ret);
		},
		argument: [targetDiv],
		failure	: AjaxObject.handleFailure,
		timeout	: 0,
		scope	: AjaxObject
	},
	emailPreview : {
		success	: function(o) {
			SUGAR.email2.o = o;
			var ret = JSON.parse(o.responseText);
			SUGAR.email2.detailView.consumeMetaPreview(ret);
		},
		failure	: AjaxObject.handleFailure,
		timeout	: 0,
		scope	: AjaxObject
	},
	viewPrint : {
		success	: AjaxObject.detailView.displayPrintable,
		failure	: AjaxObject.handleFailure,
		timeout	: AjaxObject.timeout,
		scope	: AjaxObject
	},
	viewRaw : {
		success	: AjaxObject.detailView.displayView,
		failure	: AjaxObject.handleFailure,
		timeout	: AjaxObject.timeout,
		scope	: AjaxObject
	}
};





AjaxObject.folders = {
	/**
	 * check-mail post actions
	 */
	checkMailCleanup : function(o) {
		hideOverlay();
		AjaxObject.folders.rebuildFolders(o); // rebuild TreeView

		// refresh focus ListView
		if(SUGAR.email2.grid.getDataSource().baseParams['mbox'] != "" && SUGAR.email2.grid.getDataSource().baseParams['ieId'] != "") {
			SUGAR.email2.grid.getDataSource().baseParams['emailUIAction'] = 'getMessageListXML';
			//SUGAR.email2.grid.getDataSource().initPaging(urlBase, SUGAR.email2.userPrefs.emailSettings.showNumInList);
			//forcePreview = true;
		    //SUGAR.email2.grid.getDataSource().loadPage(1, SUGAR.email2.listView.setEmailListStyles);
		    SUGAR.email2.grid.getDataSource().load({params:{start:0, limit:SUGAR.email2.userPrefs.emailSettings.showNumInList}});
		}
		SUGAR.email2.folders.startCheckTimer(); // resets the timer
	},

	/**
	 */
	rebuildFolders : function(o) {
		hideOverlay();

		var data = JSON.parse(o.responseText);

		email2treeinit(SUGAR.email2.tree, data.tree_data, 'frameFolders', data.param);
		SUGAR.email2.folders.setSugarFolders();
		//SUGAR.email2.tree.render();
	}
};
AjaxObject.folders.callback = {
	checkMail : {
		success	: AjaxObject.folders.checkMailCleanup,
		failure	: AjaxObject.handleFailure,
		timeout	: 600000, // 5 mins
		scope	: AjaxObject
	}
}

AjaxObject.rules = {
	loadRulesForSettings : function(o) {
		document.getElementById("rulesListCell").innerHTML = o.responseText;
		// assume we have the class we need
		SUGAR.routing.getStrings();
		SUGAR.routing.getDependentDropdowns();
	}
};
////	END PER MODULE CALLBACK OBJECTS
///////////////////////////////////////////////////////////////////////////


var callback = {
	success	: AjaxObject.handleSuccess,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackAccount = {
	success	: AjaxObject.ieSaveSuccess,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackAccountDelete = {
	success	: AjaxObject.ieDeleteSuccess,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackAddGroupFolderFrom = {
	success	: function(o) {
		hideOverlay();
		//SUGAR.email2.accounts.rebuildFolderList(); // refresh frameFolder
		document.getElementById('groupFolderAddName').value = '';
		document.getElementById('groupFoldersAdd').options[0].selected = true;
		document.getElementById('groupFolderAddName').value = '';
		document.getElementById('editGroupFolderList').options[0].selected = true;
		document.getElementById('groupFoldersTeam').options[0].selected = true;
		SUGAR.email2.folders.loadSettingFolder(); // refresh folder multi-selects
	},
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackSaveGroupFolderFrom = {
	success	: function(o) {
		hideOverlay();
		var data = JSON.parse(o.responseText);
		if (data.status != "done") {
			overlay(app_strings.LBL_EMAIL_ERROR_DESC, data.message, 'alert');
			return;
		}
		//SUGAR.email2.accounts.rebuildFolderList(); // refresh frameFolder
		document.getElementById('groupFolderAddName').value = '';
		document.getElementById('groupFoldersAdd').options[0].selected = true;
		document.getElementById('groupFolderAddName').value = '';
		document.getElementById('editGroupFolderList').options[0].selected = true;
		document.getElementById('groupFoldersTeam').options[0].selected = true;
		SUGAR.email2.folders.loadSettingFolder(); // refresh folder multi-selects
	},
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackEditGroupFolder = {
	success	: function(o) {
	hideOverlay();
	var data = JSON.parse(o.responseText);
	document.getElementById('groupFolderAddName').value = data.folderName;
	var groupFoldersAddTo = document.getElementById('groupFoldersAdd');
	
	SUGAR.email2.util.emptySelectOptions(groupFoldersAddTo);
	var grp = document.getElementById('groupFolders');
	for(i=0; i<grp.options.length; i++) {
		groupFoldersAddTo.options.add(new Option(grp.options[i].text, grp.options[i].value));
	}
	
	for (var i = 0 ; i < groupFoldersAddTo.options.length ; i++) {
		if (groupFoldersAddTo.options[i].value == data.parentFolderId) {
			groupFoldersAddTo.options[i].selected = true;
			//break;
		} // if
		if (groupFoldersAddTo.options[i].value == data.folderId) {
			groupFoldersAddTo.options[i] = null;
		}
	} // for
	









},
failure	: AjaxObject.handleFailure,
timeout	: AjaxObject.timeout,
scope	: AjaxObject

};
var callbackStatusForImport = {
success : function (o) {
	hideOverlay();
	if (o.responseText != "")  {
		var statusString = "";
		var data = JSON.parse(o.responseText);
		for(i=0; i<data.length; i++) {
			statusString = statusString + data[i] + '<br/>';
		}
		overlay("status", statusString, 'alert');
	}
	SUGAR.email2.grid.getDataSource().sendRequest(SUGAR.util.paramsToUrl(SUGAR.email2.grid.params));
	
},
failure	: AjaxObject.handleFailure,
timeout	: AjaxObject.timeout,
scope	: AjaxObject
	
};
var callbackComposeCache = {
	success	: AjaxObject.composeCache,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackDelete = {
	success	: AjaxObject.handleDeleteReturn,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackEmailDetailMultiple = {
	success	: function(o) {
		hideOverlay();
		var retMulti = JSON.parse(o.responseText);
		var displayTemplate = new YAHOO.SUGAR.Template(SUGAR.email2.templates['displayOneEmail']);
		var ret = new Object();

		for(var i=0; i<retMulti.length; i++) {
			ret = retMulti[i];

			SUGAR.email2._setDetailCache(ret);
			SUGAR.email2.detailView.populateDetailView(ret.meta.uid, ret.meta.mbox, ret.meta.ieId, true, SUGAR.email2.innerLayout);
		}
	},
	failure	: AjaxObject.handleFailure,
	timeout	: 0,
	scope	: AjaxObject
};
var callbackListViewSortOrderChange = {
	success	: AjaxObject.saveListViewSortOrderPart2,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject,
	argument	: [ieId, ieName, focusFolder]
};
var callbackEmptyTrash = {
	success	: function(o) {
		hideOverlay();
		AjaxObject.folderRenameCleanup;
	},
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackClearCacheFiles = {
	success	: function(o) {
		hideOverlay();
	},
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackFolderRename = {
	success	: function(o) {hideOverlay();},
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackFolderDelete = {
	success	: function(o) {
		var ret = JSON.parse(o.responseText);
		if (ret.status) {
		    if (ret.folder_id) {
		        var node = SUGAR.email2.folders.getNodeFromId(ret.folder_id);
		        if(node)
		        	SUGAR.email2.tree.removeNode(node, true);
		    } else if (ret.ieId && ret.mbox) {
		        var node = SUGAR.email2.folders.getNodeFromIeIdAndMailbox(ret.ieId, ret.mbox);
		        if(node)
		        	SUGAR.email2.tree.removeNode(node, true);
		    }
			hideOverlay();
			//SUGAR.email2.folders.loadSettingFolder();
		} else {
			hideOverlay();
			overlay(app_strings.LBL_EMAIL_ERROR_DESC, ret.errorMessage, 'alert');
		} // else
	},
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackFolderSave = {
	success	: function(o) {
		var ret = JSON.parse(o.responseText);

		switch(ret.action) {
			case 'newFolderSave':
				SUGAR.email2.folders.rebuildFolders();
			break;
		}
	},
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackFolderSubscriptions = {
	success	: AjaxObject.updateFolderSubscriptions,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackFolderUpdate = {
	success	: AjaxObject.updateFrameFolder,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackFolders = {
	success	: AjaxObject.folders.rebuildFolders,
	//success : void(true),
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackFullSync = {
	success	: AjaxObject.fullSyncCleanup,
	failure	: AjaxObject.handleFailure,
	timeout	: 9999999999999,
	scope	: AjaxObject
};
var callbackGeneric = {
	success	: function() {
		hideOverlay();
	},
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};








var callbackGetUserContacts = {
	success	: AjaxObject.addressBook.getUserContacts,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackIeAccountRetrieve = {
	success	: function(o) {
		// return JSON encoding
		hideOverlay();
		SUGAR.email2.accounts.fillIeAccount(o.responseText);
	},
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackImportOneEmail = {
	success :  AjaxObject.detailView.showImportForm,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackRelateEmail = {
    success : AjaxObject.detailView.showRelateForm,
    failure : AjaxObject.handleFailure,
    timeout : AjaxObject.timeout,
    scope   : AjaxObject
}
var callbackEmailDetailView = {
    success : AjaxObject.detailView.showEmailDetailView,
    failure : AjaxObject.handleFailure,
    timeout : AjaxObject.timeout,
    scope   : AjaxObject
}
var callbackAssignmentDialog = {
	success :  AjaxObject.detailView.showAssignmentDialogWithData,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject	
};
var callbackAssignmentAction = {
	success :  function(o) {
		SUGAR.email2.grid.getDataSource().sendRequest(SUGAR.util.paramsToUrl(SUGAR.email2.grid.params));
	    hideOverlay();
		if(o.responseText != '') {
	       overlay('Assignment action result', o.responseText, 'alert');
	    } // if
	} ,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackMoveEmails = {
	success :  function(o) {
		SUGAR.email2.grid.getDataSource().sendRequest(SUGAR.util.paramsToUrl(SUGAR.email2.grid.params), SE.grid.onDataReturnInitializeTable, SE.grid);
	    hideOverlay();
		if(o.responseText != '') {
	       overlay(app_strings.LBL_EMAIL_ERROR_DESC, o.responseText, 'alert');
	    } // if
	} ,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackLoadAttachments = {
	success	: AjaxObject.loadAttachments,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackLoadRules = {
	success	: AjaxObject.rules.loadRulesForSettings,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackLoadSignature = {
	success	: AjaxObject.loadSignature,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackDeleteSignature = {
	success	: AjaxObject.handleDeleteSignature,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
/*var callbackMoveEmails = {
    success : function(o) { SUGAR.email2.listView.moveEmailsCleanup(o) },
    failure : AjaxObject.handleFailure,
    timeout : AjaxObject.timeout,
    scope   : AjaxObject
}*/
var callbackOutboundSave = {
	success	: AjaxObject.accounts.saveOutboundCleanup,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackQuickCreate = {
	success	: AjaxObject.detailView.showQuickCreateForm,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackQuickCreateSave = {
	success	: AjaxObject.detailView.saveQuickCreateForm,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackQuickCreateSaveAndAddToAddressBook = {
	success	: AjaxObject.detailView.saveQuickCreateFormAndAddToAddressBook,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackQuickCreateSaveAndReply = {
    success : AjaxObject.detailView.saveQuickCreateFormAndReply,
    failure : AjaxObject.handleFailure,
    timeout : AjaxObject.timeout,
    scope   : AjaxObject
}
var callbackQuickCreateSaveAndReplyCase = {
    success : AjaxObject.detailView.saveQuickCreateFormAndReplyCase,
    failure : AjaxObject.handleFailure,
    timeout : AjaxObject.timeout,
    scope   : AjaxObject
}
var callbackRebuildShowAccountList = {
	success	: AjaxObject.rebuildShowFolders,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackRebuildShowAccountListForSearch = {
	success	: AjaxObject.rebuildShowFoldersForSearch,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackRefreshSugarFolders = {
	success	: function(o) {
		var t = JSON.parse(o.responseText);
		SUGAR.email2.folders.setSugarFoldersEnd(t);
	},
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackReplyForward = {
	success	: AjaxObject.handleReplyForward,
	finish : function(a, retryCount) {
		if (typeof(retryCount) == 'undefined') {
			retryCount = 0;
		} else {
			retryCount++;
		}
		var idx = SUGAR.email2.composeLayout.currentInstanceId;
		var t = tinyMCE.getInstanceById('htmleditor' + idx);
        try {
			var html = t.getContent();

            if (a.type != 'draft') {
    			if(SUGAR.email2.userPrefs.signatures.signature_prepend == 'true') {
    				html += "&nbsp;<div><hr></div>" + a.description;
    			} else {
    				html =  "&nbsp;<div><hr></div>" + a.description + html;
    			}
            }else {
                html = a.description;
            }

			t.setContent(html);//

		} catch(e) {




			if (retryCount < 5) {
				setTimeout("callbackReplyForward.finish(globalA, " + retryCount + ");", 500);
				return;
			}
		}
		var tabArray = SUGAR.email2.innerLayout.get("tabs");
		if (tabArray != null && tabArray.length > 0) {
			for (i = 0 ; i < tabArray.length ; i++) {
				var tabObject = tabArray[i];
				if (tabObject.get("id") == ("composeTab" + idx)) {
					tabObject.get("labelEl").firstChild.data = a.name;
					break;
				}
			}
		}
		
		//SUGAR.email2.innerLayout.regions.center.getPanel('composeLayout' + idx).setTitle(a.name);
		if (a.parent_name != null && a.parent_name != "") {
			document.getElementById('data_parent_name' + idx).value = a.parent_name;
		}
		if (a.parent_type != null && a.parent_type != "") {
			document.getElementById('data_parent_type' + idx).value = a.parent_type;
		}
		if (a.parent_id != null && a.parent_id != "") {
			document.getElementById('data_parent_id' + idx).value = a.parent_id;
		}
		if (a.fromAccounts.status) {
			var addressFrom = document.getElementById('addressFrom' + idx);
	        SUGAR.email2.util.emptySelectOptions(addressFrom);
	        var fromAccountOpts = a.fromAccounts.data;
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
		hideOverlay();

	},
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject,
	argument	: [sendType]
};
var callbackSendEmail = {
	success	: AjaxObject.sendEmailCleanUp,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackSettings = {
	success	: AjaxObject.updateUserPrefs,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackSettingsFolderRefresh = {
	success	: AjaxObject.settingsFolderRefresh,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackLoadSettingFolder = {
	success	: function(o) {
		AjaxObject.settingsFolderRefresh(o);
		SUGAR.email2.accounts.rebuildFolderList(); // refresh frameFolder
	},
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject

};
var callbackUploadAttachment = {
	success	: AjaxObject.uploadAttachmentSuccessful,
	upload	: AjaxObject.uploadAttachmentSuccessful,
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};
var callbackUserPrefs = {
	success	: function(o) {
		SUGAR.email2.userPrefs = JSON.parse(o.responseText);
	},
	failure	: AjaxObject.handleFailure,
	timeout	: AjaxObject.timeout,
	scope	: AjaxObject
};

var callbackContextmenus = {
	markUnread : {
		success : AjaxObject.markEmailCleanup,
		failure : AjaxObject.handleFailure,
		timeout : AjaxObject.timeout,
		scope   : AjaxObject
	}
};












var callbackCheckEmail2 = {
	success : function(o) {
		var ret = JSON.parse(o.responseText);
		overlay(app_strings.LBL_EMAIL_CHECKING_NEW, ret.text);


	},
	failure : AjaxObject.handleFailure,
	timeout : AjaxObject.timeout,
	scope	: AjaxObject
}
