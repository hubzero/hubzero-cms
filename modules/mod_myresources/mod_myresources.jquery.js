/**
 * @package     hubzero-cms
 * @file        modules/mod_myresources/mod_myresources.js
 * @copyright   Copyright 2005-2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	if (this.indexOf('?') == -1) {
		return this + '?no_html=1';
	} else {
		return this + '&no_html=1';
	}
};

jQuery(document).ready(function(jq){
	var $ = jq,
		form = $('#myresources-form');

	if (form.length <= 0) {
		return;
	}

	var url = form.attr('action').nohtml() + '&action=saveparams&update=1&mid=' + form.attr('data-module') + '&uid=' + $('#uid').val();

	$('#myresources-sort').on('change', function (e) {
		$.get(url + '&' + form.serialize(), {}, function(data) {
			$('#myresources-content').html(data);
		});
	});

	$('#myresources-limit').on('change', function (e) {
		$.get(url + '&' + form.serialize(), {}, function(data) {
			$('#myresources-content').html(data);
		});
	});
});

