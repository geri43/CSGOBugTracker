<?php
include_once("design.php");
// Which map to load
if (isset($_GET["map"])) {
	$map_id = intval($_GET["map"]);
} else {
	$map_id = "1";
}
$get_map = mysqli_query($connection,"SELECT * FROM maps WHERE id='$map_id'");
$map = array("name"=>"Not found");
$buglist = array();
$history = array();
// Get bug list
if (mysqli_num_rows($get_map)==1) {
	$map = mysqli_fetch_array($get_map);
	$get_buglist = mysqli_query($connection,"SELECT id,bugs.user_id,coords,type,state,register_date,resolve_date,description,media,priority,steam_persona,steam_avatar,steam_id FROM bugs JOIN users ON users.user_id=bugs.user_id WHERE map_id='$map[id]' ORDER BY register_date DESC");
	if (mysqli_num_rows($get_buglist)>0) {
		while ($r=mysqli_fetch_assoc($get_buglist)) {
			$buglist[] = $r;
			$get_history = mysqli_query($connection,"SELECT steam_persona,steam_avatar,steam_id,action,message,time,bug_id FROM mod_log JOIN users ON users.user_id=mod_log.mod_user_id WHERE bug_id='$r[id]' ORDER BY bug_id,time");
			if (mysqli_num_rows($get_history)>0) {
				$rhistory = array();
				while ($r2=mysqli_fetch_assoc($get_history)) {
					$rhistory[] = $r2;
				}
				$history[$r["id"]]=$rhistory;
			}
		}
	}
}
?>
<link href="css/bootstrap-sortable.min.css" rel="stylesheet">
<link href="css/bootstrap-multiselect.css" rel="stylesheet">
<script src="js/bootstrap-sortable.js"></script>
<script src="js/bootstrap-multiselect.js"></script>
<script src="js/moment.min.js"></script>
<script src="js/map_bug.js?v=2"></script>
<div class='container' style='padding-bottom:100px;background-color:rgba(0,0,0,0.7);'>
				<div class='row'>
					<h2 class="text-center"><?php echo $map["name"];?></h2>
					<div class='col-sm-10 col-sm-offset-1'>
					<table style='width:100%;'><tr><td>
						<form class='form-inline pull-left'>
						<label for='filter_type'>Filter by type:</label>
						<div id='filter_type_holder' class='form-group hidden'>
						<select id='filter_type' multiple='multiple' class='form-control'>
							<option value='0'>Wallbang</option>
							<option value='1'>Nade</option>
							<option value='2'>Texture</option>
							<option value='3'>Model</option>
							<option value='4'>Sound</option>
							<option value='5'>Material</option>
							<option value='6'>Navmesh</option>
							<option value='7'>Pixelwalking</option>
							<option value='8'>Stuck bomb</option>
							<option value='9'>Boost</option>
							<option value='10'>Bomb plant</option>
							<option value='11'>Skybox</option>
							<option value='12'>Door</option>
							<option value='13'>Brush</option>
							<option value='14'>Other</option>
						</select>
						</div>
						</form>
						</td>
						<td>
						<?php
						// It's only used for de_nuke.
						if ($map_id==1) {
						echo "<label>Map:</label>";
						echo "<button disabled='disabled' type='button' class='btn btn-default'>Top</button>";
						echo "<button type='button' class='btn-default btn btn:hover' onclick='changeMap(2)'>Bottom</button>";
						} else if ($map_id==2) {
						echo "<label>Map:</label>";
						echo "<button type='button' class='btn-default btn btn:hover' onclick='changeMap(1)'>Top</button>";
						echo "<button disabled='disabled' type='button' class='btn btn-default'>Bottom</button>";
						}
						?>
						
						</td>
						<td>
						<form class='form-inline pull-right'>
						<label for='mapchange'>Select map:</label>
						<div class='form-group'><select id='mapchange' class='form-control'>
						<?php
						// Get map list, except bottom nuke.
						$select = mysqli_query($connection,"SELECT id,name FROM maps WHERE id!=2");
						while ($r=mysqli_fetch_array($select)) 
						{ 
						echo "<option".(($map_id==$r["id"])?" selected='selected":"")." value='".$r["id"]."'>".$r["name"]."</option>";
						}
						?>
						</select></div></form>
						</td>
						</tr>
						</table>
					</div>
				</div>
				<div class='row'>
					<div class='col-sm-12'>
						<div class='text-center'>
							<div id="map" style='display:inline-block;position:relative;'>
							<img id="map_img">
							</div><br>
							<button class='btn btn-default' style='margin-top:3px;' type='button' onclick='show(-1)'>List all bugs</button>
						</div>
					</div>

				</div>
	<!-- Modal -->
	<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-body">
			<div id="modal-content">
				<div class='row'>
					<div class='col-lg-8 col-lg-offset-2 col-md-8 col-md-offset-2'>
						<div id="warning-box" class="alert alert-danger dynamicboxes text-center" role="alert" style="display:none;"></div>
						<div id="success-box" class="alert alert-success dynamicboxes text-center" role="alert" style="display:none;"></div>
					</div>
				</div>
				<h3>Bug list</h3>
				<table style='width:100%;' id="bug-table" class="table table-striped sortable">
					<thead>
					</tr><th>Type</th><th>Description</th><th>Media</th><th>Status</th><th>Posted by</th><th>Actions</th></tr>
					</thead>
					<tbody id="bug-table-body">
					
					</tbody>
				</table>
				<div id='post_area'>
				<h3>Post a bug</h3>
				<?php 
				if ($logged) {
				?>
				<form class="form-inline">
				<label for="type">Type:</label>
				<div class='form-group'>
					<select class='form-control' id="type">
						<option value='0'>Wallbang</option>
						<option value='1'>Nade</option>
						<option value='2'>Texture</option>
						<option value='3'>Model</option>
						<option value='4'>Sound</option>
						<option value='5'>Material</option>
						<option value='6'>Navmesh</option>
						<option value='7'>Pixelwalking</option>
						<option value='8'>Stuck bomb</option>
						<option value='9'>Boost</option>
						<option value='10'>Bomb plant</option>
						<option value='11'>Skybox</option>
						<option value='12'>Door</option>
						<option value='13'>Brush</option>
						<option value='14'>Other</option>
					</select>
					<span style='font-style:italic;' id='type_tooltip'>Inconsistent wallbang</span>
				</div>
				<br>
				<label for="description">Description:</label>
				<div class='form-group'>
					<textarea maxlength='500' class='form-control' id='description'></textarea>
				</div>
				<br>
				<label for="media">Imgur/Gyfcat/Youtube link:</label>
				<div class='form-group'>
					<input class='form-control' id='media'>
				</div>
				<br>
				<input class='form-control' type='button' onclick='postBug(<?php echo $map["id"];?>)' value='Post'>
				</form>
				<?php
				}
				else {
					echo "<h3 class='text-center'>You must be signed in through Steam to post bugs.</h3><div class='text-center'>";
					steamlogin();
					echo "</div>";
				}
				?>
				</div>
			</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
