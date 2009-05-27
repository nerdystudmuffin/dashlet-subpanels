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

 * Description:
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights
 * Reserved. Contributor(s): ______________________________________..
 * *******************************************************************************/

$mod_strings = array(










	



































	
	'DEFAULT_CHARSET'					=> 'UTF-8',
	'ERR_ADMIN_PASS_BLANK'				=> 'SugarCRM管理员密码不能为空。',
	
    //'ERR_CHECKSYS_CALL_TIME'			=> 'Allow Call Time Pass Reference is Off (please enable in php.ini)',
	  'ERR_CHECKSYS'                      => '兼容性检测过程中发现错误.  为了使SugarCRM正常安装, 请采取适当的方法定位到以下问题或者点击重新验证按钮，或者重新安装一次.', 
    'ERR_CHECKSYS_CALL_TIME'            => 'Allow Call TimePass Reference已打开(可以在php.ini文件中设置为“关闭”)',
    'ERR_CHECKSYS_CURL'					=> '未发现:Sugar工作计划将会在限制的功能下运行。',
  'ERR_CHECKSYS_IMAP'					=> '未发现:收件箱和营销活动(电子邮件)必须要有IMAP类库。它们不会被运行。',
	'ERR_CHECKSYS_MSSQL_MQGPC'			=> '当使用MSSQLServer数据库的时候，MagicQuotesGPC不能被打开。',
	'ERR_CHECKSYS_MBSTRING'				=> '未发现:SugarCRM将不能处理多字节字符。这会影响接收类似UTF-8字节的电子邮件。',
	'ERR_CHECKSYS_SESSION_SAVE_PATH_NOT_SET'       => 'PHP配置文件中session.save_path没有配置或者配置的目录不存在. 您需要检查配置文件.',
    'ERR_CHECKSYS_SESSION_SAVE_PATH_NOT_WRITABLE'  => 'PHP配置文件中session.save_path指定的目录不可写. 请执行必要的操作使之可写. <br>根据您的操作系统类型,你可能需要执行chmod 766或者右键文件夹访问属性并取消只读属性.',
	'ERR_CHECKSYS_MEM_LIMIT_0'			=> '警告:',
	'ERR_CHECKSYS_MEM_LIMIT_1'			=> '(设置这个为',
	'ERR_CHECKSYS_MEM_LIMIT_2'			=> 'M或者更大在您的php.ini文件中）',
	'ERR_CHECKSYS_MYSQL_VERSION'		=> '最小版本4.1.2–发现:',
	'ERR_CHECKSYS_NO_SESSIONS'			=> '读写会话变量失败。不能执行安装。',
	'ERR_CHECKSYS_NOT_VALID_DIR'		=> '不是有效的目录',
	'ERR_CHECKSYS_NOT_WRITABLE'			=> '警告:不可写',
	'ERR_CHECKSYS_PHP_INVALID_VER'		=> '安装了无效的PHP版本:(版本',
	'ERR_CHECKSYS_PHP_UNSUPPORTED'		=> '安装了不支持的PHP版本:(版本',
  //'ERR_CHECKSYS_SAFE_MODE'			=> 'Safe Mode is On (please disable in php.ini)',
  'LBL_DB_UNAVAILABLE'                => '数据库不可用',
    'LBL_CHECKSYS_DB_SUPPORT_NOT_AVAILABLE' => '没找到数据库.  请确定相应以下将要使用的数据库驱动已被安装: MySQL, MS SQLServer, or Oracle.  可能需要在php.ini文件去除相应的注释使语句生效, 或者根据PHP版本重新编译相应的binary文件.  请参阅PHP帮助来获取如何支持相应的数据库的信息.',
    'LBL_CHECKSYS_XML_NOT_AVAILABLE'        => '没有找到和XML解析库相关的方法.  可能需要在php.ini文件去除相应的注释使语句生效, 或者根据PHP版本重新编译相应的binary文件.  请参阅PHP帮助来获取如何支持相应的数据库的信息.',
    'ERR_CHECKSYS_CONFIG_NOT_WRITABLE'  => '配置文件存在但是没有写权限.  请采取相应的方法使文件可写.  根据操作系统可能需要通过以下方式修改权限- 执行"chmod 766", 或者右键点击文件名, 选择属性, 不选择只读选项.',
    'ERR_CHECKSYS_CUSTOM_NOT_WRITABLE'  => '指定路径存在但是不可写.  请采取相应的方法使文件可写.  根据操作系统可能需要通过以下方式修改权限- 执行"chmod 766", 或者右键点击文件名, 选择属性, 不选择只读选项.',
    'ERR_CHECKSYS_FILES_NOT_WRITABLE'   => "以下列出的文件或目录没有写权限或者丢失并且这些丢失的文件和目录不能创建.  根据操作系统可能需要通过以下方式修改权限- 执行'chmod 766' 修改文件或父目录权限, 或者右键点击文件或目录名, 选择属性, 不选择只读选项并应用于所有子目录.",
	//'ERR_CHECKSYS_SAFE_MODE'			=> 'Safe Mode is On (please disable in php.ini)',
	'ERR_CHECKSYS_SAFE_MODE'			=> '安全模式打开(您可以在php.ini文件中关闭它)',
    'ERR_CHECKSYS_ZLIB'					=> '未发现:zlib压缩可以使SugarCRM获取巨大的性能。',
	'ERR_DB_EXISTS_NOT'					=> '指定的数据库不存在。',
  'ERR_DB_ADMIN'						=> '数据库管理员用户名/密码无效(错误',
  'ERR_DB_ADMIN_MSSQL'                => '输入的数据库管理员的用户名/密码无效, 无法和数据库建立连接.  请输入有效的用户名和密码.',
  'ERR_DB_EXISTS_WITH_CONFIG'			=> '数据库已存在。使用选择的数据库运行安装，请重新运行安装并选择:“删除并重新创建已存在的SugarCRM表”。要更新的话，请使用管理员控制台下的更新向导。请阅读<a href="http://www.sugarforge.org/content/downloads/"target="_new">这儿</a>的更新文档。',
	'ERR_DB_EXISTS'						=> '数据库名已存在–不能创建相同名字的数据库。',
  'ERR_DB_EXISTS_PROCEED'             => '数据库名已存在.  可以<br>1.  点击后退按钮选择另一个数据库名 <br>2.  点击下一步继续安装,这将会覆盖数据库中所有存在的表.  <strong>这意味着所有的表以及其中的数据将被清空.</strong>',
	'ERR_DB_HOSTNAME'					=> '主机名不能为空。',
	'ERR_DB_INVALID'					=> '选择了无效的数据库类型。',
	'ERR_DB_LOGIN_FAILURE_MYSQL'		=> 'SugarCRM数据库用户名/密码无效(错误',
	'ERR_DB_LOGIN_FAILURE_MSSQL'		=> 'SugarCRM数据库用户名/密码无效',
	'ERR_DB_MYSQL_VERSION1'				=> 'MySQL版本',
	'ERR_DB_MYSQL_VERSION2'				=> '不支持。只支持MySQL4.1.x和更高版本。',
	'ERR_DB_NAME'						=> '数据库名不能为空。',
	'ERR_DB_NAME2'						=> "数据库名不能包含“\”“/”“.”。",
	'ERR_DB_PASSWORD'					=> 'SugarCRM的密码不匹配。',
	'ERR_DB_PRIV_USER'					=> '必须要有数据库管理员用户名。',
	'ERR_DB_USER_EXISTS'				=> 'SugarCRM用户名已存在–不能创建一个相同的用户名。',
	'ERR_DB_USER'						=> 'SugarCRM用户名不能为空。',
	'ERR_DBCONF_VALIDATION'				=> '在继续执行前，请修复下面的错误:',
  'ERR_DBCONF_PASSWORD_MISMATCH'      => '输入的验证密码和密码不一致. 其输入和密码域中相同的密码.',
	'ERR_ERROR_GENERAL'					=> '遇到了下面的错误:',
	'ERR_LANG_CANNOT_DELETE_FILE'		=> '不能删除文件:',
	'ERR_LANG_MISSING_FILE'				=> '没有发现文件:',
	'ERR_LANG_NO_LANG_FILE'			 	=> '没有在文件夹include/language中发现语言包文件。',
	'ERR_LANG_UPLOAD_1'					=> '上传出现问题。请重试。',
	'ERR_LANG_UPLOAD_2'					=> '语言包必须是ZIP文件。',
	'ERR_LANG_UPLOAD_3'					=> 'PHP不能移动临时文件到更新目录。',
	'ERR_LICENSE_MISSING'				=> '缺少必要的字段',
	'ERR_LICENSE_NOT_FOUND'				=> '没有发现许可证文件！',
	'ERR_LOG_DIRECTORY_NOT_EXISTS'		=> '无效的日志目录。',
	'ERR_LOG_DIRECTORY_NOT_WRITABLE'	=> '不可写的日志目录。',
	'ERR_LOG_DIRECTORY_REQUIRED'		=> '如果您要指定自己的日志目录，那么日志目录是必要的。',
	'ERR_NO_DIRECT_SCRIPT'				=> '不能直接运行脚本。',
	'ERR_NO_SINGLE_QUOTE'				=> '不可以使用单引号',
	'ERR_PASSWORD_MISMATCH'				=> 'SugarCRM管理员的密码不匹配。',
	'ERR_PERFORM_CONFIG_PHP_1'			=> '不能写入<spanclass=stop>config.php</span>文件。',
	'ERR_PERFORM_CONFIG_PHP_2'			=> '您可以通过手工创建config.php文件，并且粘贴下面的配置信息到config.php文件中来完成安装。然而，在继续下一步之前，您<strong>必须</strong>创建config.php文件。',
	'ERR_PERFORM_CONFIG_PHP_3'			=> '您记得创建config.php文件了吗?',
	'ERR_PERFORM_CONFIG_PHP_4'			=> '警告:不能写入到config.php文件。请确定它是否存在。',
	'ERR_PERFORM_HTACCESS_1'			=> '不能写入到',
	'ERR_PERFORM_HTACCESS_2'			=> '文件。',
	'ERR_PERFORM_HTACCESS_3'			=> '如果您想保护可以通过浏览器访问的日志文件，请在您的根目录下创建一个.htaccess文件，并加上这些行:',
	'ERR_PERFORM_NO_TCPIP'				=> '<b>我们不能检测到互联网连接。</b>当您可以连接的时候，请访问<a href=\"http://www.sugarcrm.com/home/index.php?option=com_extended_registration&task=register\">http://www.sugarcrm.com/home/index.php?option=com_extended_registration&task=register</a>来注册SugarCRM。通过了解贵公司对SugarCRM的使用，我们可以确保发布合适的应用程序来满足您的商业需求。',
	'ERR_SESSION_DIRECTORY_NOT_EXISTS'	=> '无效的会话目录。',
	'ERR_SESSION_DIRECTORY'				=> '不可写的会话目录。',
	'ERR_SESSION_PATH'					=> '如果您要指定您自己的会话路径，那么会话路径是必须的。',
	'ERR_SI_NO_CONFIG'					=> '在您的文档根目录下，您没有包含config_si.php文件，或者您没有在config.php文件中定义变量$sugar_config_si。',
	'ERR_SITE_GUID'						=> '如果您要指定您自己的应用程序编号，那么应用程序编号是必须的。',
	'ERR_UPLOAD_MAX_FILESIZE'			=> '警告:您的PHP配置必须允许至少6M的文件上传。',
	'LBL_UPLOAD_MAX_FILESIZE_TITLE'     => '上传文件大小',
  'ERR_URL_BLANK'						=> '网址不能为空。',
	'ERR_UW_NO_UPDATE_RECORD'			=> '不能定位安装记录',
	'ERROR_FLAVOR_INCOMPATIBLE'			=> '上传的文件和Sugar Suite的版本(开源版，专业版，企业版)不兼容:',
	'ERROR_LICENSE_EXPIRED'				=> "错误:您的许可证已过期",
	'ERROR_LICENSE_EXPIRED2'			=> "天以前。请到管理员界面下的<a href='index.php?action=LicenseSettings&module=Administration'>'“许可证管理”</a>，输入您新的许可证密钥。如果您在当前许可证密钥过期30天后还不输入新的许可证密钥，那么您将不能够再登录这个应用程序。",
	'ERROR_MANIFEST_TYPE'				=> '名单文件必须指定程序包类型。',
	'ERROR_PACKAGE_TYPE'				=> '名单文件指定了一个不能识别的程序包类型。',
	'ERROR_VALIDATION_EXPIRED'			=> "错误:您的验证密钥已过期",
	'ERROR_VALIDATION_EXPIRED2'			=> "天以前。请到管理员界面下的<a href='index.php?action=LicenseSettings&module=Administration'>'“许可证管理”</a>，输入您新的验证密钥。如果您在当前许可证密钥过期30天后还不输入新的许可证密钥，那么您将不能够再登录这个应用程序。",
	'ERROR_VERSION_INCOMPATIBLE'		=> '上传的文件和当前版本的Sugar Suite不兼容:',
	
	'LBL_BACK'							=> '上一步',
  'LBL_CANCEL'                        => '取消',
  'LBL_ACCEPT'                        => '同意',
	'LBL_CHECKSYS_1'					=> '为了保证您的SugarCRM完全安装，请确保下列所有系统检测记录都是绿色的。如果有任何一个是红色的，请采取必要的措施来修复它。<BR><BR>要得到这些系统检测的帮助，请访问<a href="http://www.sugarcrm.com/crm/installation"target="_blank">SugarWiki</a>。',
	'LBL_CHECKSYS_CACHE'				=> '可写缓存字目录',
	//'LBL_CHECKSYS_CALL_TIME'			=> 'PHP Allow Call Time Pass Reference Turned On',
  'LBL_DROP_DB_CONFIRM'               => 'T数据库名已经存在.<br>可以选择:<br>1.  点击取消按钮选择新的数据库名, 或者 <br>2.  点击同意按钮进入下一步.  数据库中所有的表将会被删除. <strong>这意味着所有的表以及其中存在的数据将会被删除.</strong>',
	'LBL_CHECKSYS_CALL_TIME'			=> 'PHP Allow Call Time Pass Reference已关闭',
  'LBL_CHECKSYS_COMPONENT'			=> '组件',
	'LBL_CHECKSYS_COMPONENT_OPTIONAL'	=> '可选组件',
	'LBL_CHECKSYS_CONFIG'				=> '可写的SugarCRM配置文件(config.php)',
	'LBL_CHECKSYS_CURL'					=> 'CURL模式',
	'LBL_CHECKSYS_SESSION_SAVE_PATH'    => 'Session保存路径配置',
	'LBL_CHECKSYS_CUSTOM'				=> '可写的自定义目录',
	'LBL_CHECKSYS_DATA'					=> '可写的数据子目录',
	'LBL_CHECKSYS_IMAP'					=> 'IMAP模式',
	'LBL_CHECKSYS_MQGPC'				=> 'Magic Quotes GPC',
	'LBL_CHECKSYS_MBSTRING'				=> 'MBString模式',
	'LBL_CHECKSYS_MEM_OK'				=> '好(没有限制)',
	'LBL_CHECKSYS_MEM_UNLIMITED'		=> '好(没有限制)',
	'LBL_CHECKSYS_MEM'					=> 'PHP内存限制>=',
	'LBL_CHECKSYS_MODULE'				=> '可写的模块子目录和文件',
	'LBL_CHECKSYS_MYSQL_VERSION'		=> 'MySQL版本',
	'LBL_CHECKSYS_NOT_AVAILABLE'		=> '无效',
	'LBL_CHECKSYS_OK'					=> '好',
	'LBL_CHECKSYS_PHP_INI'				=> '<b>注意:</b>PHP配置文件(php.ini)位于:',
	'LBL_CHECKSYS_PHP_OK'				=> '好(版本',
	'LBL_CHECKSYS_PHPVER'				=> 'PHP版本',
	'LBL_CHECKSYS_RECHECK'				=> '重新检查',
	'LBL_CHECKSYS_SAFE_MODE'			=> 'PHP安全模式已关闭',
	'LBL_CHECKSYS_SESSION'				=> '可写的会话保存路径(',
	'LBL_CHECKSYS_STATUS'				=> '状态',
	'LBL_CHECKSYS_TITLE'				=> '接受系统检查',
	'LBL_CHECKSYS_VER'					=> '发现:(版本',
	'LBL_CHECKSYS_XML'					=> 'XML解析',
	'LBL_CHECKSYS_ZLIB'					=> 'ZLIB压缩模式',
  'LBL_CHECKSYS_FIX_FILES'            => '请先修复以下文件和目录:',
  'LBL_CHECKSYS_FIX_MODULE_FILES'     => '请先修复以下模块的文件以及目录',
  'LBL_CLOSE'							=> '关闭',
  'LBL_THREE'                         => '3',
	'LBL_CONFIRM_BE_CREATED'			=> '被创建',
	'LBL_CONFIRM_DB_TYPE'				=> '数据库类型',
	'LBL_CONFIRM_DIRECTIONS'			=> '请确认下面的设置。如果您想更改任何变量，点击“上一步”编辑。显然，点击“下一步”开始安装。',
	'LBL_CONFIRM_LICENSE_TITLE'			=> '许可证信息',
	'LBL_CONFIRM_NOT'					=> '不',
	'LBL_CONFIRM_TITLE'					=> '确认设置',
	'LBL_CONFIRM_WILL'					=> '将',
	'LBL_DBCONF_CREATE_DB'				=> '创建数据库',
	'LBL_DBCONF_CREATE_USER'			=> '新增用户',
	'LBL_DBCONF_DB_DROP_CREATE_WARN'	=> '小心:如果这个复选框被选中，<br>所有Sugar数据将被删除。',
	'LBL_DBCONF_DB_DROP_CREATE'			=> '删除并且重新重建已存在的Sugar表?',
	'LBL_DBCONF_DB_DROP'                => '删除表',
  'LBL_DBCONF_DB_NAME'				=> '数据库名称',
	'LBL_DBCONF_DB_PASSWORD'			=> '数据库密码',
	'LBL_DBCONF_DB_PASSWORD2'			=> '重新输入数据库密码',
	'LBL_DBCONF_DB_USER'				=> '数据库用户名',
  'LBL_DBCONF_SUGAR_DB_USER'          => 'Sugar数据库用户名',
  'LBL_DBCONF_DB_ADMIN_USER'          => '数据库管理员用户名',
  'LBL_DBCONF_DB_ADMIN_PASSWORD'      => '数据库管理员密码',
	'LBL_DBCONF_DEMO_DATA'				=> '导入演示数据到数据库?',
  'LBL_DBCONF_DEMO_DATA_TITLE'              => '导入演示数据',
	'LBL_DBCONF_HOST_NAME'				=> '主机名',
  'LBL_DBCONF_HOST_NAME_MSSQL'        => '主机名\主机实例',
	'LBL_DBCONF_INSTRUCTIONS'			=> '请输入下面的数据库配置信息。如果您不确定要输入什么，我们建议您使用默认值。',
	'LBL_DBCONF_MB_DEMO_DATA'			=> '使用多字节的演示数据?',
	//'LBL_DBCONF_I18NFIX'                => '为varchar 和 char 类型 (达到255字节)多字节数据，应用数据库扩展?',
  'LBL_DBCONFIG_MSG2'                 => '数据库所在服务器或机器(主机)名:',
  'LBL_DBCONFIG_MSG3'                 => 'Sugar实例所在数据库名:',
  'LBL_DBCONFIG_B_MSG1'               => '数据库管理员用户名和密码，管理员能够创建数据库表，用户以及创建Sugar数据库.',
  'LBL_DBCONFIG_SECURITY'             => '出于安全目的, 你可以指定一个外部数据库用户连接到Sugar数据库, 该用户拥有写入, 更新和删除权限.  该用户可以是前面提到的管理员, 新提供的或者是已经存在的用户.',
  'LBL_DBCONFIG_AUTO_DD'              => '这里工作正常',
  'LBL_DBCONFIG_PROVIDE_DD'           => '提供存在的用户',
  'LBL_DBCONFIG_CREATE_DD'            => '定义用户来创建',
  'LBL_DBCONFIG_SAME_DD'              => '和管理员一样',
	//'LBL_DBCONF_I18NFIX'              => 'Apply database column expansion for varchar and char types (up to 255) for multi-byte data?',
  'LBL_MSSQL_FTS'                     => '全文检索',
  'LBL_MSSQL_FTS_INSTALLED'           => '已安装',
  'LBL_MSSQL_FTS_INSTALLED_ERR1'      => '没有安装全文检索.',
  'LBL_MSSQL_FTS_INSTALLED_ERR2'      => '可以继续安装但是将不会具有全文检索功能. 要启用全文检索需要重新安装SQL Server并激活全文检索。  请参阅SQL Server安装手册或者和管理员联系.',
  'LBL_DBCONF_PRIV_PASS'				=> '有特权的数据库用户密码',
	'LBL_DBCONF_PRIV_USER_2'			=> '以上数据库账号是有特权的用户?',
	'LBL_DBCONF_PRIV_USER_DIRECTIONS'	=> '这个有特权的数据库用户必须有完全的权限来创建数据库，删除/创建表，和创建用户。这个特权用户只用于执行安装过程中必须的操作。如果那个用户有足够的权限的话，您也可以使用相同的数据库用户。',
	'LBL_DBCONF_PRIV_USER'				=> '有特权的数据库用户名',
	'LBL_DBCONF_TITLE'					=> '数据库配置',
  'LBL_DBCONF_TITLE_NAME'             => '提供数据库名',
  'LBL_DBCONF_TITLE_USER_INFO'        => '提供数据库用户信息',
	'LBL_DISABLED_DESCRIPTION_2'		=> '更改后，您可以点击下面的“开始”按钮执行安装。<i>在安装完成后，改变标量“installer_locked”为“true”。',
	'LBL_DISABLED_DESCRIPTION'			=> '安装已经执行过一次。作为一项安全措施，禁止再次运行安装。如果您完全确信您要再次安装，请打开config.php文件，找到变量“installer_locked”，并且设置它为“false”。这行看起来像:',
	'LBL_DISABLED_HELP_1'				=> '请访问SugarCRM来获取安装帮助',
	'LBL_DISABLED_HELP_LNK'               => 'http://www.sugarcrm.com/forums/',
  'LBL_DISABLED_HELP_2'				=> '支持论坛',
	'LBL_DISABLED_TITLE_2'				=> 'SugarCRM安装已被禁止',
	'LBL_DISABLED_TITLE'				=> 'SugarCRM安装被禁止',
	'LBL_EMAIL_CHARSET_DESC'			=> '设置您地区常用的字符集',
	'LBL_EMAIL_CHARSET_TITLE'			=> '发件箱字符集',
  'LBL_EMAIL_CHARSET_CONF'            => '发送邮件字符集 ',
	'LBL_HELP'							=> '帮助',
  'LBL_INSTALL'                       => '安装',
  'LBL_INSTALL_TYPE_TITLE'            => '安装选项',
  'LBL_INSTALL_TYPE_SUBTITLE'         => '选择安装类型',
  'LBL_INSTALL_TYPE_TYPICAL'          => ' <b>典型安装</b>',
  'LBL_INSTALL_TYPE_CUSTOM'           => ' <b>自定义安装</b>',
  'LBL_INSTALL_TYPE_MSG1'             => '普通应用功能需要关键字支持, 但不安装程序并不需要. 这个时候您不需要输入关键字, 但您将需要提够关键自在您安装应用程序之后.',
  'LBL_INSTALL_TYPE_MSG2'             => '安装过程中提供最少信息. 推荐新用户使用.',
  'LBL_INSTALL_TYPE_MSG3'             => '安装过程中提供的额外选项, 在安装结束后仍然能在管理员界面找到. 推荐高级用户使用.',
	'LBL_LANG_1'						=> '如果您想安装美国英语以外的语言包，请执行下面的步骤。否则，请点击“下一步”继续安装。',
	'LBL_LANG_BUTTON_COMMIT'			=> '安装',
	'LBL_LANG_BUTTON_REMOVE'			=> '移除',
	'LBL_LANG_BUTTON_UNINSTALL'			=> '删除',
	'LBL_LANG_BUTTON_UPLOAD'			=> '上传',
	'LBL_LANG_NO_PACKS'					=> '无',
	'LBL_LANG_PACK_INSTALLED'			=> '下列语言包将被安装:',
	'LBL_LANG_PACK_READY'				=> '下列语言包已被安装:',
	'LBL_LANG_SUCCESS'					=> '成功上传语言包。',
	'LBL_LANG_TITLE'			   		=> '语言包',
	'LBL_LANG_UPLOAD'					=> '上传语言包',
	'LBL_LICENSE_ACCEPTANCE'			=> '接受许可证',
  'LBL_LICENSE_CHECKING'              => '检测系统兼容性.',
  'LBL_LICENSE_CHKENV_HEADER'         => '检测环境',
  'LBL_LICENSE_CHKDB_HEADER'          => '验证数据库连接.',
  'LBL_LICENSE_CHECK_PASSED'          => '系统通过兼容性检测.',
  'LBL_LICENSE_REDIRECT'              => '重定向中 ',
	'LBL_LICENSE_DIRECTIONS'			=> '如果您有许可证信息，请输入在下面的字段中。',
	'LBL_LICENSE_DOWNLOAD_KEY'			=> '下载密钥',
	'LBL_LICENSE_EXPIRY'				=> '有效期',
	'LBL_LICENSE_I_ACCEPT'				=> '我接受',
	'LBL_LICENSE_NUM_USERS'				=> '用户数',
	'LBL_LICENSE_OC_DIRECTIONS'			=> '请输入购买的离线客户端数',
	'LBL_LICENSE_OC_NUM'				=> '离线客户端许可证数',
	'LBL_LICENSE_OC'					=> '离线客户端许可证',
	'LBL_LICENSE_PRINTABLE'				=> '打印视图',
  'LBL_PRINT_SUMM'                    => '打印摘要',
	'LBL_LICENSE_TITLE_2'				=> 'SugarCRM许可证',
	'LBL_LICENSE_TITLE'					=> '许可证信息',
	'LBL_LICENSE_USERS'					=> '许可用户',
	
	'LBL_LOCALE_CURRENCY'				=> '货币设置',
	'LBL_LOCALE_CURR_DEFAULT'			=> '默认货币',
	'LBL_LOCALE_CURR_SYMBOL'			=> '货币符号',
	'LBL_LOCALE_CURR_ISO'				=> '货币代码(ISO4217)',
	'LBL_LOCALE_CURR_1000S'				=> '千分符',
	'LBL_LOCALE_CURR_DECIMAL'			=> '小数点分隔符',
	'LBL_LOCALE_CURR_EXAMPLE'			=> '实例',
	'LBL_LOCALE_CURR_SIG_DIGITS'		=> '精确度',
	'LBL_LOCALE_DATEF'					=> '默认日期格式',
	'LBL_LOCALE_DESC'					=> '调整下面的SugarCRM本地设置。',
	'LBL_LOCALE_EXPORT'					=> '导入/导出字符集<i>(电子邮件，.csv，vCard，PDF，数据导入)</i>',
	'LBL_LOCALE_EXPORT_DELIMITER'		=> '导出(.csv)分隔符',
	'LBL_LOCALE_EXPORT_TITLE'			=> '导出设置',
	'LBL_LOCALE_LANG'					=> '默认语言',
	'LBL_LOCALE_NAMEF'					=> '默认姓名格式',
	'LBL_LOCALE_NAMEF_DESC'				=> '“s”称谓<br/>“f”名<br/>“l”姓',
	'LBL_LOCALE_NAME_FIRST'				=> '大卫',
	'LBL_LOCALE_NAME_LAST'				=> '利文斯敦',
	'LBL_LOCALE_NAME_SALUTATION'		=> '博士',
	'LBL_LOCALE_TIMEF'					=> '默认时间格式',
	'LBL_LOCALE_TITLE'					=> '区域设置',
  'LBL_CUSTOMIZE_LOCALE'              => '定制区域设置',
	'LBL_LOCALE_UI'						=> '用户界面',
	
	'LBL_ML_ACTION'						=> '行动',
	'LBL_ML_DESCRIPTION'				=> '说明',
	'LBL_ML_INSTALLED'					=> '安装日期',
	'LBL_ML_NAME'						=> '名称',
	'LBL_ML_PUBLISHED'					=> '公布日期',
	'LBL_ML_TYPE'						=> '类型',
	'LBL_ML_UNINSTALLABLE'				=> '可删除',
	'LBL_ML_VERSION'					=> '版本',
	'LBL_MSSQL'							=> 'SQL Server',
  'LBL_MSSQL2'                        => 'SQL Server (FreeTDS)',
	'LBL_MYSQL'							=> 'MySQL',
	'LBL_NEXT'							=> '下一步',
	'LBL_NO'							=> '否',
	'LBL_ORACLE'						=> 'Oracle',
	'LBL_PERFORM_ADMIN_PASSWORD'		=> '设置站点管理员密码',
	'LBL_PERFORM_AUDIT_TABLE'			=> '审计表/',
	'LBL_PERFORM_CONFIG_PHP'			=> '创建Sugar配置文件',
	'LBL_PERFORM_CREATE_DB_1'			=> '创建数据库',
	'LBL_PERFORM_CREATE_DB_2'			=> '在',
	'LBL_PERFORM_CREATE_DB_USER'		=> '创建数据库用户名和密码...',
	'LBL_PERFORM_CREATE_DEFAULT'		=> '创建默认Sugar数据...',
	'LBL_PERFORM_CREATE_LOCALHOST'		=> '为localhost创建数据库用户名和密码...',
	'LBL_PERFORM_CREATE_RELATIONSHIPS'	=> '创建Sugar关系表',
	'LBL_PERFORM_CREATING'				=> '创建/',
	'LBL_PERFORM_DEFAULT_REPORTS'		=> '创建默认报表',
	'LBL_PERFORM_DEFAULT_SCHEDULER'		=> '创建默认工作计划任务',
	'LBL_PERFORM_DEFAULT_SETTINGS'		=> '插入默认设置',
	'LBL_PERFORM_DEFAULT_USERS'			=> '创建默认用户',
	'LBL_PERFORM_DEMO_DATA'				=> '导入演示数据到数据库中(这可能会花费一些时间)...',
	'LBL_PERFORM_DONE'					=> '完成<br>',
	'LBL_PERFORM_DROPPING'				=> '删除/',
	'LBL_PERFORM_FINISH'				=> '完成',
	'LBL_PERFORM_LICENSE_SETTINGS'		=> '更新许可证信息',
	'LBL_PERFORM_OUTRO_1'				=> '设置Sugar',
	'LBL_PERFORM_OUTRO_2'				=> '现在完成。',
	'LBL_PERFORM_OUTRO_3'				=> '总时间:',
	'LBL_PERFORM_OUTRO_4'				=> '秒。',
	'LBL_PERFORM_OUTRO_5'				=> '大约使用内存:',
	'LBL_PERFORM_OUTRO_6'				=> '字节。',
	'LBL_PERFORM_OUTRO_7'				=> '您的系统已安装，并且可以使用了。',
	'LBL_PERFORM_REL_META'				=> '关系元...',
	'LBL_PERFORM_SUCCESS'				=> '成功！',
	'LBL_PERFORM_TABLES'				=> '创建Sugar应用程序表，审计表，和关系元...',
	'LBL_PERFORM_TITLE'					=> '性能设置',
	'LBL_PRINT'							=> '打印',
	'LBL_REG_CONF_1'					=> '请用一点时间填写下面的表单来注册SugarCRM。通过了解贵公司对SugarCRM的使用，我们可以确保发布合适的应用程序来满足您的商业需求。如果您有兴趣接收关于Sugar的信息，请进入我们的邮件列表。我们不会向第三方泄漏您填写的信息。',
	'LBL_REG_CONF_2'					=> '您的姓名和电子邮件地址是注册时的必填域。其他所有域都是可选的，但是很有用。我们不会向第三方泄漏您填写的信息。',
	'LBL_REG_CONF_3'					=> '感谢注册。点击“完成”按钮登录SugarCRM。您需要使用用户名“admin”和它的密码进行第一次登录。',
	'LBL_REG_TITLE'						=> '注册',
  'LBL_REG_NO_THANKS'                 => '不用 谢谢',
  'LBL_REG_SKIP_THIS_STEP'				=> '跳过此步',
	'LBL_REQUIRED'						=> '*必填字段',
  
  'LBL_SITECFG_ADMIN_Name'            => 'Sugar 管理员名',
	'LBL_SITECFG_ADMIN_PASS_2'			=> '重新输入<em>管理员</em>密码',
	'LBL_SITECFG_ADMIN_PASS_WARN'		=> '小心:这将取代任何以前安装的管理员密码。',
	'LBL_SITECFG_ADMIN_PASS'			=> 'Sugar<em>管理员</em>密码',
	'LBL_SITECFG_APP_ID'				=> '应用程序编号',
	'LBL_SITECFG_CUSTOM_ID_DIRECTIONS'	=> '取代自动产生的应用程序编号可以防止不同的Sugar实例使用一个会话。如果您是集群安装Sugar，它们必须用享相同的应用程序编号。',
	'LBL_SITECFG_CUSTOM_ID'				=> '提供您自己的应用程序编号',
	'LBL_SITECFG_CUSTOM_LOG_DIRECTIONS'	=> '取代Sugar默认日志路径。通过浏览器访问日志文件会被限制。',
	'LBL_SITECFG_CUSTOM_LOG'			=> '使用自定义日志目录',
	'LBL_SITECFG_CUSTOM_SESSION_DIRECTIONS'	=> '提供一个安全的文件夹来存放Sugar会话信息，可以防止会话数据在共享服务器上免受攻击。',
	'LBL_SITECFG_CUSTOM_SESSION'		=> '为sugar使用自定义会话目录',
	'LBL_SITECFG_DIRECTIONS'			=> '请输入下面的站点配置信息。如果您不确定要输入什么，我们建议您使用默认值。',
	'LBL_SITECFG_FIX_ERRORS'			=> '在继续执行前，请修复下面的错误:',
	'LBL_SITECFG_LOG_DIR'				=> '日志目录',
	'LBL_SITECFG_SESSION_PATH'			=> '会话目录路径<br>(必须是可写的)',
	'LBL_SITECFG_SITE_SECURITY'			=> '高级站点安全',
	'LBL_SITECFG_SUGAR_UP_DIRECTIONS'	=> '如果选中，系统会周期检查是否有新版本的应用程序。',
	'LBL_SITECFG_SUGAR_UP'				=> '自动更新检查?',
	'LBL_SITECFG_SUGAR_UPDATES'			=> 'Sugar更新配置',
	'LBL_SITECFG_TITLE'					=> '站点配置',
  'LBL_SITECFG_TITLE2'                => '验证Sugar实例',
  'LBL_SITECFG_SECURITY_TITLE'        => '站点安全',
	'LBL_SITECFG_URL'					=> 'Sugar实例网址',
	'LBL_SITECFG_USE_DEFAULTS'			=> '使用默认值?',
	'LBL_SITECFG_ANONSTATS'             => '发送匿名使用统计?',
	'LBL_SITECFG_ANONSTATS_DIRECTIONS'        => '如果选中，每次系统检测新版本时，Sugar会匿名发送安装统计到Sugar公司。这些信息使我们更好的了解应用程序的使用和指导我们改进产品。',
  'LBL_SITECFG_URL_MSG'               => '输入将要用来访问Sugar实例的地址. 这个连接地址可以作为访问Sugar产品其它页面的根目录. 这个地址应该包括WEB服务器或机器名或IP地址.',
  'LBL_SITECFG_SYS_NAME_MSG'          => '给系统取个名字.  当用户访问Sugar产品的时候，这个名字将会显示在浏览器的标题栏上.',
  'LBL_SITECFG_PASSWORD_MSG'          => '安装结束后, 需要使用用户管理员(名字 = admin) 来登陆Sugar实例.  输入密码. 密码在登陆后可以更改.',
  'LBL_SYSTEM_CREDS'                  => '系统认证',
  'LBL_SYSTEM_ENV'                    => '系统环境',
  'LBL_START'							=> '开始',
  'LBL_SHOW_PASS'                     => '显示密码',
  'LBL_HIDE_PASS'                     => '隐藏密码',
  'LBL_HIDDEN'                        => '<i>(隐藏)</i>',
//	'LBL_NO_THANKS'						=> 'Continue to installer',
	'LBL_CHOOSE_LANG'					=> '<b>选择语言</b>',
	'LBL_STEP'							=> '步骤',
	'LBL_TITLE_WELCOME'					=> '欢迎来到SugarCRM',
	'LBL_WELCOME_1'						=> '安装会创建SugarCRM数据库表，设置启动应用程序的配置变量。整个过程大约需要10分钟。',
	'LBL_WELCOME_2'						=> '获取安装文档，请访问<a href="http://www.sugarcrm.com/crm/installation"target="_blank">SugarWiki</a>。<BR><BR>您也可以在Sugar社区<a href="http://www.sugarcrm.com/forums/"target="_blank">Sugar论坛</a>获取帮助。',
    //welcome page variables
  'LBL_TITLE_ARE_YOU_READY'            => '确定要安装么?',
  'REQUIRED_SYS_COMP' => '需要的系统组件',
  'REQUIRED_SYS_COMP_MSG' =>
                    '开始之前, 请确定系统安装了以下Sugar支持的版本的组件:<br>
                      <ul>
                      <li> 数据库/数据库管理系统 (举例: MySQL, SQL Server, Oracle)</li>
                      <li> 网络服务器 (Apache, IIS)</li>
                      </ul>
                      根据将要安装的Sugar版本, 参考发布说明中的系统组件兼容列表.<br>',
    'REQUIRED_SYS_CHK' => '初始化系统检测',
    'REQUIRED_SYS_CHK_MSG' =>
                    '当开始安装时, WEB服务器- Sugar产品所在的机器 将进行系统检测, 来确保系统配置正确和所有的组件都已正确安装<br><br>
                      系统将检测以下部分:<br>
                      <ul>
                      <li><b>PHP 版本</b> &#8211; 必须和产品兼容</li>
                                        <li><b>Session 变量</b> &#8211; 必须正常工作</li>
                                            <li> <b>多字节字符传</b> &#8211; 必须以安装并且在php.ini文件中设置为可用</li>

                      <li> <b>数据库支持</b> &#8211; 必须至少是以下数据库之一 MySQL, SQL
                      Server or Oracle</li>

                      <li> <b>Config.php</b> &#8211; 必须存在并且具有写权限</li>
					  <li>以下Sugar文件必须可写:<ul><li><b>/custom</li>
<li>/cache</li>
<li>/modules</b></li></ul></li></ul>
                                  如果系统验证失败, 将不能进行安装. 将会有相应的错误提示 - 为什么系统没有通过检测.
                                  修改系统之后, 可以重新检测系统并继续安装过程.<br>',
    'REQUIRED_INSTALLTYPE' => '典型或自定义安装',
    'REQUIRED_INSTALLTYPE_MSG' =>
                    '系统检测之后, 可以选择典型或自定义安装.<br><br>
                      对于 <b>典型</b> 和 <b>自定义</b> 安装, 需要了解一下事项:<br>
                      <ul>
                      <li> <b>数据库类型</b> 存储数据的地方 <ul><li>兼容的数据库类型: MySQL, MS SQL Server, Oracle.<br><br></li></ul></li>
                      <li> 数据库所在的<b>网络服务器</b> 或者机器 (主机)
                      <ul><li>如果数据库在当前的机器上或者和网络服务器在同一台机器上或者和Sugar产品在同一台机器上, 则名字可能会是 <i>localhost</i> 如果数据库是在您本地计算机上或是在相同 web 服务器上或机器作为您的Sugar文件.<br><br></li></ul></li>
                      <li><b>数据库名</b> 存储数据的地方</li>
                        <ul>
                          <li> 可能你会使用一个当前存在的数据库. 这个数据库中的所有表将会被删除.</li>
                          <li> 如果你所输入的数据库名不存在, 系统会创建一个新的数据库.<br><br></li>
                        </ul>
                      <li><b>数据库管理员用户名和密码</b> <ul><li>数据库管理员应该具有创建表,用户和写入数据库的权限.</li><li>
                        如果你的数据库不在当前计算机上或者你不是管理员, 你需要联系管理员来获得相应的信息.<br><br></ul></li></li>
                      <li> <b>Sugar 数据库用户名和密码</b>
                      </li>
                        <ul>
                          <li> 用户可以是数据库管理员, 或者是一个存在的数据库用户. </li>
                          <li> 如果需要创建一个数据库用户, 需要提供用户名和密码,该用户会在安装过程中被创建.</li>
                        </ul></ul><p>

                      对于 <b>自定义</b> 安装, 需要了解一下事项:<br>
                      <ul>
                      <li> <b>访问Sugar 实例的链接(URL)</b> 安装完毕后,这个链接将包括网络服务器名或机器名或IP地址.
                                <br><br></li>
                                  <li> [可选项] <b>session 目录路径</b> 自定义session目录以防session 数据受到共享服务器中的session 数据的干扰而受到破坏.<br><br></li>
                                  <li> [可选项] <b>定制日志目录路径</b> 如果您希望重写Sugar日志在默认的路径下.<br><br></li>
                                  <li> [可选项l] <b>产品 ID</b> 希望覆盖自动生成的ID来确保Sugar 实例session 不会被其他的实例使用
                                  .<br><br></li>
                                  <li><b>字符集设置</b> 所在区域最常用到的字符集.<br><br></li></ul>
                                  详细信息请参阅安装文档.
                                ',
    'LBL_WELCOME_PLEASE_READ_BELOW' => '安装前请阅读一下重要信息.  这些信息经帮助你决定是否要在现在安装本产品.',


	'LBL_WELCOME_2'						=> '要获取安装文档，请访问<a href="http://www.sugarcrm.com/crm/installation"target="_blank">SugarWiki</a>。<BR><BR>要获取工程师安装帮助，请登录<a target="_blank"href="http://support.sugarcrm.com">SugarCRM支持门户网站</a>，并提交您的问题。',

	'LBL_WELCOME_CHOOSE_LANGUAGE'		=> '选择语言',
	'LBL_WELCOME_SETUP_WIZARD'			=> '安装向导',
	'LBL_WELCOME_TITLE_WELCOME'			=> '欢迎来到SugarCRM',
	'LBL_WELCOME_TITLE'					=> 'SugarCRM安装向导',
	'LBL_WIZARD_TITLE'					=> 'SugarCRM安装向导:步骤',
	'LBL_YES'							=> '是',
  'LBL_YES_MULTI'                     => '是 - 多字节',
	// OOTB Scheduler Job Names:
	'LBL_OOTB_WORKFLOW'		=> '使工作流程任务进行',
	'LBL_OOTB_REPORTS'		=> '为计划任务产生报表',
	'LBL_OOTB_IE'			=> '检查收件箱',
	'LBL_OOTB_BOUNCE'		=> '每晚处理退回的电子邮件',
    'LBL_OOTB_CAMPAIGN'		=> '每晚批量运行电子邮件营销活动',
	'LBL_OOTB_PRUNE'		=> '每月1号精简数据库',
    'LBL_OOTB_TRACKER'		=> '每月的第一天清除历史数据',
    'LBL_PATCHES_TITLE'     => '安装最新的补丁',
    'LBL_MODULE_TITLE'      => '下载并安装语言包',
    'LBL_PATCH_1'           => '如果您想跳过这步，请点击下面的“下一步”按钮。',
    'LBL_PATCH_TITLE'       => '系统路径',
    'LBL_PATCH_READY'       => '下列路径可用于安装。',
	'LBL_SESSION_ERR_DESCRIPTION'		=> "当连接到网络服务器时，SugarCRM依赖PHP会话来存储重要的信息。您的PHP安装没有配置正确的会话信息。<br><br>通常是因为没有设置<b>“session.save_path”</b>来指示一个有效的目录。<br><br>请纠正在php.ini文件中的<atarget=_newhref='http://us2.php.net/manual/en/ref.session.php'>PHP配置</a>。",
	'LBL_SESSION_ERR_TITLE'				=> 'PHP会话配置错误',
	'LBL_SYSTEM_NAME'=> '系统名称',
	'LBL_SYSTEM_NAME_INFO'=> '它会被显示在SugarCRM的标题栏',
	'LBL_REQUIRED_SYSTEM_NAME'=> '系统名不能为空',
	'LBL_PATCH_UPLOAD' => '上传路径',
	
	//added from 5.1
'LBL_UPDATE_TRACKER_SESSIONS' => '更新tracker_sessions表',
'LBL_OOTB_DCE_CLNUP' => '在完成的DCE行动上关闭循环',
'LBL_OOTB_DCE_REPORT' => '创建行动来收集日报表',
'LBL_OOTB_DCE_SALES_REPORT' => '创建周销售报表邮件',
'LBL_INCOMPATIBLE_PHP_VERSION' => '需要PHP版本5或者以上.',
'LBL_MINIMUM_PHP_VERSION' => '至少需要PHP版本5.1.0.建议版本5.2.x.',
'LBL_YOUR_PHP_VERSION' => '(您当前的PHP版本是',
'LBL_RECOMMENDED_PHP_VERSION' => ' 推荐版本是5.2.x)',
'LBL_BACKWARD_COMPATIBILITY_ON' => 'PHP向前兼容模式关闭.设置zend.ze1_compatibility_mode为Off来继续操作',
);
?>
