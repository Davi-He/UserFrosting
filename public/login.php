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

global $email_login;

$header_content = [
    "author" => "Alex Weissman",
    "site_title" => SITE_TITLE,
    "page_title" => "Login",
    "description" => "Login to your UserFrosting account.",
    "favicon_path" => BS\URI_PUBLIC_ROOT . "css/favicon.ico"
];

// This loads the appropriate set of jumbotron links, depending on whether registration is enabled or disabled
$jumbotron_links = [
    "@source" => $can_register ? "pages/public/front-links-register.html" : "pages/public/front-links-noregister.html",
    "@content" => []
];

// This gets the top nav links, and sets the appropriate active link
$nb = templateNavbarPublic("login");

$login_form = new BS\FormBuilder([
    "@layout" => "horizontal",
    "@label_width" => 0,
    "@name" => "login",
    "@action" => "api/process_login.php",
    "@method" => "post",
    "@components" => [
        'username' => [
            '@type' => 'text',         
            '@label' => '',
            '@placeholder' => ($email_login == 1) ? 'Username or Email' : 'Username'
        ],
        'password' => [
            '@type' => 'password',
            '@label' => '',
            '@placeholder' => 'Password'
        ],
        'btn_login' => new BS\FormButtonBuilder([
            '@type' => 'submit',
            '@label' => 'Login',
            '@css_classes' => ["btn-success"]
        ])
    ],
    "jumbotron_links" => $jumbotron_links
], "forms/form-login.html");


$page = new BS\PageBuilder([
    "@header" => $header_content,
    "@name" => "login",             // "@name" must be unique for each page!
    "site_title" => SITE_TITLE,
    "main_nav" => $nb,
    "content" => $login_form,
    "main_title" => "Welcome to UserFrosting!",
    "welcome_msg" => "Please sign in."
], "pages/public/page-jumbotron.html");

echo $page->render();

?>

<script>
    $(document).ready(function() {            
      alertWidget('display-alerts');
      // Process form     
      $("form[name='login']").submit(function(e){
        // Prevent form from submitting twice
        e.preventDefault();
        var form = $(this);   
        // Serialize and post to the backend script in ajax mode
        var serializedData = form.serialize();
        serializedData += '&ajaxMode=true';     
        //console.log(serializedData);
        var url = APIPATH + "process_login.php";
        $.ajax({  
          type: "POST",  
          url: url,  
          data: serializedData
        }).done(function(result) {
            var resultJSON = processJSONResult(result);
            if (resultJSON['errors'] && resultJSON['errors'] > 0){
              alertWidget('display-alerts');
            } else {
              window.location.replace("account");
            }
        });
      });
      
    });
</script>
