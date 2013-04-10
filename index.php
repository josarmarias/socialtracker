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
echo $userId."<br>";
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

$savedFollowers=[];
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
$followerIds=$contentArray['ids'];

echo "Found x followers in database.";
echo "You currently have y followers.";

//two arrays, one of saved ids and one of current ids
//find the difference, one is new followers and one is no longer following


echo "New followers:<br>";

echo "No longer followers:<br>";

//get the names of these followers and display
foreach($followerIds as &$followerId){
//get name given id
$content= $connection->get('users/show', array('id' => $followerId));
$contentArray=(array) $content;
$followerName=$contentArray['name'];
echo $followerName."<br>";
}

//dump old db values and add new ones
//$sql="INSERT INTO entries (fbid, class) VALUES ('$_POST[fbid]','$subjectAndSection')";
// Performs the $sql query on the server to insert the values
//$db->query($sql);
 ?>






 <p>
 <a href="./clearsessions.php">Clear Sessions</a>
</p>
