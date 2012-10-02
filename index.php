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
<meta property="og:description" content="Site Allows you to integrate fb with youtube." />
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
<script src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load("swfobject", "2.1");
</script>

<!-- front -end -->
<script type="text/javascript" src="search_script.js"></script>
<script type="text/javascript" src="design.js"></script>
<script type="text/javascript" src="http://swfobject.googlecode.com/svn/trunk/swfobject/swfobject.js"></script>
<link rel="stylesheet" type="text/css" href="design.css"> </link>
<link rel="stylesheet" type="text/css" href="design2.css"></link>
<link rel="stylesheet" type="text/css" href="search_results.css"> </link>
<link rel="stylesheet" type="text/css" href="left_groups.css"></link>


<style type="text/css">
#videoDiv {
	margin-right: 3px;
}
</style>









</head>


<body onload="draw();">

<canvas id="canvas"> canvas element </canvas>

<div id="fb-root"></div>
<script src='http://connect.facebook.net/en_US/all.js#xfbml=1&appId=161649837292249' ></script>

<!--
<div class="right">
 <fb:like-box href="https://www.facebook.com/dosomesmartwork" width="250" height="250" show_faces="true" stream="false" header="true"></fb:like-box> 
</div>
-->

<img src="./images/fb_login.png" id="login"/>
<img src="./images/fb_logout.png" id="logout" style="display:none" />

<div id="notification" style="background-color:red">
</div>


<!-- Front -->
<div id="container">
<div id="header">
	<h2 id="appname">pOkERfACE..yeah !</h2> 
	<div id="navigation">
		<ul>
			<li><a href="#">Home</a></li>
			<li><a href="#">About</a></li>
			<li><a href="#">Download</a></li>
			<li><a href="#">Contact us</a></li>
		</ul>
	</div>
</div>

<div id="content-container"></div>
	<div id="left-bar">
		<div id="video_links"></div>
		<div id="user_groups"></div>
		<div id="moreBtn"></div>
	</div>

	<div id="player">
		<h2>Youtube pLAYer</h2>
		<div id="playerContainer" style="width: 20em; height: 250px; float: left;">
    			<object id="youtube-player" style="float:left;"></object>
		</div>
		<div id="playist">
			<ul>
				<li id="song"> song1</li>
				<li id="song"> song2</li>

			</ul>		
		</div>
	</div>	
	
	<!-- <div id="right-bar">
		<h3>search results for the playing video</h3>
		<div id="related">
		<div id="videos2"></div>
	-->	<!--<script 
   		 type="text/javascript" 
    		src="http://gdata.youtube.com/feeds/api/videos/PqFMFVcCZgI/related?alt=json-in-script&callback=showMyVideos2&max-results=20&format=5";>
		</script>-->
	<!--		</div>	
		<script type="text/javascript" >
       var head= document.getElementById("related");
   var script= document.createElement('script');
   script.type= 'text/javascript';
   var id="PqFMFVcCZgI";
   script.src= "http://gdata.youtube.com/feeds/api/videos/"+id+"/related?alt=json-in-script&callback=showMyVideos2&max-results=20&format=5";
   head.appendChild(script);
</script>
	</div>	
 	-->
</div>

</div>


<div id="facebook-friends"></div>
<div id="user_groups"></div>
<div id="user_posts"></div>

<!-- Playlist/youtube -->
<div id="videoDiv">Loading...</div>


<div id="yes"></div>
<h4>Video list</h4>
<div id="output"></div>

<h4>User playlist</h4>
<div id="newplaylist"></div>




<div id="footer">
<!-- <a href="terms.html">Terms and conditions</a> 
<H3>HITs: <?php echo $hits ?> </H3> -->
</div>

<!-- Javascript Functions -->

<script type="text/javascript">
function loadVideo(event) {
	$("#yes").before("<div id='videoDiv'>hello world</div>");
		swfobject.removeSWF("ytPlayer");
	var params = { allowScriptAccess: "always" };
	var atts = { id: "ytPlayer" };
        var videoID=event.target.id;
	// All of /the magic handled by SWFObject (http://code.google.com/p/swfobject/)
	swfobject.embedSWF("http://www.youtube.com/v/" + videoID +
			"?version=3&enablejsapi=1&playerapiid=player1",
			"videoDiv", "480", "295", "9", null, null, params, atts);
			}

var store1="";
function addVideo(event){
	customplay.push({name: event.target.title, id:event.target.id});
        //document.getElementById("newplaylist").innerHTML = customplay[0].id;	
	//for(i=0;i<customplay.length;i++)
	store1=store1+"<li class='links1' title="+event.target.title+" id="+event.target.id+" >"+event.target.title+"</li>"+"<b id="+event.target.id+" class='remove'>Remove</b>"+ "<br><br>";
	//alert("reached");
	document.getElementById("newplaylist").innerHTML = store1;
	$('.links1').click(loadVideo);
	//$('.remove').click(removevideo);
			}



