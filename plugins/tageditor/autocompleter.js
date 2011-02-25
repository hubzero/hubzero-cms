/**
 * Autocompleter
 *
 * @version		1.0rc4
 *
 * @license		MIT-style license
 * @author		Harald Kirschner <mail [at] digitarald.de>
 * @copyright	Author
 */
var Autocompleter = {};

Autocompleter.Base = new Class({

	options: {
		tagger: Class.empty,
		minLength: 1,
		useSelection: true,
		markQuery: true,
		inheritWidth: true,
		maxChoices: 10,
		injectChoice: null,
		onSelect: Class.empty,
		onShow: Class.empty,
		onHide: Class.empty,
		customTarget: null,
		className: 'autocompleter-choices',
		zIndex: 42,
		observerOptions: {},
		fxOptions: {},
		overflown: []
	},

	initialize: function(el, options) {
		this.setOptions(options);
		this.element = $(el);
		this.build();
		this.observer = new Observer(this.element, this.prefetch.bind(this), $merge({
			delay: 400
		}, this.options.observerOptions));
		this.value = this.observer.value;
		this.queryValue = null;
	},

	// build - Initialize DOM
	// Builds the html structure for choices and appends the events to the element.
	// Override this function to modify the html generation.
	build: function() {
		if ($(this.options.customTarget)) this.choices = this.options.customTarget;
		else {
			this.choices = new Element('ul', {
				'class': this.options.className,
				styles: {zIndex: this.options.zIndex}
			}).injectInside(document.body);
			this.fix = new OverlayFix(this.choices);
		}
		this.fx = this.choices.effect('opacity', $merge({
			wait: false,
			duration: 200
		}, this.options.fxOptions))
			.addEvent('onStart', function() {
				if (this.fx.now) return;
				this.choices.setStyle('display', '');
				this.fix.show();
			}.bind(this))
			.addEvent('onComplete', function() {
				if (this.fx.now) return;
				this.choices.setStyle('display', 'none');
				this.fix.hide();
			}.bind(this)).set(0);
		this.element.setProperty('autocomplete', 'off')
			.addEvent(window.ie ? 'keydown' : 'keypress', this.onCommand.bindWithEvent(this))
			.addEvent('mousedown', this.onCommand.bindWithEvent(this, [true]))
			.addEvent('focus', this.toggleFocus.bind(this, [true]))
			.addEvent('blur', this.toggleFocus.bind(this, [false]))
			.addEvent('trash', this.destroy.bind(this));
	},

	destroy: function() {
		this.choices.remove();
	},

	toggleFocus: function(state) {
		this.focussed = state;
		if (!state) this.hideChoices();
	},

	onCommand: function(e, mouse) {
		if (mouse && this.focussed) this.prefetch();
		if (e.key && !e.shift) switch (e.key) {
			case 'enter':
				if (this.selected && this.visible) {
					this.choiceSelect(this.selected);
					e.stop();
				} return;
			case 'up': case 'down':
				if (this.observer.value != (this.value || this.queryValue)) this.prefetch();
				else if (this.queryValue === null) break;
				else if (!this.visible) this.showChoices();
				else {
					this.choiceOver((e.key == 'up')
						? this.selected.getPrevious() || this.choices.getLast()
						: this.selected.getNext() || this.choices.getFirst() );
					this.setSelection();
				}
				e.stop(); return;
			case 'esc': this.hideChoices(); return;
		}
		this.value = false;
	},

	setSelection: function() {
		if (!this.options.useSelection) return;
		var startLength = this.queryValue.length;
		if (this.element.value.indexOf(this.queryValue) != 0) return;
		var insert = this.selected.inputValue.substr(startLength);
		if (document.getSelection) {
			this.element.value = this.queryValue + insert;
			this.element.selectionStart = startLength;
			this.element.selectionEnd = this.element.value.length;
		} else if (document.selection) {
			var sel = document.selection.createRange();
			sel.text = insert;
			sel.move("character", - insert.length);
			sel.findText(insert);
			sel.select();
		}
		this.value = this.observer.value = this.element.value;
	},

	hideChoices: function() {
		if (!this.visible) return;
		this.visible = this.value = false;
		this.observer.clear();
		this.fx.start(0);
		this.fireEvent('onHide', [this.element, this.choices]);
	},

	showChoices: function() {
		if (this.visible || !this.choices.getFirst()) return;
		this.visible = true;
		var pos = this.element.getCoordinates(this.options.overflown);
		var left = pos.left;
		if (Browser.detect().name == 'trident' && Browser.detect().version <= 5) {

			var pos2 = $('maininput').getParent().getParent().getParent().getCoordinates(this.options.overflown);
			left = pos.left - pos2.left + 145;
			// fix for IE7 dispay of autocompleter when used in aside right div
			if(left < 160) {
			left = pos.left;
			}

		}
		this.choices.setStyles({
			left: left,
			top: pos.bottom
		});
		if (this.options.inheritWidth) this.choices.setStyle('width', pos.width);
		this.fx.start(1);
		this.choiceOver(this.choices.getFirst());
		this.fireEvent('onShow', [this.element, this.choices]);
	},

	prefetch: function() {
		if (this.element.value.length < this.options.minLength) this.hideChoices();
		else if (this.element.value == this.queryValue) this.showChoices();
		else this.query();
	},

	updateChoices: function(choices) {
		this.choices.empty();
		this.selected = null;
		if (!choices || !choices.length) return;
		if (this.options.maxChoices < choices.length) choices.length = this.options.maxChoices;
		choices.each(this.options.injectChoice || function(choice, i){
			var el = new Element('li').setHTML(this.markQueryValue(choice));
			el.inputValue = choice;
			this.addChoiceEvents(el).injectInside(this.choices);
		}, this);
		this.showChoices();
	},

	choiceOver: function(el) {
		if (this.selected) this.selected.removeClass('autocompleter-selected');
		this.selected = el.addClass('autocompleter-selected');
	},

	choiceSelect: function(el) {
		this.observer.value = this.element.value = el.inputValue;
		this.hideChoices();
		this.fireEvent('onSelect', [this.element], 20);
	},

	// Marks the queried word in the given string with <span class="autocompleter-queried">*</span>
	// Call this i.e. from your custom parseChoices, same for addChoiceEvents
	//
	// @param		{String} Text
	// @return		{String} Text
	//
	markQueryValue: function(txt) {
		return (this.options.markQuery && this.queryValue) ? txt.replace(new RegExp('^(' + this.queryValue.escapeRegExp() + ')', 'i'), '<span class="autocompleter-queried">$1</span>') : txt;
	},

	// Appends the needed event handlers for a choice-entry to the given element.
	//
	// @param		{Element} Choice entry
	// @return		{Element} Choice entry
	//
	addChoiceEvents: function(el) {
		return el.addEvents({
			mouseover: this.choiceOver.bind(this, [el]),
			mousedown: this.choiceSelect.bind(this, [el])
		});
	}
});

