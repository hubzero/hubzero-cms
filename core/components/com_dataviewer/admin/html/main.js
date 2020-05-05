/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

db = {};
var db_back_link = '/administrator/index.php?option=com_dataviewer';

db.dbJQ = jQuery;

(function($) {

	setInterval(function() {
		$.get('index.php');
	}, 1000 * 60 * 10);

}(db.dbJQ));


db.dbJQ(document).ready(function($) {

	/* Back Link */
	$('#toolbar-back a.toolbar').removeAttr('onclick').attr('href', db_back_link);
	
});
