<?php
/**
 * @file
 * User has successfully authenticated with Twitter. Access tokens saved to session and DB.
 */

/* Load required lib files. */
session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.php');

/* If access tokens are not available redirect to connect page. */
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: ./clearsessions.php');
}
/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);


//Do the DB connection here

/* If method is set change API call made. Test is called by default. */
$content = $connection->get('account/verify_credentials');
$contentArray=(array) $content;

$userId=$contentArray['id'];
$userName=$contentArray['screen_name'];
$realName=$contentArray['name'];
echo $userId."<br>";
echo $userName."<br>";
echo $realName."<br>";

$content= $connection->get('followers/ids', array('id' => $userId));
$contentArray=(array) $content;

$followerIds=$contentArray['ids'];
foreach($followerIds as &$follower){
echo $follower."<br>";
}
 ?>
 
 <pre>
 New followers:
</pre>

<pre>
 No longer followers:
</pre>

 <p>
 <a href="./clearsessions.php">Clear Sessions</a>
</p>
