/**
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
HUB.Register = {
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
		var form = $('hubForm');
		form.org.selectedIndex = 0;
	},

	disableOrgOther: function() {
		var form = $('hubForm');
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
		var form = $('hubForm');
		form.corigin_usno.checked = true;
		//HUB.Register.disableRacialBackground();
	},

	setNonUSCountryResident: function() {
		var form = $('hubForm');
		form.cresident_usno.checked = true;
	},

	disableCountryOriginSubgroups: function() {
		var form = $('hubForm');
		if(form.corigin_usyes.checked) {
			HUB.Register.country_origin = form.corigin.selectedIndex;
			form.corigin.disabled = true;
			form.corigin.selectedIndex = -1;
		}
		else {
			form.corigin.disabled = false;
			form.corigin.selectedIndex = HUB.Register.country_origin;
		}
		//HUB.Register.disableRacialBackground();
	},

	disableCountryResidentSubgroups: function() {
		var form = $('hubForm');
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
		var form = $('hubForm');
		form.racenativeamerican.checked = true;
		form.racerefused.checked = false;
	},

	disableNativeTribe: function() {
		var form = $('hubForm');
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
		var form = $('hubForm');
		form.racerefused.checked = false;
	},

	disableRacialChoices: function() {
		var form = $('hubForm');
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
		var form = $('hubForm');
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
		var form = $('hubForm');
		form.hispanicyes.checked = true;
	},

	disableHispanicSubgroups: function() {
		var form = $('hubForm');
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
		var form = $('hubForm');
		form.disabilityyes.checked = true;
	},

	disableDisabilitySubgroups: function() {
		var form = $('hubForm');
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
		$('type-indie').checked = false;
		$('username').disabled = false;
		$('passwd').disabled = false;
	},
	
	disableDomains: function() {
		$('username').value = '';
		$('username').disabled = true;
		$('passwd').value = '';
		$('passwd').disabled = true;
		$('type-indie').checked = true;
		$$('.option').each(function(input) {
			var name = input.getAttribute('name');
			var value = input.getAttribute('value');
			if (name == 'domain' && value != '') {
				if (input.checked) {
					input.checked = false;
				}
			}
		});
	},
	
	initialize: function() {
		// Look for the "type-linked" element
		var typeindie = $('type-indie');
		if (typeindie) {
			// Found it - means we're on the initial registration
			// form where users choose a linked account or not
			$('username').disabled = true;
			$('passwd').disabled = true;
			$$('.option').each(function(input) {
				var name = input.getAttribute('name');
				var value = input.getAttribute('value');
				var checked = input.getAttribute('checked');
			    if (name == 'domain' && value != '') {
					input.addEvent('click', HUB.Register.disableIndie);

					if (checked == 'checked')
					{
						$('username').disabled = false;
						$('passwd').disabled = false;
					}
				}
			});
			typeindie.addEvent('click', HUB.Register.disableDomains);
		} else {
			// Not found - this should mean we're on the full
			// registration form
			
			// organization
			if ($('org')) {
				$('org').addEvent('change', HUB.Register.disableOrgOther);
				$('orgtext').addEvent('change', HUB.Register.setOrgOther);
			}
			// country of origin
			if ($('corigin_usyes')) {
				$('corigin_usyes').addEvent('change', HUB.Register.disableCountryOriginSubgroups);
				$('corigin_usno').addEvent('change', HUB.Register.disableCountryOriginSubgroups);
				$('corigin').addEvent('change', HUB.Register.setNonUSCountryOrigin);
			}
			// country of residence
			if ($('cresident_usyes')) {
				$('cresident_usyes').addEvent('change', HUB.Register.disableCountryResidentSubgroups);
				$('cresident_usno').addEvent('change', HUB.Register.disableCountryResidentSubgroups);
				$('cresident').addEvent('change', HUB.Register.setNonUSCountryResident);
			}
			// disability
			if ($('disabilityyes')) {
				$('disabilityyes').addEvent('change', HUB.Register.disableDisabilitySubgroups);
				$('disabilityblind').addEvent('change', HUB.Register.setDisabled);
				$('disabilitydeaf').addEvent('change', HUB.Register.setDisabled);
				$('disabilityphysical').addEvent('change', HUB.Register.setDisabled);
				$('disabilitylearning').addEvent('change', HUB.Register.setDisabled);
				$('disabilityvocal').addEvent('change', HUB.Register.setDisabled);
				$('disabilityother').addEvent('change', HUB.Register.setDisabled);
				$('disabilityno').addEvent('change', HUB.Register.disableDisabilitySubgroups);
				$('disabilityrefused').addEvent('change', HUB.Register.disableDisabilitySubgroups);
			}
			// hispanic
			if ($('hispanicyes')) {
				$('hispanicyes').addEvent('change', HUB.Register.disableHispanicSubgroups);
				$('hispaniccuban').addEvent('change', HUB.Register.setHispanic);
				$('hispanicmexican').addEvent('change', HUB.Register.setHispanic);
				$('hispanicpuertorican').addEvent('change', HUB.Register.setHispanic);
				$('hispanicother').addEvent('change', HUB.Register.setHispanic);
				$('hispanicno').addEvent('change', HUB.Register.disableHispanicSubgroups);
				$('hispanicrefused').addEvent('change', HUB.Register.disableHispanicSubgroups);
			}
			// race
			if ($('racenativeamerican')) {
				$('racenativeamerican').addEvent('change', HUB.Register.disableNativeTribe);
				$('racenativetribe').addEvent('change', HUB.Register.setNativeAmerican);
				$('raceasian').addEvent('change', HUB.Register.setRacialChoice);
				$('raceblack').addEvent('change', HUB.Register.setRacialChoice);
				$('racehawaiian').addEvent('change', HUB.Register.setRacialChoice);
				$('racewhite').addEvent('change', HUB.Register.setRacialChoice);
				$('racerefused').addEvent('change', HUB.Register.disableRacialChoices);
			}
		}
		
		var passmtr = $('passmeter');
		var passwd = $('password');
		if (passmtr && passwd) {
			/*
			<span id="meter-container" class="hide">
				<span id="passwd-meter" style="width:0%;" class="bad"><span>Strength</span></span>
			</span>
			*/
			
			var container = new Element('span', {
				id: 'meter-container'
			}).injectAfter(passwd);
			
			if (passwd.value != '') {
				HUB.Register.checkPass();
			} else {
				var meter = new Element('span', {
					id: 'passwd-meter',
					'class': 'bad',
					styles: {
						width: '0%'
					}
				}).injectInside(container);

				var txt = new Element('span', {}).appendText('Strength').injectInside(meter);
			}
			
			passwd.addEvent('keyup',function(event) {
				var timer = setTimeout('HUB.Register.checkPass()',200);
			});
		}
	},
	
	checkPass: function() {
		if ($('userlogin')) {
			usernm = $('userlogin').value;
		}
		passwd = $('password').value;

		var checked = new Ajax('/register/passwordstrength?no_html=1&pass='+passwd+'&user='+usernm, {
			method: 'get',
			update: $('meter-container')
		}).request();
	}
}

//-----------------------------------------------------------
window.addEvent('domready', HUB.Register.initialize);
