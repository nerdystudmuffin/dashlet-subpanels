/**
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


Rot13 = {
    map: null,

    convert: function(a) {
        Rot13.init();

        var s = "";
        for (i=0; i < a.length; i++) {
            var b = a.charAt(i);
            s += ((b>='A' && b<='Z') || (b>='a' && b<='z') ? Rot13.map[b] : b);
        }
        return s;
    },

    init: function() {
        if (Rot13.map != null)
            return;
              
        var map = new Array();
        var s   = "abcdefghijklmnopqrstuvwxyz";

        for (i=0; i<s.length; i++)
            map[s.charAt(i)] = s.charAt((i+13)%26);
        for (i=0; i<s.length; i++)
            map[s.charAt(i).toUpperCase()] = s.charAt((i+13)%26).toUpperCase();

        Rot13.map = map;
    },

    write: function(a) {
        return Rot13.convert(a);
    }
}


function getEncryptedPassword(login, password, mailbox) {
	var words = new Array(login, password, mailbox);
	for(i=0; i<3; i++) {
		word = words[i];
		if(word.indexOf('&') > 0) {
			fragment1 = word.substr(0, word.indexOf('&'));
			fragment2 = word.substr(word.indexOf('&') + 1, word.length);
			
			newWord = fragment1 + '::amp::' + fragment2;
			words[i] = newWord;
			word = newWord; // setting it locally to pass on to next IF
			fragment1 = '';
			fragment2 = '';
		}
		if(word.indexOf('+') > 0) {
			fragment1 = word.substr(0, word.indexOf('+'));
			fragment2 = word.substr(word.indexOf('+') + 1, word.length);
			
			newWord = fragment1 + '::plus::' + fragment2;
			words[i] = newWord;
			word = newWord; // setting it locally to pass on to next IF
			fragment1 = '';
			fragment2 = '';
		}
		if(word.indexOf('%') > 0) {
			fragment1 = word.substr(0, word.indexOf('%'));
			fragment2 = word.substr(word.indexOf('%') + 1, word.length);
			
			newWord = fragment1 + '::percent::' + fragment2;
			words[i] = newWord;
			word = newWord; // setting it locally to pass on to next IF
			fragment1 = '';
			fragment2 = '';
		}
	} // for
	
	return words;
} // fn

function ie_test_open_popup(module_name, action, target, width, height, mail_server, protocol, port, login, password, mailbox, ssl, personal)
{
	var words = getEncryptedPassword(login, password, mailbox);
	/*
	var words = new Array(login, password, mailbox);
	for(i=0; i<3; i++) {
		word = words[i];
		if(word.indexOf('&') > 0) {
			fragment1 = word.substr(0, word.indexOf('&'));
			fragment2 = word.substr(word.indexOf('&') + 1, word.length);
			
			newWord = fragment1 + '::amp::' + fragment2;
			words[i] = newWord;
			word = newWord; // setting it locally to pass on to next IF
			fragment1 = '';
			fragment2 = '';
		}
		if(word.indexOf('+') > 0) {
			fragment1 = word.substr(0, word.indexOf('+'));
			fragment2 = word.substr(word.indexOf('+') + 1, word.length);
			
			newWord = fragment1 + '::plus::' + fragment2;
			words[i] = newWord;
			word = newWord; // setting it locally to pass on to next IF
			fragment1 = '';
			fragment2 = '';
		}
		if(word.indexOf('%') > 0) {
			fragment1 = word.substr(0, word.indexOf('%'));
			fragment2 = word.substr(word.indexOf('%') + 1, word.length);
			
			newWord = fragment1 + '::percent::' + fragment2;
			words[i] = newWord;
			word = newWord; // setting it locally to pass on to next IF
			fragment1 = '';
			fragment2 = '';
		}
	}
	*/
	var isPersonal = (personal) ? 'true' : 'false';
	
	// lanch the popup
	URL = 'index.php?'
		+ 'module=' + module_name
		+ '&action=' + action
		+ '&target=' + target
		+ '&server_url=' + mail_server
		+ '&email_user=' + words[0]
		+ '&protocol=' + protocol
		+ '&port=' + port
		+ '&email_password=' + words[1]
		+ '&mailbox=' + words[2]
		+ '&ssl=' + ssl
		+ '&personal=' + isPersonal;
	windowName = 'popup_window';
	
	windowFeatures = 'width=' + width
		+ ',height=' + height
		+ ',resizable=1,scrollbars=1';

	win = window.open(URL, windowName, windowFeatures);

	if(window.focus)
	{
		// put the focus on the popup if the browser supports the focus() method
		win.focus();
	}

	return win;
}

