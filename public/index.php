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
require_once("../userfrosting/templates/template-components.php");

// Public page

setReferralPage(getAbsoluteDocumentPath(__FILE__));

//Forward the user to their default page if he/she is already logged in
if(isUserLoggedIn()) {
	addAlert("warning", "You're already logged in!");
    header("Location: account");
	exit();
}

use \Bootsole as BS;

$header_content = [
    "author" => "Alex Weissman",
    "site_title" => SITE_TITLE,
    "page_title" => "A secure, modern user management system based on UserCake, jQuery, and Bootstrap.",
    "description" => "Main landing page for public access to this website.",
    "favicon_path" => BS\URI_PUBLIC_ROOT . "css/favicon.ico"
];

// This loads the appropriate set of jumbotron links, depending on whether registration is enabled or disabled
$jumbotron_links = [
    "@source" => $can_register ? "pages/public/front-links-register.html" : "pages/public/front-links-noregister.html",
    "@content" => []
];

// This gets the top nav links, and sets the appropriate active link
$nb = templateNavbarPublic("home");

$page = new BS\PageBuilder([
    "@header" => $header_content,
    "@name" => "index",             // "@name" must be unique for each page!
    "site_title" => SITE_TITLE,
    "main_nav" => $nb,
    "content" => [
        // This is the main content of the page.  For convenience, we inline it here but you could move it to separate .html source file if you prefer.
        "@template" => '
            <div class="row">
                <div class="col-sm-12">
                  <a href="login.php" class="btn btn-success" role="button" value="Login">Login</a>
                </div>
            </div>
            <div class="jumbotron-links">
                {{jumbotron_links}}
            </div>
        ',
        "@content" => [
            "jumbotron_links" => $jumbotron_links
        ]
    ],
    "main_title" => "Welcome to UserFrosting!",
    "welcome_msg" => "A secure, modern user management system based on UserCake, jQuery, and Bootstrap."
], "pages/public/page-jumbotron.html");

echo $page->render();

?>
