/**
 * Autosave MooTools 1.11 plugin based off of Rik Lomas' (rikrikrik.com) jQuery plugin
 *
 */

var AutoSave = new Class({
	
	options: { 
		element: null,
		interval: 10000,
		unique: '',
		notify: true,
		path: '',
		onBeforeSave: function() { },
		onAfterSave: function() { },
		onBeforeRestore: function() { },
		onAfterRestore: function() { },
		cookieCharMaxSize: 2000,
		cookieExpiryLength: 1
	},
	
	ev: false,
	doSave: false,
	ti: 0,
	ci: 0,
	ri: 0,
	saveOnUnload: true,
	
	values: {
		'text': {},
		'check': {},
		'radio': {}
	},
	
	initialize: function(element, options) {
		this.element = $(element);
		this.setOptions(options);
		
		if (this.options.notify) {
			this.notify = new Element('div', {
				id: 'as-notify'
			}).appendText('Saving...').injectInside(document.body);
			this.fx = new Fx.Style(this.notify, 'opacity', {duration: 300, wait: false}).set(0);
		}
		
		this.ev = false;
		this.doSave = false;
		this.ti = 0;
		this.ci = 0;
		this.ri = 0;
		
		$ES('textarea', this.element).each(function(trigger) {
			this.values.text[this.ti] = trigger;
			trigger.addEvent('keyup', function(trigger) {
				this.doSave = true;
			}.bindWithEvent(this));
			this.ti++;
		}, this);
		
		$ES('input[type=text]', this.element).each(function(trigger) {
			this.values.text[this.ti] = trigger;
			trigger.addEvent('keyup', function(trigger) {
				this.doSave = true;
			}.bindWithEvent(this));
			this.ti++;
		}, this);
		
		$ES('select', this.element).each(function(trigger) {
			this.values.text[this.ti] = trigger;
			trigger.addEvent('change', function(trigger) {
				this.doSave = true;
			}.bindWithEvent(this));
			this.ti++;
		}, this);
		
		$ES('input[type=radio]', this.element).each(function(trigger) {
			this.values.radio[this.ri] = trigger;
			trigger.addEvent('click', function(trigger) {
				this.doSave = true;
			}.bindWithEvent(this));
			this.ri++;
		}, this);
		
		$ES('input[type=checkbox]', this.element).each(function(trigger) {
			this.values.check[this.ci] = trigger;
			trigger.addEvent('click', function(trigger) {
				this.doSave = true;
			}.bindWithEvent(this));
			this.ci++;
		}, this);
		
		this.restore();
		
		if (!this.ev) { 
			this.setEvents();
		}
	},
	
	setEvents: function() {
		$$('.autosave_removecookies').each(function(trigger) {
			trigger.addEvent('click', this.removeAllCookies.bindWithEvent(this));
		}, this);
		$$('.autosave_restore').each(function(trigger) {
			trigger.addEvent('click', this.restore.bindWithEvent(this));
		}, this);
		
		this.element.addEvent('submit', this.removeAllCookies.bindWithEvent(this));
		
		window.addEvent('unload', function(event) { 
			if (this.saveOnUnload) {
				this.go();
			}
			return true;
		}.bindWithEvent(this));
	
		setInterval(function() {
			if (this.doSave) {
				this.go();	
				this.doSave = false;
			} 
		}.bind(this), this.options.interval);
		
		this.ev = true;
	},
	
	go: function() {
		this.options.onBeforeSave();
		
		var m = this.values;
		var u = this.options.unique;
		
		function saveCookie(i, j, content, dur, pth)
		{
			Cookie.set('autosave_'+u+i+'_'+j, content, { duration: dur, path: pth });
		}
	
		function removeBiggerCookies(i, pth)
		{
			var j = 1;
			var cookie = Cookie.get('autosave_'+u+i+'_'+j);
			while (cookie !== null && cookie !== false && j < 20)
			{
				Cookie.remove('autosave_'+u+i+'_'+j, {path:pth});
			}
		}

		for (i in m.text)
		{
			var content;
			var j = 0;
		
			content = $(m.text[i]).value;
			size = (content) ? content.length : 0;
		
			if (size < this.options.cookieCharMaxSize) {
				saveCookie(i, 0, content, this.options.cookieExpiryLength, this.options.path);
			} else {
				removeBiggerCookies(i, this.options.path);
				for (var k = 0; k < size; k += this.options.cookieCharMaxSize)
				{
					saveCookie(i, j, content.substr(k, this.options.cookieCharMaxSize), this.options.cookieExpiryLength, this.options.path);
					j += 1;
				}
			}
		}
	
		var cookiecheck = '';
		for (i in m.check)
		{
			//var content = $(m.check[i]).getAttribute('checked') ? '1' : '0';
			var content = $(m.check[i]).checked ? '1' : '0';
			cookiecheck += content + ',';
		}
		Cookie.set('autosave_'+u+'_check', cookiecheck, {path:this.options.path});
	
		var cookieradio = '';
		for (i in m.radio)
		{
			if ($(m.radio[i]).checked) {
				cookieradio += i + ',';
			}
		}
		Cookie.set('autosave_'+u+'_radio', cookieradio, {path:this.options.path});
	
		if (this.options.notify) {
			this.saving(); 
		}
		
		this.options.onAfterSave();
	},
	
	restore: function() {
		this.options.onBeforeRestore();
		
		var m = this.values;
		var u = this.options.unique;
	
		for (i in m.text)
		{
			var j = 0;
			var restored = '';
			while (Cookie.get('autosave_'+u+i+'_'+j) !== null && Cookie.get('autosave_'+u+i+'_'+j) !== false && j < 20)
			{
				restored += Cookie.get('autosave_'+u+i+'_'+j);
				j += 1;
			}
			$(m.text[i]).value = restored;
		}
	
		var ccheck = Cookie.get('autosave_'+u+'_check');
		var cookiecheck = (ccheck) ? ccheck.split(',') : null;
		if (cookiecheck !== null)
		{
			cookiecheck.pop(); // Get rid of last element
			for (i in m.check)
			{
				//var chek = (cookiecheck[i] == '1') ? 'checked' : '';
				var chek = (cookiecheck[i] == '1') ? true : false;
				$(m.check[i]).checked = chek;
				//$(m.check[i]).setAttribute('checked') = chek;
			}
		}
			
		var cradio = Cookie.get('autosave_'+u+'_radio');
		var cookieradio = (cradio) ? cradio.split(',') : null;
		if (cookieradio !== null) {
			cookieradio.pop(); // Get rid of last element
			for (i in cookieradio)
			{
				if (i < cookieradio.length) {
					$(m.radio[cookieradio[i]]).checked = true;
					//$(m.radio[cookieradio[i]]).setAttribute('checked') = 'checked';
				}
			}
		}
				
		this.options.onAfterRestore();
	},
	
	removeAllCookies: function () {
		var u = this.options.unique;

		for (var i = 0; i < 200; i++)
		{
			var j = 0;
			while (Cookie.get('autosave_'+u+i+'_'+j) !== null && Cookie.get('autosave_'+u+i+'_'+j) !== false && j < 20)
			{
				Cookie.remove('autosave_'+u+i+'_'+j, {path:this.options.path});
			}
		}
	
		//if (this.options.path) {
			Cookie.remove('autosave_'+u+'_check', {path:this.options.path});
			Cookie.remove('autosave_'+u+'_radio', {path:this.options.path});
		/*} else {
			Cookie.remove('autosave_'+u+'_check');
			Cookie.remove('autosave_'+u+'_radio');
		}*/
		
		this.saveOnUnload = false;
	},
	
	saving: function() {
		this.fx.start(1);
		this.hide.delay(1500, this);
	},
	
	hide: function() {
		this.fx.start(0);
	}
});

AutoSave.implement(new Events, new Options);

function initAutoSave()
{
	var uniq = 'stc';
	if ($('ticketid') && $('ticketid').value != '') {
		uniq = $('ticketid').value;
	}
	var TicketAutoSave = new AutoSave('commentform',{unique:uniq,notify:true,path:'/support/ticket'});
}

//----------------------------------------------------------

//window.addEvent('domready', initAutoSave);