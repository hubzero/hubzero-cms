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
// Project Publications Manager JS
//----------------------------------------------------------

HUB.ProjectPublications = {

	initialize: function() 
	{
		// Which section is active?
		var section = '';
		if($('section')) {
			section = $('section').value;
		}
		
		// Disable next button
		var con = $('c-continue');
		if(con && section != 'description') {
			con.addEvent('click', function(e) {
				new Event(e).stop();
			});
		}
		
		// Iframe
		var target = $('upload_target');
		if(target) {
			target.addEvent('load', function(e) {
				var returned = target.contentWindow.document.body.innerHTML;
				if(returned != 'na' && returned != '') {
					HUB.ProjectPublications.initialize();
					
					// Refresh file count
					if(HUB.Projects) {
						HUB.Projects.refreshCount('files');
					}
				}
				else {
					if ($('uploader'))
					{
						$('uploader').value = '';
					}
					if($('statusmsg')) {
						$('statusmsg').innerHTML = 'There was an error uploading the file: file too big or wrong type';
					}
				}
			});
		}
				
		//---------------------------------------------
		// All sections - info tips
		//---------------------------------------------
		var tips = $$('.pub-info-link');
		if(tips.length > 0) {
			tips.each(function(item) {
				var keyupTimer;
				var tips_span = item.parentNode.getElement('span');
				tips_span.style.display = 'none';
				
				item.addEvent('click', function(e) {
					new Event(e).stop();		
					if(tips_span && tips_span.style.display == 'none') {
						tips_span.style.display = 'block';
						clearTimeout(keyupTimer);
						keyupTimer = setTimeout((function() {  
							tips_span.style.display = 'none';			
						}), 3000);	
					}	
					else {
						tips_span.style.display = 'none';
						clearTimeout(keyupTimer);
					}				
				});
			});	
		}
		
		//---------------------------------------------
		// Authors section
		//---------------------------------------------
		if(section == 'authors') {			
			
			// Container of selected content items
			var cselected = new Array(); 
			
			// Check preselected items
			var cd = $$('.c-drag');
			if(cd.length > 0) {
				cd.each(function(item) {	
					var owner = item.getProperty('id').replace('clone-author::', '');			
					cselected.push(owner);
				});	
				HUB.ProjectPublications.afterAjaxAuthors($('c-show'), cselected);
				HUB.ProjectPublications.checkBtnAuthors(cselected);
				HUB.ProjectPublications.addDrag($('c-authors'), 1);
				HUB.ProjectPublications.addHintContent(cselected);		
			}
			else if($('nosel') && $('nosel').hasClass('hidden')) {
				// Show 'no items' message
				$('nosel').removeClass('hidden');
			}
			
			// Author selector/uploader
			var show = $('c-show');
			if(show) {

				var vid = $('vid') ? $('vid').value : 0;
				
				// Build ajax url
				var url = HUB.ProjectPublications.getPubUrl(1);
				url = url + '&versionid=' + vid;
				url = url + '&active=team';
				url = url + '&action=authors';	
				
				$('c-show').empty();
				var p = new Element('p');
				imgpath = '/components/com_projects/assets/img/ajax-loader.gif';
				var img = new Element('img', {'src':imgpath}).injectInside(p);
				p.setStyles({'text-align': 'center', 'margin-top': '30px', 'vertical-align':'top'});
				$('c-show').appendChild(p);
	
				new Ajax(url, {
					method : 'get',
					update: $('c-show'),
					onComplete: function() { 
						HUB.ProjectPublications.afterAjaxAuthors($('c-show'), cselected);
					}
				}).request();	
			}			
		}
		
		//---------------------------------------------
		// Access section
		//---------------------------------------------
		if(section == 'access') {
			var access = $('access');
			
			// Check original choice
			HUB.ProjectPublications.checkAccess(access);
						
			// On click choice
			var ac = $$('.c-radio');
			if(ac.length > 0) {
				ac.each(function(item) {	
					item.addEvent('click', function(e) {
						new Event(e).stop();
						var sel = item.getProperty('id');
						access.value = HUB.ProjectPublications.getAccess(sel);
						HUB.ProjectPublications.checkAccess(access);
					});			
				});	
			}
			
			// Autocompleter for group names
			var elm = $('access-group');
			if (elm) {
				var completerGroup = new Autocompleter.MultiSelectable.Ajax.Json(elm, 'index.php?option=com_projects&no_html=1&task=autocomplete&which=publicgroup', {
					'minLength': 1, // We wait for at least one character
					'overflow': true, // Overflow for more entries
					'wrapSelectionsWithSpacesInQuotes': false,
					'multiple': true,
					'tagger': null,
					'injectChoice': function(choice) {
						var el = new Element('li').setHTML(this.markQueryValue(choice[0]));
						el.inputValue = choice[1];
						this.addChoiceEvents(el).injectInside(this.choices);
					}
				});
			}				
		}
		
		//---------------------------------------------
		// License section
		//---------------------------------------------
		if(section == 'license') {
			var license = $('license');
			
			// Check original choice
			if(license) {
				var original = $('lic-' + license.value);
				HUB.ProjectPublications.checkLicense(original, license);
			}
								
			// On click choice
			var ac = $$('.c-radio');
			if(ac.length > 0) {
				ac.each(function(item) {
					item.addEvent('click', function(e) {
						new Event(e).stop();
						var sel = item.getProperty('id').replace('lic-', '');
						license.value = sel;
						HUB.ProjectPublications.checkLicense(item, license);
					});		
				});	
			}
		}			
		
		//---------------------------------------------
		// Tags section
		//---------------------------------------------
		if(section == 'tags') 
		{	
			// Tag viewer	
			if($('pick-tags')) {
				HUB.ProjectPublications.suggestTags($('pick-tags'));
			}
		}	
		
		//---------------------------------------------
		// Notes section
		//---------------------------------------------
		if(section == 'notes') {			
			var notes = $('notes');

			// Enable save button (step is optional)
			if($('c-continue')) {
				$('c-continue').removeEvents();
			}
			
			// Enable wiki preview
			/*
			if(notes) {
				notes.addEvent('keyup', function(e) 
				{				
					HUB.ProjectPublications.previewWiki( notes, $('preview-notes') );
				});	
			}*/
		}
						
		//---------------------------------------------
		// Content section
		//---------------------------------------------
		if(section == 'content') {
			var cselected = new Array(); // container for selected content items
			var primary = $('primary') ? $('primary').value : 1; // Primary or supporting content?

			// Check preselected items
			var cd = $$('.c-drag');
			if(cd.length > 0) {
				cd.each(function(item) {
					var id = item.getProperty('id').replace('clone-', '');	
					cselected.push(id);
					HUB.ProjectPublications.showContentInfo(id, primary);	
				});	
				HUB.ProjectPublications.afterAjaxContent($('c-show'), cselected);
				
				if($('c-filelist'))
				{
					HUB.ProjectPublications.addDrag($('c-filelist'), 0);	
				}
			
				if(primary) {
					HUB.ProjectPublications.addOptionsContent(1, '');
				}				
			}
			else if($('nosel') && $('nosel').hasClass('hidden')) {
				// Show 'no items' message
				$('nosel').removeClass('hidden');
			}
			HUB.ProjectPublications.checkBtnContent(cselected, primary);
			HUB.ProjectPublications.addHintContent(cselected);		

			// Content upload tabs
			var show = $('c-show');
			var uln = $('c-underline');
			var tabs = $$('.c-tab');
			var active = 'files';

			// Which pane is active?
			if($('base')) {
				active = $('base').value;
			}
			HUB.ProjectPublications.markActiveContent(active);
			
			var vid = $('vid') ? $('vid').value : 0;
			
			// Build ajax url
			var url = HUB.ProjectPublications.getPubUrl(1);
			url = url + '&versionid=' + vid;								
						
			// Content uploader		
			if(show) 
			{			
				var keyupTimer = '';
				
				// Display loading image
				$('c-show').empty();
				var p = new Element('p');
				imgpath = '/components/com_projects/assets/img/ajax-loader.gif';
				var img = new Element('img', {'src':imgpath}).injectInside(p);
				p.setStyles({'text-align': 'center', 'margin-top': '30px', 'vertical-align':'top'});
				$('c-show').appendChild(p);
				
				// Build URL
				switch(active)
				{
					default:
					case 'files':
					  	url = url + '&action=browser';
						url = url + '&active=files';
						url = url + '&content=files';
						url = url + '&primary=' + primary;
					  	break;
					case 'notes':
				 		url = url + '&action=browser&active=notes';
				  		break;
					case 'links':
					  	url = url + '&action=browser&active=files&content=links';
					  	break;
				}
				
				// Load content
				keyupTimer = setTimeout((function() {  
					new Ajax(url, {
						method : 'get',
						update: show,
						onComplete: function() { 
							HUB.ProjectPublications.afterAjaxContent($('c-show'), cselected);
						}
					}).request();		
				}), 500);
			}
			
			// Load content in left-hand side after clicking on a tab (supporting content)
			if(tabs.length > 0) {
				tabs.each(function(item) {
					if(item.getProperty('id') == 'c-tab-' + active && !item.hasClass('active')) {
						item.addClass('active');
					}
					item.addEvent('click', function(e) {
						new Event(e).stop();
						var active = item.getProperty('id').replace('c-tab-', '');
						if(show) {
							
							switch(active)
							{
								default:
								case 'files':
								  	url = url + '&action=browser';
									url = url + '&active=files';
									url = url + '&content=files';
									url = url + '&primary=' + primary;
								  	break;
								case 'notes':
								 	url = url + '&action=browser&active=notes';
								  	break;
								case 'links':
								  	url = url + '&action=browser&active=files&content=links';
								  break;
							}
							
							$('c-show').empty();
							var p = new Element('p');
							imgpath = '/components/com_projects/assets/img/ajax-loader.gif';
							var img = new Element('img', {'src':imgpath}).injectInside(p);
							p.setStyles({'text-align': 'center', 'margin-top': '30px', 'vertical-align':'top'});
							$('c-show').appendChild(p);

							new Ajax(url, {
								method : 'get',
								update: $('c-show'),
								onComplete: function() { 
									tabs.each(function(i) {
										if (i.hasClass('active')) {
											i.removeClass('active');
										}
									});
									if(item.getProperty('id') == 'c-tab-' + active && !item.hasClass('active')) {
										item.addClass('active');
									}

									HUB.ProjectPublications.afterAjaxContent($('c-show'), cselected);
									HUB.ProjectPublications.markActiveContent(active);
								}
							}).request();
						}
					});
				});	
			}
		}
		
		//---------------------------------------------
		// Audience section 
		//---------------------------------------------
		 if(section == 'audience') {
			
			var audience = $('audience') ? $('audience').value : '';
			var noshow = $('no-audience') ? $('no-audience').checked : false;
			var picked = new Array(); // container of selected items
			
			// Check original choice
			if(audience) {
				picked = audience.split('-');
				if(picked.length > 0) {
					for (var i = 0; i < picked.length; i++)
				    {
				      if($(picked[i])) {
						$(picked[i]).addClass('c-picked');
					  }
				    }
				}
			}
			
			HUB.ProjectPublications.checkAudience(picked, noshow);

			// Do not show audience checkbox
			if($('no-audience')) {
				$('no-audience').addEvent('click', function(e) {
					if($('no-audience').checked == true) {
						picked = [];
					}
					HUB.ProjectPublications.checkAudience(picked, $('no-audience').checked);
				});
			}		

			// On click choice
			var ac = $$('.c-click');
			if(ac.length > 0) {
				ac.each(function(item) {
					item.addEvent('click', function(e) {
						new Event(e).stop();
						var sel = item.getProperty('id');
						var idx = picked.indexOf(sel);
						if($('no-audience')) {
							$('no-audience').checked = false;
						}
						if(idx==-1) {
							picked.push(sel);
							
							if(!item.hasClass('c-picked')) {
								item.addClass('c-picked');
							}
						}
						else {
							picked.splice(idx, 1);
							item.removeClass('c-picked');
						}
						HUB.ProjectPublications.checkAudience(picked, $('no-audience').checked);
					});		
				});	
			}			
		}
		
		//---------------------------------------------
		// Description section 
		//---------------------------------------------
		 if(section == 'description') {
							
			HUB.ProjectPublications.checkBtnDescription();
			
			// Check if required fields are filled in
			var required_inputs = $$('.pubinput');
			if(required_inputs.length > 0) {
				required_inputs.each(function(item) {
					item.addEvent('keyup', function(e) 
					{				
						// Enable/disable save button
						HUB.ProjectPublications.checkBtnDescription();
					});
				});
			}
			
			// Check abstract length
			if($('pub_abstract'))
			{
				$('pub_abstract').addEvent('keyup', function(e) 
				{
					HUB.ProjectPublications.setCounter($('pub_abstract'), $('counter_abstract'));
				});				
			}
								
			// Enable preview for metadata content
			var pubwiki = $$('.pubwiki');
			if(pubwiki.length > 0) {
				pubwiki.each(function(item) {
					item.addEvent('keyup', function(e) 
					{				
						// Preview description
						var previewpane = item.getProperty('id').replace('pub_', '');	
						previewpane = 'preview-' + previewpane;
						if($(previewpane)) {
							HUB.ProjectPublications.previewWiki( item, $(previewpane) );	
						}
					});
				});
			}
		}
		
		//---------------------------------------------
		// Version section 
		//---------------------------------------------
		if(section == 'version') {
			var vlabel = $('edit-vlabel');
			if(vlabel) {
				// Edit version label (dev)
				vlabel.addEvent('click', function(e) {
					var original = vlabel.innerHTML;
					HUB.ProjectPublications.addEditForm (vlabel, original);
					if($('v-label') && !$('v-label').hasClass('hidden')) {
						$('v-label').addClass('hidden');
					}														
				});
			}
		}					
		
		//---------------------------------------------
		// Gallery section 
		//---------------------------------------------
		if(section == 'gallery') {
			var show = $('c-show');
			var cselected = new Array(); // container of selected content items		
			
			// Check preselected items
			var cd = $$('.c-drag');
			if(cd.length > 0) {
				cd.each(function(item) {
					var id = item.getProperty('id').replace('clone-', '');	
					cselected.push(id);
				});	
				HUB.ProjectPublications.afterAjaxContent($('c-show'), cselected);
				HUB.ProjectPublications.addDrag($('c-filelist'), 0);			
			}
			else if($('nosel') && $('nosel').hasClass('hidden')) {
				// Show 'no items' message
				$('nosel').removeClass('hidden');
			}
			HUB.ProjectPublications.checkBtnContent(cselected, 0);
			HUB.ProjectPublications.addHintContent(cselected);	
			
			// Content uploader		
			if(show) {
				var vid = $('vid') ? $('vid').value : 0;
				
				// Loading images
				$('c-show').empty();
				var p = new Element('p');
				imgpath = '/components/com_projects/assets/img/ajax-loader.gif';
				var img = new Element('img', {'src':imgpath}).injectInside(p);
				p.setStyles({'text-align': 'center', 'margin-top': '30px', 'vertical-align':'top'});
				$('c-show').appendChild(p);
				
				// Build ajax url
				var href = HUB.ProjectPublications.getPubUrl(1);
				href = href + '&active=files&versionid=' + vid + '&action=browser&content=files&images=1';							
				
				// Get content
				new Ajax(href, {
					method : 'get',
					update: show,
					onComplete: function() { 
						HUB.ProjectPublications.afterAjaxContent($('c-show'), cselected);
					}
				}).request();
			}		
		}
	},
	
	// CONTENT
	// When content selection tabs are enabled (supporting docs)
	markActiveContent: function(active) {
		var uln = $('c-underline');
		if(uln) {
			if(active == 'files') {
				uln.setStyles({'margin-left': '25px'});
			}
			else if(active == 'apps') {
				uln.setStyles({'margin-left': '170px'});
			}
			else if(active == 'links') {
				uln.setStyles({'margin-left': '70px'});
			}
			else if(active == 'articles') {
				uln.setStyles({'margin-left': '225px'});
			}
		}
	},
	
	// TAGS
	afterAjaxTags: function(updated) 
	{	
	
		var cselected = HUB.ProjectPublications.collectTagged();
		
		// Content item selection
		var cl = $$('.c-click');
		if(cl.length > 0 && $('c-taglist')) {
			cl.each(function(item) {
				item.removeEvents(); // clean-up past events
				HUB.ProjectPublications.attachEventsTags(item, cselected, updated);	
			});	
		}
		
		// Load more suggestions
		var more = $('more-tags');
		if(more) {
			more.removeEvents();
			more.addEvent('click', function(e) {
				new Event(e).stop();
				HUB.ProjectPublications.suggestTags(updated);													
			});
		}
		
		// Clean up ac field
		var bits = $$('.bit-box')
		if(bits.length > 0) {
			bits.each(function(item) {
				item.remove();
			});
		}
		var ac = $$('.autocompleter-choices');
		if(ac.length > 0) {
			ac.each(function(item) {
				item.style.display = 'none';
			});
		}
		
		if($('actags')) {
			$('actags').value = '';
		}
		
		// Add new tag
		var newtag = $('actags');
		var bu = $('add-tag');
		var frm = $('addtag-form');
		if (bu) {
			bu.removeEvents();
			bu.addEvent('click', function(e) {
				new Event(e).stop();
				if(newtag.value != '') {
					HUB.ProjectPublications.suggestTags(updated);
				}
			});
		}
		
		// Show/hide no selections message
		HUB.ProjectPublications.showNochoiceMessage(cselected);
		HUB.ProjectPublications.checkBtnContent(cselected, 1);
		
		if($('statusmsg')) {
			$('statusmsg').style.display = 'none';
		}	
	},
	
	collectTagged: function() {
		
		var cselected = new Array(); // container for selected items
		
		// If any are picked by ajax, add to container
		var picked = $$('.c-new');
		if(picked.length > 0) {
			picked.each(function(item) {
				var id = item.getProperty('id').replace('tag:', '');
				var cloned = $('clone-' + id);
				if(!cloned) {
					HUB.ProjectPublications.cloneTag(id, item);
				}
				item.removeClass('c-new');					
			});		
		}
			
		// Check preselected items
		var cd = $$('.c-drag');
		if(cd.length > 0) {
			cd.each(function(item) {
				var id = item.getProperty('id').replace('clone-', '');	
				cselected.push(id);
			});		
		}

		return cselected;
		
	},
	
	showNochoiceMessage: function (cselected) {
		
		if(cselected.length == 0 && $('nosel') && $('nosel').hasClass('hidden')) {
			$('nosel').removeClass('hidden');
		}
		else if(cselected.length > 0 && $('nosel') && !$('nosel').hasClass('hidden')) {
			$('nosel').addClass('hidden');
		}
	},
	
	cloneTag: function(id, item) {
		
		var classes = item.getProperty('class').split(" ");
		var raw_tag = '';

		for ( i=classes.length-1; i>=0; i-- ) {
			if (classes[i].contains('tag:')) {
				var raw_tag = classes[i].split(":")[1];
			}
		}	
		raw_tag = unescape(raw_tag);
		raw_tag = unescape(raw_tag.replace(/\+/g, " "));
		
		var clone = new Element('li', {
			'id': 'clone-'+ id,
			'class': 'tagged'
		}).inject($('c-taglist'), 'bottom');
		clone.addClass('c-drag');
		clone.innerHTML = raw_tag;	
	},
	
	// TAGS - item selection
	attachEventsTags: function(item, cselected, updated) {
		
		var id = item.getProperty('id').replace('tag:', '');
		
		// Marking as selected
		var idx = cselected.indexOf(id);
		if(idx!=-1) {
			if(!item.hasClass('c-picked')) {
				item.addClass('c-picked');
			}
		}
		
		// Selecting files
		item.addEvent('click', function(e) {
			item.removeEvents();																	
			var idx = cselected.indexOf(id);
			if(idx==-1) {
				if(!item.hasClass('c-picked')) {
					item.addClass('c-picked');
				}
				cselected.push(id);
				HUB.ProjectPublications.cloneTag(id, item);										
			}
			else {
				item.removeClass('c-picked');
				var rclone = $('clone-' + id);
				if(rclone) { rclone.remove(); }
				cselected.splice(idx, 1);
			}
			
			// Show/hide no selections message
			HUB.ProjectPublications.showNochoiceMessage(cselected);
			
			// Refresh Tag viewer	
			HUB.ProjectPublications.suggestTags(updated, cselected);
			
		});
	},
	
	// CONTENT
	// Get all Ajax stuff working after screen update
	afterAjaxContent: function(updated, cselected) {
		
		var primary = $('primary') ? $('primary').value : 1; // Primary or supporting content?
		var section = $('section') ? $('section').value : '';
		
		if ($('status-msg') && $('status-msg').hasClass('ajax-loading'))
		{
			$('status-msg').empty().removeClass('ajax-loading');
		}
					
		// Directory browsing (not used)
		var gotodir = $$('.gotodir');
		if(gotodir.length > 0) {
			gotodir.each(function(item) {
				item.addEvent('click', function(e) {
					new Event(e).stop();
					var href = item.href + '&no_html=1&ajax=1';
										
					new Ajax(href,{
							'method' : 'get',
							'update' : updated,
							onComplete: function() { 
								HUB.ProjectPublications.afterAjaxContent($('c-show'), cselected);
							}
					}).request();
				});
			});	
		}
					
		// Content item selection
		var cl = $$('.c-click');
		var cf = $('c-filelist');
		if(cl.length > 0 && cf) {
			cl.each(function(item) {
				item.removeEvents(); // clean-up past events
				if(section == 'gallery') {
					HUB.ProjectPublications.attachEventsGallery(item, cselected);	
				}
				else {
					HUB.ProjectPublications.attachEventsContent(item, cselected, primary);	
				}				
			});	
		}
		else {
			if($('c-browser') && !$('c-browser').hasClass('hidden')) {
				$('c-browser').addClass('hidden');
			}
		}
				
		// Check what's uploaded
		var bu = $('b-upload');
		if(bu) {
			bu.addEvent('click', function(e) {
				var proceed = 1;
				new Event(e).stop();
				if($('uploader').value != '') {
					// Check file format
					var format = HUB.ProjectPublications.checkFormat($('uploader').value);
					
					if (format == 'archive') {
						// Confirm further action - extract files?
						if(HUB.ProjectFiles) {
							HUB.ProjectFiles.addQuestion();	
						}
					}
					else if($('upload-form')) {	
						
						if(section == 'gallery') {
							if(format != 'image' && format != 'video') {
								if($('statusmsg')) {
									$('statusmsg').innerHTML = 'Please upload an image or video file in one of accepted formats.';
									$('uploader').value = '';
									proceed = 0;
								}
							}
						}
						if(proceed == 1) {
							HUB.ProjectFiles.submitViaAjax($('upload-form'), 'Uploading file(s)... Please wait');							
						}					
					}				
				}
			});
			
		}
						
		/* Link-type content */
		var default_url = 'http://';
		var u_submit = $('c-url-submit');
				
		if($('c-url')) {
			$('c-url').value = default_url;
			$('c-url').setStyle('color', '#999');
			if(u_submit) {
				u_submit.addClass('disabled');
			}
			
			$('c-url').addEvent('click', function(e) {
				// Clear default value
				if($('c-url').value == default_url)	 {
					$('c-url').value = '';
					$('c-url').setStyle('color', '#000');
				}												   
			});
			$('c-url').addEvent('change', function(e) {
				if(u_submit) {
					if(($('c-url').value == default_url || $('c-url').value.length == 0 ) && !u_submit.hasClass('disabled')) {
						u_submit.addClass('disabled');
					}
					else if(u_submit.hasClass('disabled') && $('c-url').value.length > 0 && $('c-url').value != default_url ) {
						u_submit.removeClass('disabled');
					}
					if($('c-url').value == '')	 {
						$('c-url').value = default_url;
						$('c-url').setStyle('color', '#999');
					}
				}
			});
			
			if(u_submit) {
				u_submit.addEvent('click', function(e) {
					new Event(e).stop();
					if(!u_submit.hasClass('disabled')) {
						// Do something with the link (attach)
					}
				});
			}
		}
	},
	
	// AUTHORS
	// Get all Ajax stuff working after screen update
	afterAjaxAuthors: function(updated, cselected) {
					
		// Content item selection
		var cl = $$('.c-click');
		var cf = $('c-authors');
		if(cl.length > 0 && cf) {

			cl.each(function(item) {
				
				// Clean-up past events
				item.removeEvents(); 
				
				// Get uid
				var classes = item.getProperty('class').split(" ");
				var uid = 0;
				var name = '';
				var org = '';
				var credit = '';

				for ( i=classes.length-1; i>=0; i-- ) {
					if (classes[i].contains('user:')) {
						var uid = classes[i].split(":")[1];
					}
					if (classes[i].contains('owner:')) {
						var owner = classes[i].split(":")[1];
					}
					if (classes[i].contains('name:')) {
						var name = classes[i].split(":")[1];
					}
					if (classes[i].contains('org:')) {
						var org = classes[i].split(":")[1];
					}
					if (classes[i].contains('credit:')) {
						var credit = classes[i].split(":")[1];
					}
				}

				HUB.ProjectPublications.attachEventsAuthors(item, cselected, uid, owner, name, org, credit );	
			});	
		}
		
		// Autocompleter for member names
		var elm = $('newmember');
		if (elm) {
			var completerMember = new Autocompleter.MultiSelectable.Ajax.Json(elm, 'index.php?option=com_projects&no_html=1&task=autocomplete&which=user', {
				'minLength': 1, // We wait for at least one character
				'overflow': true, // Overflow for more entries
				'wrapSelectionsWithSpacesInQuotes': false,
				'multiple': true,
				'tagger': null,
				'injectChoice': function(choice) {
					var el = new Element('li').setHTML(this.markQueryValue(choice[0]));
					el.inputValue = choice[1];
					this.addChoiceEvents(el).injectInside(this.choices);
				}
			});
		}
		
		// Add new author
		var bu 			= $('add-author');
		frm 			= $('addmember-form');
		var provisioned = $('provisioned') ? $('provisioned').value : 0;

		if (frm && bu) 
		{				
			bu.addEvent('click', function(e) {
				new Event(e).stop();
				
				if($('confirm-box')) {
					$('confirm-box').remove();	
				}
				if(completerMember) {
					completerMember.hideChoices();	
				}
				
				if($('newmember') && $('newmember').value != '')
				{	
					if (!SqueezeBoxHub) {
						SqueezeBoxHub.initialize({ size: {x: 600, y: 400} });
					}
					
					var vid = $('vid') ? $('vid').value : 0;
					var move 		= $('move') ? $('move').value : 0;
					
					var url = HUB.ProjectPublications.getPubUrl(1);					
					url = url + '&vid=' + vid;
					url = url + '&action=editauthor&move=' + move + '&new=' + escape($('newmember').value);
					bu.href= url;
					
					SqueezeBoxHub.fromElement(bu,{						
						size: {x: 600, y: 400}, 
						classWindow: 'sbp-window',
						classOverlay: 'sbp-overlay',
						handler: 'url', 
						ajaxOptions: {
							method: 'get',
							onComplete: function() {
								if($('cancel-action')) {
									$('cancel-action').addEvent('click', function(e) {
										SqueezeBoxHub.close();
									});
								}
								// Pass selections (publications)
								if($('ajax-selections')){
									if(HUB.ProjectPublications) {
										var selections = HUB.ProjectPublications.gatherSelections('clone-author::');
										$('ajax-selections').value = selections;
									}
								}
							}
						}
					});
				}				
			});
		}
	},
	
	// AUTHORS - item selection
	attachEventsAuthors: function(item, cselected, uid, owner, name, org, credit ) {
		
		// Marking as selected
		var idx = cselected.indexOf(owner);
		if(idx!=-1) {
			if(!item.hasClass('c-picked')) {
				item.addClass('c-picked');
			}
		}
		
		// Selecting authors
		item.addEvent('click', function(e) {																		
			var idx = cselected.indexOf(owner);

			if(!item.hasClass('c-picked')) {
				item.addClass('c-picked');
				if(idx==-1) {
					cselected.push(owner);
					if(cselected.length > 0 && $('nosel') && !$('nosel').hasClass('hidden')) {
						$('nosel').addClass('hidden');
					}
						
					var clone = new Element('li', {
						'id': 'clone-author::'+ owner,
						'class': 'c-drag'
					}).inject($('c-authors'), 'bottom');
					
					if(item.hasClass('i-missing')) {
						clone.addClass('i-missing');
					}
				
					var ordernum = new Element('span', {
						'class': 'a-ordernum'
					}).inject(clone, 'top');
					
					var edit = new Element('span', {
						'class': 'c-edit'
					}).inject(clone, 'bottom');
					
					var p = new Element('span', {
						'class': 'a-wrap'
					}).inject(clone, 'bottom');
					
					var authorname = new Element('span', {
						'class': 'a-authorname'
					}).inject(p, 'bottom');
					authorname.innerHTML = unescape(name.replace(/\+/g, " "));
					
					var authororg = new Element('span', {
						'class': 'a-org'
					}).inject(p, 'bottom');
					var orgname = unescape(org.replace(/\+/g, " "));
					authororg.innerHTML = orgname ? ', ' + orgname : '';
					
					var authorcredit = new Element('span', {
						'class': 'a-credit'
					}).inject(p, 'bottom');	
					authorcredit.innerHTML = credit ? unescape(credit.replace(/\+/g, " ")) : '';
					
					var projectid 	= $('projectid') ? $('projectid').value : 0;
					var pid 		= $('pid') ? $('pid').value : 0;
					var vid 		= $('vid') ? $('vid').value : 0;
					var move 		= $('move') ? $('move').value : 0;
					
					// Build ajax url
					var href = HUB.ProjectPublications.getPubUrl(0);
					href = href + '&vid=' + vid + '&move=' + move + '&action=editauthor&uid=' + uid + '&owner=' + owner;					
					
					var a = new Element('a', {
						'class': 'showinbox',
						'href': href
					}).inject(edit, 'bottom');		
					a.innerHTML = 'Edit';
					
					if(HUB.Projects)
					{
						HUB.Projects.initialize();
					}
				}
			}
			else {
				item.removeClass('c-picked');
				if(idx!=-1) {
					var rclone = $('clone-author::' + owner);
					if(rclone) {
						rclone.remove();
					}
					cselected.splice(idx, 1);
					if(cselected.length == 0 && $('nosel') && $('nosel').hasClass('hidden')) {
						$('nosel').removeClass('hidden');
					}
				}
			}
			
			// Add ordering number & enable dragging
			HUB.ProjectPublications.addDrag($('c-authors'), 1);
			
			// Enable/disable save button
			HUB.ProjectPublications.checkBtnAuthors(cselected);
			HUB.ProjectPublications.addHintContent(cselected);		
		});
	},
	
	// GALLERY- item selection
	attachEventsGallery: function(item, cselected) {
		
		// Get id
		var classes = item.getProperty('class').split(" ");
		var it = '';

		for ( i=classes.length-1; i>=0; i-- ) {
			if (classes[i].contains('item')) {
				var it = classes[i].split("|")[1];
			}
		}
		
		// Marking as selected
		var idx = cselected.indexOf(it);
		if(idx!=-1) {
			if(!item.hasClass('c-picked')) {
				item.addClass('c-picked');
			}
		}
		
		// Selecting files
		item.addEvent('click', function(e) {																		
			var idx = cselected.indexOf(it);
			if(!item.hasClass('c-picked')) {
				item.addClass('c-picked');
				if(idx==-1) {
					cselected.push(item.title);
					if(cselected.length > 0 && $('nosel') && !$('nosel').hasClass('hidden')) {
						$('nosel').addClass('hidden');
					}
					
					var clone = new Element('li', {
						'id': 'clone-'+ it,
						'class': 'c-drag'
					}).inject($('c-filelist'), 'bottom');
					
					if(item.hasClass('i-missing')) {
						clone.addClass('i-missing');
					}
													
					// Get image info	
					var vid 		= $('vid') ? $('vid').value : 0;
					var move 		= $('move') ? $('move').value : 0;
					
					// Build ajax url
					var href = HUB.ProjectPublications.getPubUrl(1);
					href = href + '&vid=' + vid + '&move=' + move + '&action=showimage&ima=' + it;					
					
					if(clone) {
						new Ajax(href,{
								'method' : 'get',
								'update' : clone,
								onComplete: function(response) {
									HUB.ProjectPublications.afterAjaxContent($('c-show'), cselected);
									
									if(HUB.Projects)
									{
										HUB.Projects.initialize();
									}
								}
						}).request();
					}
				}
			}
			else {
				item.removeClass('c-picked');

				var rclone = $('clone-' + it);
				if(rclone) {
					rclone.remove();
				}
				cselected.splice(idx, 1);
				if(cselected.length == 0 && $('nosel') && $('nosel').hasClass('hidden')) {
					$('nosel').removeClass('hidden');
				}
			}
			HUB.ProjectPublications.checkBtnContent(cselected, 0);
			HUB.ProjectPublications.addDrag($('c-filelist'), 0);	
			HUB.ProjectPublications.addHintContent(cselected);
		});
	},
	
	// CONTENT - item selection
	attachEventsContent: function(item, cselected, primary) {
	
		// Get id
		var classes = item.getProperty('class').split(" ");
		var it = '';

		for ( i=classes.length-1; i>=0; i-- ) {
			if (classes[i].contains('item')) {
				var it = classes[i].split("|")[1];
			}
		}
	
		// Marking as selected
		var idx = cselected.indexOf(it);
		if(idx!=-1) {
			if(!item.hasClass('c-picked')) {
				item.addClass('c-picked');
			}
		}
				
		// Selecting files
		item.addEvent('click', function(e) {																		
			var idx = cselected.indexOf(it);	
			
			if(!item.hasClass('c-picked')) {
				item.addClass('c-picked');
				if(idx==-1) {
					cselected.push(item.title);
					if(cselected.length > 0 && $('nosel') && !$('nosel').hasClass('hidden')) {
						$('nosel').addClass('hidden');
					}
					var clone = item.clone([true, false]).inject($('c-filelist'), 'bottom');
		
					clone.setProperty('id', 'clone-'+ it);
					clone.setProperty('title', item.title);
					clone.removeProperty('class');
					clone.addClass('c-drag');
					if(item.hasClass('i-missing')) {
						clone.addClass('i-missing');
					}
					var missed = clone.getElement('span');
					if(missed) {
						missed.remove();
					}
					
					var vid 		= $('vid') ? $('vid').value : 0;
					var move 		= $('move') ? $('move').value : 0;
						
					var edit = new Element('span', {
						'class': 'c-edit'
					}).inject(clone, 'bottom');

					// Build ajax url
					var href = HUB.ProjectPublications.getPubUrl(0);
					href = href + '&vid=' + vid + '&role=' + primary + '&move=' + move + '&action=edititem&item=' + escape(it);
									
					if(!$('c-filelist').hasClass('noedit')) {
						var a = new Element('a', {
							'class': 'showinbox',
							'href': href
						}).inject(edit, 'bottom');		
						a.innerHTML = 'Edit';

						// Reactivate edit link behavior						
						if(HUB.Projects)
						{
							HUB.Projects.initialize();
						}
					}				
					
					// Display additional attachment info (title, revision, serve as)
					var iteminfo = new Element('span', {
						'class': 'c-iteminfo',
						'id':'c-info-' + it
					}).inject(clone, 'bottom');
					HUB.ProjectPublications.showContentInfo(it, primary);
				}
			}
			else {
				item.removeClass('c-picked');

				var rclone = $('clone-' + it);
				if(rclone) {
					rclone.remove();
				}
				cselected.splice(idx, 1);
				if(cselected.length == 0 && $('nosel') && $('nosel').hasClass('hidden')) {
					$('nosel').removeClass('hidden');
				}
			}
			HUB.ProjectPublications.checkBtnContent(cselected, primary);
			if(primary == 1) {
				HUB.ProjectPublications.addOptionsContent(0, '');
			}
			else {
				HUB.ProjectPublications.addDrag($('c-filelist'), 0);	
			}

			HUB.ProjectPublications.addHintContent(cselected);
		});
	},
	
	
	//CONTENT
	// Load content attachment info
	showContentInfo: function (it, primary)
	{
		var info = 'c-info-' + it;
		var vid  = $('vid') ? $('vid').value : 0;

		// Build ajax url
		var url = HUB.ProjectPublications.getPubUrl(1);					
		url = url + '&vid=' + vid;
		url = url + '&action=showinfo';
		url = url + '&item=' + escape(it);
		url = url + '&role=' + primary;
		
		if($(info)) 
		{			 
			new Ajax(url,{
					'method' : 'get',
					'update' : info
			}).request();			
		}
	},
	
	//CONTENT
	// Add hint to drag files
	addHintContent: function (cselected)
	{
		if($('c-instruct')) {
			if(cselected.length > 1) {
				$('c-instruct').style.display = 'block';
			}
			else {
				$('c-instruct').style.display = 'none';
			}
		}
	},
	
	// AUTHORS
	// Enable/disable save & continue button, depending on sufficiency of required information
	checkBtnAuthors: function(cselected) 
	{
		var con = $('c-continue');
		
		if(con) {
			if(cselected.length > 0 && con.hasClass('disabled')) {
				con.removeClass('disabled');
			}
			else if(cselected.length == 0 && !con.hasClass('disabled')) {
				con.addClass('disabled');
			}
			con.removeEvents();
			con.addEvent('click', function(e) {
				new Event(e).stop();
				if(!con.hasClass('disabled')) {
					var selections = HUB.ProjectPublications.gatherSelections('clone-author::'); 
					
					if(selections) {
						if($('plg-form')) {	
							$('selections').value = selections;					
							$('plg-form').submit();
						}
					}	
				}
			});
		}

	},
	
	// CONTENT
	// Enable/disable save & continue button, depending on sufficiency of required information
	checkBtnContent: function(cselected, primary) 
	{
		var con = $('c-continue');
		
		if(con) {
			if(primary == 1) {
				if(cselected.length > 0 && con.hasClass('disabled')) {
					con.removeClass('disabled');
				}
				else if(cselected.length == 0 && !con.hasClass('disabled')) {
					con.addClass('disabled');
				}
			}
			else {
				con.removeClass('disabled');	
			}
			con.removeEvents();
			con.addEvent('click', function(e) {
				new Event(e).stop();
				if(!con.hasClass('disabled')) {
					var selections = HUB.ProjectPublications.gatherSelections('clone-'); 
					
					if(selections || primary == 0) {
						if($('plg-form')) {	
							$('selections').value = selections;					
							$('plg-form').submit();
						}
					}	
				}
			});
		}

	},
	
	gatherSelections: function(replacement) {
		var items = $$('.c-drag');

		var selections = ''; 
		if(items.length > 0) {
			items.each(function(item) {
				var id = item.getProperty('id').replace(replacement, '');
				if(id != '' && id != ' ') {
					selections = selections + id + '##' ;	
				}				
			});
		}
		return selections;
	},
	
	// DESCRIPTION, METADATA
	// Preview wiki content as you type
	previewWiki: function( raw, preview )
	{
		if(preview && raw) {				
			// Build ajax url
			var url = HUB.ProjectPublications.getPubUrl(1);

			url = url + '&action=wikipreview';
			url = url + '&raw=' + escape(raw.value);
								
			new Ajax( url,{
					'method' : 'post',
					'update' : preview
			}).request();
		}		
	},
	
	// DESCRIPTION
	// Enable/disable save & continue button, depending on sufficiency of required information
	checkBtnDescription: function() 
	{
		var con = $('c-continue');
		var required_inputs = $$('.pubinput');
		var checked = 1;
		
		if(con && required_inputs.length > 0) {
			required_inputs.each(function(item) {
				if(item.value == '') {
					checked = 0;	
				}
			});
		}
			
		if((checked == 1) && con.hasClass('disabled')) {
			con.removeClass('disabled');				
		}
		else if ((checked == 0) && !con.hasClass('disabled')) {
			con.addClass('disabled');
		}
		/*
		con.addEvent('click', function(e) {
			new Event(e).stop();
			if(!con.hasClass('disabled')) {
				if($('plg-form')) {					
					$('plg-form').submit();
				}
			}
		});
		*/
	},	

	// ALL - enable sorting of selected items
	addDrag: function(listelement, display_order ) {
		var numitems = $$('.c-drag').length;
		
		if(numitems == 0) {
			return false;
		}
		if(!listelement || listelement.hasClass('noedit')) {
			return false;
		}
		
		var sort = new Sortables(listelement, {
			initialize: function(){
				var step = 0;
				this.elements.each(function(element, i) {
					element.setStyle('list-style', 'none');
					
					// Display numbering
					if(display_order) {
						var numbox = element.getElement('span');
						
						if(element.getProperty('id') != 'nosel' && numbox) {
							numbox.innerHTML = step + '. ';
						}
					}
					step++;
				});	
			},
			onStart: function(el, clone) {
				/*var edits = $$('.c-edit');
				var keyupTimer = '';

				if(edits.length > 0) {
					edits.each(function(item) {
						keyupTimer = setTimeout((function() {  
							item.style.display = 'none';			
						}), 100);

					});
				}*/	
			},
			onComplete: function(el) {
				if(display_order) {
					var items = $$('.c-drag');
					var order = 1;
					if(items.length > 0) {
						items.each(function(item) {
							var numbox = item.getElement('span');

							if(item.getProperty('id') != 'nosel' && numbox) {
								numbox.innerHTML = order + '. ';
							}
							order++;
																		
						});
					}
				}
			}
		});
	},
	
	// CONTENT - extra primary content options
	addOptionsContent: function(initial_load, picked) 
	{
		// Get selections
		var selections = HUB.ProjectPublications.gatherSelections('clone-'); 
		
		var pubop = $('pub-options');
		if(!pubop) {
			return;
		}

		var vid   = $('vid') ? $('vid').value : 0;
		var base  = $('base') ? $('base').value : 'files';
				
		var href = HUB.ProjectPublications.getPubUrl(1);
		href     = href + '&vid=' + vid;
		href	 = href + '&action=showoptions&selections=' + escape(selections);
		
		if(initial_load) {
			href = href + '&ini=1';
		}
		if(picked) {
			href = href + '&serveas=' + picked;
		}
		href = href + '&base=' + base;
							
		new Ajax(href,{
				'method' : 'get',
				'update' : pubop,
				onComplete: function(response) {
					HUB.ProjectPublications.refreshOptionsContent();
				}
		}).request();		
	},
	
	// CONTENT - extra primary content options
	refreshOptionsContent: function() 
	{
		var serveas = $$('.serve_option');

		if(serveas.length > 0) {
			serveas.each(function(item) {
				item.addEvent('click', function(e) {
					new Event(e).stop();
					HUB.ProjectPublications.addOptionsContent(0, item.value);
				});
			});
		}
	},
	
	addEditForm: function (el, original) 
	{		
		el.addClass('hidden');
		if($('editv')) {
			return;
		}
		
		var vwrap = new Element('label', {
			'id': 'editv'
		}).injectInside(el.parentNode);
		
		// Input
		var vinput = new Element('input', {
			'type': 'text',
			'name': 'label',
			'class': 'vlabel',
			'maxlength': 10,
			'value': original
		}).injectInside(vwrap);
		
		// Add a submit button
		var vsubmit = new Element('input', {
			'type': 'submit',
			'value': 'save'
		}).injectInside(vwrap);
		
		// Add a cancel button
		var vcancel = new Element('input', {
			'type': 'button',
			'value': 'x',
			'class': 'cancel',
			'events': {
				'click': function() {
					vwrap.remove();
					el.removeClass('hidden');
					if($('v-label') && $('v-label').hasClass('hidden')) {
						$('v-label').removeClass('hidden');
					}												
				}
			}
		}).injectInside(vwrap);
	},
	
	// ACCESS - get access value
	getAccess: function(access) {
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
	
	suggestTags: function (show) 
	{	
		var vid = $('vid') ? $('vid').value : 0;
		var newtag = $('actags') ? $('actags').value : '';		
		var gathered = HUB.ProjectPublications.gatherSelections('clone-');
		var selections = escape(gathered); 
		
		// Build ajax url
		var url = HUB.ProjectPublications.getPubUrl(1);
		url = url + '&vid=' + vid + '&action=loadtags&selections=' + selections + '&tags=' + escape(newtag);
		
		var coord = show.getCoordinates();
		var margin = Math.round(coord['height']/3);
		if(margin < 1) { margin = 60; }
		var adjustment = 50;
		var topmargin = margin + 'px';
		var minheight = (coord['height'] - margin - adjustment) + 'px';
		show.empty();
		var p = new Element('p');
		var imgpath = '/components/com_projects/assets/img/ajax-loader.gif';
		var img = new Element('img', {'src':imgpath}).injectInside(p);
		// doesn't work in IE
		//p.setStyles({'text-align': 'center', 'padding': 0, 'margin': 0, 'padding-top': topmargin, 'padding-bottom': topmargin, 'min-height': minheight , 'vertical-align':'middle' });
		show.appendChild(p);

		new Ajax( url, {
			method : 'get',
			update: show,
			onComplete: function() { 
				HUB.ProjectPublications.afterAjaxTags(show);
			}
		}).request();	
	},
	
	checkAudience: function(picked, noshow) {
		var ac = $$('.c-click');
		var out = $('c-sel-audience');
		var con = $('c-continue');

		if(noshow == true) {
			// uncheck all
			if(ac.length > 0) {
				ac.each(function(item) {	
					if(item.hasClass('c-picked')) {
						item.removeClass('c-picked');
					}	
				});	
			}			
		}		

		con.addEvent('click', function(e) {
			new Event(e).stop();
			if(!con.hasClass('disabled')) {
				if($('plg-form')) {					
					$('plg-form').submit();
				}
			}
		});
		
		if(noshow == false && picked.length == 0) {
			// If nothing at all selected
			if(!out.hasClass('hidden')) {
				out.addClass('hidden');
			}
			if($('nosel') && $('nosel').hasClass('hidden')) {
				$('nosel').removeClass('hidden');
			}
			if(con && !con.hasClass('disabled')) {
				con.addClass('disabled');				
			}
		}
		else {	
			
			// Collect selections
			selections = '';
			if(picked.length > 0) {
				for ( i=0; i < picked.length; i++ ) {
					selections = selections + picked[i] + '-';
				}
			}
			$('audience').value = selections;
			
			// Show selection		
			if(out) {
					
				// Build ajax url
				var url = HUB.ProjectPublications.getPubUrl(1);
				url = url + '&action=showaudience&audience=' + selections + '&no_audience=' + noshow;					
				
				new Ajax(url,{
						'method' : 'get',
						'update' : out
				}).request();
			}
			
			// Hide instructions
			if($('nosel') && !$('nosel').hasClass('hidden')) {
				$('nosel').addClass('hidden');
			}
			if(out.hasClass('hidden')) {
				out.removeClass('hidden');
			}
			// Enable save button
			if(con && con.hasClass('disabled')) {
				con.removeClass('disabled');				
			}		
		}				
	},
	
	checkLicense: function(sel, license) {
		var ac = $$('.c-radio');
		var extra = $$('.c-extra');
		var chosen = $('c-sel-license');
		
		// uncheck all
		if(ac.length > 0) {
			ac.each(function(item) {	
				if(item.hasClass('c-picked')) {
					item.removeClass('c-picked');
				}	
			});	
		}
		
		// hide all details
		if(extra.length > 0) {
			extra.each(function(item) {	
				if(!item.hasClass('hidden')) {
					item.addClass('hidden');
				}	
			});	
		}
		// check selected
		if(license && license.value != '' && license.value != 0) {
			if(sel) {
				sel.addClass('c-picked');
				// show selected on the right
				if(chosen && chosen.hasClass('hidden')) {
					chosen.removeClass('hidden');
				}
				if(chosen) {
					chosen.innerHTML = sel.title;	
				}
				
				// Hide instructions
				if($('nosel') && !$('nosel').hasClass('hidden')) {
					$('nosel').addClass('hidden');
				}

				// show extra options
				var divextra = 'extra-' + license.value;
				if($(divextra) && $(divextra).hasClass('hidden')) {
					$(divextra).removeClass('hidden');
				}
			}			
		}
		else if($('nosel') && $('nosel').hasClass('hidden')) {
			$('nosel').removeClass('hidden');
		}
		
		// Check required information
		HUB.ProjectPublications.enableButtonLicense(license);
		
		var ltext = $('license-text-' + license.value);
		var agree = $('agree-' + license.value);
		if(ltext) {
			ltext.removeEvents();
			ltext.addEvent('keyup', function(e) {
				HUB.ProjectPublications.enableButtonLicense(license);
			});
		}
		if(agree) {
			agree.removeEvents();
			agree.addEvent('click', function(e) {
				HUB.ProjectPublications.enableButtonLicense(license);
			});
		}
		
		// Load template text
		var reload 	 = $('reload-' + license.value);
		var template = $('template-' + license.value);
		if(reload && template && ltext) {
			reload.addEvent('click', function(e) {
				ltext.value = template.innerHTML;
			});
		}
				
	},
	
	enableButtonLicense: function(license) {
		// Enable save button
		var con = $('c-continue');
		var ltext = $('license-text-' + license.value);
		var agree = $('agree-' + license.value);
		var passed = 1;
					
		if((license.value == '' || license.value == 0) && !con.hasClass('disabled')) {
			con.addClass('disabled');	
		}
		else if(license.value) {
			// Check for default text
			if(ltext && !ltext.parentNode.parentNode.hasClass('hidden') && !HUB.ProjectPublications.checkLicenseText(ltext.value)) {
				passed = 0;				
			}
			if(agree && !agree.parentNode.parentNode.hasClass('hidden') && agree.checked == false) {
				passed = 0;
			}
			if(passed == 1 && con.hasClass('disabled')) { 
				con.removeClass('disabled'); 
			}		
		}

		if(passed == 0 && !con.hasClass('disabled')) {
			con.addClass('disabled');
		}
		
		con.removeEvents();
		con.addEvent('click', function(e) {
			new Event(e).stop();
			if(!con.hasClass('disabled')) {
				if($('plg-form')) {					
					$('plg-form').submit();
				}
			}
		});
	},
	
	checkAccess: function(access) {
		var ac = $$('.c-radio');
		var extra = $$('.c-extra');
		var chosen = $('c-sel-access');
		var text = '';
		
		// uncheck all
		if(ac.length > 0) {
			ac.each(function(item) {	
				if(item.hasClass('c-picked')) {
					item.removeClass('c-picked');
				}	
			});	
		}
		
		// hide all tips
		if(extra.length > 0) {
			extra.each(function(item) {	
				if(!item.hasClass('hidden')) {
					item.addClass('hidden');
				}	
			});	
		}
				
		// check selected
		if(access && access.value != '') {
			if(access.value == 0 && $('access-public')) {
				$('access-public').addClass('c-picked');
				text = 'Public';
			}
			if(access.value == 1 && $('access-registered')) {
				$('access-registered').addClass('c-picked');
				text = 'Registered';
			}
			if(access.value > 1 && $('access-restricted')) {
				$('access-restricted').addClass('c-picked');
				text = 'Restricted';
			}
			if($('nosel') && !$('nosel').hasClass('hidden')) {
				$('nosel').addClass('hidden');
			}
			
			// show selected on the right
			if(chosen && chosen.hasClass('hidden')) {
				chosen.removeClass('hidden');
			}
			if(chosen) {
				chosen.innerHTML = text;	
			}
			
			// show tips & extra options
			var divextra = 'extra-' + access.value;
			if(divextra == 'extra-3') {
				divextra = 'extra-2';
			}
			if($(divextra) && $(divextra).hasClass('hidden')) {
				$(divextra).removeClass('hidden');
			}
			
		}
		else if($('nosel') && $('nosel').hasClass('hidden')) {
			$('nosel').removeClass('hidden');
		}
		
		// Enable save button
		var con = $('c-continue');
		if(access.value == '' && !con.hasClass('disabled')) {
			con.addClass('disabled');	
		}
		else if(access.value != '' && con.hasClass('disabled')) {
			con.removeClass('disabled');
		}
		con.addEvent('click', function(e) {
			new Event(e).stop();
			if(!con.hasClass('disabled')) {
				if($('plg-form')) {					
					$('plg-form').submit();
				}
			}
		});
		
	},
	
	checkLicenseText: function(text) {
		
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
	
	// CONTENT - check selected file format
	checkFormat: function(filename) {
		//var re = /\..+$/;
		var re = /[^.]+$/;
	    var extt = filename.match(re);
	
		if(!extt) {
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
		
		if(tar[ext]) {
			return 'archive';
		}
		else if(video[ext]) {
			return 'video';
		}
		else if(image[ext]) {
			return 'image';
		}
		else {
			return 'other';
		}	
		
	},
	setCounter: function(el, numel ) {		
		var maxchars = 250;			
		var current_length = el.value.length;
		var remaining_chars = maxchars-current_length;
		if(remaining_chars < 0) {
			remaining_chars = 0;
		}
		
		if(numel) {
			if(remaining_chars <= 10){
				numel.innerHTML = remaining_chars + ' chars remaining';
				$(numel).setStyle('color', '#ff0000');			
			} else {
				$(numel).setStyle('color', '#999999');
				numel.innerHTML = remaining_chars + ' chars remaining';
			}
		}
		
		if (remaining_chars == 0) {
			el.setProperty('value', el.getProperty('value').substr(0,maxchars));
		}			
	},
	
	getPubUrl: function(no_html) {		
		var projectid = $('projectid') ? $('projectid').value : 0;
		var pid = $('pid') ? $('pid').value : 0;
		var provisioned = $('provisioned') ? $('provisioned').value : 0;

		if(provisioned == 1) {
			var url = '/publications/submit/?pid=' +  pid;
		}
		else {
			var url = '/projects/' + projectid + '/publications/?pid=' +  pid;					
		}
		if(no_html == 1) {
			url = url + '&no_html=1&ajax=1';	
		}
		return url;
	}
}

	
window.addEvent('domready', HUB.ProjectPublications.initialize);