// Texts
var types = ["Wallbang","Nade","Texture","Model","Sound","Material","Navmesh","Pixelwalking","Stuck bomb","Boost","Bomb plant","Skybox","Door","Brush","Other"];
var types_tooltip = ["Inconsistent wallbang","Bugged nade interaction","Texture glitch, wrong scale etc.","Model (cars,boxes,pipes...) issues: draw distance,hitbox etc.",
"Ambient sound, walls blocking sound, etc.","Texture/Model incorrect material (e.g metal catwalk - dirt sound)","Bot navigation problem","Walking on invisible ledges",
"A spot where a dropped bomb becomes inaccessible","Unintented boosts","Bugged bomb-plant interaction","Skybox related issues","Bugged door interaction","Invisible walls","Not listed bug type"]  
var states = ["Unconfirmed","Confirmed","Fixed","Removed"];
var actions = {"set_state":"set state to","comment":"commented"};
// Basic functions
function steamProfileIcon(steam_avatar,steam_persona,steam_id) {
	return "<a href='http://steamcommunity.com/profiles/"+steam_id+
					"' target='_blank'><img src='https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/"+steam_avatar+
					".jpg'>"+steam_persona+
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
						histories[bug_id].push({"id":0,"steam_persona":user_steam_persona,"steam_avatar":user_steam_avatar,"steam_id":user_steam_id,"action":"set_state","message":_state,"time":new Date().getTime(),"bug_id":bug_id});
						break;
					}
				}
				displayBugs($( "#filter_type" ).val());
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
					displayBugs($( "#filter_type" ).val());
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
					histories[id].push({"steam_persona":user_steam_persona,"steam_avatar":user_steam_avatar,"steam_id":user_steam_id,"action":"comment","message":comment,"time":new Date().getTime()/1000,"bug_id":id});
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
		if (_state==0) {
			string+=" <span title='Set Confirmed' class='cursor' onclick='setStateBug("+id+",1)'>✔</span><span title='Set Removed' class='cursor' onclick='setStateBug("+id+",3)'>✘</span>";
		}
		else if (_state==1) {
			string+=" <span title='Set Fixed' class='cursor' onclick='setStateBug("+id+",2)'>✘</span>";
		}
	}
	return string;
}
function showBugActions(id) {
	var string="";
	string+="<span class='cursor' onclick='toggleHistory("+id+")'>Show history</span>";
	if (rank>0) {
		string+="<br><span class='cursor' onclick='addComment("+id+")'>Add comment</span>";
	}
	return string;
}
function toggleHistory(id) {
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
			string+="<tr><td>"+steamProfileIcon(cur_hist["steam_avatar"],cur_hist["steam_persona"],cur_hist["steam_id"])+" "+actions[cur_hist["action"]]+" <b>"+cur_message+"</b> on "+cur_date.toLocaleDateString()+" "+cur_date.toLocaleTimeString()+"</td></tr>";
			}
		} else {
			string+="<tr><td>No history</td></tr>";
		}
		string += "</table>";
		$("#row_"+id).after("<tr id=history_"+id+"><td colspan=6>"+string+"</td></tr>");
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
	if (id!=-1) {
	bugs = coord_bugs[id];
	$("#post_area").show();
	} else {
	bugs = buglist;
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
			user = steamProfileIcon(bug["steam_avatar"],bug["steam_persona"],bug["steam_id"]);
			state = states[bug["state"]];
			$("#bug-table-body").append("<tr id='row_"+bug["id"]+"'><td>"+type+" issue</td><td><div title='Click to display more' style='height:60px; overflow:hidden; cursor:pointer;' onclick=\"$(this).css('overflow','');$(this).css('height','');\">"+description+"</div></td><td><div style='height:60px; overflow:hidden'>"+media+"</div></td><td>"+state+""+showBugStates(bug["state"],bug["id"])+"</td><td>"+user+"</td><td>"+showBugActions(bug["id"])+"</td></tr>");
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
}
function displayBugs(filter_type) {
	$(".badge-holder").remove();
	for (var i=0;i<buglist.length;i++) {
		bug = buglist[i];
		allow = true;
		if (filter_type!=undefined) {
			if (filter_type.indexOf(bug["type"])==-1) {
				allow=false;
			}
		}
		if (allow) {
			if (bug["state"]<2) {
				pushBug(bug);
			}
		}
	}
}