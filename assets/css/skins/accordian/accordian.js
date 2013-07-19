jQuery(document).ready(function($) {

	$('body').append('<div class="se-modal"><div class="se-modal-inner"></div></div>');

	$('.se-modal').live('click', function() {
		$('.se-modal').fadeOut('fast');
		return false;
	});

	$('.se-event-title').live('click', function() {
		
		var $this = $(this).parent();
		if ($('.se-open').length > 0 && !$this.hasClass('se-open')) {

			console.log($this);
			$('.se-open').children('.se-event-details').slideUp('fast', function() {
					console.log('here1');
					$('.se-event').removeClass('se-open');
					$this.addClass('se-open');
					$this.children('.se-event-details').slideDown('fast');
			});
		} else if ($this.hasClass('se-open')) {
			$this.children('.se-event-details').slideUp('fast', function() {
				$('.se-event').removeClass('se-open');
			});
		} else {
			console.log($this);
			$this.addClass('se-open');
			$this.children('.se-event-details').slideDown('fast');
		}
		return false;
	});

});