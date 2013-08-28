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
// Project Publication Manager JS
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.ProjectPublications = {
	jQuery: jq,
	typingTimer: '',
	
	initialize: function() 
	{
		var $ = this.jQuery;
				
		// Which section is active?
		var section = '';
		if ($('#section').length) 
		{
			section = $('#section').val();
		}
		
		// Enable js specific to each panel
		if (section == 'version') 
		{						
			HUB.ProjectPublications.panelVersion();
		}
		if (section == 'content' || section == 'gallery') 
		{						
			var gallery = section == 'gallery' ? 1 : 0;
			HUB.ProjectPublications.panelContent(gallery);
		}
		if (section == 'description') 
		{						
			HUB.ProjectPublications.panelDescription();
		}
		if (section == 'authors') 
		{						
			HUB.ProjectPublications.panelAuthors();
		}
		if (section == 'tags') 
		{						
			HUB.ProjectPublications.panelTags();
		}
		if (section == 'license') 
		{						
			HUB.ProjectPublications.panelLicense();
		}
		if (section == 'audience') 
		{						
			HUB.ProjectPublications.panelAudience();
		}
		if (section == 'access') 
		{						
			HUB.ProjectPublications.panelAccess();
		}
		if (section == 'notes') 
		{						
			HUB.ProjectPublications.panelNotes();
		}
		
		// Enable/disable save button
		HUB.ProjectPublications.checkBtn();	
		
		// Review
		HUB.ProjectPublications.submitReview();		
	},
	
	// Publication submission
	submitReview: function()
	{
		var $ = this.jQuery;
		var sreview = $('#submit-review');
		if (sreview.length && $("#status-msg").length && HUB.Projects) 
		{
			// Edit version label (dev)
			sreview.on('click', function(e)
			{ 
				$('#status-msg').empty().addClass('ajax-loading');
				$('#status-msg').html(HUB.Projects.loadingIma("Processing request. Please wait..."));
				$('#status-msg').css({'opacity':100});													
			});
		}
		
		if ($('#publish_date').length > 0) {
			$( "#publish_date" ).datepicker({
				dateFormat: 'yy-mm-dd',
				minDate: 0,
				maxDate: '+10Y'
			});
		}
	},
	
	// VERSION
	panelVersion: function()
	{
		var $ = this.jQuery;
		var vlabel = $('#edit-vlabel');
		if (vlabel.length) 
		{
			// Edit version label (dev)
			vlabel.on('click', function(e)
			{ 
				var original = vlabel.html();
				HUB.ProjectPublications.addEditForm (vlabel, original);
				if ($('#v-label') && !$('#v-label').hasClass('hidden')) 
				{
					$('#v-label').addClass('hidden');
				}														
			});
		}
	},
	
	// CONTENT
	panelContent: function(gallery)
	{
		var $ = this.jQuery;
		var primary = $('#primary').length ? $('#primary').val() : 1;
		var active 	= $('#base').length ? $('#base').val() : 'files';
		var tabs 	= $('.c-tab');
		
		// Container of selected content items
		var cselected = new Array();
		
		// Check preselected items
		var cd = $('.c-drag');
		if (cd.length > 0) 
		{
			cd.each(function(i, item) {	
				var id = $(item).attr('id').replace('clone-', '');			
				cselected.push(id);
			});		
		}
		HUB.ProjectPublications.showNoSel(cselected);
		
		// Load content choice
		if($('#c-show')) {

			var vid = $('#vid') ? $('#vid').val() : 0;
			
			// Build ajax url
			var url = HUB.ProjectPublications.getPubUrl(1);
			url = url + '&versionid=' + vid;
			
			// Build URL
			if (gallery)
			{
				url = url + '&active=files&action=browser&content=files&images=1';
			}
			else
			{
				url = url + '&active=' + active + '&action=browser';				
				url = url + '&primary=' + primary;
			}
						
			$('#c-show').empty();
			$('#c-show').append('<p id="loading-content">' + HUB.ProjectPublications.loadingIma('') + '</p>');
			$.get(url, {}, function(data) 
			{
				if (data) 
				{
					$('#c-show').html(data);
					
					// Content item selection
					var cl = $('.c-click');
					var cf = $('#c-filelist');
					if (cl.length > 0 && cf.length) 
					{
						cl.each(function(i, item) 
						{	
							var it = $(item).attr('id');
							HUB.ProjectPublications.attachEventsContent(item, cselected, it);
						});
					}
					
					HUB.ProjectPublications.afterAjaxContent();
				}
			});
		}
	
	},
	
	// DESCRIPTION
	panelDescription: function()
	{
		var $ = this.jQuery;
		
		HUB.ProjectPublications.checkBtn();
		
		// Check if required fields are filled in
		var required_inputs = $('.pubinput');
		if (required_inputs.length > 0) 
		{
			required_inputs.each(function(i, item) 
			{
				$(item).on('keyup', function(e) 
				{ 
					// Enable/disable save button
					HUB.ProjectPublications.checkBtn();
				});
			});
		}
		
		// Enable preview for metadata content
		var pubwiki = $('.pubwiki');
		if (pubwiki.length > 0) {
			pubwiki.each(function(i, item) 
			{
				$(item).on('keyup', function(e) 
				{				
					// Preview description
					var previewpane = $(item).attr('id').replace('pub_', '');	
					previewpane = '#preview-' + previewpane;
					if ($(previewpane)) {
						HUB.ProjectPublications.previewWiki( $(item), $(previewpane) );	
					}
				});
			});
		}
		
		// Check abstract length
		if($('#pub_abstract').length > 0)
		{
			$('#pub_abstract').on('keyup', function(e) 
			{
				HUB.Projects.setCounter($('#pub_abstract'), $('#counter_abstract'));
			});				
		}	
	},
	
	// AUTHORS
	panelAuthors: function()
	{
		var $ = this.jQuery;
		
		// Container of selected content items
		var cselected = new Array();
		
		// Check preselected items
		var cd = $('.c-drag');
		if (cd.length > 0) 
		{
			cd.each(function(i, item) {	
				var owner = $(item).attr('id').replace('clone-author::', '');			
				cselected.push(owner);
			});		
		}
		else if ($('#nosel').length && $('#nosel').hasClass('hidden')) 
		{
			// Show 'no items' message
			$('#nosel').removeClass('hidden');
		}
		
		// Load authors choice
		if($('#c-show')) {

			var vid = $('#vid') ? $('#vid').val() : 0;
			
			// Build ajax url
			var url = HUB.ProjectPublications.getPubUrl(1);
			url = url + '&versionid=' + vid;
			url = url + '&active=team';
			url = url + '&action=authors';
			
			$('#c-show').empty();
			$('#c-show').append('<p id="loading-content">' + HUB.ProjectPublications.loadingIma('') + '</p>');
			$.get(url, {}, function(data) 
			{
				if (data) 
				{
					$('#c-show').html(data);
					
					// Content item selection
					var cl = $('.c-click');
					var cf = $('#c-authors');
					if (cl.length > 0 && cf.length) 
					{
						cl.each(function(i, item) 
						{	
							var owner = $(item).attr('id').replace('owner:', '');
							HUB.ProjectPublications.attachEventsAuthors(item, cselected, owner);
						});
					}
					
					HUB.ProjectPublications.afterAjaxAuthors();
				}
			});
		}
	},
	
	// TAGS
	panelTags: function()
	{
		var $ = this.jQuery;
		
		if ($('#pick-tags').length) 
		{
			HUB.ProjectPublications.suggestTags();
		}
	},
	
	// LICENSE
	panelLicense: function()
	{
		var $ = this.jQuery;
		
		var license = $('#license');
		
		// Check original choice
		if (license.length) 
		{
			var original = $('#lic-' + license.val());
			HUB.ProjectPublications.checkLicense(original, license);
		}
							
		// On click choice
		var ac = $('.c-radio');
		if (ac.length > 0) 
		{
			ac.each(function(i, item) 
			{
				$(item).on('click', function(e)
				{ 
					e.preventDefault();
					
					var sel = $(item).attr('id').replace('lic-', '');
					license.val(sel);
					HUB.ProjectPublications.checkLicense(item);
					HUB.ProjectPublications.checkBtn();	
				});		
			});	
		}	
	},
	
	// AUDIENCE
	panelAudience: function()
	{
		var $ = this.jQuery;
		
		var audience = $('#audience').length ? $('#audience').val() : '';
		var picked = new Array(); // container of selected items
		
		// Check original choice
		if (audience.length) {
			picked = audience.split('-');
			if (picked.length > 0) 
			{
				for (var i = 0; i < picked.length; i++)
			    {
			      if ($(picked[i])) {
					 $(picked[i]).addClass('c-picked');
				  }
			    }
			}
		}
		
		HUB.ProjectPublications.checkAudience(picked, $('#no-audience').attr('checked'));

		// Do not show audience checkbox
		if($('#no-audience').length) {
			$('#no-audience').on('click', function(e)
			{
				if($('#no-audience').attr('checked') == 'checked') {
					picked = [];
				}
				HUB.ProjectPublications.checkAudience(picked, $('#no-audience').attr('checked'));
			});
		}		
		
		// On click choice
		var ac = $('.c-click');
		if(ac.length > 0) {
			ac.each(function(i, item)  
			{
				$(item).on('click', function(e) 
				{
					e.preventDefault();
					var sel = $(item).attr('id');
					//var idx = picked.indexOf(sel);
					var idx = HUB.Projects.getArrayIndex(sel, picked);
					
					if ($('#no-audience')) {
						$('#no-audience').removeAttr("checked");
					}
					if (idx==-1) {
						picked.push(sel);
						
						if (!$(item).hasClass('c-picked')) {
							$(item).addClass('c-picked');
						}
					}
					else {
						picked.splice(idx, 1);
						$(item).removeClass('c-picked');
					}
					HUB.ProjectPublications.checkAudience(picked, $('#no-audience').attr('checked'));
				});		
			});	
		}	
	},
	
	// ACCESS
	panelAccess: function()
	{
		var $ = this.jQuery;
		
		// Check original choice
		HUB.ProjectPublications.checkAccess();
					
		// On click choice
		var ac = $('.c-radio');
		if(ac.length > 0) {
			ac.each(function(i, item) 
			{	
				$(item).on('click', function(e) 
				{
					e.preventDefault();		
					var sel = $(item).attr('id');
					$('#access').val(HUB.ProjectPublications.getAccess(sel));
					HUB.ProjectPublications.checkAccess();
				});			
			});	
		}
	},
	
	// NOTES
	panelNotes: function()
	{
		var $ = this.jQuery;	
	},
	
	// CONTENT
	// Get all Ajax stuff working after screen update
	afterAjaxContent: function() 
	{
		var $ = this.jQuery;
		
		HUB.ProjectPublications.displayOrdering();
		HUB.ProjectPublications.addBtnContent();
		HUB.Projects.initialize();
		HUB.ProjectPublications.addDrag($('#c-filelist'));
		HUB.ProjectPublications.checkBtn();
		HUB.ProjectPublications.addPrimaryOptions('');
		HUB.ProjectPublications.readLink();		
	},
	
	// AUTHORS
	// Get all Ajax stuff working after screen update
	afterAjaxAuthors: function() 
	{
		var $ = this.jQuery;
		
		HUB.ProjectPublications.displayOrdering();
		HUB.ProjectPublications.addBtnAuthors();
		HUB.Projects.initialize();
		HUB.ProjectPublications.addDrag($('#c-authors'));	
		HUB.ProjectPublications.checkBtn();	
	},
		
	// CONTENT
	// item selection
	attachEventsContent: function(item, cselected, it) 
	{
		var $ = this.jQuery;
		var primary = $('#primary').length ? $('#primary').val() : 0;
		var gallery = $('#section').length && $('#section').val() == 'gallery' ? 1 : 0;
		var multi   = $('#base').length && $('#base').val() == 'files' ? 1 : 0;
		var vid 	= $('#vid') ? $('#vid').val() : 0;
		var move 	= $('#move') ? $('#move').val() : 0;
		var selOff  = $('#base').length && $('#base').val() == 'databases' && vid ? 1 : 0; 
				
		// Marking as selected
	//	var idx = cselected.indexOf(it);
		var idx = HUB.Projects.getArrayIndex(it, cselected);
		var order = $(item).index() + 1;
		
		if (idx!=-1) 
		{
			if (!$(item).hasClass('c-picked')) 
			{
				$(item).addClass('c-picked');
			}
			var cloned = $('#c-filelist li.attached-' + (idx + 1));
			if (cloned.length) {
				cloned.addClass('clone-' + order);
			}
		}
		
		// Cannot select items
		if (selOff == 1)
		{
			return;
		}
		
		// Selecting items
		$(item).on('click', function(e) 
		{																
			//var idx = cselected.indexOf(it);
			var idx = HUB.Projects.getArrayIndex(it, cselected);
			var order = $(item).index() + 1;
			
			// Only one selection possible?
			if (multi == 0)
			{
				var cl = $('.c-click');
				if (cl.length > 0) 
				{
					cl.each(function(i, ii) 
					{	
						$(ii).removeClass('c-picked');
						var iorder = $(ii).index() + 1;
						var iit = $(ii).attr('id');
						var iidx = HUB.Projects.getArrayIndex(iit, cselected);
						//var iidx = cselected.indexOf(iit);
												
						if (iidx != -1) 
						{					
							cselected.splice(iidx, 1);
							var icloned = $('#c-filelist li.clone-' + iorder);
							icloned.remove();
							HUB.ProjectPublications.showNoSel(cselected);
							HUB.ProjectPublications.afterAjaxContent();
						}					
					});
				}
			}				

			if (idx == -1) 
			{
				cselected.push(it);
				HUB.ProjectPublications.showNoSel(cselected);
				
				var missing = $(item).hasClass('i-missing') ? ' i-missing' : '';
				
				// Add clone
				$('#c-filelist').append ('<li class="clone-' + order + missing + ' c-drag" id="clone-' + it + '"></li>');
				
				var cloned = $('#c-filelist li.clone-' + order);
				cloned.empty().addClass('loading-content');
				cloned.html(HUB.ProjectPublications.loadingIma(''));
									
				// Build ajax url
				var url = HUB.ProjectPublications.getPubUrl(1);
				
				if (gallery)
				{
					url = url + '&vid=' + vid + '&move=' + move + '&action=showimage&ima=' + it;
				}
				else
				{
					url = url + '&vid=' + vid + '&move=' + move + '&action=showitem&item=' + it;
				}					
				
				$.get(url, {}, function(data) 
				{
					if (data) 
					{
						cloned.html(data);
						cloned.removeClass('loading-content');
						HUB.ProjectPublications.afterAjaxContent();
					}
				});
				
				if (!$(item).hasClass('c-picked')) 
				{
					$(item).addClass('c-picked');
				}
			}							
			else 
			{
				$(item).removeClass('c-picked');
				if (idx != -1) 
				{					
					var cloned = $('#c-filelist li.clone-' + order);
					cloned.remove();
										
					cselected.splice(idx, 1);
					HUB.ProjectPublications.showNoSel(cselected);
					HUB.ProjectPublications.afterAjaxContent();
				}
			}					
		});
	},
		

	// AUTHORS
	// item selection
	attachEventsAuthors: function(item, cselected, owner) 
	{
		var $ = this.jQuery;
		
		// Marking as selected
	//	var idx = cselected.indexOf(owner);
		var idx = HUB.Projects.getArrayIndex(owner, cselected);
		if (idx!=-1) 
		{
			if (!$(item).hasClass('c-picked')) 
			{
				$(item).addClass('c-picked');
			}
		}
		
		// Selecting authors
		$(item).on('click', function(e) 
		{																
		//	var idx = cselected.indexOf(owner);
			var idx = HUB.Projects.getArrayIndex(owner, cselected);

			if (!$(item).hasClass('c-picked')) 
			{
				$(item).addClass('c-picked');
				if (idx == -1) 
				{
					cselected.push(owner);
					HUB.ProjectPublications.showNoSel(cselected);
					
					// Add clone
					$('#c-authors').append ('<li class="clone-' + owner + ' c-drag" id="clone-author::' + owner + '"></li>');
					var rclone = $('#c-authors li.clone-' + owner);
					
					rclone.empty().addClass('loading-content');
					rclone.html(HUB.ProjectPublications.loadingIma(''));
							
					// Build ajax url
					var vid 		= $('#vid') ? $('#vid').val() : 0;
					var move 		= $('#move') ? $('#move').val() : 0;
					var url = HUB.ProjectPublications.getPubUrl(1);
					url = url + '&vid=' + vid + '&move=' + move + '&action=showauthor&owner=' + owner;
					$.get(url, {}, function(data) 
					{
						if (data) 
						{
							rclone.html(data);
							rclone.removeClass('loading-content');
							HUB.ProjectPublications.afterAjaxAuthors();
						}
					});
				}
			}
			else 
			{
				$(item).removeClass('c-picked');
				if (idx != -1) 
				{
					var rclone = $('#c-authors li.clone-' + owner);
					rclone.remove();
					
					cselected.splice(idx, 1);
					HUB.ProjectPublications.showNoSel(cselected);
					HUB.ProjectPublications.afterAjaxAuthors();
				}
			}					
		});
	},
	
	refreshOptionsContent: function() 
	{
		var $ = this.jQuery;
		var serveas = $('.serve_option');

		if (serveas.length > 0) 
		{
			serveas.each(function(i, item) 
			{
				$(item).on('click', function(e) 
				{
					e.preventDefault();
					HUB.ProjectPublications.addPrimaryOptions($(item).val());
				});
			});
		}
	},
	
	addPrimaryOptions: function(picked)
	{
		var $ = this.jQuery;
		
		// Get selections
		selections = HUB.ProjectPublications.gatherSelections('clone-'); 
		
		var pubop = $('#pub-options');
		if (!pubop.length) 
		{
			return;
		}
		
		var vid   = $('#vid') ? $('#vid').val() : 0;
		var base  = $('#base') ? $('#base').val() : 'files';
				
		var href = HUB.ProjectPublications.getPubUrl(1);
		href     = href + '&vid=' + vid;
		href	 = href + '&action=showoptions&selections=' + escape(selections);
		
		if (picked) 
		{
			href = href + '&serveas=' + picked;
		}
		href = href + '&base=' + base;
							
		$.get(href, {}, function(data) 
		{
			if (data) 
			{
				pubop.html(data);
				HUB.ProjectPublications.refreshOptionsContent();
			}
		});
	},
	
	// Upload content button
	addBtnContent: function() 
	{
		var $ = this.jQuery;
		var bu = $('#b-upload');
		var gallery = $('#section').length && $('#section').val() == 'gallery' ? 1 : 0;	
		var proceed = 1;	
		
		if (bu.length > 0)
		{
			bu.on('click', function(e) 
			{
				e.preventDefault();
				
				if ($('#uploader').val() != '') 
				{
					// Check file format
					var format = HUB.ProjectPublications.checkFormat($('#uploader').val());

					if (format == 'archive') 
					{
						// Confirm further action - extract files?
						if (HUB.ProjectFiles) 
						{
							HUB.ProjectFiles.addQuestion();	
						}
					}
					else if ($('#upload-form')) 
					{		
						if (gallery) 
						{
							if (format != 'image' && format != 'video') 
							{
								if ($('#statusmsg')) {
									$('#statusmsg').html('Please upload an image or video file in one of accepted formats.');
									$('#uploader').val('');
									proceed = 0;
								}
							}
						}
						if (proceed == 1) 
						{
							HUB.ProjectFiles.submitViaAjax('Uploading file(s)... Please wait');	
							$('#upload-form').submit();						
						}					
					}				
				}
			});
		}	
	},
	
	// Add authors button
	addBtnAuthors: function() 
	{
		var $ = this.jQuery;
		var bu = $('#add-author');
		
		if (bu.length)
		{
			bu.on('click', function(e) 
			{
				e.preventDefault();
				
				if ($('#confirm-box').length) 
				{
					$('#confirm-box').remove();	
				}
				
				if ($('#newmember') && $('#newmember').val()) 
				{
					var vid 		= $('#vid') ? $('#vid').val() : 0;
					var move 		= $('#move') ? $('#move').val() : 0;
					var url = HUB.ProjectPublications.getPubUrl(1);
					url = url + '&vid=' + vid + '&move=' + move;
					url = url + '&action=editauthor' + '&new=' + escape($('#newmember').val());
					
					// Modal box for actions
					$.fancybox(this,{
						type: 'ajax',
						href: url,
						width: 600,
						height: 500,
						autoSize: false,
						fitToView: false,
						wrapCSS: 'sbp-window',
						afterShow: function() {
							if ($('#cancel-action')) {
								$('#cancel-action').on('click', function(e) {
									$.fancybox.close();
								});
							}
							if ($('#ajax-selections') ) {
								if (HUB.ProjectPublications) {
									var selections = HUB.ProjectPublications.gatherSelections('clone-author::');
									$('#ajax-selections').val(selections);
								}
							}
						}
					});
				}
				
			});
		}
	},
	
	suggestTags: function() 
	{	
		var $ = this.jQuery;
		var show 		= $('#pick-tags');
		var vid 		= $('#vid') ? $('#vid').val() : 0;
		var newtag 		= $('#actags').length ? $('#actags').val() : '';
				
		// Build ajax url
		var url = HUB.ProjectPublications.getPubUrl(1);
		url = url + '&vid=' + vid + '&action=loadtags' + '&tags=' + escape(newtag);
		
		$('#pick-tags').empty().addClass('loading-content');
		$('#pick-tags').html(HUB.ProjectPublications.loadingIma(''));
		
		$.get(url, {}, function(data) 
		{
			if (data) 
			{
				$('#pick-tags').html(data);
				$('#pick-tags').removeClass('loading-content');
				HUB.ProjectPublications.afterAjaxTags();
			}
		});	
	},
	
	getSelectedTags: function()
	{
		var $ = this.jQuery;
		
		// Get selected tags
		var selected = '';
		var tags = $('.token-input-token-act p');
		
		if (tags.length > 0)
		{
			tags.each(function(i, item)  
			{
				selected = selected ? selected + ',' + $(item).html() : $(item).html();
			});
		}

		$('#actags').val(selected);
	},
	
	afterAjaxTags: function() 
	{	
		var $ = this.jQuery;
		
		HUB.ProjectPublications.getSelectedTags();
						
		// Content item selection
		var cl = $('.c-click');
		if (cl.length > 0) 
		{
			cl.each(function(i, item) 
			{
				$(item).unbind(); // clean-up past events
				$(item).on('click', function(e) 
				{
					var tag = '';
					var classes = $(item).attr('class').split(" ");
					
					for ( i=classes.length-1; i>=0; i-- ) 
					{
						if (classes[i].search("tag:") >= 0 )
						{
							tag = classes[i].split(":")[1];
						}
					}
					
					tagtxt = unescape(tag.replace(/\+/g, " "));
					var lastindex = $('.token-input-list-act li').index() + 1;
					
					$('.token-input-input-token-act').before('<li class="token-input-token-act" id="t-' + lastindex + '">' +
					'<p>' + tagtxt + '</p>' +
					'<span class="token-input-delete-token-act" id="tdel-' + lastindex + '">×</span>' + 
					'</li>'); 
						
					HUB.ProjectPublications.removeTag(lastindex, tagtxt);
					HUB.ProjectPublications.suggestTags();					
				});	
			});	
		}
		
		// More tags
		if ($('#more-tags').length > 0)
		{
			$('#more-tags').on('click', function(e) 
			{
				e.preventDefault();
				HUB.ProjectPublications.suggestTags();
			});
		}
	},
	
	removeTag: function(idx, tagtxt) 
	{		
		var tdel = $('#tdel-' + idx);
		if (tdel.length > 0)
		{
			tdel.on('click', function(e) 
			{
				tdel.parent().remove();
			});
		}
	},
	
	changeTags: function() 
	{		
		var change = $('#token-input-actags');
		
		if (change.length > 0)
		{
			alert('yes');
			$(change).on('change', function(e) 
			{
				HUB.ProjectPublications.getSelectedTags();
			});	
		}
	},
	
	checkLicense: function(sel) 
	{
		var $ = this.jQuery;
		var license = $('#license');
	
		var ac = $('.c-radio');
		var extra = $('.c-extra');
		var chosen = $('#c-sel-license');
		
		// uncheck all
		if (ac.length > 0) 
		{
			ac.each(function(i, item) 
			{
				if ($(item).hasClass('c-picked')) {
					$(item).removeClass('c-picked');
				}	
			});	
		}
		
		// hide all details
		if(extra.length > 0) 
		{
			extra.each(function(i, item) 
			{	
				if(!$(item).hasClass('hidden')) 
				{
					$(item).addClass('hidden');
				}	
			});	
		}
		
		// check selected
		if(license && license.val() != '' && license.val() != 0) 
		{
			if ($(sel)) 
			{
				$(sel).addClass('c-picked');
				
				// show selected on the right
				if (chosen && chosen.hasClass('hidden')) 
				{
					chosen.removeClass('hidden');
				}
				if (chosen) 
				{
					chosen.html($(sel).attr('title'));	
				}
				
				// Hide instructions
				if ($('#nosel') && !$('#nosel').hasClass('hidden')) 
				{
					$('#nosel').addClass('hidden');
				}

				// show extra options
				var divextra = '#extra-' + license.val();
				if ($(divextra) && $(divextra).hasClass('hidden')) 
				{
					$(divextra).removeClass('hidden');
				}
			}			
		}
		else if ($('#nosel') && $('#nosel').hasClass('hidden')) 
		{
			$('#nosel').removeClass('hidden');
		}
		
		var ltext = $('#license-text-' + license.val());
		var agree = $('#agree-' + license.val());
		
		if (ltext.length) 
		{
			ltext.unbind();
			ltext.on('keyup', function(e) 
			{
				HUB.ProjectPublications.checkBtn();
			});
		}
		
		if (agree.length) {
			agree.unbind();
			agree.on('click', function(e) 
			{
				HUB.ProjectPublications.checkBtn();
			});
		}
		
		// Load template text
		var reload 	 = $('#reload-' + license.val());
		var template = $('#template-' + license.val());
		if (reload.length && template.length && ltext.length) 
		{
			reload.on('click', function(e)
			{
				ltext.val(template.html());
				HUB.ProjectPublications.checkBtn();
			});
		}	
	},
	
	addEditForm: function (el, original) 
	{		
		var $ = this.jQuery;
		
		if ($('#editv').length > 0) {
			return;
		}
		
		$(el).addClass('hidden');
				
		// Add form
		$(el).parent().append('<label id="editv">' + 
			'<input type="text" name="label" value="' + original + '" maxlength="10" class="vlabel" />' +
			'<input type="submit" value="save" />' +
			'<input type="button" value="cancel" class="cancel" id="cancel-rename" />' +
		'</label>');

		$('#cancel-rename').on('click', function(e){
			e.preventDefault();
			$('#editv').remove();
			$(el).removeClass('hidden');
			if($('#v-label') && $('#v-label').hasClass('hidden')) {
				$('#v-label').removeClass('hidden');
			}
		});		
	},
	
	// Enable/disable save & continue button
	checkBtn: function() 
	{
		var $ = this.jQuery;
		var con = $('#c-continue');
		var selections = '';
		var enable = 1;
		var section = $('#section').length ? $('#section').val() : '';
		
		// We need to have the button on page
		if (!con.length) 
		{
			return false;
		}
		con.unbind();
		
		// Diferent behavior for different sections
		if (section == 'content' || section == 'gallery')
		{
			selections = HUB.ProjectPublications.gatherSelections('clone-');
			var primary = $('#primary').length ? $('#primary').val() : 0;
			if (primary == 1 && !selections)
			{
				enable = 0;
			}
		}
		if (section == 'description')
		{
			var required = $('.pubinput');
			if (required.length > 0) 
			{
				required.each(function(i, item)  
				{
					if ($(item).val() == '') {
						enable = 0;	
					}
				});
			}
		}
		if (section == 'authors')
		{
			// Need at least one author selected
			selections = HUB.ProjectPublications.gatherSelections('clone-author::');
			enable = selections ? 1 : 0;
		}		
		else if (section == 'license')
		{
			var license = $('#license');
			var ltext = $('#license-text-' + license.val());
			var agree = $('#agree-' + license.val());
			
			if ((license.val() == '' || license.val() == 0)) 
			{
				enable = 0;	
			}
			else if (license.val()) 
			{
				// Check for default text
				if (ltext.length && !ltext.parent().parent().hasClass('hidden')
					&& !HUB.ProjectPublications.checkLicenseText(ltext.val())) {
					enable = 0;				
				}
				if (agree.length && !agree.parent().parent().hasClass('hidden') 
					&& agree.attr('checked') != 'checked') {
					enable = 0;
				}	
			}
		}
		else if (section == 'audience')
		{			
			if ($('#no-audience').attr('checked') != 'checked' && $('.c-picked').length == 0)
			{
				enable = 0;
			}
		}
		
		// Style button appropriately
		if (enable == 1) 
		{
			con.removeClass('disabled');
		}
		else if (!con.hasClass('disabled')) 
		{
			con.addClass('disabled');
		}
		
		// On-click action	
		con.on('click', function(e) 
		{
			e.preventDefault();

			if (!con.hasClass('disabled')) 
			{ 
				if ($('#plg-form').length) 
				{	
					if ($('#selections').length)
					{
						$('#selections').val(selections);	
					}			
					$('#plg-form').submit();
				}
			}
		});
	},
	
	loadingIma: function(txt)
	{
		var $ = this.jQuery;
		
		var html = '<span id="fbwrap">' + 
			'<span id="facebookG">' +
			' <span id="blockG_1" class="facebook_blockG"></span>' +
			' <span id="blockG_2" class="facebook_blockG"></span>' +
			' <span id="blockG_3" class="facebook_blockG"></span> ' +
			txt +
			'</span>' +
		'</span>';
		
		return html;
	},
	
	checkLicenseText: function(text) 
	{
		var $ = this.jQuery;
		if(text == '') {
			return false;
		}
		var defaults = [ 
			'YEAR',
			'OWNER',
			'ORGANIZATION',
			'ONE LINE DESCRIPTION',
			'URL'  
		];
		
		var matches = /YEAR/;		
		if(text.match(matches) != null) {
			return false;
		}
		var matches = /OWNER/;		
		if(text.match(matches) != null) {
			return false;
		}
		var matches = /ORGANIZATION/;		
		if(text.match(matches) != null) {
			return false;
		}
		var matches = /URL/;		
		if(text.match(matches) != null) {
			return false;
		}
		var matches = /ONE LINE DESCRIPTION/;		
		if(text.match(matches) != null) {
			return false;
		}
			
		return true;		
	},
	
	addDrag: function(list) 
	{
		var $ = this.jQuery;
		var numitems = $('.c-drag').length;
		var step = 0;
		
		if (numitems == 0) 
		{
			return false;
		}
		if ($(list).length == 0 || $(list).hasClass('noedit')) 
		{
			return false;
		}
		
		// Drag items	 
		$(list).sortable(
		{
		   	update: function() 
			{
			    HUB.ProjectPublications.displayOrdering();
				HUB.ProjectPublications.checkBtn();
		   	}
		});
	},
	
	displayOrdering: function()
	{
		var nums = $('.a-ordernum');
		var o	 = 1;
		
		if (nums.length > 0)
		{
			nums.each(function(i, item) 
			{	
				$(item).html(o);
				o++;
			});
		}
	},
	
	gatherSelections: function(replacement) 
	{
		var $ = this.jQuery;
		var items = $('.c-drag');
		var selections = ''; 
				
		if(items.length > 0) {
			items.each(function(i, item)  
			{
				var id = $(item).attr('id');
				
				if (replacement)
				{
					id = id.replace(replacement, '');
				}
				
				if (id != '' && id != ' ') 
				{
					selections = selections + id + '##' ;	
				}				
			});
		}
		return selections;
	},
	
	checkAudience: function(picked, noshow) 
	{
		var $ = this.jQuery;
		var ac  = $('.c-click');
		var out = $('#c-sel-audience');
		var con = $('#c-continue');

		if (noshow == 'checked') 
		{
			// uncheck all
			if(ac.length > 0) {
				ac.each(function(i, item)  
				{	
					if($(item).hasClass('c-picked')) {
						$(item).removeClass('c-picked');
					}	
				});	
			}			
		}
		
		// Collect selections
		selections = '';
		if (picked.length > 0) {
			for ( i=0; i < picked.length; i++ ) {
				selections = selections + picked[i] + '-';
			}
		}
		$('#audience').val(selections);
		
		var vid 		= $('#vid') ? $('#vid').val() : 0;
		var newtag 		= $('#actags').length ? $('#actags').val() : '';		

		// Build ajax url
		var url = HUB.ProjectPublications.getPubUrl(1);
		url = url + '&vid=' + vid + '&action=showaudience&audience=' + selections + '&no_audience=' + noshow;	
				
		$.get( url, {}, function(data) {
			if(data)
			{
				out.html(data);
			}
		});
		
		// Show/Hide instructions
		if (picked.length > 0 || noshow == 'checked') 
		{
			if($('#nosel').length && !$('#nosel').hasClass('hidden')) {
				$('#nosel').addClass('hidden');
			}
			if(out.hasClass('hidden')) {
				out.removeClass('hidden');
			}
		}
		else
		{
			if($('#nosel').length && $('#nosel').hasClass('hidden')) {
				$('#nosel').removeClass('hidden');
			}
			if(!out.hasClass('hidden')) {
				out.addClass('hidden');
			}
		}
		
		HUB.ProjectPublications.checkBtn();

	},
	
	// When no selection is made
	showNoSel: function(cselected)
	{
		var $ = this.jQuery;
		
		if (cselected.length > 0 && $('#nosel').length && !$('#nosel').hasClass('hidden')) 
		{
			$('#nosel').addClass('hidden');
		}
		if (cselected.length == 0 && $('#nosel').length && $('#nosel').hasClass('hidden')) 
		{
			$('#nosel').removeClass('hidden');
		}	
	},
	
	checkFormat: function(filename) 
	{
		var $ = this.jQuery;
		var re = /[^.]+$/;
	    var extt = filename.match(re);
	
		if (!extt) {
			return 'other';
		}
		else {
			var ext = extt.toString().toLowerCase();
		}
		
		// Compressed file extensions
		var tar = {
		  'gz'  	: 1,
		  '7z'      : 1,
		  'zip' 	: 1,
		  'zipx' 	: 1,
		  'sit' 	: 1,
		  'sitx' 	: 1,
		  'rar' 	: 1
		};
		
		// Video file extensions
		var video = {
		  'avi'  	: 1,
		  'mpeg' 	: 1,
		  'mov' 	: 1,
		  'mp4'  	: 1,
		  'mpg'  	: 1,
		  'rm'  	: 1,
		  'ogg'  	: 1,
		  'wmv'  	: 1
		};
		
		// Image file extensions
		var image = {
		  'bmp'  : 1,
		  'jpeg' : 1,
		  'jpg'  : 1,
		  'jpe'  : 1,
		  'gif'  : 1,
		  'png'  : 1,
		  'tif'  : 1,
		  'tiff' : 1
		};
		
		if (tar[ext]) {
			return 'archive';
		}
		else if (video[ext]) {
			return 'video';
		}
		else if (image[ext]) {
			return 'image';
		}
		else {
			return 'other';
		}			
	},
	
	getPubUrl: function(no_html) 
	{		
		var $ = this.jQuery;
		
		var projectid = $('#projectid') ? $('#projectid').val() : 0;
		var pid = $('#pid') ? $('#pid').val() : 0;
		var provisioned = $('#provisioned') ? $('#provisioned').val() : 0;

		if (provisioned == 1) {
			var url = '/publications/submit/?pid=' +  pid;
		}
		else {
			var url = '/projects/' + projectid + '/publications/?pid=' +  pid;					
		}
		if (no_html == 1) {
			url = url + '&no_html=1&ajax=1';	
		}
		return url;
	},
	
	previewWiki: function( raw, preview )
	{
		var $ = this.jQuery;
		if (preview.length && raw.length) 
		{				
			// Build ajax url
			var url = HUB.ProjectPublications.getPubUrl(1);

			url = url + '&action=wikipreview';
			url = url + '&raw=' + escape(raw.val());
			
			$.get(url, {}, function(data) 
			{
				if (data) 
				{
					preview.html(data);
				}
			});
		}		
	},
	
	checkAccess: function() 
	{
		var $ = this.jQuery;
		var ac 		= $('.c-radio');
		var extra 	= $('.c-extra');
		var chosen 	= $('#c-sel-access');
		var text 	= '';
		var access  = $('#access');
		
		// uncheck all
		if(ac.length > 0) {
			ac.each(function(i, item) 
			{	
				if($(item).hasClass('c-picked')) {
					$(item).removeClass('c-picked');
				}	
			});	
		}
		
		// hide all tips
		if(extra.length > 0) {
			extra.each(function(i, item)  
			{	
				if(!$(item).hasClass('hidden')) {
					$(item).addClass('hidden');
				}	
			});	
		}
				
		// check selected
		if (access && access.val() != '') 
		{
			if(access.val() == 0 && $('#access-public').length) {
				$('#access-public').addClass('c-picked');
				text = 'Public';
			}
			if(access.val() == 1 && $('#access-registered').length) {
				$('access-registered').addClass('c-picked');
				text = 'Registered';
			}
			if(access.val() > 1 && $('#access-restricted').length) {
				$('#access-restricted').addClass('c-picked');
				text = 'Restricted';
			}
			if($('#nosel') && !$('#nosel').hasClass('hidden')) {
				$('#nosel').addClass('hidden');
			}
			
			// show selected on the right
			if(chosen && chosen.hasClass('hidden')) {
				chosen.removeClass('hidden');
			}
			if (chosen) {
				chosen.html(text);	
			}
			
			// show tips & extra options
			var divextra = '#extra-' + access.val();
			if (divextra == '#extra-3') {
				divextra = '#extra-2';
			}
			if ($(divextra) && $(divextra).hasClass('hidden')) {
				$(divextra).removeClass('hidden');
			}			
		}
		else if($('#nosel') && $('#nosel').hasClass('hidden')) {
			$('#nosel').removeClass('hidden');
		}
	},
	
	getAccess: function(access) 
	{
		var num = 0;
		if(access == 'access-public') {
			num = 0;
		}
		if(access == 'access-registered') {
			num = 1;
		}
		if(access == 'access-restricted') {
			num = 2;
		}
		return num;		
	},
	
	readLink: function ()
	{
		var $ = this.jQuery;
		
		if (!$('#parse-url').length)
		{
			return false;
		}
		
		$('#parse-url').unbind();
		             
		var doneTypingInterval = 2000;
		var link = $('#parse-url').val();
		
		$('#parse-url').on('keyup', function(e) 
		{
			clearTimeout(HUB.ProjectPublications.typingTimer);
			HUB.ProjectPublications.typingTimer = setTimeout(HUB.ProjectPublications.parseLink, doneTypingInterval);
			$('#link-loading').html('');
			if (!$('#link-preview').hasClass('hidden'))
			{
				$('#link-preview').addClass('hidden');
			}
			if (!$('#link-submit').hasClass('hidden'))
			{
				$('#link-submit').addClass('hidden');
			}		
		});
		
		$('#parse-url').on('keydown', function(e) {
			clearTimeout(HUB.ProjectPublications.typingTimer);
		});
		
	},
	
	parseLink: function ()
	{
		var $ = this.jQuery;
				
		$('#link-loading').html(HUB.ProjectPublications.loadingIma('Loading link preview...'));	
				
		if (!$('#link-preview').hasClass('hidden'))
		{
			$('#link-preview').addClass('hidden');
		}
		if (!$('#link-submit').hasClass('hidden'))
		{
			$('#link-submit').addClass('hidden');
		}
		
		if (!HUB.ProjectPublications.isValidURL($('#parse-url').val()))
	    {          
			$('#link-loading').html('<p class="notice">Please enter a valid URL starting with http:// or https://</p>');
			return false;
        }
				
		$.post("index.php?option=com_projects&task=parseurl&no_html=1&ajax=1&url="+escape($('#parse-url').val()), {}, 
			function (response) {
			
			response = $.parseJSON(response);
			clearTimeout(HUB.ProjectPublications.typingTimer);
						
			if (response.error)
			{
				$('#link-loading').html(response.error);
			}	
			else
			{				
				$('#link-loading').html('');
				$('#link-preview').html(response.output);
				$('#link-preview').removeClass('hidden');	
				$('#link-submit').removeClass('hidden');				
			}		
		});			
	},
	
	isValidURL: function (url)
	{
		var $ = this.jQuery;   
		var RegExp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
	
	    if (RegExp.test(url))
		{
	       return true;
	    } 
		else
		{
	       return false;
	    }
	}	
}

jQuery(document).ready(function($){
	HUB.ProjectPublications.initialize();
});	
