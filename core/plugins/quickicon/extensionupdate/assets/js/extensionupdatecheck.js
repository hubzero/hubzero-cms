/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

jQuery(document).ready(function(jq){
	$.ajaxSetup({
		dataType   : "json",
		type       : 'POST',
		cache      : false,
		success    : function(data, textStatus, jqXHR) {
			try {
				var updateInfoList = JSON.decode(msg, true);
			} catch(e) {
				// An error occured
				$('#plg_quickicon_extensionupdate').find('span').html(plg_quickicon_extensionupdate_text.ERROR);
			}

			if (updateInfoList instanceof Array) {
				if (updateInfoList.length < 1) {
					// No updates
					$('#plg_quickicon_extensionupdate').find('span').html(plg_quickicon_extensionupdate_text.UPTODATE);
				} else {
					var updateString = plg_quickicon_extensionupdate_text.UPDATEFOUND.replace("%s", updateInfoList.length);
					$('#plg_quickicon_extensionupdate').find('span').html(updateString);
				}
			} else {
				// An error occured
				$('#plg_quickicon_extensionupdate').find('span').html(plg_quickicon_extensionupdate_text.ERROR);
			}
		},
		error: function(data, textStatus, jqXHR) {
			// An error occured
			$('#plg_quickicon_extensionupdate').find('span').html(plg_quickicon_extensionupdate_text.ERROR);
		}
	});

	$.ajax({
		url  : plg_quickicon_extensionupdate_ajax_url + 'eid=0&skip=700',
		type : 'GET'
	});
});