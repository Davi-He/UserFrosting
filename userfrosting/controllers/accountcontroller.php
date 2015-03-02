<?php

use \UserFrosting as UF;

// Handles account controller options
class AccountController extends BaseController {

    public function pageHome(){
        // Get the message stream
        $ms = \Fortress\HTTPRequestFortress::$message_stream; 
        
        //Forward the user to their default page if he/she is already logged in
        if(isUserLoggedIn()) {
            $ms->addMessageTranslated("danger", "LOGIN_ALREADY_COMPLETE");
            //header("Location: " . URI_PUBLIC_ROOT);
            exit();
        }
        
        return $this->_twig->render("pages/public/home.html", [
            "author" => "Alex Weissman",
            "title" => UF\SITE_TITLE,    
            "page_title" => "A secure, modern user management system based on UserCake, jQuery, and Bootstrap.",
            "description" => "Main landing page for public access to this website.",
            "active_page" => "",
            "captcha_image" => generateCaptcha()
        ]);
    }
    
    public function pageLogin(){
        $validators = new Fortress\ClientSideValidator(UF\PATH_SCHEMA . "forms/login.json");
        
        return $this->_twig->render("pages/public/login.html", [
            "author" => "Alex Weissman",
            "title" => UF\SITE_TITLE,    
            "page_title" => "Login",
            "description" => "Login to your UserFrosting account.",
            "active_page" => "account/login",
            "validators" => $validators->formValidationRulesJson()
        ]);
    }

    public function pageRegister($can_register = false){
        if (!userIdExists('1')){
            addAlert("danger", lang("MASTER_ACCOUNT_NOT_EXISTS"));
            header("Location: install/wizard_root_user.php");
            exit();
        }
        
        $validators = new Fortress\ClientSideValidator(UF\PATH_SCHEMA . "forms/register.json");
        
        // If registration is disabled, send them back to the home page with an error message
        if (!$can_register){
            addAlert("danger", lang("ACCOUNT_REGISTRATION_DISABLED"));
            header("Location: " . UF\URI_PUBLIC_ROOT . "account/login");
            exit();
        }
    
        return $this->_twig->render("pages/public/register.html", [
            "author" => "Alex Weissman",
            "title" => UF\SITE_TITLE,    
            "page_title" => "Register",
            "description" => "Register for a new UserFrosting account.",
            "active_page" => "account/register",
            "captcha_image" => generateCaptcha(),
            "validators" => $validators->formValidationRulesJson()
        ]);
    }

    public function pageForgotPassword($token = null){
      
        $validators = new Fortress\ClientSideValidator(UF\PATH_SCHEMA . "forms/forgot-password.json");
        
        return $this->_twig->render("pages/public/forgot-password.html", [
            "author" => "Alex Weissman",
            "title" => UF\SITE_TITLE,    
            "page_title" => "Reset Password",
            "description" => "Reset your UserFrosting password.",
            "active_page" => "",
            "token" => $token,
            "confirm_ajax" => $token ? 1 : 0,
            "validators" => $validators->formValidationRulesJson()
        ]);
    }
    
    public function pageResendActivation(){
        $validators = new Fortress\ClientSideValidator(UF\PATH_SCHEMA . "forms/resend-activation.json");
         
        return $this->_twig->render("pages/public/resend-activation.html", [
            "author" => "Alex Weissman",
            "title" => UF\SITE_TITLE,    
            "page_title" => "Resend Activation",
            "description" => "Resend the activation email for your new UserFrosting account.",
            "active_page" => "",
            "validators" => $validators->formValidationRulesJson()
        ]);
    }
    
