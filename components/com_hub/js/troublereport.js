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

//----------------------------------------------------------
// Slider
//----------------------------------------------------------
Fx.Slider = Fx.Base.extend({

	options: {
	},

	initialize: function(el, options) {
		this.el = $(el);
		this.el.style.overflow = 'hidden';
		this.el.iniWidth = this.el.offsetWidth;
		this.el.iniHeight = this.el.offsetHeight;
		this.parent(this.options);
	},

	go: function() {
		this.duration = this.options.duration;
		this.startTime = (new Date).getTime();
		this.timer = setInterval (this.step.bind(this), 13);
	},

	step: function() {
		var time  = (new Date).getTime();
		var Tpos   = (time - this.startTime) / (this.duration);
		if (time >= this.duration+this.startTime) {
			this.now = this.to;
			clearInterval (this.timer);
			this.timer = null;
			if (this.options.onComplete) setTimeout(this.options.onComplete.bind(this), 10);
		} else {
			this.now = ((-Math.cos(Tpos*Math.PI)/2) + 0.5) * (this.to-this.from) + this.from;
			//this time-position, sinoidal transition thing is from script.aculo.us
		}
		this.increase();
	},

	custom: function(from, to) {
		if (this.timer != null) return;
		this.from = from;
		this.to = to;
		this.go();
	},

	hide: function() {
		this.now = 0;
		this.increase();
	},

	clearTimer: function() {
		clearInterval(this.timer);
		this.timer = null;
	},

	increase: function() {
		this.el.style.height = this.now + "px";
	},

	toggle: function() {
		if (this.el.offsetHeight > 0) this.custom(this.el.offsetHeight, 0);
		else this.custom(0, this.el.scrollHeight);
	}
});

//----------------------------------------------------------
// Trouble Report form
//----------------------------------------------------------
HUB.TroubleReport = {
	form: 'troublereport',
	name: 'trName',
	email: 'trEmail',
	login: 'trLogin',
	problem: 'trProblem',
	loader: 'trSending',
	success: 'trSuccess',

	initialize: function() {
		if (!$(HUB.TroubleReport.form)) {
			return;
		}
		var pane = 'help-pane';
		var container = 'help-container';
		var send = 'send';
	
		$(pane).style.height = '0px';
		
		var p = new Element('p');
		var img = new Element('img', {'src':'templates/stolas/images/anim/circling-ball-black.gif'}).injectInside(p);
		var txt = document.createTextNode(' Sending report ...');
		p.appendChild(txt);
		$(HUB.TroubleReport.loader).appendChild(p);

		var fa = new Fx.Slider(pane, {duration: 800,onComplete: function(){
				if(HUB.trOpen == true) { 
					HUB.trOpen = false; 
					$(container).style.visibility = 'hidden';
					$(send).style.display = 'none';
				} else { HUB.trOpen = true; }
			}});
	
		var tab = $('tab');
		if(tab) {
			var alink = tab.childNodes[0];
			if(alink) {
				alink.onclick = function() {
					if(HUB.trOpen == false) { 
						$(container).style.visibility = 'visible';
						$(send).style.display = 'inline';
					}
					fa.toggle();
					return false;
				}
			}
		
			var frm = document.getElementById(HUB.TroubleReport.form);
			if(frm) {
				//frm.addEvent('submit', );
				frm.onsubmit = function() {
						HUB.TroubleReport.validateFields();
						return false;
					}
			}
		}
	},

	hideTimer: function() {
		HUB.TroubleReport.hide(HUB.TroubleReport.loader);
		HUB.TroubleReport.show(HUB.TroubleReport.success);
	},
	
	resetForm: function() {
		$(HUB.TroubleReport.problem).value = '';
		HUB.TroubleReport.hide(HUB.TroubleReport.success);
		HUB.TroubleReport.show(HUB.TroubleReport.form);
	},
	
	reshowForm: function() {
		HUB.TroubleReport.hide(HUB.TroubleReport.success);
		HUB.TroubleReport.show(HUB.TroubleReport.form);
	},
	
	sendReport: function() {
		HUB.TroubleReport.hide(HUB.TroubleReport.form);
		HUB.TroubleReport.show(HUB.TroubleReport.loader);
		
		$(HUB.TroubleReport.form).send({
				update: HUB.TroubleReport.success,
				onComplete: function() {
					HUB.TroubleReport.hideTimer();
				}
        });
	},
	
	validateFields: function() {
		var name    = $(HUB.TroubleReport.name);
		var email   = $(HUB.TroubleReport.email);
		var login   = $(HUB.TroubleReport.login);
		var problem = $(HUB.TroubleReport.problem);

		var whiteSpace = /^[\s]+$/;
		
		if ( problem.value == '' || whiteSpace.test(problem.value) ) {
			alert("You're trying to send an empty trouble report. Please type something and try again.");
		} else if ( email.value == '' || HUB.TroubleReport.validateEmail(email.value) === false) {
			alert("Please provide a valid email address");
			email.focus();
		} else {
			HUB.TroubleReport.sendReport();
		}
	},
	
	validateEmail: function(emailStr) {
		var emailReg1 = /(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/; // not valid
		var emailReg2 = /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,6}|[0-9]{1,3})(\]?)$/; // valid
		if (!(!emailReg1.test(emailStr) && emailReg2.test(emailStr))) {
			return false;
		}
		return true;
	},
	
	hide: function(obj) {
		$(obj).style.display = 'none';
	},
	
	show: function(obj) {
		$(obj).style.display = 'block';
	}
}

HUB.trOpen = false;

//----------------------------------------------------------

window.addEvent('domready', HUB.TroubleReport.initialize);
