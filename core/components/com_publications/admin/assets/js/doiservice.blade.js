/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var DATACITE = 2;
var EZID = 1;
var NONE = 0;

function showEl(id) {
	var el = document.getElementById(id);
	if (el) {
		el.style.display = '';
	}
}

function hideEl(id) {
	var el = document.getElementById(id);
	if (el) {
		el.style.display = 'none';
	}
}

function updateDoiFields(val) {
	if (val == DATACITE) {
		showEl('doi_shoulder');
		showEl('hzform_doi_shoulder');
		hideEl('hzform_doi_prefix-lbl');
		hideEl('hzform_doi_prefix');
		showEl('hzform_datacite_doi_service-lbl');
		showEl('hzform_datacite_doi_service');
		showEl('hzform_datacite_doi_userpw-lbl');
		showEl('hzform_datacite_doi_userpw');
		hideEl('hzform_ezid_doi_service-lbl');
		hideEl('hzform_ezid_doi_service');
		hideEl('hzform_ezid_doi_userpw-lbl');
		hideEl('hzform_ezid_doi_userpw');
	} else if (val == EZID) {
		showEl('hzform_doi_shoulder-lbl');
		showEl('hzform_doi_shoulder');
		showEl('hzform_doi_prefix-lbl');
		showEl('hzform_doi_prefix');
		showEl('hzform_ezid_doi_service-lbl');
		showEl('hzform_ezid_doi_service');
		showEl('hzform_ezid_doi_userpw-lbl');
		showEl('hzform_ezid_doi_userpw');
		hideEl('hzform_datacite_doi_service-lbl');
		hideEl('hzform_datacite_doi_service');
		hideEl('hzform_datacite_doi_userpw-lbl');
		hideEl('hzform_datacite_doi_userpw');
	} else if (val == NONE) {
		hideEl('hzform_doi_shoulder-lbl');
		hideEl('hzform_doi_shoulder');
		hideEl('hzform_doi_prefix-lbl');
		hideEl('hzform_doi_prefix');
		hideEl('hzform_datacite_doi_service-lbl');
		hideEl('hzform_datacite_doi_service');
		hideEl('hzform_datacite_doi_userpw-lbl');
		hideEl('hzform_datacite_doi_userpw');
		hideEl('hzform_ezid_doi_service-lbl');
		hideEl('hzform_ezid_doi_service');
		hideEl('hzform_ezid_doi_userpw-lbl');
		hideEl('hzform_ezid_doi_userpw');
	}
}

document.addEventListener('DOMContentLoaded', function() {
	var switcher = document.getElementById(
		'hzform_datacite_ezid_doi_service_switch'
	);

	if (switcher) {
		updateDoiFields(switcher.value);

		switcher.addEventListener('change', function() {
			updateDoiFields(this.value);
		});
	}
});
