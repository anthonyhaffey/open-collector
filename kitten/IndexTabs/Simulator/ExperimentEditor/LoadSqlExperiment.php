<?php
/*  Collector (Garcia, Kornell, Kerr, Blake & Haffey)
    A program for running experiments on the web
    Copyright 2012-2016 Mikey Garcia & Nate Kornell


    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 3 as published by
    the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
 		
		Kitten release (2019) author: Dr. Anthony Haffey (a.haffey@reading.ac.uk)		
*/  
	$new_exp_json = file_get_contents(__DIR__ . '/default_new_experiment.json');
		
	$user_email  = $_SESSION['user_email'];
	$initial_sql = "Select * FROM `users` WHERE `email`='$user_email'";
	$result 	 = $conn->query($initial_sql);
	
	
	$user_data = mysqli_fetch_assoc($result);	
	// prevent frequent exposure of user data by removing on server side	
	unset($user_data['password']);
	unset($user_data['email_confirm_code']);
	unset($user_data['pepper']);
	unset($user_data['salt']);	
	$user_data 	 = json_encode($user_data);	
	$experiment_sql = "SELECT * FROM `view_experiment_researchers` WHERE `email`='$user_email'";	
	$result 	 = $conn->query($experiment_sql);	
	$experiments	=	[];	
	$published_links  = [];
  
	while($row = $result->fetch_assoc()) {
		array_push($experiments,  	$row['name']);	
		array_push($published_links, $row['published_id']."|".$row['experiment_id']);		
	}
  
	
?>

<div id="load_toolbar">
	<!-- 				  
		This example takes the user through Dropbox's API OAuth flow using <code>Dropbox.getAuthenticationUrl()</code> method [<a href="http://dropbox.github.io/dropbox-sdk-js/Dropbox.html#getAuthenticationUrl">docs</a>] and then uses the generated access token to list the contents of their root directory.				
	-->				  
		
	<table>
		<tr>
			<td>				
				<button type="button" id="new_experiment_button" class="btn btn-primary">New</button>
			</td>
			<td>
				<span id="experiments"></span>				        
			</td>      
			<td>
				<span id="dropbox_inputs" style="display:none">
					<button id="save_btn" class="btn btn-primary">Save</button>					
					<button id="rename_exp_btn" class="btn btn-primary">Rename</button>
					<button id="delete_exp_btn" class="btn btn-primary">Delete</button>
					<button id="versions_btn" class="btn btn-primary">Versions</button>
					<input  id='publish_link' type='text' readonly style='display:none'>
					<input  id='preview_link' type='text' readonly style='display:none'>
					<button id="run_btn"      class="btn btn-primary">Run</button>
					<a id="run_link" style="display:none" href="" target="_blank"></a>					
				</span>				
			</td>			
		</tr>
	</table>
</div>

<script> /* notifying of new shared experiments */

var new_experiment_data = <?= $new_exp_json ?>;
	
</script>


<script> /* authenticating dropbox account */
$("#links_btn").on("click",function(){
	var experiment = megaUberJson.exp_mgmt.experiment.replaceAll(" ","");
	
	participant_link = $("#publish_link").val();
	preview_link = $("#preview_link").val();
	iframe_code  = 	"<button class='btn btn-primary' data-toggle='collapse' data-target='#preview_"+experiment+"'> Show/Hide Preview </button>"+
					"<button class='btn btn-primary' onclick='window.open(\""+preview_link+"\");'>Open in new tab</button>"+
	"<div id='preview_"+experiment+"' class='collapse'> "+
	"<table><tr><td align='right'>"+
	"<button class='btn btn-primary' onclick='$(\"#iframe_"+experiment+"\").attr(\"src\",\""+preview_link+"\")'>Refresh</button></td></tr>"+
	"<tr><td><iframe id='iframe_"+experiment+"' src='"+preview_link+"' style='width:800px; height:800px'></iframe></td></tr>"+
	"</table>"+
	"</div>";
	
	
	bootbox.dialog({
		title:"Links",
		message:"Send these links to participants or collaborators:<br><br>"+
				"<table>"+
					"<tr>"+
						"<td>Participant link (to collect data)</td>"+
						"<td><input style='width:300px' onfocus='this.select();' readonly value='"+participant_link+"'></td>"+
					"</tr>"+
					"<tr>"+
						"<td>Preview link (will <b style='color:red'>NOT</b> collect data)</td>"+
						"<td><input style='width:300px' onfocus='this.select();' readonly value='"+preview_link+"'></td>"+
					"</tr>"+
					"<tr>"+
						"<td valign='middle'>iframe code</td>"+
						"<td><textarea style='width:300px;height:250px;' onfocus='this.select();' readonly>"+iframe_code+"</textarea></td>"+
					"</tr>"+
				"</table>",
	});		
});

