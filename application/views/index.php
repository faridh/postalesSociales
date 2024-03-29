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
            
            var fb_init             = false;
            var appID               = '<?php echo FACEBOOK_ID; ?>';
            var userID              = 0;
            var accessToken         = 0;
            var redirectURI         = '<?php echo CANVAS_PAGE; ?>';
            var environment         = '<?php echo FRONTEND_ENVIRONMENT; ?>';
            var userObject          = new Object();
            var friendsList         = new Object();
            var friendsArray        = new Array();
            var photoList           = new Object();
            var unavailableUsers    = new Array();
            var sentPostcardsList   = new Array();
            var sentPostcards       = 0;
            
            var imageId             = '';
            
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
                            accessToken = response.authResponse.accessToken;
                            fetchUserSentPostcards();
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
            
            function fetchUserSentPostcards()
            {
                $.ajax(
                    {
                        url: "index.php/main/getUserSentPostcards",
                        data: { userId:userID },
                        type: 'POST',
                        error: function(result, error_code, error_thrown)
                        {
                            log_message("fetchUserSentPostcards() ERROR");
                        },
                        success: function(result)
                        {
                            var tempPostcardList = JSON.parse(result);
                            
                            for ( postcardId in tempPostcardList.postcards ) 
                            {
                                sentPostcardsList.push(tempPostcardList.postcards[postcardId]);
                            }
                            sentPostcards = sentPostcardsList.length;
                            fetchFBUser();
                        }
                    }
                );
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
                        for ( friend in friendsList.data )
                        {
                            friendsArray.push(friendsList.data[friend]);
                        }
                        
                        $("#loading-message").html("¡Buscando fotos del Usuario!");
                        fetchFBUserPhotos();
                        //$("#loading").fadeOut();
                        filterList();
                        fillList(); 
                    }
                });
            }
            
            function filterList()
            {
                var friendsIndex = 0;
                for ( friendsIndex = 0; friendsIndex < friendsList.data.length; friendsIndex++ )
                {
                    for ( var postcardIndex = 0; postcardIndex < sentPostcardsList.length; postcardIndex++ )
                    {
                        if ( friendsList.data[friendsIndex].id == sentPostcardsList[postcardIndex].friend_id  )
                        {
                            friendsList.data.splice(friendsIndex, 1);
                            --friendsIndex;
                        }
                    }
                }                
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
                $('#user_image').attr('height', '260px');
                $('#user_image').attr('width', '260px');
                $('#user_image').css('margin-top', '20px');
                $('#sent_postcards_number').html(sentPostcards);
                $('#friends_number').html(friendsArray.length - sentPostcards);
                if ( (friendsArray.length - sentPostcards) == 0 )
                {
                    $('#friends_left').html('');
                }
            }
            
            function changeBackground(imageId)
            {
                $('#postcard_image_container').css('background-image', 'url("images/templates/background'+imageId+'.png")');
            }
            
            function displayChangePostcard()
            {
                $('#change_postcard_overlay').css('visibility', 'visible');
                $('#change_postcard_overlay').fadeIn(250);
            }
            
            function hideChangePostcard()
            {
                $('#change_postcard_overlay').fadeOut(250, 
                    function()
                    {
                        $('#change_postcard_overlay').css('visibility', 'hidden');
                    }
                );
            }
            
            function getResults()
            {

                var max = 2000;
                var friends = [];
                $.each($('#list').find(".checked"), function(_i, _f)
                {
                    var $f = $(_f).closest(".option");
                    if ( friends.length < 20 )
                    {
                        friends.push($f.attr(FriendList.ATTRIBUTES.fid));
                    }
                    
                    if ( friends.length >= max )
                    {
                        return false;
                    }
                });
                return friends;
            }
            
            function sendPostcards()
            {
                
                showLoading("Enviando Postales...");
                
                var title           = $('#postcard_title').val();
                var message         = $('#postcard_text').val();
                var list            = getResults();
                var activeIds       = new Object();
                activeIds.friends   = new Array();
                                
                for ( tempFriendId in list )
                {
                    var tempFriend  = new Object();
                    tempFriend.id   = list[tempFriendId];
                    activeIds.friends.push(tempFriend);
                }
                
                FB.ui(
                    {
                        method: 'apprequests',
                        message: 'Una tarjeta de Postalitas !!',
                        to: list.toString()
                    }, 
                    function(response)
                    {
                        log_message("sendPostcards() CALLBACK");
                        log_message(response);
                        if ( response != null )
                        {
                            if ( !response.hasOwnProperty('error_code') )
                            {
                                $.ajax(
                                    {
                                        url: "index.php/main/sendPostcard",
                                        data: 
                                            { 
                                                userId:userID, 
                                                friends:activeIds, 
                                                title:title, 
                                                message:message, 
                                                backgroundId:'0', 
                                                songId:'0' 
                                            },
                                        type: 'POST',
                                        error: function(result, error_code, error_thrown)
                                        {
                                            log_message("sendPostcards() ERROR");
                                        },
                                        success: function(result)
                                        {
                                            log_message(result);

                                            setTimeout(
                                                function()
                                                {
                                                    completeLoading(function(){}, "¡Terminado!"); 
                                                }, 1000);
                                        }
                                    }
                                );
                            }
                            else
                            {
                                completeLoading(function(){}, "¡ERROR!");
                            }
                        }
                        else
                        {
                            completeLoading(function(){}, "¡ERROR!");
                        }
                    }
                );
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
                    <h1 class="black_text">Tarjetitas Navideñas</h1>
                </div>
                
                <div id="selector" class="Design1">
                    
                    <div class="Prompt" id="instructions">Deselecciona a los amigos a los que no les quieras enviar esta tarjeta</div>     
                    <input type="text" id="search">
                    <div id="list"></div>
                    <div class="Prompt">
                        Send a card to these <span id="friend_count" style="margin:0;padding:0"></span> friends
                        <a href="javascript:void(0);" id="uncheck_all">
                            Uncheck all
                        </a>
                    </div>

                    <div id="choose_more" style="display:none;">
                        Choose up to <span id="remaining"></span> more friends
                    </div>

                    <div id="select_from">
                    </div>
                    
                </div>
                
                <div id="photoSelector">

                    <div id="success_message">
                        <h3>¡Excelente!</h3>

                        <p>
                            Has mandado esta postal a <span id="sent_postcards_number">0</span> amigos tuyos.
                            <span id="friends_left">
                                ¡Todavía hay <span id="friends_number">0</span> amigos más que amarían recibir esta tarjeta!
                            </span>
                        </p>
                    <div id="added_list"></div>
                    
                    <div id="progress_bar"><div id="progress" style="width: 0px; ">0%</div></div>
                        <a id="submit_again" class="BigButton Glowing" style='color:#fff;'  href="javascript:void(false);">Send to More Friends</a> 
                    </div>
                    
                    <input type="text" class="input_text warning" id="postcard_title" autocomplete="off" value="¡Feliz Navidad!"/>

                    <div id="postcard_image_container" class="bg_selector">
                        <img id="user_image"  src="" onmouseover="javascript:displayChangePostcard();" onmouseout="javascript:hideChangePostcard();"/>
                        <div id="change_postcard_overlay">
                            
                            <img src="images/small_camera.png" style="margin-top: 5px;"/> <span>Cambiar foto</span>
                            &nbsp;
                        </div>
                    </div>
                    
                    <br/>
                    
                    <div id="postcard_image_background_selector">
                        
                        <img class="bg_selector" id="icon01" src="images/templates/icons/background_icon01.png" onclick="javascript:changeBackground('01');" />
                        <img class="bg_selector" id="icon02" src="images/templates/icons/background_icon02.png" onclick="javascript:changeBackground('02');" />
                        <img class="bg_selector" id="icon03" src="images/templates/icons/background_icon03.png" onclick="javascript:changeBackground('03');" />
                        <img class="bg_selector" id="icon04" src="images/templates/icons/background_icon04.png" onclick="javascript:changeBackground('04');" />
                        
                    </div>
                    
                    <label for="postcard_text">
                        <textarea class="input_text warning" id="postcard_text" autocomplete="off" rows="5"/>¡Felices Fiestas!
                        </textarea>
                    </label>
                    
                    <input type="submit" class="button" value="¡Enviar!" onclick="javascript:sendPostcards();" />
                    
                </div>
                
            </div>
            
        </div>
        
    </body>
    
</html>
