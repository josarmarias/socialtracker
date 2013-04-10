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
}

$reply = (array) $cb->statuses_homeTimeline();
echo $reply;

params = array(
    'screen_name' => 'mynetx'
);
$reply = $cb->users_show($params);
echo $reply;
//cast returns to arrays

/*
3. Mapping API methods to Codebird function calls
-------------------------------------------------

As you can see from the last example, there is a general way how Twitter’s API methods
map to Codebird function calls. The general rules are:

1. For each slash in a Twitter API method, use an underscore in the Codebird function.

    Example: ```statuses/update``` maps to ```Codebird::statuses_update()```.

2. For each underscore in a Twitter API method, use camelCase in the Codebird function.

    Example: ```statuses/home_timeline``` maps to ```Codebird::statuses_homeTimeline()```.

3. For each parameter template in method, use UPPERCASE in the Codebird function.
    Also don’t forget to include the parameter in your parameter list.

    Examples:
    - ```statuses/show/:id``` maps to ```Codebird::statuses_show_ID('id=12345')```.
    - ```users/profile_image/:screen_name``` maps to
      ```Codebird::users_profileImage_SCREEN_NAME('screen_name=mynetx')```.
*/
?>