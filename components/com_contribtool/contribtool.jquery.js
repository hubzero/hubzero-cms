/**
 * @package     hubzero-cms
 * @file        components/com_contribtool/contribtool.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Contribtool admin actions form
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.ContribTool = {
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
		
		if($('#ctSending')) {
			HUB.ContribTool.hide(HUB.ContribTool.loader);
		}
		if($('#ctCancel')) {
			HUB.ContribTool.hide(HUB.ContribTool.canceltool);
		}
		if($('#ctComment')) {
			HUB.ContribTool.hide(HUB.ContribTool.commentarea);
		}
		
		
		$('.returntoedit').each(function(i, item) {
			$(item).on('click', function() {
				var editform = document.getElementById("hubForm");
				editform.step.value = editform.step.value-2;
				editform.task.value = "start";
				editform.submit();
				return false;
			});
		});
		
		
		if ($('#admintools')) { // show admin controls from start
			HUB.ContribTool.show(HUB.ContribTool.admintools);
			$('#ctad').addClass('collapse');
			$('#ctad').on('click', function(){
				if ($('#ctad').hasClass('collapse')) {
					$('#ctad').removeClass('collapse');
					HUB.ContribTool.hide(HUB.ContribTool.admintools);
					$('#ctad').addClass('expand');
				} else {
					$('#ctad').removeClass('expand');
					HUB.ContribTool.show(HUB.ContribTool.admintools);
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
		if (ssform && $('#vchange_dev')) {
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
		if ($('#ss-pop-form')) {
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
				var newi = $(this.parentNode).attr('id').replace('_', '');
				$('#newstate').val(newi);
				$(HUB.ContribTool.statusform).submit();
				return false;
			});
		});
		
		// flip license code
		if ($('#curcode')) {
			if (document.getElementById('versionForm')) {
				var sel = getSelectedOption('versionForm', 't_code');
				if (sel.value == "@OPEN") {
					HUB.ContribTool.show($('#lic'));
					HUB.ContribTool.show($('#legendnotes'));
					HUB.ContribTool.hide($('#lic_cl'));
				} else {
					HUB.ContribTool.hide($('#lic'));
					HUB.ContribTool.hide($('#legendnotes'));
					HUB.ContribTool.show($('#lic_cl'));
				}
			}
			
			$('#t_code').on('change', function(){
				HUB.ContribTool.licOptions();
			});
			$('#templates').on('change', function(){
				HUB.ContribTool.getTemplate();
			});
		}
		
		if ($('.showcancel')) {
			$('.showcancel').on('click', function(){
				HUB.ContribTool.show(HUB.ContribTool.canceltool);
				return false;
			});
		
			$('.hidecancel').on('click', function(){
				HUB.ContribTool.hide(HUB.ContribTool.canceltool);
				return false;
			});
		}
		
		if ($('.showmsg')) {
			$('.showmsg').on('click', function(){
				HUB.ContribTool.show(HUB.ContribTool.commentarea);
				return false;
			});
		
			$('.hidemsg').on('click', function(){
				HUB.ContribTool.hide(HUB.ContribTool.commentarea);
				return false;
			});
		}
		
		// show groups
		var groups = $('.groupchoices');
		if (groups) {
			groups.each(function(i, item) {
				$(item).on('change', function(){
					HUB.ContribTool.checkGroup(this.selectedIndex, this.length);
				});
			});
		}
		
		$('script').each(function(i, s) {
			if (s.src && s.src.match(/hub\.jquery\.js(\?.*)?$/)) {
				HUB.ContribTool.templatepath = s.src.replace(/js\/hub\.jquery\.js(\?.*)?$/,'');
			}
		});
		
		// admin actions
		var admincalls = $('.admincall');
		if (admincalls) {
			admincalls.each(function(i, item) {
				$(item).on('click', function(){
					var actionlabel = $($(this).parent()).attr('id');
					var frm = document.getElementById(HUB.ContribTool.form.replace('#', ''));
				
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
				
					$(HUB.ContribTool.loader).html('');
					$('<p><img src="' + HUB.ContribTool.templatepath + 'html/com_contribtool/images/ajax-loader.gif" />' + actiontxt + '</p>').appendTo($(HUB.ContribTool.loader));

					frm.elements['task'].value = actionlabel;
					frm.elements['no_html'].value = 1;
					HUB.ContribTool.sendForm();
					return false;
				});
			});
		}
	},

	licOptions: function() {
		var $ = this.jQuery;
			
		if (document.getElementById('versionForm')) {
			var sel = getSelectedOption( 'versionForm', 't_code' );
			if (sel.value == "@OPEN") {
				HUB.ContribTool.show($('#lic'));
				HUB.ContribTool.show($('#legendnotes'));
				HUB.ContribTool.hide($('#lic_cl'));
			} else {
				HUB.ContribTool.hide($('#lic'));
				HUB.ContribTool.hide($('#legendnotes'));
				HUB.ContribTool.show($('#lic_cl'));
			}
		}
	},
	
	getTemplate: function() {
		var id = getSelectedOption( 'versionForm', 'templates' );
		if (id.value != 'c1') {
			var hi = document.getElementById(id.value).value;
			var co = document.getElementById('license');
			co.value = hi;
		} else {
			var co = document.getElementById('license');
			co.value = '';
		}
	},
	
	hideTimer: function() {
		HUB.ContribTool.hide(HUB.ContribTool.loader);
		HUB.ContribTool.show(HUB.ContribTool.success);
	},
	
	sendForm: function() {
		var $ = this.jQuery;
		
		HUB.ContribTool.show(HUB.ContribTool.loader);
		HUB.ContribTool.hide(HUB.ContribTool.success);
		
		$.post('/index.php', $(HUB.ContribTool.form).serialize(), function(data){
			$(HUB.ContribTool.success).html(data);
			HUB.ContribTool.hideTimer();
		});
	},
	
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
	HUB.ContribTool.initialize();
});
