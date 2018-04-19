/**
 * @package     hubzero-cms
 * @file        plugins/resources/collections/assets/js/collections.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */
$(function(){
	$('#add-collection').fancybox();
	$('body').on('change', '#collectionForm #pid', function(e){
		var selected = $(this).val();
		if (selected.length)
		{
			$('#new-series-add').hide();
			$('#new-series-add').prev('.col').find('.or').hide();
			$('input[name="resource-title"]').val('');
		}		
		else
		{
			$('#new-series-add').prev('.col').find('.or').show();
			$('#new-series-add').show();
		}
	});
	$('#collectionForm').submit(function(e){
		e.preventDefault();
		var that = $(this);
		$.ajax({
			url: that.attr('action'),
			data: that.serialize() + '&no_html=1',
			success: function(response){
				if (!response.success) {
					that.parent().html('<p class="error" style="margin-left: 1em; margin-right: 1em;">' + response.message + '</p>')
				} else {
					that.parent().html('<p class="success" style="margin-left: 1em; margin-right: 1em;">' + response.message + '</p>');
				}
				setTimeout(function(){
					$.fancybox.close();
					location.reload();
				}, 2 * 1000);
			}

		});
	});
});
