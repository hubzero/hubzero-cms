/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

const DATACITE = 2;
const EZID = 1;
const NONE = 0;

jQuery(document).ready(function(jq){
	var $ = jq;
	
	if ($('#hzform_datacite_ezid_doi_service_switch').val() == DATACITE)
	{
		$('#doi_shoulder').show();
		$('#hzform_doi_shoulder').show();
		$('#hzform_doi_prefix-lbl').hide();
		$('#hzform_doi_prefix').hide();
		$('#hzform_datacite_doi_service-lbl').show();
		$('#hzform_datacite_doi_service').show();
		$('#hzform_datacite_doi_userpw-lbl').show();
		$('#hzform_datacite_doi_userpw').show();
		$('#hzform_ezid_doi_service-lbl').hide();
		$('#hzform_ezid_doi_service').hide();
		$('#hzform_ezid_doi_userpw-lbl').hide();
		$('#hzform_ezid_doi_userpw').hide();
	}
	
	if ($('#hzform_datacite_ezid_doi_service_switch').val() == EZID)
	{
		$('#hzform_doi_shoulder-lbl').show();
		$('#hzform_doi_shoulder').show();
		$('#hzform_doi_prefix-lbl').show();
		$('#hzform_doi_prefix').show();
		$('#hzform_ezid_doi_service-lbl').show();
		$('#hzform_ezid_doi_service').show();
		$('#hzform_ezid_doi_userpw-lbl').show();
		$('#hzform_ezid_doi_userpw').show();
		$('#hzform_datacite_doi_service-lbl').hide();
		$('#hzform_datacite_doi_service').hide();
		$('#hzform_datacite_doi_userpw-lbl').hide();
		$('#hzform_datacite_doi_userpw').hide();
	}
	
	if ($('#hzform_datacite_ezid_doi_service_switch').val() == NONE)
	{
		$('#hzform_doi_shoulder-lbl').hide();
		$('#hzform_doi_shoulder').hide();
		$('#hzform_doi_prefix-lbl').hide();
		$('#hzform_doi_prefix').hide();
		$('#hzform_datacite_doi_service-lbl').hide();
		$('#hzform_datacite_doi_service').hide();
		$('#hzform_datacite_doi_userpw-lbl').hide();
		$('#hzform_datacite_doi_userpw').hide();
		$('#hzform_ezid_doi_service-lbl').hide();
		$('#hzform_ezid_doi_service').hide();
		$('#hzform_ezid_doi_userpw-lbl').hide();
		$('#hzform_ezid_doi_userpw').hide();
	}
	
	$('#hzform_datacite_ezid_doi_service_switch').on('change', function()
	{
		if ($('#hzform_datacite_ezid_doi_service_switch').val() == DATACITE)
		{
			$('#hzform_doi_shoulder-lbl').show();
			$('#hzform_doi_shoulder').show();
			$('#hzform_doi_prefix-lbl').hide();
			$('#hzform_doi_prefix').hide();
			$('#hzform_datacite_doi_service-lbl').show();
			$('#hzform_datacite_doi_service').show();
			$('#hzform_datacite_doi_userpw-lbl').show();
			$('#hzform_datacite_doi_userpw').show();
			$('#hzform_ezid_doi_service-lbl').hide();
			$('#hzform_ezid_doi_service').hide();
			$('#hzform_ezid_doi_userpw-lbl').hide();
			$('#hzform_ezid_doi_userpw').hide();
		}
		else if ($('#hzform_datacite_ezid_doi_service_switch').val() == EZID)
		{
			$('#hzform_doi_shoulder-lbl').show();
			$('#hzform_doi_shoulder').show();
			$('#hzform_doi_prefix-lbl').show();
			$('#hzform_doi_prefix').show();
			$('#hzform_ezid_doi_service-lbl').show();
			$('#hzform_ezid_doi_service').show();
			$('#hzform_ezid_doi_userpw-lbl').show();
			$('#hzform_ezid_doi_userpw').show();
			$('#hzform_datacite_doi_service-lbl').hide();
			$('#hzform_datacite_doi_service').hide();
			$('#hzform_datacite_doi_userpw-lbl').hide();
			$('#hzform_datacite_doi_userpw').hide();
		}
		else if ($('#hzform_datacite_ezid_doi_service_switch').val() == NONE)
		{
			$('#hzform_doi_shoulder-lbl').hide();
			$('#hzform_doi_shoulder').hide();
			$('#hzform_doi_prefix-lbl').hide();
			$('#hzform_doi_prefix').hide();
			$('#hzform_datacite_doi_service-lbl').hide();
			$('#hzform_datacite_doi_service').hide();
			$('#hzform_datacite_doi_userpw-lbl').hide();
			$('#hzform_datacite_doi_userpw').hide();
			$('#hzform_ezid_doi_service-lbl').hide();
			$('#hzform_ezid_doi_service').hide();
			$('#hzform_ezid_doi_userpw-lbl').hide();
			$('#hzform_ezid_doi_userpw').hide();
		}
	});
});