Autocompleter.Base.implement(new Events);
Autocompleter.Base.implement(new Options);

Autocompleter.Local = Autocompleter.Base.extend({

	options: {
		minLength: 0,
		filterTokens : null
	},

	initialize: function(el, tokens, options) {
		this.parent(el, options);
		this.tokens = tokens;
		if (this.options.filterTokens) this.filterTokens = this.options.filterTokens.bind(this);
	},

	query: function() {
		this.hideChoices();
		this.queryValue = this.element.value;
		this.updateChoices(this.filterTokens());
	},

	filterTokens: function(token) {
		var regex = new RegExp('^' + this.queryValue.escapeRegExp(), 'i');
		return this.tokens.filter(function(token) {
			return regex.test(token);
		});
	}

});

Autocompleter.Ajax = {};

Autocompleter.Ajax.Base = Autocompleter.Base.extend({

	options: {
		postVar: 'value',
		postData: {},
		ajaxOptions: {},
		onRequest: Class.empty,
		onComplete: Class.empty
	},

	initialize: function(el, url, options) {
		this.parent(el, options);
		this.ajax = new Ajax(url, $merge({
			autoCancel: true
		}, this.options.ajaxOptions));
		this.ajax.addEvent('onComplete', this.queryResponse.bind(this));
		this.ajax.addEvent('onFailure', this.queryResponse.bind(this, [false]));
	},

	query: function(){
		var data = $extend({}, this.options.postData);
		data[this.options.postVar] = this.element.value;
		this.fireEvent('onRequest', [this.element, this.ajax]);
		this.ajax.request(data);
	},

	// Inherated classes have to extend this function and use this.parent(resp)
	//
	// @param		{String} Response
	//
	queryResponse: function(resp) {
		this.value = this.queryValue = this.element.value;
		this.selected = false;
		this.hideChoices();
		this.fireEvent(resp ? 'onComplete' : 'onFailure', [this.element, this.ajax], 20);
	}

});

