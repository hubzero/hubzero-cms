/**
 * @package     hubzero-cms
 * @file        plugins/hubzero/wikieditortoolbar/wikieditortoolbar.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
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

(function(jQuery) {

	jQuery.fn.wikitoolbar = function(options) {

		return this.each(function() {

			var toolbar = jQuery(this),
				id = toolbar.attr('id').split('-').pop(),
				textbox = jQuery('#'+id),
				editButtons = new Array(),
				helpButtons = new Array();

			// Don't generate buttons for browsers which don't fully support it.
			if (!(document.selection && document.selection.createRange)
				&& textbox.selectionStart === null) {
				return false;
			}

			textbox.css('margin-top', '0');
			toolbar.removeClass('hide');

			addButton("wiki-button-bold","Bold text","\'\'\'","\'\'\'","Bold text","mw-editbutton-bold-"+id);
			addButton("wiki-button-italic","Italic text","\'\'","\'\'","Italic text","mw-editbutton-italic-"+id);
			addButton("wiki-button-underline","Underline","__","__","","mw-editbutton-underline-"+id);
			addButton("wiki-button-superscript","Superscript","^","^","","mw-editbutton-superscript-"+id);
			addButton("wiki-button-subscript","Subscript",",,",",,","","mw-editbutton-subscript-"+id);
			addButton("wiki-button-strikethrough","Strikethrough","~~","~~","","mw-editbutton-strikethrough-"+id);
			addButton("wiki-button-link","Internal link","[","]","Link title","mw-editbutton-link-"+id);
			if (!textbox.hasClass('minimal')) {
				addButton("wiki-button-headline","Level 2 headline","\n== "," ==\n","Headline text","mw-editbutton-headline-"+id);
				if (!textbox.hasClass("no-image-macro")) {
					addButton("wiki-button-image","Embedded image","[[Image(",")]]","Example.jpg","mw-editbutton-image-"+id);
				}
				if (!textbox.hasClass("no-file-macro")) {
					addButton("wiki-button-file","Embedded file","[[File(",")]]","File.doc","mw-editbutton-file-"+id);
				}
				addButton("wiki-button-resource","Embedded resource","[[Resource(",")]]","123","mw-editbutton-resource-"+id);
			}
			addButton("wiki-button-math","Mathematical formula (LaTeX)","\x3cmath\x3e","\x3c/math\x3e","Insert formula here","mw-editbutton-math-"+id);
			addButton("wiki-button-nowiki","Ignore wiki formatting","{{{","}}}","Insert non-formatted text here","mw-editbutton-nowiki-"+id);
			if (!textbox.hasClass('minimal')) {
				addButton("wiki-button-hr","Horizontal line (use sparingly)","\n----\n","","","mw-editbutton-hr-"+id);
				addButton("wiki-button-table","Table","\n||cell1||cell2||\n||cell3||cell4||\n","","","mw-editbutton-table-"+id);
			}

			helpButtons[helpButtons.length] = {
				"imageId": "mw-editbutton-help-"+id,
				"imageFile": "wiki-button-help",
				"speedTip": "Help on formatting",
				"tagOpen": "",
				"tagClose": "",
				"sampleText": ""
			};

			for (var i = 0; i < editButtons.length; i++) 
			{
				insertEditButton(toolbar, editButtons[i], id);
			}
			insertHelpButton(toolbar, helpButtons[0]);

			// this function generates the actual toolbar buttons with localized text
			// we use it to avoid creating the toolbar where javascript is not enabled
			function addButton(imageFile, speedTip, tagOpen, tagClose, sampleText, imageId) {
				// Don't generate buttons for browsers which don't fully
				// support it.
				editButtons[editButtons.length] = {
					"imageId"   : imageId,
					"imageFile" : imageFile,
					"speedTip"  : speedTip,
					"tagOpen"   : tagOpen,
					"tagClose"  : tagClose,
					"sampleText": sampleText
				};
			};

			// this function generates the actual toolbar buttons with localized text
			// we use it to avoid creating the toolbar where javascript is not enabled
			function insertEditButton(parent, item, id) {
				var li = jQuery(document.createElement('li'));

				var a = jQuery(document.createElement('a'))
					.attr('title', item.speedTip)
					.addClass(item.imageFile)
					.html(item.speedTip)
					.on('click', function() {
						insertTags(item.tagOpen, item.tagClose, item.sampleText, id);
						return false;
					});
				if (item.imageId) {
					a.attr('id', item.imageId);
				}

				li.append(a);
				parent.append(li);
				return true;
			};

			// this function generates the actual toolbar buttons with localized text
			// we use it to avoid creating the toolbar where javascript is not enabled
			function insertHelpButton(parent, item) {
				var li = jQuery(document.createElement('li'));

				var a = jQuery(document.createElement('a'))
					.attr('title', item.speedTip)
					.attr('href', "/wiki/Help:WikiFormatting?tmpl=component")
					.html(item.speedTip)
					.addClass(item.imageFile + " popup")
					.on('click', function() {
						window.open(this.href, 'popup', 'resizable=1,scrollbars=1,height=520,width=760');
						return false;
					});
				if (item.imageId) {
					a.attr('id', item.imageId);
				}

				li.append(a);
				parent.append(li);
				return true;
			};

			// Apply tagOpen/tagClose to selection in textarea,
			// use sampleText instead of selection if there is none
			insertTags = function(tagOpen, tagClose, sampleText, id) {
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
			};
		});
	};

})(jQuery);

//----------------------------------------------------------
// Wiki toolbar
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

var wyktoolbar = [];

function initWikitoolbar() {
	jQuery('.wiki-toolbar').each(function(i, toolbar) {
		var tbar = jQuery(toolbar),
			id = tbar.attr('id').split('-').pop();

		for (var i = 0; i < wyktoolbar.length; i++) 
		{
			if (wyktoolbar[i] == id && tbar.children().length) {
				return;
			}
		}

		jQuery(tbar).wikitoolbar();

		wyktoolbar.push(id);
	});
};

jQuery(document).ready(function(jq){
	initWikitoolbar();
});
jQuery(document).on('ajaxLoad', initWikitoolbar);
