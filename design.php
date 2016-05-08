<?php
include_once("config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>CS:GO Bug Tracker</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="A website designed to keep track of all the known bugs of CS:GO maps">
<meta name="keywords" content="csgo,bug,tracker">
<meta name="author" content="CS:GO Bug Tracker">
<script src="js/jquery-1.11.2.min.js"></script>
<link href="css/bootstrap.min.css?v=1" rel="stylesheet">
<link href="css/main_top.css?v=1" rel="stylesheet">
<link href="css/main.css?v=1" rel="stylesheet">
<link rel="shortcut icon" type="image/ico" href="favicon.ico" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<script src="js/bootstrap.min.js"></script>
</head>
<body>
<?php
$rank = ($logged?$user_data["rank"]:0);
$user_steam_avatar = ($logged?$user_data['steam_avatar']:"0");
$user_steam_id = ($logged?$user_data['steam_id']:"0");
$user_steam_persona = ($logged?mysqli_real_escape_string($connection,$user_data['steam_persona']):"0");
echo "
<script type='text/javascript'>
	user_steam_avatar = '$user_steam_avatar';
	user_steam_id = '$user_steam_id';
	user_steam_persona = '$user_steam_persona';
	rank = '$rank';
</script>
";

?>
<div id="custom-bootstrap-menu" style="z-index:50" class="navbar navbar-default navbar-fixed-top " role="navigation">
    <div class="container-fluid">
<div class="navbar-header">
      <a class="navbar-brand" href="index.php">
        <img alt="Blackjack" src="images/logo.png" width="108" height="40">
      </a>
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="sr-only">Toggle navigation</span><span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
            </button>
        </div>
        <div class="navbar-collapse collapse navbar-menubuilder">
            <ul class="nav navbar-nav navbar-left">
				<li id="index_menu"><a href="index.php">Map bugs</a>
                </li>
				<li>
				<a href='https://www.reddit.com/r/CSGOBugTracker/' title='Reddit Page' target='_blank'><img src='images/ico_reddit.png' alt='Reddit' width='16' height='16'></a>
				</li>
				<li>
				<a href='https://github.com/geri43/CSGOBugTracker' title='Github Page' target='_blank'><img src='images/ico_github.png' alt='Github' width='16' height='16'></a>
				</li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li>
<?php
if ($logged) {
logoutbutton();
} else {
steamlogin();
}
?>
</li>
            </ul>
        </div>
    </div>
</div>
<script type="text/javascript">
$("#<?php echo basename(ltrim($_SERVER['PHP_SELF'],'/'),'.php');?>_menu").addClass("active");
</script>