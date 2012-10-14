<?php
# Make sure we display errors to the browser
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

$scriptName = $_SERVER['PHP_SELF'];
require_once("../include_files/security.php");
require_once("../include_files/info.php");
require_once("../include_files/classes/gamers.class.php");


###flag code

if (isset($_GET['f_id'])){
$flag = secure_clean($_GET['f_id']);

$sql = "INSERT INTO flags (profile_id) VALUES ($flag)";
$result = mysql_query($sql,$connection);
}



if (isset($_GET['p'])){
$page = secure_clean($_GET['p']);
//$scriptName = $scriptName."?p=".$page;

}else {

$page = -1;
//echo 'the page is '.$page;
}

$profiles = array();

function get_profiles($p){
global $profiles;
global $connection;
global $page;
if ($page == '-1' ||$page == '0' ){
$p = 0;
}else{
//$p = $p+1;
}
$limiter = $p.",3";
//echo $limiter;
$sql = "SELECT * FROM profiles ORDER BY date_submitted DESC Limit $limiter ";
//$count = 0;
$result = mysql_query($sql,$connection);
while ($row = mysql_fetch_array($result)){
$profiles[] = $row['profile_id'];
	}

if ($page == '-1'){

//echo 'page is minus';
$profiles[2] = $profiles[1];
$profiles[1] = $profiles[0];


}	
	return $profiles;
}


### form process

if ($_POST['submit_check']== '2'){
$email = secure_clean($_POST['email']);
$gamertag = secure_clean($_POST['gamertag']);
$sql = "UPDATE  profiles SET email = '$email' WHERE gamertag = '$gamertag'";
$result = mysql_query($sql, $connection);


}


$next_page= $page+1;
$up_next_link = '<a href="'.$scriptName.'?p='.$next_page.'">';
$vote_link = $scriptName.'?p='.$next_page;

/*
######################
# Create gamer objects
######################
if ($page == '-1'){
$profiles = get_profiles($page);
//$gamerLast = new gamers($profiles[0]);
$gamerCurrent = new gamers($profiles[1]);
$gamerNext = new gamers($profiles[2]);

$gamerLast = new gamers('0');
$gamerLast->setScore('0.0');
$gamerLast->setVotes('0');
$gamerLast->setImg('/img/avatar_body.png');
$gamerLast->setGamertag('Avatar');


}else{
$profiles = get_profiles($page);

if (!empty($profiles)){

//$profiles = get_profiles($page);
$gamerLast = new gamers($profiles[0]);
$gamerCurrent = new gamers($profiles[1]);
$gamerNext = new gamers($profiles[2]);

}else{

//no more entries go back to begining

$profiles = get_profiles('0');
$gamerLast = new gamers($profiles[0]);
$gamerCurrent = new gamers($profiles[1]);
$gamerNext = new gamers($profiles[2]);

$next_page= '1';
$up_next_link = '<a href="'.$scriptName.'?p='.$next_page.'">';
$vote_link = $scriptName.'?p='.$next_page;


}
//$gamerNext->setImg('/img/avatar_body.png');

}

*/

