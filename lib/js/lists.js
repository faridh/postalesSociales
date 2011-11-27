function fillList()
{
    console.log('Filling friend list...');
    /*
    for(var friend in friendsList.data)
    {
        friend = friendsList.data[friend];
        $('#list').append('<div class="option" data-fid="'+friend.id+'" data-name="'+friend.name+'">'+
        '<div class="checkbox checked"></div>'+
        '<img class="profile" src="http://graph.facebook.com/'+friend.id+'/picture">'+
        friend.name+'</div>');  
    }*/
    FriendList.init();
}

    (function( $ ){
        $.fn.childFilter = function(_selector, _attr, _term){
        /* fsFilter
            Filter the users by name */ 
            var $set = $(this).find(_selector);
            $set.hide();
            // Regex filter the name fields for all the people
            $set.filter(function(){
                return $(this).attr(_attr).match(new RegExp(_term, 'gi'));
            }).show();
            return ($(this));
    }})( jQuery );


function sortByName(a, b) {
    // Necessary to order the FB friends list
    var x = a.name;
    var y = b.name;
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}

$(document).ready(function(){

    $("#card_form_submit, #submit_again").click(function(){
        var fl = FriendList.getResults();
        FacebookHandler.addUsers(fl);

        return false;
    });
    $("#show_added").bind("click",function(){
        $("#added_list").slideToggle(500,function(){
            FriendList.fixHeight();
        });
    });

    $("#card_form").bind("ajax:success", function(data, status, xhr) {
       // FriendList.init(user);
    });

    var sentList = [503195234, 511368992, 500535225, 100000153486236];
    sentList = [];
    FriendList.addSent(sentList);
    //FriendList.init();

});

var FacebookHandler = {

    addUsers : function(_friendList) {
        if(_friendList && _friendList.length > 50){
            _friendList = _friendList.slice(0,50);
        }

    var cid = $.trim('3'),
    pid =  $.trim(''),
    data = {
      card_id: '2421'
    };

    //If cid and pid are present send
    if(cid) data['cid'] = cid;
    if(pid) data['pid'] = pid;

        $("#loading_mask").fadeIn();

        FB.ui({
            method: 'apprequests',
            title: "Send holiday cards on Facebook",
      message: "Carlos just sent you a holiday card on Facebook!  Accept this card to see the photo.",
      to: _friendList,
      data : data

    }, FacebookHandler.requestCallback);
  },

  requestCallback: function(request) {
    $("#loading_mask").fadeOut();

    //If there was an error we return and don't do anything
    if (!request || !request.to) return;

    //Paste the invites into the form
    $("#invite_ids").val(request.to.join(","));
    FriendList.addSent(request.to);

    $("#list").slideUp(400, function(){
        FriendList.init();
        $("#card_form").submit();
    });


  }

}


