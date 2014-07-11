/**
 * @package     hubzero-cms
 * @file        components/com_feedaggregator/assets/js/posts.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function (jq) {
	var $ = jq;

	//enable fancyboxes
	if (jQuery.fancybox) {
		$('.fancybox-inline').fancybox({
			'transitionIn' : 'elastic',
			'transitionOut' : 'elastic'
		});
	}

	// handles the changing of state of the button
	$('.actionBtn').on('click', function() {
		//var post = changeState(this);
		var self = $(this),
			post = {
				"record_id": self.data('id'),
				"action": self.data('action')
			};

		if (post.action == 'remove') {
			$.post(
				"/index.php?option=com_feedaggregator&task=updateStatus&no_html=1", {
					'id': post.record_id,
					'action': post.action
				},
				function (data) {
					if (jQuery.fancybox) {
						$.fancybox.next();
					}
					$("#row-" + post.record_id).attr('style','background-color:red');
					$("#row-" + post.record_id).remove();
				}
			);
		} else {
			$.post(
				"/index.php?option=com_feedaggregator&task=updateStatus&no_html=1", {
					'id': post.record_id,
					'action': post.action 
				},
				function (data) {
					if (jQuery.fancybox) {
						$.fancybox.next();
					}
					if (post.action == "mark") {
						$('#status-' + post.record_id).text('under review');
						$('#status-' + post.record_id).attr('style','color: purple');
					} else if (post.action == "approve") {
						$('#status-' + post.record_id).text('approved');
						$('#status-' + post.record_id).attr('style','color: green');
					}
				}
			);
		}

		$('.btnGrp' + post.record_id).each(function(){
			if ($(this).prop('disabled')) {
				$(this).removeAttr('disabled');
			}
		});
		$('#' + post.action + '-' + post.record_id).attr('disabled','disabled');
		$('#' + post.action + '-prev-' + post.record_id).attr('disabled','disabled');
	}); //end button pressing
}); // end ready
