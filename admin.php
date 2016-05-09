<?php
include_once("design.php");
if ($logged && $user_data["rank"]>1) {
$get_maps = mysqli_query($connection,"SELECT * FROM maps");
$maps = array();
while ($r=mysqli_fetch_assoc($get_maps)) {
	$maps[] = $r;
}

$get_users = mysqli_query($connection,"SELECT user_id,steam_id,steam_persona,rank,ban FROM users ORDER BY steam_persona ASC");
$users = array();
while ($r=mysqli_fetch_assoc($get_users)) {
	$users[] = $r;
}

$get_map_mods =  mysqli_query($connection,"SELECT user_id,map_id FROM map_mods");
$map_mods = array();
while ($r=mysqli_fetch_assoc($get_map_mods)) {
	$map_mods[] = $r;
}
?>
<script src="js/map_bug.js?v=2"></script>
<script src="js/admin_bug.js?v=1"></script>
<div class='container' style='padding-bottom:100px;'>
				<div class='row'>
					<div class='col-md-8 col-md-offset-2'>
						<h2 class="text-center">Admin panel</h2>
						<div id="warning-box" class="alert alert-danger dynamicboxes text-center" role="alert" style="display:none;"></div>
						<div id="success-box" class="alert alert-success dynamicboxes text-center" role="alert" style="display:none;"></div>
						<h3>Edit maps</h3>
						<form class='form-inline' id='mapForm'>
							<label for="select_map">Select map:</label>
							<select class='form-control' id="select_map">
								<option value='-1'>Select a map</option>
							</select><br>
							<div class='form-group' id='map_fields' style='display:none;'>
							Id :<input class='form-control' name="map_id" disabled='disabled' value='-1' id="map_id"><br>
							<label for="map_name">Map name</label>
							<input name="map_name" id='map_name' class='form-control' value=''><br>
							<label for="image_path">Image path</label>
							<input name="image_path" id='image_path' class='form-control' value=''><br>
							<label for="width">Width</label>
							<input name="width" id='width' class='form-control' value=''><br>
							<label for="height">Height</label>
							<input name="height" id='height' class='form-control' value=''><br>
							<label for="grid_size">Grid size</label>
							<input name="grid_size" id='grid_size' class='form-control' value=''><br>
							<label for="priv_to_mod">Need privilege to mod (0 or 1)</label>
							<input name="priv_to_mod" id='priv_to_mod' class='form-control' value=''><br>
							<button type='button' class='btn btn-primary' onclick='editMap()'>Save settings</button> - <button type='button' class='btn btn-primary' onclick='newMap()'>Insert as new map</button> - <button type='button' class='btn btn-primary' onclick='deleteMap()'>Delete map</button>
							</div>
						</form>
						
						<h3>Edit users</h3>
						<form class='form-inline'>
						<label for="select_user">Select user:</label>
							<select class='form-control' id="select_user">
								<option value='-1'>Select an user</option>
							</select><br>
							<div class='form-group' id ='user_fields' style='display:none;'>
								<input type='hidden' id='user_steam_id'>
								<input type='hidden' id='user_id'>
								<label>Current rank: </label><span id='user_rank'></span><br>
								<label>Banned: </label><span id='user_banned'></span><br>
								Ban: <button type='button' class='btn btn-primary' onclick='banUser()'>Toggle ban</button><br>
								Rank: <button type='button' class='btn btn-primary' onclick='rankUser(0)'>Set User</button> - <button type='button' class='btn btn-primary' onclick='rankUser(1)'>Set Moderator</button> - <button type='button' class='btn btn-primary' onclick='rankUser(2)'>Set Admin</button><br>
								Map moderator on: <span id='map_mod'></span><br>
								Map mod (need_privilege): <button type='button' class='btn btn-primary' onclick='mapModUser(1)'>Allow moderation on selected map</button> - <button type='button' class='btn btn-primary' onclick='mapModUser(0)'>Remove moderation status</button>
							</div>
						</form>
						
					</div>
				</div>
</div>
<script>
var map_list = <?php echo json_encode($maps); ?>;
var user_list = <?php echo json_encode($users); ?>;
var rank_list = <?php echo json_encode($rank_array); ?>;
var map_mod_list = <?php echo json_encode($map_mods); ?>;
$( "#select_map" ).change(function() {
			changeMap($(this).val());
});
$( "#select_user" ).change(function() {
			changeUser($(this).val());
});
$( document ).ready(function() {
	for (i=0;i<map_list.length;i++) {
		map = map_list[i];
		$("#select_map").append("<option value="+i+">"+map["id"]+" - "+map["name"]+"</option>");
	}
	for (i=0;i<user_list.length;i++) {
		user = user_list[i];
		$("#select_user").append("<option value="+i+">"+user["steam_persona"]+" - "+user["steam_id"]+"</option>");
	}
});
</script>
<?php
}	
include_once("design2.php");
?>