var FriendList = {

    SELECTOR : "#list",
    PROGRESS_SELECTOR : "#progress_container",
    ADDED_SELECTOR : "#added_list",
    CANDIDATE_SELECTOR : "#select_from",
    AUTO_SELECT_FIRST : 25000,
    
    TEMPLATES : {
        friendOption : "<div class='option'></div>",
        friendPhoto: "<img class='profile'>",
        removeButton : "<div class='remove'></div>",
        checkbox : "<div class='checkbox'></div>"
    },
    ATTRIBUTES : {
        fid : "data-fid",
        name : "data-name"
    },
    sentCache : {},

    init : function(user){      
        $.ajax({
            type: 'GET',
            url: "https://graph.facebook.com/" + userObject.id + "/friends&access_token="+ accessToken,
            contentType: 'application/json; charset=UTF-8',
            dataType: "jsonp",
            success: 
                function(json){
                    console.log('DUNN');
                    console.log(json);
                    FriendList.clear();
                    FriendList.addFriends(friendsList.data);
                    //FriendList.addFriends(json.sort(sortByName));
                    $("#list").slideDown(300,function(){
                        FriendList.fixHeight();
                    });
                    FriendList.activate();
                    FriendList.updateCount();
                    FriendList.showProgress(true);
                    FriendList.fixHeight();
                    $("#loading_mask").fadeOut();
                }
            ,
            error: function (xhr, textStatus, errThrown) {
                if (textStatus == 'parsererror') {
                    var json;
                    try { json = eval('(' + xhr.responseText + ')'); }
                    catch (e) { }
                //ProcessResults(json);
                }
            }
        });
        
    },

    addSent : function(sentList){
        for(var i = 0; i < sentList.length; i ++){
            this.sentCache[sentList[i]] = true;
        };
    },

    updateCount : function(){
        var len = $("#friend_list_container #list").find(".option").length, 
            max;
        max = 50 - len;
        $("#friend_count").html(len);
        $("#remaining").html(max);
    },

    clear : function(){
        var $list = $(this.SELECTOR);
        var $addedList = $(this.ADDED_SELECTOR);
        var $selectPool = $(this.CANDIDATE_SELECTOR);
        $list.html("");
        $addedList.html("");
        $selectPool.html("");
    },

    addFriends : function(_friends){    
            console.log('adding friends');
            console.log(_friends);
            var i, 
            len = _friends.length, 
            $list = $(this.SELECTOR),
            $addedList = $(this.ADDED_SELECTOR),
            $selectPool = $(this.CANDIDATE_SELECTOR),
            autodata = [],
            recommended = 0,
            addedCache = document.createDocumentFragment(),
            poolCache = document.createDocumentFragment();

        for( i=0; i<len; i++){
        // Each friend can go in one of two places
            if(this.sentCache[_friends[i].id]){
            // Already sent a card
                addedCache.appendChild(this.newFriend(_friends[i])[0]);
            } else {
                // The 'recommended' auto-selected friends
                autodata.push({
                    label : _friends[i].name,
                    value : _friends[i].id
                });
                recommended ++;
                var $f = this.newFriend(_friends[i]);
                if (recommended <= this.AUTO_SELECT_FIRST){
                    $f.find(".checkbox").addClass("checked");
                }
                poolCache.appendChild($f[0]);
            }
        }
        $list.append(poolCache);
        $addedList.append(addedCache);
    },
    
    newFriend : function(_friend){
        var $f = $(this.TEMPLATES.friendOption);
        $f.html(_friend.name);
        $f.prepend($(this.TEMPLATES.friendPhoto).attr("src","http://graph.facebook.com/" + _friend.id + "/picture"));
        $f.prepend(this.TEMPLATES.checkbox);
        $f.attr(this.ATTRIBUTES.fid, _friend.id);
        $f.attr(this.ATTRIBUTES.name, _friend.name);
        return $f;
    },
    
    activate : function(){

        $("#uncheck_all").unbind("click").bind("click",function(){
            $("#list").find(".checkbox").removeClass("checked");
            $("#instructions").html("Check the friends you want to send this card to");
        });
        
        $("#list").find(".option").unbind("click").bind("click",function(){
            $(this).find(".checkbox").toggleClass("checked");
        });
        
        $("#search").unbind("click").bind("change keyup keydown", function(){
$("#list").childFilter( ".option", "data-name", $(this).val());
        });
 
    },
    
    getResults : function(){
        var max = 2000;
        var friends = [];
        $.each($(this.SELECTOR).find(".checked"),function(_i, _f){
            var $f = $(_f).closest(".option");
            friends.push($f.attr(FriendList.ATTRIBUTES.fid));
            if(friends.length >= max){
                return false;
            }
        });
        return friends;
    },
    
    showProgress : function(_animate){
        var numAdded = $("#added_list").find(".option").length,
            total = 0,
            percent = 0;


        if(numAdded>0){
            $("#progress_container").slideDown(300,function(){
                FriendList.fixHeight();
            });
        }

        total = $("#friend_list_container").find(".option").length +
                $("#added_list").find(".option").length;
        percent = Math.round(100*numAdded/total);
        
        var $p = $("#progress_container").find("#progress");
        var newW = Math.round($p.parent().width() * percent/100);
        if(_animate){
            $p.animate({'width': newW });
        } else {
            $p.css('width', (newW));
        }
        $p.html(percent + "%");

        $("#progress_summary").html(numAdded);
        $("#total_remaining").html(total - numAdded);
    },
    
    fixHeight : function(){
        var height = Math.max($("#friend_list_container").height(),$("#card_container").height());
        $("#content").height(height);
        FB.Canvas.setSize({ width: 760, height: $("body").outerHeight() + 50 });
    }
    
};
