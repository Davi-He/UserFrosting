<?php

use \Bootsole as BS;

// Builds the public navbar for the front page
function templateNavbarPublic($activeItem = "home"){
    global $can_register;

    $nb = new BS\NavbarBuilder([
        "brand_label" => "UserFrosting",
        "brand_url" => BS\URI_PUBLIC_ROOT,
        "@components" => [
            "nav" => new BS\NavBuilder([
                "@type" => "nav",
                "@items" => [ 
                    "home" => [
                        "@label" => "Home",
                        "@url" => BS\URI_PUBLIC_ROOT.  "index.php"
                    ],
                    "login" => [
                        "@label" => "Login",
                        "@url" => BS\URI_PUBLIC_ROOT.  "login.php"
                    ]
                ],
                "@css_classes" => ["nav-pills pull-right"]
            ])
        ]
    ], "pages/navs/main-nav-jumbotron.html");     
    
    // Add registration button, if permitted
    if ($can_register){
        $nb->getComponent("nav")->addItem("register", [
                "@label" => "Register",
                "@url" => BS\URI_PUBLIC_ROOT.  "register.php"
            ]);
    }

    // Set the appropriate active element
    $nb->getComponent("nav")->setActiveItem($activeItem);    
    return $nb;
}

?>