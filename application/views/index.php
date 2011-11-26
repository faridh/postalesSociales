<?php

    date_default_timezone_set('America/Mexico_City');
    require 'config.php';
?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
    
    <head>
        
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="title" content="Postales Sociales">
        <meta name="description" content="Manda postales de amor a tus seres queridos!">
        <meta name="google-site-verification" content="">
        <meta name="author" content="ikiSoftware">
        <meta name="Copyright" content="Copyright ikiSoftware 2011. All Rights Reserved.">

        <link rel="shortcut icon" href="images/favicon.ico">
        <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
        <title>Postales Sociales</title>
        
        <link href="css/reset.css" rel="stylesheet" media="screen">
        <link href="css/style.css" rel="stylesheet" media="screen">
        <link href="css/posts.css" rel="stylesheet" media="screen">
        
        <script src="lib/js/jquery-1.7.min.js" type="text/javascript"></script>
        <script src="lib/js/animations.js" type="text/javascript"></script>
        <script src="lib/js/logging.js" type="text/javascript"></script>
        
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        
        <script type="text/javascript">
            
            var fb_init         = false;
            var appID           = '<?php echo FACEBOOK_ID; ?>';
            var userID          = 0;
            var redirectURI     = '<?php echo CANVAS_PAGE; ?>';
            var access_token    = '';
            var environment     = '<?php echo FRONTEND_ENVIRONMENT; ?>';
            var friendsList     = new Object();
            
            window.fbAsyncInit = function()
            {
                FB.init({
                    appId      : '<?php echo FACEBOOK_ID; ?>', // App ID
                    channelURL : '//<?php echo CANVAS_URL; ?>/channel.php', // Channel File
                    status     : true, // check login status
                    cookie     : true, // enable cookies to allow the server to access the session
                    oauth      : true, // enable OAuth 2.0
                    xfbml      : true  // parse XFBML
                });
                $("#loading-message").html("¡Autorizando aplicacion!");
                fb_init = true;
                testLoginStatus();
            };
            
            (function(d)
            {
                var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
                js = d.createElement('script'); js.id = id; js.async = true;
                js.src = "//connect.facebook.net/en_US/all.js";
                d.getElementsByTagName('head')[0].appendChild(js);
            }(document));
            
            
            function testLoginStatus()
            {
                if ( !fb_init )
                {
                    setTimeout(testLoginStatus, 100);
                }
                else
                {
                    FB.getLoginStatus( function(response) 
                    {
                        if (response.authResponse) 
                        {
                            // logged in and connected user, someone you know
                            userID = response.authResponse.userID;
                            fetchFBUser();
                        } 
                        else
                        {
                            // no user session available, someone you dont know
                            var path = 'https://www.facebook.com/dialog/oauth?';
                            var queryParams = 
                                [
                                'client_id=' + appID,
                                'redirect_uri=' + redirectURI,
                                'scope=user_likes,email,user_birthday,user_photos,user_birthday',
                                'response_type=token'
                            ];
                            var query = queryParams.join('&');
                            var url = path + query;
                            top.location.href = url;
                        }
                    });
                }
            }
            
            function fetchFBUser()
            {
                
                FB.api('/me', function(response) 
                {
                    if(response.error)
                    {
                        fetchFBUser();
                    }
                    else
                    {
                        
                        $("#loading-message").html("¡Terminado!");
                        $("#loading").fadeOut();
                        showWelcomeForm();
                    }
                    
                });
            }

        </script>
        
    </head>
    
    <body>
        
        <div id="fb-root"></div>
        
        <div id="loading">
            <div id="loading-content">
                <h2 id="loading-message">¡Conectando con Facebook!</h2>
                <img id="loading-image" src="http://fc09.deviantart.net/fs70/f/2011/196/f/4/nyan_cat__d_by_alice2700-d3rrflu.gif" />
            </div>
        </div>
        
    </body>
    
</html>
