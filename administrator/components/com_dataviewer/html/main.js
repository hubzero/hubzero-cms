/**
 * @package     hubzero.cms.site
 * @subpackage  com_dataviewer
 *
 * @author      Sudheera R. Fernando sudheera@xconsole.org
 * @copyright   Copyright 2005-2011,2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3 or later; see LICENSE.txt
 */

db = {};
var db_back_link = '/administrator/index.php?option=com_dataviewer';

db.dbJQ = jQuery.noConflict();

(function() {

	setInterval(function() {
		db.dbJQ.get('index.php');
	}, 1000 * 60 * 10);

}());


db.dbJQ(document).ready(function($) {

	/* Back Link */
	$('#toolbar-back a.toolbar').removeAttr('onclick').attr('href', db_back_link);
	
});
