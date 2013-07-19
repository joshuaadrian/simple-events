jQuery(document).ready(function($) {

	$('.se_pagination li a').live('click', function() {
		if (!$(this).parent().hasClass('se-active')) {
			$('.se_pagination li').removeClass('se-active');
			$('.se_content li').removeClass('se-active');
			$(this).parent().addClass('se-active');
			$($(this).attr('href')).addClass('se-active');
		}
		return false;
	});

});