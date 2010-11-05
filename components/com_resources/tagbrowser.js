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

	nextLevel: function(type, input, input2, level, id, rid, terms) {
		var browser = new HUB.TagBrowser.detect();
		
		if(level == 2) {
			if(HUB.TagBrowser.col2active!='' && $(HUB.TagBrowser.col2active)) {
				//var prevactive = $(HUB.TagBrowser.col2active);
				$(HUB.TagBrowser.col2active).removeClass('open');
			}
			var curractive = $(id);
			curractive.addClass('open');
			HUB.TagBrowser.col2active = id;
		} else {
			if(HUB.TagBrowser.col1active!='') {
				var prevactive = $(HUB.TagBrowser.col1active);
				if(prevactive) {
					prevactive.removeClass('open');
				}
			}
			var curractive = $(id);
			curractive.addClass('open');
			HUB.TagBrowser.col1active = id;
		}
		var sortby = '';
		if ($('sortby')) {
			sortby = $('sortby').value;
		}
		var filterby = '';
		var frm = document.getElementById('tagBrowserForm');
		if (frm && frm.filter) {
			for(var i=0; i < frm.filter.length; i++){
				if(frm.filter[i].checked) {
					filterby += '&filter[]='+frm.filter[i].value;
				}
			}
		}
		var termsString = (terms != '')? '&terms='+terms : '';
		var url = HUB.TagBrowser.baseURI+'&type='+type+'&level='+level+'&input='+input+'&input2='+input2+'&id='+rid+'&sortby='+sortby+filterby+termsString;
		if(browser.isFirefox ==false && browser.isCamino==false && browser.isMozilla) {
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
		}).request();
	},
	
	changeSort: function() {
		var type = $('pretype').value;
		var k = $('preinput2').value;
		var p = null;
		$$("#level-1 .open").each(function(el) {
			p = el.id;
		});
		var i = p.replace('col1_','');
		i = (i == 'all') ? '' : i;
		
		HUB.TagBrowser.nextLevel(type, i, k, 2, p, 0);
	},
	
	sc: 0,
	
	setScroll: function() {
		if ($('d')) {
			atg = $('atg').value;
			d = $('d').value;
			if ($("col1_"+atg)) {
				objDiv = $("ultags");
				dist = $("col1_"+atg).offsetHeight;
				objDiv.scrollTop = ((dist * d) - dist);
				clearTimeout(HUB.TagBrowser.sc);
			}
		}
	},

	initialize: function() {
		var browser = new HUB.TagBrowser.detect();
		if ((browser.isMac && browser.isIE)||(browser.isSafari && (browser.isSafari2 == false))) {
			HUB.TagBrowser.incompatible();
		} else {
			var tagbrowser = $('tagbrowser');
			if (tagbrowser) {
				var input = $('preinput').value;
				var input2 = $('preinput2').value;
				var type  = $('pretype').value;
				var id = $('id').value;
				var terms = ($('terms'))? $('terms').value : '';
				var termsString = (terms != '')? '&terms='+terms : '';
				var designator = ($('designator'))? $('designator').value : '';
				var designatorString = (designator != '')? '&designator='+designator : '';

				tagbrowser.style.display = 'block';
				if ($('tbh2')) {
					$('tbh2').style.display = 'block';
				}
				$('viewalltools').style.display = 'none';
			
				if (input != '') {
					HUB.TagBrowser.col2active = 'col1_'+input;
				} else {
					HUB.TagBrowser.col2active = 'col1_all';
				}
				
				//$('level-1').cildNode[0]
				imgpath = '/components/com_resources/images/loading.gif';
				
				var img1 = new Element('img', {'id':'loading-img1','src':imgpath}).injectInside($('level-1-loading'));
				var img2 = new Element('img', {'id':'loading-img2','src':imgpath}).injectInside($('level-2-loading'));
				
				//if ((browser.isFirefox==false && browser.isCamino==false && browser.isMozilla) || browser.isIE) {
					var myAjax = new Ajax(HUB.TagBrowser.baseURI+'&type='+type+'&level=1&input='+input+'&input2='+input2+'&id='+id+termsString+designatorString, {
						method: 'get',
						update: $('level-1'),
						evalScripts: false,
						onSuccess: function() {
							HUB.TagBrowser.sc = setTimeout("HUB.TagBrowser.setScroll()", 500);
						}
					}).request();
				//} else {
				//	var myAjax = new Ajax(HUB.TagBrowser.baseURI+'&type='+type+'&level=1&input='+input+'&id='+id, {method: 'get',update: $('level-1'),evalScripts:true}).request();
				//}
				var myAjax = new Ajax(HUB.TagBrowser.baseURI+'&type='+type+'&level=2&input='+input+'&input2='+input2+'&id='+id+termsString+designatorString, {method: 'get',update: $('level-2')}).request();
			}
		}
	}
}

//----------------------------------------------------------

window.addEvent('domready', HUB.TagBrowser.initialize);
