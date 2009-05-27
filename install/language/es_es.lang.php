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
 * Description: Defines the Spanish language pack for the base application.

 * Source: SugarCRM 5.1.0
 * Contributor(s): Alberto Serrano (alb.serrano@gmail.com).
 ********************************************************************************/

$mod_strings = array(














































	
	'DEFAULT_CHARSET'					=> 'UTF-8',
	'ERR_ADMIN_PASS_BLANK'				=> 'Introduzca la contraseña de admin de Sugar.',
//	'ERR_CHECKSYS_CALL_TIME'			=> '"Allow Call Time Pass Reference" está Deshabilitado (por favor, habilítelo en php.ini)',
    'ERR_CHECKSYS'                      => 'Se han detectado errores durante las comprobaciones de compatibilidad.  Para que su Instalación de SugarCRM funcione correctamente, lleva a cabo los siguientes pasos para corregir los problemas listados a continuación y haga clic en el botón comprobar de nuevo, o inicie de nuevo la instalación, por favor.',
	'ERR_CHECKSYS_CALL_TIME'			=> '"Allow Call Time Pass Reference" está Habilitado (por favor, establézcalo a Off en php.ini)',
	'ERR_CHECKSYS_CURL'					=> 'No encontrado: El Planificador de Sugar tendrá funcionalidad limitada.',
	'ERR_CHECKSYS_IMAP'					=> 'No encontrado: Correo Entrante y Campañas (Correo Electrónico) requieren las bibliotecas de IMAP. Ninguno será funcional.',
	'ERR_CHECKSYS_MSSQL_MQGPC'			=> 'Magic Quotes GPC no puede ser activado cuando se usa MS SQL Server.',
	'ERR_CHECKSYS_MEM_LIMIT_0'			=> 'Aviso: ',
	'ERR_CHECKSYS_MEM_LIMIT_1'			=> ' (Establézcalo a ',
	'ERR_CHECKSYS_MEM_LIMIT_2'			=> 'M o más en su archivo your php.ini)',
	'ERR_CHECKSYS_MYSQL_VERSION'		=> 'Versión Mínima 4.1.2 - Encontrada: ',
	'ERR_CHECKSYS_NO_SESSIONS'			=> 'Ha ocurrido un error al escribir y leer las variables de sesión.  No se ha podido proceder con la instalación.',
	'ERR_CHECKSYS_NOT_VALID_DIR'		=> 'No es un Directorio Válido',
	'ERR_CHECKSYS_NOT_WRITABLE'			=> 'Aviso: No Escribible',
	'ERR_CHECKSYS_PHP_INVALID_VER'		=> 'Su versión de PHP no está soportada por Sugar. Debe instalar una versión que sea compatible con la aplicación Sugar. Por favor, consulte la Matriz de Compatibilidad en las Notas de Lanzamiento para más información sobre las versiones de PHP soportadas. Su versión es ',
	'ERR_CHECKSYS_PHP_UNSUPPORTED'		=> 'Versión de PHP Instalada No Soportada: ( ver',
    'LBL_DB_UNAVAILABLE'                => 'Base de datos no disponible',
    'LBL_CHECKSYS_DB_SUPPORT_NOT_AVAILABLE' => 'No se ha encontrado el soporte de base de datos.  Por favor, asegúrese de que tiene los controladores necesarios para alguno de los siguientes tipo de Base de Datos: MySQL, MS SQLServer, u Oracle.  Es posible que tenga que descomentar la extensión en el archivo php.ini, o recompilarlo con el archivo binario apropiado, dependiendo de la versión de PHP.  Por favor, consulte el manual de PHP para más información sobre cómo habilitar el Soporte de Base de Datos.',
    'LBL_CHECKSYS_XML_NOT_AVAILABLE'        => 'Las funciones asociadas con las Bibliotecas de Análisis de XML que son requeridas por la aplicación Sugar no han sido encontradas.  Es posible que tenga que descomentar la extensión en el archivo php.ini, o recompilarlo con el archivo binario apropiado, dependiendo de la versión de PHP.  Por favor, consulte el manual de PHP para más información.',
    'ERR_CHECKSYS_MBSTRING'             => 'Las funciones asociadas con la extensión de PHP para Cadenas Multibyte (mbstring) que son requeridas por la aplicación Sugar no han sido encontradas. <br/><br/>Normalmente, el módulo mbstring no está habilitado por defecto en PHP y debe ser activado con --enable-mbstring en la compilación de PHP. Por favor, consulte el manual de PHP para más información sobre como habilitar el soporte de mbstring.',
    'ERR_CHECKSYS_SESSION_SAVE_PATH_NOT_SET'       => 'La opción session.save_path de su archivo de configuración php (php.ini) no ha sido establecida o ha sido establecida a una carpeta que no existe. Es posible que tenga que establecer la opción save_path setting en php.ini o verificar que existe la carpeta establecida en save_path.',
    'ERR_CHECKSYS_SESSION_SAVE_PATH_NOT_WRITABLE'  => 'La opción session.save_path de su archivo de configuración php (php.ini) ha sido establecida a una carpeta que no es escribible.  Por favor, lleve a cabo los pasos necesarios para hacer la carpeta escribible.  <br>Dependiendo de su Sistema Operativo, es posible que tenga que cambiar los permisos usando chmod 766, o hacer clic con el botón derecho del ratón sobre el archivo para acceder a las propiedades y desmarcar la opción de sólo lectura.',
	'ERR_CHECKSYS_CONFIG_NOT_WRITABLE'  => 'El archivo de configuración (config.php) existe pero no es escribible.  Por favor, lleve a cabo los pasos necesarios para hacerlo escribible.  Dependiendo de su Sistema Operativo, es posible que tenga que cambiar los permisos usando chmod 766, o hacer clic con el botón derecho del ratón sobre el archivo para acceder a las propiedades y desmarcar la opción de sólo lectura.',
    'ERR_CHECKSYS_CUSTOM_NOT_WRITABLE'  => 'El Directorio Custom existe pero no es escribible.  Es posible que tenga que cambiar sus permisos (chmod 766) o hacer clic con el botón derecho del ratón sobre él y desmarcar la opción de sólo lectura, dependiendo de su Sistema Operativo.  Por favor, lleve a cabo los pasos necesarios para que el archivo sea escribible.',
    'ERR_CHECKSYS_FILES_NOT_WRITABLE'   => "Los siguientes archivos o directorios no son escribibles o no existen y no pueden ser creados.  Dependiendo de su Sistema Operativo, corregir esto requerirá cambiar los permisos en los archivos o en su directorio padre (chmod 766), o hacer clic con el botón derecho en el directorio padre y desmarcar la opción 'sólo lectura' y aplicarla en todas las subcarpetas.",
//	'ERR_CHECKSYS_SAFE_MODE'			=> 'El Modo Seguro está activado (por favor, deshabilítelo en php.ini)',
	'ERR_CHECKSYS_SAFE_MODE'			=> 'El Modo Seguro está activado (es posible que desee deshabilitarlo en php.ini)',
	'ERR_CHECKSYS_ZLIB'					=> 'No encontrado: SugarCRM obtiene grandes beneficios de rendimiento con compresión zlib.',
	'ERR_DB_ADMIN'						=> 'El nombre de usuario o contraseña del administrador de base de datos no son válidos, y la conexión a base de datos no ha podido ser establecida. Por favor, introduzca un nombre de usuario y contraseña válidos. (Error: ',
	'ERR_DB_ADMIN_MSSQL'				=> 'El nombre de usuario o contraseña del administrador de base de datos no son válidos, y la conexión a base de datos no ha podido ser establecida. Por favor, introduzca un nombre de usuario y contraseña válidos.',
	'ERR_DB_EXISTS_NOT'					=> 'La base de datos especificada no existe.',
	'ERR_DB_EXISTS_WITH_CONFIG'			=> 'La base de datos ya existe y contiene datos de configuración.  Para ejecutar una instalación con la base de datos elegida, por favor, ejecute de nuevo la instalación y seleccione: "¿Eliminar y crear de nuevo las tablas de SugarCRM?"  Para actualizar, utilice el Asistente de Actualizaciones en la Consola de Administración.  Por favor, lea la documentación referente a actualizaciones <a href="http://www.sugarforge.org/content/downloads/" target="_new">aquí</a>.',
	'ERR_DB_EXISTS'						=> 'El nombre de base de datos suministrado ya existe -- no puede crearse otra con el mismo nombre.',
    'ERR_DB_EXISTS_PROCEED'             => 'El nombre de base de datos suministrado ya existe.  Puede<br>1.  pulsar el botón Atrás y elegir un nuevo nombre <br>2.  hacer clic en Siguiente y continuar, pero todas las tablas existentes en esta base de datos serán eliminadas.  <strong>Esto implica que sus tablas y datos serán eliminados permanentemente.</strong>',
	'ERR_DB_HOSTNAME'					=> 'El nombre de equipo no puede estar vacío.',
	'ERR_DB_INVALID'					=> 'El tipo de base de datos seleccionado no es válido.',
	'ERR_DB_LOGIN_FAILURE_MYSQL'		=> 'El nombre de usuario o contraseña de base de datos no son válidos, y la conexión a base de datos no ha podido ser establecida. Por favor, introduzca un nombre de usuario y contraseña válidos. (Error: ',
	'ERR_DB_LOGIN_FAILURE_MSSQL'		=> 'El nombre de usuario o contraseña de base de datos no son válidos, y la conexión a base de datos no ha podido ser establecida. Por favor, introduzca un nombre de usuario y contraseña válidos. (Error: ',
	'ERR_DB_MYSQL_VERSION1'				=> 'Su versión de MySQL (',
	'ERR_DB_MYSQL_VERSION2'				=> ' no está soportada por Sugar.  Debe instalar una versión que sea compatible con la aplicación Sugar. Por favor, consulte la Matriz de Compatibilidad en las Notas de Lanzamiento para más información sobre las versiones de MySQL soportadas. ',
	'ERR_DB_NAME'						=> 'El nombre de base de datos no puede estar vacío.',
	'ERR_DB_NAME2'						=> "El nombre de base de datos no puede contener los caracteres '\\', '/', o '.'",
	'ERR_DB_PASSWORD'					=> 'Las contraseñas introducidas para el administrador de base de datos de Sugar no coinciden.  Por favor, introduzca de nuevo la misma contraseña en los campos de contraseña.',
	'ERR_DB_PRIV_USER'					=> 'Introduzca un nombre de usuario de base de datos.  El usuario es necesario para la conexión inicial a la base de datos.',
	'ERR_DB_USER_EXISTS'				=> 'El nombre de usuario para la base de datos de Sugar ya existe -- no es posible crear otro con el mismo nombre. Por favor, introduzca un nuevo nombre de usuario.',
	'ERR_DB_USER'						=> 'Introduzca un nombre de usuario para el administrador de la base de datos de Sugar.',
	'ERR_DBCONF_VALIDATION'				=> 'Por favor, corrija los siguientes errores antes de continuar:',
    'ERR_DBCONF_PASSWORD_MISMATCH'      => 'Las contraseñas introducidas para el usuario de base de datos de Sugar no coinciden.  Por favor, introduzca de nuevo la misma contraseña en los campos de contraseña.',
	'ERR_ERROR_GENERAL'					=> 'Se han encontrado los siguientes errores:',
	'ERR_LANG_CANNOT_DELETE_FILE'		=> 'El archivo no puede ser eliminado: ',
	'ERR_LANG_MISSING_FILE'				=> 'El archivo no ha sido encontrado: ',
	'ERR_LANG_NO_LANG_FILE'			 	=> 'No se ha encontrado un paquete de idioma en include/language dentro de: ',
	'ERR_LANG_UPLOAD_1'					=> 'Ha ocurrido un problema con su subida de archivo.  Por favor, inténtelo de nuevo.',
	'ERR_LANG_UPLOAD_2'					=> 'Los paquetes de idioma deben ser archivos ZIP.',
	'ERR_LANG_UPLOAD_3'					=> 'PHP no ha podido mover el archivo temporal al directorio de actualizaciones.',
	'ERR_LICENSE_MISSING'				=> 'Faltan Campos Requeridos',
	'ERR_LICENSE_NOT_FOUND'				=> '¡No se ha encontrado el archivo de licencia!',
	'ERR_LOG_DIRECTORY_NOT_EXISTS'		=> 'El directorio de trazas indicado no es un directorio válido.',
	'ERR_LOG_DIRECTORY_NOT_WRITABLE'	=> 'El directorio de trazas indicado no es un directorio escribible.',
	'ERR_LOG_DIRECTORY_REQUIRED'		=> 'Se requiere un directorio de trazas si desea indicar uno personalizado.',
	'ERR_NO_DIRECT_SCRIPT'				=> 'No se ha podido procesar el script directamente.',
	'ERR_NO_SINGLE_QUOTE'				=> 'No puede utilizarse las comillas simples para ',
	'ERR_PASSWORD_MISMATCH'				=> 'Las contraseñas introducidas para el usuario administrador de Sugar no coinciden.  Por favor, introduzca de nuevo la misma contraseña en los campos de contraseña.',
	'ERR_PERFORM_CONFIG_PHP_1'			=> 'No ha podido escribirse en el archivo <span class=stop>config.php</span>.',
	'ERR_PERFORM_CONFIG_PHP_2'			=> 'Puede continuar esta instalación crando manualmente el archivo config.php y pegando la información de configuración indicada a continuación en el archivo config.php.  Sin embargo, <strong>debe </strong>crear el archivo config.php antes de avanzar al siguiente paso.',
	'ERR_PERFORM_CONFIG_PHP_3'			=> '¿Recordó crear el archivo config.php?',
	'ERR_PERFORM_CONFIG_PHP_4'			=> 'Aviso: No ha podido escribirse en el archivo config.php.  Por favor, asegúrese de que existe.',
	'ERR_PERFORM_HTACCESS_1'			=> 'No ha podido escribirse en el archivo ',
	'ERR_PERFORM_HTACCESS_2'			=> ' .',
	'ERR_PERFORM_HTACCESS_3'			=> 'Si quiere securizar su archivo de trazas, para evitar que sea accesible mediante el navegador web, cree un archivo .htaccess en su directorio de trazas con la línea:',
	'ERR_PERFORM_NO_TCPIP'				=> '<b>No se ha podido detectar una conexión a internet.</b>Por favor, cuando disponga de una, visite <a href="http://www.sugarcrm.com/home/index.php?option=com_extended_registration&task=register">http://www.sugarcrm.com/home/index.php?option=com_extended_registration&task=register</a> para registrarse con SugarCRM. Permitiéndonos saber un poco sobre los planes de su compañía para utilizar SugarCRM, podemos asegurarnos de que siempre estamos suministrando el producto adecuado para las necesidades de su negocio.',
	'ERR_SESSION_DIRECTORY_NOT_EXISTS'	=> 'El directorio de sesión indicado no es un directorio válido.',
	'ERR_SESSION_DIRECTORY'				=> 'El directorio de sesión indicado no es un directorio escribible.',
	'ERR_SESSION_PATH'					=> 'Se requiere un directorio de sesión si desea indicar uno personalizado.',
	'ERR_SI_NO_CONFIG'					=> 'No ha incluido config_si.php en la carpeta raíz de documentos, o no ha definido $sugar_config_si en config.php',
	'ERR_SITE_GUID'						=> 'Se requiere un ID de Aplicación si desea indicar uno personalizado.',
	'ERR_UPLOAD_MAX_FILESIZE'			=> 'Aviso: Su configuración de PHP debería ser cambiada para permitir subidas de archivos de al menos 6MB.',
    'LBL_UPLOAD_MAX_FILESIZE_TITLE'     => 'Tamaño para Subida de Archivos',
	'ERR_URL_BLANK'						=> 'Introduce el URL base para la instancia de Sugar.',
	'ERR_UW_NO_UPDATE_RECORD'			=> 'No se ha localizado el registro de instalación de',
	'ERROR_FLAVOR_INCOMPATIBLE'			=> 'El archivo subido no es compatible con esta edición (Community Edition, Professional, o Enterprise) de Sugar: ',
	'ERROR_LICENSE_EXPIRED'				=> "Error: Su licencia caducó hace ",
	'ERROR_LICENSE_EXPIRED2'			=> " día(s).   Por favor, vaya a la <a href='index.php?action=LicenseSettings&module=Administration'>'\"Administración de Licencias\"</a>, en la pantalla de Administración, para introducir su nueva clave de licencia.  Si no introduce una nueva clave de licencia en 30 días a partir de la caducidad de su clave de licencia, no podrá iniciar la sesión en esta aplicación.",
	'ERROR_MANIFEST_TYPE'				=> 'El archivo de manifiesto debe especificar el tipo de paquete.',
	'ERROR_PACKAGE_TYPE'				=> 'El archivo de manifiesto debe especifica un tipo de paquete no reconocido',
	'ERROR_VALIDATION_EXPIRED'			=> "Error: Su clave de validación caducó hace ",
	'ERROR_VALIDATION_EXPIRED2'			=> " día(s).   Por favor, vaya a la <a href='index.php?action=LicenseSettings&module=Administration'>'\"Administración de Licencias\"</a>, en la pantalla de Administración, para introducir su nueva clave de validación.  Si no introduce una nueva clave de validación en 30 días a partir de la caducidad de su clave de validación, no podrá iniciar la sesión en esta aplicación.",
	'ERROR_VERSION_INCOMPATIBLE'		=> 'El archivo subido no es compatible con esta versión de Sugar: ',
	
	'LBL_BACK'							=> 'Atrás',
    'LBL_CANCEL'                        => 'Cancelar',
    'LBL_ACCEPT'                        => 'Acepto',
	'LBL_CHECKSYS_1'					=> 'Para que su instalación de SugarCRM funcione correctamenteto, asegúrese de que todos los elementos de comprobación listados a continuación están en verde. Si alguno está en rojo, por favor, realice los pasos necesarios para corregirlos. <BR><BR> Para encontrar ayuda sobre estas comprobaciones del sistema, por favor visite el <a href="http://www.sugarcrm.com/crm/installation" target="_blank">Sugar Wiki</a>',
	'LBL_CHECKSYS_CACHE'				=> 'Subdirectorios de Caché Escribibles',
//	'LBL_CHECKSYS_CALL_TIME'			=> 'PHP "Allow Call Time Pass Reference" Habilitado',
    'LBL_DROP_DB_CONFIRM'               => 'El Nombre de Base de datos suministrado ya existe.<br>Tiene las siguientes opciones:<br>1.  Hacer clic en el botón Cancelar y seleccionar un nuevo nombre de base de datos, o <br>2.  Hacer clic en el botón Aceptar y continuar.  Todas las tablas existentes en la base de datos serán eliminadas. <strong>Esto implica que todas sus tablas y datos actuales desaparecerán.</strong>',
	'LBL_CHECKSYS_CALL_TIME'			=> 'PHP "Allow Call Time Pass Reference" Deshabilitado',
	'LBL_CHECKSYS_COMPONENT'			=> 'Componente',
	'LBL_CHECKSYS_COMPONENT_OPTIONAL'	=> 'Componentes Opcionales',
	'LBL_CHECKSYS_CONFIG'				=> 'Archivo de Configuración de SugarCRM (config.php) Escribible',
	'LBL_CHECKSYS_CURL'					=> 'Módulo cURL',
    'LBL_CHECKSYS_SESSION_SAVE_PATH'    => 'Configuración de la Ruta de Almacenamiento de Sesiones',
	'LBL_CHECKSYS_CUSTOM'				=> 'Directorio Personalizado (custom) Escribible',
	'LBL_CHECKSYS_DATA'					=> 'Subdirectorios de Datos Escribibles',
	'LBL_CHECKSYS_IMAP'					=> 'Módulo IMAP',
	'LBL_CHECKSYS_MQGPC'				=> 'Magic Quotes GPC',
	'LBL_CHECKSYS_MBSTRING'				=> 'Módulo de Cadenas MB',
	'LBL_CHECKSYS_MEM_OK'				=> 'Correcto (Sin Límite)',
	'LBL_CHECKSYS_MEM_UNLIMITED'		=> 'Correcto (Sin Límite)',
	'LBL_CHECKSYS_MEM'					=> 'Límite de Memoria PHP >= ',
	'LBL_CHECKSYS_MODULE'				=> 'Subdirectorios y Archivos de Módulos Escribibles',
	'LBL_CHECKSYS_MYSQL_VERSION'		=> 'Versión de MySQL',
	'LBL_CHECKSYS_NOT_AVAILABLE'		=> 'No Disponible',
	'LBL_CHECKSYS_OK'					=> 'Correcto',
	'LBL_CHECKSYS_PHP_INI'				=> '<b>Nota:</b> Su archivo de configuración de PHP (php.ini) está localizado en:',
	'LBL_CHECKSYS_PHP_OK'				=> 'Correcto (ver ',
	'LBL_CHECKSYS_PHPVER'				=> 'Versión de PHP',
	'LBL_CHECKSYS_RECHECK'				=> 'Comprobar de nuevo',
	'LBL_CHECKSYS_SAFE_MODE'			=> 'Modo Seguro de PHP Deshabilitado',
	'LBL_CHECKSYS_SESSION'				=> 'Ruta de Almacenamiento de Sesión Escribible (',
	'LBL_CHECKSYS_STATUS'				=> 'Estado',
	'LBL_CHECKSYS_TITLE'				=> 'Aceptación de Comprobaciones del Sistema',
	'LBL_CHECKSYS_VER'					=> 'Encontrado: ( ver ',
	'LBL_CHECKSYS_XML'					=> 'Análisis XML',
	'LBL_CHECKSYS_ZLIB'					=> 'Módulo de Compresión ZLIB',
    'LBL_CHECKSYS_FIX_FILES'            => 'Por favor, corrija los siguientes archivos o directorios antes de continuar:',
    'LBL_CHECKSYS_FIX_MODULE_FILES'     => 'Por favor, corrija los siguientes directorios de módulos y los archivos en ellos contenidos antes de continuar:',
    'LBL_CLOSE'							=> 'Cerrar',
    'LBL_THREE'                         => '3',
	'LBL_CONFIRM_BE_CREATED'			=> 'será creado',
	'LBL_CONFIRM_DB_TYPE'				=> 'Tipo de Base de datos',
	'LBL_CONFIRM_DIRECTIONS'			=> 'Por favor, confirme la siguiente configuración.  Si desea cambiar cualquiera de los valores, haga clic en "Atrás" para editarlos.  En otro caso, haga clic en "Siguiente" para iniciar la instalación.',
	'LBL_CONFIRM_LICENSE_TITLE'			=> 'Información de Licencia',
	'LBL_CONFIRM_NOT'					=> 'no',
	'LBL_CONFIRM_TITLE'					=> 'Confirmar Configuración',
	'LBL_CONFIRM_WILL'					=> '',
	'LBL_DBCONF_CREATE_DB'				=> 'Crear Base de datos',
	'LBL_DBCONF_CREATE_USER'			=> 'Crear Usuario',
	'LBL_DBCONF_DB_DROP_CREATE_WARN'	=> 'Advertencia: Todos los datos de Sugar serán eliminados<br>si se marca esta opción.',
	'LBL_DBCONF_DB_DROP_CREATE'			=> '¿Eliminar las tablas de Sugar actuales y crearlas de nuevo?',
	'LBL_DBCONF_DB_NAME'				=> 'Nombre de Base de datos',
    'LBL_DBCONF_DB_DROP'                => 'Eliminar Tablas',
	'LBL_DBCONF_DB_PASSWORD'			=> 'Contraseña del Usuario de Base de datos de Sugar',
	'LBL_DBCONF_DB_PASSWORD2'			=> 'Introduzca de nuevo la Contraseña del Usuario de Base de datos de Sugar',
	'LBL_DBCONF_DB_USER'				=> 'Usuario de Base de datos de Sugar',
	'LBL_DBCONF_SUGAR_DB_USER'			=> 'Usuario de Base de datos de Sugar',
    'LBL_DBCONF_DB_ADMIN_USER'          => 'Nombre de usuario del Administrador de Base de datos',
    'LBL_DBCONF_DB_ADMIN_PASSWORD'      => 'Contraseña del Administrador de Base de datos',
	'LBL_DBCONF_DEMO_DATA'				=> '¿Introducir Datos de Demostración en la Base de datos?',
    'LBL_DBCONF_DEMO_DATA_TITLE'        => 'Seleccione los Datos de Demo',
	'LBL_DBCONF_HOST_NAME'				=> 'Nombre de Equipo',
    'LBL_DBCONF_HOST_NAME_MSSQL'        => 'Nombre de Equipo \ Instancia de Equipo',
	'LBL_DBCONF_INSTRUCTIONS'			=> 'Por favor, introduzca la información de configuración de su base de datos a continuación. Si no está seguro de qué datos utilizar, le sugerimos que utilice los valores por defecto.',
	'LBL_DBCONF_MB_DEMO_DATA'			=> 'Utilizar texto multi-byte en datos de demostración?',
    'LBL_DBCONFIG_MSG2'                 => 'Nombre del servidor web o máquina (equipo) en el que la base de datos está ubicada:',
    'LBL_DBCONFIG_MSG3'                 => 'Nombre de la base de datos que albergará los datos de la instancia de Sugar que va a instalar:',
//'LBL_DBCONF_I18NFIX'              => 'Aplicar expansión de columnas de base de datos para los tipos varchar y char (hasta 255) para datos multi-byte?',
	'LBL_DBCONF_PRIV_PASS'				=> 'Contraseña de Usuario Privilegiado de Base de datos',
	'LBL_DBCONF_PRIV_USER_2'			=> '¿Corresponde la Cuenta de Base de datos Anterior a un Usuario Privilegiado?',
	'LBL_DBCONF_PRIV_USER_DIRECTIONS'	=> 'Este usuario privilegiado de base de datos debe tener los permisos adecuados para crear una base de datos, eliminar/crear tablas, y crear un usuario.  Este usuario privilegiado de base de datos sólo se utilizará para realizar estas tareas según sean necesarias durante el proceso de instalación.  También puede utilizar el mismo usuario de base de datos anterior si tiene los privilegios suficientes.',
    'LBL_DBCONFIG_B_MSG1'               => 'Para configurar la base de datos de Sugar, es necesario el nombre de usuario y contraseña del administrador de base de datos que puede crear tablas de base de datos y usarios y que puede escribir a la base de datos.',
    'LBL_DBCONFIG_SECURITY'             => 'Por motivos de seguridad, puede especificar un usuario de base de datos exclusivo para conectarse a la base de datos de Sugar.  Este usuario debe ser capaz de escribir, actualizar y recuparar datos en la base de datos de Sugar que será creada para esta instancia.  Este usuario puede ser el administrador de base de datos anteriormente especificado, o puede introducir la información de un usuario de base de datos nuevo o existente.',
    'LBL_DBCONFIG_AUTO_DD'              => 'Hágalo por mi',
    'LBL_DBCONFIG_PROVIDE_DD'           => 'Introduzca un usuario existente',
    'LBL_DBCONFIG_CREATE_DD'            => 'Defina el usuario a crear',
    'LBL_DBCONFIG_SAME_DD'              => 'El mismo que el usuario Administrador',
	//'LBL_DBCONF_I18NFIX'              => 'Apply database column expansion for varchar and char types (up to 255) for multi-byte data?',
    'LBL_MSSQL_FTS'                     => 'Búsqueda de Texto Completo',
    'LBL_MSSQL_FTS_INSTALLED'           => 'Instalado',
    'LBL_MSSQL_FTS_INSTALLED_ERR1'      => 'La búsqueda de texto completo no está instalada.',
    'LBL_MSSQL_FTS_INSTALLED_ERR2'      => 'Puede continuar con la instalación, pero no podrá utilizar la funcionalidad de Búsqueda de Texto Completo salvo que reinstale su servidor SQL Server con la Búsqueda de Texto Completo habilitada.  Por favor, consulte la guía de instalación de SQL Server para más información sobre cómo hacer esto, o contacte con su Administrador.',
	'LBL_DBCONF_PRIV_PASS'				=> 'Contraseña del Usuario Privilegiado de Base de datos',
	'LBL_DBCONF_PRIV_USER'				=> 'Nombre del Usuario Privilegiado de Base de datos',
	'LBL_DBCONF_TITLE'					=> 'Configuración de Base de datos',
    'LBL_DBCONF_TITLE_NAME'             => 'Introduzca el Nombre de Base de Datos',
    'LBL_DBCONF_TITLE_USER_INFO'        => 'Introduzca la Información de Usuario de Base de Datos',
	'LBL_DISABLED_DESCRIPTION_2'		=> 'Después de que se haya realizado este cambio, puede hacer clic en el botón "Iniciar" situado abajo, para iniciar su instalación.  <i>Una vez se haya completado la instalación, es probable que desee cambiar el valor para la variable \'installer_locked\' a \'true\'.</i>',
	'LBL_DISABLED_DESCRIPTION'			=> 'El instalador ya ha sido ejecutado. Como medida de seguridad, se ha deshabilitado para que no sea ejecutado por segunda vez.  Si está totalmente seguro de que desea ejecutarlo de nuevo, por favor vaya a su archivo config.php y localice (o añada) una variable llamada  \'installer_locked\' y establézcala a \'false\'.  La línea debería quedar como lo siguiente:',
	'LBL_DISABLED_HELP_1'				=> 'Para ayuda sobre la instalación, por favor visite los foros de soporte de SugarCRM',
    'LBL_DISABLED_HELP_LNK'             => 'http://www.sugarcrm.com/forums/',
	'LBL_DISABLED_HELP_2'				=> '',
	'LBL_DISABLED_TITLE_2'				=> 'La Instalación de SugarCRM ha sido Deshabilitada',
	'LBL_DISABLED_TITLE'				=> 'Instalación de SugarCRM Deshabilitada',
	'LBL_EMAIL_CHARSET_DESC'			=> 'Juego de caracteres más utilizado en su configuración regional',
	'LBL_EMAIL_CHARSET_TITLE'			=> 'Configuración de Correo Saliente',
    'LBL_EMAIL_CHARSET_CONF'            => 'Juego de Caracteres para Correo Saliente ',
	'LBL_HELP'							=> 'Ayuda',
    'LBL_INSTALL'                       => 'Instalar',
    'LBL_INSTALL_TYPE_TITLE'            => 'Opciones de Instalación',
    'LBL_INSTALL_TYPE_SUBTITLE'         => 'Seleccione un Tipo de Instalación',
    'LBL_INSTALL_TYPE_TYPICAL'          => ' <b>Instalación Típica</b>',
    'LBL_INSTALL_TYPE_CUSTOM'           => ' <b>Instalación Personalizada</b>',
    'LBL_INSTALL_TYPE_MSG1'             => 'La clave se requiere para la funcionalidad general de la aplicación, pero no es necesaria para la instalación. No necesita introducir una clave válida en estos momentos, pero deberá introducirla tras la instalación de la aplicación.',
    'LBL_INSTALL_TYPE_MSG2'             => 'Requiere la mínima información posible para la instalación. Recomendada para usuarios nóveles.',
    'LBL_INSTALL_TYPE_MSG3'             => 'Provee opciones adicionales a establecer durante la instalación. La mayoría de éstas están también disponibles tras la instalación en las pantallas de adminitración. Recomendado para usuarios avanzados.',
	'LBL_LANG_1'						=> 'Para utilizar un idioma en Sugar distinto al del idioma por defecto (Inglés de EEUU), puede subir e instalar ahora el paquete de idioma. También podrá subir e instalar paquetes de idioma desde la aplicación Sugar.  Si quiere saltarse este paso, haga clic en Siguiente.',
	'LBL_LANG_BUTTON_COMMIT'			=> 'Proceder',
	'LBL_LANG_BUTTON_REMOVE'			=> 'Quitar',
	'LBL_LANG_BUTTON_UNINSTALL'			=> 'Desinstalar',
	'LBL_LANG_BUTTON_UPLOAD'			=> 'Subir',
	'LBL_LANG_NO_PACKS'					=> 'ninguno',
	'LBL_LANG_PACK_INSTALLED'			=> 'Los siguientes paquetes de idioma han sido instalados: ',
	'LBL_LANG_PACK_READY'				=> 'Los siguientes paquetes de idioma están listos para ser instalados: ',
	'LBL_LANG_SUCCESS'					=> 'El paquete de idioma ha sido subido con éxito.',
	'LBL_LANG_TITLE'			   		=> 'Paquete de Idioma',
	'LBL_LANG_UPLOAD'					=> 'Subir un Paquete de Idioma',
	'LBL_LICENSE_ACCEPTANCE'			=> 'Aceptación de Licencia',
    'LBL_LICENSE_CHECKING'              => 'Haciendo comprobaciones de compatibilidad del sistema.',
    'LBL_LICENSE_CHKENV_HEADER'         => 'Comprobando Entorno',
    'LBL_LICENSE_CHKDB_HEADER'          => 'Validando Credenciales de BD.',
    'LBL_LICENSE_CHECK_PASSED'          => 'El sistema ha pasado las pruebas de compatibilidad.',
    'LBL_LICENSE_REDIRECT'              => 'Redirigiendo a ',
	'LBL_LICENSE_DIRECTIONS'			=> 'Si tiene información acerca de su licencia, por favor introdúzcala en los siguientes campos.',
	'LBL_LICENSE_DOWNLOAD_KEY'			=> 'Introduzca Clave de Descarga',
	'LBL_LICENSE_EXPIRY'				=> 'Fecha de Caducidad',
	'LBL_LICENSE_I_ACCEPT'				=> 'Acepto',
	'LBL_LICENSE_NUM_USERS'				=> 'Número de Usuarios',
	'LBL_LICENSE_OC_DIRECTIONS'			=> 'Por favor, introduzca el nombre de clientes desconectados adquiridos.',
	'LBL_LICENSE_OC_NUM'				=> 'Número de Licencias de Cliente Desconectado',
	'LBL_LICENSE_OC'					=> 'Licencias de Cliente Desconectado',
	'LBL_LICENSE_PRINTABLE'				=> ' Vista Imprimible ',
	'LBL_PRINT_SUMM'						=> 'Imprimir Resumen',
	'LBL_LICENSE_TITLE_2'				=> 'Licencia de SugarCRM',
	'LBL_LICENSE_TITLE'					=> 'Información de Licencia',
	'LBL_LICENSE_USERS'					=> 'Usuarios con Licencia',
	
	'LBL_LOCALE_CURRENCY'				=> 'Configuración de Moneda',
	'LBL_LOCALE_CURR_DEFAULT'			=> 'Moneda por Defecto',
	'LBL_LOCALE_CURR_SYMBOL'			=> 'Símbolo de Moneda',
	'LBL_LOCALE_CURR_ISO'				=> 'Código de Moneda (ISO 4217)',
	'LBL_LOCALE_CURR_1000S'				=> 'Separador de miles',
	'LBL_LOCALE_CURR_DECIMAL'			=> 'Separador Decimal',
	'LBL_LOCALE_CURR_EXAMPLE'			=> 'Ejemplo',
	'LBL_LOCALE_CURR_SIG_DIGITS'		=> 'Dígitos Significavos',
	'LBL_LOCALE_DATEF'					=> 'Formato de Fecha por Defecto',
	'LBL_LOCALE_DESC'					=> 'Las opciones de configuración regional especificadas se reflejará a nivel global en la instancia de Sugar.',
	'LBL_LOCALE_EXPORT'					=> 'Juego de caracteres de Importación/Exportación <i>(Correo, .csv, vCard, PDF, importación de datos)</i>',
	'LBL_LOCALE_EXPORT_DELIMITER'		=> 'Delimitador para Exportación (.csv)',
	'LBL_LOCALE_EXPORT_TITLE'			=> 'Configuración de Importación/Exportación',
	'LBL_LOCALE_LANG'					=> 'Idioma por Defecto',
	'LBL_LOCALE_NAMEF'					=> 'Formato de Nombre por Defecto',
	'LBL_LOCALE_NAMEF_DESC'				=> 's Título<br />f Nombre<br />l Apellido',
	'LBL_LOCALE_NAME_FIRST'				=> 'David',
	'LBL_LOCALE_NAME_LAST'				=> 'Livingstone',
	'LBL_LOCALE_NAME_SALUTATION'		=> 'Dr.',
	'LBL_LOCALE_TIMEF'					=> 'Formato de Hora por Defecto',
	'LBL_LOCALE_TITLE'					=> 'Configuración Regional',
    'LBL_CUSTOMIZE_LOCALE'              => 'Personalizar Configuración Regional',
	'LBL_LOCALE_UI'						=> 'Interfaz de Usuario',
	
	'LBL_ML_ACTION'						=> 'Acción',
	'LBL_ML_DESCRIPTION'				=> 'Descripción',
	'LBL_ML_INSTALLED'					=> 'Fecha de Instalación',
	'LBL_ML_NAME'						=> 'Nombre',
	'LBL_ML_PUBLISHED'					=> 'Fecha de Publicación',
	'LBL_ML_TYPE'						=> 'Tipo',
	'LBL_ML_UNINSTALLABLE'				=> 'No desinstalable',
	'LBL_ML_VERSION'					=> 'Versión',
	'LBL_MSSQL'							=> 'SQL Server',
	'LBL_MSSQL2'                        => 'SQL Server (FreeTDS)',
	'LBL_MYSQL'							=> 'MySQL',
	'LBL_NEXT'							=> 'Siguiente',
	'LBL_NO'							=> 'No',
	'LBL_ORACLE'						=> 'Oracle',
	'LBL_PERFORM_ADMIN_PASSWORD'		=> 'Estableciendo la contraseña del admin del sitio',
	'LBL_PERFORM_AUDIT_TABLE'			=> 'tabla de auditoría / ',
	'LBL_PERFORM_CONFIG_PHP'			=> 'Creando el archivo de configuración de Sugar',
	'LBL_PERFORM_CREATE_DB_1'			=> '<b>Creando la base de datos</b> ',
	'LBL_PERFORM_CREATE_DB_2'			=> ' <b>en</b> ',
	'LBL_PERFORM_CREATE_DB_USER'		=> 'Creando el usuario y la contraseña de Base de datos...',
	'LBL_PERFORM_CREATE_DEFAULT'		=> 'Creando datos de Sugar predeterminados',
	'LBL_PERFORM_CREATE_LOCALHOST'		=> 'Creando el usuario y la contraseña de Base de datos para localhost...',
	'LBL_PERFORM_CREATE_RELATIONSHIPS'	=> 'Creando tablas de relaciones de Sugar',
	'LBL_PERFORM_CREATING'				=> 'creando / ',
	'LBL_PERFORM_DEFAULT_REPORTS'		=> 'Creando informes predefinidos',
	'LBL_PERFORM_DEFAULT_SCHEDULER'		=> 'Creando trabajos del planificador por defecto',
	'LBL_PERFORM_DEFAULT_SETTINGS'		=> 'Insertando configuración por defecto',
	'LBL_PERFORM_DEFAULT_USERS'			=> 'Creando usuarios por defecto',
	'LBL_PERFORM_DEMO_DATA'				=> 'Insertando en las tablas de base de datos datos de demostración (esto puede llevar un rato)',
	'LBL_PERFORM_DONE'					=> 'hecho<br>',
	'LBL_PERFORM_DROPPING'				=> 'eliminando / ',
	'LBL_PERFORM_FINISH'				=> 'Finalizado',
	'LBL_PERFORM_LICENSE_SETTINGS'		=> 'Actualizando información de licencia',
	'LBL_PERFORM_OUTRO_1'				=> 'La instalación de Sugar ',
	'LBL_PERFORM_OUTRO_2'				=> ' ha sido completada.',
	'LBL_PERFORM_OUTRO_3'				=> 'Tiempo total: ',
	'LBL_PERFORM_OUTRO_4'				=> ' segundos.',
	'LBL_PERFORM_OUTRO_5'				=> 'Memoria utiliza aproximadamente: ',
	'LBL_PERFORM_OUTRO_6'				=> ' bytes.',
	'LBL_PERFORM_OUTRO_7'				=> 'Su sistema ha sido instalado y configurado para su uso.',
	'LBL_PERFORM_REL_META'				=> 'metadatos de relaciones ... ',
	'LBL_PERFORM_SUCCESS'				=> '¡Éxito!',
	'LBL_PERFORM_TABLES'				=> 'Creando las tables de aplicación de Sugar, tablas de auditoría, y metadatos de relaciones',
	'LBL_PERFORM_TITLE'					=> 'Realizar Instalación',
	'LBL_PRINT'							=> 'Imprimir',
	'LBL_REG_CONF_1'					=> 'Por favor, complete el siguiente breve formulario para recibir anuncios sobre el producto, noticias sobre formación, ofertas especiales e invitaciones especiales a eventos de SugarCRM. No vendemos, alquilamos, compartimos, o distribuimos de ningún otro modo a terceras partes la información aquí recogida.',
	'LBL_REG_CONF_2'					=> 'Su nombre y dirección de correo electrónico son los únicos campos requeridos para el registro. El resto de campos son opcionales, pero de mucho valor. No vendemos, alquilamos, compartimos, o distribuimos en modo alguno la información aquí recogida a terceros.',
	'LBL_REG_CONF_3'					=> 'Gracias por registrarse. Haga clic en el botón Finalizar para iniciar una sesión en SugarCRM. Necesitará iniciar la sesión por primera vez utilizando el nombre de usuario "admin" y la contraseña que introdujo en el paso 2.',
	'LBL_REG_TITLE'						=> 'Registro',
    'LBL_REG_NO_THANKS'                 => 'No Gracias',
    'LBL_REG_SKIP_THIS_STEP'            => 'Saltar este Paso',
	'LBL_REQUIRED'						=> '* Campo requerido',
	'LBL_SITECFG_ADMIN_Name'			=> 'Nombre del Administrador de la Aplicación Sugar',
	'LBL_SITECFG_ADMIN_PASS_2'			=> 'Introduzca de nuevo la Contraseña del Usuario Admin de Sugar',
	'LBL_SITECFG_ADMIN_PASS_WARN'		=> 'Precaución: Esto substituirá la contraseña de admin de cualquier instalación previa.',
	'LBL_SITECFG_ADMIN_PASS'			=> 'Contraseña del Usuario Admin de Sugar',
	'LBL_SITECFG_APP_ID'				=> 'ID de Aplicación',
	'LBL_SITECFG_CUSTOM_ID_DIRECTIONS'	=> 'Si está seleccionado, debe introducir un ID de aplicación para sustituir al ID autogenerado. El ID asegura que las sesiones de una instancia de Sugar no son utilizadas por otras instancias.  Si tiene un cluster de instalaciones Sugar, todas deben compartir el mismo ID de aplicación.',
	'LBL_SITECFG_CUSTOM_ID'				=> 'Proveer Su Propio ID de Aplicación',
	'LBL_SITECFG_CUSTOM_LOG_DIRECTIONS'	=> 'Si está seleccionado, debe especificar un directorio de trazas para sustituir al directorio por defecto de trazas de Sugar.  Independientemente de donde resida el archivo de trazas, el acceso al mismo a través del navegador será restringido mediante una redirección definida en un archivo .htaccess.',
	'LBL_SITECFG_CUSTOM_LOG'			=> 'Usar un Directorio Personalizado de Trazas',
	'LBL_SITECFG_CUSTOM_SESSION_DIRECTIONS'	=> 'Si está seleccionado, debe especificar una carpeta segura para almacenar la información de las sesiones de Sugar. Esto se hace para evitar que los datos de la sesión sean vulnerables en servidores compartidos.',
	'LBL_SITECFG_CUSTOM_SESSION'		=> 'Utilizar un Directorio Personalizado de Sesiones para Sugar',
	'LBL_SITECFG_DIRECTIONS'			=> 'Por favor, introduzca la información de configuración de su sitio a continuación. Si no está seguro del significado de los campos, le sugerimos que utilice los valores por defecto.',
	'LBL_SITECFG_FIX_ERRORS'			=> '<b>Por favor, corrija los siguientes errores antes de continuar:</b>',
	'LBL_SITECFG_LOG_DIR'				=> 'Directorio de Trazas',
	'LBL_SITECFG_SESSION_PATH'			=> 'Ruta al Directorio de Sesiones<br>(debe ser escribible)',
	'LBL_SITECFG_SITE_SECURITY'			=> 'Seleccione Opciones de Seguridad',
	'LBL_SITECFG_SUGAR_UP_DIRECTIONS'	=> 'Si está seleccionado, el sistema comprobará periódicamente si hay disponibles versiones actualizadas de la aplicación.',
	'LBL_SITECFG_SUGAR_UP'				=> '¿Comprobar Automáticamente Actualizaciones?',
	'LBL_SITECFG_SUGAR_UPDATES'			=> 'Configuración de Actualizaciones de Sugar',
	'LBL_SITECFG_TITLE'					=> 'Configuración del Sitio',
    'LBL_SITECFG_TITLE2'                => 'Identifique su Instancia de Sugar',
    'LBL_SITECFG_SECURITY_TITLE'        => 'Seguridad del Sitio',
	'LBL_SITECFG_URL'					=> 'URL de la Instancia de Sugar',
	'LBL_SITECFG_USE_DEFAULTS'			=> '¿Usar valores por defecto?',
	'LBL_SITECFG_ANONSTATS'             => 'Enviar Estadísticas de Uso Anónimas?',
	'LBL_SITECFG_ANONSTATS_DIRECTIONS'        => 'Si está seleccionado, Sugar enviará estadísticas anónimas sobre su instalación a SugarCRM Inc. cada vez que su sistema compruebe la existencia de nuevas versiones. Esta información nos ayudará a entender mejor cómo la aplicación es usada y guiar así las mejoras al producto.',
    'LBL_SITECFG_URL_MSG'               => 'Introduzca el URL que será utilizado para acceder a la instancia de Sugar tras la instalación. Este URL también se usará como base para los URLs de las páginas de la aplicación Sugar. El URL debería incluir el nombre de servidor web o máquina, o su dirección IP.',
    'LBL_SITECFG_SYS_NAME_MSG'          => 'Introduzca un nombre para su sistema.  Este nombre se mostrará en la barra de título del navegador cuando los usuarios visiten la aplicación Sugar.',
    'LBL_SITECFG_PASSWORD_MSG'          => 'Tras la instalación, necesitará usar el usuario administrador de Sugar (nombre de usuario = admin) para iniciar la sesión en la instancia de Sugar.  Introduzca una contraseña para este usuario administrador. Esta contraseña puede ser cambiada tras el inicio de sesión inicial.',
    'LBL_SYSTEM_CREDS'                  => 'Credenciales del Sistema',
    'LBL_SYSTEM_ENV'                    => 'Entorno del Sistema',
	'LBL_START'							=> 'Iniciar',
    'LBL_SHOW_PASS'                     => 'Mostrar Contraseñas',
    'LBL_HIDE_PASS'                     => 'Ocultar Contraseñas',
    'LBL_HIDDEN'                        => '<i>(oculto)</i>',
//	'LBL_NO_THANKS'						=> 'Continuar con la instalación',
	'LBL_CHOOSE_LANG'					=> '<b>Elija su idioma</b>',
	'LBL_STEP'							=> 'Paso',
	'LBL_TITLE_WELCOME'					=> 'Bienvenido a SugarCRM ',
	'LBL_WELCOME_1'						=> 'Este instalador crea las tablas de base de datos de SugarCRM y establece las variables de configuración necesarias para iniciar. El proceso completo debería tardar unos diez minutos.',
	'LBL_WELCOME_2'						=> 'Para encontrar documentación sobre la instalación, por favor visite el <a href="http://www.sugarcrm.com/crm/installation" target="_blank">Sugar Wiki</a>.  <BR><BR> También puede encontrar ayuda dela Comunidad Sugar en los <a href="http://www.sugarcrm.com/forums/" target="_blank">Sugar Forums</a>.',
    //welcome page variables
    'LBL_TITLE_ARE_YOU_READY'            => '¿Está listo para proceder con la instalación?',
    'REQUIRED_SYS_COMP' => 'Componentes del Sistema Requeridos',
    'REQUIRED_SYS_COMP_MSG' =>
                    'Antes de empezar, por favor asegúrese de que tiene las versiones soportadas de los siguientes componentes
                      del sistema:<br>
                      <ul>
                      <li> Base de Datos/Sistema de Gestión de Base de Datos (Ejemplos: MySQL, SQL Server, Oracle)</li>
                      <li> Servidor Web (Apache, IIS)</li>
                      </ul>
                      Consulte la Matriz de Compatibilidad en las Notas de Lanzamiento para 
                      los componentes del sistema compatibles para la versión de Sugar que está instalando.<br>',
    'REQUIRED_SYS_CHK' => 'Comprobación Inicial del Sistema',
    'REQUIRED_SYS_CHK_MSG' =>
                    'Cuando inicie el proceso de instalación, se realizará una comprobación del sistema en el servidor web en el que los archivos de Sugar están localizados para
                      asegurar que el sistema está debidamente configurado y tiene todos los componentes necesarios
                      para completar la instalación con éxito. <br><br>
                      El sistema comprueba lo siguiente:<br>
                      <ul>
                      <li>Que la <b>versión de PHP</b> &#8211; sea compatible 
                      con la aplicación</li>
                                        <li><b>Las Variables de Sesión</b> &#8211; deben funcionar adecuadamente</li>
                                            <li> <b>Las Cadenas MB</b> &#8211; deben estar instaladas y habilitadas en php.ini</li>

                      <li> <b>El Soporte de Base de Datos</b> &#8211; debe existir para MySQL, SQL
                      Server u Oracle</li>

                      <li> <b>Config.php</b> &#8211; debe existir y tiene que tener los permisos
                                  adecuados para que sea escribible</li>
					  <li>Los siguientes archivos de Sugar deben ser escribibles:<ul><li><b>/custom</li>
<li>/cache</li>
<li>/modules</b></li></ul></li></ul>
                                  Si la comprobación falla, no podrá continuar con la instalación. Un mensaje de error será mostrado, explicándole por qué su sistema
                                  no ha pasado las comprobaciones.
                                  Tras realizar los cambios necesarios, puede realizar las comprobaciones 
                                  del sistema de nuevo para continuar con la instalación.<br>',
    'REQUIRED_INSTALLTYPE' => 'Instalación Típica o Personalizada',
    'REQUIRED_INSTALLTYPE_MSG' =>
                    'Tras la comprobación del sistema, puede elejir entre 
                      la instalación Típica o la Personalizada.<br><br>
                      Tanto para la instalación <b>Típica</b> como para la <b>Personalizada</b>, necesitará saber lo siguiente:<br>
                      <ul>
                      <li> <b>Tipo de base de datos</b> que almacenará los datos de Sugar <ul><li>Tipos de base de datos compatibles: 
                      MySQL, MS SQL Server, Oracle.<br><br></li></ul></li>
                      <li> <b>Nombre del servidor web</b> o máquina (equipo) en el que la base de datos setá ubicada
                      <ul><li>Esto puede ser <i>localhost</i> si la base de datos está en su equipo local o en en el mismo servidor web o máquina que sus archivos Sugar.<br><br></li></ul></li>
                      <li><b>Nombre de la base de datos</b> que desea utilizar para almacenar los datos de Sugar</li>
                        <ul>
                          <li> Puede que ya disponga de una base de datos que quiera utilizar. Si proporciona
                          el nombre de una base de datos existente, las tablas de la base de datos serán eliminadas
                          durante la instalación, cuando se defina el esquema para la base de datos de Sugar.</li>
                          <li> Si no tiene una base de datos, el nombre que proporcione se utilizará para la nueva 
                          base de datos que será creada para la instacia durante la instalación.<br><br></li>
                        </ul>
                      <li><b>Nombre y contraseña del usuario administrador de Base de datos</b> <ul><li>El administrador de base de datos debería ser capaz de crear tablas y usuarios y de escribir en base de datos.</li><li>Puede que necesite
                      contactar con su administrador de base de datos para que le proporcione esta información si la base de datos no está
                      ubicada en su equipo local y/o si usted no es el administrador de base de datos.<br><br></ul></li></li>
                      <li> <b>Nombre y contraseña del usuario de base de datos de Sugar</b>
                      </li>
                        <ul>
                          <li> El usuario puede ser el administrador de base de datos, o puede proporcionar el nombre de 
                          otro usuario de base de datos existente. </li>
                          <li> Si quiere crear un nuevo usuario de base de datos para este propósito, podrá
                          proporcionar un nuevo nombre de usuario y contraseña durante el proceso de instalación,
                          y el usuario será creado durante la instalación. </li>
                        </ul></ul><p>

                      Para la instalación <b>Personalizada</b>, también necesitará conocer lo siguiente:<br>
                      <ul>
                      <li> <b>El URL que se utilizará para acceder a la instancia de Sugar</b> tras su instalación.
                      Este URL debería incluir el nombre del servidor web o de máquina, o su dirección IP.<br><br></li>
                                  <li> [Opcional] <b>Ruta al directorio de sesiones</b> si desea utilizar un directorio
                                  de sesiones personalizado para la información de Sugar con el objeto de evitar que los datos de las sesiones
                                  sean vulnerables en servidores compartidos.<br><br></li>
                                  <li> [Opcional] <b>Ruta a un directorio personalizado de trazas</b> si desea sustituir el directorio por defecto para las trazas de Sugar.<br><br></li>
                                  <li> [Opcional] <b>ID de Aplicación</b> si desea sustituir el ID autogenerado
                                  que asegura que las sesiones de una instancia de Sugar no sean utilizadas por otras instancias.<br><br></li>
                                  <li><b>Juego de Caracteres</b> más comúnmente usado en su configuración regional.<br><br></li></ul>
                                  Para información más detallada, por favor consulte la Guía de Instalación.
                                ',
    'LBL_WELCOME_PLEASE_READ_BELOW' => 'Por favor, lea la siguiente información importante antes de proceder con la instalación.  La información le ayudará a determinar si está o no preparado en estos momentos para instalar la aplicación.',




	'LBL_WELCOME_CHOOSE_LANGUAGE'		=> '<b>Seleccione su idioma</b>',
	'LBL_WELCOME_SETUP_WIZARD'			=> 'Asistente de Instalación',
	'LBL_WELCOME_TITLE_WELCOME'			=> 'Bienvenido a SugarCRM ',
	'LBL_WELCOME_TITLE'					=> 'Asistente de Instalación de SugarCRM',
	'LBL_WIZARD_TITLE'					=> 'Asistente de Instalación de Sugar: ',
	'LBL_YES'							=> 'Sí',
	'LBL_MULTI'							=> 'Sí - Multibyte',
	// OOTB Scheduler Job Names:
	'LBL_OOTB_WORKFLOW'		=> 'Procesar Tareas de Workflow',
	'LBL_OOTB_REPORTS'		=> 'Ejecutar Tareas Programadas de Generación de Informes',
	'LBL_OOTB_IE'			=> 'Comprobar Bandejas de Entrada',
	'LBL_OOTB_BOUNCE'		=> 'Ejecutar Proceso Nocturno de Correos de Campaña Rebotados',
	'LBL_OOTB_CAMPAIGN'		=> 'Ejecutar Proceso Nocturno de Campañas de Correo Masivo',
	'LBL_OOTB_PRUNE'		=> 'Truncar Base de datos al Inicio del Mes',
	'LBL_OOTB_TRACKER'		=> 'Limpiar Tablas de Monitorización',
    'LBL_UPDATE_TRACKER_SESSIONS' => 'Actualizar tabla tracker_sessions',
    'LBL_PATCHES_TITLE'     => 'Instalar Últimos Parches',
    'LBL_MODULE_TITLE'      => 'Descargar e Instalar Paquetes de Idioma',
    'LBL_PATCH_1'           => 'Si desea saltar este paso, haga clic en Siguiente.',
    'LBL_PATCH_TITLE'       => 'Parche del Sistema',
    'LBL_PATCH_READY'       => 'Los siguientes parches están listos para ser instalados:',
	'LBL_SESSION_ERR_DESCRIPTION'		=> "SugarCRM depende de las sesiones de PHP para almacenar información importante mientras que está conectado a su servidor web.  Su instalación de PHP no tiene la información de Sesión correctamente configurada.  
											<br><br>Un error de configuración bastante común es que la directiva <b>'session.save_path'</b> no apunte a un directorio válido.  <br>
											<br> Por favor, corrija su <a target=_new href='http://us2.php.net/manual/en/ref.session.php'>configuración PHP</a> en el archivo php.ini localizado donde se indica a continuación.",
	'LBL_SESSION_ERR_TITLE'				=> 'Error de Configuración de Sesiones PHP',
	'LBL_SYSTEM_NAME'=>'Nombre del Sistema',
	'LBL_REQUIRED_SYSTEM_NAME'=>'Introduzca un Nombre de Sistema para la instancia de Sugar.',
	'LBL_PATCH_UPLOAD' => 'Seleccione un archivo con un parche de su equipo local',
	'LBL_INCOMPATIBLE_PHP_VERSION' => 'Se requiere la versión de PHP 5 o superior.',
	'LBL_MINIMUM_PHP_VERSION' => 'La versión mínima requerida de PHP es la 5.1.0. Se recomienda usar la versión de PHP 5.2.x.',
	'LBL_YOUR_PHP_VERSION' => '(Su versión actual de PHP es ',
	'LBL_RECOMMENDED_PHP_VERSION' =>' La versión recomendada de PHP es la 5.2.x)',
	'LBL_BACKWARD_COMPATIBILITY_ON' => 'El modo de compatibilidad hacia atrás de PHP está habilitado. Establezca zend.ze1_compatibility_mode a Off antes de continuar',
);

?>