$("#rename_exp_btn").on("click",function(){
	bootbox.prompt("What would you like to rename this experiment to?",function(new_name){
		if($("#experiment_list").text().indexOf(new_name) !== -1){
			bootbox.alert("You already have an experiment with this name");
		} else { //proceed
			var original_name = $("#experiment_list").val();
			dbx.filesMove({from_path:"/Experiments/"+original_name+".json",to_path:"/Experiments/"+new_name+".json"})
				.then(function(result){
					megaUberJson.exp_mgmt.experiments[new_name] = 
					megaUberJson.exp_mgmt.experiments[original_name];
					delete(megaUberJson.exp_mgmt.experiments[original_name]);
					$.post("IndexTabs/Simulator/AjaxMySQL.php",{
						action:"rename",
						original_name:original_name,
						new_name:new_name
					}, function(returned_result){
						updateUberMegaFile();
						list_experiments();
						$("#experiment_list").val(new_name);						
					});
				})
				.catch(function(error){
					report_error(error);
				});
		}
		//confirm that there isn't another experiment with that name
		
	});
});

$("#run_btn").on("click",function(){
	var select_html = '<select id="select_condition" class="custom-select">';
	clean_conditions();  
  exp_json.conditions.forEach(function(condition){
		select_html += "<option>" + condition.name + "</option>";
	});
	select_html += "</select>";
	
	bootbox.dialog({
		title:"Select a Condition",
		message:"The multiple conditions functionality <b>HAS ONLY</b> just been finalised. Please pilot this carefully when using it in your own research. <br><br> Which condition would you like to run? <br><br>" + select_html, 
		buttons: {
			start: {
				label: "Start",
				className: 'btn-primary',
				callback: function(){
					var selected_cond_name = $("#select_condition").val();					
					exp_condition = selected_cond_name;
					var link_old = $("#run_link")[0].href;
							link_old = link_old.split("&");
							link_new = link_old[0] + "&name=" + selected_cond_name;
					$("#run_link")[0].href = link_new;
					$("#run_link")[0].click();
				}
			},
			cancel: {
				label: "Cancel",
				className: 'btn-secondary',
				callback: function(){
					//nada;
				}
			}
		}
	});	
});

$("#versions_btn").on("click",function(){	
	if(typeof($_GET) == "undefined" || typeof($_GET.uid) == "undefined"){
		bootbox.alert("If you login a dropbox account, it'll automatically backup your experiment files");
	} else {
		experiment = megaUberJson.exp_mgmt.experiment;
		var version_address = "https://www.dropbox.com/history/Apps/Open-Collector/experiments/"+experiment+".json?_subject_uid="+ $_GET.uid +"&undelete=1";
		
		$("#synch_btn").on("click",function(){
			alert("hi there");
		});
		
		var dialog = bootbox.dialog({
			title: 'Revert back to an earlier version',
			message: "<p>Click <a href='"+version_address+"' target='_blank'>here</a> to see version history of this file in dropbox<br><br>If you've reverted the current experiment '"+experiment+"' to an earlier version, click on the 'synch' button to load the reverted version of the experiment.</p>",
			buttons: {
					synch: {
							label: "Synch",
							className: 'btn-primary',
							callback: function(){
								$.get(megaUberJson.exp_mgmt.experiments[experiment].location.replace("www.","dl."),function(result){
									megaUberJson.exp_mgmt.experiments[experiment] = JSON.parse(result);
									updateUberMegaFile();
									update_handsontables();
								});
							}
					},
					cancel: {
							label: "Cancel",
							className: 'btn-secondary',
							callback: function(){
									//nothing, just close
							}
					},
			}
    });
	}
});

