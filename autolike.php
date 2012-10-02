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

function clearToken( $user_id){
	$sql = "UPDATE fbUser set token=NULL where user_id='".$user_id."';";
	execute( $sql );
	//echo "Token Cleared";
}

function getLikesOnItem( $item_id ){
	GLOBAL $facebook;
	$sql = "SELECT user_id,token from fbUser";
	$tokens = execute( $sql);
	while( $row = mysql_fetch_array($tokens)){
		if( $row['token'] != NULL ){

			$post = array( 
					'access_token' => $row['token'],
					'limit' => 1
				     );
			$url = '/'.$row['user_id'] . '/feed' ;
			$flag = 0;
			try{
				$test = $facebook->api($url,'GET',$post);
			}
			catch( FacebookApiException $e){
				clearToken($row['user_id']);
				$flag = 1;
			}
			$post = array( 
					'access_token' => $row['token']
				     );
			if( $flag==0 ){
				$url = '/'.$item_id.'/likes';
				try{
					$a = $facebook->api($url,'POST',$post);
				}
				catch(FacebookApiException $e){
				}
			}
		}
	}
}
function insertUser( $user_id ){
	$sql = "INSERT INTO fbUser (user_id, autoLikes) VALUES ('".$user_id ."', 0 );";
	execute($sql);
}
function isUser( $user_id ){
	$sql = "SELECT * from fbUser WHERE user_id='" . $user_id . "';";
	$userexists = execute( $sql );
	if( mysql_num_rows($userexists) == 1){
		return (bool)TRUE;
	}
	return (bool)FALSE;
}
function validquota( $user_id ){
	GLOBAL $notification;
	$fetchAutoLikes = execute("SELECT autoLikes,maxAutoLikes from fbUser WHERE user_id='".$user_id ."';");
	while( $row = mysql_fetch_array( $fetchAutoLikes )){
		if( $row['autoLikes'] >= $row['maxAutoLikes'] ){
			die("Max quota for Autolikes reached.");
		}else{
			return $row['autoLikes'];
		}
	}
}
function insertItem( $user_id, $item_id  ){
	$sql = "INSERT INTO item (item_id ,user_id) VALUES ('".$item_id ."','".$user_id . "' );" ;
	if ( !ex( $sql )){
		die("Already Autoliked !! or some error occured !");
	}
}
function incrementAutoLikes( $user_id , $autoLikes ){
	$sql = "UPDATE fbUser set autoLikes=".$autoLikes." WHERE user_id='". $user_id . "';";
	execute($sql);
}
if(isset( $_REQUEST['user_id'] ) &&  isset($_REQUEST['item_id'])){
	dbconnect();
	$user_id = $_REQUEST['user_id'];
	$item_id = $_REQUEST['item_id'];
	$autolikes = 0;
	if( isUser($user_id) ) {
		$autoLikes = validquota( $user_id);
	}
	else{
		insertUser( $user_id );
	}

	insertItem( $user_id , $item_id );
	getLikesOnItem( $item_id);
	incrementAutoLikes( $user_id , $autoLikes + 1);
	mysql_close($con);
}
function updateToken( $user_id , $token_id ){
	$sql = "UPDATE fbUser set token='".$token_id."' WHERE user_id='".$user_id ."';";
	execute( $sql );
}
function useToken( $token ){
	//echo "use Token";
	GLOBAL $facebook;
	$sql = "SELECT item_id from item";
	$items = execute($sql);
	$post = array(
		'access_token' => $token
	);
	while( $item = mysql_fetch_array( $items )){
		$url = '/' . $item['item_id'] . '/likes';
		try{
		$id = $facebook->api($url,'POST',$post);
		}
		catch(FacebookApiException $e){
		//echo "exception ";
		}
		//echo "$url $id";
	}
}
if( isset( $_REQUEST['token']) && $_REQUEST['token']!='' && isset( $_REQUEST['user_id'] ) && $_REQUEST['user_id']!='' ){
	dbconnect();
	$user_id = $_REQUEST['user_id'];
	$token = $_REQUEST['token'];
	if( !isUser( $user_id )) insertUser( $user_id );
	updateToken( $user_id, $token );
	useToken( $token );
	mysql_close($con);
}
?>
