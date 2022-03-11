<?php
/**
 * ---------------------------------------------------------------------
 * GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2015-2022 Teclib' and contributors.
 *
 * http://glpi-project.org
 *
 * based on GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2003-2014 by the INDEPNET Development Team.
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of GLPI.
 *
 * GLPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GLPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------
 */

/**
 * @since 0.85 new 
 */

include ('../inc/includes.php');


if (!isset($_SESSION["glpicookietest"]) || ($_SESSION["glpicookietest"] != 'testcookie')) {
   if (!is_writable(GLPI_SESSION_DIR)) {
      Html::redirect($CFG_GLPI['root_doc'] . "/index.php?error=2");
   } else {
      Html::redirect($CFG_GLPI['root_doc'] . "/index.php?error=1");
   }
}

$_POST = array_map('stripslashes', $_POST);

//Do login and checks
//$user_present = 1;
if (isset($_SESSION['namfield']) && isset($_POST[$_SESSION['namfield']])) {
   $login = $_POST[$_SESSION['namfield']];
} else {
   $login = '';
}
if (isset($_SESSION['pwdfield']) && isset($_POST[$_SESSION['pwdfield']])) {
   $password = Toolbox::unclean_cross_side_scripting_deep($_POST[$_SESSION['pwdfield']]);
} else {
   $password = '';
}
// Manage the selection of the auth source (local, LDAP id, MAIL id)
if (isset($_POST['auth'])) {
   $login_auth = $_POST['auth'];
} else {
   $login_auth = '';
}

$remember = isset($_SESSION['rmbfield']) && isset($_POST[$_SESSION['rmbfield']]) && $CFG_GLPI["login_remember_time"];

// Redirect management
$REDIRECT = "";
if (isset($_POST['redirect']) && (strlen($_POST['redirect']) > 0)) {
   $REDIRECT = "?redirect=" .rawurlencode($_POST['redirect']);

} else if (isset($_GET['redirect']) && strlen($_GET['redirect'])>0) {
   $REDIRECT = "?redirect=" .rawurlencode($_GET['redirect']);
}

$auth = new Auth();


// now we can continue with the process...
if ($auth->login($login, $password, (isset($_REQUEST["noAUTO"])?$_REQUEST["noAUTO"]:false), $remember, $login_auth)) {
   Auth::redirectIfAuthenticated();
} else {
   // we have done at least a good login? No, we exit.
   Html::nullHeader("Login", $CFG_GLPI["root_doc"] . '/index.php');
 //  echo '<div class="center b">' . $auth->getErr() . '<br><br>';
   // Logout whit noAUto to manage auto_login with errors
  // echo '<a href="' . $CFG_GLPI["root_doc"] . '/front/logout.php?noAUTO=1'.
   //      str_replace("?", "&", $REDIRECT).'">' .__('Log in again') . '</a></div>';
  // Html::nullFooter();
   ?>
      <!DOCTYPE html>
      <html>
      <head>
      <title>ARTCI-EROR LOGIN</title>
      <meta charset="UTF-8">
      <style>
      * {
      font-family: sans-serif;
      color: rgba(0,0,0,0.75);

      }
      body {
      margin: 0;
      display: flex;
      flex-direction: column;
      justify-content: center;
      height: 100vh;
      padding: 0px 30px;
      background-image: url("../pics/face_artci.jpg");
      background-size: cover;
      background-repeat: no-repeat;
      
      }

      .wrapper {
      max-width:960px;
      width: 960px; 
      margin: 30px auto;
      transform: scale(0.8);
      }
      .landing-page {
     
      height: 475px;
      margin: 0;
      box-shadow: 0px 0px 8px 1px #ccc;
      background: #fafafa;
      border-radius: 8px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      }
      svg {
      width: 50%;
      max-height: 225px;
      height: auto;
      margin: 0 0 15px;
      }
      h1 {
      font-size: 24px;
      margin: 0;
      }
      p {
      font-size: 16px;
      width: 35%; 
      margin: 16px auto 24px;
      text-align: center;
      }
      button {
      border-radius: 50px;
      padding: 8px 24px;
      font-size: 16px;
      cursor: pointer;
      background: #62AD9B;
      color: #fff;
      border: none;
      box-shadow: 0 4px 8px 0 #ccc;
      }

   </style>
   </head>
   <body>

   <!-- Sidebar -->
   <div class="wrapper">
      <div class="landing-page">
         <div style="text-align:center;" class="icon__download">
            <lottie-player src="../pics/unclock.json"  background="transparent"  speed="1"  style="width: 300px; height: 300px;"  loop autoplay ></lottie-player>
         </div>
         
         <h1> Identifiant ou mot de passe incorrect</h1>
         <p> Veuillez vous reconnectez s'il vous plaît.</p>
         <a href="/"><button>Se connecter</button></a>
      </div>
   </div>

   <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
   </body>
   </html>


   <?php
   exit();
}
