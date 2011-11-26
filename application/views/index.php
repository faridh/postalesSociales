<?php

    date_default_timezone_set('America/Mexico_City');
    require 'config.php';
?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
    
    <head>
        
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="title" content="Amigos ITESM">
        <meta name="description" content="Conectate con tus amigos del Tec de Monterrey!">
        <meta name="google-site-verification" content="">
        <meta name="author" content="ikiSoftware">
        <meta name="Copyright" content="Copyright ikiSoftware 2011. All Rights Reserved.">

        <link rel="shortcut icon" href="images/favicon.ico">
        <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
        <title>Amigos ITESM</title>
        
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
            var albumID         = 0;
            var redirectURI     = '<?php echo CANVAS_PAGE; ?>';
            var access_token    = '';
            var environment     = '<?php echo FRONTEND_ENVIRONMENT; ?>';
            var registeredUser  = false;
            var friendsList     = new Object();
            var allLikes        = new Object();
            var school          = '';
            var grade           = '';
            var email           = '';
            var friendsLikes    = new Object();
            var myTotalLikes    = 0;
            var likesByType     = new Array();
            var meFlag          = false;
            var friendsFlag     = false;
            var moviesFlag      = false;
            var musicFlag       = false;
            var booksFlag       = false;
            var televisionFlag  = false;
            var gamesFlag       = false;
            var dataSentFlag    = false;
            var topMatches      = new Object();

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
                           $("#loading-message").html("Buscando info. del usuario");
                           getUser();
                       } 
                       else
                       {
                           // no user session available, someone you dont know
                           $("#loading-message").html("¡Pidiendo permisos!");
                           var path = 'https://www.facebook.com/dialog/oauth?';
                           var queryParams = 
                               [
                                   'client_id=' + appID,
                                   'redirect_uri=' + redirectURI,
                                   'scope=user_likes,email,user_birthday,user_education_history,user_checkins,user_photos,user_birthday',
                                   'response_type=token'
                               ];
                           var query = queryParams.join('&');
                           var url = path + query;
                           top.location.href = url;
                       }
                   });
               }
           }
           
            function getUser()
            {

                $.ajax(
                {
                    url: "index.php/main/getUser",
                    data: { userId:userID },
                    type: 'POST',
                    error: function(result, error_code, error_thrown)
                    {
                        //console.log(result);
                        $("#loading-message").html("Error conectando con Facebook");
                    },
                    success: function(result)
                    {
                        var finalResult = JSON.parse(result);
                        registeredUser  = finalResult.registeredUser;
                        albumID         = finalResult.albumId
                        $("#loading-message").html("¡Configurando aplicacion!");
                        testFetchUser();
                    }
                });
            }
           
           function testFetchUser()
           {
               
               FB.api('/me', function(response) 
               {
                   if(response.error)
                   {
                       testFetchUser();
                   }
                   else
                   {
                       if ( registeredUser )
                       {
                           //fetchData();
                           //--- eliminar estas linea cuando se haga el fetch
                           $("#loading-message").html("¡Terminado!");
                           $("#loading").fadeOut();
                           showWelcomeForm();
                       }
                       else
                       {
                           $("#loading-message").html("¡Terminado!");
                           $("#loading").fadeOut();
                           showWelcomeForm();
                       }
                   }
                   
               });
           }
           
           function fetchData()
           {
               
               var valid_form  = true;
               var school_val  = $('#school').val();
               var grade_val   = $('#grade').val();

               if ( school_val == '' || school_val == 'Escribe el nombre de tu escuela' )
               {
                   valid_form = false;
                   $('#school').css("border", "#d81c1c 2px solid");
               }
               else
               {
                   $('#school').css("border", "none");
               }
               
               if ( grade_val == 0 )
               {
                   valid_form = false;
                    $('#grade').css("border", "#d81c1c 2px solid");
               }
               else
               {
                   $('#grade').css("border", "none");
               }

               if ( !valid_form ) return;
               
               $('#school').attr('disabled', true);
               $('#grade').attr('disabled', true);
               school  = school_val;
               grade   = grade_val;
               hideSchoolForm();
               
               // ME
               FB.api('/me', function(response) 
               {
                   email           = response.email;
                   allLikes['me']  = new Object();
                   meFlag          = true;
                   allLikes['me']  = response;
                   $("#loading-message").html("Obteniendo info. del usuario");
                   if ( meFlag && friendsFlag && moviesFlag && musicFlag && booksFlag && televisionFlag && gamesFlag )
                       sendDataToServer();
               });
               
               // ME
               FB.api('/me/friends', function(response) 
               {
                   friendsFlag = true;
                   for ( var friend in response.data )
                   {
                       if ( typeof response.data[friend] != 'function' ) 
                       {
                           friendsList[response.data[friend].id] = response.data[friend].id;
                       }
                   }
                   $("#loading-message").html("Obteniendo amigos del usuario");
                   if ( meFlag && friendsFlag && moviesFlag && musicFlag && booksFlag && televisionFlag && gamesFlag )
                       sendDataToServer();
               });
               
               // MOVIES
               FB.api('/me/movies', function(response) 
               {
                   allLikes['movies']      = new Object();
                   moviesFlag              = true;
                   myTotalLikes           += response.data.length;
                   likesByType['movies']   = response.data.length;
                   for ( var movie in response.data )
                   {
                       if ( typeof response.data[movie] != 'function' )
                       {
                           allLikes['movies'][response.data[movie].id] = response.data[movie].name;
                       }
                   }
                   
                   $("#loading-message").html("Obteniendo pelis del usuario");
                   if ( meFlag && friendsFlag && moviesFlag && musicFlag && booksFlag && televisionFlag && gamesFlag )
                       sendDataToServer();
               });
               
               // MUSIC
               FB.api('/me/music', function(response) 
               {
                   allLikes['music']       = new Object();
                   musicFlag               = true;
                   myTotalLikes           += response.data.length;
                   likesByType['music']    = response.data.length;
                   for ( var music in response.data )
                   {
                       if ( typeof response.data[music] != 'function' )
                       {
                           allLikes['music'][response.data[music].id] = response.data[music].name;
                       }
                   }
                   $("#loading-message").html("Obteniendo musica del usuario");
                   if ( meFlag && friendsFlag && moviesFlag && musicFlag && booksFlag && televisionFlag && gamesFlag )
                       sendDataToServer();
               });
               
               // BOOKS
               FB.api('/me/books', function(response) 
               {
                   allLikes['books']       = new Object();
                   booksFlag               = true;
                   myTotalLikes           += response.data.length;
                   likesByType['books']   = response.data.length;
                   for ( var book in response.data )
                   {
                       if ( typeof response.data[book] != 'function' )
                       {
                           allLikes['books'][response.data[book].id] = response.data[book].name;
                       }
                   }
                   $("#loading-message").html("Obteniendo libros del usuario");
                   if ( meFlag && friendsFlag && moviesFlag && musicFlag && booksFlag && televisionFlag && gamesFlag )
                       sendDataToServer();
               });
               
               // TELEVISION
               FB.api('/me/television', function(response) 
               {
                   allLikes['television']      = new Object();
                   televisionFlag              = true;
                   myTotalLikes               += response.data.length;
                   likesByType['television']   = response.data.length;
                   for ( var show in response.data )
                   {
                       if ( typeof response.data[show] != 'function' )
                       {
                           allLikes['television'][response.data[show].id] = response.data[show].name;
                       }
                   }
                   $("#loading-message").html("Obteniendo series del usuario");
                   if ( meFlag && friendsFlag && moviesFlag && musicFlag && booksFlag && televisionFlag && gamesFlag )
                       sendDataToServer();
               });
               
               // GAMES
               FB.api('/me/games', function(response) 
               {
                   allLikes['games']       = new Object();
                   gamesFlag               = true;
                   myTotalLikes           += response.data.length;
                   likesByType['games']    = response.data.length;
                   for ( var game in response.data )
                   {
                       if ( typeof response.data[game] != 'function' )
                       {
                           allLikes['games'][response.data[game].id] = response.data[game].name;
                       }
                   }
                   $("#loading-message").html("Obteniendo juegos del usuario");
                   if ( meFlag && friendsFlag && moviesFlag && musicFlag && booksFlag && televisionFlag && gamesFlag )
                       sendDataToServer();
               });
               
           }
           
           function sendDataToServer()
              {
                  if ( dataSentFlag == false )
                  {
                      $("#loading-message").html("Conectando a servidor");

                      allLikes.school = school;
                      allLikes.grade  = grade;
                      allLikes.email  = email;

                      $.ajax(
                      {
                          url: "index.php/main/registerUser",
                          data: { data:allLikes },
                          type: 'POST',
                          error: function(result)
                          {
                              dataSentFlag = true;
                          },
                          success: function(result)
                          {
                              dataSentFlag = true;
                              createAppAlbum();
                              getTopMatches();
                          }
                      });
                  }
              }
           
           function getTopMatches()
           {
               
               $('#top_matches').html('');
               $("#loading-message").html("Filtrando respuesta del servidor");
               
               if ( meFlag && moviesFlag && musicFlag && booksFlag && televisionFlag && gamesFlag )
               {
                   $.ajax(
                   {
                       url: "index.php/main/getTopMatches",
                       data: { data:allLikes },
                       type: 'POST',
                       error: function(result, error_code, error_thrown)
                       {
                           dataSentFlag = true;
                       },
                       success: function(result)
                       {
                           $("#loading-message").html("Cargando datos...");
                           dataSentFlag = true;
                           
                           var resultCopy   = new Array();
                           result           = JSON.parse(result);
                           result           = sortResults(result);
                           
                           if ( environment != 'DEVELOPMENT' )
                           {
                               result = filterResults(result);
                           }
                           
                           
                           if ( result.length > 10 )
                           {
                               result = result.slice(0, 10);
                           }
                           
                           if ( result.length == 0 )
                           {
                               //log_message("NO SE ENCONTRARON RESULTADOS. DESPLEGAR PERFILES ALEATORIOS.");
                           }
                           else
                           {
                               topMatches = result;
                               showTopPage();
                           }
                       }
                   });
               }
               else
               {
                   //log_message("FETCH DATA FIRST");
               }
           }
           
           function filterResults(friendsArray)
           {
               for ( var index = 0; index < friendsArray.length; index++ )
               {
                   if ( typeof friendsArray[index] != 'function')
                   {
                       if ( isInFriendsList(friendsArray[index]) )
                       {
                           friendsArray.splice(index, 1);
                           index--;
                       }
                   }
               }
               return friendsArray;
           }
           
           function isInFriendsList(friendObject)
           {
               var isInArray = false;
               if ( friendsList.hasOwnProperty(friendObject.id) )
               {
                   isInArray = true;
               }
               return isInArray;
           }

           function showAddFriend(id)
           {
               FB.ui(
               {
                   method: 'friends',
                   id: id
               },
                   function(response) 
                   {
                       //log_message("showAddFriend()");
                   }
               );
           }

           function sortResults(friendsArray)
           {
               var transformedArray = new Array();
               
               for ( var index in friendsArray )
               {
                   transformedArray.push(friendsArray[index]);
               }
               
               qsort(transformedArray, 0, transformedArray.length);
               return transformedArray;
           }
           
           function qsort(array, begin, end)
           {
               if ( end - 1 > begin) 
               {
                   var pivot = begin + Math.floor(Math.random()*( end - begin));
                   pivot = partition(array, begin, end, pivot);

                   qsort(array, begin, pivot);
                   qsort(array, pivot + 1, end);
               }
           }
           
           function partition(array, begin, end, pivot)
           {
               var piv = array[pivot];
               array.swap(pivot, end-1);
               
               var store = begin;
               var ix;
               
               for ( ix=begin; ix < end-1; ++ix ) 
               {
                   if(array[ix].total_likes >= piv.total_likes) 
                   {
                       array.swap(store, ix);
                       ++store;
                   }
               }
               array.swap(end-1, store);

               return store;
           }
           
           Array.prototype.swap = function(a, b)
           {
               var tmp = this[a];
               this[a] = this[b];
               this[b] = tmp;
           }
           
           function toggleShowInfo(id)
           {
               $("#"+id).toggle("fast");
               return false;
           }
           
           function showTopPage()
           {
               
               addNavigationCategories();
               
               $.ajax(
                {
                    url: "index.php/main/getTopPosts",
                    data: { page:0 },
                    type: 'POST',
                    error: function(result, error_code, error_thrown)
                    {
                        log_message("getTopPosts() ERROR");
                        log_message(result);
                        log_message(error_code);
                        log_message(error_thrown);
                    },
                    success: function(result)
                    {
                        populatePosts(JSON.parse(result));
                    }
                });
                
                log_message("DESACTIVANDO TOP MATCHES [TEMPORALMENTE].");
               
           }
           
            function displayTopMatches(friendsArray)
            {   
               
                $("#friend-content").animate({"opacity":"0"}, 1000);
                $("#friend-content").hide();
                $('#top_matches').html('');
                
                var counter = 0;
               
               for ( var index in friendsArray )
               {

                   
                   var totalLikes  = friendsArray[index].total_likes;
                   var likesIds    = friendsArray[index].likes;
                   var friendId    = friendsArray[index].id;
                   
                   if ( friendId != undefined )
                   {
                       
                       counter++;
                       
                       var currentFriendDiv    = '<div id="' + friendId + '" class="personcontainer">';
                       var currentSimilarity   = ( myTotalLikes > 0 )? Math.round((totalLikes / myTotalLikes) * 100) : 0;
                       
                       currentFriendDiv += '<div  class="personalinfo">';
                           currentFriendDiv += '<h2>#'+counter+'</h2>';
                           currentFriendDiv += '<div class="imagebox"><img id="image_' + friendId + '" src="https://graph.facebook.com/' + friendId + '/picture" /></div>';
                           currentFriendDiv += '<div class="namebox">';
                               currentFriendDiv += '<h2 id="name_' + friendId + '"></h2>';
                               currentFriendDiv += '<img src="images/addfriend.png" class="addfriendbutton" onclick="javascript:showAddFriend('+friendId+')"/>';
                           currentFriendDiv += '</div>';
                           currentFriendDiv += '<h3>' + currentSimilarity + '%</h3>';
                       currentFriendDiv += '</div>';
                       currentFriendDiv += '<div class="show_more_info"><a onclick="toggleShowInfo(\'like_container_' + friendId + '\')"><img src="images/icon-down-arrow.png" /><h3> Mostrar más info.</h3></a></div><br/><br/>';
                           
                       currentFriendDiv += '<div id="like_container_' + friendId + '" class="like_container">';
                       
                            currentFriendDiv += '<div class="projectsHeader" onclick="javascript:toggleShowInfo(\''+friendId+'_projects_container\')"><h2 class="like_category">projects</h2></div>';
                                currentFriendDiv += '<div id="'+friendId+'_projects_container">';
                                    
                                    currentFriendDiv += '<div class="like_box">';
                                        currentFriendDiv += '<a href="#" target="_blank">'
                                            currentFriendDiv += '<div style="height:50px;"><center><img style="display: auto; position: relative: top: 0px;" src="images/mexican_revolution/mx_thumb.jpg" /></center></div>';
                                            currentFriendDiv += '<p>Semana de la Revolución</p>';
                                        currentFriendDiv += '</a>'
                                    currentFriendDiv += '</div>';
                                    
                                    currentFriendDiv += '<div class="like_box">';
                                        currentFriendDiv += '<a href="#" target="_blank">'
                                            currentFriendDiv += '<div style="height:50px;"><center><img style="display: auto; position: relative: top: 0px;" src="images/mexican_revolution/fashion_thumb.jpg" /></center></div>';
                                            currentFriendDiv += '<p>Semana de Moda</p>';
                                        currentFriendDiv += '</a>'
                                    currentFriendDiv += '</div>';
                                    
                                currentFriendDiv += '</div><br/><br/><br/><br/>';
                       
                       for ( var type in likesIds )
                       {
                           
                           var currentCategorySimilarity = ( likesByType[type] > 0 )? Math.round((likesIds[type].length / likesByType[type]) * 100) : 0;
                           
                           currentFriendDiv += '<div class="'+type+'Header" onclick="javascript:toggleShowInfo(\''+friendId+'_'+type+'_container\')"><h2 class="like_category">' + type + '</h2><h3>' + currentCategorySimilarity + '%</h3></div>';
                           currentFriendDiv += '<div id="'+friendId+'_'+type+'_container">';
                           var line_counter = 0;
                           for ( var likeId in likesIds[type] )
                           {
                               if ( typeof likesIds[type][likeId] != 'function' )
                               {
                                   if(line_counter % 5 == 0) currentFriendDiv += '<br/>';
                                   currentFriendDiv += '<div class="like_box">';
                                   currentFriendDiv += '<a href="http://www.facebook.com/profile.php?id='+likesIds[type][likeId]+'" target="_blank">'
                                   currentFriendDiv += '<div style="height:50px;"><center><img style="display: auto; position: relative: top: 0px;" id="like_image_' + likesIds[type][likeId] + '" class="like_image_' + likesIds[type][likeId] + '" src="https://graph.facebook.com/' + likesIds[type][likeId] + '/picture" /></center></div>';
                                   currentFriendDiv += '<p class="like_name_' + likesIds[type][likeId] + '"></p>';
                                   currentFriendDiv += '</a>'
                                   currentFriendDiv += '</div>';
                                   line_counter++;

                                   FB.api('/'+likesIds[type][likeId], function(likeResponse)
                                   {
                                       var currentLikeName = $('.like_name_'+likeResponse.id).html();
                                       if ( currentLikeName == '' )
                                       {
                                           $('.like_name_'+likeResponse.id).html($('.like_name_'+likeResponse.id).html()+"  " + likeResponse.name);
                                       }
                                   });
                               }
                           }
                           currentFriendDiv += '</div><br/><br/><br/>';
                       }
                       
                       currentFriendDiv += '<div class="friendsHeader" onclick="javascript:toggleShowInfo(\'common_friends_' + friendId + '\')"><h2 class="like_category">Friends in Common</h2><h3 id="friend_number_'+friendId+'">0</h3></div>';
                       currentFriendDiv += '<div id="common_friends_' + friendId + '">';
                       currentFriendDiv += '</div>';
                       currentFriendDiv += '</div>';

                       currentFriendDiv += '</div><br/>';
                       $('#top_matches').html($('#top_matches').html() + currentFriendDiv);

                       fetchFriendData(friendId);
                       fetchMutualFriends(friendId);                
                   }
                   
               }
               
               for ( var index2 in friendsArray )
               {
                   toggleShowInfo("like_container_"+friendsArray[index2].id);
               }
           }
           
            function populatePosts(posts)
            {
                
                log_message("populatePosts()");
                log_message(posts);
                
                $("#main-content").css("display", "inherit");
                $("#loading").fadeOut(1000,
                    function()
                    {
                        $("#loading").css("display", "none");
                        $("#main-container").css("visibility", "visible");
                        $("#main-container").animate({"opacity":"1"}, 1000);
                        $("#friend-content").css("visibility", "visible");
                        $("#friend-content").animate({"opacity":"1"}, 1000); 
                        $("#sun").animate({"opacity":"1"}, 1000); 
                    }
                );
            }
           
            function createAppAlbum()
            {
                if ( albumID == 0 || albumID == null || albumID == undefined)
                {
                    FB.api('/me/albums', 'POST',
                        {
                            name: 'Test Album',
                            message: 'Test Album Description'
                        },
                        function(response) 
                        {
                            if ( response.id )
                            {
                                albumID = response.id;
                                
                                $.ajax(
                                {
                                    url: "index.php/main/registerAppAlbum",
                                    data: { albumId:albumID, userId:userID },
                                    type: 'POST',
                                    error: function(result, error_code, error_thrown)
                                    {
                                        $("#loading-message").html("Error registrando album de fotos");
                                    },
                                    success: function(result)
                                    {
                                        $("#loading-message").html("¡Configurando album de fotos!");
                                    }
                                });

                            }
                            else
                            {
                                log_message("createAppAlbum() ERROR");
                            }
                        }
                    );
                }
            }
            
            function displayPhotoUploadWidget()
            {
                var fileUploadForm = '<div id="post-form-container">';
                
                    fileUploadForm += '<form id="post-form" enctype="multipart/form-data" method="POST">';
                        fileUploadForm += '<input type="hidden" name="MAX_FILE_SIZE" value="100000" />';
                            fileUploadForm += 'Choose a file to upload: <input name="uploadedfile" type="file" /><br />';
                        fileUploadForm += '<input type="button" value="Upload File" onclick="javascript:submitPost();"/>';
                    fileUploadForm += '</form>';
                
                fileUploadForm += '</div>';
                
                $('#top_matches').html($('#top_matches').html() + fileUploadForm);
            }
            
            function submitPost()
            {
                log_message("post-form-submit");
                
                $.ajax(
                {
                    url: "index.php/main/uploadPhoto",
                    type: "POST",
                    data: {  },
                    error: function(result, error_code, error_thrown)
                    {
                        log_message("submitPost() ERROR");
                        log_message(result);
                        log_message(error_code);
                        log_message(error_thrown);
                    },
                    success: function(result)
                    {
                        log_message("submitPost() SUCCESS");
                        log_message(result);
                    }
                });
                
                return false;
            }
            
            function uploadPhoto()
            {
                $.ajax(
                {
                    url: "index.php/main/uploadPhoto",
                    type: 'POST',
                    error: function(result, error_code, error_thrown)
                    {
                        log_message("uploadPhoto() ERROR");
                        log_message(result);
                        log_message(error_code);
                        log_message(error_thrown);
                    },
                    success: function(result)
                    {
                        log_message("uploadPhoto() SUCCESS");
                        log_message(result);
                    }
                });
            }
           
           function fetchFriendData(friendId)
           {
               FB.api('/'+friendId, function(response)
                   {
                       if ( response.error )
                       {
                           $('#name_'+friendId).html($('#name_'+friendId).html() + "No disponible");
                       }
                       else
                       {
                           $('#name_'+response.id).html($('#name_'+response.id).html() + response.name);
                           $('#link_'+response.id).attr('href', response.link);
                           $('#'+response.id+'_aname').html($('#'+response.id+'_aname').html() + "" + response.first_name);
                       }
                   }
               );
           }
           
           function fetchMutualFriends(friendId)
           {
               FB.api('/me/mutualfriends/'+friendId, function(response)
                   {
                       /*
                        * TODO: WE MUST ADD AN ERROR CASE
                        */
                       var line_counter    = 0; 
                       for ( var index in response.data )
                       {
                           if ( typeof response.data[index] != 'function' )
                           {
                               var friendsInfo     = '';    
                               if(line_counter % 5 == 0) friendsInfo += '<br/>';
                               friendsInfo += '<div class="like_box">';
                               friendsInfo += '<a href="http://www.facebook.com/profile.php?id='+response.data[index].id+'" target="_blank">'
                               friendsInfo += '<div style="height:50px;"><center><img style="display: auto; position: relative: top: 0px; left: 0px;" id="like_image_' + response.data[index].id + '" class="like_image_' + response.data[index].id + '" src="https://graph.facebook.com/' + response.data[index].id + '/picture" /></center></div>';
                               friendsInfo += '<p class="like_name_' + response.data[index].id + '">'+response.data[index].name+'</p>';
                               friendsInfo += '</a>'
                               friendsInfo += '</div>';
                               $('#common_friends_'+friendId).html($('#common_friends_'+friendId).html() + friendsInfo);
                               line_counter++;
                           }
                       }
                       
                       $('#friend_number_'+friendId).html(line_counter);
                   }
               );
           }
           
           
            function addNavigationCategories()
            {
                $("#friend-content").css("opacity","0");
                $("#friend-content").css("visibility","visible");
                // $('#friend-content').html('<div id="topten" class="clearfix"><div><h2>Top 10</h2></div><div id="top_matches"></div></div> <!--end section-->');
            }
           
            function testLoading()
            {
                showLoading("Enviando Voto...");

                setTimeout(
                    function()
                    {
                        completeLoading(function(){log_message("Success !!");}, "¡Terminado!"); 
                    }, 4000);
            }
            
            function displayHot()
            {
                $("#top_matches").animate({"opacity":"0"}, 1000);
                $("#top_matches").hide();
                $("#publish-container").animate({"opacity":"0"}, 1000);
                $("#publish-container").hide();
                $("#info-container").animate({"opacity":"0"}, 1000);
                $("#info-container").hide();
                $("#friend-content").css("visibility", "visible");
                $("#friend-content").animate({"opacity":"1"}, 1000);
                $("#friend-content").show();
            }
            
            function displayPostPage()
            {
                $("#friend-content").animate({"opacity":"0"}, 1000);
                $("#friend-content").hide();
                $("#top_matches").animate({"opacity":"0"}, 1000);
                $("#top_matches").hide();
                $("#info-container").animate({"opacity":"0"}, 1000);
                $("#info-container").hide();
                $("#publish-container").css("visibility", "visible");
                $("#publish-container").animate({"opacity":"1"}, 1000);
                $("#publish-container").show();
            }
            
            function displayInfo()
            {
                $("#friend-content").animate({"opacity":"0"}, 1000);
                $("#friend-content").hide();
                $("#top_matches").animate({"opacity":"0"}, 1000);
                $("#top_matches").hide();
                $("#publish-container").animate({"opacity":"0"}, 1000);
                $("#publish-container").hide();
                $("#info-container").css("visibility", "visible");
                $("#info-container").animate({"opacity":"1"}, 1000);
                $("#info-container").show();
            }
            
            function displaySocializer()
            {
                $("#friend-content").animate({"opacity":"0"}, 1000);
                $("#friend-content").hide();
                $("#info-container").animate({"opacity":"0"}, 1000);
                $("#info-container").hide();
                $("#publish-container").animate({"opacity":"0"}, 1000);
                $("#publish-container").hide();
                $("#top_matches").css("visibility", "visible");
                $("#top_matches").animate({"opacity":"1"}, 1000);
                $("#top_matches").show();
                displayTopMatches(topMatches);
            }
            
            function simulatePost()
            {
                showLoading("Publicando contenido...");

                setTimeout(
                    function()
                    {
                        completeLoading(function(){log_message("simulatePost() Success !!");}, "¡Terminado!"); 
                    }, 4000);
                    
                setTimeout(function(){ displayHot() }, 5000);
            }

        </script>
        
    </head>
    
    <body>
        
        <div id="fb-root"></div>
        
        
        <div id="page">
            
            <div id="welcome">
                <div id="welcome-form">
                    <h2>¡Bienvenido a ConectaTec!</h2>
                    <h3>del Tecnológico de Monterrey</h3>
                    <img src="images/icons/png/64x64/sunny.png" />
                    <img src="images/icons/png/64x64/joystick.png" />
                    <img src="images/icons/png/64x64/movie.png" />
                    <img src="images/icons/png/64x64/iphone.png" />
                    <img src="images/icons/png/64x64/address_book.png" />
                    <img src="images/icons/png/64x64/phone_ringing.png" />
                    <p>Conectatec es una aplicación con la que podrás conocer personas como tú y con los cuales podrás convivir dentro del Tec de Monterrey</p>
                    <input type="submit" class="button" value="¡Conectar!" onclick="javascript:showSchoolForm();" />
                    <br/>
                </div>
            </div>
            
            <div id="main-content">
                
                <div>
                    
                    <div id="main-container">
                    
                        <h2>Semana de la Revolución Mexicana</h2>
                        
                        <div class="navigation_header">

                            <span class="navigation_header_button hot" onclick="javascript:displayHot()"><h5 style="color: #FFFFFF;">Hot</h5></span>
                            <span class="navigation_header_button post-page" onclick="javascript:displayPostPage()"><h5 style="color: #FFFFFF;">Publicar</h5></span>
                            <span class="navigation_header_button info" onclick="javascript:displayInfo()"><h5 style="color: #FFFFFF;">Información</h5></span>
                            <span class="navigation_header_button socializer" onclick="javascript:displaySocializer()"><h5 style="color: #FFFFFF;">Amigos</h5></span>

                        </div>
                        
                    </div>
                    
                    <div id="friend-content">
                        
                        <div>
                            <div class="post">
                                <div class="post-content">
                                    <img src="images/mexican_revolution/rm1.jpg"/>
                                </div>
                                <div class="post-info">
                                    <h3 style="color:#3B5998;">Esta es mi Tierra</h3>
                                    <div class="user">
                                        <img class="user-img" src="http://graph.facebook.com/mrxko/picture" />
                                        <div class="user-info">
                                            <span style="color: black;">por <a class="username" href="https://www.facebook.com/mrxko" target="_blank">Marko</a></span>
                                            <br/>
                                            <span style="color: black;">Escuela A</span>
                                        </div>
                                    </div>
                                    <p>
                                        <span class="post-date">hoy</span>
                                        <span class="post-likes">13</span>
                                        <span class="post-comments">2</span>
                                    </p>
                                    <div class="post-buttons">
                                        <div class="like-button">
                                            <a href="javascript:testLoading()">
                                                <span>Me Gusta!</span>
                                            </a>
                                        </div>
                                        <div class="comment-button">
                                             <a>
                                                <span>Comentar!</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="share-buttons">
                                    </div>
                                    <div class="post-tags">
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <br/>
                        </div>
                        
                        <div>
                            <div class="post">
                                <div class="post-content">
                                    <img src="images/mexican_revolution/rm2.jpg" />
                                </div>
                                <div class="post-info">
                                    <h3 style="color:#3B5998;">Pasado y Presente</h3>
                                    <div class="user">
                                        <img class="user-img" src="http://graph.facebook.com/soul248/picture" />
                                        <div class="user-info">
                                            <span style="color: black;">por <a class="username" href="https://www.facebook.com/soul248" target="_blank">Carlos</a></span>
                                            <br/>
                                            <span style="color: black;">Escuela B</span>
                                        </div>
                                    </div>
                                    <p>
                                        <span class="post-date">hoy</span>
                                        <span class="post-likes">11</span>
                                        <span class="post-comments">0</span>
                                    </p>
                                    <div class="post-buttons">
                                        <div class="like-button">
                                            <a href="javascript:testLoading()">
                                                <span>Me Gusta!</span>
                                            </a>
                                        </div>
                                        <div class="comment-button">
                                             <a>
                                                <span>Comentar!</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="share-buttons">
                                    </div>
                                    <div class="post-tags">
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <br/>
                        </div>
                        
                        <div>
                            <div class="post">
                                <div class="post-content">
                                    <img src="images/mexican_revolution/rm3.jpg" />
                                </div>
                                <div class="post-info">
                                    <h3 style="color:#3B5998;">Sangre Joven</h3>
                                    <div class="user">
                                        <img class="user-img" src="http://graph.facebook.com/faridh.mendoza/picture" />
                                        <div class="user-info">
                                            <span style="color: black;">por <a class="username" href="https://www.facebook.com/faridh.mendoza" target="_blank">Faridh</a></span>
                                            <br/>
                                            <span style="color: black;">Escuela C</span>
                                        </div>
                                    </div>
                                    <p>
                                        <span class="post-date">hace 2 días</span>
                                        <span class="post-likes">9</span>
                                        <span class="post-comments">0</span>
                                    </p>
                                    <div class="post-buttons">
                                        <div class="like-button">
                                            <a href="javascript:testLoading()">
                                                <span>Me Gusta!</span>
                                            </a>
                                        </div>
                                        <div class="comment-button">
                                             <a>
                                                <span>Comentar!</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="share-buttons">
                                    </div>
                                    <div class="post-tags">
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <br/>
                        </div>
                        
                        <div>
                            <div class="post">
                                <div class="post-content">
                                    <img src="images/mexican_revolution/rm4.jpg" />
                                </div>
                                <div class="post-info">
                                    <h3 style="color:#3B5998;">Mural de Libertad</h3>
                                    <div class="user">
                                        <img class="user-img" src="http://graph.facebook.com/carlos.mondragon/picture" />
                                        <div class="user-info">
                                            <span style="color: black;">por <a class="username" href="https://www.facebook.com/carlos.mondragon" target="_blank">Carlos</a></span>
                                            <br/>
                                            <span style="color: black;">Escuela D</span>
                                        </div>
                                    </div>
                                    <p>
                                        <span class="post-date">hace 3 días</span>
                                        <span class="post-likes">7</span>
                                        <span class="post-comments">2</span>
                                    </p>
                                    <div class="post-buttons">
                                        <div class="like-button">
                                            <a href="javascript:testLoading()">
                                                <span>Me Gusta!</span>
                                            </a>
                                        </div>
                                        <div class="comment-button">
                                             <a>
                                                <span>Comentar!</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="share-buttons">
                                    </div>
                                    <div class="post-tags">
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <br/>
                        </div>
                        
                    </div>
                    
                </div>
                
                <div id="publish-container">
                    
                    <div class="official-post-large">
                    
                        <div class="official-post-info-large">
                            <h3 style="color:#3B5998;">Publicar</h3>
                            
                            
                            <label><h4>Título</h4><br/>
                                <input type="text" class="input_text warning" id="title-upload" autocomplete="off" value="Escribe el título de tu publicación"/>
                            </label>

                            <label><h4>URL de la Foto </h4><br/>
                                <input type="text" class="input_text warning" id="picture-upload" autocomplete="off" value="Escribe el link de la imagen"  />
                                <input type="submit" class="button" value="Subir" />
                            </label>
                            
                            <label><h4>Descripción </h4><br/>
                                <input type="text" class="input_text warning" id="description-upload" autocomplete="off" value="Escribe una descripción"  />
                            </label>
                            
                            <input type="submit" class="button" value="¡Publicar!" onclick="javascript:simulatePost();" />
                            
                        </div>
                        
                        <br/>&nbsp;
                        <br/>&nbsp;
                        <br/>&nbsp;
                        
                        <a><h4>Ver las reglas de uso</h4></a>
                        
                    </div>
                    
                </div>
                
                <div id="info-container">
                    
                    <div class="official-post">
                        
                        <div class="official-post-info">
                            <h3 style="color:#3B5998;">Semana de... ¡¡ Fotos de la Revolución !!</h3>
                            
                            <div class="user">
                                <img class="user-img" src="http://graph.facebook.com/carlos.mondragon/picture" />
                                <div class="user-info">
                                    <span style="color: black;">por <a class="username" href="https://www.facebook.com/carlos.mondragon" target="_blank">Carlos</a></span>
                                </div>
                            </div>
                            
                            <hr/>
                            
                            <div class="post-text" style="color: black;">
                                
                                ¡Esta semana el Instituto Tecnológico y de Estudios Superiores de Monterrey celebra la Revolución Mexicana! 
                                Sube una foto alusiva al tema y su descripción y las tres mejores fotos seleccionadas por los usuarios y
                                por un jurado del ITESM ganarán tarjetas de regalo de iTunes.
                                
                                <br/>
                                <br/>
                                
                                <center>
                                    
                                    <img src="images/itunes_cards.png" />
                                    
                                </center>
                                
                            </div>
                            
                        </div>
                        
                        <div class="previous-posts">
                            
                            <h4>Concursos pasados</h4>
                            
                            <hr/>
                            
                            <ul style="color:black;">
                                
                                <li>Noviembre</li>
                                <li>Octubre</li>
                                <li>Septiembre</li>
                                
                            </ul>
                            
                        </div>
                        
                    </div>
                    <br/>
                    <br/>
                        
                </div>
                
                <div id="top_matches">
                    
                </div>
                
            </div>
            
            <div id="sun">
                <img src="images/sunnycloud.png" />
            </div>
            
            <div id="copyright">
            </div>
            
        </div>
        
        <div id="loading">
            <div id="loading-content">
                <h2 id="loading-message">¡Conectando con Facebook!</h2>
                <img id="loading-image" src="http://fc09.deviantart.net/fs70/f/2011/196/f/4/nyan_cat__d_by_alice2700-d3rrflu.gif" />
            </div>
        </div>
        
        <div id="school-form">
            
            <div id="school-form-container">
                
                <br/>
                <h3 style="color:#3B5998;">¡Algo sobre ti!</h3>
                
                <img src="images/icons/png/64x64/resume.png" />
                
                <p>¡Dános un poco de información de ti para conocerte mejor!</p>
                <label>
                    <input type="text" class="input_text warning" id="school" autocomplete="off" value="Escribe el nombre de tu escuela" onblur="if (this.value == ''){this.value = 'Escribe el nombre de tu escuela'; }" onfocus="if (this.value == 'Escribe el nombre de tu escuela') {this.value = '';}" />
                </label>

                <label>
                    
                    <select id="grade" class="selectbox warning">
                        
                        <option value="0" selected="selected" data-skip="1">¿En qué grado estás?</option>
                        <optgroup label="Secundaria">
                            <option value="1">Primero de Secundaria</option>
                            <option value="2">Segundo de Secundaria</option>
                            <option value="3">Tercero de Secundaria</option>
                        </optgroup>
                        <optgroup label="Preparatoria">
                            <option value="4">Primero de Preparatoria</option>
                            <option value="5">Segundo de Preparatoria</option>
                            <option value="6">Tercero de Preparatoria</option>
                        </optgroup>
                    </select>
                    
                </label>
                <br/>

                <input type="submit" class="button" value="¡Buscar!" onclick="javascript:fetchData();" />
                
            </div>
            
        </div>
        
    </body>
    
</html>