Autocompleter.Ajax.Json = Autocompleter.Ajax.Base.extend({

	queryResponse: function(resp) {
		this.parent(resp);
		var choices = Json.evaluate(resp || false);
		if (!choices || !choices.length) return;
		this.updateChoices(choices);
	}

});

Autocompleter.Ajax.Xhtml = Autocompleter.Ajax.Base.extend({

	options: {
		parseChoices: null
	},

	queryResponse: function(resp) {
		this.parent(resp);
		if (!resp) return;
		this.choices.setHTML(resp).getChildren().each(this.options.parseChoices || this.parseChoices, this);
		this.showChoices();
	},

	parseChoices: function(el) {
		var value = el.innerHTML;
		el.inputValue = value;
		el.setHTML(this.markQueryValue(value));
	}

});

Autocompleter.MultiSelectable = {};
Autocompleter.MultiSelectable.Base = Autocompleter.Base.extend({
  options: {
    multiSelect: true,
    wrapSelectionsWithSpacesInQuotes: true,
    useSelection: false //doesn't work well with entries containing spaces
  },

  choiceSelect: function(el) {
    //when multiSelect is enabled, append to field value instead of overwriting it.
    //this.observer.value = this.element.value = this.options.multiSelect? this.element.value.trimLastElement() + el.inputValue + ", ":el.inputValue;
	this.observer.value = this.element.value = this.options.multiSelect ? this.element.value.trimLastElement() + el.inputValue : el.inputValue;
	if (this.options.tagger) {
		this.options.tagger.add(this.observer.value);
		this.observer.value = this.element.value = '';
    }
	this.hideChoices();
    this.fireEvent('onSelect', [this.element], 20);
  },

  prefetch: function() {
    //when multiSelect is enabled, min len test on last query element so that min len is tested for every elements.
	var elValueToTest = this.options.multiSelect? this.element.value.lastElement(): this.element.value;

    if (elValueToTest.length < this.options.minLength) this.hideChoices();
		else if (elValueToTest == this.queryValue) this.showChoices();
		else this.query();
	},

  onCommand: function(e, mouse) {
		if (mouse && this.focussed) this.prefetch();
		if (e.key && !e.shift) switch (e.key) {
			case 'enter':
				if (this.selected && this.visible) {
					if (Browser.detect().name != 'trident') {
						this.options.tagger.dispose(this.options.tagger.current.getPrevious());
					}
					this.choiceSelect(this.selected);
					e.stop();
				} return;
			case 'up': case 'down':
        //when in multiselect mode, test on last element of element (observer) value or it will not
        //listen to key up and down.
        var elValueToTest = this.options.multiSelect? this.observer.value.lastElement(): this.observer.value;
        if (elValueToTest != (this.value || this.queryValue)) this.prefetch();
				else if (this.queryValue === null) break;
				else if (!this.visible) this.showChoices();
				else {
					this.choiceOver((e.key == 'up')
						? this.selected.getPrevious() || this.choices.getLast()
						: this.selected.getNext() || this.choices.getFirst() );
					this.setSelection();
				}
				e.stop(); return;
			case 'esc': this.hideChoices(); return;
		}
		this.value = false;
	},

  updateChoices: function(choices) {
		this.choices.empty();
		this.selected = null;
		if (!choices || !choices.length) return;
		if (this.options.maxChoices < choices.length) choices.length = this.options.maxChoices;
		choices.each(this.options.injectChoice || function(choice, i){
			var el = new Element('li').setHTML(this.markQueryValue(choice));
      //wrapping in quotes is requested/needed
      el.inputValue = this.options.wrapSelectionsWithSpacesInQuotes? choice.wrapInQuotes(): choice;
      this.addChoiceEvents(el).injectInside(this.choices);
		}, this);
		this.showChoices();
	}
});

