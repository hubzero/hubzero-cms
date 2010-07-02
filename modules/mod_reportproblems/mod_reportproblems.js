//----------------------------------------------------------
// Slider
//----------------------------------------------------------
if (typeof(Fx) === 'undefined') {
	// Stop everything
} else {
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
	// Establish the namespace if it doesn't exist
	//----------------------------------------------------------
	if (!HUB) {
		var HUB = {
			Modules: {}
		};
	} else if (!HUB.Modules) {
		HUB.Modules = {};
	}

	//----------------------------------------------------------
	// Trouble Report form
	//----------------------------------------------------------
	HUB.Modules.ReportProblems = new Class({

		options: { 
			container: null,
			paneId:    'help-pane',
			formId:    'troublereport',
			nameId:    'trName',
			emailId:   'trEmail',
			loginId:   'trLogin',
			problemId: 'trProblem',
			captchaId: 'trAnswer',
			loaderId:  'trSending',
			successId: 'trSuccess',
			sendId:    'send-form'
		},

		baseheight: '200px',

		open: false,

		initialize: function(container, options) {

			this.container = $(container);
			if (!this.container) {
				return;
			}
			this.setOptions(options);

			this.send = $(this.options.sendId);
			this.success = $(this.options.successId);

			this.pane = $(this.options.paneId);
			this.pane.setStyle('height','0px');

			var p = new Element('p').setText(' Sending report ...');
			var img = new Element('img', {'src':HUB.Base.templatepath+'images/anim/circling-ball-black.gif'}).injectTop(p);

			this.loader = $(this.options.loaderId);
			p.injectInside(this.loader);

			var fa = new Fx.Slider(this.pane, {
				duration: 800,
				onComplete: function(){
					if (this.open == true) { 
						this.open = false; 
						this.container.setStyle('visibility','hidden');
						this.send.setStyle('display','none');
					} else {
						this.open = true;
					}
				}
			});

			// NEES' version of close button, should probably revert to hubzero default
			this.closetab = $('closethis');
			if(this.closetab) {
				this.closetab.addEvent('click', function(event){
					//event.stop();
					fa.toggle();
					this.open = false;
					return false;
				});
			}

			var res = new Element('div', {
				id: 'help-btn-close',
				alt: 'Close',
				title: 'Close',
				events: {
					'click': function(event) {
						fa.toggle();
					}
				}
			}).injectInside(this.pane);

			this.tab = $('tab');
			if (this.tab) {
				this.tab.addEvent('click', function(e) {
					new Event(e).stop();
					if (this.open == false) { 
						this.container.setStyle('visibility','visible');
						this.send.setStyle('display','inline');
					}
					fa.toggle();
					return false;
				}.bindWithEvent(this));

				this.name    = $(this.options.nameId);
				this.email   = $(this.options.emailId);
				this.login   = $(this.options.loginId);
				this.problem = $(this.options.problemId);
				this.answer  = $(this.options.captchaId);

				this.frm = $(this.options.formId);
				if (this.frm) {
					var d = document.createElement('DIV');
					d.innerHTML = '<iframe src="about:blank" id="upload_target" name="upload_target" style="width:0px;height:0px;border:0px solid #fff;"></iframe>';
					document.body.appendChild(d);
					
					this.frm.target = 'upload_target';
					this.frm.addEvent('submit', function(e) {
						new Event(e).stop();
						this.validateFields();
					}.bindWithEvent(this));

					this.baseheight = this.frm.getStyle('height');
				}
			}
		},

		hideTimer: function() {
			this.loader.hide();
			this.success.innerHTML = document.getElementById('upload_target').contentWindow.document.getElementById('report-response').innerHTML;
			this.success.setStyles({
				'display':'block',
				'height':this.baseheight
			});
		},

		resetForm: function() {
			this.problem.setProperties({'value':''});
			this.success.hide();
			this.frm.setStyle('display','block');
		},

		reshowForm: function() {
			this.success.hide();
			this.frm.setStyle('display','block');
		},

		sendReport: function() {
			this.frm.hide();
			this.loader.setStyles({
				'display':'block',
				'height':this.baseheight
			});
			success = this.success;
			//AIM.submit(this.frm, {'onStart' : startCallback, 'onComplete' : completeCallback});
			/*this.frm.send({
				update: success,
				onComplete: function() {
					HUB.ReportProblem.hideTimer();
				}
	        });*/
			this.frm.submit();
		},

		validateFields: function() {
			var whiteSpace = /^[\s]+$/;

			if (this.problem.value == '' || whiteSpace.test(this.problem.value) ) {
				alert("You're trying to send an empty trouble report. Please type something and try again.");
				this.problem.focus();
			} else if (this.name.value == '' || whiteSpace.test(this.name.value) ) {
				alert("The 'name' field is required. Please type something and try again.");
				this.name.focus();
			} else if (this.email.value == '' || this.validateEmail(this.email.value) === false) {
				alert("Please provide a valid email address.");
				this.email.focus();
			} else if (this.answer.value == '') {
				alert("Please provide an answer to the math question.");
				this.answer.focus();
			} else {
				this.sendReport();
			}
		},

		validateEmail: function(emailStr) {
			var emailReg1 = /(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/; // not valid
			var emailReg2 = /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,6}|[0-9]{1,3})(\]?)$/; // valid
			if (!(!emailReg1.test(emailStr) && emailReg2.test(emailStr))) {
				return false;
			}
			return true;
		}
	});

	HUB.Modules.ReportProblems.implement(new Events, new Options);

	//----------------------------------------------------------

	function initReportProblem()
	{
		HUB.ReportProblem = new HUB.Modules.ReportProblems('help-container',{});
	}

	//----------------------------------------------------------

	window.addEvent('domready', initReportProblem);
}
