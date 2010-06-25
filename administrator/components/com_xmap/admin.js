/**
 * $Id: admin.js 82 2008-02-03 23:14:53Z root $
 * $LastChangedDate: 2008-02-03 17:14:53 -0600 (dom, 03 feb 2008) $
 * $LastChangedBy: root $
 * Xmap by Guillermo Vargas
 * a sitemap component for Joomla! CMS (http://www.joomla.org)
 * Author Website: http://joomla.vargas.co.cr
 * Project License: GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
var divloading=null;
var divOptions = null;
var currSitemapMenu = null;
var menudelay=null;

function menu_listItemTask( id, task, option ) {
	var f = document.frmSettings;
	cb = eval( 'f.' + id );
	if (cb) {
		cb.checked = true;
		submitbutton(task);
	}
	return false;
}

function changeDisplayImage(sitemap) {
	if (document.frmSettings.ext_image.value !='') {
		document.frmSettings.imagelib.src='../components/com_xmap/images/' + document.frmSettings.ext_image.value;
	} else {
		document.frmSettings.imagelib.src='../images/blank.png';
	}
}

function addExclude(sitemap) {
	var exclude = document.frmSettings.exclmenus.value.split(',');
	exclude.push( document.frmSettings.excl_menus.value );
	//remove duplicates;
	var tmp = new Object();
	for(var i = 0; i < exclude.length; i++) {
		var id = parseInt(exclude[i]);
		if( isNaN(id))
			continue;

		tmp[ id ] = id;
	}
	exclude = new Array();
	for(var k in tmp) {
		exclude.push( tmp[k] );
	}
	document.frmSettings.exclmenus.value = exclude.join(',');
}

function addSitemap() {
	showLoading();
	var myAjax = new Ajax(
	ajaxURL +'&action=add_sitemap',
	{
		method: 'get', 
		onComplete: showSitemap
	}).request();
}

function saveProperty(sitemapid,property,value) {
	showLoading();
	var myAjax = new Ajax(
	ajaxURL,
	{
		method: 'post', 
		postBody: 'option=com_xmap&task=ajax_request&action=save_property&sitemap='+sitemapid+'&property='+property+'&value='+escape(value), 
		onComplete: checkResultSave.bindAsEventListener(this,[sitemapid])
	}).request();
}

function setAsDefault() {
	showLoading();
	sitemapid=currSitemapMenu;
	var myAjax = new Ajax(
	ajaxURL,
	{
		method: 'post', 
		postBody: 'option=com_xmap&task=ajax_request&action=set_default&sitemap='+sitemapid, 
		onComplete: checkResultSetDefault.bindAsEventListener(this,[sitemapid])
	}).request();
}

function changePluginState(pluginid) {
	showLoading();
	sitemapid=currSitemapMenu;
	var myAjax = new Ajax(
	ajaxURL,
	{
		method: 'post', 
		postBody: 'option=com_xmap&task=ajax_request&action=change_plugin_state&plugin='+pluginid, 
		onComplete: checkResultChangePluginState.bindAsEventListener(this,[pluginid])
	}).request();
}

function checkResultChangePluginState (ajaxResponse,pluginid) {
	hideLoading();
	var pluginimg = $('pluginstate'+pluginid);
	var plugin = $('plugin'+pluginid);
	if ( ajaxResponse == '1' ) {
		pluginimg.src = 'images/publish_g.png';
		plugin.removeClass('unpublished');
		plugin.addClass('published');
		pluginimg.title='Published';
	} else if ( ajaxResponse == '0' ) {
		pluginimg.src = 'images/publish_x.png';
		plugin.removeClass('published');
		plugin.addClass('unpublished');
		pluginimg.title='Unpublished';
	} else {
		alert(ajaxResponse);
	}
}

function deleteSitemap() {
	if (confirm(deleteSitemapConfirmMessage)) {
		showLoading();
		sitemapid=currSitemapMenu;
		var myAjax = new Ajax(
		ajaxURL,
		{
			method: 'post', 
			postBody: 'option=com_xmap&task=ajax_request&action=delete_sitemap&sitemap='+sitemapid, 
			onComplete: checkResultDelete.bindAsEventListener(this,[sitemapid])
		}).request();
	}
}

function uninstallPlugin(pluginid) {
	if (confirm(unistallPluginConfirmMessage)) {
		showLoading();
		var myAjax = new Ajax(
		ajaxURL,
		{
			method: 'post', 
			postBody: 'option=com_xmap&task=ajax_request&action=uninstallplugin&plugin='+pluginid, 
			onComplete: checkResultUninstallPlugin.bindAsEventListener(this,[pluginid])
		}).request();
	}
}

function clearCacheSitemap() {
	showLoading();
	sitemapid=currSitemapMenu;
	var myAjax = new Ajax(
	ajaxURL,
	{
		method: 'post', 
		postBody: 'option=com_xmap&task=ajax_request&action=clean_cache_sitemap&sitemap='+sitemapid, 
		onComplete: function (ajaxResponse) {hideLoading();alert(ajaxResponse);}
	}).request();
}

function copySitemap() {
	showLoading();
	sitemapid=currSitemapMenu;
	var myAjax = new Ajax(
	ajaxURL,
	{
		method: 'post', 
		postBody: 'option=com_xmap&task=ajax_request&action=copy_sitemap&sitemap='+sitemapid, 
		onComplete: showSitemap
	}).request();
}
function settingsSitemap() {
	showLoading();
	sitemapid=currSitemapMenu;
	var container = $('sitemapsettings');
	var outer = $('divbg');
	if (!container) {
		container = new Element ('div',{
			'id':'sitemapsettings',
			'name':'sitemapsettings',
			'class':'settings settingssitemap'
		});
		outer.setOpacity(0.6);
		outer.appendChild (container);
		var body = document.getElementsByTagName("body").item(0);
		body.appendChild(container);
	}
	container.setHTML(loadingMessage);
	outer.setStyle('display','');
	centerElement(outer,true);
	container.setStyle('display','');
	centerElement(container);

	var myAjax = new Ajax(
	ajaxURL,
	{
		method: 'post', 
		postBody: 'option=com_xmap&task=ajax_request&action=edit_sitemap_settings&sitemap='+sitemapid, 
		onComplete: showSitemapSettings
	}).request();
}
function settingsPlugin(pluginid) {
	showLoading();
	sitemapid=currSitemapMenu;
	var container = $('pluginsettings');
	var outer = $('divbg');
	if (!container) {
		container = new Element ('div',{
			'id':'pluginsettings',
			'name':'pluginsettings',
			'class':'settings settingsplugin'
		});
		outer.setOpacity(0.6);
		outer.appendChild (container);
		var body = document.getElementsByTagName("body").item(0);
		body.appendChild(container);
	}
	container.setHTML(loadingMessage);
	outer.setStyle('display','');
	centerElement(outer,true);
	container.setStyle('display','');
	centerElement(container);

	var myAjax = new Ajax(
	ajaxURL,
	{
		method: 'post', 
		postBody: 'option=com_xmap&task=ajax_request&action=edit_plugin_settings&plugin='+pluginid, 
		onComplete: showPluginSettings
	}).request();
}

function optionsMenuSettings(sitemapid,menutype,menu) {
	showLoading();
	closeMenu(menu);
	var container = $('menuoptions');
	var outer = $('divbg');
	if (!container) {
		container = new Element ('div',{
			'id':'menuoptions',
			'name':'menuoptions',
			'class':'settings settingsmenu'
		});
		outer.setOpacity(0.6);
		outer.appendChild (container);
		var body = document.getElementsByTagName("body").item(0);
		body.appendChild(container);
	}
	container.setHTML(loadingMessage);
	outer.setStyle('display','');
	centerElement(outer,true);
	container.setStyle('display','');
	centerElement(container);

	var myAjax = new Ajax(
		ajaxURL,
		{
			method: 'post', 
			postBody: 'option=com_xmap&task=ajax_request&action=menu_options&sitemap='+sitemapid+'&menutype='+menutype, 
			onComplete: showOptionsMenuSettings
		}).request();
}

function saveSettings(id,action,container) {
	showLoading();
	var theForm = $('frmSettings'+id);
	var postVars = 'option=com_xmap&task=ajax_request&action='+action+'&' + theForm.toQueryString();
	var myAjax = new Ajax(
		ajaxURL,
		{
			method: 'post',
			postBody: postVars,
			onComplete: checkResultSaveSettings.bindAsEventListener(this,[id,container])
		}).request();
}

function saveMenuOptions() {
	showLoading();
	var theForm = $('frmMenuOptions');
	var postVars = 'option=com_xmap&task=ajax_request&action=save_menu_options&' + theForm.toQueryString();
	var myAjax = new Ajax(
		ajaxURL,
		{
			method: 'post',
			postBody: postVars,
			onComplete: checkResultSaveMenuOptions
		}).request();
}

function checkResultDelete(ajaxResponse,sitemapid) {
	hideLoading();
	if (ajaxResponse != '1') {
		alert(ajaxResponse);
	}else{
		var sitemap = $('sitemap'+sitemapid);
		sitemap.remove();
	}
}

function checkResultUninstallPlugin(ajaxResponse,pluginid) {
	hideLoading();
	if (ajaxResponse != '1') {
		alert(ajaxResponse);
	}else{
		var sitemap = $('plugin'+pluginid);
		sitemap.remove();
	}
}

function checkResultSetDefault(ajaxResponse,sitemapid) {
	hideLoading();
	var img;
	if (ajaxResponse != '1') {
		alert(ajaxResponse);
	}else{
		if (sitemapdefault ) {
			img = $('imgdefault'+sitemapdefault);
			if (img) 
				img.src=mosConfigLiveSite+ '/administrator/components/com_xmap/images/no_default.gif';
		}
		sitemapdefault = sitemapid;
		var img = $('imgdefault'+sitemapid);
		img.src =mosConfigLiveSite+'/administrator/components/com_xmap/images/default.gif';
	}
}
function checkResultSaveSettings(ajaxResponse,sitemapid,container) {
	hideLoading();
	if (ajaxResponse != '1') {
		alert(ajaxResponse);
	}else{
		closeSettings(container);
	}
}
function checkResultSaveMenuOptions(ajaxResponse) {
	hideLoading();
	if (ajaxResponse != '1') {
		alert(ajaxResponse);
	}else{
		closeSettings('menuoptions');
	}
}


function checkResultSave(ajaxResponse,sitemapid) {
	hideLoading();
	if (ajaxResponse != '1') {
		alert(ajaxResponse);
	}
}

function showSitemap(ajaxResponse) {
	hideLoading();
	var container = $('sitemaps');
	var sitemap = new Element ('div');
	sitemap.setHTML(ajaxResponse);
	container.appendChild(sitemap);
}

function showSitemapSettings(ajaxResponse) {
	hideLoading();
	var container = $('sitemapsettings');
	var outer = $('divbg');
	window.onscroll=function(outer,container) {centerElement(outer,true);centerElement(container);}.pass([outer,container]);
	container.setHTML(ajaxResponse);
	centerElement(outer,true);
	centerElement(container);
	outer.setStyle('display','');
	container.setStyle('display','');
	var SettTooltips = new Tips($$('#sitemapsettings .hasTip'), { maxTitleChars: 50, fixed: false});
}

function showPluginSettings(ajaxResponse) {
	hideLoading();
	var container = $('pluginsettings');
	var outer = $('divbg');
	window.onscroll=function(outer,container) {centerElement(outer,true);centerElement(container);}.pass([outer,container]);
	container.setHTML(ajaxResponse);
	centerElement(outer,true);
	centerElement(container);
	outer.setStyle('display','');
	container.setStyle('display','');
	var SettTooltips = new Tips($$('#pluginsettings .hasTip'), { maxTitleChars: 50, fixed: false});
}

function showOptionsMenuSettings(ajaxResponse) {
	hideLoading();
	var container = $('menuoptions');
	var outer = $('divbg');
	window.onscroll=function(outer,container) {centerElement(outer,true);centerElement(container);}.pass([outer,container]);
	container.setHTML(ajaxResponse);
	centerElement(outer,true);
	centerElement(container);
	outer.setStyle('display','');
	container.setStyle('display','');
	var SettTooltips = new Tips($$('#menuoptions .hasTip'), { maxTitleChars: 50, fixed: false});
}

function closeSettings(name) {
	window.onscroll=null;
	var outer = $('divbg');
	var container = $(name);
	outer.setStyle('display','none');
	container.setStyle('display','none');
}

function editTextField(elm,id,prop) {
	if ( !elm.editing) {
		elm.editing = true;
		elm.oldInnerHTML = elm.innerHTML;
		var input = document.createElement('INPUT');
		input.type='text';
		input.name=prop;
		input.id=prop+id;
		input.value=elm.innerHTML;
		input.size=30;
		input.onblur=updateSitemapProperty.pass([elm,input,id]);
		input.onkeyup=checkKey.bindAsEventListener(this,[elm,input,id]);
		Element.setHTML(elm,'');
		elm.appendChild(input);
	}
}
function checkKey (e,elm,input,id){
	if(!e) { e=window.event; }
	if(e.keyCode == 13){
		updateSitemapProperty(elm,input,id,e);
		return false;
	}
}
function updateSitemapProperty(elm,input,id,event) {
	if (elm.oldInnerHTML != input.value) {
		saveProperty(id,input.name,input.value);
	} 
	var value = input.value;
	Element.remove(input);
	elm.editing=false;
	elm.innerHTML = value;
}
function optionsMenu(id) {
	currSitemapMenu=id;
	if (!divOptions) {
		divOptions = $('optionsmenu');
		divOptions.setStyle('display','');
		divOptions.set({'events':{'click': closeOptionsMenu}});
	}
	if (divOptions.sitemapid && divOptions.sitemapid != id) {
		divOptions.setStyle('visibility','hidden');
		
	}
	divOptions.sitemapid=id;
	var elm = $('optionsbut'+id);
	var pos = elm.getPosition();
	var dim1 = elm.getSize();
	var dim2 = divOptions.getSize();
	divOptions.setStyle('left',(pos.x+dim1.size.x-dim2.size.x) + 'px');
	divOptions.setStyle('top',(pos.y + dim1.size.y + 2) + 'px');

	divOptions.clicks=0;
	if (divOptions.style.visibility == 'visible') {
		divOptions.setStyle('visibility','hidden');
	} else {
		divOptions.setStyle('visibility','visible');
	}
}

function handleClick() {
	if ( divOptions && divOptions.clicks >= 1 ) {
		closeOptionsMenu();
	} else if ( divOptions ) {
		divOptions.clicks++;
	}
}
function closeOptionsMenu() {
	divOptions.setStyle('visibility','hidden');
}
function showMenuOptions(elmid,menuname,sitemap) {
	var menu = $('divoptions');
	var elm = $(elmid);
	if (menu.blocked)
		return false;
	if (menu.parent && menu.parent != elm) {
		closeMenu(menu);
	}
	clearDelay();
	menu.blocked=true;
	menu.setHTML('');
	menu.appendChild( new Element('div',{
		'events': {
			'click':moveMenuPosition.pass([sitemap,menuname,'-1',menu]),
			'mouseover': clearDelay
		},
		'class': 'menuoption'
	}).setHTML(moveUMenuMessage));
	menu.appendChild( new Element('div',{
                'events': {
                        'click':moveMenuPosition.pass([sitemap,menuname,'1',menu]),
			'mouseover': clearDelay
                },
                'class': 'menuoption'
        }).setHTML(moveDMenuMessage));
	menu.appendChild( new Element('div',{
                'events': {
                        'click':removeMenuFromSitemap.pass([sitemap,menuname,menu]),
			'mouseover': clearDelay
                },
                'class': 'menuoption'
        }).setHTML(deleteMenuMessage));
	menu.appendChild( new Element('div',{
                'events': {
                        'click':optionsMenuSettings.pass([sitemap,menuname,menu]),
			'mouseover': clearDelay
                },
                'class': 'menuoption'
        }).setHTML(editMenuOptionsMessage));
	menu.setStyle('display','');
	var pos = elm.getPosition();
	var dim1 = elm.getSize(elm);
	var dim2 = menu.getSize();
	elm.addClass('menuoptionhover');
	menu.set({
		'events': {
			'mouseover': clearDelay
		},
        	'styles':{
        		'visibility':'visible',
        		'z-index':'10',
			'left':(pos.x+dim1.size.x-dim2.size.x) + 'px',
			'top':(pos.y + dim1.size.y) + 'px'
        	}
	});
	elm.menu=menu;
	menu.parent=elm;
}
function showMenusList (sitemap,elmid) {
	var menulist = $('menulistdropdown');
	if  (!menulist) {
		var menulist = new Element ('div',{
			'class':'menulistdrop',
			'id':'menulistdropdown'
		});
		var theform = new Element('form',{
			'name':'frmmenulist',
			'id':'frmmenulist'
		});
		theform.appendChild(menulist);
		var body = document.getElementsByTagName("body").item(0);
		body.appendChild(theform);
	} else {
		if ((menulist.currSitemap && menulist.currSitemap == sitemap) || elmid==null) {
			menulist.currSitemap = '';
			menulist.setStyle('display','none');
			return true;
		}
		menulist.setStyle('display','none');
		menulist.setHTML('');
	}
	var elm = $(elmid);
	menulist.currSitemap = sitemap;
	var menu;
	var checkbox;
	for(i=0;i<menus.length;i++){
	 	var menu = $(menus[i]+sitemap);
		if (!menu) {
			menu=document.createElement('DIV');
			checkbox=document.createElement('INPUT');
			checkbox.setAttribute('type','checkbox');
			checkbox.setAttribute('name','menus[]');
			checkbox.setAttribute('id','menu_'+sitemap+menus[i]);
			checkbox.setAttribute('value',menus[i]);
			menu.appendChild(checkbox);
			var label = document.createElement('label');
			label.setAttribute('for','menu_'+sitemap+menus[i]);
			label.appendChild(document.createTextNode(menus[i]));
			menu.appendChild(label);
			menulist.appendChild(menu);
		}
	}
	menu=document.createElement('DIV');
	var option= new Element ('div',{
		'events': {'click':addMenusToSitemap.pass([sitemap])},
		'class':'menulistoption'
	});
	option.appendChild(document.createTextNode(addMessage));
	menu.appendChild(option);
	var option= new Element ('div',{
		'events': {'click':showMenusList.pass([sitemap,elm.id])},
		'class':'menulistoption'
	});
	option.appendChild(document.createTextNode(cancelMessage));
	menu.appendChild(option);
	menulist.appendChild(menu);
	//var pos = Position.cumulativeOffset(elm);
	var pos = elm.getPosition();
	var dim = elm.getSize();
	menulist.set({'styles':{left: pos.x + 'px',top: (pos.y + dim.size.y) + 'px'}});
	menulist.setStyle('display','');
}
function addMenusToSitemap (sitemap) {
	//Close the menu
	showMenusList(sitemap,null);
	showLoading();
	var theform = $('frmmenulist');
	var postVars= 'option=com_xmap&task=ajax_request&action=add_menu_sitemap&sitemap='+sitemap+'&'+theform.toQueryString();
				var myAjax = new Ajax(
	ajaxURL,
				{
		method: 'post',
		postBody: postVars,
		update: 'menulist'+sitemap,
		onComplete: hideLoading
	}).request();
}
function removeMenuFromSitemap (sitemap,menuname,menu) {
	showLoading();
	closeMenu(menu);
	var postVars= 'option=com_xmap&task=ajax_request&action=remove_menu_sitemap&sitemap='+sitemap+'&menu='+menuname;
	var myAjax = new Ajax(
	ajaxURL,
				{
		method: 'post',
		update:'menulist'+sitemap,
		postBody: postVars,
		onComplete: hideLoading
	}).request();
}
function moveMenuPosition (sitemap,menuname,move,menu) {
	//Close the menu
	closeMenu(menu);
	showLoading();
	var postVars= 'option=com_xmap&task=ajax_request&action=move_menu_sitemap&sitemap='+sitemap+'&menu='+menuname+'&move='+move;
	var myAjax = new Ajax(
	ajaxURL,
				{
		method: 'post',
		update: 'menulist'+sitemap,
		postBody: postVars,
		onComplete: function () {hideLoading(); closeMenu()}
	}).request();
}
function refreshMenuList (sitemap) {
	showLoading();
	var myAjax = new Ajax(
				ajaxURL+ 'action=get_menus_sitemap&sitemap='+sitemap,
				{
						method: 'get',
						update: 'menulist'+sitemap,
						onComplete: function () {hideLoading(); closeMenu()}
				}).request();
}
function hideOptions (menu) {
	if (menu) {
		menu.blocked=false;
		menudelay = window.setTimeout(closeMenu.pass([menu]),300);
	}
}
function closeMenu (menu) {
	if (!menu) {
		menu = $('divoptions');
	}
	menu.blocked=false;
	if (menu.parent && menu.parent.removeClass) {
		menu.parent.removeClass('menuoptionhover');
	}
	menu.parent=null;
	menu.setStyle('display','none');
	clearDelay();
}
function clearDelay(){
	if (menudelay){
		window.clearTimeout(menudelay);
	}
}
function centerElement(elm,resize) {
	var x,y,w,h;
	if (self.innerHeight) { // all except Explorer
		x = (self.innerWidth/2) + self.pageXOffset;
		y = (self.innerHeight/2) + self.pageYOffset;
		w = self.innerWidth;
		h = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		x = (document.documentElement.clientWidth/2) + document.documentElement.scrollLeft;
		y = (document.documentElement.clientHeight/2) + document.documentElement.scrollTop;
		w = document.body.clientWidth;
		h = document.body.clientHeight;
	} else if (document.body) { // other Explorers
		x = (document.body.clientWidth/2) + document.body.scrollLeft;
		y = (document.body.clientHeight/2) + document.body.scrollTop;
		w = document.body.clientWidth;
		h = document.body.clientHeight;
	}
	if (resize) {
		elm.setStyle('width',w);
		elm.setStyle('height',h);
	}
	dim = elm.getSize();
	elm.setStyle('left',Math.round(x - (dim.size.x/2)) + 'px');
	elm.setStyle('top',Math.round(y- (dim.size.y/2))+ 'px');
}
function showLoading() {
	if (!divloading) {
		divloading = $('divloading');
	}
	loadingPosition(divloading);
	var body = document.getElementsByTagName("body").item(0);
	window.onscroll=loadingPosition.pass([divloading]);
	divloading.setStyle('display','');
}
function hideLoading() {
	if (divloading) {
		divloading.setStyle('display','none');
		var body = document.getElementsByTagName("body").item(0);
		body.onscroll=null;
	}
}
function loadingPosition(div) {
	if (self.innerHeight) { // all except Explorer
		y = self.pageYOffset;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		y = document.documentElement.scrollTop;
	} else if (document.body) { // other Explorers
		y =  document.body.scrollTop;
	} else {
		return false;
	}
	Element.set(div,{'styles':{top: (y+3)+'px'}});
}