(function(window){
	window.utils = {
		parseQueryString: function(str) {
			var ret = Object.create(null);
			if (typeof str !== 'string') {
				return ret;
			}
			str = str.trim().replace(/^(\?|#|&)/, '');
			if (!str) {
				return ret;
			}
			str.split('&').forEach(function (param) {
			var parts = param.replace(/\+/g, ' ').split('=');
			// Firefox (pre 40) decodes `%3D` to `=`
			// https://github.com/sindresorhus/query-string/pull/37
			var key = parts.shift();
			var val = parts.length > 0 ? parts.join('=') : undefined;

			key = decodeURIComponent(key);

			// missing `=` should be `null`:
			// http://w3.org/TR/2012/WD-url-20120524/#collect-url-parameters
			val = val === undefined ? null : decodeURIComponent(val);

			if (ret[key] === undefined) {
			  ret[key] = val;
			} else if (Array.isArray(ret[key])) {
			  ret[key].push(val);
			} else {
			  ret[key] = [ret[key], val];
			}
			});

			return ret;
		}
	};
})(window);


// get dropbox token for user
var CLIENT_ID = '6xumb4iloq9sz1u';



function check_trialtypes_in_proc(procedure,post_trialtype){
	var experiment 		= megaUberJson.exp_mgmt.experiment;
	var this_exp   		= megaUberJson.exp_mgmt.experiments[experiment];
	var this_proc  		= this_exp.all_procs[procedure];
	var trialtypes 		= [];
	var trial_type_col  = this_proc[0].map(function(element){
		if(element !== null){
			return element.toLowerCase();
		}		
	}).indexOf(post_trialtype);	
	for(var i in this_proc){
		if(i > 0) {
			if(this_proc[i][trial_type_col] !== null){				
				trialtypes.push(this_proc[i][trial_type_col].toLowerCase());
			}
		}
	}		
	trialtypes = trialtypes.filter(n => n);
	console.dir(trialtypes);
	if(typeof(megaUberJson.exp_mgmt.experiments[experiment].trialtypes) == "undefined"){
		megaUberJson.exp_mgmt.experiments[experiment].trialtypes = {};
	}
	trialtypes.forEach(function(trialtype){				
		if(typeof(megaUberJson.trialtypes.user_trialtypes[trialtype]) !== "undefined"){
			megaUberJson.exp_mgmt.experiments[experiment].trialtypes[trialtype] = megaUberJson.trialtypes.user_trialtypes[trialtype];
		} else if(typeof(megaUberJson.trialtypes.default_trialtypes[trialtype]) !== "undefined"){
			megaUberJson.exp_mgmt.experiments[experiment].trialtypes[trialtype] = megaUberJson.trialtypes.default_trialtypes[trialtype];
		} else {
			custom_alert("Invalid trialtype <b>"+trialtype+"</b> in at least one of your procedure sheets. The file will save, but the experiment won't run until you use a valid trialtype.",4000);
		}
	});	
}
function getAccessTokenFromUrl() { // Parses the url and gets the access token if it is in the urls hash
 return utils.parseQueryString(window.location.hash).access_token;
}
function isAuthenticated() { // If the user was just redirected from authenticating, the urls hash will contain the access token.
  return !!getAccessTokenFromUrl();
}
function list_experiments(){	
	name_list = Object.keys(megaUberJson.exp_mgmt.experiments);	
	//synch with dropbox
	dbx.filesListFolder({path:"/experiments"})
		.then(function(experiments){
			experiments.entries.forEach(function(entry){
				if(entry[".tag"] == "file" && entry.name.indexOf(".json") !== -1 ){
					var entry_name = entry.name.toLowerCase().replace(".json","");
					//do not write over uberMegaFile for now if there is an experiment json with the same name
					if(name_list.indexOf(entry_name) == -1){					
						name_list.push(entry_name);
						synch_experiment(entry_name);						
					}
				}				
			});
			var select_html = "<select id='experiment_list'  class='custom-select'><option hidden disabled selected>Select an experiment</option>";
			name_list.sort(function(a,b){
				return a.toLowerCase().localeCompare(b.toLowerCase());
			});
			name_list.forEach(function(item_name){
				select_html += "<option>" + item_name + "</option>";
			});
			select_html += "</select>";
			$("#experiments").html(select_html);
			$("#experiment_list").on("change",function(){
				if(first_load == false){
					megaUberJson.exp_mgmt.any_loaded = true;
					$("#save_btn").click();
				} else {						
					remove_from_list("Select a dropbox experiment");
					first_load = false;
				}				
				megaUberJson.exp_mgmt.experiment = this.value;
				exp_json = megaUberJson.exp_mgmt.experiments[megaUberJson.exp_mgmt.experiment];	
			
				var this_exp = megaUberJson.exp_mgmt.experiments[this.value];		
				$("#dropbox_inputs").show();
				$("#run_link").attr("href","https://www.open-collector.org/"+ megaUberJson.exp_mgmt.version + "/sqlExperiment.php?location="+this_exp.location);		
				$("#run_btn").attr("title","https://www.open-collector.org/"+ megaUberJson.exp_mgmt.version + "/sqlExperiment.php?location="+this_exp.location);		
				update_handsontables();
			});
		})
		.catch(function(error){
			report_error(error);
		});
}

function renderItems() {
  // Highlight to users which accounts they are logged in with
  ////////////////////////////////////////////////////////////
  highlight_account("dropbox_account_email");
  highlight_account("collector_account_email");

  list_experiments();
	list_boosts();
  list_surveys();
	list_trialtypes();
	list_graphics();

	
	first_load = true;
	
	
	var published_list = <?= json_encode($published_links) ?>;
	megaUberJson.exp_mgmt.published_ids = {};
	for(var i = 0; i< name_list.length; i++){
		megaUberJson.exp_mgmt.published_ids[name_list[i]] = published_list[i];
	}
	megaUberJson.exp_mgmt.version = "<?= $_SESSION['version'] ?>";
	initiate_actions();	
	user_data = megaUberJson.exp_mgmt.user_data;
  
  autoload_boosts();  
}
function synch_experiment(entry_name){
	dbx.sharingCreateSharedLink({path:"/experiments/" + entry_name + ".json"})
		.then(function(result){
			console.dir(result);
			$.get(result.url.replace("www.","dl."), function(exp_json){
				megaUberJson.exp_mgmt.experiments[entry_name] = JSON.parse(exp_json);
			});
		})
		.catch(function(error){
			report_error(error);
		});
}
function stim_proc_defaults(proc_values,stim_values){
	var experiment = megaUberJson.exp_mgmt.experiment;
	var this_exp   = megaUberJson.exp_mgmt.experiments[experiment];
	
	// selecting Stimuli.csv and Procedure.csv as default	
	if(proc_values.indexOf("Procedure.csv") !== -1){
		$('#proc_select').val("Procedure.csv");
		this_exp.procedure = "Procedure.csv";
	} else {
		this_exp.procedure = this_exp[proc_values[0]];
	}
	if(stim_values.indexOf("Stimuli.csv") !== -1){
		$('#stim_select').val("Stimuli.csv");
		this_exp.stimuli = "Stimuli.csv";
	} else {
		this_exp.stimuli = this_exp[stim_values[0]];
	}
}
function update_dropdown_lists(){	
	var experiment = megaUberJson.exp_mgmt.experiment;
	var this_exp   = megaUberJson.exp_mgmt.experiments[experiment];
	var stim_values = [];
	var proc_values = [];
  
  //wipe the stimuli list
  $('#proc_select').find('option').remove();
  $('#stim_select').find('option').remove();
  
  //wipe the procedure list
  
	Object.keys(this_exp.all_procs).forEach(function(this_proc){
		proc_values.push(this_proc);
		$('#proc_select').append($('<option>', {
			value: 	this_proc,
			text: 	this_proc
		}));
	});
	Object.keys(this_exp.all_stims).forEach(function(this_stim){
		stim_values.push(this_stim);
		$('#stim_select').append($('<option>', {
			value: 	this_stim,
			text: 	this_stim
		}));
	});
	stim_proc_defaults(proc_values,stim_values);
}
function update_handsontables(){
	var experiment = megaUberJson.exp_mgmt.experiment;
	var this_exp   = megaUberJson.exp_mgmt.experiments[experiment];
	
	update_dropdown_lists();
	var stim_file = this_exp.stimuli; //location_to_filename(this_exp.stimuli);
	var proc_file = this_exp.procedure; //location_to_filename(this_exp.procedure);
	
	if(typeof(stim_file) == "undefined"){
		stim_file = Object.keys(this_exp.all_stims)[0];
		proc_file = Object.keys(this_exp.all_procs)[0];
	}
	createExpEditorHoT(this_exp.all_stims[stim_file],"Stimuli",  stim_file);
	createExpEditorHoT(this_exp.all_procs[proc_file], "Procedure",proc_file);
	createExpEditorHoT(this_exp.cond_array,"Conditions","Conditions.csv");
	
	$("#run_stop_buttons").show();
	megaUberJson.exp_mgmt.any_loaded = true;	
	$("#dropbox_inputs").show();
}
function update_trial_json(){
	// list all the trialtypes currently existing;
	var experiment 		= megaUberJson.exp_mgmt.experiment;
	var this_exp   		= megaUberJson.exp_mgmt.experiments[experiment];
	var proc_trialtypes = {};
	var proc_keys		= Object.keys(this_exp.all_procs);
	
	//list all columns with trialtype in them 	
	var post_trialtypes = this_exp.all_procs["Procedure.csv"][0].filter(function(key){
		return /trial type/.test(key);
	});	
	proc_keys.forEach(function(procedure){
		post_trialtypes.forEach(function(trialtype){			
			check_trialtypes_in_proc(procedure,trialtype.toLowerCase());		
		});
	});	
}
function updateUberMegaFile(new_old){
	if(typeof(new_old) !== "undefined" && typeof(new_old) == "new"){		
		message = "Your uberMegaFile has been created. Enjoy creating experiments for free!";
		var dialog = bootbox.dialog({
			title: 'Creating your uberMegaFile',
			message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>'
		});
		dbx_obj.new_upload({path:"/uberMegaFile.json",contents:JSON.stringify(megaUberJson),mode:'overwrite'},function(result){			
				dialog.find('.bootbox-body').html(message);
				setTimeout(function(){
					dialog.modal("hide");
				},10000);				
				if($("#simulator_table").is(":hidden")){			
					$("#option_Edit").click();				
				}
			},function(error){
				bootbox.alert(error.error + "<br> Perhaps wait a bit and save again?");;
			});			
	} else {
		dbx_obj.new_upload({path:"/uberMegaFile.json",contents:JSON.stringify(megaUberJson),mode:'overwrite'},function(result){			
			if($("#simulator_table").is(":hidden")){				
				$("#option_Edit").click();				
			}
		},function(error){
			bootbox.alert(error.error + "<br> Perhaps wait a bit and save again?");
		});		
	} 
};

if(typeof(user_data.received_experiments) !== "undefined"){
	user_data.received_experiments 	 = user_data.received_experiments.split("||");	
} else {
	user_data.received_experiments 	 = [];	
}

if(typeof(user_data.accepted_experiments) !== "undefined"){
	user_data.accepted_experiments 	 = user_data.accepted_experiments.split("||");	
} else {
	user_data.accepted_experiments 	 = [];	
}



// CTRL key shortcuts
/////////////////////

var keys = {};
$(document).keydown(function (e) {
    keys[e.which] = true;
});

$(window).bind('keydown', function(event) {  
  if (event.ctrlKey || event.metaKey) {
    switch (String.fromCharCode(event.which).toLowerCase()) {
      case 's':
        event.preventDefault();
          $("#save_btn").click();
        break;
    }
  }	
});
$(window).bind('keyup', function(event) {
  delete keys[event.which];
});

// Experiment Template
//////////////////////

experiment_template = {
  "commit_message":"Original commit",
  "procedure": "Procedure.csv",
  "stimuli": "Stimuli.csv",
  "cond_array": [
    [
      "name",
      "notes",
      "stimuli",
      "procedure",
			"login",
			"download_at_end",
			"participant_id",
			"completion_code",
			"start_message",
			"end_message",
			"fullscreen",
			"buffer"
    ],
    [
      "Condition1",
      "You can put more detailed notes in this field",
      "Stimuli.csv",
      "Procedure.csv",
			"on",
			"on",
			"on",
			"on",
			"",
			"",
			"on",
			5
    ]
  ],
  "cond_loc": "https://dl.dropbox.com/s/tmg3e80od7avxxl/Conditions.csv?dl=0",
  "all_procs": {
    "Procedure.csv": [
      [
        "Item",
        "trial type",
        "Max Time",
        "Text",
        "Shuffle 1"
      ],
      [
        "0",
        "instruct",
        "user",
        "Thank you for participating in this experiment. The task will begin during the next trial.",
        "off"
      ]
    ]
  },
  "all_stims": {
    "Stimuli.csv": [
      [
        "Cue",
        "Answer"
      ],
      [
        "A",
        "Apple"
      ],
      [
        "B",
        "Banana"
      ],
    ]
  }
}
</script>