$(function() {
	$('table tr').each(function() {
		if ($(this).next().hasClass('h_txt-holder')) {
			$(this).addClass('no_b');
			//$(this).next().hide();
			$(this).mouseenter(function() {
				$(this).next().addClass('hover');
			}).mouseleave (function() {
				var self = $(this);
				function fun() {
					if (!self.next().hasClass('hover2')) {
						self.next().removeClass('hover');
					}
					clearTimeout(_to);
				}
				_to = setTimeout(fun, 12);
				
			});
			/*$(this).click(function() {
				if (!$(this).next().hasClass('visible')) {
					$(this).next().addClass('visible');
				} else {
					$(this).next().removeClass('visible')
				}
			});*/
			$(this).next().hover(function(){
				$(this).prev().addClass('hover');
				$(this).addClass('hover2');
			}, function() {
				$(this).prev().removeClass('hover');
				$(this).removeClass('hover2');
				if ($(this).hasClass('hover')) {
					$(this).removeClass('hover');
				}
			});
			/*$(this).next().click(function() {
				if (!$(this).hasClass('visible')) {
					$(this).show().addClass('visible');
				} else {
					$(this).hide().removeClass('visible')
				}
			});*/
		}
		if ($(this).hasClass('light') && $(this).next().hasClass('h_txt-holder')){
			$(this).next().addClass('h_txt-holder-light');
		}
	});
});