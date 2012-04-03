/**
 * @package     hubzero-cms
 * @file        components/com_resources/tagbrowser.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//----------------------------------------------------------
// Establish the namespace if it doesn't exist
//----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Tag Browser
//----------------------------------------------------------
HUB.TagBrowser = {
	
	jQuery: $,

	settings: {
	},
	
	//isIE: false,
	col1active: '',
	col2active: '',
	baseURI: 'index.php?option=com_resources&task=browser&no_html=1',

	detect: function() {
		// simplify things
		var agent 	= navigator.userAgent.toLowerCase();
				
		// detect platform
		this.isMac		= (agent.indexOf('mac') != -1);
		this.isWin		= (agent.indexOf('win') != -1);
		this.isWin2k	= (this.isWin && (
				agent.indexOf('nt 5') != -1));
		this.isWinSP2	= (this.isWin && (
				agent.indexOf('xp') != -1 || 
				agent.indexOf('sv1') != -1));
		this.isOther	= (
				agent.indexOf('unix') != -1 || 
				agent.indexOf('sunos') != -1 || 
				agent.indexOf('bsd') != -1 ||
				agent.indexOf('x11') != -1 || 
				agent.indexOf('linux') != -1);
				
		// detect browser
		this.isSafari	= (agent.indexOf('safari') != -1);
		this.isSafari2 = (this.isSafari && (parseFloat(agent.substring(agent.indexOf("applewebkit/")+"applewebkit/".length,agent.length).substring(0,agent.substring(agent.indexOf("applewebkit/")+"applewebkit/".length,agent.length).indexOf(' '))) >=  300));
		this.isOpera	= (agent.indexOf('opera') != -1);
		this.isNN		= (agent.indexOf('netscape') != -1);
		this.isIE		= (agent.indexOf('msie') != -1);
		this.isFirefox	= (agent.indexOf('firefox') != -1);
		this.isCamino	= (agent.indexOf('camino') != -1);
		this.isMozilla	= (agent.indexOf('mozilla') != -1);
	},

	incompatible: function() {
		$('tagbrowser').parentNode.removeChild(div);
		$('tbh2').parentNode.removeChild(tbh2);
	},

	nextLevel: function(type, input, input2, level, id, rid) {
		var com = this,
			$ = this.jQuery;

		if (level == 2) {
			if (HUB.TagBrowser.col2active != '' && $(HUB.TagBrowser.col2active)) {
				$(HUB.TagBrowser.col2active).removeClass('open');
			}
			var curractive = $('#'+id);
			curractive.addClass('open');
			HUB.TagBrowser.col2active = '#'+id;
		} else {
			if (HUB.TagBrowser.col1active!='') {
				var prevactive = $(HUB.TagBrowser.col1active);
				if (prevactive) {
					prevactive.removeClass('open');
				}
			}
			var curractive = $('#'+id);
			curractive.addClass('open');
			HUB.TagBrowser.col1active = '#'+id;
		}
		var sortby = '';
		if ($('#sortby')) {
			sortby = $('#sortby').val();
		}
		var filterby = '';
		var frm = document.getElementById('tagBrowserForm');
		if (frm && frm.filter) {
			for (var i=0; i < frm.filter.length; i++){
				if (frm.filter[i].checked) {
					filterby += '&filter[]='+frm.filter[i].value;
				}
			}
		}
		var url = HUB.TagBrowser.baseURI+'&type='+type+'&level='+level+'&input='+input+'&input2='+input2+'&id='+rid+'&sortby='+sortby+filterby;
		/*if(browser.isFirefox ==false && browser.isCamino==false && browser.isMozilla) {
			var ev = false;
		} else {
			var ev = true;
		}
		var myAjax = new Ajax(url, {
			method: 'get',
			update: $('level-'+level), 
			onSuccess: function(){
				if ($('rid')) {
					var r = $('rid').value;
					if ($('col2_'+r)) {
						$('col2_'+r).addClass('open');
					}
				}
			}, 
			evalScripts: ev
		}).request();*/
		
		$.get(url, {}, function(data) {
			$('#level-'+level).html(data);
			if ($('#rid')) {
				var r = $('#rid').val();
				if ($('#col2_'+r)) {
					$('#col2_'+r).addClass('open');
				}
			}
		});
	},
	
	changeSort: function() {
		var com = this,
			$ = this.jQuery;
			
		var type = $('#pretype').val();
		var k = $('#preinput2').val();
		var p = null;
		$("#level-1 .open").each(function(el) {
			p = el.attr('id');
		});
		var i = p.replace('col1_', '');
		i = (i == 'all') ? '' : i;
		
		HUB.TagBrowser.nextLevel(type, i, k, 2, p, 0);
	},
	
	sc: 0,
	
	setScroll: function() {
		var com = this,
			$ = this.jQuery;
			
		if ($('#d')) {
			atg = $('#atg').val();
			d = $('#d').val();
			if ($("#col1_"+atg)) {
				objDiv = $("#ultags");
				dist = $("#col1_"+atg).offsetHeight;
				objDiv.scrollTop = ((dist * d) - dist);
				clearTimeout(HUB.TagBrowser.sc);
			}
		}
	},

	initialize: function() {
		var com = this,
			$ = this.jQuery,
			imgpath = '/components/com_resources/images/loading.gif';
			
		if (!$('#tagbrowser')) {
			return;
		}
			
		var input = $('#preinput').val();
		var input2 = $('#preinput2').val();
		var type = $('#pretype').val();
		var id = $('#id').val();

		$('#tagbrowser').show();
		$('#tbh2').show();
		$('#viewalltools').hide();
			
		if (input != '') {
			HUB.TagBrowser.col2active = '#col1_'+input;
		} else {
			HUB.TagBrowser.col2active = '#col1_all';
		}
		
		$('<img src="'+imgpath+'" id="loading-img1" />').appendTo($('#level-1-loading'));
		$('<img src="'+imgpath+'" id="loading-img2" />').appendTo($('#level-2-loading'));
		
		$.get(HUB.TagBrowser.baseURI+'&type='+type+'&level=1&input='+input+'&input2='+input2+'&id='+id, {}, function(data) {
			$('#level-1').html(data);
			HUB.TagBrowser.sc = setTimeout("HUB.TagBrowser.setScroll()", 500);
		});

		$.get(HUB.TagBrowser.baseURI+'&type='+type+'&level=2&input='+input+'&input2='+input2+'&id='+id, {}, function(data) {
			$('#level-2').html(data);
		});
	}
}

jQuery(document).ready(function($){
	HUB.TagBrowser.initialize();
});

