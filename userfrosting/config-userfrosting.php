<?php
/*

UserFrosting Version: 0.3.0
By Alex Weissman
Copyright (c) 2015

Based on the UserCake user management system, v2.0.2.
Copyright (c) 2009-2012

UserFrosting, like UserCake, is 100% free and open-source.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the 'Software'), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/

namespace UserFrosting;

// Time zone
date_default_timezone_set("America/New_York");

// Used to force backend scripts to log errors rather than print them as output
function logAllErrors($errno, $errstr, $errfile, $errline, array $errcontext) {
	ini_set("log_errors", 1);
	ini_set("display_errors", 0);
	
    error_log("Error ($errno): $errstr in $errfile on line $errline");
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

// Set true for dev server, false for production server.  Allows for quickly switching back and forth between development and production modes.
defined("UserFrosting\SERVER_DEV")
	or define("UserFrosting\SERVER_DEV", true);

// Set true for running unminified/merged CSS, false to run minified CSS.  Don't forget to reminify your CSS!
defined("UserFrosting\CSS_DEV")
	or define("UserFrosting\CSS_DEV", true);

// Set true for running unminified/merged JS, false to run minified JS.  Don't forget to reminify your JS!
defined("UserFrosting\JS_DEV")
	or define("UserFrosting\JS_DEV", true);
  
// Determine if this is SSL or unsecured connection
if (!defined("UserFrosting\SCHEME_PREFIX")){
    // Determine if connection is http or https
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
        // SSL connection
        define("UserFrosting\SCHEME_PREFIX", "https://");
    } else {
        define("UserFrosting\SCHEME_PREFIX", "http://");
    }
}

if (SERVER_DEV) {
    /*********** Dev configuration **********/
    $config = [
        "db" => [
            "dbname"   => "userfrosting", //Name of Database
            "username" => "root", //Name of database user
            "password" => "password", //Password for database user
            "host" => "localhost"
        ]
    ];
    
    /********* Override these in config-bootsole.php *********/

    /* The public URI corresponding to the document root of your website **relative to the TLD**. */
    defined("UserFrosting\URI_PUBLIC_RELATIVE")
        or define("UserFrosting\URI_PUBLIC_RELATIVE", "/userfrosting/public/");    
        
    /* The public URI corresponding to the document root of your website. */
    defined("UserFrosting\URI_PUBLIC_ROOT")
        or define("UserFrosting\URI_PUBLIC_ROOT", SCHEME_PREFIX . "localhost/userfrosting/public/");
    
    /* The root directory of your public web assets (e.g. 'public', 'public_html', etc) */
    defined("UserFrosting\PATH_PUBLIC_ROOT")
        or define ("UserFrosting\PATH_PUBLIC_ROOT", realpath(dirname(__FILE__) . "/../public") . "/");
            
} else {
    /*********** Production configuration **********/
    $config = [
        "db" => [
            "dbname"   => "userfrosting", //Name of Database
            "username" => "root", //Name of database user
            "password" => "password", //Password for database user
            "host"     => "localhost"
        ]
    ];
    
    /********* Override these in config-bootsole.php *********/

    /* The public URI corresponding to the document root of your website **relative to the TLD**. */
    defined("UserFrosting\URI_PUBLIC_RELATIVE")
        or define("UserFrosting\URI_PUBLIC_RELATIVE", "/");
        
    /* The public URI corresponding to the document root of your website. */
    defined("UserFrosting\URI_PUBLIC_ROOT")
        or define("UserFrosting\URI_PUBLIC_ROOT", SCHEME_PREFIX ."userfrosting.com/");
    
    /* The root directory of your public web assets (e.g. 'public', 'public_html', etc) */
    defined("UserFrosting\PATH_PUBLIC_ROOT")
        or define ("UserFrosting\PATH_PUBLIC_ROOT", realpath(dirname(__FILE__) . "/../public") . "/");
            
}

/* The public URI corresponding to the Javascript assets of your website. */
defined("UserFrosting\URI_JS_ROOT")
    or define("UserFrosting\URI_JS_ROOT", URI_PUBLIC_ROOT . "js/");    

/* The public URI corresponding to the CSS assets of your website. */
defined("UserFrosting\URI_CSS_ROOT")
    or define("UserFrosting\URI_CSS_ROOT", URI_PUBLIC_ROOT . "css/");
    

/* Establish DB connection */
$db_table_prefix = "uf_";

$PDO_DB = new \PDO("mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8", $config['db']['username'], $config['db']['password']);
$PDO_DB->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
$PDO_DB->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

global $PDO_DB;

GLOBAL $errors;
GLOBAL $successes;

$errors = array();
$successes = array();

/***** File (local) paths ******/

/* The root directory of your Javascript assets */    
defined("UserFrosting\PATH_JS_ROOT")
	or define ("UserFrosting\PATH_JS_ROOT", PATH_PUBLIC_ROOT . "js/");  

/* The root directory of your CSS assets */
defined("UserFrosting\PATH_CSS_ROOT")
	or define ("UserFrosting\PATH_CSS_ROOT", PATH_PUBLIC_ROOT . "css/");
    
/* The root directory in which the UserFrosting resources reside.  Should usually be the same directory that this config file resides in.*/
defined("Userfrosting\PATH_UF_ROOT")
    or define("Userfrosting\PATH_UF_ROOT", realpath(dirname(__FILE__)) . "/");

defined("UserFrosting\PATH_VENDOR_ROOT")
    or define ("UserFrosting\PATH_VENDOR_ROOT", realpath(dirname(__FILE__) . "/../vendor") . "/");
    
