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
		var $ = this.jQuery;
		
		var id = $('#templates');
		if (id.val() != 'tc') {
			var hi = $(id.val()).val();
			var co = $('#pagetext');
			co.val(hi);
			
			var ji = $('#'+id.val()+'_tags').val();
			var jo = $('#actags');
			jo.val(ji);

			if ($('#maininput-actags') && jo) {
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
	},
	
	initialize: function() {
		var $ = this.jQuery;
		
		if ($('#templates')) {
			$('#templates').bind('change', HUB.Wiki.getTemplate);
		}
		
		var mode = $('#params_mode');
		if (mode) {
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
				onSubmit: function(id, file) {
					//$("#ajax-upload-left").append("<div id=\"ajax-upload-uploading\" />");
				},
				onComplete: function(id, file, response) {
					$('.qq-upload-list').empty();
					HUB.Wiki.updateFileList();
				}
			});
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
		var $ = this.jQuery;
		
		var mode = $('#params_mode');
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

jQuery(document).ready(function($){
	HUB.Wiki.initialize();
});

