/*
Scipt:
	AppleSearch.js

NOTE:
	Accepts a form ID and various class/ID names for elements within the form. If it
	finds an appropriate form, it creates and attaches a couple elements and attaches
	an accompanying stylesheet to make the search field look and act like Apple's 
	"search" input element.

	<form method="get" action="index.php?option=com_search" id="searchform">
		<fieldset>
			<label for="searchword">Search</label>
			<input type="text" name="searchword" id="searchword" value="enter keyword" />
			<input type="submit" name="submitquery" id="submitquery" value="Go" />
			<input type="hidden" name="option" value="com_search" />
			<span id="endcap"></span>
		</fieldset>
		<div class="clear"></div>
	</form>
*/

var AppleSearch = new Class({
	
	options: { 
		element:     null,
		input:      'searchword',
		label:      'searchword-label',
		labeltext:  'Search',
		labelcolor: '#ccc',
	},

	input: null,
	label: null,

	initialize: function(element, options) {
		this.element = $(element);
		this.setOptions(options);

		this.input = $(this.options.input);
		if (!this.input) {
			return;
		}
		
		this.label = $(this.options.label);
		this.options.labelcolor = this.label.getStyle('color');
		this.options.labeltext = this.label.firstChild.nodeValue;

		if (navigator.userAgent.toLowerCase().indexOf('safari') < 0  && document.getElementById) {
			this.inputColor = this.input.getStyle('color');
			
			this.input.setStyles({'color':this.options.labelcolor}).setProperties({'value':this.options.labeltext});
			this.input.addEvent('focus', this.eventFocus.bindWithEvent(this));
			this.input.addEvent('blur', this.eventBlur.bindWithEvent(this));
		} else {
			this.input.setStyles({
				'width':'214px',
				'padding-left': '0'
			}).setProperties({
				'type':'search',
				'value':'',
				'placeholder':this.options.labeltext,
				'autosave':'bsn_srch',
				'results':'5'
			});
		}
	},

	eventFocus: function(event) {
		if (this.input.getProperty('value') == this.options.labeltext) {
			this.input.setStyles({'color':this.inputColor}).setProperties({'value':''});
		}
	},
	
	eventBlur: function(event) {
		if (this.input.getProperty('value') == '') {
			this.input.setStyles({'color':this.options.labelcolor}).setProperties({'value':this.options.labeltext});
		}
	}
});

AppleSearch.implement(new Options);

function initAppleSearch()
{
	var HUBAppleSearch = new AppleSearch('searchform',{});
}

//----------------------------------------------------------

window.addEvent('domready', initAppleSearch);