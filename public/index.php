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
require_once("../userfrosting/controllers/controller.php");
require_once("../userfrosting/controllers/accountcontroller.php");

use \UserFrosting as UF;

// Public page
setReferralPage(UF\getAbsoluteDocumentPath(__FILE__));

// URI router
$klein = new \Klein\Klein();

// Front page
$klein->respond('GET', UF\URI_PUBLIC_RELATIVE, function(){
    $controller = new AccountController();
    return $controller->pageHome();
});

// Account-related actions
$klein->respond('GET', UF\URI_PUBLIC_RELATIVE . 'account/[:action]', function ($request, $response, $service) {
    global $can_register;
    
    $controller = new AccountController();
    
    switch ($request->action) {
        case "login":               return $controller->pageLogin();
        case "logout":              return $controller->logout();        
        case "register":            return $controller->pageRegister($can_register);
        case "resend-activation":   return $controller->pageResendActivation();
        case "forgot-password":     return $controller->pageForgotPassword($request->token);    
        default:                    return $controller->page404();   
    }
});

$klein->respond('POST', UF\URI_PUBLIC_RELATIVE . 'account/[:action]', function ($request, $response, $service) {
    $controller = new AccountController();
    
    switch ($request->action) {
        case "login":               return $controller->login($request->params());
        case "register":            return $controller->register($request->params());
        case "resend-activation":   return $controller->resendActivation($request->params());
        case "forgot-password":     return $controller->forgotPassword($request->params());    
        default:                    return $controller->page404();   
    }
});

// PHP Info (only visible as master user)
$klein->respond('GET', UF\URI_PUBLIC_RELATIVE . 'phpinfo', function ($request, $response, $service) {
    echo phpinfo();
});

// Alert stream
$klein->respond('GET', UF\URI_PUBLIC_RELATIVE . 'alerts', function ($request, $response, $service) {
    $controller = new BaseController();
    return $controller->getAlerts();
});

// See https://github.com/chriso/klein.php/wiki/Handling-404%27s
$klein->onHttpError(function ($code, $router) {

    switch ($code) {
        case 404:
            $controller = new BaseController();
            return $controller->page404();
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