    public function login(){
        // Load the request schema
        $requestSchema = new Fortress\RequestSchema(UF\PATH_SCHEMA . "forms/login.json");
        
        // Get the message stream
        $ms = \Fortress\HTTPRequestFortress::$message_stream; 
               
        // Expect a POST request
        $rf = new Fortress\HTTPRequestFortress("post", $requestSchema, UF\URI_PUBLIC_ROOT);
        
        //Forward the user to their default page if he/she is already logged in
        if(isUserLoggedIn()) {
            $ms->addMessageTranslated("danger", "LOGIN_ALREADY_COMPLETE");
            $rf->raiseFatalError();
        }
        
        // Remove ajaxMode and csrf_token from the request data
        $rf->removeFields(['ajaxMode', 'csrf_token']);
        
        // Sanitize data
        $rf->sanitize();

        // Validate, and halt on validation errors.
        $rf->validate();
        
        // Create a new group with the filtered data
        $data = $rf->data();
        
        // Determine whether we are trying to log in with an email address or a username
        $isEmail = filter_var($data['user_name'], FILTER_VALIDATE_EMAIL);
        
        // If it's an email address, but email login is not enabled, raise an error.
        if ($isEmail && !$email_login){
            $ms->addMessageTranslated("danger", "ACCOUNT_USER_OR_PASS_INVALID");
            $rf->raiseFatalError();
        }
        
        // Try to load the user data
        if($isEmail){
            if (emailExists($data['user_name'])){
                $userdetails = fetchUserAuthByEmail($data['user_name']);
            } else {
                $ms->addMessageTranslated("danger", "ACCOUNT_USER_OR_PASS_INVALID");
                $rf->raiseFatalError();            
            }
            
        } else {
            if (usernameExists($data['user_name'])){
                $userdetails = fetchUserAuthByUserName($data['user_name']);
            } else {
                $ms->addMessageTranslated("danger", "ACCOUNT_USER_OR_PASS_INVALID");
                $rf->raiseFatalError();
            }
        }
        
        // Check that the user's account is activated
        if ($userdetails["active"] == 0) {
            $ms->addMessageTranslated("danger", "ACCOUNT_INACTIVE");
            $rf->raiseFatalError();
        }
        
        // Check that the user's account is enabled
        if ($userdetails["enabled"] == 0){
            $ms->addMessageTranslated("danger", "ACCOUNT_DISABLED");
            $rf->raiseFatalError();
        }
        
        
        // Validate the password
        if(!passwordVerifyUF($data['password'], $userdetails["password"]))  {
            //Again, we know the password is at fault here, but lets not give away the combination incase of someone bruteforcing
            $ms->addMessageTranslated("danger", "ACCOUNT_USER_OR_PASS_INVALID");
            $rf->raiseFatalError();
        }
        
        //Passwords match! we're good to go'
        
        //Construct a new logged in user object
        //Transfer some db data to the session object
        $loggedInUser = new loggedInUser();
        $loggedInUser->email = $userdetails["email"];
        $loggedInUser->user_id = $userdetails["id"];
        $loggedInUser->hash_pw = $userdetails["password"];
        $loggedInUser->title = $userdetails["title"];
        $loggedInUser->displayname = $userdetails["display_name"];
        $loggedInUser->username = $userdetails["user_name"];
        $loggedInUser->alerts = array();
        
        //Update last sign in
        $loggedInUser->updateLastSignIn();
        
        // Update password if we had encountered an outdated hash
        if (getPasswordHashTypeUF($userdetails["password"]) != "modern"){
            // Hash the user's password and update
            $password_hash = passwordHashUF($data['password']);
            if ($password_hash === null){
                error_log("Notice: outdated password hash could not be updated because the new hashing algorithm is not supported.  Are you running PHP >= 5.3.7?");
            } else {
                $loggedInUser->hash_pw = $password_hash;
                updateUserField($loggedInUser->user_id, 'password', $password_hash);
                error_log("Notice: outdated password hash has been automatically updated to modern hashing.");
            }
        }
        
        // Create the user's CSRF token
        $loggedInUser->csrf_token(true);
        
        $_SESSION["userCakeUser"] = $loggedInUser;
        
        $ms->addMessage("success", "Welcome back, " . $loggedInUser->displayname);

        restore_error_handler();
        
        $rf->raiseSuccess();

    }
    
    public function logout(){
        session_destroy();
    }
}

?>
