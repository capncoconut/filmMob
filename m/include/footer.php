<?php
/* FOOTER FILE */
require_once("../../include_files/classes/messages.class.php");
$gamer_id = secure_clean($_SESSION['gamer_id']);
$notifications_count = new messages($gamer_id);
$total_notifications = $notifications_count->get_notifications();
?>
<div id="footer">
	<div id="footImg"><a href="contacts.php"><img src="img/contacts.png"></a></div>
	<div id="footImg"><a href="search.php"><img src="img/search.png"></a></div>
	<div id="footImg"><a href="spin.php"><img src="img/roulette.png"></a></div>
	<div id="footImg"><a href="inbox.php"><img src="img/messages.png"></a></div>
	<div id="footImg"><a href="profile.php?i=<?=$gamer_id?>"><img src="img/profile.png"></a></div>
		<!! IF THERE ARE MESSAGES IN THE INBOX THEN LOAD THIS -->
<?php
if ($total_notifications >0){
echo <<<END
	<a href="inbox.php"><div id="littleRedBox">$total_notifications</div></a>
END;
}

?>
</div>
