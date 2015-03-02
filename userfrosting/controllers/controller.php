<?php

use \UserFrosting as UF;

class BaseController {

    protected $_twig = null;

    public function __construct(){
        global $email_login, $can_register;   
    
        // Load page schema
        $pageSchema = UF\PageSchema::load("default", UF\PATH_SCHEMA . "pages/pages.json");
        
        // Twig templating object
        $loader = new Twig_Loader_Filesystem(UF\PATH_TEMPLATES);
        $this->_twig = new Twig_Environment($loader);
        // Global Twig parameters
        $this->_twig->addGlobal("favicon_path", UF\URI_PUBLIC_ROOT . "css/favicon.ico");
        $this->_twig->addGlobal("css_includes", $pageSchema['css']);
        $this->_twig->addGlobal("uri_css_root", UF\URI_CSS_ROOT);
        $this->_twig->addGlobal("js_includes", $pageSchema['js']);
        $this->_twig->addGlobal("uri_js_root", UF\URI_JS_ROOT);
        $this->_twig->addGlobal("uri_public_root", UF\URI_PUBLIC_ROOT);
        $this->_twig->addGlobal("uri_image_root", UF\URI_PUBLIC_ROOT . "images/");
        $this->_twig->addGlobal("site_title", UF\SITE_TITLE);
        $this->_twig->addGlobal("email_login", $email_login);
        $this->_twig->addGlobal("can_register", $can_register);
    }
    
    public function page404(){
        return $this->_twig->render("pages/public/404.html", [
            "author" => "Alex Weissman",
            "title" => UF\SITE_TITLE,    
            "page_title" => "404 Error",
            "description" => "We couldn't deliver.  We're sorry."
        ]);
    }
    
    public function addAlert(){
        // Always a publicly accessible script
        $_SESSION["Fortress"]["userAlerts"]->addMessage($_POST['type'], $_POST['message']);
    }
    
    public function getAlerts(){
        if (isset($_SESSION["Fortress"]["userAlerts"])){
            echo json_encode($_SESSION["Fortress"]["userAlerts"]->messages());
            
            // Reset alerts after they have been delivered
            $_SESSION["Fortress"]["userAlerts"]->resetMessageStream();
        }
    }
 
}


?>
