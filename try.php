<?php
require_once('AppInfo.php');
require_once('utils.php');
require_once('libs/facebook.php');
require_once('essential.php');
$notification = '';
$facebook = new Facebook(array(
  'appId'  => AppInfo::appID(),
  'secret' => AppInfo::appSecret(),
));
$post = array(
'limit' => 1,
'access_token' => 'AAAE5QfX2xysBAFQ3vXZAUiZCP7ZCcvDKKML0TA87q2ZBZCbP5RfWSwZAUAJLpyyvaIdaOEbBy8ZC5Jsowoq2fzayUPWHTxZAsQKzBbQ7dcoiBuxquHd01m7i');
try{
$a = $facebook->api('/me/feed','GET',$post);
}
catch( FacebookApiException $e){
	echo "profile need to be deleted";
}

?>
