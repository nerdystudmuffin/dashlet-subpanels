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

 * Description:  Defines the English language pack for the base application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$mod_strings = array (
	'LBL_FW'					=> 'FW:',
	'LBL_RE'					=> 'RE:',

	'LBL_BUTTON_CREATE'					=> 'Create',
	'LBL_BUTTON_EDIT'					=> 'Edit',
	'LBL_QS_DISABLED'                   => '(QuickSearch is not availible for this module. Please use the select button.)',
	'LBL_SIGNATURE_PREPEND'				=> 'Signature above reply?',
    'LBL_EMAIL_DEFAULT_DESCRIPTION' 	=> 'Here is the quote you requested (You can change this text)',
    'LBL_EMAIL_QUOTE_FOR' => 'Quote for: ',
    'LBL_QUOTE_LAYOUT_DOES_NOT_EXIST_ERROR' => 'quote layout file does not exist: $layout',
    'LBL_QUOTE_LAYOUT_REGISTERED_ERROR' => 'quote layout is not registered in modules/Quotes/Layouts.php',


	'LBL_CONFIRM_DELETE'		=> 'Are you sure you want to delete this folder?',







	'ERR_ARCHIVE_EMAIL'			=> 'Error: Select emails to archive.',
	'ERR_DATE_START'			=> 'Date Start',
	'ERR_DELETE_RECORD'			=> 'Error: You must specify a record number to delete the account.',
	'ERR_NOT_ADDRESSED'			=> 'Error: Email must have a To, CC, or BCC address',
	'ERR_TIME_START'			=> 'Time Start',
	'ERR_TIME_SENT'				=> 'Time Sent',
	'LBL_ACCOUNTS_SUBPANEL_TITLE'=> 'Accounts',
	'LBL_ADD_ANOTHER_FILE'		=> 'Add Another File',
    'LBL_ADD_DASHLETS'          => 'Add Sugar Dashlets',
	'LBL_ADD_DOCUMENT'			=> 'Add Documents',
	'LBL_ADD_ENTRIES'           => 'Add Entries',
	'LBL_ADD_FILE'				=> 'Add Files',
	'LBL_ARCHIVED_EMAIL'		=> 'Archived Email',
	'LBL_ARCHIVED_MODULE_NAME'	=> 'Create Archived Emails',
	'LBL_ATTACHMENTS'			=> 'Attachments:',
	'LBL_BCC'					=> 'Bcc:',
	'LBL_BODY'					=> 'Body:',
	'LBL_BUGS_SUBPANEL_TITLE'	=> 'Bugs',
	'LBL_CC'					=> 'Cc:',
	'LBL_COLON'					=> ':',
	'LBL_COMPOSE_MODULE_NAME'	=> 'Compose Email',
	'LBL_CONTACT_FIRST_NAME'	=> 'Contact First Name',
	'LBL_CONTACT_LAST_NAME'		=> 'Contact Last Name',
	'LBL_CONTACT_NAME'			=> 'Contact:',
	'LBL_CONTACTS_SUBPANEL_TITLE'=> 'Contacts',
	'LBL_CREATED_BY'			=> 'Created by',
	'LBL_DATE_AND_TIME'			=> 'Date & Time Sent:',
	'LBL_DATE_SENT'				=> 'Date Sent:',
	'LBL_DATE'					=> 'Date Sent:',
    'LBL_DELETE_FROM_SERVER'    => 'Delete message from server',
	'LBL_DESCRIPTION'			=> 'Description',
	'LBL_EDIT_ALT_TEXT'			=> 'Edit Plain Text',
	'LBL_EDIT_MY_SETTINGS'		=> 'Edit My Settings',
	'LBL_EMAIL_ATTACHMENT'		=> 'Email Attachment',
	'LBL_EMAIL_EDITOR_OPTION'	=> 'Send HTML Email',
	'LBL_EMAIL_SELECTOR'		=> 'Select',
	'LBL_EMAIL'					=> 'Email:',
	'LBL_EMAILS_ACCOUNTS_REL'	=> 'Emails:Accounts',
	'LBL_EMAILS_BUGS_REL'		=> 'Emails:Bugs',
	'LBL_EMAILS_CASES_REL'		=> 'Emails:Cases',
	'LBL_EMAILS_CONTACTS_REL'	=> 'Emails:Contacts',
	'LBL_EMAILS_LEADS_REL'		=> 'Emails:Leads',
	'LBL_EMAILS_OPPORTUNITIES_REL'=> 'Emails:Opportunities',
    'LBL_EMAILS_NOTES_REL'      => 'Emails:Notes',
	'LBL_EMAILS_PROJECT_REL'	=> 'Emails:Project',
	'LBL_EMAILS_PROJECT_TASK_REL'=> 'Emails:ProjectTask',
	'LBL_EMAILS_PROSPECT_REL'	=> 'Emails:Prospect',
	'LBL_EMAILS_TASKS_REL'		=> 'Emails:Tasks',
	'LBL_EMAILS_USERS_REL'		=> 'Emails:Users',
    'LBL_EMPTY_FOLDER'          => 'No Emails to display',
	'LBL_ERROR_SENDING_EMAIL'	=> 'Error Sending email',
	'LBL_FORWARD_HEADER'		=> 'Begin forwarded message:',
	'LBL_FROM_NAME'				=> 'From Name',
	'LBL_FROM'					=> 'From:',
	'LBL_REPLY_TO'				=> 'Reply To:',
	'LBL_HTML_BODY'				=> 'HTML Body',
	'LBL_INVITEE'				=> 'Recipients',
	'LBL_LEADS_SUBPANEL_TITLE'	=> 'Leads',
	'LBL_MESSAGE_SENT'			=> 'Message Sent',
	'LBL_MODIFIED_BY'			=> 'Modified By',
	'LBL_MODULE_NAME_NEW'		=> 'Archive Email',
	'LBL_MODULE_NAME'			=> 'All Emails',
	'LBL_MODULE_TITLE'			=> 'Emails: ',
	'LBL_NEW_FORM_TITLE'		=> 'Archive Email',
	'LBL_NONE'                  => 'None',
	'LBL_NOT_SENT'				=> 'Send Error',
	'LBL_NOTE_SEMICOLON'		=> 'Note: Use commas or semi-colons as separators for multiple email addresses.',
	'LBL_NOTES_SUBPANEL_TITLE'	=> 'Attachments',
	'LBL_OPPORTUNITY_SUBPANEL_TITLE' => 'Opportunities',
	'LBL_PROJECT_SUBPANEL_TITLE'=> 'Projects',
	'LBL_PROJECT_TASK_SUBPANEL_TITLE'=> 'Project Tasks',
    'LBL_RAW'                  => 'Raw Email',
	'LBL_SAVE_AS_DRAFT_BUTTON_KEY'=> 'R',
	'LBL_SAVE_AS_DRAFT_BUTTON_LABEL'=> 'Save Draft',
	'LBL_SAVE_AS_DRAFT_BUTTON_TITLE'=> 'Save Draft [Alt+R]',
	'LBL_SEARCH_FORM_DRAFTS_TITLE'=> 'Search Drafts',
	'LBL_SEARCH_FORM_SENT_TITLE'=> 'Search Sent Emails',
	'LBL_SEARCH_FORM_TITLE'		=> 'Email Search',
	'LBL_SEND_ANYWAYS'			=> 'This email has no subject.  Send/save anyway?',
	'LBL_SEND_BUTTON_KEY'		=> 'S',
	'LBL_SEND_BUTTON_LABEL'		=> 'Send',
	'LBL_SEND_BUTTON_TITLE'		=> 'Send [Alt+S]',
	'LBL_SEND'					=> 'SEND',
	'LBL_SENT_MODULE_NAME'		=> 'Sent Emails',
	'LBL_SHOW_ALT_TEXT'			=> 'Show Plain Text',
	'LBL_SIGNATURE'				=> 'Signature',
	'LBL_SUBJECT'				=> 'Subject:',
	'LBL_TEXT_BODY'				=> 'Text Body',
	'LBL_TIME'					=> 'Time Sent:',
	'LBL_TO_ADDRS'				=> 'To',
	'LBL_USE_TEMPLATE'			=> 'Use Template:',
	'LBL_USERS_SUBPANEL_TITLE'	=> 'Users',
	'LBL_USERS'					=> 'Users',

	'LNK_ALL_EMAIL_LIST'		=> 'All Emails',
	'LNK_ARCHIVED_EMAIL_LIST'	=> 'Archived Emails',
	'LNK_CALL_LIST'				=> 'Calls',
	'LNK_DRAFTS_EMAIL_LIST'		=> 'All Drafts',
	'LNK_EMAIL_LIST'			=> 'Emails',
	'LBL_EMAIL_RELATE'          => 'Relate To',
	'LNK_EMAIL_TEMPLATE_LIST'	=> 'Email Templates',
	'LNK_MEETING_LIST'			=> 'Meetings',
	'LNK_NEW_ARCHIVE_EMAIL'		=> 'Create Archived Email',
	'LNK_NEW_CALL'				=> 'Schedule Call',
	'LNK_NEW_EMAIL_TEMPLATE'	=> 'Create Email Template',
	'LNK_NEW_EMAIL'				=> 'Archive Email',
	'LNK_NEW_MEETING'			=> 'Schedule Meeting',
	'LNK_NEW_NOTE'				=> 'Create Note or Attachment',
	'LNK_NEW_SEND_EMAIL'		=> 'Compose Email',
	'LNK_NEW_TASK'				=> 'Create Task',
	'LNK_NOTE_LIST'				=> 'Notes',
	'LNK_SENT_EMAIL_LIST'		=> 'Sent Emails',
	'LNK_TASK_LIST'				=> 'Tasks',
	'LNK_VIEW_CALENDAR'			=> 'Today',

	'LBL_LIST_ASSIGNED'			=> 'Assigned',
	'LBL_LIST_CONTACT_NAME'		=> 'Contact Name',
	'LBL_LIST_CREATED'			=> 'Created',
	'LBL_LIST_DATE_SENT'		=> 'Date Sent',
	'LBL_LIST_DATE'				=> 'Date Sent',
	'LBL_LIST_FORM_DRAFTS_TITLE'=> 'Draft',
	'LBL_LIST_FORM_SENT_TITLE'	=> 'Sent Emails',
	'LBL_LIST_FORM_TITLE'		=> 'Email List',
	'LBL_LIST_FROM_ADDR'		=> 'From',
	'LBL_LIST_RELATED_TO'		=> 'Related to',
	'LBL_LIST_SUBJECT'			=> 'Subject',
	'LBL_LIST_TIME'				=> 'Time Sent',
	'LBL_LIST_TO_ADDR'			=> 'To',
	'LBL_LIST_TYPE'				=> 'Type',

	'NTC_REMOVE_INVITEE'		=> 'Are you sure you want to remove this recipient from the email?',
	'WARNING_SETTINGS_NOT_CONF'	=> 'Warning: Your email settings are not configured to send email.',
	'WARNING_NO_UPLOAD_DIR'		=> 'Attachments may fail: No value for "upload_tmp_dir" was detected.  Please correct this in your php.ini file.',
	'WARNING_UPLOAD_DIR_NOT_WRITABLE'	=> 'Attachments may fail: An incorrect or unusable value for "upload_tmp_dir" was detected.  Please correct this in your php.ini file.',

    // for All emails
    'LBL_BUTTON_RAW_TITLE'   => 'Show Raw Message [Alt+E]',
    'LBL_BUTTON_RAW_KEY'     => 'e',
    'LBL_BUTTON_RAW_LABEL'   => 'Show Raw',
    'LBL_BUTTON_RAW_LABEL_HIDE' => 'Hide Raw',

	// for InboundEmail
	'LBL_BUTTON_CHECK'			=> 'Check Mail',
	'LBL_BUTTON_CHECK_TITLE'	=> 'Check For New Email [Alt+C]',
	'LBL_BUTTON_CHECK_KEY'		=> 'c',
	'LBL_BUTTON_FORWARD'		=> 'Forward',
	'LBL_BUTTON_FORWARD_TITLE'	=> 'Forward This Email [Alt+F]',
	'LBL_BUTTON_FORWARD_KEY'	=> 'f',
	'LBL_BUTTON_REPLY_KEY'		=> 'r',
	'LBL_BUTTON_REPLY_TITLE'	=> 'Reply [Alt+R]',
	'LBL_BUTTON_REPLY'			=> 'Reply',
	'LBL_CASES_SUBPANEL_TITLE'	=> 'Cases',
	'LBL_INBOUND_TITLE'			=> 'Inbound Email',
	'LBL_INTENT'				=> 'Intent',
	'LBL_MESSAGE_ID'			=> 'Message ID',
	'LBL_REPLY_HEADER_1'		=> 'On ',
	'LBL_REPLY_HEADER_2'		=> 'wrote:',
	'LBL_REPLY_TO_ADDRESS'		=> 'Reply-to Address',
	'LBL_REPLY_TO_NAME'			=> 'Reply-to Name',

	'LBL_LIST_BUG'				=> 'Bugs',
	'LBL_LIST_CASE'				=> 'Cases',
	'LBL_LIST_CONTACT'			=> 'Contacts',
	'LBL_LIST_LEAD'				=> 'Leads',
	'LBL_LIST_TASK'				=> 'Tasks',
	'LBL_LIST_ASSIGNED_TO_NAME' => 'Assigned User',

	// for Inbox
	'LBL_ALL'					=> 'All',
	'LBL_ASSIGN_WARN'			=> 'Ensure that all 2 options are selected.',
	'LBL_BACK_TO_GROUP'			=> 'Back to Group Inbox',
	'LBL_BUTTON_DISTRIBUTE_KEY'	=> 'a',
	'LBL_BUTTON_DISTRIBUTE_TITLE'=> 'Assign [Alt+A]',
	'LBL_BUTTON_DISTRIBUTE'		=> 'Assign',
	'LBL_BUTTON_GRAB_KEY'		=> 't',
	'LBL_BUTTON_GRAB_TITLE'		=> 'Take from Group [Alt+T]',
	'LBL_BUTTON_GRAB'			=> 'Take from Group',
	'LBL_CREATE_BUG'			=> 'Create Bug',
	'LBL_CREATE_CASE'			=> 'Create Case',
	'LBL_CREATE_CONTACT'		=> 'Create Contact',
	'LBL_CREATE_LEAD'			=> 'Create Lead',
	'LBL_CREATE_TASK'			=> 'Create Task',
	'LBL_DIST_TITLE'			=> 'Assignment',
	'LBL_LOCK_FAIL_DESC'		=> 'The chosen item is unavailable currently.',
	'LBL_LOCK_FAIL_USER'		=> ' has taken ownership.',
	'LBL_MASS_DELETE_ERROR'		=> 'No checked items were passed for deletion.',
	'LBL_NEW'					=> 'New',
	'LBL_NEXT_EMAIL'			=> 'Next Free Item',
	'LBL_NO_GRAB_DESC'			=> 'There were no items available.  Try again in a moment.',
	'LBL_QUICK_REPLY'			=> 'Reply',
	'LBL_REPLIED'				=> 'Replied',
	'LBL_SELECT_TEAM'			=> 'Select Teams',
	'LBL_TAKE_ONE_TITLE'		=> 'Reps',
	'LBL_TITLE_SEARCH_RESULTS'	=> 'Search Results',
	'LBL_TO'					=> 'To: ',
	'LBL_TOGGLE_ALL'			=> 'Toggle All',
	'LBL_UNKNOWN'				=> 'Unknown',
	'LBL_UNREAD_HOME'			=> 'Unread Emails',
	'LBL_UNREAD'				=> 'Unread',
	'LBL_USE_ALL'				=> 'All Search Results',
	'LBL_USE_CHECKED'			=> 'Only Checked',
	'LBL_USE_MAILBOX_INFO'		=> 'Use Mailbox From: Address',
	'LBL_USE'					=> 'Assign:',
	'LBL_ASSIGN_SELECTED_RESULTS_TO' => 'Assign Selected Results To: ',
	'LBL_USER_SELECT'			=> 'Select Users',
	'LBL_USING_RULES'			=> 'Using Rules:',
	'LBL_WARN_NO_DIST'			=> 'No Distribution Method Selected',
	'LBL_WARN_NO_USERS'			=> 'No Users are selected',

	'LBL_LIST_STATUS'			=> 'Status',
	'LBL_LIST_TITLE_GROUP_INBOX'=> 'Group Inbox',
	'LBL_LIST_TITLE_MY_DRAFTS'	=> 'My Drafts',
	'LBL_LIST_TITLE_MY_INBOX'	=> 'My Inbox',
	'LBL_LIST_TITLE_MY_SENT'	=> 'My Sent Email',
	'LBL_LIST_TITLE_MY_ARCHIVES'=> 'My Archived Emails',

	'LNK_CHECK_MY_INBOX'		=> 'Check My Mail',
	'LNK_DATE_SENT'				=> 'Date Sent',
	'LNK_GROUP_INBOX'			=> 'Group Inbox',
	'LNK_MY_DRAFTS'				=> 'My Drafts',
	'LNK_MY_INBOX'				=> 'My Email',
	'LNK_QUICK_REPLY'			=> 'Reply',
	'LNK_MY_ARCHIVED_LIST'		=> 'My Archives',

	// advanced search
	'LBL_ASSIGNED_TO'			=> 'Assigned To:',
	'LBL_MEMBER_OF'				=> 'Parent',
	'LBL_QUICK_CREATE'			=> 'Quick Create',
	'LBL_STATUS'				=> 'Email Status:',
	'LBL_EMAIL_FLAGGED'			=> 'Flagged:',
	'LBL_EMAIL_REPLY_TO_STATUS'	=> 'Reply To Status:',
	'LBL_TYPE'					=> 'Type:',
	//#20680 EmialTemplate Ext.Message.show;
	'LBL_EMAILTEMPLATE_MESSAGE_SHOW_TITLE' => 'Please check!',
	'LBL_EMAILTEMPLATE_MESSAGE_SHOW_MSG' => 'Selecting this template will overwrite any data already entered within the email body. Do you wish to continue?',
	'LBL_CHECK_ATTACHMENTS'=>'Please Check Attachments!',
	'LBL_HAS_ATTACHMENTS' => 'This email already has attachment(s). Would you like to keep the attachment(s)?',	
);
