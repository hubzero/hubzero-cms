/**
 * @package     hubzero-cms
 * @file        components/com_contribtool/contribtool.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//----------------------------------------------------------
// Contribtool admin actions form
//----------------------------------------------------------
HUB.ContribTool = {
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
	
	initialize: function() {
		
		
		
		if($('#ctSending')) {
		HUB.ContribTool.hide(HUB.ContribTool.loader);
		}
		if($('#ctCancel')) {
		HUB.ContribTool.hide(HUB.ContribTool.canceltool);
		}
		if($('#ctComment')) {
		HUB.ContribTool.hide(HUB.ContribTool.commentarea);
		}
		
		
		var editform = document.getElementById("hubForm");
		if (editform) {
			$('.returntoedit').each(function(i, item) {
				item.on('click', function() {
					var editform = document.getElementById("hubForm");
					editform.step.value = editform.step.value-2;
					editform.task.value = "start";
					editform.submit();
					return false;
				});
			});
		}
		
		
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
				$(item.getElements('a')[0]).on('click', function() {				
					var vnum = $(item.getElements('a')[0]).attr('id').replace('exp_','');
					var vtr = '#configure_' + vnum;
					
					var aexp = item.getElements('a')[0];
					
					if ($(aexp).hasClass('collapse')) {
						$('#displays_' + vnum).removeClass('highlighted_upper');
						$('#conftdone_' + vnum).removeClass('highlighted_lower');
						$('#conftdtwo_' + vnum).removeClass('highlighted_lower');
						$(aexp).removeClass('collapse');
						$(vtr).addClass('hide');
						$(aexp).addClass('expand');
					} else {
						$('#displays_' + vnum).addClass('highlighted_upper');
						$('#conftdone_' + vnum).addClass('highlighted_lower');
						$('#conftdtwo_' + vnum).addClass('highlighted_lower');
						$(aexp).removeClass('expand');
						$(vtr).removeClass('hide');
						$(aexp).addClass('collapse');
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
				
				$.post($('index.php', {}, function(data) {
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
		var flip = $('.flip');
		if (flip) {
			flip.each(function(i, item) {
				item.on('click', function(){
					var newi = $(item.parentNode).attr('id').replace('_', '');
					var frm2 = document.getElementById(HUB.ContribTool.statusform);
					frm2.newstate.value = newi;
					frm2.submit( );
					return false;
				});
			});
		}
		
		// flip license code
		if ($('#curcode')) {
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
				item.on('change', function(){
					HUB.ContribTool.checkGroup(this.selectedIndex, this.length);
				});
			});
		}
		
		// admin actions
		var admincalls = $('.admincall');
		if (admincalls) {
			admincalls.each(function(i, item) {
				item.on('click', function(){
					var actionlabel = $(item.parentNode).getProperty('id');
					var frm = document.getElementById(HUB.ContribTool.form);
				
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
				
					$(HUB.ContribTool.loader).empty();
					var p = new Element('p');
					var img = new Element('img', {'src':HUB.Base.templatepath+'html/com_contribtool/images/ajax-loader.gif'}).injectInside(p);
					var txt = document.createTextNode(actiontxt);
					p.appendChild(txt);
					$(HUB.ContribTool.loader).appendChild(p);
				
					frm.task.value = actionlabel;
					frm.no_html.value = 1;
					HUB.ContribTool.sendForm();
					return false;
				});
			});
		}
	},

	licOptions: function() {
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
		HUB.ContribTool.show(HUB.ContribTool.loader);
		HUB.ContribTool.hide(HUB.ContribTool.success);
		
		$(HUB.ContribTool.form).send({
				update: HUB.ContribTool.success,
				onComplete: function() {
					HUB.ContribTool.hideTimer();
				}
        });
	},
	
	hide: function(obj) {
		$(obj).css('display', 'none');
	},
	
	show: function(obj) {
		$(obj).css('display', 'block');
	},
	
	checkGroup: function(optionSelected, optionTotal) {
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
