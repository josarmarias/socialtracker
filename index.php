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
$realName=$contentArray['name'];
echo $userId."<br>";
echo $realName."<br>";

$content= $connection->get('followers/ids', array('id' => $userId));
$contentArray=(array) $content;

$followerIds=$contentArray['ids'];
foreach($followerIds as &$followerId){
echo $followerId."<br>";
//get name given id
$content= $connection->get('users/show', array('id' => $followerId));
$contentArray=(array) $content;
$followerName=$contentArray['name'];
echo $followerName."<br>";
}

 ?>

<p>
Found x followers in database.
You currently have y followers.
</p>

<p>
New followers:
</p>

<p>
No longer followers:
</p>

 <p>
 <a href="./clearsessions.php">Clear Sessions</a>
</p>
