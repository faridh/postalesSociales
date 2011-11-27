function fillList()
{
    console.log('Filling friend list...');

    for(var friend in friendsList.data)
    {
        friend = friendsList.data[friend];
        $('#list').append('<div class="option" data-fid="'+friend.id+'" data-name="'+friend.name+'">'+
        '<div class="checkbox checked"></div>'+
        '<img class="profile" src="http://graph.facebook.com/'+friend.id+'/picture">'+
        friend.name+'</div>');  
    }
}
