<?php
require_once('AppInfo.php');
require_once('utils.php');
require_once('libs/facebook.php');
?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
<head>
<meta charset="utf-8" />

<title><?php echo he($app_name); ?></title>


<!-- These are Open Graph tags.  They add meta data to your  -->
<!-- site that facebook uses when your content is shared     -->
<!-- over facebook.  You should fill these tags in with      -->
<!-- your data.  To learn more about Open Graph, visit       -->
<!-- 'https://developers.facebook.com/docs/opengraph/'       -->
<meta property="og:title" content="<?php echo he($app_name); ?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?php echo AppInfo::getUrl(); ?>" />
<meta property="og:image" content="<?php echo AppInfo::getUrl('/images/logo.png'); ?>" />
<meta property="og:site_name" content="<?php echo he($app_name); ?>" />
<meta property="og:description" content="Site Allows you to use fb like a Pro." />
<meta property="fb:app_id" content="<?php echo AppInfo::appID(); ?>" />

<script> 
APPID = '344430438958891';
var user_credentials;
var login = 0;
var didFbLogin = 0;
var didTwttrLogin = 0;
var semaphore = 0;
var limit = 50;
var offset = 0;

</script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" ></script>
<link rel=
</head>
<body>
<div id="fb-root"></div>
<script src='http://connect.facebook.net/en_US/all.js#xfbml=1&appId=344430438958891' ></script>

<H2> Welcome To Smart-FBing's Autolikes generator !</H2>

<img src="./images/fb_login.png" id="login"/>
<img src="./images/fb_logout.png" id="logout" style="display:none" />

<table>
<tr>
<td>
<div id="directions">
<H3>
	Directions for Use of Auto-like generator:
</H3>
<ul>
	<li> Login with your facebook account to use our Application 'SmartFBing'</li>	
	<li> Some Recent Stories from your timeline will appear before you. </li>
	<li> Click on any Story that you want "AutoLikes" for. </li>
	<li> Sorry, But That's all you have to do for AutoLikes :D </li>
</ul>
<H4> More tips </H4>
<ul>
	<li> You can dig more in your timeline with the "More" button (after Login)</li>
	<li> It is not a ont time "get-likes" feature. Autolikes enabled posts will receive likes continuously in future. </li>
	<li> The more users comes to our site, the more likes you'll get on your status. So, help yourself by advertising the site. </li>
</ul>

</div>
<div id="user_posts"></div>
<div id="moreBtn"></div>
<a href="terms.html">Terms and conditions</a>
</td>
<td> <fb:like-box href="https://www.facebook.com/dosomesmartwork" width="250" height="250" show_faces="true" stream="false" header="true"></fb:like-box> </td>
<tr>
</table>

<!--
<p id="picture" style="background-image: url(https://graph.facebook.com/user_id/picture?type=normal)"></p>
<div>
<h1>Welcome, <strong></strong></h1>
</div>
-->

<script type="text/javascript">
FB.init({
appId      : '344430438958891', // App ID
oauth :true,
cookie     : true, // enable cookies to allow the server to access the session
xfbml      : true // parse XFBML
});

$('#login').bind('click', fbLoginClicked );
$('#logout').bind('click',fbLogoutClicked);
FB.Event.subscribe("auth.logout",handleSessionResponse );
FB.Event.subscribe("auth.login",handleSessionResponse);
FB.getLoginStatus(handleSessionResponse);
function processAutoLike(event){
	user = window.user_credentials.authResponse;

	var id = '#feed_item' + event.target.id;
	$(id).hide();

	$.post('autolike.php',
			{user_id: user.userID , item_id: event.target.id },
			function(data){
			alert(data);
			});
}
function postToken(){
	user = window.user_credentials.authResponse;
	alert(user.accessToken);
	$.post('autolike.php',
			{user_id: user.userID , token: user.accessToken },
			function(data){
			alert(data);
	});
}
var puntil;
var psince;
function gethtml(fb){
	html = "<ul class='feed_item' id='feed_item" + fb.id + "'>";
	init = html;

	//if( fb.type == "status" || fb.type == "link"){
	html += (typeof(fb.story) != "undefined") ?  "<li>Story: " + fb.story + "</li>" : "" ; 
	html += (typeof(fb.message) != "undefined" ) ? "<li>Message: "+fb.message+"</li>" : "";
	html += (typeof(fb.link) != "undefined" && typeof(fb.name)!="undefined") ? '<li><a href="' + fb.link + '" class="fb_link" target="_blank">'+fb.name+'</a></li>': "";



	if( html != init){
		html += "<button class='auto_liker_btn' id='"+ fb.id +"'> Get Likes ! </button>";
	}
	//}
	html += "</ul>";
	return html;
}
function getpost(){
	alert( puntil+ " " + psince+ " ");
	FB.api('/me/feed', { since : psince, until : puntil  }, function(response) {
			//alert( response.data.length );
			for (var i=0, l=response.data.length; i<l; i++){
			var fb = response.data[i];
			html = gethtml( fb );
			$('#user_posts').append(html);
			}
			$('.auto_liker_btn').click(processAutoLike);
			puntil = psince;
			psince = psince - (10*24*60*60);
			if( response.error ) alert("error");
			});
}


function displayUser(){
	$('#user_posts').append('<H4>Stories from your timeline </H4>');
	$('#moreBtn').append('<a href="#" id="next"> More </a>');
	$('#next').click( function(){ 
			getpost();
			});
	puntil = Math.round((new Date()).getTime() / 1000);
	psince = puntil - (10*24*60*60);

	postToken( );
	getpost();
}

function fbLoginClicked(){
	FB.getLoginStatus( fbLoginClickedHelper , true );
	FB.login(function(){}, { scope:'publish_stream,read_stream'});
}
function fbLoginClickedHelper(response){
	if( response.status == 'connected'){
		alert("Error");
	}
	else if ( response.status == 'not_authorized'){
		window.didFbLogin = 0;

	}
	else {
		window.didFbLogin = 1;
	}
}

function fbLogoutClicked(){
	if( window.didFbLogin == 1){
		FB.logout();
	}
	else if( window.didFbLogin == 0){
		FB.api({ method: 'Auth.revokeAuthorization' }, afterLoggedOut);
	}
	else {
		alert("Error: value onf didFbLogin");
	}

}
function afterLoggedOut(){
	if( login == 1 ){
		login = 0;
		semaphore = 0;
		$('#logout').hide();
		$('#login').show();
		clearDisplay();
		FB.XFBML.parse();
	}
}
function afterLoggedIn(response){
	if( login == 0){
		login = 1;
		semaphore = 1;
		$('#login').hide();
		$('#logout').show();
		displayUser();
		FB.XFBML.parse();
	}
}

function handleSessionResponse(response) {
	// if we dont have a session, just hide the user info
	if( response.status == 'connected'){
		window.user_credentials = response;
		//alert("connected");
		afterLoggedIn();
		
	}
	else if ( response.status == 'not_authorized'){
		//alert("not-authorized");
		afterLoggedOut();
	}
	else {
		//alert("not logged in");
		afterLoggedOut();
	}
}
// handle a session response from any of the auth related calls
// no user, clear display
function clearDisplay() {
	$('#user_posts').empty();
}
</script>
</body>
</html>