</div>
<script>
var map = <?php echo json_encode($map); ?>;
var buglist = <?php echo json_encode($buglist);?>;
var histories = <?php echo json_encode($history);?>;
var selected_coords = 0;
var coord_bugs = {};
function changeMap(id) {
	var url = "index.php?map="+id;
	if (url) {
		  window.location = url;
	}
}
$( "#mapchange" ).change(function() {
			changeMap($(this).val());
});
$( "#type" ).change(function() {
			$("#type_tooltip").html(types_tooltip[$(this).val()]);
});
$( "#filter_type" ).change(function() {
	displayBugs($( "#filter_type" ).val());
});
$( document ).ready(function() {
$.bootstrapSortable();
$('#filter_type').multiselect();
$("#filter_type_holder").removeClass("hidden");
if (map!=null) {
	if (map["id"]!=0) {
	$("body").css("background-image", "url(images/maps/"+map["image_path"]+"_background.jpg)");
	}
	$("#map_img").attr("src","images/maps/"+map["image_path"]+".png");
	var topa = 0;
	var lefta = 0;
	var owidth = parseInt(map["width"]);
	var oheight = parseInt(map["height"]);
	var grid_size = parseInt(map["grid_size"]);
	var maxw = Math.floor(owidth/grid_size);
	var maxh = Math.floor(oheight/grid_size);
	var counter = 0;
	// Build map grid
	for (var s=0;s<maxh;s++) {
		for (var i=0;i<maxw;i++) {
		counter++;
		$("#map").append("<div id="+counter+" onclick=show("+counter+") class=cube style='width:"+grid_size+"px;height:"+grid_size+"px;top:"+(topa)+"px;left:"+(lefta)+"px;'></div>");
		lefta+=grid_size;
		}
		lefta=0;
		topa+=grid_size;
	}
	// Overlay bug count
	displayBugs();
}
});
</script>		
<?php		
		if (!$logged) {
					echo "<h3 class='text-center'>Sign in through Steam, to post bugs.</h3><div class='text-center'>";
					steamlogin();
					echo "</div>";
		}

include_once("design2.php");
?>