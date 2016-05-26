<?php
require("class.phpmailer.php");
$mail = new PHPMailer();
$mail-> IsSMTP();
$mail->Host = "express-smtp.cites.uiuc.edu";
$mail->Port = 25; //587 for gmail
//$mail->SMTPAuth = true;
//$mail->SMTPSecure = 'tls';
//$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only

$Shareinfofirst_name = $_POST['ShareInfofirst_name'];
$ShareInfolast_name = $_POST['ShareInfolast_name'];
$ShareInfoartifact = $_POST['ShareInfoartifact'];
$ShareInfocomments = $_POST['ShareInfocomments'];
$ShareInfoemail = $_POST['ShareInfoemail'];
$formtime = $_POST['formtime'];
$AmIHuman = $_POST['AmIHuman'];
if((empty($_POST['ShareInfoemail'])) || (empty($_POST['ShareInfocomments'])) || (empty($_POST['ShareInfofirst_name'])) || (empty($_POST['ShareInfolast_name'])) 
|| (!empty($_POST['AmIHuman'])) || (empty($_POST['formtime'])) || ($_POST['formtime'] > time()-7) || (!filter_var($ShareInfoemail, FILTER_VALIDATE_EMAIL))){
header('Location:  /search/messageNotSent.php');
die();

}
//$mail->Username = 'USERNAME';
//$mail->Password = 'PASSWORD';
//$mail->From = "$ShareInfoemail";
//$mail->FromName = "$ShareInfofirst_name";
$mail->SetFrom('noreply@illinois.edu', 'Spurlock Search');
$mail->addAddress('jenwhite@illinois.edu', 'Jennifer White'); // Replace this email and name for testing. jenwhite@illinois.edu

$mail->Subject = "Suggested info: $ShareInfoartifact";
$mail->Body = "<h2>User-Suggested Artifact Information</h2><br>Contributor Name: $Shareinfofirst_name $ShareInfolast_name <br>Contributor E-mail: $ShareInfoemail <br><br>Artifact: $ShareInfoartifact <br>Comments:<br> $ShareInfocomments";
$mail->IsHTML(true);

//$mail->SMTPSecure = 'ssl';
//START code for email reply to user
$mail2 = new PHPMailer();
$mail2-> IsSMTP();
$mail2->Host = "express-smtp.cites.uiuc.edu";
$mail2->Port = 25; //587 for gmail
$mail2->SetFrom('noreply@illinois.edu', 'Spurlock Search');
$mail2->addAddress($ShareInfoemail, 'Site User');
$mail2->Subject = "Spurlock Museum Reply";
$mail2->Body = "Thank you for sharing what you know!  All public comments are reviewed by Museum staff and used at their professional discretion.  We appreciate your support.<br><br> Sincerely,<br> Spurlock Museum Staff<br><br>Your Submission:<br> $ShareInfocomments";
$mail2->IsHTML(true);
if(!$mail2->Send())
{
echo "Message did not send <p>";
//echo "Mailer Error: " . $mail2->ErrorInfo;
header('Location:  /search/messageNotSent.php');
exit;
}

echo "Message has been sent";
if(!$mail->Send())
{
echo "Message did not send <p>";
header('Location:  /search/messageNotSent.php');
//echo "Mailer Error: " . $mail->ErrorInfo;
exit;
}

echo "Message has been sent";

header('Location:  /search/messageconfirmation.php');


?>
