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

	'ERR_ADMIN_PASS_BLANK'				=> 'SugarCRM Admin Passwort kann icht  freigelassen werden.',
	'ERR_CHECKSYS_CALL_TIME'			=> 'Call Time Pass Reference ist - Off - (bitte in php.ini aktivieren)',
	'ERR_CHECKSYS_CURL'					=> 'Nicht gefunden: Sugar Zeitplaner wird nur eingeschränkt funktionieren.',
	'ERR_CHECKSYS_MEM_LIMIT_1'			=> 'Warnung: $memory_limit (Setzetn Sie den Parameter auf ',
	'ERR_CHECKSYS_MEM_LIMIT_2'			=> 'M oder größer in Ihrer php.ini)',
	'ERR_CHECKSYS_NO_SESSIONS'			=> 'Es konnten keine Session-Variablen gelesen oder geschrieben werden. Installation abgebrochen.',
	'ERR_CHECKSYS_NOT_VALID_DIR'		=> 'Kein gültiges Verzeichnis',
	'ERR_CHECKSYS_NOT_WRITABLE'		=> 'Warnung: Nicht beschreibbar',
	'ERR_CHECKSYS_PHP_INVALID_VER'		=> 'Ungültige PHP-Version installiert: ( ver',
	'ERR_CHECKSYS_PHP_UNSUPPORTED'		=> 'Nicht unterstützte PHP Version installiert: ( ver',
	'ERR_CHECKSYS_SAFE_MODE'			=> 'Safe Mode ist On (bitte in der php.ini ausschalten)',
	'ERR_DB_ADMIN'						=> 'Datenbank Admin-Benutzername und/oder Passwort ist ungültig (Fehler ',
	'ERR_DB_EXISTS_NOT'					=> 'Datenbank-Auswahl/Spezifikation existiert nicht.',
	'ERR_DB_EXISTS_WITH_CONFIG'		=> 'Datenbank besteht bereits mit Konfigurationsdaten.  Für eine erneute Installation beginnen Sie die Installation mit der Auswahl: "Bestehende Datenbank und Tabellen nutzen?"  Für ein Upgrade nutzen Sie bitte den Upgrade-Assistenten im Adminbereich.  Bitte lesen Sie hierzu auch die Upgrade-Dokumentation unter <a href="http://www.sugarforge.org/content/downloads/" target="_new"></a>.',
	'ERR_DB_EXISTS'						=> 'Datenbankname existiert bereits.',
	'ERR_DB_HOSTNAME'					=> 'Hostname kann nicht freigelassen werden.',
	'ERR_DB_INVALID'					=> 'Ungültiger Datenbanktyp ausgewählt.',
	'ERR_DB_LOGIN_FAILURE_MYSQL'		=> 'SugarCRM Datenbank Benutzername und/oder Passwort ist ungültig (Fehler ',
	'ERR_DB_MYSQL_VERSION1'				=> 'MySQL Version ',
	'ERR_DB_MYSQL_VERSION2'				=> ' wird nicht unterstützt. Nur MySQL 4.1.x und höher wird unterstützt.',
	'ERR_DB_NAME'						=> 'Datenbankname kann nicht freigelassen werden.',
	'ERR_DB_NAME2'						=> "Datenbankname kann nicht beeinhalten ein '\\', '/', oder '.'",
	'ERR_DB_PASSWORD'					=> 'Passwörter stimmen nicht überein.',
	'ERR_DB_PRIV_USER'					=> 'Datenbank Admin-Benutzername ist erforderlich.',
	'ERR_DB_USER_EXISTS'				=> 'Benutzername existiert bereits.',
	'ERR_DB_USER'						=> 'Benutzername kann nicht freigelassen werden.',
	'ERR_DBCONF_VALIDATION'				=> 'Bitte lösen Sie folgende Fehler bevor Sie fortfahren:',
	'ERR_ERROR_GENERAL'					=> 'Folgende Fehler sind aufgetreten:',
	'ERR_LANG_CANNOT_DELETE_FILE'		=> 'Datei nicht löschbar: ',
	'ERR_LANG_MISSING_FILE'				=> 'Datei nicht findbar: ',
	'ERR_LANG_NO_LANG_FILE'			 	=> 'Keine Datei eines Sprachpakets wurde gefunden in: ',
	'ERR_LANG_UPLOAD_1'					=> 'Ihr Upload ist fehlgeschlagen.  Bitte erneut versuchen.',
	'ERR_LANG_UPLOAD_2'					=> 'Dateipakete müssen als ZIP-Archiv bereitstehen.',
	'ERR_LANG_UPLOAD_2'					=> 'PHP konnte nicht die temp-Datei in das Upgrade-Verzeichnis verschieben.',
	'ERR_LICENSE_MISSING'				=> 'Erforderliche Dateien fehlen.',
	'ERR_LICENSE_NOT_FOUND'				=> 'Lizenz-Datei fehlt!',
	'ERR_LOG_DIRECTORY_NOT_EXISTS'		=> 'Log-Verzeichnis ist kein gültiges Verzeichnis.',
	'ERR_LOG_DIRECTORY_NOT_WRITABLE'	=> 'Log-Verzeichnis ist nicht beschreibbar.',
	'ERR_LOG_DIRECTORY_REQUIRED'		=> 'Ein Log-Verzeichnis ist erforderlich.',
	'ERR_NO_DIRECT_SCRIPT'				=> 'Das Skript konnte nich ausgeführt werden.',
	'ERR_PASSWORD_MISMATCH'				=> 'Passwörter für den Administrator stimmen nicht überein.',
	'ERR_PERFORM_CONFIG_PHP_1'			=> 'Es konnte die Datei <span class=stop>config.php</span> nicht beschreiben werden.',
	'ERR_PERFORM_CONFIG_PHP_2'			=> 'Für eine manuelle Installation erstellen Sie die Datei config.php mit nachfolgende Informationen.  Sie <strong>müssen </strong>eine Datei config.php erstellen, bevor Sie mit dem nächsten Schritt fortfahren.',
	'ERR_PERFORM_CONFIG_PHP_3'			=> 'Haben Sie eine config.php erstellt?',
	'ERR_PERFORM_CONFIG_PHP_4'			=> 'Warnung: Es konnten nicht die config.php beschrieben werden.  Bitte stellen Sie sicher, dass diese existiert und korrekte Rechte vergeben sind.',
	'ERR_PERFORM_HTACCESS_1'			=> 'Es konnte nicht geschrieben werden auf ',
	'ERR_PERFORM_HTACCESS_2'			=> ' Datei.',
	'ERR_PERFORM_HTACCESS_3'			=> 'Wenn Sie den Zugriff auf Ihre Logfiles sichern möchten, erstellen Sie eine .htaccess Datei in Ihrem Log-Verzeichnis mit der Zeile:',
	'ERR_PERFORM_NO_TCPIP'				=> '<b>Wir konnten keine Internet-Verbindung feststellen.</b> Zur Fehlersuche informieren Sie sich ggf. unter <a href="http://www.sugarcrm.com/home/index.php?option=com_extended_registration&task=register">http://www.sugarcrm.com/home/index.php?option=com_extended_registration&task=register</a> to register with SugarCRM. By letting us know a little bit about how your company plans to use SugarCRM, we can ensure we are always delivering the right application for your business needs.',
	'ERR_SESSION_DIRECTORY_NOT_EXISTS'	=> 'Session-Verzeichnis ist kein gültiges Verzeichnis.',
	'ERR_SESSION_DIRECTORY'				=> 'Session-Verzeichnis ist kein beschreibbares Verzeichnis.',
	'ERR_SESSION_PATH'					=> 'Session-Verzeichnisangabe ist erforderlich, falls Sie ein ein individuelles Verzeichnis anlegen wollen.',
	'ERR_SI_NO_CONFIG'					=> 'Sie haben nicht die config_si.php im Dokumenten-Stammverzeichnis abgelegt, oder Sie haben nicht $sugar_config_si in der config.php definiert',
	'ERR_SITE_GUID'						=> 'Application ID ist erforderlich, falls Sie diese selbst definieren möchten.',
	'ERR_URL_BLANK'						=> 'URL kann nicht freigelassen sein.',
	'ERROR_FLAVOR_INCOMPATIBLE'		=> 'Die Dateiversion ist nicht kompatibel zu (Community Edition, Professional, or Enterprise) der Sugar: ',
	'ERROR_LICENSE_EXPIRED'				=> "Fehler: Ihre Lizenz ist abgelaufen ",
	'ERROR_LICENSE_EXPIRED2'			=> " seit Tagen.   Bitte rufen Sie das <a href='index.php?action=LicenseSettings&module=Administration'>'\"Lizenz-Management\"</a>  im Adminbereich auf, und geben Sie Ihren neuen Lizenzschlüssel ein.  Falls Sie keinen neuen Lizenzschlüssel innerhalb von 30 Tagen - nach Verfallsdatum Ihres bisherigen Lizenzschlüssels - eingeben, werden Sie diese Applikation nicht weiter verwenden können.",
	'ERROR_MANIFEST_TYPE'				=> 'Die Manifest-Datei muss einen Paket-Typ definieren.',
	'ERROR_PACKAGE_TYPE'				=> 'Die Manifest-Datei hat einen unbekannten Paket-Typ festgestellt.',
	'ERROR_VALIDATION_EXPIRED'			=> "Fehler: Ihr Lizenzschlüssel ist abgelaufen  ",
	'ERROR_VALIDATION_EXPIRED2'		=> " seit Tagen.   Bitte rufen Sie das <a href='index.php?action=LicenseSettings&module=Administration'>'\"Lizenz-Management\"</a>  im Adminbereich auf, und geben Sie Ihren neuen Lizenzschlüssel ein.  Falls Sie keinen neuen Lizenzschlüssel innerhalb von 30 Tagen - nach Verfallsdatum Ihres bisherigen Lizenzschlüssels - eingeben, werden Sie diese Applikation nicht weiter verwenden können.",
	'ERROR_VERSION_INCOMPATIBLE'		=> 'Die Datei ist nicht kompatibel mit dieser Version der Sugar-Suite: ',

	'LBL_BACK'							=> 'Zurück',
	'LBL_CHECKSYS_1'					=> 'Eine fehlerfreie SugarCRM Installation ist nur bei grünen Punkten des Systemchecks gewährleistet. Bitte sorgen Sie ggf. für entsprechende Problemlösung.',
	'LBL_CHECKSYS_CACHE'				=> 'Schreibbarer Cache - Sub-Verzeichnisse',
	'LBL_CHECKSYS_CALL_TIME'			=> 'PHP erlaubt Call Time Pass Reference Turned On',
	'LBL_CHECKSYS_COMPONENT'			=> 'Komponente',
	'LBL_CHECKSYS_CONFIG'				=> 'Beschreibbare SugarCRM-Konfigurationsdatei (config.php)',
	'LBL_CHECKSYS_CURL'					=> 'cURL Bibliothek',
	'LBL_CHECKSYS_CUSTOM'				=> 'Beschreibbares Kunden-Verzeichnis',
	'LBL_CHECKSYS_DATA'					=> 'Beschreibbare Daten Sub-Verzeichnisse',
	'LBL_CHECKSYS_MEM_OK'				=> 'OK (Keine Beschränkung)',
	'LBL_CHECKSYS_MEM_UNLIMITED'		=> 'OK (Unbeschränkt)',
	'LBL_CHECKSYS_MEM'					=> 'PHP Memory-Beschränkung >= ',
	'LBL_CHECKSYS_MODULE'				=> 'Beschreibbare Module, Sub-Verzeichnisse und Dateien',
	'LBL_CHECKSYS_NOT_AVAILABLE'		=> 'Nicht verfügbar',
	'LBL_CHECKSYS_OK'					=> 'OK',
	'LBL_CHECKSYS_PHP_INI'				=> '<b>Hinweis:</b> Ihre php-Konfigurationsdatei (php.ini) fiinden Sie unter:',
	'LBL_CHECKSYS_PHP_OK'				=> 'OK (ver ',
	'LBL_CHECKSYS_PHPVER'				=> 'PHP-Version',
	'LBL_CHECKSYS_RECHECK'				=> 'Nochmals überprüfen',
	'LBL_CHECKSYS_SAFE_MODE'			=> 'PHP Safe Mode steht auf Off',
	'LBL_CHECKSYS_SESSION'				=> 'Speicherpfad für beschreibbare Sessions (',
	'LBL_CHECKSYS_STATUS'				=> 'Status',
	'LBL_CHECKSYS_TITLE'				=> 'System Check Akzeptanz',
	'LBL_CHECKSYS_XML'					=> 'XML Parsing',
	'LBL_CLOSE'							=> 'Schließen',
	'LBL_CONFIRM_BE_CREATED'			=> 'wurde erstellt',
	'LBL_CONFIRM_DB_TYPE'				=> 'Datenbank-Typ',
	'LBL_CONFIRM_DIRECTIONS'			=> 'Bitte bestätigen Sie nachfolgende Einstellungen.  Falls Sie Werte ändern möchten, gehen Sie einen Schritt "zurück".  Ansonsten starten Sie nun die Installation mit "Weiter".',
	'LBL_CONFIRM_LICENSE_TITLE'		=> 'Lizenzinformation',
	'LBL_CONFIRM_NOT'					=> 'nicht',
	'LBL_CONFIRM_TITLE'					=> 'Einstellungen bestätigen',
	'LBL_CONFIRM_WILL'					=> 'wollen',
	'LBL_DBCONF_CREATE_DB'				=> 'Datenbank erstellen',
	'LBL_DBCONF_CREATE_USER'			=> 'Benutzer erstellen',
	'LBL_DBCONF_DB_DROP_CREATE_WARN'	=> 'Achtung: Alle Sugar Daten werden gelöscht <br>falls diese Checkbox aktiviert ist.',
	'LBL_DBCONF_DB_DROP_CREATE'		=> 'Bestehende Sugar Datenbank-Tabellen wiederherstellen?',
	'LBL_DBCONF_DB_NAME'				=> 'Datenbank-Name',
	'LBL_DBCONF_DB_PASSWORD'			=> 'Datenbank-Passwort',
	'LBL_DBCONF_DB_PASSWORD2'			=> 'Datenbank-Passwort wiederholen',
	'LBL_DBCONF_DB_USER'				=> 'Datenbank-Benutzername',
	'LBL_DBCONF_DEMO_DATA'				=> 'Datenbank mit Demo-Daten füllen?',
	'LBL_DBCONF_HOST_NAME'				=> 'Host-Name',
	'LBL_DBCONF_INSTRUCTIONS'			=> 'Bitte geben Sie Ihre Datenbank-Konfigurationsdaten ein. Fall Sie diese nicht kennen, empfehlen wir, die Standardeinstellungen zu nutzen.',
	'LBL_DBCONF_MB_DEMO_DATA'			=> 'Wollen Sie Multi-Byte Texte in den Demo-Daten nutzen (für Europäische Sprachen!)?',
	'LBL_DBCONF_PRIV_PASS'				=> 'Passwort für priviligierten Datenbank-Benutzer',
	'LBL_DBCONF_PRIV_USER_2'			=> 'Datenbank-Account ist ein Privilegiert Benutzer?',
	'LBL_DBCONF_PRIV_USER_DIRECTIONS'	=> 'Der priviligierte Datenbank-Benutzer muss das Recht haben, eine Datenbank zu erstellen, Tabellen zu löschen etc. Der priviligierte Datenbank-Benutzer wird nur genutzt, um während der Installation auf die Datenbank zugreifen zu können.',
	'LBL_DBCONF_PRIV_USER'				=> 'Priviligierter Datenbank-Benutzername',
	'LBL_DBCONF_TITLE'					=> 'Datenbank Konfiguration',
	'LBL_DISABLED_DESCRIPTION_2'		=> 'Nachdem diese Anpassungen vollzogen sind, können Sie mit "Start" die Installation starteb.  <i>Nach Abschluss der Installation sollten Sie die Werte setzen \'installer_locked\' nach \'true\'.</i>',
	'LBL_DISABLED_DESCRIPTION'			=> 'Die Installation wurde ausgeführt.  Zum Schutz wird die erneute Installation verhindert.  Falls Sie eine erneute Installation durchführen wollen, ändern Sie Ihre config.php Datei unter \'installer_locked\' und ändern Sie den Eintrag zu \'false\'.  Beispielhaft:',
	'LBL_DISABLED_HELP_1'				=> 'Für weitere Hilfe bei der Installation, besuchen Sie bitte das Online-Portal von SugarCRM',
	'LBL_DISABLED_HELP_2'				=> 'Support-Foren',
	'LBL_DISABLED_TITLE_2'				=> 'SugarCRM-Installation wurde deaktiviert',
	'LBL_DISABLED_TITLE'				=> 'SugarCRM-Installation deaktiviert',
	'LBL_HELP'							=> 'Hilfe',
	'LBL_LANG_1'						=> 'Sie können hier ein weiteres Sprachpaket installieren.  Andernfalls kommen Sie mit "Weiter" zum nächsten Schritt.',
	'LBL_LANG_BUTTON_COMMIT'			=> 'Installieren',
	'LBL_LANG_BUTTON_REMOVE'			=> 'Entfernen',
	'LBL_LANG_BUTTON_UNINSTALL'		=> 'Deinstallieren',
	'LBL_LANG_NO_PACKS'					=> 'keines',
	'LBL_LANG_PACK_INSTALLED'			=> 'Es sind folgende Sprachpakete installiert: ',
	'LBL_LANG_PACK_READY'				=> 'Folgende Sprachpakte können nun installiert werden: ',
	'LBL_LANG_TITLE'			   		=> 'Sprachpaket',
	'LBL_LANG_UPLOAD'					=> 'Ein Sprachpaket hochladen',
	'LBL_LICENSE_ACCEPTANCE'			=> 'Lizenz-Akzeptanzstatus',
	'LBL_LICENSE_DIRECTIONS'			=> 'Fall Sie über einen Lizenzschlüssel und Lizenzinformationen verfügen, tragen Sie diese bitte ein.',
	'LBL_LICENSE_DOWNLOAD_KEY'			=> 'Download eines Lizenzschlüssels',
	'LBL_LICENSE_EXPIRY'				=> 'Ablaufdatum',
	'LBL_LICENSE_I_ACCEPT'				=> 'Ich akzeptiere',
	'LBL_LICENSE_NUM_USERS'				=> 'Zahl der Benutzer ',
	'LBL_LICENSE_OC_DIRECTIONS'		=> 'Biite geben Sie die Zahl der erworbenen Offline-Clients an.',
	'LBL_LICENSE_OC_NUM'				=> 'Anzahl der Offline Client Lizenzen',
	'LBL_LICENSE_OC'					=> 'Offline Client Lizenzen',
	'LBL_LICENSE_PRINTABLE'				=> ' Druckansicht ',
	'LBL_LICENSE_TITLE_2'				=> 'SugarCRM-Lizenz',
	'LBL_LICENSE_TITLE'					=> 'Lizenz-Informationen',
	'LBL_LICENSE_USERS'					=> 'Lizenzierte Benutzer',
	'LBL_ML_ACTION'						=> 'Aktion',
	'LBL_ML_DESCRIPTION'				=> 'Beschreibung',
	'LBL_ML_INSTALLED'					=> 'Installationsdatum',
	'LBL_ML_NAME'						=> 'Name',
	'LBL_ML_PUBLISHED'					=> 'Publikationsdatum',
	'LBL_ML_TYPE'						=> 'Typ',
	'LBL_ML_UNINSTALLABLE'				=> 'Nicht deinstallierbar',
	'LBL_ML_VERSION'					=> 'Version',
	'LBL_MYSQL'							=> 'MySQL',
	'LBL_NEXT'							=> 'Weiter',
	'LBL_NO'							=> 'Nein',
	'LBL_ORACLE'						=> 'Oracle',
	'LBL_PERFORM_ADMIN_PASSWORD'		=> 'Setzen Sie ein Admin-Passwort für die Installation',
	'LBL_PERFORM_AUDIT_TABLE'			=> 'Audit Tabelle / ',
	'LBL_PERFORM_CONFIG_PHP'			=> 'Erstelle Sugar Konfigurationsdatei ',
	'LBL_PERFORM_CREATE_DB_1'			=> 'Erstelle Datenbank ',
	'LBL_PERFORM_CREATE_DB_2'			=> ' ein ',
	'LBL_PERFORM_CREATE_DB_USER'		=> 'Erstelle ein(en) Datenbank-Benutzernamen und -Passwort...',
	'LBL_PERFORM_CREATE_DEFAULT'		=> 'Erstelle Standard-Daten',
	'LBL_PERFORM_CREATE_LOCALHOST'		=> 'Erstellle ein(en) Datenbank-Benutzernamen und -Passwort für die lokale Installation...',
	'LBL_PERFORM_CREATE_RELATIONSHIPS'	=> 'Erstelle relationale Datenbanktabellen ',
	'LBL_PERFORM_CREATING'				=> 'erstelle / ',
	'LBL_PERFORM_DEFAULT_REPORTS'		=> 'Erstelle Standard-Berichte',
	'LBL_PERFORM_DEFAULT_SCHEDULER'	=> 'Erstelle Standard-Jobs für den Zeitplaner',
	'LBL_PERFORM_DEFAULT_SETTINGS'		=> 'Füge Standard-Einstellungen hinzu',
	'LBL_PERFORM_DEFAULT_USERS'		=> 'Erstelle Standard-Benutzer ',
	'LBL_PERFORM_DEMO_DATA'				=> 'Fülle die Datenbank mit Demo-Daten (das kann eine Weile dauern)...',
	'LBL_PERFORM_DONE'					=> 'erfolgt<br>',
	'LBL_PERFORM_DROPPING'				=> 'Daten werden gelöscht / ',
	'LBL_PERFORM_FINISH'				=> 'Beendet',
	'LBL_PERFORM_LICENSE_SETTINGS'		=> 'Aktualisiere Lizenz-Information',
	'LBL_PERFORM_OUTRO_1'				=> 'Das Setup von Sugar ',
	'LBL_PERFORM_OUTRO_2'				=> ' ist nun abgeschlossen.',
	'LBL_PERFORM_OUTRO_3'				=> 'Gesamtzeit: ',
	'LBL_PERFORM_OUTRO_4'				=> ' Sekunden.',
	'LBL_PERFORM_OUTRO_5'				=> 'Es wird angenähert Speicher genutzt: ',
	'LBL_PERFORM_OUTRO_6'				=> ' Bytes.',
	'LBL_PERFORM_OUTRO_7'				=> 'Ihr System ist nun installiert und zur Nutzung bereit.',
	'LBL_PERFORM_REL_META'				=> 'Beziehungsdaten meta ... ',
	'LBL_PERFORM_SUCCESS'				=> 'Erfolgreich!',
	'LBL_PERFORM_TABLES'				=> 'Erstelle Suggar Appliaktions-Tabellen, Audit-Tabellen und Metadaten ...',
	'LBL_PERFORM_TITLE'					=> 'Das Setup ausführen',
	'LBL_PRINT'							=> 'Drucken',
	'LBL_REG_CONF_1'					=> 'Bitte nehmen Sie sich die Zeit, für eine Registration bei SugarCRM. Lassen Sie und ein wenig über Ihre Firma erfahren. Es erleichtert uns, Ihren Bedürfnissen entsprechende Angebote zu entwickeln.',
	'LBL_REG_CONF_2'					=> 'Nur Ihr Name und Ihre Email-Adresse sind für die Registrierung erforderlich. Alle übrigen Angaben sind optional. Wir behandeln Ihre Daten vertraulich und geben diese nicht an Dritte weiter.',
	'LBL_REG_CONF_3'					=> 'Vielen Dank für Ihre Registrierung. Sie müssen nun sich erstamlig anlemden mit dem Benutzernamen "admin" und dem Passwort, welches Sie im 2.Schritt angeben hatten.',
	'LBL_REG_TITLE'						=> 'Registrierung',
	'LBL_REQUIRED'						=> '* Erforderliche Angaben',
	'LBL_SITECFG_ADMIN_PASS_2'			=> 'Wiederholung Sugar <em>Admin</em> Passwort',
	'LBL_SITECFG_ADMIN_PASS_WARN'		=> 'Achtung: Dies wird alle Admin-Passwörter früherer Installation überschreiben.',
	'LBL_SITECFG_ADMIN_PASS'			=> 'Sugar <em>Admin</em> Passwort',
	'LBL_SITECFG_APP_ID'				=> 'Applikations-ID',
	'LBL_SITECFG_CUSTOM_ID_DIRECTIONS'	=> 'Das Überschreiben der automatisch generierten Applikations-ID verhindert, das Sessions einer Instanz auf einer anderen Instanz genutzt werden können.  Falls Sie einen Cluster von Sugar-Installationen betreiben wollen, müssen diese aber auf eine gemeinsame Applikations-ID zugreifen.',
	'LBL_SITECFG_CUSTOM_ID'				=> 'Unterstütze die eigene Applikations-ID',
	'LBL_SITECFG_CUSTOM_LOG_DIRECTIONS'=> 'Überschreibe das Standard-Verzeichnis für Log-Dateien.  Der Zugriff über den Webbrowser wird geschützt über eine .htaccess Umleitung.',
	'LBL_SITECFG_CUSTOM_LOG'			=> 'Nutze ein individuelles Log-Verzeichnis',
	'LBL_SITECFG_CUSTOM_SESSION_DIRECTIONS'	=> 'Stelle ein sicheren Ordner bereit, um bei Shared oder Virtuellen Servern den Zugriff auf Sugar Session-Dateien und -Information zu schützen.',
	'LBL_SITECFG_CUSTOM_SESSION'		=> 'Nutze ein individuelles Session-Verzeichnis für Sugar',
	'LBL_SITECFG_DIRECTIONS'			=> 'Bitte geben Sie Informationen zur Konfiguration Ihrer Installation ein. Falls Ihnen Informationen fehlen, empfehlen wir Ihnen, die die Standardeinstellungen zu nutzen.',
	'LBL_SITECFG_FIX_ERRORS'			=> 'Bitte lösen Sie folgende Fehler, bevor Sie weiterverfahren:',
	'LBL_SITECFG_LOG_DIR'				=> 'Log-Verzeichnis',
	'LBL_SITECFG_SESSION_PATH'			=> 'Pfad zum Session-Verzeichnis <br>(muss beschreibbar sein)',
	'LBL_SITECFG_SITE_SECURITY'		=> 'Erweiterte Sicherheitseinstellungen',
	'LBL_SITECFG_SUGAR_UP_DIRECTIONS'	=> 'Wenn der Punkt aktiviert ist, übermittelt Ihr System anonyme Informationen zu Ihrer Installation an SugarCRM Inc. Dies hilft uns, unsere Produkte gemäß Ihren Anforderungen weiterzuentwickeln.  Im Gegenzug erhält der Administrator Hinweise auf Updates oder neue Versionen.',
	'LBL_SITECFG_SUGAR_UP'				=> 'Sugar Updates aktivieren?',
	'LBL_SITECFG_SUGAR_UPDATES'		=> 'Update-Konfiguration',
	'LBL_SITECFG_TITLE'					=> 'Seiten-Konfiguration',
	'LBL_SITECFG_URL'					=> 'URL der Sugar-Installation',
	'LBL_SITECFG_USE_DEFAULTS'			=> 'Standard-Einstellungen nutzen?',
	'LBL_START'							=> 'Start',
	'LBL_STEP'							=> 'Schritt',
	'LBL_TITLE_WELCOME'					=> 'Willkommen zu SugarCRM ',
	'LBL_WELCOME_1'						=> 'Dieser Installationsprozess definiert eine SugarCRM-Datenbank, Datenbank-Tabellen und setzt die Basis-Konfigurationseinstellungen, damit Sie starten können. Der Prozess dauert nur wenige Minuten.',
	'LBL_WELCOME_2'						=> 'Für weitergehnde Hilfe zur Installation, besuchen Sie bitte das SugarCRM <a href="http://www.sugarcrm.com/forums/" target="_blank">Support Forum</a>.',
	'LBL_WELCOME_CHOOSE_LANGUAGE'		=> 'Bitte wählen Sie Ihre Sprache',
	'LBL_WELCOME_SETUP_WIZARD'			=> 'Setup-Assistent',
	'LBL_WELCOME_TITLE_WELCOME'		=> 'Willkommen zu SugarCRM ',
	'LBL_WELCOME_TITLE'					=> 'SugarCRM Setup-Assistent',
	'LBL_WIZARD_TITLE'					=> 'SugarCRM Setup-Assistent: Schritt ',
	'LBL_YES'							=> 'Ja',
);

?>
