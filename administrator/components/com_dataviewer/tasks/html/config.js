/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando sudheera@xconsole.org
 * @copyright   Copyright 2005-2011,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */


db.dbJQ(document).ready(function($) {

	var editor = ace.edit('db-conf-editor');
	editor.getSession().setUseSoftTabs(false);
	editor.setTheme('ace/theme/github');
	editor.getSession().setMode('ace/mode/json');
	editor.setFontSize('16px');

	$('#db-config-update').on('click', function() {
		$('#db-conf-update-form input[name="conf_text"]').val(editor.getSession().getValue());
		$('#db-conf-update-form').submit();
	});


	var viewer = ace.edit('db-conf-viewer');
	viewer.getSession().setUseSoftTabs(false);
	viewer.setTheme('ace/theme/github');
	viewer.getSession().setMode('ace/mode/json');
	viewer.setFontSize('16px');
	viewer.setReadOnly(true);

	$('#db-config-view').on('click', function() {
		var v = viewer;
		$.get($(this).data('link'), function(data) {
			v.getSession().setValue(data)
			$('#dv-view-conf').dialog({
				title: "Current configuration for the data viewer for this database",
				modal: true,
				height: 600,
				width: 800
			});
		});
	});
});
