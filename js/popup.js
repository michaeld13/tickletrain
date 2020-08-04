$(document).ready(function(){
    $(".popup-holder").click(function(){
        if ($(this).hasClass("active")){
            $(this).removeClass("active");
            $(this).find(".pop_up").hide();
        }else{
            $(this).siblings(".active").find(".pop_up").hide();
            $(this).siblings(".active").removeClass("active");
            $(this).addClass("active");
            $(this).find(".pop_up").show();
        }
    });
    $("a.delete_link").click(function(){
        mconfirm("Are you sure you want to delete this Tickle?", $(this).attr("href"));
        return false;
    });
});
