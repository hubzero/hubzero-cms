/**
 * @package     hubzero-cms
 * @file        components/com_wiki/wiki.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
if (!jq) {
	var jq = $;
}

HUB.Wiki = {
	jQuery: jq,
	
	getTemplate: function() {
		var $ = HUB.Wiki.jQuery
			hi = '';
		
		var id = $('#templates');
		if (id.val() != 'tc') {
			var hi = $('#'+id.val()).val();
			var co = $('#pagetext');
			co.val(hi);

			var ji = $('#'+id.val()+'_tags').val();
			var jo = $('#actags');
			jo.val(ji);

			if ($('#token-input-actags') && jo) {
				var data = [];
				if (ji) {
					if (ji.indexOf(',') == -1) {
						var values = [ji];
					} else {
						var values = ji.split(',');
					}
					$(values).each(function(i, v){
						v = v.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
						var id = null, 
							name = null;
						if (v.match(/(.+?) \((.+?)\)/ig)) {
							id = v.replace(/(.+?) \((.+?)\)/ig, '$2');
						    name = v.replace(/(.+?) \((.+?)\)/ig, '$1');
						}
						id = (id) ? id : v;
						name = (name) ? name : id;

						data[i] = {
							'id': id,
							'name': name
						};
						jo.tokenInput('add', data[i]);
					});
				}
				
				/*jo.tokenInput('/index.php?option=com_tags&no_html=1&task=autocomplete', {
	                theme: 'act',
					hintText: 'Type in a search term',
					prePopulate: data,
					tokenLimit: null,
					preventDuplicates: true,
					resultsFormatter: function(item){ 
						if (option != 'tags') {
							var html = "<li>";
							if (item['picture']) {
								html += '<img src="'+item['picture']+'" width="30" height="30" alt="picture" />';
							}
							html += item[this.propertyToSearch]+ " ("+item['id']+")";
							if (item['org']) {
								html += '<span>' + item['org'] + '</span>';
							}
							if (item['picture']) {
								html += '<div style="display:inline;clear:left;"></div>';
							}
							html += "</li>";
							return html;
						}
						return "<li>" + item[this.propertyToSearch]+ "</li>";
					},
					onAdd: function(){
						if (wsel) {
							$.getJSON('/index.php?option=com_groups&no_html=1&task=memberslist&group=' + $('#'+id).val(), function(data) {
								HUB.Plugins.Autocomplete.writeSelectList(data.members, wsel);
							});
						}
					}
	            });*/
				//jo.tokenInput().add();
				/*var ul = $($('#maininput-actags').parent().parent());
				var label = $($('#maininput-actags').parent().parent().parent());
				label.remove(ul);
				
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
				});*/
			}
		} else {
			$('#pagetext').val('');
		}
		if (typeof(wykiwygs) === 'undefined') {
			return;
		}
		if (wykiwygs.length) {
			for (i=0; i<wykiwygs.length; i++)
			{
				wykiwygs[i].t.value = hi;
				wykiwygs[i].e.body.innerHTML = wykiwygs[i].makeHtml(wykiwygs[i].t.value);
			}
		}
	},
	
	initialize: function() {
		var $ = this.jQuery;

		if ($('#templates').length > 0) {
			$('#templates').on('change', HUB.Wiki.getTemplate);
		}
		
		var mode = $('#params_mode');
		if (mode.length > 0) {
			mode.bind('change', HUB.Wiki.checkMode);
		}
		
		if ($('#file-uploader')) {
			HUB.Wiki.updateFileList();
		}

		if (typeof(qq) != 'undefined') {
			var uploader = new qq.FileUploader({
				element: $('#file-uploader')[0],
				action: $('#file-uploader').attr('data-action'),
				multiple: true,
				debug: false,
				extraDropzones: [$('#pagetext-overlay')],
				onSubmit: function(id, file) {
					//$("#ajax-upload-left").append("<div id=\"ajax-upload-uploading\" />");
				},
				onComplete: function(id, file, response) {
					$('.qq-upload-list').empty();
					HUB.Wiki.updateFileList();
				}
			});
			
			/*var dropArea = document.getElementById('pagetext-overlay');

			if (dropArea) {
				var dzt = new qq.UploadDropZone({
					element: dropArea,
					onEnter: function(e){
						//qq.addClass(dropArea, self._classes.dropActive);
						e.stopPropagation();
					},
					onLeave: function(e){
						e.stopPropagation();
					},
					onLeaveNotDescendants: function(e){
						//qq.removeClass(dropArea, self._classes.dropActive); 
					},
					onDrop: function(e){
						dropArea.style.display = 'none';
						//qq.removeClass(dropArea, self._classes.dropActive);
						uploader._uploadFileList(e.dataTransfer.files);
						if (typeof(wykiwygs) !== 'undefined') {
							if (wykiwygs.length) {
								for (i=0; i<wykiwygs.length; i++) {
									wykiwygs[i].action('insertHTML', '[[File(' + e.dataTransfer.files[0].name + ')]] ', false);
								}
							}
						}
						if (typeof(HUB.Plugins.WikiEditorToolbar) !== 'undefined') {
							HUB.Wiki.insertTags('[[File(', ')]] ', e.dataTransfer.files[0].name, 'pagetext');
						}
					}
				});

				dropArea.style.display = 'none';

				qq.attach(document, 'dragenter', function(e){
					if (!dzt._isValidFileDrag(e)) return; 

					dropArea.style.display = 'block';
				});
				qq.attach(document, 'dragleave', function(e){
					if (!dzt._isValidFileDrag(e)) return;
					var relatedTarget = document.elementFromPoint(e.clientX, e.clientY);
					// only fire when leaving document out
					if ( ! relatedTarget || relatedTarget.nodeName == "HTML") {
						dropArea.style.display = 'none';
					}
				});
			}*/
		}
	},

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
	},

	updateFileList: function() {
		var $ = HUB.Wiki.jQuery;
		
		if ($('#file-uploader')) {
			$.get($('#file-uploader').attr('data-list'), {}, function(data) {
				$('#file-uploader-list').html(data);
				$('a.delete')
					.unbind('click')
					.on('click', function(event){
						event.preventDefault();
						$.get($(this).attr('href'), {}, function(data) {
							HUB.Wiki.updateFileList();
						});
					});
			});
		}
	},

	checkMode: function() {
		var $ = HUB.Wiki.jQuery;

		var mode = $('#params_mode');
		if (mode.length > 0) {
			if (mode.val() != 'knol') {
				$($('#params_authors').parent()).addClass('hide');
				$($('#params_hide_authors').parent()).addClass('hide');
				$($('#params_allow_changes').parent()).addClass('hide');
				$($('#params_allow_comments').parent()).addClass('hide');
			} else {
				if ($($('#params_authors').parent()).hasClass('hide')) {
					$($('#params_authors').parent()).removeClass('hide');
				}
				if ($($('#params_hide_authors').parent()).hasClass('hide')) {
					$($('#params_hide_authors').parent()).removeClass('hide');
				}
				if ($($('#params_allow_changes').parent()).hasClass('hide')) {
					$($('#params_allow_changes').parent()).removeClass('hide');
				}
				if ($($('#params_allow_comments').parent()).hasClass('hide')) {
					$($('#params_allow_comments').parent()).removeClass('hide');
				}
			}
		}
	}
}

jQuery(document).ready(function($){
	HUB.Wiki.initialize();
});

