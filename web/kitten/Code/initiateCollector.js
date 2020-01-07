/*
  Collector (Garcia, Kornell, Kerr, Blake & Haffey)
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
if(typeof(dev_obj) == "undefined"){
  dev_obj = {};
}
dev_obj.context = "";
dev_obj.version = "kitten"; //this needs to be updated when moving to cat, but perhaps can just be hardcoded
dev_obj.published_links = [];


function collectorPapaParsed(preparsed){

	post_parsed = Papa.parse(Papa.unparse(preparsed),{
		beforeFirstChunk: function(chunk) {
			var rows = chunk.split( /\r\n|\r|\n/ );
			var headings = rows[0].toLowerCase();
			rows[0] = headings;
			return rows.join("\r\n");
		},
		header:true,
		skipEmptyLines:true
	}).data;

	return post_parsed;
}

function create_alerts_container() {
	if (typeof(alerts_ready) !== "undefined" && alerts_ready) return;

	var top_padding = parseFloat($("#sim_navbar").css("height").replace("px","")) + parseFloat($("#top_navbar").css("height").replace("px",""));


	var el = $("<div>");
	el.css({
			position: "fixed",
			top: top_padding + "px",
			left: "10px",
			right: "10px",
			backgroundColor: "#ffc8c8",
			borderRadius: "6px",
			border: "1px solid #DAA",
			color: "#800",
	});

	el.attr("id", "alerts");
	el.css("z-index", "1000");

	$("body").append(el);

	var style = $("<style>");
	style.html("#alerts > div { margin: 10px 5px; }");

	$("body").append(style);

	alerts_ready = true;
}
function custom_alert(msg,duration) {
  if(typeof(duration) == "undefined"){
		duration = 1000;
	}
	create_alerts_container();
	var el = $("<div>");
	el.html(msg);
	el.css("opacity", "0");
	$("#alerts").append(el).show();
	el.animate({opacity: "1"}, 600, "swing", function() {
		$(this).delay(duration).animate({height: "0px"}, 800, "swing", function() {
			$(this).remove();
			if ($("#alerts").html() === '') {
				$("#alerts").hide();
			}
		});
	});
}
function detect_context(){
  if(document.URL.indexOf("localhost") !== -1){
    return "localhost";
  } else if(document.URL.indexOf("github.io") !== -1) { //assume it's github
    return "github";
  } else if(document.URL.indexOf("gitpod.io") !== -1){
    return "gitpod"
  } else {
    return "server";
  }
}
function detect_version(){
  if(document.URL.indexOf("/kitten/") !== -1){
    return "kitten";
  }
}

//detect if this is local or github or ocollector.org
function initiate_collector(){
  dev_obj.context = detect_context();
  switch(dev_obj.context){
    case "server":
      $.post("code/initiateCollector.php",{
				//nothing to post, just want to run it.
      },function(local_key){
				$("#login_div").show();
        if(local_key !== "no-key"){
          window.localStorage.setItem("local_key",local_key);
        }
      });
      break;
    case "github":
    case "gitpod":
    case "localhost":
      $("#logged_in").show();
      break;
  }
}

function update_dropdown_list(list_id,list,option_class){
    user_trialtype_list = user_trialtype_list.sort();
	user_trialtype_list.forEach(function(trialtype){
		var new_option = "<option class='"+option_class+"'>"+trialtype+"</option>";
		$("#"+list_id).append(new_option);
	});
}

String.prototype.replaceAll = function(str1, str2, ignore) //by qwerty at https://stackoverflow.com/questions/2116558/fastest-method-to-replace-all-instances-of-a-character-in-a-string
{
  return this.replace(new RegExp(str1.replace(/([\/\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g,"\\$&"),(ignore?"gi":"g")),(typeof(str2)=="string")?str2.replace(/\$/g,"$$$$"):str2);
}

initiate_collector();