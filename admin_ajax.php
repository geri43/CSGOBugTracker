<?php
include_once("config.php");
if ($logged && $user_data["rank"]>1 && $admin_panel_enabled) {
	if (isset($_POST["rank"]) && isset($_POST["user_steam_id"])) {
		$success = false;
		$message = "An error has happened.";
		$rank = intval($_POST["rank"]);
		$steam_id = mysqli_real_escape_string($connection,$_POST["user_steam_id"]);
		if ($rank>=0 && $rank<=2) {
			mysqli_query($connection,"UPDATE users SET rank='$rank' WHERE steam_id='$steam_id'");
			$success = true;
			$message = "Rank changed.";
		}
		$json = array(
				'success' => $success,
				'msg' => $message
		);
		echo json_encode($json);
	}
	if (isset($_POST["map_id"]) && isset($_POST["user_id"]) && isset($_POST["set_mod"])) {
		$success = false;
		$message = "An error has happened.";
		$map_id = intval($_POST["map_id"]);
		$user_id = intval($_POST["user_id"]);
		$set_mod = intval($_POST["set_mod"]);
		if ($set_mod==0) {
			mysqli_query($connection,"DELETE FROM map_mods WHERE user_id='$user_id' AND map_id='$map_id'");
			$success = true;
			$message = "User removed from map moderation list.";
		} else if ($set_mod==1) {
			mysqli_query($connection,"INSERT INTO map_mods VALUES('$map_id','$user_id')");
			$success = true;
			$message = "User added to map moderation list.";
		}
		$json = array(
				'success' => $success,
				'msg' => $message
		);
		echo json_encode($json);
	}
	else if (isset($_POST["ban"]) && isset($_POST["user_steam_id"])) {
		$success = true;
		$message = "Ban has been toggled.";
		$steam_id = mysqli_real_escape_string($connection,$_POST["user_steam_id"]);
		mysqli_query($connection,"UPDATE users SET ban=IF(ban=1, 0, 1) WHERE steam_id='$steam_id'");
		$json = array(
				'success' => $success,
				'msg' => $message
		);
		echo json_encode($json);
	}
	else if (isset($_GET["edit"]) || isset($_GET["modify"]) || isset($_GET["delete"])) {
			$success = false;
			$message = "An error has happened.";
			if (isset($_GET["delete"])) {
				if (isset($_POST["map_id"])) {
					$map_id = intval($_POST["map_id"]);
					mysqli_query($connection,"DELETE FROM mod_log WHERE bug_id IN (SELECT id FROM bugs WHERE map_id='$map_id'");
					mysqli_query($connection,"DELETE FROM bugs WHERE map_id='$map_id'");
					mysqli_query($connection,"DELETE FROM map_mods WHERE map_id='$map_id'");
					mysqli_query($connection,"DELETE FROM maps WHERE id='$map_id'");
					$success = true;
					$message = "Map deleted.";
				}
			}
			else {
				$escaped = array_map("htmlspecialchars", $_POST);
				$escaped = array_map(array($connection,'real_escape_string',), $escaped);
				if (isset($escaped["map_id"]) && isset($escaped["map_name"]) && isset($escaped["image_path"]) && isset($escaped["width"]) && isset($escaped["height"]) && isset($escaped["grid_size"]) && isset($escaped["priv_to_mod"])) {
					if (isset($_GET["edit"])) {
					mysqli_query($connection,"UPDATE maps SET name='$escaped[map_name]',image_path='$escaped[image_path]',width='$escaped[width]',height='$escaped[height]',grid_size='$escaped[grid_size]',privilege_to_mod='$escaped[priv_to_mod]' WHERE id='$escaped[map_id]'");
					$message = "Map updated";
					}
					else {
					mysqli_query($connection,"INSERT INTO maps (name,image_path,width,height,grid_size,privilege_to_mod) VALUES('$escaped[map_name]','$escaped[image_path]','$escaped[width]','$escaped[height]','$escaped[grid_size]','$escaped[priv_to_mod]')");
					$message = "New map added.";
					}
					$success = true;

				}
			}
			$json = array(
					'success' => $success,
					'msg' => $message
			);
			echo json_encode($json);
	}
} else {
	$json = array(
			'success' => false,
			'msg' => "The admin panel is disabled. Enable it in the config file. (It's suggested to disable it after you are done.)"
	);
	echo json_encode($json);
}
?>