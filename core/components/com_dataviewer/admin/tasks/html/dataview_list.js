/**
 * @package     hubzero.cms.admin
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando sudheera@xconsole.org
 * @copyright   Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT or later; see LICENSE.txt
 */


db.dbJQ(document).ready(function($) {

	/* New Dataview */
	$('#toolbar-new a.toolbar').removeAttr('onclick').on('click', function() {
		
		$('#db-dd-new').dialog({
			height: 300,
			width: 600,
			modal: true
		});		
		return false;
	});


	$('.db-dd-remove-link').on('click', function() {
		var dd = $(this).data('dd');
		var title = $(this).closest('tr').find('td:nth(1)').text();
		if (confirm('Are you sure you want to remove "' + title + '" Dataview?')) {
			$('#db-dd-remove-frm input[name="dd_name"]').val(dd);
			$('#db-dd-remove-frm').submit();
		}

		return false;
	});
});