FB.init({
appId      : '161649837292249', // App ID
oauth :true,
cookie     : true, // enable cookies to allow the server to access the session
xfbml      : true // parse XFBML
});

var curr_group;
var playlists = {};
var customplay = new Array();
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
var MAX = 25;
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
	if( link.type == "video" && is_youtube_url(link.link) ){
		return true;
	}
	return false;
}
function is_youtube_url(url){
	var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/;
	var match = url.match(regExp);
	if (match&&match[2].length==11){
		return true;
	}else{
		return false;
	}
}
function youtube_parser(url){
	var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/;
	var match = url.match(regExp);
	if (match&&match[2].length==11){
		return match[2];
	}else{
		alert("Cannot extract id from link");
	}
}
function getlinkhtml( link ){
	html = "<ul class='link_item' id='link_item" + link.id + "'>";
	html += "<li class='links' id="+link.link +">Name: " + link.name + "</li>"; 

	html += "<b title="+link.name+" id="+link.link+" class='add'>Add</b>";
	html += "<li>Goto: "+ link.link +"</li>";
	html += "<li>likes: "+ link.count +"</li>";
	html += "</ul>";
	return html;
}

function getlinksfromgrouphelper(event){
		
		$('#video_links').empty();
		plimit =MAX;
		poffset = 0;
		curr_group = event.target.id;
		if( typeof playlists[curr_group] == 'undefined' ){
			alert("new group created");
			playlists[curr_group] = {};
			playlists[curr_group]['links'] = new Array();
			playlists[curr_group]['poffset'] = 0;
			playlists[curr_group]['plimit'] = MAX;
		}
		else{
			alert("Group already present");
		}
		getlinksfromgroup();
}
var order_by = 'least_liked';
function orderby(playlist){
	if( order_by == 'asc'){
	playlist.sort( function(a,b){
		return b.time - a.time	;
	});
	 }
	else if( order_by == 'desc'){
		playlist.sort( function(a,b){
			return a.time - b.time;
		});
	 }
	else if( order_by == 'most_liked'){
		playlist.sort( function(a,b){
			return b.count - a.count;
		});
	}
	else if( order_by == 'least_liked'){
		playlist.sort( function(a,b){
			return a.count - b.count;
		});
	}
}
function renderplaylist( playlist ){
	orderby( playlist );
	$('#video_links').empty();
	for(i=0;i< playlist.length ;i++){
		linkhtml = getlinkhtml(   playlist[i] );
		$('#video_links').append( linkhtml);

	}
	$('.links').click(loadVideo);
	$('.add').click(addVideo);
}

function getlinksfromgroup(){
	pre_len = playlists[curr_group]['links'].length;
	FB.api('/'+ curr_group +'/feed', { limit: playlists[curr_group]['plimit'],offset : playlists[curr_group]['poffset'], fields:'id,likes,name,link,type,updated_time' }, function(response) {
			//alert( "Fetched " + response.data.length );
			if( response.data.length == 0){
					
			renderplaylist( playlists[curr_group].links);
				return ;
			}
			for (var i=0, l=response.data.length; i<l; i++){
			var link = response.data[i];
			console.log(link);
			if( isvalidlink( link )){
			  var templinkobj = {};
			  templinkobj.name = link.name;
			templinkobj.link = youtube_parser(link.link);
			templinkobj.id = link.id;
			templinkobj.time = Date.parse(link.updated_time)/1000;
			if( typeof link.likes != 'undefined' ) templinkobj.count = link.likes.count;
			else templinkobj.count = 0;
			

			playlists[curr_group]['links'].push( templinkobj );
			}
			}
			playlists[curr_group]['poffset'] +=  MAX;
			playlists[curr_group]['plimit'] += MAX;
			len = playlists[curr_group]['links'].length;
			if( len - pre_len < 25 ) {
				getlinksfromgroup();
			}
			renderplaylist( playlists[curr_group].links);
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
		FB.api('/me/groups', {fields:'name,id'} , function(groups) {
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
	        //query: 'SELECT name FROM user WHERE uid = me()'
	        query: 'SELECT post_id, actor_id, target_id, attachment , message FROM stream WHERE source_id ="327559000672561" LIMIT 50'
		}, function(response) {
				console.log( response );
			        for(i=0;i<response.length;i++)
				             {
						//alert(response[i].name);
					      }
				});
}

function displayUser(){
	//postlink();
	$('#moreBtn').append('<a href="#" id="next"> More </a>');
	$('#next').click( function(){ 
			//getpost();
			getlinksfromgroup();
	});
	//puntil = Math.round((new Date()).getTime() / 1000);
	//psince = puntil - (timeint);
	postToken( );
	//getpost();
	//testfql();
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

