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
// AppleSearch
//----------------------------------------------------------

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
		if (!$(element)) {
			return;
		}

		this.element = $(element);
		this.setOptions(options);

		input = $(this.options.input);
		
		this.getLabel();

		if( navigator.userAgent.toLowerCase().indexOf('safari') < 0  && document.getElementById ) {
			var dummy = $(this.options.dummy);
			var path = dummy.href.replace(/dummy\.css(\?.*)?$/,'');
			if (dummy)	dummy.href = path + this.options.css + '.css';

			var fieldsets = this.element.getElementsByTagName('fieldset');
			if (fieldsets) {
				var cap = document.createElement('span');
				cap.setAttribute('id',this.options.endcap);
				fieldsets[0].appendChild(cap);
				
				var d_clr = document.createElement('div');
				d_clr.setAttribute('class','clear');
				this.element.appendChild(d_clr);
				// <form method="get" action="index.php?option=com_search" id="searchform">
				//  <fieldset>
				//   <label for="searchword">Search</label>
				//   <input type="text" name="searchword" id="searchword" value="enter keyword" />
				//   <input type="submit" name="submitquery" id="submitquery" value="Go" />
				//   <input type="hidden" name="option" value="com_search" />
				//   <span id="endcap"></span>
				//  </fieldset>
				//  <div class="clear"></div>
				// </form>
			}
	
			this.inputColor = input.getStyle('color');
			
			if(input) {
				this.input = input;
				input.value = this.options.labeltext;
				input.style.color = this.options.labelcolor;
				
				input.addEvent('keyup', this.eventKeyup.bindWithEvent(this));
				input.addEvent('focus', this.eventFocus.bindWithEvent(this));
				input.addEvent('blur', this.eventBlur.bindWithEvent(this));
			}
			
			this.element.onsubmit = function() {
					if(input && input != this.options.labeltext) {
						return true;
					} else {
						return false;
					}
				}
		} else {
			if(input) {
				var labels = this.element.getElementsByTagName('label');
				if(labels) {
					for (i = 0; i < labels.length; i++) {
						if (labels[i].getAttribute('for') == this.options.input) {
							labels[i].style.display = 'none';
						}
					}
				}

				submitq = $(this.options.submitq);
				if(submitq) {
					submitq.style.display = 'none';
				}
				
				input.type = 'search';
				input.value = '';
				input.style.width = '180px';
				input.setAttribute('placeholder',this.options.labeltext);
				input.setAttribute('autosave','bsn_srch');
				input.setAttribute('results','5');
			}
		}
	},

	eventKeyup: function(event) {
		this.onChange();
	},

	eventFocus: function(event) {
		if (this.input.value == this.options.labeltext) {
			this.input.value = '';
			this.input.style.color = this.inputColor;
		}
	},
	
	eventBlur: function(event) {
		if (this.input.value == '') {
			this.input.value = this.options.labeltext;
			this.input.style.color = this.options.labelcolor;
		}
	},

	getLabel: function() {
		var labels = this.element.getElementsByTagName('label');
		var tlabel;
		
		if(labels) {
			for (i = 0; i < labels.length; i++) {
				if (labels[i].getAttribute('for') == this.options.input) {
					tlabel = labels[i];
				}
			}
			if(!tlabel) {
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
		var input = $(this.options.input);
		var cap   = $(this.options.endcap);

		if (input.value != '' && input.value != this.options.labeltext) {
			if(cap) {
				cap.className = this.options.endcapx;
				cap.addEvent('click', this.clearField.bindWithEvent(this));
			}
		} else if (input.value == '' || input.value == this.options.labeltext) {
			if(cap) {
				cap.className = '';
				cap.onclick = null;
			}
		}
	},
	
	clearField: function(event) {
		input = $(this.options.input);
		input.value = this.options.labeltext;
		input.style.color = this.options.labelcolor;
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
