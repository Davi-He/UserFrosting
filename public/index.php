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

use \UserFrosting as UF;

// Public page
setReferralPage(UF\getAbsoluteDocumentPath(__FILE__));

//Forward the user to their default page if he/she is already logged in
if(isUserLoggedIn()) {
	addAlert("warning", "You're already logged in!");
    header("Location: account");
	exit();
}

// Load page schema
$pageSchema = UF\PageSchema::load("default", UF\PATH_SCHEMA . "pages/pages.json");

// Twig templating object
$loader = new Twig_Loader_Filesystem(UF\PATH_TEMPLATES);
$twig = new Twig_Environment($loader);
// Global Twig parameters
$twig->addGlobal("favicon_path", UF\URI_PUBLIC_ROOT . "css/favicon.ico");
$twig->addGlobal("css_includes", $pageSchema['css']);
$twig->addGlobal("uri_css_root", UF\URI_CSS_ROOT);
$twig->addGlobal("js_includes", $pageSchema['js']);
$twig->addGlobal("uri_js_root", UF\URI_JS_ROOT);
$twig->addGlobal("uri_public_root", UF\URI_PUBLIC_ROOT);
$twig->addGlobal("uri_image_root", UF\URI_PUBLIC_ROOT . "images/");
$twig->addGlobal("site_title", UF\SITE_TITLE);

// URI router
$klein = new \Klein\Klein();

// Front page
$klein->respond('GET', UF\URI_PUBLIC_RELATIVE, function(){
    global $pageSchema, $loader, $twig, $email_login, $can_register;
    
    echo $twig->render("pages/public/home.html", [
        "author" => "Alex Weissman",
        "title" => UF\SITE_TITLE,    
        "page_title" => "A secure, modern user management system based on UserCake, jQuery, and Bootstrap.",
        "description" => "Main landing page for public access to this website.",
        "active_page" => "",
        "email_login" => $email_login,
        "can_register" => $can_register,
        "captcha_image" => generateCaptcha()
    ]);
});

$klein->respond('GET', UF\URI_PUBLIC_RELATIVE . 'account/login', function ($request, $response, $service) {
    global $pageSchema, $loader, $twig, $email_login, $can_register;

    $validators = new Fortress\ClientSideValidator(UF\PATH_SCHEMA . "forms/login.json");
    
    echo $twig->render("pages/public/login.html", [
        "author" => "Alex Weissman",
        "title" => UF\SITE_TITLE,    
        "page_title" => "Login",
        "description" => "Login to your UserFrosting account.",
        "active_page" => "account/login",
        "email_login" => $email_login,
        "can_register" => $can_register,
        "validators" => $validators->formValidationRulesJson()
    ]);
});

$klein->respond('GET', UF\URI_PUBLIC_RELATIVE . 'account/register', function ($request, $response, $service) {
    global $pageSchema, $loader, $twig, $email_login, $can_register;

    if (!userIdExists('1')){
        addAlert("danger", lang("MASTER_ACCOUNT_NOT_EXISTS"));
        header("Location: install/wizard_root_user.php");
        exit();
    }
    
    $validators = new Fortress\ClientSideValidator(UF\PATH_SCHEMA . "forms/register.json");
    
    // If registration is disabled, send them back to the home page with an error message
    if (!$can_register){
        addAlert("danger", lang("ACCOUNT_REGISTRATION_DISABLED"));
        header("Location: account/login");
        exit();
    }

    echo $twig->render("pages/public/register.html", [
        "author" => "Alex Weissman",
        "title" => UF\SITE_TITLE,    
        "page_title" => "Register",
        "description" => "Register for a new UserFrosting account.",
        "active_page" => "account/register",
        "email_login" => $email_login,
        "can_register" => $can_register,
        "captcha_image" => generateCaptcha(),
        "validators" => $validators->formValidationRulesJson()
    ]);
});

$klein->respond('GET', UF\URI_PUBLIC_RELATIVE . 'account/forgot-password', function ($request, $response, $service) {
    global $pageSchema, $loader, $twig, $email_login, $can_register;
    $params = $request->paramsGet()->all(["token"]);
    if(empty($params["token"]))
        $params["token"] = null;
    
    $validators = new Fortress\ClientSideValidator(UF\PATH_SCHEMA . "forms/forgot-password.json");
    
    echo $twig->render("pages/public/forgot-password.html", [
        "author" => "Alex Weissman",
        "title" => UF\SITE_TITLE,    
        "page_title" => "Reset Password",
        "description" => "Reset your UserFrosting password.",
        "active_page" => "",
        "email_login" => $email_login,
        "can_register" => $can_register,
        "token" => $params["token"],
        "confirm_ajax" => $params["token"] ? 1 : 0,
        "validators" => $validators->formValidationRulesJson()
    ]);

});

$klein->respond('GET', UF\URI_PUBLIC_RELATIVE . 'account/resend-activation', function ($request, $response, $service) {
    global $pageSchema, $loader, $twig, $email_login, $can_register;

    $validators = new Fortress\ClientSideValidator(UF\PATH_SCHEMA . "forms/resend-activation.json");
     
    echo $twig->render("pages/public/resend-activation.html", [
        "author" => "Alex Weissman",
        "title" => UF\SITE_TITLE,    
        "page_title" => "Resend Activation",
        "description" => "Resend the activation email for your new UserFrosting account.",
        "active_page" => "",
        "email_login" => $email_login,
        "can_register" => $can_register,
        "validators" => $validators->formValidationRulesJson()
    ]);

});

// See https://github.com/chriso/klein.php/wiki/Handling-404%27s
$klein->onHttpError(function ($code, $router) {
    global $pageSchema, $loader, $twig;
    switch ($code) {
        case 404:
            $router->response()->body(
                $twig->render("pages/public/404.html", [
                    "author" => "Alex Weissman",
                    "title" => UF\SITE_TITLE,    
                    "page_title" => "404 Error",
                    "description" => "We couldn't deliver.  We're sorry."
                ])
            );
            break;
        case 405:
            $router->response()->body(
                'You can\'t do that!'
            );
            break;
        default:
            $router->response()->body(
                'Oh no, a bad error happened that caused a '. $code
            );
    }
});

// Route the request!
$klein->dispatch();

?>
