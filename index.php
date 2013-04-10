<?php
require_once ('src/codebird.php');
Codebird::setConsumerKey('j1wLvQtdXPOaWN4HJVwKw', 'JJ0ahgla4s99b9h5GLVme0IeKkw3tOqT4fcp22F1Q'); // static, see 'Using multiple Codebird instances'

$cb = Codebird::getInstance();

session_start();

if (! isset($_GET['oauth_verifier'])) {
    // gets a request token
    $reply = $cb->oauth_requestToken(array(
        'oauth_callback' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
    ));

    // stores it
    $cb->setToken($reply->oauth_token, $reply->oauth_token_secret);
    $_SESSION['oauth_token'] = $reply->oauth_token;
    $_SESSION['oauth_token_secret'] = $reply->oauth_token_secret;

    // gets the authorize screen URL
    $auth_url = $cb->oauth_authorize();
    header('Location: ' . $auth_url);
    die();

} else {
    // gets the access token
    $cb->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
    $reply = $cb->oauth_accessToken(array(
        'oauth_verifier' => $_GET['oauth_verifier']
    ));
    // store the authenticated token, which may be different from the request token (!)
    $_SESSION['oauth_token'] = $reply->oauth_token;
    $_SESSION['oauth_token_secret'] = $reply->oauth_token_secret;
    $userId=$_SESSION['user_id'];
    echo $userId;
    echo $_SESSION['screen_name'];
}

$followers=(array) $cb->followers_ids($userId);
var_dump($followers);
?>