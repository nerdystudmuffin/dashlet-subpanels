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
var numberEmailAddresses=0;var replyToFlagObject=new Object();var verifying=false;var enterPressed=false;var emailView;function prefillEmailAddresses(tableId,o){for(i=0;i<o.length;i++){o[i].email_address=o[i].email_address.replace('&#039;',"'");addEmailAddress(tableId,o[i].email_address,o[i].primary_address,o[i].reply_to_address,o[i].opt_out,o[i].invalid_email);}}
function retrieveEmailAddress(event){var callbackFunction=function success(data){vals=YAHOO.lang.JSON.parse(data.responseText);target=vals.target;if(vals.email){email=vals.email;if(email!=''&&/\d+$/.test(target)){matches=target.match(/\d+$/);targetNumber=matches[0];optOutEl=document.getElementById('emailAddressOptOutFlag'+targetNumber);if(optOutEl){optOutEl.checked=email['opt_out']==1?true:false;}
invalidEl=document.getElementById('emailAddressInvalidFlag'+targetNumber);if(invalidEl){invalidEl.checked=email['invalid_email']==1?true:false;}}}
var index=target.substring(12);verifyElementFlag=document.getElementById('emailAddressVerifiedFlag'+index);if(verifyElementFlag.parentNode.childNodes.length>1){verifyElementFlag.parentNode.removeChild(verifyElementFlag.parentNode.lastChild);}
verifiedTextNode=document.createElement('span');verifiedTextNode.innerHTML='';verifyElementFlag.parentNode.appendChild(verifiedTextNode);verifyElementFlag.value="true";verifyElementValue=document.getElementById('emailAddressVerifiedValue'+index);verifyElementValue.value=document.getElementById('emailAddress'+index).value;verifying=false;savePressed=false;if(event){elm=document.activeElement||event.explicitOriginalTarget;if(typeof elm.type!='undefined'&&elm.type.toLowerCase()=='submit'){savePressed=true;}}
if(savePressed||enterPressed){setTimeout(forceSubmit,2100);}else if(tabPressed){document.getElementById('emailAddressPrimaryFlag'+index).focus();}}
if(!event){event=window.event;}
target=(event.srcElement?event.srcElement:(event.target?event.target:event.currentTarget));index=target.id.substring(12);verifyElementFlag=document.getElementById('emailAddressVerifiedFlag'+index);verifyElementValue=document.getElementById('emailAddressVerifiedValue'+index);verifyElementFlag.value=(target.value==verifyElementValue.value)?"true":"false"
if(verifyElementFlag.parentNode.childNodes.length>1){verifyElementFlag.parentNode.removeChild(verifyElementFlag.parentNode.lastChild);}
if(/emailAddress\d+$/.test(target.id)&&target.value!=''&&isValidEmail(target.value)&&!verifying&&verifyElementFlag.value=="false"){verifiedTextNode=document.createElement('span');verifyElementFlag.parentNode.appendChild(verifiedTextNode);verifiedTextNode.innerHTML=SUGAR.language.get('app_strings','LBL_VERIFY_EMAIL_ADDRESS');verifying=true;var cObj=YAHOO.util.Connect.asyncRequest('GET','index.php?&module=Contacts&action=RetrieveEmail&target='+target.id+'&email='+target.value,{success:callbackFunction,failure:callbackFunction});}}
function handleKeyDown(event){e=getEvent(event);eL=getEventElement(e);if((kc=e["keyCode"])){enterPressed=(kc==13)?true:false;tabPressed=(kc==9)?true:false;if(enterPressed||tabPressed){retrieveEmailAddress(e);freezeEvent(e);}}}
function getEvent(event){return(event?event:window.event);}
function getEventElement(e){return(e.srcElement?e.srcElement:(e.target?e.target:e.currentTarget));}
function freezeEvent(e){if(e.preventDefault)e.preventDefault();e.returnValue=false;e.cancelBubble=true;if(e.stopPropagation)e.stopPropagation();return false;}
function addEmailAddress(tableId,address,primaryFlag,replyToFlag,optOutFlag,invalidFlag){var insertInto=document.getElementById(tableId);var parentObj=insertInto.parentNode;var newContent=document.createElement("input");var nav=new String(navigator.appVersion);var newContentPrimaryFlag;if(nav.match(/MSIE/gim)){newContentPrimaryFlag=document.createElement("<input name='emailAddressPrimaryFlag' />");}else{newContentPrimaryFlag=document.createElement("input");}
var newContentReplyToFlag=document.createElement("input");var newContentOptOutFlag=document.createElement("input");var newContentInvalidFlag=document.createElement("input");var newContentVerifiedFlag=document.createElement("input");var newContentVerifiedValue=document.createElement("input");var removeButton=document.createElement("img");var tbody=document.createElement("tbody");var tr=document.createElement("tr");var td1=document.createElement("td");var td2=document.createElement("td");var td3=document.createElement("td");var td4=document.createElement("td");var td5=document.createElement("td");var td6=document.createElement("td");var td7=document.createElement("td");var td8=document.createElement("td");newContent.setAttribute("type","text");newContent.setAttribute("name","emailAddress"+numberEmailAddresses);newContent.setAttribute("id","emailAddress"+numberEmailAddresses);newContent.setAttribute("size","30");if(address!=''){newContent.setAttribute("value",address);}
removeButton.setAttribute("id","removeButton"+numberEmailAddresses);removeButton.setAttribute("name",numberEmailAddresses)
removeButton.setAttribute("src","index.php?entryPoint=getImage&themeName="+SUGAR.themes.theme_name+"&imageName=delete_inline.gif");removeButton['onclick']=function(){removeFromValidate(emailView,'emailAddress'+this.name);var oNodeToRemove=document.getElementById('emailAddressRow'+this.name);oNodeToRemove.parentNode.removeChild(oNodeToRemove);removedIndex=parseInt(this.name);if(numberEmailAddresses!=removedIndex){for(x=removedIndex+1;x<numberEmailAddresses;x++){document.getElementById('emailAddress'+x).setAttribute("name","emailAddress"+(x-1));document.getElementById('emailAddress'+x).setAttribute("id","emailAddress"+(x-1));if(document.getElementById('emailAddressInvalidFlag'+x)){document.getElementById('emailAddressInvalidFlag'+x).setAttribute("id","emailAddressInvalidFlag"+(x-1));}
if(document.getElementById('emailAddressOptOutFlag'+x)){document.getElementById('emailAddressOptOutFlag'+x).setAttribute("id","emailAddressOptOutFlag"+(x-1));}
if(document.getElementById('emailAddressPrimaryFlag'+x)){document.getElementById('emailAddressPrimaryFlag'+x).setAttribute("id","emailAddressPrimaryFlag"+(x-1));}
document.getElementById('emailAddressVerifiedValue'+x).setAttribute("id","emailAddressVerifiedValue"+(x-1));document.getElementById('emailAddressVerifiedFlag'+x).setAttribute("id","emailAddressVerifiedFlag"+(x-1));rButton=document.getElementById('removeButton'+x);rButton.setAttribute("name",(x-1));rButton.setAttribute("id","removeButton"+(x-1));document.getElementById('emailAddressRow'+x).setAttribute("id",'emailAddressRow'+(x-1));}}
numberEmailAddresses--;if(numberEmailAddresses==0){return;}
primaryFound=false;for(x=0;x<numberEmailAddresses;x++){if(document.getElementById('emailAddressPrimaryFlag'+x).checked){primaryFound=true;}}
if(!primaryFound){document.getElementById('emailAddressPrimaryFlag0').checked=true;document.getElementById('emailAddressPrimaryFlag0').value='emailAddress0';}}
newContentPrimaryFlag.setAttribute("type","radio");newContentPrimaryFlag.setAttribute("name","emailAddressPrimaryFlag");newContentPrimaryFlag.setAttribute("id","emailAddressPrimaryFlag"+numberEmailAddresses);newContentPrimaryFlag.setAttribute("value","emailAddress"+numberEmailAddresses);newContentPrimaryFlag.setAttribute("enabled","true");newContentReplyToFlag.setAttribute("type","radio");newContentReplyToFlag.setAttribute("name","emailAddressReplyToFlag");newContentReplyToFlag.setAttribute("id","emailAddressReplyToFlag"+numberEmailAddresses);newContentReplyToFlag.setAttribute("value","emailAddress"+numberEmailAddresses);newContentReplyToFlag.setAttribute("enabled","true");newContentReplyToFlag['onclick']=function(){var form=document.forms[emailView];if(!form){form=document.forms['editContactForm'];}
var nav=new String(navigator.appVersion);if(nav.match(/MSIE/gim)){for(i=0;i<form.elements.length;i++){var id=new String(form.elements[i].id);if(id.match(/emailAddressReplyToFlag/gim)&&form.elements[i].type=='radio'&&id!=this.id){form.elements[i].checked=false;}}}
for(i=0;i<form.elements.length;i++){var id=new String(form.elements[i].id);if(id.match(/emailAddressReplyToFlag/gim)&&form.elements[i].type=='radio'&&id!=this.id){replyToFlagObject[id]=false;}}
if(replyToFlagObject[this.id]){replyToFlagObject[this.id]=false;this.checked=false;}else{replyToFlagObject[this.id]=true;this.checked=true;}}
newContentOptOutFlag.setAttribute("type","checkbox");newContentOptOutFlag.setAttribute("name","emailAddressOptOutFlag[]");newContentOptOutFlag.setAttribute("id","emailAddressOptOutFlag"+numberEmailAddresses);newContentOptOutFlag.setAttribute("value","emailAddress"+numberEmailAddresses);newContentOptOutFlag.setAttribute("enabled","true");newContentOptOutFlag['onClick']=function(){var form=document.forms[emailView];if(!form){form=document.forms['editContactForm'];}
var nav=new String(navigator.appVersion);if(nav.match(/MSIE/gim)){for(i=0;i<form.elements.length;i++){var id=new String(form.elements[i].id);if(id.match(/emailAddressOptOutFlag/gim)&&form.elements[i].type=='checkbox'&&id!=this.id){form.elements[i].checked=false;}}
this.checked=true;}}
newContentInvalidFlag.setAttribute("type","checkbox");newContentInvalidFlag.setAttribute("name","emailAddressInvalidFlag[]");newContentInvalidFlag.setAttribute("id","emailAddressInvalidFlag"+numberEmailAddresses);newContentInvalidFlag.setAttribute("value","emailAddress"+numberEmailAddresses);newContentInvalidFlag.setAttribute("enabled","true");newContentInvalidFlag['onClick']=function(){var form=document.forms[emailView];if(!form){form=document.forms['editContactForm'];}
var nav=new String(navigator.appVersion);if(nav.match(/MSIE/gim)){for(i=0;i<form.elements.length;i++){var id=new String(form.elements[i].id);if(id.match(/emailAddressInvalidFlag/gim)&&form.elements[i].type=='checkbox'&&id!=this.id){form.elements[i].checked=false;}}
this.checked=true;}}
newContentVerifiedFlag.setAttribute("type","hidden");newContentVerifiedFlag.setAttribute("name","emailAddressVerifiedFlag"+numberEmailAddresses);newContentVerifiedFlag.setAttribute("id","emailAddressVerifiedFlag"+numberEmailAddresses);newContentVerifiedFlag.setAttribute("value","true");newContentVerifiedValue.setAttribute("type","hidden");newContentVerifiedValue.setAttribute("name","emailAddressVerifiedValue"+numberEmailAddresses);newContentVerifiedValue.setAttribute("id","emailAddressVerifiedValue"+numberEmailAddresses);newContentVerifiedValue.setAttribute("value",address);emailView=(emailView=='')?'EditView':emailView;addToValidateVerified(emailView,"emailAddressVerifiedFlag"+numberEmailAddresses,'bool',false,SUGAR.language.get('app_strings','LBL_VERIFY_EMAIL_ADDRESS'));tr.setAttribute("id","emailAddressRow"+numberEmailAddresses);td1.setAttribute("class","tabEditViewDF");td1.setAttribute("nowrap","NOWRAP");td2.setAttribute("class","dataLabel");td3.setAttribute("class","dataLabel");td4.setAttribute("class","dataLabel");td5.setAttribute("class","dataLabel");td6.setAttribute("class","dataLabel");td7.setAttribute("class","dataLabel");td8.setAttribute("class","dataLabel");td1.setAttribute("className","dataLabel");td2.setAttribute("className","dataLabel");td3.setAttribute("className","dataLabel");td4.setAttribute("className","dataLabel");td5.setAttribute("className","dataLabel");td6.setAttribute("className","dataLabel");td7.setAttribute("className","dataLabel");td8.setAttribute("className","dataLabel");td1.appendChild(newContent);td1.appendChild(document.createTextNode(" "));td2.appendChild(removeButton);td3.appendChild(newContentPrimaryFlag);td4.appendChild(newContentReplyToFlag);td5.appendChild(newContentOptOutFlag);td6.appendChild(newContentInvalidFlag);td7.appendChild(newContentVerifiedFlag);td8.appendChild(newContentVerifiedValue);tr.appendChild(td1);tr.appendChild(td2);tr.appendChild(td3);if(typeof(module)!='undefined'&&module=='Users'){tr.appendChild(td4);}else{tr.appendChild(td5);tr.appendChild(td6);}
tr.appendChild(td7);tr.appendChild(td8);tbody.appendChild(tr);insertInto.appendChild(tbody);parentObj.insertBefore(document.getElementById('targetBody'),insertInto);if(primaryFlag=='1'||(numberEmailAddresses==0)){newContentPrimaryFlag.setAttribute("checked",'true');}
if(replyToFlag=='1'){newContentReplyToFlag.setAttribute("checked","true");}
if(replyToFlag=='1'){replyToFlagObject[newContentReplyToFlag.id]=true;}else{replyToFlagObject[newContentReplyToFlag.id]=false;}
if(optOutFlag=='1'){newContentOptOutFlag.setAttribute("checked",'true');}
if(invalidFlag=='1'){newContentInvalidFlag.setAttribute("checked","true");}
newContent.onblur=retrieveEmailAddress;newContent.onkeydown=handleKeyDown;addToValidate(emailView,'emailAddress'+numberEmailAddresses,'email',false,SUGAR.language.get('app_strings','LBL_EMAIL_ADDRESS_BOOK_EMAIL_ADDR'));numberEmailAddresses++;}
function forceSubmit(){theForm=YAHOO.util.Dom.get(emailView);if(theForm){theForm.action.value='Save';if(!check_form(emailView)){return false;}
if(emailView=='EditView'){theForm.submit();}else if(emailView=='QuickCreate'){SUGAR.subpanelUtils.inlineSave(theForm.id,theForm.module.value.toLowerCase());}}}