function ie_test_open_popup_with_submit(module_name, action, pageTarget, width, height, mail_server, protocol, port, login, password, mailbox, ssl, personal)
{
	var words = getEncryptedPassword(login, password, mailbox);
	/*	
	var words = new Array(login, password, mailbox);
	for(i=0; i<3; i++) {
		word = words[i];
		if(word.indexOf('&') > 0) {
			fragment1 = word.substr(0, word.indexOf('&'));
			fragment2 = word.substr(word.indexOf('&') + 1, word.length);
			
			newWord = fragment1 + '::amp::' + fragment2;
			words[i] = newWord;
			word = newWord; // setting it locally to pass on to next IF
			fragment1 = '';
			fragment2 = '';
		}
		if(word.indexOf('+') > 0) {
			fragment1 = word.substr(0, word.indexOf('+'));
			fragment2 = word.substr(word.indexOf('+') + 1, word.length);
			
			newWord = fragment1 + '::plus::' + fragment2;
			words[i] = newWord;
			word = newWord; // setting it locally to pass on to next IF
			fragment1 = '';
			fragment2 = '';
		}
		if(word.indexOf('%') > 0) {
			fragment1 = word.substr(0, word.indexOf('%'));
			fragment2 = word.substr(word.indexOf('%') + 1, word.length);
			
			newWord = fragment1 + '::percent::' + fragment2;
			words[i] = newWord;
			word = newWord; // setting it locally to pass on to next IF
			fragment1 = '';
			fragment2 = '';
		}
	}
	*/
	var isPersonal = (personal) ? 'true' : 'false';
	
	var formObject = document.getElementById('testSettingsView');
	formObject.module.value = module_name;
	formObject.action.value = action;
	formObject.target = pageTarget;
	formObject.target1.value = pageTarget;
	formObject.server_url.value = mail_server;
	formObject.email_user.value = words[0];
	formObject.protocol.value = protocol;
	formObject.port.value = port;
	formObject.email_password.value = words[1];
	formObject.mailbox.value = words[2];
	formObject.ssl.value = ssl;
	formObject.personal.value = isPersonal;
	
	if (!isDataValid(true)) {
		return;
	} // if
	// lanch the popup
	URL = 'index.php?'
		+ 'module=' + module_name
		+ '&to_pdf=1'
		+ '&action=InboundEmailTest';
	windowName = pageTarget;
	
	windowFeatures = 'width=' + width
		+ ',height=' + height
		+ ',resizable=1,scrollbars=1';

	win = window.open(URL, windowName, windowFeatures);
	
	if(window.focus)
	{
		// put the focus on the popup if the browser supports the focus() method
		win.focus();
	}

	formObject.submit();
	return win;
}

