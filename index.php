<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<!--
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
-->
<head>

<style>
body, html {
  height: 100%;
}
</style>


<head>
	<link rel="shortcut icon" type="image/x-icon" href="logos/collector.ico.png" />	
</head>

<body>



<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/4.4.0/papaparse.min.js"></script>
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/ammap.css" type="text/css" media="all" />
    <script src="https://www.amcharts.com/lib/3/ammap.js"></script>
    <script src="https://www.amcharts.com/lib/3/maps/js/worldLow.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="   crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
</head>

<ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#navs-home" role="tab" aria-controls="pills-home" aria-selected="true">Versions</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#navs-contributors" role="tab" aria-controls="pills-profile" aria-selected="false">Contributors</a>
	</li>
	<li class="nav-item"  id="researchers_map_tab">
		<a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#navs-map" role="tab" aria-controls="pills-contact" aria-selected="false">Researcher map</a>
	</li>
  <li class="nav-item"  id="participant_map_tab">
		<a class="nav-link" id="pills-pp-tab" data-toggle="pill" href="#navs-pp_map" role="tab" aria-controls="pills-pp" aria-selected="false">Participant map</a>
	</li>
  <li class="nav-item"  id="security_tab">
		<a class="nav-link" id="pills-security-tab" data-toggle="pill" href="#navs-security" role="tab" aria-controls="pills-security" aria-selected="false">Security</a>
	</li>
	<li class="nav-item"  id="contact_tab">
		<a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#navs-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Contact</a>
	</li>
</ul>
<div class="tab-content" id="pills-tabContent">
	<div class="tab-pane fade show active" id="navs-home" role="tabpanel" aria-labelledby="pills-home-tab">
		<div class="jumbotron">
			<h1 class="display-4">Collector</h1>
			<p class="lead">A free tool for online data collection</p>
			<hr class="my-4">
			<p>Select which version you would like to use:</p>
			<img src="logos/kitten.png"/ style="height:50px;width:50px"> <a class="btn btn-primary btn-lg" href="kitten" role="button">Kitten</a> A developmental version of <b>Cat</b> <br><br>
			<img src="logos/cat.png"/ style="height:50px;width:50px"> <button class="btn btn-disabled btn-lg" href="" role="button">Cat</button> (not yet released)
		</div>
	</div>
	<div class="tab-pane fade" id="navs-contributors" role="tabpanel" aria-labelledby="pills-profile-tab">
		<?php require("contributors.php") ?>	
	</div>
	<div class="tab-pane fade" id="navs-map" role="tabpanel" aria-labelledby="pills-contact-tab">
		<div id="mapdiv" style="width: 100%; height:400px;"></div>
    <div id="listdiv"></div>
	</div>
  <div class="tab-pane fade" id="navs-pp_map" role="tabpanel" aria-labelledby="pills-contact-tab">
		<div id="ppmapdiv" style="width: 100%; height:400px;"></div>
    <div id="pplistdiv"></div>
	</div>
  <div class="tab-pane fade" id="navs-security" role="tabpanel" aria-labelledby="pills-security-tab">
		<?php require("security.html") ?>	
	</div>
	<div class="tab-pane fade" id="navs-contact" role="tabpanel" aria-labelledby="pills-contact-tab">			
			<table class="table">
				<tr>
					<th> Query type </th>
					<th> Person </th>
					<th> Role </th>
					<th> Email </th>
				<tr>
					<td> General </td>
					<td> Dr Anthony Haffey </td>
					<td> Lead Developer </td>
					<td> anthony dot haffey at gmail dot com </td>
				</tr>
			</table>
	</div>
</div>

<style>
#mapdiv {
  background: #eee;
}
</style>
<script>

var icon = "M21.25,8.375V28h6.5V8.375H21.25zM12.25,28h6.5V4.125h-6.5V28zM3.25,28h6.5V12.625h-6.5V28z";

maps_initiated = {
  researcher:false,
  participant:false
}

