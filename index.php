<?php
require_once('AppInfo.php');
require_once('utils.php');
require_once('libs/facebook.php');

?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" lang="en">
<head>
<!--
Facetube_Buddy : It enhances youtube lover's experience from facebook
Copyright (C) 2012  Parag Gupta, Yashasvi Girdhar , Mohit Agarwal

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
This program comes with ABSOLUTELY NO WARRANTY;
This is free software, and you are welcome to redistribute it
under certain conditions.
-->




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
<link rel="stylesheet" type="text/css" href="header.css"> </link>
<link rel="stylesheet" type="text/css" href="canvas.css"></link>
<link rel="stylesheet" type="text/css" href="search_results.css"> </link>
<link rel="stylesheet" type="text/css" href="left_groups.css"></link>
<link rel="stylesheet" type="text/css" href="player_playist.css"></link>


<style type="text/css">
#videoDiv {
	margin-right: 3px;
}
</style>




</head>


<body onload="draw();">
<script> 
var store1="";
</script>
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
	<h2 id="appname">Facebook Buddy!</h2> 
</div>

<div id="content-container"></div>
	<div id="left-bar">
		<div id="video_links"></div>
		<div id="user_groups"></div>
		<div id="moreBtn"></div>
	</div>

	<div id="player">
		<h2>Youtube pLAYer</h2>
		<div id="playerContainer" style="width: 350px; height: 200px;">
    			<object id="youtube-player" style="align:center;"></object>
		</div>
	</div>
	
	<div id="playist">
			<ul id="custom-video">
			</ul>	
	</div>

	<div id="right-bar">
		<h4>rELATED rESULTS</h4>
		<div id="related">
		<div id="videos2"></div>
		</div>	
		<script type="text/javascript" >
			var head= document.getElementById("related");
   		 var script= document.createElement('script');
   		 script.type= 'text/javascript';
   	    id="PqFMFVcCZgI";
   	    script.src= "http://gdata.youtube.com/feeds/api/videos/"+id+"/related?alt=json-in-script&callback=showMyVideos2&max-results=20&format=5";
   		 head.appendChild(script);
		  
		  function search(){      	 
      	 var head= document.getElementById("related");
   		 var script= document.createElement('script');
   		 script.type= 'text/javascript';
   	    script.src= "http://gdata.youtube.com/feeds/api/videos/"+id+"/related?alt=json-in-script&callback=showMyVideos2&max-results=20&format=5";
   		 head.appendChild(script);

	document.getElementById("custom-video").innerHTML = store1;
   		 }
		</script>
	</div>	

</div>

</div>


<div id="facebook-friends"></div>
<div id="user_groups"></div>
<div id="user_posts"></div>

<!-- Playlist/youtube -->
<!--
<div id="videoDiv">Loading...</div>


<div id="yes"></div>
<h4>Video list</h4>
<div id="output"></div>

<h4>User playlist</h4>
<div id="newplaylist"></div>
-->


<!--
<div id="footer">
 <a href="terms.html">Terms and conditions</a> 
<H3>HITs: <?php echo $hits ?> </H3> 
</div>
-->

<!-- Javascript Functions -->

<script type="text/javascript">
/*
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
			*/
function addVideo(event){
	var k;
	for ( k=0;k<customplay.length;k++){
  		if( customplay[k].id == event.target.id ){
			alert("Video already present in custom playlist");
			return;
		}
	}		

	customplay.push({name: event.target.title, id:event.target.id});
        //document.getElementById("newplaylist").innerHTML = customplay[0].id;	
	//for(i=0;i<customplay.length;i++)
		store1 = store1+ '<li id="playvideo"' + 'onclick=loadVideo("http://www.youtube.com/v/'+event.target.id +  '?version=3&f=related&app=youtube_gdata",true)>'+ event.target.title  ;
		store1 = store1 + "<img src='http://img.youtube.com/vi/" +  event.target.id +"/1.jpg'></img></li>";
	//store1=store1+"<li class='links1' id='playvideo' title="+event.target.title+" id="+event.target.id+" >"+event.target.title+"</li>"+"<b id="+event.target.id+" class='remove'>Remove</b>"+ "<br><br>";
	//alert("reached");
	document.getElementById("custom-video").innerHTML = store1;
	//$('.links1').click(loadVideo);
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

var puntil;
var timeint = 5*24*60*60;
var psince;
var plimit;
var poffset;
var MAX = 25;
function getgrouphtml( group ){
	html = "<ul class='group_item' id='group_item" + group.id + "'>";
	//html += "<li>Name: " + group.name + "</li>"; 
	//html += "<li>Id:  <a href='#' class='group_id' id='" + group.id + "'>"+  group.id +"</a></li>";
	html += "<li><a href='#' class='group_id' id='" + group.id + "'>"+ group.name + "</a></li>"; 
	html += "</ul>";
	return html;
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

	html += "<a href='#' title="+link.name+" id="+link.link+" class='add'>Add&nbsp</a>";
	html += "<span class='links' id="+link.link +  ">" + link.name + "</span>"; 
	
	html += "</ul>";
	return html;
}

function getlinksfromgrouphelper(event){
		
		$('#video_links').empty();
		plimit =MAX;
		poffset = 0;
		curr_group = event.target.id;
		if( typeof playlists[curr_group] == 'undefined' ){
		
			playlists[curr_group] = {};
			playlists[curr_group]['links'] = new Array();
			playlists[curr_group]['poffset'] = 0;
			playlists[curr_group]['plimit'] = MAX;
		}
		else{

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
			if( typeof response.data == 'undefined' || response.data.length == 0){
					
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
	message: "Enhance your youtube experience from your facebook",
	link : "http://web.iiit.ac.in/~parag.gupta/facetube/"
	}, function(response) {
	  if (!response || response.error) {
	        } else {
		      }
	 });
}

function getgroups(){
		FB.api('/me/groups', {fields:'name,id'} , function(groups) {
			if( groups.error ) alert("error");
			$('#user_groups').append("<H4> Your Fb Groups</H4>");

			for (var i=0, l=groups.data.length; i<l; i++){
			var group = groups.data[i];
			grouphtml = getgrouphtml( group );
			$('#user_groups').append(grouphtml);
			}
			$('.group_id').click( getlinksfromgrouphelper );
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
	//postToken( );
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
	$('#user_groups').empty();
	$('#video_links').empty();
}
</script>
</body>
</html>

