<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<!--
Copyright Facebook Inc.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
	    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	    See the License for the specific language governing permissions and
	    limitations under the License.
	    -->
	    <head>
	    <meta http-equiv="content-type" content="text/html; charset=utf-8">
	    <title>Connect JavaScript - jQuery Login Example</title>
	    <script src="http://platform.twitter.com/anywhere.js?id=aTSNyOXy7gdt2aHrsrGbg&v=1" type="text/javascript"></script>
	    </head>
	    <body>
	    <h1>Connect JavaScript - jQuery Login Example</h1>
	    <div id='facebookbtn'>
	    <img src="connectFacebook.png" style="display:none" id="login"/>
	    <img src="LogoutFacebook.png" id="logout" style="display:none" />
	    </div>

	    <div id="twttrLogin">
	    
	    <img src="twitterLogoin.png" style="display:none" id="twttr_login_connect"/>
	    <img src="but.png" id="twttr_logout_connect" style="display:none" />
	    
	    </div>
	    <div id="user"> </div>
	    <div id="user-info" style="display: none;"></div>
	    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	    <div id="fb-root"></div>
	    <script src="http://connect.facebook.net/en_US/all.js#xfbml=1&appId=329610143773673"></script>
	    <script>
	    // initialize the library with the API key
	    FB.init({ appId: '329610143773673' , oauth : true});


		var user_credentials;
		var login = 0;
		var didFbLogin = 0;
		var didTwttrLogin = 0;
		var semaphore = 0;
	    $('#login').bind('click', fbLoginClicked );

$('#logout').bind('click',fbLogoutClicked);

			$('#twttr_logout_connect').bind('click',signOutfnc);

			$('#twttr_login_connect').bind('click', twttrLoginfnc);
function fbLoginClicked(){
	    	FB.getLoginStatus( fbLoginClickedHelper , true );
			    FB.login();
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

// no user, clear display
function clearDisplay() {
	$('#user-info').empty();
}
function afterLoggedOut(){
	if( login == 1 ){
	login = 0;
	semaphore = 0;
	$('#logout').hide();
	$('#login').show();
	$('#twttr_login_connect').show();
	$('#twttr_logout_connect').hide();
	clearDisplay();
	FB.XFBML.parse();
	}
}
function afterLoggedIn(response){
	if( login == 0){
	login = 1;
	semaphore = 1;
	$('#twttr_login_connect').hide();
	$('#twttr_logout_connect').hide();
	$('#login').hide();
	$('#logout').show();
	displayUser();
	FB.XFBML.parse();
	}
}
FB.Event.subscribe("auth.logout",handleSessionResponse );
FB.Event.subscribe("auth.login", handleSessionResponse );

// handle a session response from any of the auth related calls
function handleSessionResponse(response) {
	// if we dont have a session, just hide the user info
	if( response.status == 'connected'){
		window.user_credentials = response;
		alert("connected");
		afterLoggedIn();
	}
	else if ( response.status == 'not_authorized'){
		alert("not-authorized");
		afterLoggedOut();
	}
	else {
		alert("not logged in");
		afterLoggedOut();
	}
}


function displayUser(){
	// if we have a session, query for the user's profile picture and name
	FB.api(
			{
method: 'fql.query',
query: 'SELECT name, pic FROM profile WHERE id=' + window.user_credentials.authResponse.userID
},
function(response) {
var user = response[0];
$('#user-info').html('<img src="' + user.pic + '">' + user.name).show('fast');
}
);
}
function twttrDisplayUser(T){
	//alert("twttrDisplayUser");
	var user,name,pic;
	user = T.currentUser;
	name = user.data('screen_name');
	pic = user.data('profile_image_url');
	img = "<img src='" + pic +"'/>";
	$('#user').append("User: " + name + "</br>" + img );
}
function twttrClearUser(){
	//alert("twttrClearUser");
	$('#user').empty();
}
function twttrLoginfnc(){

	twttr.anywhere( function(T) {T.signIn();});
}
function signOutfnc(){
	twttr.anywhere.signOut();
}
twttr.anywhere(function (T) {
		T.bind("authComplete", function (e, user) {
			// triggered when auth completed successfully
			$('#login').hide();
			$('#logout').hide();
			$('#twttr_login_connect').hide();
			$('#twttr_logout_connect').show();
			twttrDisplayUser(T);
			});

		T.bind("signOut", function (e) {
			// triggered when user logs out
			$('#twttr_logout_connect').hide();
			$('#logout').hide();
			$('#login').show();
			$('#twttr_login_connect').show();
			twttrClearUser();
			});

		//checing initial status.
		if( semaphore == 0){
		if( T.isConnected()){
			$('#login').hide();
			$('#logout').hide();
			$('#twttr_login_connect').hide();
			$('#twttr_logout_connect').show();
			twttrDisplayUser(T);
		}
		else{
			$('#logout').hide();
			$('#login').show();
			$('#twttr_logout_connect').hide();
			$('#twttr_login_connect').show();
		}
		}
}
);


</script>
</body>
</html>
