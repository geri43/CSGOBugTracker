// Texts
var types = ["Wallbang","Nade","Texture","Model","Sound","Material","Navmesh","Pixelwalking","Stuck bomb","Boost","Bomb plant","Skybox","Door","Brush","Other"];
var types_tooltip = ["Inconsistent wallbang","Bugged nade interaction","Texture glitch, wrong scale etc.","Model (cars,boxes,pipes...) issues: draw distance,hitbox etc.",
"Ambient sound, walls blocking sound, etc.","Texture/Model incorrect material (e.g metal catwalk - dirt sound)","Bot navigation problem","Walking on invisible ledges",
"A spot where a dropped bomb becomes inaccessible","Unintented boosts","Bugged bomb-plant interaction","Skybox related issues","Bugged door interaction","Invisible walls, bad clipping.","Not listed bug type"]  
var states = ["Unconfirmed","Confirmed","Fixed","Removed"];
var actions = {"set_state":"set state to","comment":"commented"};
// Basic functions
function steamProfileIcon(array) {
	return "<a href='http://steamcommunity.com/profiles/"+array["steam_id"]+
					"' target='_blank'><img src='https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/"+array["steam_avatar"]+
					".jpg'>"+array["steam_persona"]+
					"</a>";
}
function showMessage(success,msg) {
	$( ".dynamicboxes" ).hide( "medium", function() {});
	if (success) {
		$( "#success-box" ).text(msg);
		$( "#success-box" ).show( "medium", function() {});
	} else {
		$( "#warning-box" ).text(msg);
		$( "#warning-box" ).show( "medium", function() {});
	}
}
// Ajax functions
function setStateBug(bug_id,_state) {
	$.ajax({
		url: 'map_ajax.php?setstate',
		type: 'GET',
		data: {'bug_id': bug_id,'state': _state},
		success:function(result){
			obj = JSON.parse(result);
			showMessage(obj.success,obj.msg);
			if (obj.success==true) {
				for (i=0;i<buglist.length;i++) {
					if (buglist[i]["id"]==bug_id) {
						buglist[i]["state"]=_state;
						if (histories[bug_id]==undefined) {
						histories[bug_id] = [];
						}
						histories[bug_id].unshift({"id":0,"steam_persona":user_steam_persona,"steam_avatar":user_steam_avatar,"steam_id":user_steam_id,"action":"set_state","message":_state,"time":new Date().getTime()/1000,"bug_id":bug_id});
						break;
					}
				}
				displayBugs();
				show(selected_coords);
			}
		}
	});
}
function postBug(map_id) {
	post_type = $("#type").val();
	post_description = $("#description").val();
	post_media = $("#media").val();
	if (post_description=="" || post_media=="") {
		showMessage(false,"All fields must be filled.");
	} else {
		$.ajax({
			url: 'map_ajax.php?post',
			type: 'POST',
			data: {'type': post_type, 'description': post_description, 'media': post_media, 'map': map_id, 'coords' : selected_coords },
			success:function(result){
				obj = JSON.parse(result);
				showMessage(obj.success,obj.msg);
				if (obj.success) {
					media = "<a href='"+post_media+"' target='_blank'>"+post_media+"</a>";
					buglist.unshift(JSON.parse(obj.get));
					displayBugs();
					show(selected_coords);
				}
			}
		});
	}
}
function addComment(id) {
	var comment = prompt("Please enter your comment");
	if (comment != null) {
		$.ajax({
			url: 'map_ajax.php?comment',
			type: 'POST',
			data: {'comment': comment,'bug_id':id },
			success:function(result){
				obj = JSON.parse(result);
				showMessage(obj.success,obj.msg);
				if (obj.success==true) {
					toggleHistory(id);
					if (histories[id]==undefined) {
					histories[id] = [];
					}
					histories[id].unshift({"steam_persona":user_steam_persona,"steam_avatar":user_steam_avatar,"steam_id":user_steam_id,"action":"comment","message":comment,"time":new Date().getTime()/1000,"bug_id":id});
					toggleHistory(id);
				}
			}
		});
	}
}
// Bug list display functions
function showBugStates(_state,id) {
	var string = "";
	if (rank>0) {
		string+="<select data-id='"+id+"' class='modifyChanger form-control'><option value='-1'>Modify:</option>";
		for (_i=0;_i<states.length;_i++) {
			if (_state!=_i) {
				string+="<option value='"+_i+"'>"+states[_i]+"</option>";
			}
		}
		string+="</select>";
	}
	return string;
}
function showBugActions(id,array_idx) {
	var string="";
	string+="<span class='cursor' onclick='toggleHistory("+id+","+array_idx+")'>Show history</span>";
	if (rank>0) {
		string+="<br><span class='cursor' onclick='addComment("+id+")'>Add comment</span>";
	}
	return string;
}
function toggleHistory(id,array_idx) {
	if ($("#history_"+id).html()==undefined) {
		var string = "<table class='table'>";
		if (histories[id]!=undefined) {
			for (i=0;i<histories[id].length;i++) {
			var cur_hist = histories[id][i];
			var cur_date = new Date(cur_hist["time"]*1000);
			var cur_message = cur_hist["message"];
			if (cur_hist["action"]=="set_state") {
			cur_message = states[cur_message];
			}
			string+="<tr><td>"+steamProfileIcon(cur_hist)+" "+actions[cur_hist["action"]]+" <b>"+cur_message+"</b> on "+cur_date.toLocaleDateString()+" "+cur_date.toLocaleTimeString()+"</td></tr>";
			}
		}
		var post_date = new Date(bugs[array_idx]["register_date"]*1000);
		string+="<tr><td>"+steamProfileIcon(bugs[array_idx])+" posted this bug report on "+post_date.toLocaleDateString()+" "+post_date.toLocaleTimeString()+"</td></tr>";
		string += "</table>";
		$("#row_"+id).after("<tr class=history-table id=history_"+id+"><td colspan=6>"+string+"</td></tr>");
	} else {
		$("#history_"+id).remove();
	}
}
// Modal display functions
function show(id) {
	if (id!=selected_coords) {
		$( ".dynamicboxes" ).hide();
	}
	selected_coords = id;
	bugs = [];
	if (id!=-1) {
		bugs = coord_bugs[id];
		$("#post_area").show();
	} 
	else {
		for (i=0;i<buglist.length;i++) {
			if (bugAllowed(buglist[i])) {
				bugs.push(buglist[i]);
			}
		}
		$("#post_area").hide();
	}
	$("#bug-table-body").html("");
	if (bugs==undefined) {
		$("#bug-table-body").html("<tr><td colspan=6>No bugs posted here.</td></tr>");
	} else {
		for (i=0;i<bugs.length;i++) {
			bug = bugs[i];
			type = types[bug["type"]];
			description = bug["description"];
			media = "<a href='"+bug["media"]+"' target='_blank'>"+bug["media"]+"</a>";
			user = steamProfileIcon(bug);
			state = states[bug["state"]];
			$("#bug-table-body").append("<tr id='row_"+bug["id"]+"'><td>"+type+" issue</td><td><div title='Click to display more' style='height:60px; overflow:hidden; cursor:pointer;' onclick=\"$(this).css('overflow','');$(this).css('height','');\">"+description+"</div></td><td><div style='height:60px; overflow:hidden'>"+media+"</div></td><td>"+state+""+showBugStates(bug["state"],bug["id"])+"</td><td>"+user+"</td><td>"+showBugActions(bug["id"],i)+"</td></tr>");
		}
		$.bootstrapSortable();
	}
	$("#myModal").modal("show");
}
// Bug list arranging functions
function pushBug(bug) {
	find = $('#'+bug["coords"]+'_has');
	if (find.length) {
		coord_bugs[bug["coords"]].push(bug);
		count = parseInt(find.html());
		find.html(count+1);
	} else {
		coord_bugs[bug["coords"]] = [];
		coord_bugs[bug["coords"]].push(bug);
		$("#"+bug["coords"]).append("<div class='badge-holder'><span class='badge' id='"+bug["coords"]+"_has'>1</span></div>");
	}
	if (bug["state"]==3) {
		$("#"+bug["coords"]+"_has").css("background-color","#E64607");
	} else if (bug["state"]==2) {
		$("#"+bug["coords"]+"_has").css("background-color","#81C456");
	}
	else if (bug["state"]==0) {
		$("#"+bug["coords"]+"_has").css("background-color","#E69A07");
	}
}
function displayBugs() {
	$(".badge-holder").remove();
	for (var i=0;i<buglist.length;i++) {
		bug = buglist[i];
		if (bugAllowed(bug)) {
			pushBug(bug);
		}
	}
}
function bugAllowed(_bug) {
		allow = true;
		filter_type = $( "#filter_type" ).val();
		filter_state = $( "#filter_state" ).val();
		if (filter_type!=undefined) {
			if (filter_type.indexOf(_bug["type"])==-1) {
				allow=false;
			}
		}
		if (filter_state!=undefined && allow) {
			if (filter_state.indexOf(_bug["state"])==-1) {
				allow=false;
			}
		}
		return allow;
}