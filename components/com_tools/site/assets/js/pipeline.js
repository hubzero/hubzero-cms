/**
 * @package     hubzero-cms
 * @file        components/com_ToolsPipeline/ToolsPipeline.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// ToolsPipeline admin actions form
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.ToolsPipeline = {
	jQuery: jq,
	
	form: '#adminCalls',
	statusform: '#statusForm',
	toolid: '#id',
	action: '#action',
	newstate: '#newstate',
	loader: '#ctSending',
	success: '#ctSuccess',
	wrap : '#wrap',
	canceltool : '#ctCancel',
	commentarea : '#ctComment',
	admintools : '#admintools',
	templatepath : '',
	
	initialize: function() {
		var $ = this.jQuery;
		
		if ($('#ctSending').length > 0) {
			HUB.ToolsPipeline.hide(HUB.ToolsPipeline.loader);
		}
		if ($('#ctCancel').length > 0) {
			HUB.ToolsPipeline.hide(HUB.ToolsPipeline.canceltool);
		}
		if ($('#ctComment').length > 0) {
			HUB.ToolsPipeline.hide(HUB.ToolsPipeline.commentarea);
		}

		if ($('#admintools').length > 0) { // show admin controls from start
			HUB.ToolsPipeline.show(HUB.ToolsPipeline.admintools);
			$('#ctad').addClass('collapse');
			$('#ctad').on('click', function(){
				if ($('#ctad').hasClass('collapse')) {
					$('#ctad').removeClass('collapse');
					HUB.ToolsPipeline.hide(HUB.ToolsPipeline.admintools);
					$('#ctad').addClass('expand');
				} else {
					$('#ctad').removeClass('expand');
					HUB.ToolsPipeline.show(HUB.ToolsPipeline.admintools);
					$('#ctad').addClass('collapse');
				}
				return false;
			});
		}

		var conf = $('.conf');
		var config = $('.config');
		if (config) {
			// version page controls
			$('.showcontrols').each(function(i, item) {
				$($(item).find('a')[0]).on('click', function() {
					var vnum = $(this).attr('id').replace('exp_',''); //$(item.getElements('a')[0]).attr('id').replace('exp_','');
					var vtr = '#configure_' + vnum;
					
					//var aexp = item.getElements('a')[0];
					
					if ($(this).hasClass('collapse')) {
						$('#displays_' + vnum).removeClass('highlighted_upper');
						$('#conftdone_' + vnum).removeClass('highlighted_lower');
						$('#conftdtwo_' + vnum).removeClass('highlighted_lower');
						$(this).removeClass('collapse');
						$(vtr).addClass('hide');
						$(this).addClass('expand');
					} else {
						$('#displays_' + vnum).addClass('highlighted_upper');
						$('#conftdone_' + vnum).addClass('highlighted_lower');
						$('#conftdtwo_' + vnum).addClass('highlighted_lower');
						$(this).removeClass('expand');
						$(vtr).removeClass('hide');
						$(this).addClass('collapse');
					}
					return false;
				});
			});
		}
		
		// show screenshots for diferent versions
		var ssform = document.getElementById("screenshots-form");
		if (ssform && $('#vchange_dev').length > 0) {
			$('#vchange_dev').on('change', function() {
				ssform.changing_version.value = 1;
				ssform.submit();
			});
			$('#vchange_current').on('change', function() {	
				ssform.changing_version.value = 1;
				ssform.submit();
			});
		}
		
		// close pop-up window
		if ($('#ss-pop-form').length > 0) {
			$('#ss-pop-form').on('submit', function(e) {	
				e.preventDefault();
				
				$.post($('#ss-pop-form').attr('action'), {}, function(data) {
					window.close();
					if (window.opener && !window.opener.closed) {
						parentssform = window.opener.document.getElementById("screenshots-form");
						parentssform.changing_version.value = 1;
						parentssform.submit();
					}
				});
			});
		}
		
		// change status
		$('.flip').each(function(i, item) {
			$(item).on('click', function(){
				var newi = $($(this).parent()).attr('id').replace('_', '');
				$('#newstate').val(newi);
				$(HUB.ToolsPipeline.statusform).submit();
				return false;
			});
		});
		
		// Manage license screen
		HUB.ToolsPipeline.manageLicense();
		
		$('.showcancel').on('click', function(){
			HUB.ToolsPipeline.show(HUB.ToolsPipeline.canceltool);
			return false;
		});
	
		$('.hidecancel').on('click', function(){
			HUB.ToolsPipeline.hide(HUB.ToolsPipeline.canceltool);
			return false;
		});
		
		$('.showmsg').on('click', function(){
			HUB.ToolsPipeline.show(HUB.ToolsPipeline.commentarea);
			return false;
		});
	
		$('.hidemsg').on('click', function(){
			HUB.ToolsPipeline.hide(HUB.ToolsPipeline.commentarea);
			return false;
		});
		
		// show groups
		$('.groupchoices').each(function(i, item) {
			$(item).on('change', function(){
				HUB.ToolsPipeline.checkGroup(this.selectedIndex, this.length);
			});
		});
		
		$('script').each(function(i, s) {
			if (s.src && s.src.match(/hub\.jquery\.js(\?.*)?$/)) {
				HUB.ToolsPipeline.templatepath = s.src.replace(/js\/hub\.jquery\.js(\?.*)?$/,'');
			}
		});
		
		if ($('#cancel-action').length > 0) {
			$('#cancel-action').unbind();
			$('#cancel-action').on('click', function(e) {
				$($(this).parent()).animate({opacity:0.0}, 500, function() {
				    $($(this).parent()).html('');
				});
			});
		}
		
		// NEW
		var adminactions = $('.adminaction');
		if (adminactions.length > 0)
		{						
			adminactions.each(function(i, item) {
				$(item).on('click', function() {
					
					href = $(this).attr('href');
					if (href.indexOf('no_html=1') == -1) {
						if (href.indexOf('?') == -1) {
							href += '?no_html=1';
						} else {
							href += '&no_html=1';
						}
						$(this).attr('href', href);
					}
					
					var outcome = $('#ctOutcome');
					
					var loading = HUB.ToolsPipeline.loadingIma('Performing your request');
				
					$(outcome).html(loading);
					
					$.get($(this).attr('href'), {}, function(data){
						$(outcome).html(data);
						
						$(outcome).prepend($('<span id="cancel-action">&nbsp;</span>'));
						
						if ($('#cancel-action').length > 0) {
							$('#cancel-action').unbind();
							$('#cancel-action').on('click', function(e) {
								$(outcome).animate({opacity:0.0}, 500, function() {
								    $(outcome).html('');
								});
							});
						}
						
					});

					return false;
					
				});
			});
		}
		
		// admin actions
		var admincalls = $('.admincall');
		if (admincalls) {
			admincalls.each(function(i, item) {
				$(item).on('click', function(){
					href = $(this).attr('href');
					if (href.indexOf('no_html=1') == -1) {
						if (href.indexOf('?') == -1) {
							href += '?no_html=1';
						} else {
							href += '&no_html=1';
						}
						$(this).attr('href', href);
					}

					var actionlabel = $($(this).parent()).attr('id');
					//var frm = document.getElementById(HUB.ToolsPipeline.form.replace('#', ''));
				
					var actiontxt = '';
					if (actionlabel == 'publishtool') {
						actiontxt = 'Publishing tool...';
					} else if (actionlabel == 'publishtool') {
						actiontxt =  'Installing tool...';
					} else if (actionlabel == 'createtool') {
						actiontxt =  'Creating tool project area...';
					} else if (actionlabel == 'retiretool') {
						actiontxt =  'Retiring tool...';
					} else {
						actiontxt =  'Performing action...';
					}
				
					$(HUB.ToolsPipeline.loader).html('');
					$('<p><img src="/components/com_tools/assets/img/ajax-loader.gif" />' + actiontxt + '</p>').appendTo($(HUB.ToolsPipeline.loader));

					HUB.ToolsPipeline.show(HUB.ToolsPipeline.loader);
					HUB.ToolsPipeline.hide(HUB.ToolsPipeline.success);

					$.get($(this).attr('href'), {}, function(data){
						$(HUB.ToolsPipeline.success).html(data);
						HUB.ToolsPipeline.hideTimer();
					});

					return false;
				});
			});
		}
	},
	
	loadingIma: function(txt)
	{
		var html = '<p id="fbwrap">' + 
			'<span id="facebookG">' +
			' <span id="blockG_1" class="facebook_blockG"></span>' +
			' <span id="blockG_2" class="facebook_blockG"></span>' +
			' <span id="blockG_3" class="facebook_blockG"></span> ';
		
		if (txt)
		{
			html = html + txt;
		}
		
		html = html + 
			'</span>' +
		'</p>';
		
		return html;
	},
	
	manageLicense: function() 
	{
		var $ = this.jQuery;
		
		if (!$('#licenseForm').length) 
		{
			return false;
		}
		
		HUB.ToolsPipeline.licOptions();
		
		// Show options depending on open/closed source selection
		$('#t_code').on('change', function()
		{
			HUB.ToolsPipeline.licOptions();
		});

		$('#templates').on('change', function()
		{
			HUB.ToolsPipeline.getTemplate();
		});
		
	},

	licOptions: function() 
	{
		var $ = this.jQuery;
		var sel = $('#t_code').find(":selected").attr('value');
			
		if (sel == "@OPEN") {
			$("#open-source").show();
			$("#closed-source").hide();
			$('#choice-icon').addClass('opensource');
			$('#choice-icon').removeClass('closedsource');
		} 
		else {
			$("#open-source").hide();
			$("#closed-source").show();
			$('#choice-icon').removeClass('opensource');
			$('#choice-icon').addClass('closedsource');
		}
		
	},
	
	getTemplate: function() {
		var $ = this.jQuery;
		var tmpl = $('#templates').find(":selected").attr('value');
		if (tmpl != 'c1') 
		{
			var t = $('#' + tmpl).text();
			var co = $('#license').val(t);
		} 
	},
	
	hideTimer: function() {
		HUB.ToolsPipeline.hide(HUB.ToolsPipeline.loader);
		HUB.ToolsPipeline.show(HUB.ToolsPipeline.success);
	},
	
	/*sendForm: function() {
		var $ = this.jQuery;
		
		HUB.ToolsPipeline.show(HUB.ToolsPipeline.loader);
		HUB.ToolsPipeline.hide(HUB.ToolsPipeline.success);
		
		$.post('/index.php', $(HUB.ToolsPipeline.form).serialize(), function(data){
			$(HUB.ToolsPipeline.success).html(data);
			HUB.ToolsPipeline.hideTimer();
		});
	},*/ 
	
	hide: function(obj) {
		var $ = this.jQuery;
		
		$(obj).css('display', 'none');
	},
	
	show: function(obj) {
		var $ = this.jQuery;
		
		$(obj).css('display', 'block');
	},
	
	checkGroup: function(optionSelected, optionTotal) {
		var $ = this.jQuery;
		
		if (optionSelected==(optionTotal - 1)) {
			$("#groupname").show();
		} else {
			$("#groupname").hide();
		}
	}
}

jQuery(document).ready(function($){
	HUB.ToolsPipeline.initialize();
});
