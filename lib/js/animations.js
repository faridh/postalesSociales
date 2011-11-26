var isLoading = false;


function setupAnimations()
{
    $("#welcome-form").css("opacity","0");
    $("#sun").css("opacity","0");
    $("#school-form").css("opacity","0");
    //$("#school-form").css("opacity","0");
    $("#welcome").css("display","table");
    $("#sun").css("display","inherit");
}


function showWelcomeForm()
{
    $("#welcome-form").animate({"opacity":"1"},2000);
    $("#sun").animate({"opacity":"1"},2000, function(){
        $("#loading-message").html("");
        $("#loading-image").css("visibility","hidden");
    });
}

function showSchoolForm()
{
    $("#welcome-form").animate({"opacity":"0"},500);
    $("#sun").animate({"opacity":"0"},500, function(){
        $("#school-form").css("display","inherit");
        $("#school-form").css("top","50px");
        $("#loading").fadeIn(1000);
        $("#welcome-form").css("display","none");
        $("#school-form").animate({"opacity":"1", "top":"10px"}, 1000);
    });
}

function hideSchoolForm()
{
    $("#loading-message").html("Conectando con el servidor");
    $("#loading-message").css("opacity","0");
    
    $("#loading-image").css("opacity","0");
    $("#loading-image").css("visibility","visible");

    $("#school-form").animate({"opacity":"0", "top":"-50px"}, 1000, function(){
        $("#loading-message").animate({"opacity":"1"},1000);
        $("#loading-image").animate({"opacity":"1"},1000);
        $("#school-form").css("display","none");
    });
}

function showLoading(message)
{
    if(!isLoading)
    {
        $("#loading-message").html(message);
        $("#loading-image").css("opacity","0");
        $("#loading-image").css("visibility","visible");
        $("#loading").fadeIn(1000);
        $("#loading-message").animate({"opacity":"1"},1000);
        $("#loading-image").animate({"opacity":"1"},1000);
        isLoading = true;
    }
    else
    {
        $("#loading-message").html(message);
    }
   
}

function completeLoading(callback, message)
{
    if(message && typeof(message) === 'string')
    {
        $("#loading-message").html(message);
    }
    else if(callback && typeof(callback) === 'string')
    {
        $("#loading-message").html(callback);
    }
    
    $("#loading").fadeOut(1000, function(){
        $("#loading-message").html("");
        $("#loading-image").css("visibility","hidden");
        if(callback && typeof(callback) === 'function')
        {
            callback();
        }
        isLoading = false;
    });
    $("#loading-message").animate({"opacity":"0"},1000);
    $("#loading-image").animate({"opacity":"0"},1000);
}

$(window).load(function(){
   setupAnimations(); 
});