//navs-pp_map
$("#participant_map_tab").on("click",function(){
  if(maps_initiated.participant == false){
    maps_initiated.participant = true;
    $.post("AjaxPPCountries.php",{},function(returned_data){
      
      data = Papa.parse(Papa.unparse(JSON.parse(returned_data)),{       
        delimiter: "",	// auto-detect
        newline: "",	// auto-detect
        quoteChar: '"',
        escapeChar: '"',
        header: true,
        trimHeader: false,
        dynamicTyping: false,
        preview: 0,
        encoding: "",
        worker: false,
        comments: false,
        step: undefined,
        complete: undefined,
        error: undefined,
        download: false,
        skipEmptyLines: false,
        chunk: undefined,
        fastMode: undefined,
        beforeFirstChunk: undefined,
        withCredentials: undefined
      });
            
      areas_array  = [];
      images_array = [];
      
      var table_content = "<table class='table'>" +
                            "<thead>" +
                              "<tr>" +
                                "<th scope='col'>Country</th>" +
                                "<th scope='col'>Participants</th>" +
                              "</tr>" +
                            "</thead>" +
                            "<tbody>";
      
      
      var row_order = Object.keys(data.data);
      
      row_order.sort(function(a,b) {
        return data.data[a].frequency - data.data[b].frequency;
      });
      console.dir(data);
      data.data_sorted = [];
      for(var i = 0; i < data.data.length; i++){
        data.data_sorted[i] = data.data[row_order[i]];
      }
      data.data_sorted = data.data_sorted.reverse();
      
      var max_frequency = 0;
      data.data_sorted.forEach(function(row){
        if(row.frequency > max_frequency){
          max_frequency = row.frequency;
        }
      });
      
      data.data_sorted.forEach(function(row){
        
        table_content +=  "<tr>"+
                            "<td>" + row.country + "</td>" +
                            "<td>" + row.frequency + "</td>" +
                          "</tr>" 
        
        var this_opacity = row.frequency/max_frequency < .1 ? .1 : row.frequency/max_frequency;
        
        if(row.institute !== ""){
          areas_array.push({
            id:row.code,
            color:"rgb(0, 0, 255, " + this_opacity + ")",
            //color:"#6495ED",
            fillAlphas:row.frequency
          });
          
        }
      });
      
      table_content += "</tbody>"  + "</table>";
      
      $("#pplistdiv").html(table_content);
      
      AmCharts.makeChart( "ppmapdiv", {
        /**
         * this tells amCharts it's a map
         */
        "type": "map",
      
      
     
        /**
         * create data provider object
         * map property is usually the same as the name of the map file.
         * getAreasFromMap indicates that amMap should read all the areas available
         * in the map data and treat them as they are included in your data provider.
         * in case you don't set it to true, all the areas except listed in data
         * provider will be treated as unlisted.
         */
        "dataProvider": {
          "map": "worldLow",
          "areas": areas_array,
      "images": images_array
        },
      "projection": "winkel3",

        /** 
         * create areas settings
         * autoZoom set to true means that the map will zoom-in when clicked on the area
         * selectedColor indicates color of the clicked area.
         */
        "areasSettings": {
          "autoZoom": true,
          "selectedColor": "#CC0000"
        },

        /**
         * let's say we want a small map to be displayed, so let's create it
         */
        "smallMap": {}
      });		 
    })
    
  
  }  
  
});

$("#researchers_map_tab").on("click",function(){
  if(maps_initiated.researcher == false){
    maps_initiated.researcher = true;
  
    $.post("AjaxInstitutions.php",{},function(returned_data){
      data = Papa.parse(Papa.unparse(JSON.parse(returned_data)),{       
        delimiter: "",	// auto-detect
        newline: "",	// auto-detect
        quoteChar: '"',
        escapeChar: '"',
        header: true,
        trimHeader: false,
        dynamicTyping: false,
        preview: 0,
        encoding: "",
        worker: false,
        comments: false,
        step: undefined,
        complete: undefined,
        error: undefined,
        download: false,
        skipEmptyLines: false,
        chunk: undefined,
        fastMode: undefined,
        beforeFirstChunk: undefined,
        withCredentials: undefined
      });

      console.dir(data.data);
      data.data = data.data.filter(row => row.latitude !== "");
            
            
      areas_array  = [];
      images_array = [];
      
      var table_content = "<table class='table'>" +
                            "<thead>" +
                              "<tr>" +
                                "<th scope='col'>Institute</th>" +
                                "<th scope='col'>Country</th>" +                                
                              "</tr>" +
                            "</thead>" +
                            "<tbody>";
      
      
      data.data.forEach(function(row){
        
        table_content +=  "<tr>"+
                            "<td>" + row.institute + "</td>" +
                            "<td>" + row.country + "</td>" +
                          "</tr>" 
        
        if(row.institute !== ""){
          areas_array.push({
            id:row.code,
            color:"#6495ED"
          });
          images_array.push({
            
            "latitude": row.latitude,
            "longitude": row.longitude,
            "svgPath": icon,
            "color": "#CCCC00",
            "scale": 0.5,
            "label": row.institute,
            "labelShiftY": 2
            
          });
        }
      });
      
      table_content += "</tbody>"  + "</table>";
      
      $("#listdiv").html(table_content);
      
      AmCharts.makeChart( "mapdiv", {
        /**
         * this tells amCharts it's a map
         */
        "type": "map",
      
      
     
        /**
         * create data provider object
         * map property is usually the same as the name of the map file.
         * getAreasFromMap indicates that amMap should read all the areas available
         * in the map data and treat them as they are included in your data provider.
         * in case you don't set it to true, all the areas except listed in data
         * provider will be treated as unlisted.
         */
        "dataProvider": {
          "map": "worldLow",
          "areas": areas_array,
      "images": images_array
        },
      "projection": "winkel3",

        /** 
         * create areas settings
         * autoZoom set to true means that the map will zoom-in when clicked on the area
         * selectedColor indicates color of the clicked area.
         */
        "areasSettings": {
          "autoZoom": true,
          "selectedColor": "#CC0000"
        },

        /**
         * let's say we want a small map to be displayed, so let's create it
         */
        "smallMap": {}
      });		 
    });
  }
});



</script>