/**
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the 'Software'), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
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
