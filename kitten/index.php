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
	<link rel="shortcut icon" type="image/x-icon" href="../logos/collector.ico.png" />
	<meta charset="utf-8">
</head>


<script src="browserCheck.js"></script>
<?php
require_once 'Code/initiateCollector.php';
require_once("libraries.html");
require "Code/nojs.php";
?>

<?php
if(isset($_SESSION['user_email']) && $_SESSION['user_email'] !== 'guest'){    	
  require_once "../../sqlConnect.php";  
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	$cwd = explode("/",getcwd ());
	if(count($cwd) == 1){ //then developing on local host
		$cwd = explode("\\",getcwd ());
		$_SESSION['version'] = $cwd[5];
		$_SESSION['local_website'] = "http://localhost/collector_local";
	} else {
    $_SESSION['version'] = $cwd[4];
		$_SESSION['local_website'] = "https://www.ocollector.org";
	}
  $logo_string = explode(".",$_SESSION['version']);
  $logo_string = $logo_string[0];

?>
<link rel="stylesheet" href="Style.css"></link>
<nav class="navbar fixed-top navbar-light bg-primary navbar-expand-lg" id="top_navbar" style="height:50px; padding:5px">
  <?php require("../logos/logo.php") ?>
	<div class="collapse navbar-collapse">	
		<ul class="navbar-nav mr-auto">
			<div id="page_selected"></div>
		</ul>		    
	</div>
	<a href="https://github.com/anthonyhaffey/open-collector/issues" target="_blank" style="margin:2px">
		<button class="btn btn-primary">Discuss</button>
	</a>
	<a href="http://www.uoropen.org/Workshops/Collector-Tutorial/" target="_blank" style="margin:2px">
		<button class="btn btn-primary">Tutorial</button>
	</a>
	<button class="btn btn-outline-primary bg-white" id="help_btn" style="margin:2px; font-weight:bold">Help</button>
	<?php require("LogoutInterface.php"); ?>
</nav>


<table id="content_table" style="width:100%">
  <tr>
		<td colspan="2" class='survey_cell_view_td'><textarea id="survey_cell_view" readonly></textarea></td>		
	</td>
	<tr>
    <td id="content_area">
      <?php 
        $names = array("Simulator","Surveys","Boost","Data");
        $pages = ["Simulator/simulator.php","Surveys/Surveys.php","Boost/Boost.php","Data/data.php"];
        for($i = 0; $i < count($pages); $i++){
          $this_name = $names[$i];
          $this_page = $pages[$i];          
      ?>
          <div class="collapse multi-collapse" style="width:100%" id="<?php echo "collapse_$this_name" ?>" >
            <?php require("IndexTabs/$this_page"); ?>		
          </div>	          
      <?php
        }
?>
    </td>		
		<td id="help_area" class="help_class">
			<?php 
				if($_SESSION['user_email'] !== "guest"){
					require("IndexTabs/Help/help.php");
				}
			?>
		</td>
	</tr>
</table>


<script>

navbar_names  = <?= json_encode($names) ?>;
navbar_html = "";
navbar_colors = ["primary","primary","primary","primary"];

navbar_names.forEach(function(name,index){
	navbar_html += '<label class="btn btn-'+navbar_colors[index]+' select_page" id="option_'+name+'"  data-toggle="collapse" href="#collapse_'+name+'" role="button" aria-expanded="false" aria-controls="#collapse_'+name+'">'+
		'<input type="radio" style="display:none" name="options" autocomplete="off" >'+name+
	'</label>';	
});
$("#page_selected").html(navbar_html);

$(".select_page").on("click",function(){	
	$('.collapse').collapse('hide');
	$('.select_page').css("font-weight","normal");
  $(this).css("font-weight","bold");	
  $('.select_page').removeClass("bg-white");
  $('.select_page').removeClass("text-primary");  
  $(this).addClass("bg-white");
  $(this).addClass("text-primary");
  var this_id = this.id;
	if(this_id == "option_Simulator"){
		$("#help_content").animate(
		{
			top:"100px"
		},
		{
			duration:200      
		});  
	} else {
		setTimeout(function(){	
			$("#help_content").animate(
			{
				top:"60px"
			},
			{
				duration:200
			});
		},300);		
	}
});	
if($("#collector_account_email").html() == "--undefined--" | $("#collector_account_email").html() == "guest"){
	highlight_account("dropbox_account_email");
	highlight_account("collector_account_email");	
}

</script>


<script>

window.mobilecheck = function() {
  var check = false;
  (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
  return check;
};

if(window.mobilecheck() == false) {
	$("#large_view").show();
	$("#mobile_view").hide();
} else {
	$("#large_view").hide();
	$("#mobile_view").show();
};

</script>
<?php
	mysqli_close($conn);
} else {
  require "LoginInterface.php";
}
?>