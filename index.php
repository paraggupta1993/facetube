<?php
require_once('AppInfo.php');
require_once('utils.php');
require_once('libs/facebook.php');
require_once('essential.php');

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
APPID = '161649837292249';
var user_credentials;
var login = 0;
var didFbLogin = 0;
var didTwttrLogin = 0;
var semaphore = 0;
var limit = 50;
var offset = 0;

</script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" ></script>
<link rel="stylesheet" type="text/css" href="mystyle.css"/>
</head>
<body>
<div id="fb-root"></div>
<script src='http://connect.facebook.net/en_US/all.js#xfbml=1&appId=161649837292249' ></script>
<H2> Welcome To Smart-FBing's Facebook Buddy !</H2>

<div class="right">
 <fb:like-box href="https://www.facebook.com/dosomesmartwork" width="250" height="250" show_faces="true" stream="false" header="true"></fb:like-box> 
</div>
<img src="./images/fb_login.png" id="login"/>
<img src="./images/fb_logout.png" id="logout" style="display:none" />


<div id="directions">
<H3>
	Directions for Use of Facebook Buddy:
</H3>
<ul>
	<li> Login with your facebook account to use our Application 'Facetube buddy'</li>	
</ul>

</div>
<div id="notification" style="background-color:red">
</div>
<div class="right">

</div>
<div id="facebook-friends"></div>
<div id="video_links"></div>
<div id="user_groups"></div>
<div id="user_posts"></div>
<div id="moreBtn"></div>

<div id="footer">

<!-- <a href="terms.html">Terms and conditions</a> -->
<H3>HITs: <?php echo $hits ?> </H3>
</div>

<!-- Javascript Functions -->

<script type="text/javascript">
FB.init({
appId      : '161649837292249', // App ID
oauth :true,
cookie     : true, // enable cookies to allow the server to access the session
xfbml      : true // parse XFBML
});

var curr_group;
var playlists = {};

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
				//alert(data);
				$('#notification').empty();
				$('#notification').append('<p> Notification: '+ data );
				
			});
}

function postToken(){
	user = window.user_credentials.authResponse;
	//alert(user.accessToken);
	$.post('autolike.php',
			{user_id: user.userID , token: user.accessToken },
			function(data){
				$('#notification').empty();
				$('#notification').append('<p> Notification: '+ data );
	});
}
var puntil;
var timeint = 5*24*60*60;
var psince;
var plimit;
var poffset;
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
function getgrouphtml( group ){
	html = "<ul class='group_item' id='group_item" + group.id + "'>";
	html += "<li>Name: " + group.name + "</li>"; 
	html += "<li>Id:  <a href='#' class='group_id' id='" + group.id + "'>"+  group.id +"</a></li>";
	html += "</ul>";
	return html;
}
function getpost(){
	//alert( puntil+ " " + psince+ " ");
	FB.api('/me/feed', { since : psince, until : puntil  }, function(response) {
			alert( "Fetched " + response.data.length + "more stories." );
			for (var i=0, l=response.data.length; i<l; i++){
			var fb = response.data[i];
			html = gethtml( fb );
			$('#user_posts').append(html);
			}
			$('.auto_liker_btn').click(processAutoLike);
			puntil = psince;
			psince = psince - (timeint);
			if( response.error ) alert("error");
			});
}
function isvalidlink(link ){
	if( link.type == "video"){
		return true;
	}
	return false;

}
function youtube_parser(url){

	var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/;
	var match = url.match(regExp);
	if (match&&match[2].length==11){
		return match[2];
	}else{
		alert("Cannot extract");
	}
}
function getlinkhtml( link ){
	html = "<ul class='link_item' id='link_item" + link.id + "'>";
	html += (typeof(link.name) != "undefined") ?  "<li>Name: " + link.name + "</li>" : "" ; 
	html += (typeof(link.link) != "undefined" ) ? "<li>Goto: "+ link.link +"</li>" : "";
	html += "</ul>";
	return html;
}

function getlinksfromgrouphelper(event){
		
		$('#video_links').empty();
		plimit =25;
		poffset = 0;
		curr_group = event.target.id;
		if( typeof playlists[curr_group] == 'undefined' ){
			alert("new group created");
			playlists[curr_group] = {};
			playlists[curr_group]['links'] = new Array();
			playlists[curr_group]['poffset'] = 0;
			playlists[curr_group]['plimit'] = 25;
		}
		else{
			alert("Group already present");
		}
		getlinksfromgroup();
}
function renderplaylist( group ) {
	$('#video_links').empty();
	for(i=0;i< group.links.length ;i++){
		linkhtml = getlinkhtml(   group.links[i] );
		$('#video_links').append( linkhtml);
	}
}
function getlinksfromgroup(){
	FB.api('/'+ curr_group +'/feed', { limit: playlists[curr_group][plimit],offset : playlists[curr_group][poffset] }, function(response) {
			alert( "Fetched " + response.data.length );
			for (var i=0, l=response.data.length; i<l; i++){
			var link = response.data[i];
			if( isvalidlink( link )){
			var templinkobj = {};
			templinkobj.name = link.name;
			templinkobj.link = link.link;
			templinkobj.id = link.id;

			playlists[curr_group]['links'].push( templinkobj );
			}
			}
			playlists[curr_group]['poffset'] +=  25;
			playlists[curr_group]['plimit'] += 25;
			renderplaylist( playlists[curr_group]);
			if( response.error ) alert("error");
			});
}

function postlink(){
	FB.api('/me/feed', 'post', {
	message: "Auto-liker for fb !! keep getting ur friend's like on ur status.",
	link : "http://smartfbing.phpfogapp.com"
	}, function(response) {
	  if (!response || response.error) {
	        } else {
		      }
	 });
}
/*
function displayStatus(){
	$.post('autolike.php',
		{user_id: user.userID },
			function(data){
				//alert(data);
				$('#notification').empty();
				$('#notification').append('<p> Notification: '+ data );
				
			});

}*/

function getgroups(){
		FB.api('/me/groups', function(groups) {
			//alert( "Fetched " + groups.data.length + "groups" );
			for (var i=0, l=groups.data.length; i<l; i++){
			var group = groups.data[i];
			grouphtml = getgrouphtml( group );
			$('#user_groups').append(grouphtml);
			}
			$('.group_id').click( getlinksfromgrouphelper );
			if( groups.error ) alert("error");
			});
}
function testfql(){
	FB.api({
	    method: 'fql.query',
	        query: 'SELECT name FROM user WHERE uid = me()'
		}, function(response) {
				console.dir( response );
			        for(i=0;i<response.length;i++)
				             {
						//alert(response[i].name);
					      }
				});

}

function displayUser(){
	//postlink();
	$('#user_posts').append('<H4>Stories from your timeline </H4>');
	$('#moreBtn').append('<a href="#" id="next"> More </a>');
	$('#next').click( function(){ 
			//getpost();
			getlinksfromgroup();
			});
	//puntil = Math.round((new Date()).getTime() / 1000);
	//psince = puntil - (timeint);
	postToken( );
	//getpost();
	testfql();
	getgroups();
}

function fbLoginClicked(){
	FB.getLoginStatus( fbLoginClickedHelper , true );
	FB.login(function(){}, { scope:'publish_stream,read_stream,user_groups'});
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

