<?php
/*
 * Template Name: Upwork Template
 * Description: A Page Template for displaying Upwork API capability.
 */

session_start();

//SOMETIMES I HAVE TO DO THIS (CLEAR THE SESSION) IF THERE IS SOMETHING WRONG ON THE AUTHENTICATION/AUTHORIZATION PROCESS!! THEN COMMENT ITS AGAIN!
/*unset($_SESSION['callback_url']);
unset($_SESSION['access_token']);
unset($_SESSION['access_secret']);
unset($_SESSION['request_token']);
unset($_SESSION['request_secret']);
exit;
*/

    
require WP_UPWORK_PLUGIN_DIR . '/vendor/autoload.php';
$access_token=get_option('wp_upwork_access_token');
$access_token_secret=get_option('wp_upwork_access_token_secret');
if($access_token && $access_token_secret){
    $config = new \Upwork\API\Config(
        array(
            'consumerKey'       => get_option('wp_upwork_api_key'),  // SETUP YOUR CONSUMER KEY
            'consumerSecret'    => get_option('wp_upwork_api_secret'),                  // SETUP YOUR KEY SECRET
            'accessToken'       => $access_token,                   // got access token
            'accessSecret'      => $access_token_secret,            // got access secret
            'mode'              => 'web',                           // can be 'nonweb' for console apps (default),
                                                                    // and 'web' for web-based apps
            'debug'             => false, // enables debug mode. Note that enabling debug in web-based applications can block redirects
            'authType'          => 'OAuthPHPLib' // your own authentication type, see AuthTypes directory
        )
    );
    $client = new \Upwork\API\Client($config);
}else{
    if(empty($_SESSION['callback_url']))
        $_SESSION['callback_url']='http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    
    $config = new \Upwork\API\Config(
        array(
            'consumerKey'       => get_option('wp_upwork_api_key'),  // SETUP YOUR CONSUMER KEY
            'consumerSecret'    => get_option('wp_upwork_api_secret'),                  // SETUP YOUR KEY SECRET
            'accessToken'       => $_SESSION['access_token'],                   // got access token
            'accessSecret'      => $_SESSION['access_secret'],            // got access secret
            'requestToken'      => $_SESSION['request_token'],      // got request token
            'requestSecret'     => $_SESSION['request_secret'],     // got request secret
            'verifier'          => $_GET['oauth_verifier'],         // got oauth verifier after authorization
            'mode'              => 'web',                           // can be 'nonweb' for console apps (default),
                                                                    // and 'web' for web-based apps
            'debug'             => true, // enables debug mode. Note that enabling debug in web-based applications can block redirects
            'authType'          => 'OAuthPHPLib' // your own authentication type, see AuthTypes directory
        )
    );
    $client = new \Upwork\API\Client($config);
    if (empty($_SESSION['request_token']) && empty($_SESSION['access_token'])) {
        // we need to get and save the request token. It will be used again
        // after the redirect from the Upwork site
        $requestTokenInfo = $client->getRequestToken();
        $_SESSION['request_token']  = $requestTokenInfo['oauth_token'];
        $_SESSION['request_secret'] = $requestTokenInfo['oauth_token_secret'];
        // request authorization
        $client->auth();
    } elseif (empty($_SESSION['access_token'])) {
        // the callback request should be pointed to this script as well as
        // the request access token after the callback
        $accessTokenInfo = $client->auth();
        $_SESSION['access_token']   = $accessTokenInfo['access_token'];
        $_SESSION['access_secret']  = $accessTokenInfo['access_secret'];
        
        update_option('wp_upwork_access_token',$accessTokenInfo['access_token']);
        update_option('wp_upwork_access_token_secret',$accessTokenInfo['access_secret']);

        //$redirect_url="http://localhost/works/wordpress_local/upwork/";
        header("Location:".$_SESSION['callback_url']);
    }
}

// $accessTokenInfo has the following structure
// array('access_token' => ..., 'access_secret' => ...);
// keeps the access token in a secure place
// if authenticated
if ($access_token) {
//if ($_SESSION['access_token']) {
    //var_dump($_SESSION);
    // clean up session data
    unset($_SESSION['callback_url']);
    unset($_SESSION['access_token']);
    unset($_SESSION['access_secret']);
    unset($_SESSION['request_token']);
    unset($_SESSION['request_secret']);
    
    // gets info of the authenticated user
    //$auth = new \Upwork\API\Routers\Auth($client);
    //$info = $auth->getUserInfo();
    
    //$roles = new \Upwork\API\Routers\Hr\Roles($client);
    //$info = $roles->getAll();
    //echo "<BR/>PROFILE:<BR/>".print_r($info);
    $jobs = new \Upwork\API\Routers\Jobs\Search($client);
    $params=array(
        "q"=>"laravel",
        "page"=>"0;5"  //page param is not working anyway!!!
    );
    $result = $jobs->find($params);

    get_header(); ?>
        <div id="job_result" class="job_result" style="margin-top: -50px;">

            <?php 
                $i=1;
                foreach($result->jobs as $j){
                    echo $i.". ".$j->title."<br/>";
                    $i++;
                }

            ?>

        </div><!-- #primary -->
    <?php get_sidebar(); ?>
    <?php get_footer(); ?>
<?php
}
