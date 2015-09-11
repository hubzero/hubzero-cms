/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando srf@xconsole.org
 * @copyright   Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT or later; see LICENSE.txt
 */

jQuery(document).ready(function($) {

	$('#dv_gallery_list img').click(function() {
		var idx = $('#dv_gallery_list img').index(this);
		$('#dv_gallery_viewer img').hide();
		$('#dv_gallery_viewer img:eq(' + idx + ')').fadeIn('slow');
		if($(this).attr('title') !== '') {
			$('#dv_gallery_desc').html($(this).attr('title'));
		} else {
			$('#dv_gallery_desc').html('No description available.');
		}
		$('#dv_gallery_dl_image a').attr('href', $('#dv_gallery_viewer img:eq(' + idx + ')').parent().attr('href'));
	});

	$('#dv_gallery_list img:eq(0)').trigger('click');

	$("#color").buttonset();
	$("#description").button();

	$('input[name=color]:radio').click(function() {
		$('#dv_gallery_viewer').css('background-color', $('input[name=color]:radio:checked').val());
	});

	$('#description').click(function() {
		if ($(this).attr('checked') === true) {
			$('#dv_gallery_desc').show('fade');
		} else {
			$('#dv_gallery_desc').hide('fade');
		}
	});

});
