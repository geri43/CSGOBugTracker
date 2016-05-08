<?php
//error_reporting(0);
date_default_timezone_set('Europe/Sarajevo');
require 'steamauth/steamauth.php';
$mysql["server"] = "localhost";
$mysql["user"] = "root"; 
$mysql["password"] = "";
$mysql["database"] = "bugtracker";
$connection = mysqli_connect($mysql["server"], $mysql["user"], $mysql["password"], $mysql["database"]);
$time = time();
$site_title = "CS:GO Bug Tracker";
$rank_array = ["User","Moderator","Admin"];
$militime = round(microtime(true) * 1000);
$profile = -1;
if(!$connection) {
		die("<style type='text/css'>body { background-color:white; }</style><center><h1>$site_title</h1><br><br>Our servers are busy right now. Please check back later.</center>");
}
mysqli_query($connection,"SET NAMES 'utf8'");
$logged = false;
$session_id = session_id();
if(isset($_SESSION['steamid']))
{
	$result = mysqli_query($connection,"SELECT * FROM users WHERE steam_id='$_SESSION[steamid]' LIMIT 1");
	if (!$result || mysqli_num_rows($result)==0) {
		mysqli_query($connection,"INSERT INTO users (steam_id) VALUES ('$_SESSION[steamid]')");
		$logged = true;
		$result = mysqli_query($connection,"SELECT * FROM users WHERE steam_id='$_SESSION[steamid]' LIMIT 1");
		$user_data = mysqli_fetch_assoc($result);
		$profile = $user_data['user_id'];
	}
	else {
		$logged=true;
		$user_data = mysqli_fetch_assoc($result);
		$profile = $user_data['user_id'];
		mysqli_query($connection,"UPDATE users SET last_action='$time' WHERE user_id='$profile'");
		include ('steamauth/userInfo.php');
	}
}
function logoutbutton() {
	global $user_data,$rank_array;
    echo "<form action=\"steamauth/logout.php\" method=\"post\">Logged in as: ".$user_data["steam_persona"]." (".$rank_array[$user_data["rank"]].") <button type='submit' value='Logout' class='btn btn-default mrg' />Logout</button></form>"; //logout button
}
?>