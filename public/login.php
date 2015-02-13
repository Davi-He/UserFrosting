<?php
/*

UserFrosting Version: 0.2.3
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

require_once("../userfrosting/config-userfrosting.php");

// Public page

setReferralPage(getAbsoluteDocumentPath(__FILE__));

//Forward the user to their default page if he/she is already logged in
if(isUserLoggedIn()) {
	addAlert("warning", "You're already logged in!");
	header("Location: account");
	exit();
}

use \Bootsole as BS;

// Load page schema
$pageSchema = BS\PageSchema::load("default", BS\PATH_SCHEMA . "pages/pages.json");

$loader = new Twig_Loader_Filesystem(BS\PATH_TEMPLATES);
$twig = new Twig_Environment($loader);

echo $twig->render("pages/public/login.html", [
    "author" => "Alex Weissman",
    "site_title" => SITE_TITLE,
    "title" => SITE_TITLE,    
    "page_title" => "Login",
    "description" => "Login to your UserFrosting account.",
    "favicon_path" => BS\URI_PUBLIC_ROOT . "css/favicon.ico",
    "css_includes" => $pageSchema['css'],
    "uri_css_root" => BS\URI_CSS_ROOT,
    "js_includes" => $pageSchema['js'],
    "uri_js_root" => BS\URI_JS_ROOT,
    "uri_public_root" => BS\URI_PUBLIC_ROOT,
    "active_page" => "login.php",
    "email_login" => $email_login,
    "can_register" => $can_register
]);

?>