######################
# Create gamer objects
######################
if ($page == '-1'){
$profiles = get_profiles($page);
//$gamerLast = new gamers($profiles[0]);
$gamerCurrent = new gamers($profiles[1]);
$gamerNext = new gamers($profiles[2]);

$gamerLast = new gamers('0');
$gamerLast->setScore('0.0');
$gamerLast->setVotes('0');
$gamerLast->setImg('/img/avatar_body.png');
$gamerLast->setGamertag('Avatar');
}else{
$profiles = get_profiles($page);

//if we have atleast 1 result in arr
 if (!empty($profiles)){
 
 if(empty($profiles[2])){
// one is missing
//echo "one missing";
$gamerLast = new gamers($profiles[0]);
$gamerCurrent = new gamers($profiles[1]);

$gamerNext = new gamers('0');
$gamerNext->setScore('0.0');
$gamerNext->setVotes('0');
$gamerNext->setImg('/img/avatar_body.png');
$gamerNext->setGamertag('Last Avatar');
 
 }
 
  if (empty($profiles[1])){
// two are missing
//echo "two missing";

$gamerLast = new gamers($profiles[0]);

$gamerCurrent = new gamers('0');
$gamerCurrent->setScore('0.0');
$gamerCurrent->setVotes('0');
$gamerCurrent->setImg('/img/avatar_body.png');
$gamerCurrent->setGamertag('Last Avatar');


$gamerNext = new gamers('0');
$gamerNext->setScore('0.0');
$gamerNext->setVotes('0');
$gamerNext->setImg('/img/avatar_body.png');
$gamerNext->setGamertag('Last Avatar');

}

// do all
if (!empty($profiles[1]) && !empty($profiles[2])){
//echo "do all";

$gamerLast = new gamers($profiles[0]);
$gamerCurrent = new gamers($profiles[1]);
$gamerNext = new gamers($profiles[2]);
//$profiles = get_profiles($page);
}
}else{

//no more entries go back to begining

$profiles = get_profiles('0');
$gamerLast = new gamers($profiles[0]);
$gamerCurrent = new gamers($profiles[1]);
$gamerNext = new gamers($profiles[2]);

$next_page= '1';
$up_next_link = '<a href="'.$scriptName.'?p='.$next_page.'">';
$vote_link = $scriptName.'?p='.$next_page;

}
//$gamerNext->setImg('/img/avatar_body.png');

}

######################
# Voting Process Logic
######################
if ($_POST['submit_check'] == '3'){
$gamerprofile = secure_clean($_POST['profile_id']);
$_score = secure_clean($_POST['score']);
#### entry into votes
$sql = "INSERT INTO profile_x_votes (profile_id, vote_id) VALUES('$gamerprofile','$_score')";
$result = mysql_query($sql, $connection);

##########find the current total
$sql = "SELECT total_id, votes FROM profile_x_totals WHERE profile_id = '$gamerprofile' ";
//echo $sql;
$result = mysql_query($sql, $connection);
$row = mysql_fetch_array($result);

############## if no initial total then write first entry
if (!$row ){
// go ahead and insert
$sql = "INSERT INTO profile_x_totals (profile_id, total_id, votes, added_score)  VALUES ('$gamerprofile','$_score','1','$_score')";
$result = mysql_query($sql, $connection);
$gamerLast->setScore($_score);

}else{
###### we found an entry so lets find the total amt & total votes

$sql = "SELECT total_id, votes, added_score FROM profile_x_totals WHERE profile_id = '$gamerprofile' ";
$result = mysql_query($sql, $connection);
while ($row = mysql_fetch_array($result)){
$total_amt = $row['total_id'];
$total_votes = $row['votes'];
$total_add = $row['added_score'];


}
/*
########### find total votes
$sql "SELECT Count(profile_id) as total_count FROM profile_x_votes WHERE profile_id = '$gamerprofile'";
$result = mysql_query($sql, $connection);
while ($row = mysql_fetch_array($result)){
$total_count = $row['total_count'];
}
*/
$added_score = $total_add + $_score;
$total_votes = $total_votes+1;
$average_score = $added_score/$total_votes;
$average_score = round($average_score, 1);
### put in the total
$sql = "UPDATE profile_x_totals SET total_id = '$average_score', votes ='$total_votes', added_score = '$added_score'  WHERE profile_id ='$gamerprofile'";
$result = mysql_query($sql, $connection);
#### make current profile have new score
$gamerLast->setScore($average_score);
$gamerLast->setVotes($total_votes);

}
}


