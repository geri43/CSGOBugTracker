function editMap() {
	var sure = confirm("Modifying width, height, grid size will misplace all current bugs on the map. Are you sure?");
	if (sure) {
		disabled=$("#mapForm").find(':input:disabled').removeAttr('disabled');
		$.ajax({
			url: 'admin_ajax.php?edit',
			type: 'POST',
			data: $("#mapForm").serialize(),
			success:function(result){
				disabled.attr('disabled','disabled');
				obj = JSON.parse(result);
				showMessage(obj.success,obj.msg);
			}
		});
	}
}
function newMap() {
	disabled=$("#mapForm").find(':input:disabled').removeAttr('disabled');
	$.ajax({
		url: 'admin_ajax.php?modify',
		type: 'POST',
		data: $("#mapForm").serialize(),
		success:function(result){
			disabled.attr('disabled','disabled');
			obj = JSON.parse(result);
			showMessage(obj.success,obj.msg);
		}
	});
}
function deleteMap() {
	var sure = confirm("This will delete all bugs, or history linked to this map. Are you sure?");
	if (sure) {
		$.ajax({
			url: 'admin_ajax.php?delete',
			type: 'POST',
			data: {"map_id":$("#map_id").val()},
			success:function(result){
				obj = JSON.parse(result);
				showMessage(obj.success,obj.msg);
			}
		});
	}
}
function mapModUser(rank) {
	map_id = $("#map_id").val();
	if (map_id==-1) {
		showMessage(0,"Select a map");
	} else {
		$.ajax({
			url: 'admin_ajax.php',
			type: 'POST',
			data: {"user_id":$("#user_id").val(),"set_mod":rank,"map_id":map_id},
			success:function(result){
				obj = JSON.parse(result);
				showMessage(obj.success,obj.msg);
			}
		});
	}
}
function rankUser(rank) {
	$.ajax({
		url: 'admin_ajax.php',
		type: 'POST',
		data: {"user_steam_id":$("#user_steam_id").val(),"rank":rank},
		success:function(result){
			obj = JSON.parse(result);
			showMessage(obj.success,obj.msg);
		}
	});
}
function banUser(rank) {
	$.ajax({
		url: 'admin_ajax.php',
		type: 'POST',
		data: {"user_steam_id":$("#user_steam_id").val(),"ban":"0"},
		success:function(result){
			obj = JSON.parse(result);
			showMessage(obj.success,obj.msg);
		}
	});
}
function changeMap(id) {
	if (id!=-1) {
		map = map_list[id];
		$("#map_id").val(map["id"]);
		$("#map_name").val(map["name"]);
		$("#image_path").val(map["image_path"]);
		$("#width").val(map["width"]);
		$("#height").val(map["height"]);
		$("#grid_size").val(map["grid_size"]);
		$("#priv_to_mod").val(map["privilege_to_mod"]);
		$("#map_fields").show();
	} else {
		$("#map_fields").hide();
	}
}
function changeUser(id) {
	if (id!=-1) {
		user = user_list[id];
		$("#user_rank").html(rank_list[user["rank"]]);
		$("#user_banned").html(user["ban"]);
		$("#user_steam_id").val(user["steam_id"]);
		$("#user_id").val(user["user_id"]);
		$("#map_mod").html("");
		for (i=0;i<map_mod_list.length;i++) {
			if (map_mod_list[i]["user_id"]==user["user_id"]) {
				for (t=0;t<map_list.length;t++) {
					if (map_list[t]["id"]==map_mod_list[i]["map_id"]) {
						$("#map_mod").append("[Map:"+map_list[t]["name"]+",Id:"+map_list[t]["id"]+"] ");
					}
				}
			}
		}
		$("#user_fields").show();
	} else {
		$("#user_fields").hide();
	}
}