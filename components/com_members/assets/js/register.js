/**
 * @package     hubzero-cms
 * @file        components/com_register/register.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
//----------------------------------------------------------
// Registration form validation
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Register = {
	jQuery: jq,
	orgother: "",
	country_origin: 0,
	country_resident: 0,
	racenativetribe: "",
	racenativeamerican: false,
	raceasian: false,
	raceblack: false,
	racehawaiian: false,
	racewhite: false,
	racerefused: false,
	hispaniccuban: false,
	hispanicmexican: false,
	hispanicpuertorican: false,
	hispanicother: "",
	disabilityblind: false,
	disabilitydeaf: false,
	disabilityphysical: false,
	disabilitylearning: false,
	disabilityvocal: false,
	disabilityother: "",

	setOrgOther: function() {
		var $ = HUB.Register.jQuery;

		$('#org').attr('selectedIndex', '0');
	},

	disableOrgOther: function() {
		var $ = HUB.Register.jQuery;

		if ($('#org').attr('selectedIndex') == 0) {
			$('#orgtext')
				.attr('disabled', false)
				.val(HUB.Register.orgother);
		}
		else if ($('#orgtext').val()) {
			HUB.Register.orgother = $('#orgtext').val();
			$('#orgtext')
				.attr('disabled', true)
				.val("");
		}
	},

	setNonUSCountryOrigin: function() {
		var $ = HUB.Register.jQuery;

		$('#corigin_usno').attr('checked', true);
		HUB.Register.disableRacialBackground();
	},

	setNonUSCountryResident: function() {
		var $ = HUB.Register.jQuery;

		$('#cresident_usno').attr('checked', true);
	},

	disableCountryOriginSubgroups: function() {
		var $ = HUB.Register.jQuery;

		if ($('#corigin_usyes').attr('checked')) {
			HUB.Register.country_origin = $('#corigin').attr('selectedIndex');
			$('#corigin')
				.attr('disabled', true)
				.attr('selectedIndex', '-1');
		} else {
			$('#corigin')
				.attr('disabled', false)
				.attr('selectedIndex', HUB.Register.country_origin);
		}
		HUB.Register.disableRacialBackground();
	},

	disableCountryResidentSubgroups: function() {
		var $ = HUB.Register.jQuery;

		if ($('#cresident_usyes').attr('checked')) {
			HUB.Register.country_resident = $('#cresident').attr('selectedIndex');
			$('#cresident')
				.attr('disabled', true)
				.attr('selectedIndex', '-1');
		} else {
			$('#cresident')
				.attr('disabled', false)
				.attr('selectedIndex', HUB.Register.country_resident);
		}
	},

	setNativeAmerican: function() {
		var $ = HUB.Register.jQuery;

		$('#racenativeamerican').attr('checked', true);
		$('#racerefused').attr('checked', false);
	},

	disableNativeTribe: function() {
		var $ = HUB.Register.jQuery;

		if ($('#racenativeamerican').attr('checked')) {
			$('#racenativetribe')
				.attr('disabled', false)
				.val(HUB.Register.racenativetribe);
			HUB.Register.setRacialChoice();
		} else {
			HUB.Register.racenativetribe = $('#racenativetribe').val();
			$('#racenativetribe')
				.attr('disabled', true)
				.val("");
		}
	},

	setRacialChoice: function() {
		var $ = HUB.Register.jQuery;

		$('#racerefused').attr('checked', false);
	},

	disableRacialChoices: function() {
		var $ = HUB.Register.jQuery;

		if ($('#racerefused').attr('checked')) {
			HUB.Register.racenativeamerican = $('#racenativeamerican').attr('checked');
			$('#racenativeamerican')
				.attr('disabled', true)
				.attr('checked', false);

			HUB.Register.raceasian = $('#raceasian').attr('checked');
			$('#raceasian')
				.attr('disabled', true)
				.attr('checked', false);

			HUB.Register.raceblack = $('#raceblack').attr('checked');
			$('#raceblack')
				.attr('disabled', true)
				.attr('checked', false);

			HUB.Register.racehawaiian = $('#racehawaiian').attr('checked');
			$('#racehawaiian')
				.attr('disabled', true)
				.attr('checked', false);

			HUB.Register.racewhite = $('#racewhite').attr('checked');
			$('#racewhite')
				.attr('disabled', true)
				.attr('checked', false);
		}
		else if (!$('#racerefused').attr('disabled')) {
			$('#racenativeamerican')
				.attr('checked', HUB.Register.racenativeamerican)
				.attr('disabled', false);
			$('#raceasian')
				.attr('checked', HUB.Register.raceasian)
				.attr('disabled', false);
			$('#raceblack')
				.attr('checked', HUB.Register.raceblack)
				.attr('disabled', false);
			$('#racehawaiian')
				.attr('checked', HUB.Register.racehawaiian)
				.attr('disabled', false);
			$('#racewhite')
				.attr('checked', HUB.Register.racewhite)
				.attr('disabled', false);
		}
		HUB.Register.disableNativeTribe();
	},

	disableRacialBackground: function() {
		var $ = HUB.Register.jQuery;

		if ($('#corigin_usyes').attr('checked')) {
			$('#racenativeamerican').attr('checked', HUB.Register.racenativeamerican);
			$('#racenativeamerican').attr('disabled', false);
			$('#raceasian').attr('checked', HUB.Register.raceasian);
			$('#raceasian').attr('disabled', false);
			$('#raceblack').attr('checked', HUB.Register.raceblack);
			$('#raceblack').attr('disabled', false);
			$('#racehawaiian').attr('checked', HUB.Register.racehawaiian);
			$('#racehawaiian').attr('disabled', false);
			$('#racewhite').attr('checked', HUB.Register.racewhite);
			$('#racewhite').attr('disabled', false);
			$('#racerefused').attr('checked', HUB.Register.racerefused);
			$('#racerefused').attr('disabled', false);
		} else {
			HUB.Register.racenativeamerican = $('#racenativeamerican').attr('checked');
			$('#racenativeamerican')
				.attr('disabled', true)
				.attr('checked', false);

			HUB.Register.raceasian = $('#raceasian').attr('checked');
			$('#raceasian')
				.attr('disabled', true)
				.attr('checked', false);

			HUB.Register.raceblack = $('#raceblack').attr('checked');
			$('#raceblack')
				.attr('disabled', true)
				.attr('checked', false);

			HUB.Register.racehawaiian = $('#racehawaiian').attr('checked');
			$('#racehawaiian')
				.attr('disabled', true)
				.attr('checked', false);

			HUB.Register.racewhite = $('#racewhite').attr('checked');
			$('#racewhite')
				.attr('disabled', true)
				.attr('checked', false);

			HUB.Register.racerefused = $('#racerefused').attr('checked');
			$('#racerefused')
				.attr('disabled', true)
				.attr('checked', false);
		}
		HUB.Register.disableRacialChoices();
	},

	setHispanic: function() {
		var $ = HUB.Register.jQuery;

		$('#hispanicyes').attr('checked', true);
	},

	disableHispanicSubgroups: function() {
		var $ = HUB.Register.jQuery;

		if($('#hispanicyes').attr('checked')) {
			$('#hispaniccuban').attr('checked', HUB.Register.hispaniccuban);
			$('#hispaniccuban').attr('disabled', false);
			$('#hispanicmexican').attr('checked', HUB.Register.hispanicmexican);
			$('#hispanicmexican').attr('disabled', false);
			$('#hispanicpuertorican').attr('checked', HUB.Register.hispanicpuertorican);
			$('#hispanicpuertorican').attr('disabled', false);
			$('#hispanicother').val(HUB.Register.hispanicother);
			$('#hispanicother').attr('disabled', false);
		}
		else if(!$('#hispanicother').attr('disabled')) {
			$('#hispaniccuban').attr('disabled', true);
			HUB.Register.hispaniccuban = $('#hispaniccuban').attr('checked');
			$('#hispaniccuban').attr('checked', false);
			$('#hispanicmexican').attr('disabled', true);
			HUB.Register.hispanicmexican = $('#hispanicmexican').attr('checked');
			$('#hispanicmexican').attr('checked', false);
			$('#hispanicpuertorican').attr('disabled', true);
			HUB.Register.hispanicpuertorican = $('#hispanicpuertorican').attr('checked');
			$('#hispanicpuertorican').attr('checked', false);
			$('#hispanicother').attr('disabled', true);
			HUB.Register.hispanicother = $('#hispanicother').val();
			$('#hispanicother').val("");
		}
	},

	setDisabled: function() {
		var $ = HUB.Register.jQuery;

		$('#disabilityyes').attr('checked', true);
	},

	disableDisabilitySubgroups: function() {
		var $ = HUB.Register.jQuery;

		if ($('#disabilityyes').attr('checked')){
			$('#disabilityblind')
				.attr('checked', HUB.Register.disabilityblind)
				.attr('disabled', false);
			$('#disabilitydeaf')
				.attr('checked', HUB.Register.disabilitydeaf)
				.attr('disabled', false);
			$('#disabilityphysical')
				.attr('checked', HUB.Register.disabilityphysical)
				.attr('disabled', false);
			$('#disabilitylearning')
				.attr('checked', HUB.Register.disabilitylearning)
				.attr('disabled', false);
			$('#disabilityvocal')
				.attr('checked', HUB.Register.disabilityvocal)
				.attr('disabled', false);
			$('#disabilityother')
				.val(HUB.Register.disabilityother)
				.attr('disabled', false);
		}
		else if (!$('#disabilityother').attr('disabled')) {
			HUB.Register.disabilityblind = $('#disabilityblind').attr('checked');
			$('#disabilityblind')
				.attr('disabled', true)
				.attr('checked', false);

			HUB.Register.disabilitydeaf = $('#disabilitydeaf').attr('checked');
			$('#disabilitydeaf')
				.attr('disabled', true)
				.attr('checked', false);

			HUB.Register.disabilityphysical = $('#disabilityphysical').attr('checked');
			$('#disabilityphysical')
				.attr('disabled', true)
				.attr('checked', false);

			HUB.Register.disabilitylearning = $('#disabilitylearning').attr('checked');
			$('#disabilitylearning')
				.attr('disabled', true)
				.attr('checked', false);

			HUB.Register.disabilityvocal = $('#disabilityvocal').attr('checked');
			$('#disabilityvocal')
				.attr('disabled', true)
				.attr('checked', false);

			HUB.Register.disabilityother = $('#disabilityother').val();
			$('#disabilityother')
				.attr('disabled', true)
				.val("");
		}
	},
	
	disableIndie: function() {
		var $ = HUB.Register.jQuery;

		$('#type-indie').attr('checked', false);
		$('#username').attr('disabled', false);
		$('#passwd').attr('disabled', false);
	},
	
	disableDomains: function() {
		var $ = HUB.Register.jQuery;

		$('#username').val('');
		$('#username').attr('disabled', true);
		$('#passwd').val('');
		$('#passwd').attr('disabled', true);
		$('#type-indie').attr('checked', true);
		$('.option').each(function(i, input) {
			var name = $(input).attr('name');
			var value = $(input).val();
			if (name == 'domain' && value != '') {
				if ($(input).attr('checked')) {
					$(input).attr('checked', false);
				}
			}
		});
	},

	initialize: function() {
		var $ = HUB.Register.jQuery, w = 760, h = 520;

		$('.com_register a.popup').each(function(i, trigger) {
			href = $(this).attr('href');
			if (href.indexOf('?') == -1) {
				href += '?tmpl=component';
			} else {
				href += '&tmpl=component';
			}
			$(this).attr('href', href);
		});

		if ($('#tagcloud')) {
			$('#tagcloud').removeClass('hide');

			$('#tags-hint').text('Enter tags above or click one of the tags below.');

			$('.tags a').on('click', function (e) {
				e.preventDefault();
				
				var val = $('#actags').val();
				$('#actags').val((val) ? val + ', ' + $(this).text() : $(this).text());
				if (jQuery().tokenInput) {
					$('#actags').tokenInput('add', {
						id: $(this).attr('rel'), 
						name: $(this).text()
					});
				}
				return false;
			});
		}

		// Look for the "type-linked" element
		var typeindie = $('#type-indie');
		if (typeindie.length) {
			// Found it - means we're on the initial registration
			// form where users choose a linked account or not
			$('#username').attr('disabled', true);
			$('#passwd').attr('disabled', true);
			$('.option').each(function(i, input) {
				var name = $(input).attr('name');
				var value = $(input).val();
				var checked = $(input).attr('checked');
				if (name == 'domain' && value != '') {
					$(input).on('click', HUB.Register.disableIndie);

					if (checked == 'checked') {
						$('#username').attr('disabled', false);
						$('#passwd').attr('disabled', false);
					}
				}
			});
			$(typeindie).on('click', HUB.Register.disableDomains);
		} else {
			// Not found - this should mean we're on the full
			// registration form

			// organization
			if ($('#org')) {
				$('#org').change(HUB.Register.disableOrgOther);
				$('#orgtext').change(HUB.Register.setOrgOther);
			}
			// country of origin
			if ($('#corigin_usyes')) {
				$('#corigin_usyes').change(HUB.Register.disableCountryOriginSubgroups);
				$('#corigin_usno').change(HUB.Register.disableCountryOriginSubgroups);
				$('#corigin').change(HUB.Register.setNonUSCountryOrigin);
				HUB.Register.disableCountryOriginSubgroups();
			}
			// country of residence
			if ($('#cresident_usyes')) {
				$('#cresident_usyes').change(HUB.Register.disableCountryResidentSubgroups);
				$('#cresident_usno').change(HUB.Register.disableCountryResidentSubgroups);
				$('#cresident').change(HUB.Register.setNonUSCountryResident);
				HUB.Register.disableCountryResidentSubgroups();
			}
			// disability
			if ($('#disabilityyes')) {
				$('#disabilityyes').change(HUB.Register.disableDisabilitySubgroups);
				$('#disabilityblind').change(HUB.Register.setDisabled);
				$('#disabilitydeaf').change(HUB.Register.setDisabled);
				$('#disabilityphysical').change(HUB.Register.setDisabled);
				$('#disabilitylearning').change(HUB.Register.setDisabled);
				$('#disabilityvocal').change(HUB.Register.setDisabled);
				$('#disabilityother').change(HUB.Register.setDisabled);
				$('#disabilityno').change(HUB.Register.disableDisabilitySubgroups);
				$('#disabilityrefused').change(HUB.Register.disableDisabilitySubgroups);
			}
			// hispanic
			if ($('#hispanicyes')) {
				$('#hispanicyes').change(HUB.Register.disableHispanicSubgroups);
				$('#hispaniccuban').change(HUB.Register.setHispanic);
				$('#hispanicmexican').change(HUB.Register.setHispanic);
				$('#hispanicpuertorican').change(HUB.Register.setHispanic);
				$('#hispanicother').change(HUB.Register.setHispanic);
				$('#hispanicno').change(HUB.Register.disableHispanicSubgroups);
				$('#hispanicrefused').change(HUB.Register.disableHispanicSubgroups);
			}
			// race
			if ($('#racenativeamerican')) {
				$('#racenativeamerican').change(HUB.Register.disableNativeTribe);
				$('#racenativetribe').change(HUB.Register.setNativeAmerican);
				$('#raceasian').change(HUB.Register.setRacialChoice);
				$('#raceblack').change(HUB.Register.setRacialChoice);
				$('#racehawaiian').change(HUB.Register.setRacialChoice);
				$('#racewhite').change(HUB.Register.setRacialChoice);
				$('#racerefused').change(HUB.Register.disableRacialChoices);
			}
		}

		var passmtr = $('#passmeter');
		var passwd = $('#password');
		if (passmtr.length && passwd.length) {
			$('<span id="meter-container"></span>').insertAfter(passwd);

			if (passwd.val() != '') {
				HUB.Register.checkPass();
			} else {
				$('<span id="passwd-meter" style="width:0%" class="bad"><span>Strength</span></span>').appendTo('#meter-container');
			}

			passwd.keyup(function(event) {
				var timer = setTimeout('HUB.Register.checkPass()',200);
			});
		}

		var userlogin = $('#userlogin');
		var usernameStatusAfter = $('#userlogin');

		if (userlogin.length > 0) {
			usernameStatusAfter.after('<p class="hint" id="usernameStatus">&nbsp;</p>');
			
			userlogin.focusout(function(obj) {
				var timer = setTimeout('HUB.Register.checkLogin()',200);
			});
		}

		if ($('#orcid').length > 0) {
			$('#orcid-fetch').on('click', function(e) {
				HUB.Register.showOrcid(this);
				return false;
			});
		}

		HUB.Register.fetchOrcid();
	},

	checkPass: function() {
		var $ = HUB.Register.jQuery;

		if ($('#userlogin')) {
			usernm = $('#userlogin').val();
		}
		passwd = $('#password').val();

		$.post($('#base_uri').val() + '/members/register/passwordstrength?no_html=1', {'format': 'raw', 'pass':passwd, 'user':usernm}, function(data) {
			$('#meter-container').html(data);
		});
	},

	checkLogin: function() {
		var $ = HUB.Register.jQuery;
		var submitTo = $('#base_uri').val() + '/members/register/checkusername?userlogin=' + $('#userlogin').val();
		var usernameStatus = $('#usernameStatus');

		$.getJSON(submitTo, function(data) {
			usernameStatus.html(data.message);
			usernameStatus.removeClass('ok');
			usernameStatus.removeClass('notok');
			if (data.status == 'ok') {
				usernameStatus.addClass('ok');
			} else {
				usernameStatus.addClass('notok');
			}
		});
	},

	showOrcid: function(currentForm) {
		var $ = this.jQuery;

		$.fancybox({
			type: 'iframe',   // change this to 'ajax' if you want to use AJAX
			width: 700,
			height: 'auto',
			autoSize: false,
			fitToView: false,
			titleShow: false,
			closeClick: false,
			helpers: { 
				overlay : {closeClick: false} // prevents closing when clicking OUTSIDE fancybox
			},
			tpl: {
					wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
			},
			beforeLoad: function() {
				//href = $(this).attr('href');
				href = $('#base_uri').val() + '/index.php?option=com_members&controller=orcid&return=1&fname=' + $('#first-name').val() + '&lname=' + $('#last-name').val()  + '&email=' + $('#email').val();
				if (href.indexOf('?') == -1) {
					href += '?tmpl=component';    // Change to no_html=1 if using AJAX
				} else {
					href += '&tmpl=component';    // Change to no_html=1 if using AJAX
				}
				$(this).attr('href', href);
			}/*,
			afterClose: function() {
				currentForm.submit();
			}*/
		});
	},
	
	fetchOrcidRecords: function() {
		var $ = this.jQuery;

		var firstName = $('#first-name').val();
		var lastName = $('#last-name').val();
		var email = $('#email').val();

		if (!firstName && !lastName && !email) {
			alert('Please fill at least one of the fields.');
			return;
		}

		// return param: 1 means return ORCID to use to finish registration, assumes registration page
		// return param: 0 means do not return ORCID, assumes profile page
		$.ajax({
			url: $('#base_uri').val() + '/index.php?option=com_members&controller=orcid&task=fetch&no_html=1&fname=' + firstName + '&lname=' + lastName + '&email=' + email + '&return=1',
			type: 'GET',
			success: function(data, status, jqXHR) {
				$('#section-orcid-results').html(jQuery.parseJSON(data));
			}
		});
	},

	fetchOrcid: function() {
		var $ = this.jQuery;

		$('body').on('click', '#get-orcid-results', function(event) {
			event.preventDefault();

			HUB.Register.fetchOrcidRecords();
		});
	},

	associateOrcid: function(parentField, orcid) {
		window.top.document.getElementById('orcid').value = orcid;

		parent.jQuery.fancybox.close();
	},

	createOrcid: function(fname, lname, email) {
		var uri = $('#base_uri').val() + '/index.php?option=com_members&controller=orcid&task=create&no_html=1&fname=' + fname + '&lname=' + lname + '&email=' + email;
		console.log(uri);
		$.ajax({
			url: uri,
			type: 'GET',
			success: function(data, status, jqXHR) {
				var response =jQuery.parseJSON(data);

				if (response.success) {
					if (response.orcid) {
						alert('Successful creation of your new ORCID. Claim the ORCID through the link sent to your email.');
						window.parent.document.getElementById('orcid').value = response.orcid;
						parent.jQuery.fancybox.close();
					} else {
						alert('ORCID service reported a successful creation but we failed to retrieve an ORCID. Please contact support.');
					}
				} else {
					if (response.message) {
						alert(response.message);
					} else {
						alert('Failed to create a new ORCID. Possible existence of an ORCID with the same email.');
					}
				}
			}
		});
	}
}

jQuery(document).ready(function($){
	HUB.Register.initialize();
});