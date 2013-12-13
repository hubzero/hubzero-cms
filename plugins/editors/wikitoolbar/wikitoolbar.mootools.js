/**
 * @package     hubzero-cms
 * @file        plugins/hubzero/wikitoolbar/wikitoolbar.mootools.js
 * @copyright   Copyright 2005-2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

HUB.Plugins.WikiEditorToolbar = {
	editButtons: new Array(),
	helpButtons: new Array(),
	
	initialize: function() {
		var self = HUB.Plugins.WikiEditorToolbar;

		$$('.wiki-toolbar-content').each(function(textbox) {
			id = textbox.get('id');

			var toolbar = new Element('ul');
			toolbar.set('id', 'wiki-toolbar-' + id);
			toolbar.addClass('wiki-toolbar');
			toolbar.addClass('hide');

			toolbar.inject(textbox, 'before');

			toolbar.removeClass('hide');

			// Don't generate buttons for browsers which don't fully support it.
			if (!(document.selection && document.selection.createRange)
				&& textbox.selectionStart === null) {
				return false;
			}
			
			self.generateToolbar(id);
			for (var i = 0; i < self.editButtons.length; i++) 
			{
				self.insertEditButton(toolbar, self.editButtons[i], id);
			}
			self.insertHelpButton(toolbar, self.helpButtons[0]);
			
			self.editButtons = new Array();
			self.helpButtons = new Array();
		});
	},

	generateToolbar: function(id) {
		var self = HUB.Plugins.WikiEditorToolbar;

		self.addButton("wiki-button-bold","Bold text","\'\'\'","\'\'\'","Bold text","mw-editbutton-bold-"+id);
		self.addButton("wiki-button-italic","Italic text","\'\'","\'\'","Italic text","mw-editbutton-italic-"+id);
		self.addButton("wiki-button-underline","Underline","__","__","","mw-editbutton-underline-"+id);
		self.addButton("wiki-button-superscript","Superscript","^","^","","mw-editbutton-superscript-"+id);
		self.addButton("wiki-button-subscript","Subscript",",,",",,","","mw-editbutton-subscript-"+id);
		self.addButton("wiki-button-strikethrough","Strikethrough","~~","~~","","mw-editbutton-strikethrough-"+id);
		
		self.addButton("wiki-button-link","Internal link","[","]","Link title","mw-editbutton-link-"+id);
		self.addButton("wiki-button-headline","Level 2 headline","\n== "," ==\n","Headline text","mw-editbutton-headline-"+id);
		if ($(id).hasClass("no-image-macro"))
		{
			self.addButton("wiki-button-image","Embedded image","[[Image(",")]]","Example.jpg","mw-editbutton-image-"+id);
		}
		if ($(id).hasClass("no-file-macro")) 
		{
			self.addButton("wiki-button-file","Embedded file","[[File(",")]]","File.doc","mw-editbutton-file-"+id);
		}
		self.addButton("wiki-button-resource","Embedded resource","[[Resource(",")]]","123","mw-editbutton-resource-"+id);
		self.addButton("wiki-button-math","Mathematical formula (LaTeX)","\x3cmath\x3e","\x3c/math\x3e","Insert formula here","mw-editbutton-math-"+id);
		self.addButton("wiki-button-nowiki","Ignore wiki formatting","{{{","}}}","Insert non-formatted text here","mw-editbutton-nowiki-"+id);
		self.addButton("wiki-button-hr","Horizontal line (use sparingly)","\n----\n","","","mw-editbutton-hr-"+id);
		self.addButton("wiki-button-table","Table","\n||cell1||cell2||\n||cell3||cell4||\n","","","mw-editbutton-table-"+id);

		self.helpButtons[self.helpButtons.length] = {
			"imageId": "mw-editbutton-help-"+id,
			"imageFile": "wiki-button-help",
			"speedTip": "Help on formatting",
			"tagOpen": "",
			"tagClose": "",
			"sampleText": ""
		};
	},
	
	// this function generates the actual toolbar buttons with localized text
	// we use it to avoid creating the toolbar where javascript is not enabled
	addButton: function(imageFile, speedTip, tagOpen, tagClose, sampleText, imageId) {
		var self = HUB.Plugins.WikiEditorToolbar;
		// Don't generate buttons for browsers which don't fully
		// support it.
		self.editButtons[self.editButtons.length] = {
			"imageId": imageId,
			"imageFile": imageFile,
			"speedTip": speedTip,
			"tagOpen": tagOpen,
			"tagClose": tagClose,
			"sampleText": sampleText
		};
	},
	
	// this function generates the actual toolbar buttons with localized text
	// we use it to avoid creating the toolbar where javascript is not enabled
	insertEditButton: function(parent, item, id) {
		var li = document.createElement("li");
		var a = document.createElement("a");
		a.className = item.imageFile;
		if (item.imageId) a.id = item.imageId;
		a.title = item.speedTip;
		a.innerHTML = item.speedTip;
		a.onclick = function() {
			HUB.Plugins.WikiEditorToolbar.insertTags(item.tagOpen, item.tagClose, item.sampleText, id);
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
		a.href = "/wiki/Help:WikiFormatting";
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
	insertTags: function(tagOpen, tagClose, sampleText, id) {
		var txtarea = document.getElementById(id);
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

var wyktoolbar = [];

window.addEvent('domready', HUB.Plugins.WikiEditorToolbar.initialize);

