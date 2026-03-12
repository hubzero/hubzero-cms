/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	if (task == 'preview') {
		var id = '',
			ids = document.getElementsByName('id[]');
		for (var i = 0; i < ids.length; i++) {
			if (id == '' && ids[i].type == 'checkbox' && ids[i].checked) {
				id = parseInt(ids[i].value);
			}
		}

		HUB.Administrator.Newsletter.newsletterPreview(id);
		return;
	}

	if (task == 'stop') {
		var message = document.getElementById('admin-form').getAttribute('data-confirm-stop');
		if (!confirm(message)) {
			return;
		}
	}

	if (task == 'dosendnewsletter') {
		if (!HUB.Administrator.Newsletter.sendNewsletterCheck()) {
			return;
		}

		if (!HUB.Administrator.Newsletter.sendNewsletterDoubleCheck()) {
			return;
		}
	}

	var afrm = document.getElementById('adminForm');

	if (afrm) {
		Hubzero.submitform(task, afrm);
		return;
	}

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

if (!HUB) {
	var HUB = {};
}

if (!HUB.Administrator) {
	HUB.Administrator = {};
}

HUB.Administrator.Newsletter = {

	initialize: function() {
		var scheduler = document.getElementById('scheduler');

		var addBtn = document.getElementById('add-newsletter');
		if (addBtn) {
			addBtn.addEventListener('click', function (e) {
				e.preventDefault();
				Hubzero.submitbutton('add');
			});
		}

		var manageBtn = document.getElementById('btn-manage');
		if (manageBtn) {
			manageBtn.addEventListener('click', function (e) {
				e.preventDefault();
				Hubzero.submitbutton('manage');
			});
		}

		if (scheduler) {
			var schedulerAlt = document.getElementById('scheduler-alt');
			if (schedulerAlt) {
				schedulerAlt.style.display = 'none';
			}

			document.querySelectorAll('input[name=scheduler]').forEach(function (radio) {
				radio.addEventListener('change', function () {
					if (this.value == 0) {
						if (schedulerAlt) schedulerAlt.style.display = '';
					} else {
						if (schedulerAlt) schedulerAlt.style.display = 'none';
					}
				});
			});

			// datepicker removed — use native date input or flatpickr
		}

		HUB.Administrator.Newsletter.mailingListAndCount();
	},

	sendNewsletterCheck: function() {
		var scheduler = document.querySelector('input[name=scheduler]:checked'),
			mailinglist = document.getElementById('mailinglist');

		if (scheduler && scheduler.value == '0') {
			var scheduler_date = document.getElementById('scheduler_date'),
				scheduler_date_hour = document.getElementById('scheduler_date_hour'),
				scheduler_date_minute = document.getElementById('scheduler_date_minute'),
				scheduler_date_meridian = document.getElementById('scheduler_date_meridian');

			if (!scheduler_date || !scheduler_date_hour || !scheduler_date_minute || !scheduler_date_meridian
				|| scheduler_date.value == '' || scheduler_date_hour.value == ''
				|| scheduler_date_minute.value == '' || scheduler_date_meridian.value == '') {
				alert('You must fill out all the newsletter scheduling fields.');
				return false;
			}
		}

		if (!mailinglist || mailinglist.value == '' || mailinglist.value == 0) {
			alert('You must select a mailing list to send the newsletter to.');
			if (mailinglist) mailinglist.focus();
			return false;
		}

		return true;
	},

	sendNewsletterDoubleCheck: function() {
		var message = '',
			message_datetime = '',
			scheduler = document.querySelector('input[name=scheduler]:checked'),
			newsletterNameEl = document.getElementById('newsletter-name'),
			newsletterName = newsletterNameEl ? newsletterNameEl.value : '',
			scheduler_date = document.getElementById('scheduler_date'),
			scheduler_date_hour = document.getElementById('scheduler_date_hour'),
			scheduler_date_minute = document.getElementById('scheduler_date_minute'),
			scheduler_date_meridian = document.getElementById('scheduler_date_meridian');

		if (scheduler && scheduler.value == 1) {
			message_datetime = 'Now (Your email might take up to one hour to send)';
		} else {
			message_datetime = (scheduler_date ? scheduler_date.value : '')
				+ ' at ' + (scheduler_date_hour ? scheduler_date_hour.value : '')
				+ ':' + (scheduler_date_minute ? scheduler_date_minute.value : '')
				+ ' ' + (scheduler_date_meridian ? scheduler_date_meridian.value : '');
		}

		message  = "Are you sure you want to send the following newsletter? \n\n";
		message += newsletterName.replace(/\s+/g, ' ');
		message += "\n\n---------- On ----------\n\n";
		message += message_datetime;

		if (!confirm(message)) {
			return false;
		}

		return true;
	},

	newsletterPreview: function(id) {
		var url = 'index.php?option=com_newsletter&task=preview&id=' + id + '&tmpl=component';
		window.open(url, 'newsletter_preview', 'width=800,height=600,scrollbars=yes,resizable=yes');
	},

	mailingListAndCount: function() {
		var mailinglist = document.getElementById('mailinglist'),
			mailinglistCount = document.getElementById('mailinglist-count');

		if (mailinglistCount) {
			mailinglistCount.style.display = 'none';
		}

		if (mailinglist) {
			mailinglist.addEventListener('change', function () {
				var value = this.value;
				if (value != '' && value != 0) {
					fetch('index.php?option=com_newsletter&controller=mailinglist&task=emailcount&mailinglistid=' + value + '&no_html=1', {
						headers: { 'Accept': 'application/json' }
					})
					.then(function (response) { return response.json(); })
					.then(function (data) {
						var emailCount = data.length;

						if (mailinglistCount) {
							mailinglistCount.style.display = '';

							var counter = document.getElementById('mailinglist-count-count');
							if (counter) {
								counter.innerHTML = emailCount;
							}
						}

						var emailsEl = document.getElementById('mailinglist-emails');
						if (emailsEl) {
							emailsEl.innerHTML = '<br />--------------------------------<br />' + data.join('<br />');
						}
					});
				} else {
					if (mailinglistCount) {
						mailinglistCount.style.display = 'none';
					}
				}
			});
		}
	}
};

document.addEventListener('DOMContentLoaded', function () {
	HUB.Administrator.Newsletter.initialize();

	// Preview iframe
	var previewIframe = document.getElementById('preview-iframe');

	if (previewIframe) {
		var previewCodeContainer = document.getElementById('preview-code');
		var previewCode = previewCodeContainer ? previewCodeContainer.querySelector('table') : null;

		if (previewCode) {
			previewIframe.style.width = (previewCode.getAttribute('width') || '600') + 'px';
			previewIframe.style.height = (previewCode.getAttribute('height') || '400') + 'px';

			var iframeDoc = previewIframe.contentDocument || previewIframe.contentWindow.document;
			iframeDoc.open();
			iframeDoc.write(previewCode.outerHTML);
			iframeDoc.close();
		}
	}
});
