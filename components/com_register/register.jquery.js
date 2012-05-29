/**
 * @package     hubzero-cms
 * @file        components/com_register/register.jquery.js
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
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		form.org.selectedIndex = 0;
	},

	disableOrgOther: function() {
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		if(form.org.selectedIndex == 0) {
			form.orgtext.disabled = false;
			form.orgtext.value = HUB.Register.orgother;
		}
		else if(form.orgtext.value) {
			form.orgtext.disabled = true;
			HUB.Register.orgother = form.orgtext.value;
			form.orgtext.value = "";
		}
	},

	setNonUSCountryOrigin: function() {
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		form.corigin_usno.checked = true;
		HUB.Register.disableRacialBackground();
	},

	setNonUSCountryResident: function() {
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		form.cresident_usno.checked = true;
	},

	disableCountryOriginSubgroups: function() {
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		if(form.corigin_usyes.checked) {
			HUB.Register.country_origin = form.corigin.selectedIndex;
			form.corigin.disabled = true;
			form.corigin.selectedIndex = -1;
		}
		else {
			form.corigin.disabled = false;
			form.corigin.selectedIndex = HUB.Register.country_origin;
		}
		HUB.Register.disableRacialBackground();
	},

	disableCountryResidentSubgroups: function() {
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		if(form.cresident_usyes.checked) {
			HUB.Register.country_resident = form.cresident.selectedIndex;
			form.cresident.disabled = true;
			form.cresident.selectedIndex = -1;
		} else {
			form.cresident.disabled = false;
			form.cresident.selectedIndex = HUB.Register.country_resident;
		}
	},

	setNativeAmerican: function() {
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		form.racenativeamerican.checked = true;
		form.racerefused.checked = false;
	},

	disableNativeTribe: function() {
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		if(form.racenativeamerican.checked) {
			form.racenativetribe.disabled = false;
			form.racenativetribe.value = HUB.Register.racenativetribe;
			HUB.Register.setRacialChoice();
		} else {
			form.racenativetribe.disabled = true;
			HUB.Register.racenativetribe = form.racenativetribe.value;
			form.racenativetribe.value = "";
		}
	},

	setRacialChoice: function() {
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		form.racerefused.checked = false;
	},

	disableRacialChoices: function() {
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		if(form.racerefused.checked) {
			form.racenativeamerican.disabled = true;
			HUB.Register.racenativeamerican = form.racenativeamerican.checked;
			form.racenativeamerican.checked = false;
			form.raceasian.disabled = true;
			HUB.Register.raceasian = form.raceasian.checked;
			form.raceasian.checked = false;
			form.raceblack.disabled = true;
			HUB.Register.raceblack = form.raceblack.checked;
			form.raceblack.checked = false;
			form.racehawaiian.disabled = true;
			HUB.Register.racehawaiian = form.racehawaiian.checked;
			form.racehawaiian.checked = false;
			form.racewhite.disabled = true;
			HUB.Register.racewhite = form.racewhite.checked;
			form.racewhite.checked = false;
		}
		else if(!form.racerefused.disabled) {
			form.racenativeamerican.checked = HUB.Register.racenativeamerican;
			form.racenativeamerican.disabled = false;
			form.raceasian.checked = HUB.Register.raceasian;
			form.raceasian.disabled = false;
			form.raceblack.checked = HUB.Register.raceblack;
			form.raceblack.disabled = false;
			form.racehawaiian.checked = HUB.Register.racehawaiian;
			form.racehawaiian.disabled = false;
			form.racewhite.checked = HUB.Register.racewhite;
			form.racewhite.disabled = false;
		}
		HUB.Register.disableNativeTribe();
	},

	disableRacialBackground: function() {
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		if(form.corigin_usyes.checked) {
			form.racenativeamerican.checked = HUB.Register.racenativeamerican;
			form.racenativeamerican.disabled = false;
			form.raceasian.checked = HUB.Register.raceasian;
			form.raceasian.disabled = false;
			form.raceblack.checked = HUB.Register.raceblack;
			form.raceblack.disabled = false;
			form.racehawaiian.checked = HUB.Register.racehawaiian;
			form.racehawaiian.disabled = false;
			form.racewhite.checked = HUB.Register.racewhite;
			form.racewhite.disabled = false;
			form.racerefused.checked = HUB.Register.racerefused;
			form.racerefused.disabled = false;
		} else {
			form.racenativeamerican.disabled = true;
			HUB.Register.racenativeamerican = form.racenativeamerican.checked;
			form.racenativeamerican.checked = false;
			form.raceasian.disabled = true;
			HUB.Register.raceasian = form.raceasian.checked;
			form.raceasian.checked = false;
			form.raceblack.disabled = true;
			HUB.Register.raceblack = form.raceblack.checked;
			form.raceblack.checked = false;
			form.racehawaiian.disabled = true;
			HUB.Register.racehawaiian = form.racehawaiian.checked;
			form.racehawaiian.checked = false;
			form.racewhite.disabled = true;
			HUB.Register.racewhite = form.racewhite.checked;
			form.racewhite.checked = false;
			form.racerefused.disabled = true;
			HUB.Register.racerefused = form.racerefused.checked;
			form.racerefused.checked = false;
		}
		HUB.Register.disableRacialChoices();
	},

	setHispanic: function() {
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		form.hispanicyes.checked = true;
	},

	disableHispanicSubgroups: function() {
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		if(form.hispanicyes.checked){
			form.hispaniccuban.checked = HUB.Register.hispaniccuban;
			form.hispaniccuban.disabled = false;
			form.hispanicmexican.checked = HUB.Register.hispanicmexican;
			form.hispanicmexican.disabled = false;
			form.hispanicpuertorican.checked = HUB.Register.hispanicpuertorican;
			form.hispanicpuertorican.disabled = false;
			form.hispanicother.value = HUB.Register.hispanicother;
			form.hispanicother.disabled = false;
		}
		else if(!form.hispanicother.disabled) {
			form.hispaniccuban.disabled = true;
			HUB.Register.hispaniccuban = form.hispaniccuban.checked;
			form.hispaniccuban.checked = false;
			form.hispanicmexican.disabled = true;
			HUB.Register.hispanicmexican = form.hispanicmexican.checked;
			form.hispanicmexican.checked = false;
			form.hispanicpuertorican.disabled = true;
			HUB.Register.hispanicpuertorican = form.hispanicpuertorican.checked;
			form.hispanicpuertorican.checked = false;
			form.hispanicother.disabled = true;
			HUB.Register.hispanicother = form.hispanicother.value;
			form.hispanicother.value = "";
		}
	},

	setDisabled: function() {
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		form.disabilityyes.checked = true;
	},

	disableDisabilitySubgroups: function() {
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		if(form.disabilityyes.checked){
			form.disabilityblind.checked = HUB.Register.disabilityblind;
			form.disabilityblind.disabled = false;
			form.disabilitydeaf.checked = HUB.Register.disabilitydeaf;
			form.disabilitydeaf.disabled = false;
			form.disabilityphysical.checked = HUB.Register.disabilityphysical;
			form.disabilityphysical.disabled = false;
			form.disabilitylearning.checked = HUB.Register.disabilitylearning;
			form.disabilitylearning.disabled = false;
			form.disabilityvocal.checked = HUB.Register.disabilityvocal;
			form.disabilityvocal.disabled = false;
			form.disabilityother.value = HUB.Register.disabilityother;
			form.disabilityother.disabled = false;
		}
		else if(!form.disabilityother.disabled) {
			form.disabilityblind.disabled = true;
			HUB.Register.disabilityblind = form.disabilityblind.checked;
			form.disabilityblind.checked = false;
			form.disabilitydeaf.disabled = true;
			HUB.Register.disabilitydeaf = form.disabilitydeaf.checked;
			form.disabilitydeaf.checked = false;
			form.disabilityphysical.disabled = true;
			HUB.Register.disabilityphysical = form.disabilityphysical.checked;
			form.disabilityphysical.checked = false;
			form.disabilitylearning.disabled = true;
			HUB.Register.disabilitylearning = form.disabilitylearning.checked;
			form.disabilitylearning.checked = false;
			form.disabilityvocal.disabled = true;
			HUB.Register.disabilityvocal = form.disabilityvocal.checked;
			form.disabilityvocal.checked = false;
			form.disabilityother.disabled = true;
			HUB.Register.disabilityother = form.disabilityother.value;
			form.disabilityother.value = "";
		}
	},
	
	disableIndie: function() {
		var $ = this.jQuery;
		
		$('#type-indie').checked = false;
		$('#username').disabled = false;
		$('#passwd').disabled = false;
	},
	
	disableDomains: function() {
		var $ = this.jQuery;
		
		$('#username').value = '';
		$('#username').disabled = true;
		$('#passwd').value = '';
		$('#passwd').disabled = true;
		$('#type-indie').checked = true;
		$('.option').each(function(i, input) {
			var name = $(input).attr('name');
			var value = $(input).val();
			if (name == 'domain' && value != '') {
				if ($(input).checked) {
					$(input).checked = false;
				}
			}
		});
	},
	
	initialize: function() {
		var $ = this.jQuery;
		
		if ($('#tagcloud')) {
			$('#tagcloud').removeClass('hide');
			
			$('#tags-hint').text('Enter tags above or click one of the tags below.');
			
			$('.tags a').each(function(i, el){
				$(el).click(function (e) {
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
			});
		}
		
		// Look for the "type-linked" element
		var typeindie = $('#type-indie');
		if (typeindie) {
			// Found it - means we're on the initial registration
			// form where users choose a linked account or not
			$('#username').disabled = true;
			$('#passwd').disabled = true;
			$('.option').each(function(i, input) {
				var name = $(input).attr('name');
				var value = $(input).val();
				var checked = $(input).attr('checked');
			    if (name == 'domain' && value != '') {
					$(input).click(HUB.Register.disableIndie);

					if (checked == 'checked')
					{
						$('#username').disabled = false;
						$('#passwd').disabled = false;
					}
				}
			});
			$(typeindie).click(HUB.Register.disableDomains);
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
			}
			// country of residence
			if ($('#cresident_usyes')) {
				$('#cresident_usyes').change(HUB.Register.disableCountryResidentSubgroups);
				$('#cresident_usno').change(HUB.Register.disableCountryResidentSubgroups);
				$('#cresident').change(HUB.Register.setNonUSCountryResident);
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
		if (passmtr && passwd) {
			/*
			<span id="meter-container" class="hide">
				<span id="passwd-meter" style="width:0%;" class="bad"><span>Strength</span></span>
			</span>
			*/
			
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
	},
	
	checkPass: function() {
		var $ = this.jQuery;
		
		if ($('#userlogin')) {
			usernm = $('#userlogin').val();
		}
		passwd = $('#password').val();

		$.post('/register/passwordstrength?no_html=1', {'format': 'raw', 'pass':passwd, 'user':usernm}, function(data) {
			$('#meter-container').html(data);
		});
	}
}

jQuery(document).ready(function($){
	HUB.Register.initialize();
});


