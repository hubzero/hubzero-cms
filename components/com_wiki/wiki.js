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
// Resource Ranking pop-ups
//----------------------------------------------------------

HUB.Wiki = {
	editButtons: new Array(),
	helpButtons: new Array(),
	
	getTemplate: function() {
		var id = $('templates');
		if (id.value != 'tc') {
			var hi = $(id.value).value;
			var co = $('pagetext');
			co.value = hi;
			
			var ji = $(id.value+'_tags').value;
			var jo = $('actags');
			jo.value = ji;
			
			if ($('maininput') && jo) {
				var ul = $($('maininput').getParent().getParent());
				var label = $($('maininput').getParent().getParent().getParent());
				label.removeChild(ul);
				
				var actags = new AppleboxList(jo, {'hideempty': false, 'resizable': {'step': 8}});

				var actkn = '';
				if ($('actkn')) {
					//actkn = '&'+$('actkn').value+'=1';
					actkn = '&admin=true';
				}

				var completer2 = new Autocompleter.MultiSelectable.Ajax.Json($('maininput'), '/index.php?option=com_tags&no_html=1&task=autocomplete'+actkn, {
					'tagger': actags,
					'minLength': 1, // We wait for at least one character
					'overflow': true, // Overflow for more entries
					'wrapSelectionsWithSpacesInQuotes': false
				});
			}
		} else {
			var co = $('pagetext');
			co.value = '';
		}
	},
	
	initialize: function() {
		if ($('templates')) {
			$('templates').addEvent('change', HUB.Wiki.getTemplate);
		}
		
		var toolbar = $('wiki-toolbar');
		if (!toolbar) {
			return false;
		}
		
		var textbox = $('pagetext');
		if (!textbox) {
			return false;
		}
		
		// Don't generate buttons for browsers which don't fully support it.
		if (!(document.selection && document.selection.createRange)
			&& textbox.selectionStart === null) {
			return false;
		}
		
		toolbar.removeClass('hidden');
		
		HUB.Wiki.generateToolbar();
		
		for (var i = 0; i < HUB.Wiki.editButtons.length; i++) 
		{
			HUB.Wiki.insertEditButton(toolbar, HUB.Wiki.editButtons[i]);
		}
		HUB.Wiki.insertHelpButton(toolbar, HUB.Wiki.helpButtons[0]);
		
		var mode = $('params_mode');
		if (mode) {
			mode.addEvent('change', HUB.Wiki.checkMode);
		}
	},

	checkMode: function() {
		var mode = $('params_mode');
		if (mode.value != 'knol') {
			$($('params_authors').parentNode).addClass('hide');
			$($('params_allow_changes').parentNode).addClass('hide');
			$($('params_allow_comments').parentNode).addClass('hide');
		} else {
			if ($($('params_authors').parentNode).hasClass('hide')) {
				$($('params_authors').parentNode).removeClass('hide');
			}
			if ($($('params_allow_changes').parentNode).hasClass('hide')) {
				$($('params_allow_changes').parentNode).removeClass('hide');
			}
			if ($($('params_allow_comments').parentNode).hasClass('hide')) {
				$($('params_allow_comments').parentNode).removeClass('hide');
			}
		}
	},

	generateToolbar: function() {
		HUB.Wiki.addButton("wiki-button-bold","Bold text","\'\'\'","\'\'\'","Bold text","mw-editbutton-bold");
		HUB.Wiki.addButton("wiki-button-italic","Italic text","\'\'","\'\'","Italic text","mw-editbutton-italic");
		HUB.Wiki.addButton("wiki-button-underline","Underline","__","__","","mw-editbutton-underline");
		HUB.Wiki.addButton("wiki-button-superscript","Superscript","^","^","","mw-editbutton-superscript");
		HUB.Wiki.addButton("wiki-button-subscript","Subscript",",,",",,","","mw-editbutton-subscript");
		HUB.Wiki.addButton("wiki-button-strikethrough","Strikethrough","~~","~~","","mw-editbutton-strikethrough");
		
		HUB.Wiki.addButton("wiki-button-link","Internal link","[","]","Link title","mw-editbutton-link");
		HUB.Wiki.addButton("wiki-button-headline","Level 2 headline","\n== "," ==\n","Headline text","mw-editbutton-headline");
		HUB.Wiki.addButton("wiki-button-image","Embedded image","[[Image(",")]]","Example.jpg","mw-editbutton-image");
		HUB.Wiki.addButton("wiki-button-file","Embedded file","[[File(",")]]","File.doc","mw-editbutton-file");
		HUB.Wiki.addButton("wiki-button-resource","Embedded resource","[[Resource(",")]]","123","mw-editbutton-resource");
		HUB.Wiki.addButton("wiki-button-math","Mathematical formula (LaTeX)","\x3cmath\x3e","\x3c/math\x3e","Insert formula here","mw-editbutton-math");
		HUB.Wiki.addButton("wiki-button-nowiki","Ignore wiki formatting","{{{","}}}","Insert non-formatted text here","mw-editbutton-nowiki");
		HUB.Wiki.addButton("wiki-button-hr","Horizontal line (use sparingly)","\n----\n","","","mw-editbutton-hr");
		HUB.Wiki.addButton("wiki-button-table","Table","\n||cell1||cell2||\n||cell3||cell4||\n","","","mw-editbutton-table");
		
		HUB.Wiki.helpButtons[HUB.Wiki.helpButtons.length] =
			{"imageId": "mw-editbutton-help",
			 "imageFile": "wiki-button-help",
			 "speedTip": "Help on formatting",
			 "tagOpen": "",
			 "tagClose": "",
			 "sampleText": ""};
	},
	
	// this function generates the actual toolbar buttons with localized text
	// we use it to avoid creating the toolbar where javascript is not enabled
	addButton: function(imageFile, speedTip, tagOpen, tagClose, sampleText, imageId) {
		// Don't generate buttons for browsers which don't fully
		// support it.
		HUB.Wiki.editButtons[HUB.Wiki.editButtons.length] =
			{"imageId": imageId,
			 "imageFile": imageFile,
			 "speedTip": speedTip,
			 "tagOpen": tagOpen,
			 "tagClose": tagClose,
			 "sampleText": sampleText};
	},
	
	// this function generates the actual toolbar buttons with localized text
	// we use it to avoid creating the toolbar where javascript is not enabled
	insertEditButton: function(parent, item) {		
		var li = document.createElement("li");
		var a = document.createElement("a");
		a.className = item.imageFile;
		if (item.imageId) a.id = item.imageId;
		a.title = item.speedTip;
		a.innerHTML = item.speedTip;
		a.onclick = function() {
			HUB.Wiki.insertTags(item.tagOpen, item.tagClose, item.sampleText);
			return false;
		};
		
		li.appendChild(a);
		parent.appendChild(li);
		return true;
	},
	
	// this function generates the actual toolbar buttons with localized text
	// we use it to avoid creating the toolbar where javascript is not enabled
	insertHelpButton: function(parent, item) {		
		var li = document.createElement("li");
		var a = document.createElement("a");
		a.className = item.imageFile + " popup";
		if (item.imageId) a.id = item.imageId;
		a.title = item.speedTip;
		a.innerHTML = item.speedTip;
		a.href = "/topics/Help:WikiFormatting";
		a.onclick = function() {
			window.open(this.href, 'popup', 'resizable=1,scrollbars=1,height=520,width=760');
			return false;
		};
		
		li.appendChild(a);
		parent.appendChild(li);
		return true;
	},
	
	// Apply tagOpen/tagClose to selection in textarea,
	// use sampleText instead of selection if there is none
	insertTags: function(tagOpen, tagClose, sampleText) {
		var txtarea = document.getElementById('pagetext');
		var selText, isSample = false;

		if (document.selection && document.selection.createRange) { // IE/Opera
			// Save window scroll position
			if (document.documentElement && document.documentElement.scrollTop) {
				var winScroll = document.documentElement.scrollTop
			} else if (document.body) {
				var winScroll = document.body.scrollTop;
			}
			
			// Get current selection  
			txtarea.focus();
			
			var range;
			if (window.getSelection) {
				range = window.getSelection();
			} else if (document.getSelection) {
		        range = document.getSelection();
			} else if (document.selection) {
				range = document.selection.createRange();
			}

			selText = range.text;
			
			// Insert tags
			checkSelectedText();
			range.text = tagOpen + selText + tagClose;
			
			// Mark sample text as selected
			if (isSample && range.moveStart) {
				if (window.opera) {
					tagClose = tagClose.replace(/\n/g,'');
				}
				range.moveStart('character', - tagClose.length - selText.length); 
				range.moveEnd('character', - tagClose.length); 
			}
			range.select();
			
			// Restore window scroll position
			if (document.documentElement && document.documentElement.scrollTop) {
				document.documentElement.scrollTop = winScroll
			} else if (document.body) {
				document.body.scrollTop = winScroll;
			}
		} else if (txtarea.selectionStart || txtarea.selectionStart == '0') { // Mozilla
			// Save textarea scroll position
			var textScroll = txtarea.scrollTop;
			
			// Get current selection
			txtarea.focus();
			var startPos = txtarea.selectionStart;
			var endPos = txtarea.selectionEnd;
			selText = txtarea.value.substring(startPos, endPos);
			
			// Insert tags
			checkSelectedText();
			
			txtarea.value = txtarea.value.substring(0, startPos)
				+ tagOpen + selText + tagClose
				+ txtarea.value.substring(endPos, txtarea.value.length);
			
			// Set new selection
			if (isSample) {
				txtarea.selectionStart = startPos + tagOpen.length;
				txtarea.selectionEnd = startPos + tagOpen.length + selText.length;
			} else {
				txtarea.selectionStart = startPos + tagOpen.length + selText.length + tagClose.length;
				txtarea.selectionEnd = txtarea.selectionStart;
			}
			
			// Restore textarea scroll position
			txtarea.scrollTop = textScroll;
		} 

		function checkSelectedText() {
			if (!selText) {
				selText = sampleText;
				isSample = true;
			} else if (selText.charAt(selText.length - 1) == ' ') { // Exclude ending space char
				selText = selText.substring(0, selText.length - 1);
				tagClose += ' ';
			} 
		}

	}
}

window.addEvent('domready', HUB.Wiki.initialize);
