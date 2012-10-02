<?php
/**
 * Copyright 2011 Facebook, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require 'libs/facebook.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
			'appId'  => '262936577088527',
			'secret' => '086f870021e3ba3b5362b99db60c7ab9',
			));

$user = $facebook->getUser();

if($user){
	try{
		$user_profile = $facebook->api('/me');
		/*$feed = $facebook->api('me/feed?limit=1');
		  foreach($feed['data'] as $status){
		  try{
		  $q = '/' . $status['id'] . "/comments";
		  echo "$q </br>";
		  $facebook->api($q , 'post', array( 'message' => 'Hey this is a now getting ..........'));
		  }catch (FacebookApiException $e){
		  echo "exception: $e </br>";
		  echo "type :" . $e->getType() . "</br>";
		  echo "type :" . $e->getMessage() . "</br>";

		  error_log($e);
		  }
		  }


		  echo "hmmmm </br>";
		 */
		if(isset($_REQUEST['statusbar']) && $_REQUEST['statusbar']!=null && $_REQUEST['statusbar']!=''){
			$facebook->api('/me/feed','post',
					array('message' => $_REQUEST['statusbar'])
				      );
		}
		$like = 0;
		if(isset($_REQUEST['like'])){
			//print "liked";
			$like = 1;
		}
		
		if(isset($_REQUEST['comment'])){
			$msg = $_REQUEST['msg'];
			$limit = $_REQUEST['limit'];
			$timestamp = $_REQUEST['timestamp'];

			$query = "/me/feed";
			if($limit !='' && $limit!=null){
				$query .= "?limit=" . $limit;
			}
			if($timestamp !='' && $timestamp!=null){
				$query .= "?since=". strtotime($timestamp);
			}
			$query .= "&limit=10000";
			$feed = $facebook->api($query);
			//echo "$query";
			print sizeof($feed['data']);
			foreach($feed['data'] as $status){
				try{
					if($status['from']['id']!=$user_profile['id']){
						$already_commented=0;
						if($like>=1){
							print "Liked " . $like ;
							$q = '/' . $status['id'] . "/likes";
							$facebook->api($q , 'post');
						}

						foreach($status['comments']['data'] as $comment){
							try{
								if($comment['from']['id'] == $user_profile['id']){
									$already_commented=1;
									break;
								}
							}catch (FacebookApiException $e){
								echo "Inner exception $e";
								error_log($e);
							}
						}
						if(!$already_commented){
							//commenting 
							$q = '/' . $status['id'] . "/comments";
							$facebook->api($q , 'post', array('message'=>$msg));
							
							
							echo "<pre>" ;
							print_r($status);
							echo "</pre>";
							echo "exception not coming";
						}
					}
				}catch (FacebookApiException $e){
					echo "Outer exception $e";
					error_log($e);
				}
			}
		}

	} catch (FacebookApiException $e) {
		echo "OuterMost exception";
		error_log($e);
		$user = null;
	}
}

// Login or logout url will be needed depending on current user state.
if ($user) {
	$logoutUrl = $facebook->getLogoutUrl();
} else {
	$loginUrl = $facebook->getLoginUrl(
			array(
				'scope' => 'read_stream,publish_stream'
			     ));
}


// This call will always work since we are fetching public data.

?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<title>php-sdk</title>
<style>
body {
	font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
}
h1 a {
	text-decoration: none;
color: #3b5998;
}
h1 a:hover {
	text-decoration: underline;
}
</style>
</head>
<body>
<h1>php-sdk</h1>

<?php if ($user): ?>
<a href="<?php echo $logoutUrl; ?>">Logout</a>
<?php else: ?>
<div>
Login using OAuth 2.0 handled by the PHP SDK:
<a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
</div>
<?php endif ?>

<h3>PHP Session</h3>
<form name='status' action='<?php echo $PHP_SELF ?>' method='GET'>
<input type='text' name='statusbar'/>
<input type='submit' value='update'/>
<form>
</br>
<form name='comment' action='<?php echo $PHP_SELF ?>' method='GET'>
comment : <input type='text' name='msg'/>
limit : <input type='text' name='limit'/>
timestamp : <input type='text' name='timestamp'/>
Like : <input type='checkbox' name='like' value='yes'/>
<input type='submit' name='comment' value='comment'/>
<form>


<?php if ($user): ?>
<pre><?php //print_r($user_profile);
?></pre>
<h3>You</h3>
<img src="https://graph.facebook.com/<?php echo $user; ?>/picture">

<?php else: ?>
<strong><em>You are not Connected.</em></strong>
<?php endif ?>

</body>
</html>
