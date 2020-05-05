/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

jQuery(document).ready(function ( $ ) {
	setTimeout(doWork, 10);

	function doWork() {
		var rows = $('.quota-row');

		rows.each(function (i, el) {
			var id = $(el).find('.row-id').val();
			var usage = $(el).find('.usage-outer');

			$.ajax({
				url      : $(el).attr('data-quota'),
				dataType : 'JSON',
				type     : 'GET',
				data     : {"id":id},
				success  : function ( data, textStatus, jqXHR ) {
					if (data.percent > 100) {
						data.percent = 100;
						usage.find('.usage-inner').addClass('max');
					}
					usage.prev('.usage-calculating').hide();
					usage.fadeIn();
					usage.find('.usage-inner').css('width', data.percent+"%");
				},
				error : function ( ) {
					usage.prev('.usage-calculating').hide();
					usage.next('.usage-unavailable').show();
				}
			});
		});
	};
});