Autocompleter.MultiSelectable.Ajax = {};
Autocompleter.MultiSelectable.Ajax.Base = Autocompleter.MultiSelectable.Base.extend({
  // duplicated code (same as Autocompleter.Ajax.Base)
  options: {
		postVar: 'value',
		postData: {},
		ajaxOptions: {},
		onRequest: Class.empty,
		onComplete: Class.empty
	},

  // duplicated code (same as Autocompleter.Ajax.Base)
  initialize: function(el, url, options) {
		this.parent(el, options);
		this.ajax = new Ajax(url, $merge({
			autoCancel: true
		}, this.options.ajaxOptions));
		this.ajax.addEvent('onComplete', this.queryResponse.bind(this));
		this.ajax.addEvent('onFailure', this.queryResponse.bind(this, [false]));
	},

  query: function(){
    var data = $extend({}, this.options.postData);

    //query only on last element if multiSelectable is enabled if multiSelectable is enabled
		data[this.options.postVar] = this.options.multiSelect? this.element.value.lastElement(): this.element.value;

		this.fireEvent('onRequest', [this.element, this.ajax]);
		this.ajax.request(data);
	},

	queryResponse: function(resp) {
    //query only on last element if multiSelectable is enabled
		this.value = this.queryValue = this.options.multiSelect? this.element.value.lastElement(): this.element.value;

		this.selected = false;
		this.hideChoices();
		this.fireEvent(resp ? 'onComplete' : 'onFailure', [this.element, this.ajax], 20);
	}
});

//ready to use extension for JSON queries (identical to Autocompleter.Ajax.Json)
Autocompleter.MultiSelectable.Ajax.Json = Autocompleter.MultiSelectable.Ajax.Base.extend({
	queryResponse: function(resp) {
		this.parent(resp);
		var choices = Json.evaluate(resp || false);
		if (!choices || !choices.length) return;
		this.updateChoices(choices);
	}
});

//ready to use extension for Local Array queries (identical to Autocompleter.Local)
Autocompleter.MultiSelectable.Local = Autocompleter.MultiSelectable.Base.extend({
	initialize: function(el, tokens, options) {
		this.parent(el, options);
		this.tokens = tokens;
		if (this.options.filterTokens) this.filterTokens = this.options.filterTokens.bind(this);
	},

	query: function() {
		this.hideChoices();
		this.queryValue = this.element.value.lastElement();
		this.updateChoices(this.filterTokens());
	},

	filterTokens: function(token) {
		var regex = new RegExp('^' + this.queryValue.escapeRegExp(), 'i');
		return this.tokens.filter(function(token) {
			return regex.test(token);
		});
	}
});

String.extend({
  lastElement: function(separator){
	var txt = this.trim();
	var index = txt.lastIndexOf(separator || ', ');
	return (index == -1)? txt: txt.substr(index + 2, txt.length);
  },//end lastElement


  trimLastElement: function(separator){
	var txt = this.trim();
	var index = txt.lastIndexOf(separator || ', ');
	return (index == -1)? "": txt.substr(0, index + 2);
  },//end trimLastElement


  wrapInQuotes: function(){
	var index = this.trim().lastIndexOf(', ');
	return (index == -1)? this: '"' + this + '"'
  }//end wrapInQuotes
}); //end String.extend

