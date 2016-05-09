<?php
ob_start();
if(!isset($_SESSION)) 
{
	$sess_name = session_name();
	if (session_start()) {
		setcookie($sess_name, session_id(), null, '/', null, null, true);
	}
}
require ('openid.php');
function logoutbutton() {
	global $user_data,$rank_array;
    echo "<form action=\"steamauth/logout.php\" method=\"post\">Logged in as: ".$user_data["steam_persona"]." (".$rank_array[$user_data["rank"]].") <button type='submit' value='Logout' class='btn btn-default mrg' />Logout</button></form>"; //logout button
}
function steamlogin()
{
try {

	require("settings.php");
    $openid = new LightOpenID($steamauth['domainname']);
    
    $button['small'] = "small";
    $button['large_no'] = "large_noborder";
    $button['large'] = "large_border";
    $button = $button[$steamauth['buttonstyle']];
    
    if(!$openid->mode) {
        if(isset($_GET['login'])) {
            $openid->identity = 'http://steamcommunity.com/openid';
            header('Location: ' . $openid->authUrl());
        }
    echo "<form action=\"http://".$steamauth['domainname']."/?login\" method=\"post\"> <input type=\"image\" src=\"http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_".$button.".png\" alt='login'></form>";
}
     elseif($openid->mode == 'cancel') {
        echo 'User has canceled authentication!';
    } else {
		global $users_table;
        if($openid->validate()) { 
                $id = $openid->identity;
                $ptn = "/^http:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
                preg_match($ptn, $id, $matches);
              
                session_start();
                $_SESSION['steamid'] = $matches[1];
                 if (isset($steamauth['loginpage'])) {
					header('Location: '.$steamauth['loginpage']);
                 }
        } else {
                echo "User is not logged in.\n";
        }
    }
} catch(ErrorException $e) {
    echo $e->getMessage();
}
}
?>