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
        <script src="lib/js/lists.js" type="text/javascript"></script>
        
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        
        <script type="text/javascript">
            
            var fb_init         = false;
            var appID           = '<?php echo FACEBOOK_ID; ?>';
            var userID          = 0;
            var redirectURI     = '<?php echo CANVAS_PAGE; ?>';
            var environment     = '<?php echo FRONTEND_ENVIRONMENT; ?>';
            var userObject      = new Object();
            var friendsList     = new Object();
            var photoList       = new Object();
            var imageId         = '';
            
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
                $("#loading-message").html("¡Autorizando aplicación!");
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
                                'scope=user_likes,email,user_birthday,friends_birthday,offline_access,publish_stream,user_photos,user_birthday',
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
                    if (response.error)
                    {
                        fetchFBUser();
                    }
                    else
                    {
                        userObject = response
                        $("#loading-message").html("¡Buscando amigos del Usuario!");
                        log_message("USER: ");
                        log_message(response);
                        fetchFBUserFriends();
                    }
                    
                });
            }
            
            function fetchFBUserFriends()
            {
                FB.api('/me/friends', function(response) 
                {
                    if (response.error)
                    {
                        log_message("fetchFBUserFriends() ERROR");
                    }
                    else
                    {
                        friendsList = response;
                        $("#loading-message").html("¡Buscando fotos del Usuario!");
                        log_message("FRIENDS: ");
                        log_message(response);
                        fetchFBUserPhotos();
                        //$("#loading").fadeOut();
                        fillList(); 
                    }
                });
            }
            
            function fetchFBUserPhotos()
            {
                FB.api('/me/photos', function(response) 
                {
                    if (response.error)
                    {
                        log_message("fetchFBUserPhotos() ERROR");
                    }
                    else
                    {
                        photoList = response;
                        $("#loading-message").html("¡Iniciando Aplicación!");
                        log_message("PHOTOS: ");
                        log_message(response);
                        $("#loading").fadeOut();
                        displayUserInterface();
                    }
                });
            }
            
            function displayUserInterface()
            {
                $('#main-container').css('visibility', 'visible');
                $('#main-container').show();
                $('#user_image').attr('src', 'https://graph.facebook.com/'+ userID +'/picture?type=large');
                $('#user_image').attr('height', '200px');
                $('#user_image').attr('width', '200px');
                $('#user_image').css('margin-top', '50px');
                log_message("READY TO DISPLAY USER INTERFACE");
            }
            
            function changeBackground(imageId)
            {
                $('#postcard_image_container').css('background-image', 'url("images/templates/background'+imageId+'.png")');
            }
            
            function sendPostcards()
            {
                log_message("sendPostcards()");
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

        <div id="content">
            
            <div id="main-container">
            
                <div class="page_header">
                
                    <h1 class="black_text">LOGO, BLA BLA BLA</h1>
                    
                </div>
                
                <div id="selector">
                       <div id="instructions">Deselecciona a los amigos a los que no les quieras enviar esta tarjeta</div>     
                   <div id="list"></div>
                </div>
                
                <div id="photoSelector">

                    <input type="text" class="input_text warning" id="postcard_title" autocomplete="off" value="¡Feliz Navidad!"/>

                    <div id="postcard_image_container" class="bg_selector">
                        <img id="user_image"  src="" />
                    </div>
                    
                    <br/>
                    
                    <div id="postcard_image_background_selector">
                        
                        <img class="bg_selector" id="icon01" src="images/templates/icons/background_icon01.png" onclick="javascript:changeBackground('01');" />
                        <img class="bg_selector" id="icon02" src="images/templates/icons/background_icon02.png" onclick="javascript:changeBackground('02');" />
                        <img class="bg_selector" id="icon03" src="images/templates/icons/background_icon03.png" onclick="javascript:changeBackground('03');" />
                        <img class="bg_selector" id="icon04" src="images/templates/icons/background_icon04.png" onclick="javascript:changeBackground('04');" />
                        
                    </div>
                    
                    <label for="postcard_text">
                        <textarea class="input_text warning" id="postcard_text" autocomplete="off" rows="5"/>
                            ¡Felices Fiestas!
                        </textarea>
                    </label>
                    
                    <input type="submit" class="button" value="¡Enviar!" onclick="javascript:sendPostcards();" />
                    
                </div>
                
            </div>
            
        </div>
        
    </body>
    
</html>
