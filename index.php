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

/* If method is set change API call made. Test is called by default. */
$content = $connection->get('account/verify_credentials');
$contentArray=(array) $content;

$userId=$contentArray['id'];
$realName=$contentArray['name'];
echo $realName."<br>";

//connect to db
$dsn = "pgsql:"
    . "host=ec2-23-21-91-97.compute-1.amazonaws.com;"
    . "dbname=d79rcco6km5fni;"
    . "user=qrcbvngsucryjg;"
    . "port=5432;"
    . "sslmode=require;"
    . "password=iNwOAXNyk0KurrBt9hwyxd_x_D";
$db = new PDO($dsn);

$savedFollowers=array();
//get existing db content
$query = "SELECT followerid FROM followers WHERE userid = '$userId';";
                                  $numberSaved=0;
                                	$result = $db->query($query);
                                	while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                            $numberSaved=$numberSaved+1;
                                            $savedFollowers[]=$row["followerid"];          
                                }
$result->closeCursor();

//get current followers from twitter
$content= $connection->get('followers/ids', array('id' => $userId));
$contentArray=(array) $content;
$currentFollowers=$contentArray['ids'];

echo "Found ".count($savedFollowers)." followers in database.<br>";
echo "You currently have ".count($currentFollowers)." followers.<br>";
echo "<hr>";

//two arrays, one of saved ids and one of current ids
//find the difference, one is new followers and one is no longer following

//in current, not in database
$newFollowers=array_diff($currentFollowers, $savedFollowers);

//in database, not in current
$noLongerFollowers=array_diff($savedFollowers, $currentFollowers);

echo "New followers:<br>";
//get the names of these followers and display
foreach($newFollowers as &$followerId){
//get name given id
$content= $connection->get('users/show', array('id' => $followerId));
$contentArray=(array) $content;
$followerName=$contentArray['name'];
echo $followerName."<br>";

//add these new people to the db
$sql="INSERT INTO followers (userid, followerid) VALUES ('$userId','$followerId')";
$db->query($sql);
}
echo "<hr>";

echo "No longer followers:<br>";
//get the names of these followers and display
foreach($noLongerFollowers as &$followerId){
//get name given id
$content= $connection->get('users/show', array('id' => $followerId));
$contentArray=(array) $content;
$followerName=$contentArray['name'];
echo $followerName."<br>";

//delete these people from db
$sql="DELETE FROM followers WHERE followerid='$followerId' AND userid='$userId'";
$db->query($sql);
}
echo "<hr>";

echo "Saved new followers to database.<br>";
 ?>
 <p>
 <a href="./clearsessions.php">Clear Sessions</a>
</p>
