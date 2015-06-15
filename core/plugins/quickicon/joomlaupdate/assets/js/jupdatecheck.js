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
				$('#plg_quickicon_joomlaupdate').find('img').attr('src',plg_quickicon_joomlaupdate_img.ERROR);
				$('#plg_quickicon_joomlaupdate').find('span').html(plg_quickicon_joomlaupdate_text.ERROR);
			}
			if (updateInfoList instanceof Array) {
				if (updateInfoList.length < 1) {
					// No updates
					$('#plg_quickicon_joomlaupdate').find('img').attr('src',plg_quickicon_joomlaupdate_img.UPTODATE);
					$('#plg_quickicon_joomlaupdate').find('span').html(plg_quickicon_joomlaupdate_text.UPTODATE);
				} else {
					var updateInfo = updateInfoList.shift();
					if (updateInfo.version != plg_quickicon_jupdatecheck_jversion) {
						var updateString = plg_quickicon_joomlaupdate_text.UPDATEFOUND.replace("%s", updateInfo.version+"");
						$('#plg_quickicon_joomlaupdate').find('img').attr('src',plg_quickicon_joomlaupdate_img.UPDATEFOUND);
						$('#plg_quickicon_joomlaupdate').find('span').html(updateString);
					} else {
						$('#plg_quickicon_joomlaupdate').find('img').attr('src',plg_quickicon_joomlaupdate_img.UPTODATE);
						$('#plg_quickicon_joomlaupdate').find('span').html(plg_quickicon_joomlaupdate_text.UPTODATE);
					}
				}
			} else {
				// An error occured
				$('#plg_quickicon_joomlaupdate').find('img').attr('src',plg_quickicon_joomlaupdate_img.ERROR);
				$('#plg_quickicon_joomlaupdate').find('span').html(plg_quickicon_joomlaupdate_text.ERROR);
			}
		},
		error: function(data, textStatus, jqXHR) {
			// An error occured
			$('#plg_quickicon_joomlaupdate').find('img').attr('src',plg_quickicon_joomlaupdate_img.ERROR);
			$('#plg_quickicon_joomlaupdate').find('span').html(plg_quickicon_joomlaupdate_text.ERROR);
		},
		url: plg_quickicon_joomlaupdate_ajax_url
	};

	setTimeout(function(){
		$.ajax({
			url  : plg_quickicon_joomlaupdate_ajax_url + 'eid=700&cache_timeout=3600',
			type : 'GET'
		});
	}, 2000);
});