/* The root directory in which the UserFrosting templates reside. */
defined("UserFrosting\PATH_TEMPLATES")
    or define("UserFrosting\PATH_TEMPLATES", PATH_UF_ROOT . "templates/");

/* The root directory in which the UserFrosting schema reside. */
defined("UserFrosting\PATH_SCHEMA")
    or define("UserFrosting\PATH_SCHEMA", PATH_UF_ROOT . "schema/");

/* The default page schema (for determining CSS/JS includes in PageHeaderBuilder and PageFooterBuilder). */
defined("UserFrosting\FILE_SCHEMA_PAGE_DEFAULT")
    or define("UserFrosting\FILE_SCHEMA_PAGE_DEFAULT", PATH_SCHEMA . "pages/pages.json");

/* Other constants */

// Set to true if you want authorization failures to be logged to the PHP error log.
defined("UserFrosting\LOG_AUTH_FAILURES")
	or define("UserFrosting\LOG_AUTH_FAILURES", false);

defined("UserFrosting\SESSION_NAME")
    or define("UserFrosting\SESSION_NAME", "UserFrosting");
   
defined("MAIL_TEMPLATES")
	or define("MAIL_TEMPLATES", PATH_UF_ROOT . "/mail-templates/");

require_once("funcs.php");
require_once("error_functions.php");
require_once("template_functions.php");
require_once("password.php");
require_once("db_functions.php");
require_once("pageschema.php");
require_once(PATH_VENDOR_ROOT . "autoload.php");

// Set validation parameters

\Valitron\Validator::langDir(PATH_UF_ROOT . "/locale/valitron/"); // always set langDir before lang.
\Valitron\Validator::lang('en');

//Retrieve basic configuration settings

$settings = fetchConfigParameters();

//Grab plugin settings, used in plugin like so:
//$pvalue = $plugin_settings['variable_name']['config_value'];
/*
 $pvalue = $plugin_settings['$pmsystem']['value'];
 if ($pvalue != 1){
    // Forward to index page
    addAlert("danger", "Whoops, looks like the private message system is not enabled");
    header("Location: ".SITE_ROOT."account/index.php");
    exit();
 }
 */
$plugin_settings = fetchConfigParametersPlugins();

//Set Settings
$emailDate = date('dmy');
$emailActivation = $settings['activation'];
$can_register = $settings['can_register'];
$websiteName = $settings['website_name'];
$websiteUrl = $settings['website_url'];
$emailAddress = $settings['email'];
$resend_activation_threshold = $settings['resend_activation_threshold'];
$language = $settings['language'];
$new_user_title = $settings['new_user_title'];
$email_login = $settings['email_login'];
$token_timeout = $settings['token_timeout'];
$version = $settings['version'];

// Check for upgrade, do this hear for access to $version
checkUpgrade($version, SERVER_DEV);

// Define paths here
defined("SITE_ROOT")
    or define("SITE_ROOT", SCHEME_PREFIX.$websiteUrl);

defined("ACCOUNT_ROOT")
    or define("ACCOUNT_ROOT", SITE_ROOT . "account/");

defined("UserFrosting\SITE_TITLE")
    or define("UserFrosting\SITE_TITLE", $websiteName);
    		
// Include paths for files containing secure functions
$files_secure_functions = array(
    dirname(__FILE__) . "/secure_functions.php"
);

// Include paths for pages to add to site page management
$page_include_paths = fetchFileList();
	
// This is the user id of the master (root) account.
// The root user cannot be deleted, and automatically has permissions to everything regardless of group membership.
$master_account = 1;

$default_hooks = array("#WEBSITENAME#","#WEBSITEURL#","#DATE#");
$default_replace = array($websiteName,SITE_ROOT,$emailDate);

function getAbsoluteDocumentPath($localPath){
	return SITE_ROOT . getRelativeDocumentPath($localPath);
}

// Return the document path of a file, relative to the root directory of the site.  Takes the absolute local path of the file (such as defined by __FILE__)
function getRelativeDocumentPath($localPath){
	// Replace backslashes in local path (if we're in a windows environment)
	$localPath = str_replace('\\', '/', $localPath);
	
	// Get lowercase version of path
	$localPathLower = strtolower($localPath);

	// Replace backslashes in public root (if we're in a windows environment)
	$localRoot = str_replace('\\', '/', PATH_PUBLIC_ROOT);	
	
	// Get lowercase version of path
	$localRootLower = strtolower($localRoot) . "/";
	
	// Remove local root but preserve case
	$pos = strpos($localPathLower, $localRootLower);
	if ($pos !== false) {
		return substr_replace($localPath,"",$pos,strlen($localRootLower));
	} else {
		return $localRoot;
	}
}

//Pages to require
require_once("validate_form.php");
require_once("authorization.php");
require_once("secure_functions.php");
require_once("class.mail.php");
require_once("class.user.php");

//ChromePhp debugger for chrome console
// http://craig.is/writing/chrome-logger
//require_once("chrome.php");

session_name(SESSION_NAME);
session_start();

/*
// The dirname(__FILE__) . "/..." construct tells PHP to look for the include file in the same directory as this (the config) file
if (!file_exists($language)) {
	$language = dirname(__FILE__) . "/languages/en.php";
}

if(!isset($language)) $language = dirname(__FILE__) . "/languages/en.php";
*/

// Set the message stream
\Fortress\HTTPRequestFortress::setMessageStream('userAlerts');

// Set the translation path
\Fortress\MessageTranslator::setTranslationTable(PATH_UF_ROOT . "locale/en_US.php");

//Global User Object Var
//loggedInUser can be used globally if constructed
if(isset($_SESSION["userCakeUser"]) && is_object($_SESSION["userCakeUser"]))
{
	$loggedInUser = $_SESSION["userCakeUser"];
}