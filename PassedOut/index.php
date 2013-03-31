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
  </head>
  <body>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="brand" href="#">PassedOut</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li>
              	<a href="#" id="welcome">Welcome, <?php echo htmlspecialchars($login->getUserName(), ENT_COMPAT, 'UTF-8');?></a>
              </li>
              <li class="divider-vertical"></li>
              <li>
              	<a id="place-marker" href="#">Place Marker</a>
              </li>
              <li class="divider-vertical"></li>
              <li class="dropdown">
                <div id="search-container" class="navbar-search" data-toggle="dropdown">
                    <input id="search-input" type="text" class="search-query" placeholder="Search">
                    <i class="icon-search"></i>
                </div>
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
    <div id="map-canvas"></div>
    <div id="marker-details" tabindex="-1" class="popover fade in">
            <h3 class="popover-title">Edit Marker Details</h3>
            <div class="popover-content">
            	<span>Title:</span>
            	<input id="details-title" type="text" />
            	<span>Description:</span>
            	<textarea id="details-description" rows="3"></textarea>
            	<button id="details-delete" class="btn btn-danger">Delete</button>
            	<button id="details-save" class="btn pull-right">Save</button>
            	<button id="details-cancel" class="btn pull-right">Cancel</button>

            </div>
        </div>
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
    <script src="./js/passedout-marker.js"></script>
    <script src="./js/passedout.js"></script>
  </body>
</html>