if ($_POST['submit_check']== '1'){

$gamertag = secure_clean($_POST['gamertag']);
$url_gamertag = rawurlencode($gamertag);

$img = "http://avatar.xboxlive.com/avatar/".$url_gamertag."/avatar-body.png";

if(getimagesize($img))
{
//echo "File Is OK";
$valid_gamer_tag = true;

}
else
{
//echo "File is Broken";
$valid_gamer_tag = false;
$gamerLast->setScore('0.0');
$gamerLast->setVotes('0');
$gamerLast->setImg('/img/avatar_body.png');
$gamerLast->setGamertag('NOT FOUND!');

}

$sql = "SELECT * FROM profiles WHERE gamertag = '$gamertag'";
//echo $sql;
$result = mysql_query($sql, $connection);
$row = mysql_fetch_array($result);
###### if no result its okay to insert

if (!$row && $valid_gamer_tag == true){

$sql = "INSERT INTO profiles (gamertag, img, date_submitted) VALUES('$gamertag','$img',NOW())";
$result = mysql_query($sql, $connection);

$max_id = mysql_insert_id();
$gamerLast = new gamers($max_id);
$gamerLast->setScore('0.0');
$gamerLast->setVotes('0');
$gamerCurrent = new gamers($max_id);



	}

$sql = "SELECT * FROM profiles WHERE gamertag = '$gamertag'";
$result = mysql_query($sql, $connection);
if ($row && $valid_gamer_tag == true){
while($row = mysql_fetch_array($result)){
		$search_gamer = $row['profile_id'];
		}
$gamerLast = new gamers($search_gamer);

	}
} 
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

<title>PUG : Party Up Gamer : Avatar Hot or Not : Let's Play Together</title>
<meta name="description" content="See how your XBOX Live Avatar Stacks up with PUG's AVATAR HOT OR NOT." />
<meta name="keywords" content="hot or not, avatar hot or not, avarar comparison, play games together, xbox, parties, play online, valve, steam, psn, gaming network" />
<meta name="author" content="PUGLY 
. ">
<meta NAME="RATING" CONTENT="General"/>
<meta Name="Distribution" Content="Global"/>

<link href="css/style.css" rel="stylesheet" type="text/css" />

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
<body bgcolor="#fff">