function isDataValid(validateMonitoredFolder) {
	var formObject = document.getElementById('testSettingsView');
    var errors = new Array();
    var out = new String();
	
    if(trim(formObject.server_url.value) == "") {
        errors.push(SUGAR.language.get('app_strings', 'LBL_EMAIL_ERROR_SERVER'));
    }
    if(trim(formObject.email_user.value) == "") {
        errors.push(SUGAR.language.get('app_strings', 'LBL_EMAIL_ERROR_USER'));
    }
    if(trim(formObject.email_password.value) == "") {
        errors.push(SUGAR.language.get('app_strings', 'LBL_EMAIL_ERROR_PASSWORD'));
    }
    if(formObject.protocol.protocol == "") {
        errors.push(SUGAR.language.get('app_strings', 'LBL_EMAIL_ERROR_PROTOCOL'));
    }
    if (formObject.protocol.value == 'imap' && validateMonitoredFolder) {
    	if (trim(formObject.mailbox.value) == "") {
    		errors.push(SUGAR.language.get('app_strings', 'LBL_EMAIL_ERROR_MONITORED_FOLDER'));
    	} // if
    }
    if(formObject.port.value == "") {
        errors.push(SUGAR.language.get('app_strings', 'LBL_EMAIL_ERROR_PORT'));
    }
    
    if(errors.length > 0) {
        out = SUGAR.language.get('app_strings', 'LBL_EMAIL_ERROR_DESC');
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
    
} // fn

function getFoldersListForInboundAccount(module_name, action, pageTarget, width, height, mail_server, protocol, port, login, password, mailbox, ssl, personal, searchFieldValue) {
	
	var words = getEncryptedPassword(login, password, mailbox);
	var isPersonal = (personal) ? 'true' : 'false';
	
	var formObject = document.getElementById('testSettingsView');
	formObject.module.value = module_name;
	formObject.action.value = action;
	formObject.target = pageTarget;
	formObject.target1.value = pageTarget;
	formObject.server_url.value = mail_server;
	formObject.email_user.value = words[0];
	formObject.protocol.value = protocol;
	formObject.port.value = port;
	formObject.email_password.value = words[1];
	formObject.mailbox.value = words[2];
	formObject.ssl.value = ssl;
	formObject.personal.value = isPersonal;
	formObject.searchField.value = searchFieldValue;
	if (!isDataValid(false)) {
		return;
	} // if
	
	// lanch the popup
	URL = 'index.php?'
		+ 'module=' + module_name
		+ '&to_pdf=1'
		+ '&action=InboundEmailTest';
	windowName = pageTarget;
	
	windowFeatures = 'width=' + width
		+ ',height=' + height
		+ ',resizable=1,scrollbars=1';

	win = window.open(URL, windowName, windowFeatures);
	
	if(window.focus) {
		// put the focus on the popup if the browser supports the focus() method
		win.focus();
	} // if

	formObject.submit();
	return win;
	
} // fn

function setPortDefault() {
	var prot	= document.getElementById('protocol');
	var ssl		= document.getElementById('ssl');
	var port	= document.getElementById('port');
	var stdPorts= new Array("110", "143", "993", "995");
	var stdBool	= new Boolean(false);
	
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
}

function toggle_monitored_folder(field) {

	var field1=document.getElementById('protocol');
	//var target=document.getElementById('pop3_warn');
	//var mark_read = document.getElementById('mark_read');
	var mailbox = document.getElementById('mailbox');
	//var inbox = document.getElementById('inbox');
	var label_inbox = document.getElementById('label_inbox');
	var subscribeFolderButton = document.getElementById('subscribeFolderButton');
	var trashFolderRow = document.getElementById('trashFolderRow');
	var trashFolderRow1 = document.getElementById('trashFolderRow1');
	var sentFolderRow = document.getElementById('sentFolderRow');
	
	if (field1.value == 'imap') {
		//target.style.display="none";
		mailbox.disabled=false;
        // This is not supported in IE
        try {
		  trashFolderRow.style.display = '';
		  sentFolderRow.style.display = '';
		  trashFolderRow1.style.display = '';
          mailbox.style.display = '';
		  //mailbox.type='text';
          subscribeFolderButton.style.display = '';
        } catch(e) {};
		//inbox.style.display='';
		label_inbox.style.display='';
	}
	else {
		//target.style.display="";
		mailbox.value = "INBOX";
        mailbox.disabled=false; // cannot disable, else the value is not passed
        // This is not supported in IE
        try {
		  trashFolderRow.style.display = "none";
		  sentFolderRow.style.display = "none";
		  trashFolderRow1.style.display = "none";
          subscribeFolderButton.style.display = "none";
          mailbox.style.display = "none";
		  //mailbox.type='hidden';
        } catch(e) {};
       
		//inbox.style.display = "";
		label_inbox.style.display = "none";
	}
}
