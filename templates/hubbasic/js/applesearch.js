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
		labeltext:  'Search',
		labelcolor: '#ccc',
		dummy:      'dummy_css',
		css:        'applesearch',
		endcap:     'endcap',
		endcapx:    'close',
		clear:      'srch_clear',
		submitq:    'submitquery'
	},

	initialize: function(element, options) {

		this.element = $(element);
		this.setOptions(options);

		input = $(this.options.input);
		if (!input) {
			return;
		}
		this.input = input;
		this.getLabel();

		if (navigator.userAgent.toLowerCase().indexOf('safari') < 0  && document.getElementById) {
			var dummy = $(this.options.dummy);
			if (dummy) {
				var path = dummy.href.replace(/dummy\.css(\?.*)?$/,'');
				dummy.href = path + this.options.css + '.css';
			}
			
			var fieldsets = this.element.getElementsByTagName('fieldset');
			if (fieldsets) {
				this.cap = new Element('span',{
					'id':this.options.endcap
				}).addEvent('click', this.clearField.bindWithEvent(this)).injectInside(fieldsets[0]);
				
				this.clr = new Element('div',{
					'class':'clear'
				}).injectInside(this.element);
			}
	
			this.inputColor = input.getStyle('color');
			
			this.input.setStyles({'color':this.options.labelcolor}).setProperties({'value':this.options.labeltext});
			this.input.addEvent('keyup', this.eventKeyup.bindWithEvent(this));
			this.input.addEvent('focus', this.eventFocus.bindWithEvent(this));
			this.input.addEvent('blur', this.eventBlur.bindWithEvent(this));
		} else {
			var labels = this.element.getElementsByTagName('label');
			if (labels) {
				for (i = 0; i < labels.length; i++) 
				{
					if (labels[i].getAttribute('for') == this.options.input) {
						labels[i].style.display = 'none';
					}
				}
			}

			submitq = $(this.options.submitq);
			if (submitq) {
				submitq.setStyles({
					'display':'none'
				});
			}
			
			this.input.setStyles({
				'width':'214px'
			}).setProperties({
				'type':'search',
				'value':'',
				'placeholder':this.options.labeltext,
				'autosave':'bsn_srch',
				'results':'5'
			});
		}
	},

	eventKeyup: function(event) {
		this.onChange();
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
	},

	getLabel: function() {
		var labels = this.element.getElementsByTagName('label');
		var tlabel;
		
		if (labels) {
			for (i = 0; i < labels.length; i++) 
			{
				if (labels[i].getAttribute('for') == this.options.input) {
					tlabel = labels[i];
				}
			}
			if (!tlabel) {
				input = $(this.options.input);
				tlabel = input.parentNode;
			}
			if (tlabel.firstChild.nodeValue != null && 
				tlabel.firstChild.nodeValue != 'null' && 
				tlabel.firstChild.nodeValue != '') {
				this.options.labeltext = tlabel.firstChild.nodeValue;
			}
			var value = tlabel.style['color'];
			if (!value) {
				if (document.defaultView && document.defaultView.getComputedStyle) {
					var css = document.defaultView.getComputedStyle(tlabel, null);
					value = css ? css.getPropertyValue('color') : null;
				} else if (tlabel.currentStyle) {
					value = tlabel.currentStyle['color'];
				}
			}
			this.options.labelcolor = value;
		}
	},

	onChange: function() {
		var v = this.input.getProperty('value');
		if (v != '' && v != this.options.labeltext) {
			this.cap.setProperties({'class':this.options.endcapx});
		} else if (v == '' || v == this.options.labeltext) {
			this.cap.setProperties({'class':''});
		}
	},
	
	clearField: function(event) {
		this.input.setStyles({'color':this.options.labelcolor}).setProperties({'value':this.options.labeltext});
		this.onChange();
	}
});

AppleSearch.implement(new Options);

function initAppleSearch()
{
	var HUBAppleSearch = new AppleSearch('searchform',{});
}

//----------------------------------------------------------

window.addEvent('domready', initAppleSearch);