<div id="fb-root"></div>
<script>
  (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=251931958159547";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>

<!-- KILL ANALYTICS -->

	<div id="hotBox">

		<div id="hotHead">
		<div class="innertube">
	
			<div id="headImg"><img src="img/hotCall.png" width="216"></div>
			
			<div id="adMeTopRight">
			
			<script type="text/javascript">
				var AdBrite_Title_Color = 'fa6900';
				var AdBrite_Text_Color = '363636';
				var AdBrite_Background_Color = 'e0e4cc';
				var AdBrite_Border_Color = 'CCCCCC';
				var AdBrite_URL_Color = '69d2e7';
				try{var AdBrite_Iframe=window.top!=window.self?2:1;var AdBrite_Referrer=document.referrer==''?document.location:document.referrer;AdBrite_Referrer=encodeURIComponent(AdBrite_Referrer);}catch(e){var AdBrite_Iframe='';var AdBrite_Referrer='';}
				</script>
				<span style="white-space:nowrap;"><script type="text/javascript">document.write(String.fromCharCode(60,83,67,82,73,80,84));document.write(' src="http://ads.adbrite.com/mb/text_group.php?sid=2115065&zs=3732385f3930&ifr='+AdBrite_Iframe+'&ref='+AdBrite_Referrer+'" type="text/javascript">');document.write(String.fromCharCode(60,47,83,67,82,73,80,84,62));
				</script>
				</span>


</div>
					

		</div>
		</div> 
		<!-- KILLS HEADER -->

		<!-- START NAVI IF NEEDED -->
		<div id="navTop">
		
		<!-- <h4 class="navi"> </h4> -->
			
		</div>
		
		<!-- BEGINS MAIN CONTENT -->
			<div id="wrapItUp">
				<div id="hotCenter">
		
					<div id="voteWrap">
						
						<div id="leftNot"><img src="img/not.png"></div>
						
					<!-- BEGIN VOTE BUTTONS 1-5 -->
					
						<div id="votebox">
						<form action="<?=$vote_link?>" method="POST" >
							

							<input type="hidden" name="profile_id" value="<?=$gamerCurrent->_profile_id?>">
							<input type="hidden" name="submit_check" value="3">
   							<input id="vote-bt" type="submit" class="voteme" name="score" value="1">
   						</div>
   						
					
						<div id="votebox">
   							<input id="vote-bt" type="submit" class="voteme" name="score" value="2">
   						</div>
   						
   						<div id="votebox">
							
   							<input id="vote-bt" type="submit" class="voteme" name="score" value="3">
   						</div>
   						
   						<div id="votebox">
   							<input id="vote-bt" type="submit" class="voteme" name="score" value="4">
   						</div>
   						
   						<div id="votebox">
   							<input id="vote-bt" type="submit" class="voteme" name="score" value="5">
   						</div>
   						
   						</form>
   						
   						<div id="rightHot"><img src="img/hot.png"></div>
					
					</div>
					<div id="voteName">
					<h1 class="not"><center><?=$gamerCurrent->_gamertag?></center></h1>
					</div>
					
					<div id="avaLova">
					
					<img id="avatar" src="<?=$gamerCurrent->_img?>">
				
					
				</div>
					
					<div id="flag"><center><a href="<?=$scriptName."?p=".$next_page."&f_id=".$gamerCurrent->_profile_id?>"><img src="img/Flag.png"></a><br> for <br> removal</center></div>
					
				</div>
			</div>
			
			<!-- BEGIN LEFT SIDE  -->
			
			<div id="hotLeftAd">
			
				<script type="text/javascript">
					var AdBrite_Title_Color = 'fa6900';
					var AdBrite_Text_Color = '363636';
					var AdBrite_Background_Color = 'e0e4cc';
					var AdBrite_Border_Color = 'CCCCCC';
					var AdBrite_URL_Color = '69d2e7';
					try{var AdBrite_Iframe=window.top!=window.self?2:1;var AdBrite_Referrer=document.referrer==''?document.location:document.referrer;AdBrite_Referrer=encodeURIComponent(AdBrite_Referrer);}catch(e){var AdBrite_Iframe='';var AdBrite_Referrer='';}
				</script>
				<script type="text/javascript">document.write(String.fromCharCode(60,83,67,82,73,80,84));document.write(' src="http://ads.adbrite.com/mb/text_group.php?sid=2115104&zs=3132305f363030&ifr='+AdBrite_Iframe+'&ref='+AdBrite_Referrer+'" type="text/javascript">');document.write(String.fromCharCode(60,47,83,67,82,73,80,84,62));
				</script>
				
</div>

			<div id="hotLeft">
			<div id="leftImgTop"><a href="index"><img src="img/beta_call.png" width="216"></a></div>
				<div id="hotBuffer">		
					<div class="innertube">	
					
					<div id="hotNewThing"><h1 class="hot"> <center><?=$gamerNext->_gamertag?></center></h1></div>
					<div id="hotProfileContentL"> 
					<img id="avatarLeft" src="<?=$gamerNext->_img?>" > 
								
					</div>
					<?=$up_next_link?><input  type="submit" class="next" name="next" value="Next >"></a>
					</div>
					<hr class="hr">
					<h1 class="hot">Hotness Rank</h1>
					
<?php
$sql = "SELECT p.gamertag, x.total_id FROM profiles p, profile_x_totals x WHERE x.profile_id = p.profile_id ORDER by total_id DESC Limit 0,5";
//echo $sql;
$_count = 0;

$result = mysql_query($sql, $connection);
while ($row = mysql_fetch_array($result)){
$count+=1;

echo <<<END
{$count} . {$row['gamertag']}<br>
END;

}
unset($count);


?>
					<h1 class="not">Notness Rank</h1>
<?php
$sql = "SELECT p.gamertag, x.total_id FROM profiles p, profile_x_totals x WHERE x.profile_id = p.profile_id ORDER by total_id ASC Limit 0,5";
//echo $sql;

$result = mysql_query($sql, $connection);
while ($row = mysql_fetch_array($result)){

$count+=1;

echo <<<END
{$count} . {$row['gamertag']}<br>
END;


}
unset($count);


?>
	
	</div>
				<div id="leftImg"><h3>Powered by...</h3><a href="index"><img src="img/smallpug.png" width="216"></a></div>	
			</div>
	</div>

			<div id="hotRight">
				<div class="innertube">
					<div id="hotLogin">		
					
					<!-- IF NOT LOGGED IN DISPLAY  -->
					
					<table border=0 padding-right=3 cellspacing=0 class="0">
					<tr>
					<td align="left">
					

<?php


if ($_POST['submit_check'] != '1' || $_POST['submit_check'] != '2') {
echo <<<END

					<form action="$scriptName" method="POST" >
					<input onfocus="this.value=''" type="username" required id="login-bar" class="textbar2"  name="gamertag" value=" xbox gamertag" size="17" maxlength="40"> 
					<input type="hidden" name="submit_check" value="1">
   					<input id="login-signup-bt" type="submit" class="orange" name="submit" value="Submit"> 	
   					<h3>See how your avatar stacks up.</h3>						
   					</form> 


END;



}



?>
					</td>   
					</tr>
					</table>  
					
					<!-- IF LOGGED IN OR IF VOTED DISPLAY -->
	
					<div id="hotProfileContent"> 
					<div id="hotProfileName"><h1 class="hot"><?=$gamerLast->_gamertag?></h1></div>
					<img id="avatarRight" src="<?=$gamerLast->_img?>"> 
					</div>
					
					<div id="hotProfileInfo">
					<h1 class="not">Score</h1>
					<h3 class="score"><?=$gamerLast->_total?></h3>
					Based on <?=$gamerLast->_total_votes?> votes.
					
					<br><br>
					
					<a href="https://twitter.com/share" class="twitter-share-button" data-text="Hot profile on Avatar Hot or Not - LOVE MULTIPLAYER" data-via="PartyUpGamer" data-hashtags="avatarhotornot">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

<br>

<div class="fb-like" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false"></div>

					</div> 
					
					
<?php
if ($_POST['submit_check']== '1' && $valid_gamer_tag == true && !isset($search_gamer)){




echo <<<END

<h4>Claim your avatar. Enter your email for a chance to win prizes including an Avatar Marketplace Shopping Spree and more. </h4>
					<table border=0 padding-right=3 cellspacing=0 class="0">
					<tr>
					<td align="left">
					<form action="$scriptName" method="POST" >
					<input onfocus="this.value=''" type="username" required id="login-bar" class="textbar2"  name="email" value=" email" size="20" maxlength="40"> 
					<input type="hidden" name="submit_check" value="2">
					<input type="hidden" name="gamertag" value="{$gamertag}">

   					<input id="login-signup-bt" type="submit" class="orange" name="submit" value="Save">
   					
   					</form> 
					</td>   
					</tr>
					</table>


END;
}

if ($_POST['submit_check']== '1' && $valid_gamer_tag == false){



echo <<<END

<h4>Looks like we couldn't find that gamertag. Try searching again! </h4>
	

END;



}
 

?>

					
					
					
					<hr class="hr">
									
					<script type="text/javascript">
						var AdBrite_Title_Color = 'fa6900';
						var AdBrite_Text_Color = '363636';
						var AdBrite_Background_Color = 'e0e4cc';
						var AdBrite_Border_Color = 'CCCCCC';
						var AdBrite_URL_Color = '69d2e7';
						try{var AdBrite_Iframe=window.top!=window.self?2:1;var AdBrite_Referrer=document.referrer==''?document.location:document.referrer;AdBrite_Referrer=encodeURIComponent(AdBrite_Referrer);}catch(e){var AdBrite_Iframe='';var AdBrite_Referrer='';}
					</script>
					<script type="text/javascript">document.write(String.fromCharCode(60,83,67,82,73,80,84));document.write(' src="http://ads.adbrite.com/mb/text_group.php?sid=2115071&zs=3330305f323530&ifr='+AdBrite_Iframe+'&ref='+AdBrite_Referrer+'" type="text/javascript">');document.write(String.fromCharCode(60,47,83,67,82,73,80,84,62));
					</script></a>
				</div>
			</div>
				
		</div>


		<div id="hotFoot">
		<center>Working towards a squeaker free experience since 2012 Pugly &copy; 2012 | <a href="/blog/">Blog</a> | <a href="javascript:;" onClick="window.open('tos.php','myWin','scrollbars=yes,width=1000,height=400');">Terms of Service</a> | <a href="/blog/?page_id=62">F.A.Q.</a> | Contact : thepug @ partyupgamer.com	</center><br>
		</div>

	</div>
</body>
</html>