var OverlayFix = new Class({

	initialize: function(el) {
		this.element = $(el);
		if (window.ie){
			this.element.addEvent('trash', this.destroy.bind(this));
			this.fix = new Element('iframe', {
				properties: {
					frameborder: '0',
					scrolling: 'no',
					src: 'javascript:false;'
				},
				styles: {
					position: 'absolute',
					border: 'none',
					display: 'none',
					filter: 'progid:DXImageTransform.Microsoft.Alpha(opacity=0)'
				}
			}).injectAfter(this.element);
		}
	},

	show: function() {
		if (this.fix) this.fix.setStyles($extend(
			this.element.getCoordinates(), {
				display: '',
				zIndex: (this.element.getStyle('zIndex') || 1) - 1
			}));
		if (this.fix) this.fix.setStyle('left', '0');
		return this;
	},

	hide: function() {
		if (this.fix) this.fix.setStyle('display', 'none');
		return this;
	},

	destroy: function() {
		this.fix.remove();
	}

});

//----------------------------------------------------------
// Browser detection
// Don't care for this but IE needs some personalized attention
//----------------------------------------------------------
var Browser = $merge({

	Engine: {name: 'unknown', version: 0},

	Platform: {name: (window.orientation != undefined) ? 'ipod' : (navigator.platform.match(/mac|win|linux/i) || ['other'])[0].toLowerCase()},

	Features: {xpath: !!(document.evaluate), air: !!(window.runtime), query: !!(document.querySelector)},

	Plugins: {},

	Engines: {

		presto: function(){
			return (!window.opera) ? false : ((arguments.callee.caller) ? 960 : ((document.getElementsByClassName) ? 950 : 925));
		},

		trident: function(){
			return (!window.ActiveXObject) ? false : ((window.XMLHttpRequest) ? 5 : 4);
		},

		webkit: function(){
			return (navigator.taintEnabled) ? false : ((Browser.Features.xpath) ? ((Browser.Features.query) ? 525 : 420) : 419);
		},

		gecko: function(){
			return (document.getBoxObjectFor == undefined) ? false : ((document.getElementsByClassName) ? 19 : 18);
		}

	}

}, Browser || {});

Browser.Platform[Browser.Platform.name] = true;

Browser.detect = function(){

	for (var engine in this.Engines){
		var version = this.Engines[engine]();
		if (version){
			this.Engine = {name: engine, version: version};
			this.Engine[engine] = this.Engine[engine + version] = true;
			break;
		}
	}

	return {name: engine, version: version};

};

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Tag Autocompleter
//----------------------------------------------------------
HUB.CompleteTags = {
	initialize: function() {
		var el = $('actags');

		if (el) {
			var actags = new AppleboxList(el, {'hideempty': false, 'resizable': {'step': 8}});

			var completer2 = new Autocompleter.MultiSelectable.Ajax.Json($('maininput'), '/index.php?option=com_tags&no_html=1&task=autocomplete', {
				'tagger': actags,
				'minLength': 1, // We wait for at least one character
				'overflow': true, // Overflow for more entries
				'wrapSelectionsWithSpacesInQuotes': false
			});
		}
	}
        ,
        materials: function(){
          var el = $('strMaterialType');

		if (el) {
			var actags = new AppleboxList(el, {'hideempty': false, 'resizable': {'step': 8}});

			var completer2 = new Autocompleter.MultiSelectable.Ajax.Json($('maininput'), '/warehouse/materiallist?no_html=1', {
				'tagger': actags,
				'minLength': 1, // We wait for at least one character
				'overflow': true, // Overflow for more entries
				'wrapSelectionsWithSpacesInQuotes': false
			});
		}
        }
}

//----------------------------------------------------------

window.addEvent('domready', HUB.CompleteTags.initialize);
window.addEvent('domready', HUB.CompleteTags.materials);