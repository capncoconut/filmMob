<?php
# Make sure we display errors to the browser
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

$scriptName = $_SERVER['PHP_SELF'];
require_once("../../include_files/security.php");
require_once("../../include_files/info.php");
require_once("../../include_files/session.php");

require_once("../../include_files/classes/gamer.class.php");
require_once("../../include_files/classes/contact.class.php");
require_once("../../include_files/pug_functions.php");

$fullScriptName = fullScriptName();
#########################################################
# Initialize a new session or obtain old one if possible
#########################################################
session_name($mySessionName); # $mySessionName is defined in info_session.php
session_start();

########################################
#CHECK LOGIN STATUS
########################################
if ($_SESSION['log_status']!="true" || $_SESSION['gamer_id']<1)
{ header("Location: index.php?error=login");
exit;
}

$gamer_id = secure_clean( $_SESSION['gamer_id']);


if(isset($_GET['i'])){
$gamer_profile = $_GET['i'];
$gamer_profile = secure_clean($gamer_profile);
}else{

$gamer_profile = '1';

}
$profile = new gamer($gamer_profile,$gamer_id);
/*
echo	$profile->_gamertag ;
echo	$profile->_img ;  
echo	$profile->_small_img;  
echo	$profile->_about_me;
echo	$profile->_looking_for;
echo	$profile->_city;
echo	$profile->_state;
echo	$profile->_country;
*/
###### process add contact form
$submit_check = secure_clean($_POST['submit_check']);
if ($submit_check =="add_contact"){
$contact = new contacts($gamer_id);
$add_new_contact = $contact->add_contact($gamer_profile);
$profile->set_contact_state(true);


}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<meta name="viewport" content = "width=device-width, initial-scale = 1.0, maximum-scale=1.0, user-scalable = no">

<title>PUG : Party Up Gamer : Mobility</title>
<meta name="description" content="Search for people to play with only on Party Up Gamer" />
<meta name="keywords" content="muliplayer, gaming network, gamer dating, gamer matchmaking, finding people to play with, xbox, play xbox with friends"/>
<meta name="author" content="PUGLY">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta NAME="RATING" CONTENT="General"/>
<meta Name="Distribution" Content="Global"/>

<link href="css/mob.css" rel="stylesheet" type="text/css" />
<link href='http://fonts.googleapis.com/css?family=Press+Start+2P' rel='stylesheet' type='text/css'>

<!-- THIS HERE IS SUPPOSED TO HIDE THE ADDRESS BAR -->  

<script>
 window.addEventListener("load",function() {
   setTimeout(function(){
    window.scrollTo(0, 0);
    }, 0);
 });
</script>

<!-- THIS HERE BE GOOGLE ANALITICS CODE -->  

<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-29425707-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</head>

<body orient="portrait">

<?php
include_once ('include/header.php');
?>

<div id="wrapper">
	<div id="signLine">
		<!-- BEGIN PROFILE SECTION -->  
		
		<div id="settingsTopBox">
		
		<div id="imgTopBox"><img src="<?=$profile->_small_img?>" width="30px"></div>
		<div id="settingsTop"><?=$profile->_gamertag?></div>
		
		</div>
		
		
		<hr class="hr2">

		<div id="settingsDescribe" color="white">
		Account
		</div>	
		
		<a href="profile.php?i=<?=$gamer_id?>">
		<div id="settingsLinkBox">
			<div id="settingsBoxImg">
				<img src="img/SettingsProfile.png" height="30px;">
			</div>
			<div id="settingsBoxTxt">
				<input type="submit" class="btnSettings" name ="submit" value ="View Profile">

			</div>
		</div>
		</a>
		
		<a href="profileEdit.php?i=<?=$gamer_id?>">
		<div id="settingsLinkBox">
			<div id="settingsBoxImgAlt">
				<img src="img/edit.png" height="20px;">
			</div>
			<div id="settingsBoxTxt">
				<input type="submit" class="btnSettings" name ="submit" value ="Edit Profile">

			</div>
		</div>
		</a>
		
		<div id="settingsDescribe" color="white">
		About
		</div>	
		
		<a href="aboutPUG.php">
		<div id="settingsLinkBox">
			<div id="settingsBoxImg">
				<img src="img/foot.png" height="30px;">
			</div>
			<div id="settingsBoxTxt">
				<input type="submit" class="btnSettings" name ="submit" value ="About PUG">
			</div>
		</div>
		</a>
		
		<div id="settingsDescribe" color="white">
		Logout
		</div>	
		
		<a href="signout.php">
		<div id="settingsLinkBox">
			<div id="settingsBoxImgAlt">
				<img src="img/circle.png" height="20px;">
			</div>
			<div id="settingsBoxTxt">
				<input type="submit" class="btnSettings" name ="submit" value ="Logout">
			</div>
		</div>
		</a>
		

   		<!-- <input type="submit" class="signUp" name ="submit" value ="SIGN ME UP"> <br/><br/> -->

		<br> 		
	</div>

	
		<div id="eachResult">

			<div id="quickMessage">
			</div>
		</div>
     
     	<div id="signupBottom"></div>
     
		
	</div>
	
</div>



<?php 
include_once ('include/footer.php'); 
?>
</body>
</html>
