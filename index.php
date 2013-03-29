<?php
require_once("./config/db.php");
require_once('./classes/Database.class.php');
require_once('./classes/Login.class.php');

$db = new Database();
$login = new Login($db);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>PassedOut</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Mark the places where you visited">
    <meta name="author" content="AlexApps">
    <link href="./css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/site.css" rel="stylesheet">
   	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=true"></script>
    <script type="text/javascript">
      function initialize() {
        var mapOptions = {
          center: new google.maps.LatLng(0, 0),
          zoom: 2,
          minZoom: 2,
          mapTypeId: google.maps.MapTypeId.SATELLITE
        };
        var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
      }
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
  </head>
  <body>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="#">PassedOut</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li>
              	<a href="#" onclick="return false;">Welcome, <?php echo htmlspecialchars($login->getUserName(), ENT_COMPAT, 'UTF-8');?></a>
              </li>
              <li class="divider-vertical"></li>
              <li>
              	<a href="#">Place Marker</a>
              </li>
            </ul>
            <ul class="nav pull-right">
                <?php
                if ($login->isUserLoggedIn()) { ?>
					<li><a href="./?sign-out">Sign out</a></li>
    			<?php } else { ?>
    			<li class="dropdown">
					<a class="dropdown-toggle" href="#" data-toggle="dropdown">Sign Up <strong class="caret"></strong></a>
					<div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">
                    <form method="post" action="./" accept-charset="UTF-8">
                        <input type="text" placeholder="Username" id="username" name="username">
                        <input type="password" placeholder="Password" id="password" name="password">
                        <input type="password" placeholder="Repeat Password" id="repeat-password" name="repeat-password">
                        <input class="btn btn-success btn-block" type="submit" id="sign-up" name="sign-up" value="Sign Up">
                    </form>
					</div>
				</li>
				<li class="divider-vertical"></li>
    			<li class="dropdown">
					<a class="dropdown-toggle" href="#" data-toggle="dropdown">Sign In <strong class="caret"></strong></a>
					<div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">
                    <form method="post" action="./" accept-charset="UTF-8">
                        <input type="text" placeholder="Username" id="username" name="username">
                        <input type="password" placeholder="Password" id="password" name="password">
                        <input class="btn btn-success btn-block" type="submit" id="sign-in" name="sign-in" value="Sign In">
                    </form>
					</div>
				</li>
    			<?php } ?>
			</ul>
          </div>
        </div>
      </div>
    </div>
    <div id="map-canvas"/>
    <?php

    if ($login->errors) {
        foreach ($login->errors as $error) {

    ?>
    <div class="login_message error">
        <?php echo $error; ?>
    </div>
    <?php

        }
    }

    if ($login->messages) {
        foreach ($login->messages as $message) {
    ?>
    <div class="login_message success">
        <?php echo $message; ?>
    </div>
    <?php

        }
    } ?>
    <script src="./js/jquery-1.9.1.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
  </body>
</html>

<!--
passedout
hkJHue3a9nj2jijJ#
-->