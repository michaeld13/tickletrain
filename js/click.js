jQuery(function(){
	$(document).click(function(event){
		if (!$(event.target).closest(".popup-holder").length){
			$(".popup-holder").removeClass("active");
		}
		if ($(event.target).closest("#pp1").length){
			$("#pp2").removeClass("active");
		}
		if ($(event.target).closest("#pp2").length){
			$("#pp1").removeClass("active");
		}
	});
	
	/*$('tr').click(function(){
		if ( !$(this).hasClass('show') ) {
			$(this).addClass('show');
		}
		else {
			$(this).removeClass('show');
		}
	});*/
});