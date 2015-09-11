/**
 * @package     hubzero.cms.admin
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando sudheera@xconsole.org
 * @copyright   Copyright 2010-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT or later; see LICENSE.txt
 */


db.dbJQ(document).ready(function($) {

	/* Tabs */
	$('#tabs').tabs();

	$('#db-tables-dv-iframe').on('load', function() {
		$('#tabs').on('tabsactivate', iframe_fix);
	});

	function iframe_fix() {
		if ($('#tabs').tabs('option', 'active') === 0) {
			$('#db-tables-dv-iframe')[0].contentWindow.dv_table.fnDraw();
			$('#db-tables-dv-iframe')[0].contentWindow.scroll(1,0);		
			$('#tabs').off('tabsactivate', iframe_fix);
		}
	}


	/* JSON Editor */
	var editor = ace.edit('db-dd-editor');
	editor.getSession().setUseSoftTabs(false);
	editor.setTheme('ace/theme/github');
	editor.getSession().setMode('ace/mode/json');
	editor.setFontSize('16px');
	editor.setReadOnly(true);

	/* PHP Editor */
	var editor_php = ace.edit('db-dd-editor-php');
	editor_php.getSession().setUseSoftTabs(false);
	editor_php.setTheme('ace/theme/ambiance');
	editor_php.getSession().setMode('ace/mode/php');
	editor_php.setFontSize('16px');
	editor_php.setShowPrintMargin(false);
	editor_php.getSession().setValue($('#db-dd-source-php').val());
	

	$('#db-dd-update').on('click', function() {
		$('#db-dd-update-form input[name="dd_text"]').val(editor_php.getSession().getValue());
		$('#db-dd-update-form').submit();
	});

	
});
