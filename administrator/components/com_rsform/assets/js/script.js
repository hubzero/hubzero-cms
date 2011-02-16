jQuery(document).ready(function(){ 
	jQuery(function() {
		jQuery('#componentPreview tbody').tableDnD({
			onDragClass: 'rsform_dragged',
			onDrop: function (table, row) {
				tidyOrder(true);
			}
		});
		toggleOrderSpans();
		
		$$('a.rsmodal').each(function(el) {
				el.addEvent('click', function(e) {
					new Event(e).stop();
					window.open(el.href, 'Richtext', 'width=600, height=500');
				});
			});
		
		jQuery(document).click(function() { closeAllDropdowns(); });
	});
});	

function buildXmlHttp()
{
	var xmlHttp;
	try
	{
		xmlHttp=new XMLHttpRequest();
	}
	catch (e)
	{
		try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	return xmlHttp;
}

function tidyOrder(update_php)
{
	if (!update_php)
		update_php = false;
		
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	
	var params = new Array();
	
	var must_update_php = update_php;
	var orders = document.getElementsByName('order[]');
	var cids = document.getElementsByName('cid[]');
	for (i=0; i<orders.length; i++)
	{
		params.push('cid_' + cids[i].value + '=' + parseInt(i + 1));
		
		if (orders[i].value != i + 1)
			must_update_php = true;
		
		orders[i].value = i + 1;
	}
	
	toggleOrderSpans();
	
	if (update_php && must_update_php)
	{
		xml=buildXmlHttp();

		var url = 'index.php?option=com_rsform&task=components.save.ordering&randomTime=' + Math.random();
		xml.open("POST", url, true);
		
		params = params.join('&');
		
		//Send the proper header information along with the request
		xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xml.setRequestHeader("Content-length", params.length);
		xml.setRequestHeader("Connection", "close");

		xml.send(params);
		xml.onreadystatechange=function()
		{
			if(xml.readyState==4)
			{
				formId = document.getElementById('formId').value;
				if (document.getElementById('FormLayoutAutogenerate').checked == true)
					generateLayout(formId, 'no');
					
				document.getElementById('state').innerHTML='Status: ok';
				document.getElementById('state').style.color='';
			}
		}
	}
	else
	{
		document.getElementById('state').innerHTML='Status: ok';
		document.getElementById('state').style.color='';
	}
}

function toggleOrderSpans()
{
	var table = jQuery('#componentPreview tbody tr');
	var k = 0;
	for (i=0; i<table.length; i++)
	{
		jQuery(table[i]).removeClass('row0');
		jQuery(table[i]).removeClass('row1');
		jQuery(table[i]).addClass('row' + k);
		k = 1 - k;
		
		for (var j=0; j<table[i].childNodes.length; j++)
			if (table[i].childNodes[j].innerHTML && (table[i].childNodes[j].innerHTML.indexOf('#reorder') != -1 || table[i].childNodes[j].innerHTML.indexOf('class="jgrid"') != -1))
			{
				var orderRow = table[i].childNodes[j];
				break;
			}
		
		//var orderRow = jQuery(table[i]).children()[6];
		jQuery(orderRow.getElementsByTagName('span')[0]).css('visibility', 'visible');
		jQuery(orderRow.getElementsByTagName('span')[1]).css('visibility', 'visible');
		if (i == 0)
			jQuery(orderRow.getElementsByTagName('span')[0]).css('visibility', 'hidden');
		if (i == table.length - 1)
			jQuery(orderRow.getElementsByTagName('span')[1]).css('visibility', 'hidden');
	}
}

function displayTemplate(componentTypeId,componentId)
{	
	if (document.getElementById('componentEdit'+componentTypeId).innerHTML != '' && (document.getElementById('componentIdToEdit').value == componentId || !componentId))
	{
		jQuery('#componentEdit'+componentTypeId).slideUp('slow', function () {
			document.getElementById('componentEdit'+componentTypeId).innerHTML = '';
		});
		
		return;
	}

	var divs = document.getElementsByTagName('div');
	for(i=0;i<divs.length;i++)
		if(divs[i].title=='componentEdit')
			divs[i].innerHTML='';
	
	document.getElementById('componentEdit'+componentTypeId).innerHTML = '<img id="rsform_loading" src="components/com_rsform/assets/images/loading.gif" alt="" />';
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';

	document.getElementById('componentIdToEdit').value=-1;
	
	xml=buildXmlHttp();
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			document.getElementById('componentEdit'+componentTypeId).innerHTML=xml.responseText;			
			jQuery('#componentEdit'+componentTypeId).hide();
			jQuery('#componentEdit'+componentTypeId).slideDown('slow');
			
			try {
				var top = f_scrollTop();
				if (top > 200)
					jQuery.scrollTo(jQuery('#componentEdit'+componentTypeId), 100);
			}
			catch (err) {
				// do nothing
			}
			
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
			changeValidation($('VALIDATIONRULE'));
			
			// calendar validation
			if (componentTypeId == 6)
			{
				jQuery('#MINDATE').bind('keyup', function() { this.value = rsfp_validateDate(this.value); });
				jQuery('#MAXDATE').bind('keyup', function() { this.value = rsfp_validateDate(this.value); });
				
				Calendar.setup({
					inputField     :    "MINDATE",     // id of the input field
					ifFormat       :    "%m/%d/%Y",      // format of the input field
					button         :    "MINDATE",  // trigger for the calendar (button ID)
					align          :    "Tl",           // alignment (defaults to "Bl")
					singleClick    :    true
				});
				Calendar.setup({
					inputField     :    "MAXDATE",     // id of the input field
					ifFormat       :    "%m/%d/%Y",      // format of the input field
					button         :    "MAXDATE",  // trigger for the calendar (button ID)
					align          :    "Tl",           // alignment (defaults to "Bl")
					singleClick    :    true
				});
			}
		}
    }
	
	if (componentId)
	{
		document.getElementById('componentIdToEdit').value=componentId;
		xml.open('GET','index.php?option=com_rsform&task=components.display&componentType=' + componentTypeId + '&componentId=' + componentId + '&format=raw&randomTime=' + Math.random(), true);
	}
	else
		xml.open('GET','index.php?option=com_rsform&task=components.display&componentType='+componentTypeId+'&format=raw&randomTime='+Math.random(), true);
		
	xml.send(null);
}

function rsfp_validateDate(value)
{
	value = value.replace(/[^0-9\/]/g, '');	
	return value;
}

function f_scrollTop() {
	return f_filterResults (
		window.pageYOffset ? window.pageYOffset : 0,
		document.documentElement ? document.documentElement.scrollTop : 0,
		document.body ? document.body.scrollTop : 0
	);
}
function f_filterResults(n_win, n_docel, n_body) {
	var n_result = n_win ? n_win : 0;
	if (n_docel && (!n_result || (n_result > n_docel)))
		n_result = n_docel;
	return n_body && (!n_result || (n_result > n_body)) ? n_body : n_result;
}

function removeComponent(formId,componentId)
{
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	xml=buildXmlHttp();
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			var table=document.getElementById('componentPreview');
			var rows=document.getElementsByName('previewComponentId');
			for(i=0;i<rows.length;i++)
				if(rows[i].value==componentId)
					table.deleteRow(i);
			
			if (xml.responseText.indexOf('NOSUBMIT') != -1)
				document.getElementById('rsform_submit_button_msg').style.display = '';
			
			tidyOrder(true);
			
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
    }
	xml.open('GET','index.php?option=com_rsform&task=components.remove&ajax=1&cid[]='+componentId+'&formId='+formId+'&randomTime='+Math.random(),true);
	xml.send(null);
}

function processComponent(componentType)
{
	for (var i=0; i<document.getElementsByName('componentSaveButton').length; i++)
		document.getElementsByName('componentSaveButton')[i].disabled = true;
	
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	
	xml=buildXmlHttp();

	var url = 'index.php?option=com_rsform&task=components.validate.name&randomTime=' + Math.random();
	xml.open("POST", url, true);
	params  = 'componentName=' + escape(document.getElementById('NAME').value);
	params += '&formId=' + document.getElementById('formId').value;
	params += '&currentComponentId=' + document.getElementById('componentIdToEdit').value;
	params += '&componentType=' + componentType;
	
	if (componentType == 9)
		params += '&destination=' + escape(document.getElementById('DESTINATION').value);
	if (componentType == 6)
	{
		params += '&mindate=' + escape(document.getElementById('MINDATE').value);
		params += '&maxdate=' + escape(document.getElementById('MAXDATE').value);
	}
	
	//Send the proper header information along with the request
	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");

	xml.send(params);
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			if(xml.responseText.indexOf('Ok') == -1)
			{
				alert(xml.responseText);
				document.getElementById('state').innerHTML='Status: ok';
				document.getElementById('state').style.color='';
				
				for (var i=0; i<document.getElementsByName('componentSaveButton').length; i++)
					document.getElementsByName('componentSaveButton')[i].disabled = false;
			}
			else
				submitbutton('components.save');
		}
    }
}

function changeFormAutoGenerateLayout(formId)
{
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	var layouts=document.getElementsByName('FormLayoutName');
	var layoutName='';
	for(i=0;i<layouts.length;i++)
		if(layouts[i].checked)
			layoutName=layouts[i].value

	xml=buildXmlHttp();
	xml.onreadystatechange=function()
	{
		if(xml.readyState==4)
		{
			if(document.getElementById('FormLayoutAutogenerate').checked==true)
			{
				document.getElementById('rsform_layout_msg').style.display = 'none';
				document.getElementById('formLayout').readOnly=true;
			}
			else
			{
				document.getElementById('rsform_layout_msg').style.display = '';
				document.getElementById('formLayout').readOnly=false;
			}

			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
	}
	xml.open('GET','index.php?option=com_rsform&task=forms.changeAutoGenerateLayout&formId='+formId+'&randomTime='+Math.random()+'&formLayoutName='+layoutName,true);
	xml.send(null);
}

function generateLayout(formId,alert)
{
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	if(alert!='no')
	{
		var answer=confirm("Pressing the 'Generate layout' button will ERASE your current layout. Are you sure you want to continue?");
		if(answer==false) return;
	}
	var layoutName = 'inline';
	for (var i = 0; i<document.getElementsByName('FormLayoutName').length; i++)
		if (document.getElementsByName('FormLayoutName')[i].checked)
			layoutName = document.getElementsByName('FormLayoutName')[i].value;

	xml=buildXmlHttp();
	xml.onreadystatechange=function()
	{
		if(xml.readyState==4)
		{
			document.getElementById('formLayout').value=xml.responseText;
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
	}
	xml.open('GET','index.php?option=com_rsform&task=layouts.generate&layoutName='+layoutName+'&formId='+formId+'&randomTime='+Math.random(),true);
	xml.send(null);
}

function saveLayoutName(formId,layoutName)
{
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	xml=buildXmlHttp();
	xml.open('GET','index.php?option=com_rsform&task=layouts.save.name&formId='+formId+'&randomTime='+Math.random()+'&formLayoutName='+layoutName,true);
	xml.send(null);
	xml.onreadystatechange=function()
	{
		if(xml.readyState==4)
		{
			if(document.getElementById('FormLayoutAutogenerate').checked==true)
				generateLayout(formId, 'no');
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
	}
	
	
}
 
function refreshCaptcha(componentId, captchaPath)
{
	if(!captchaPath) captchaPath = 'index.php?option=com_rsform&task=captcha&componentId=' + componentId;
	document.getElementById('captcha' + componentId).src = captchaPath + '&' + Math.random();
	document.getElementById('captchaTxt' + componentId).value='';
	document.getElementById('captchaTxt' + componentId).focus();
}

function isset(varname)  {
  if(typeof( window[ varname ] ) != "undefined") return true;
  else return false;
}




//MAPPINGS//

function updateColumns()
{
	var currentTable=document.getElementById('rsform_mapping_table').value;
	
	xmlHttp = buildXmlHttp();	
	xmlHttp.onreadystatechange=function()
	{
		if(xmlHttp.readyState==4)
		{
			document.getElementById('rsform_html_mapping_column').innerHTML=xmlHttp.responseText;
		}
	}
	
	xmlHttp.open("GET","index.php?option=com_rsform&task=plugin&plugin_task=mappings.getColumns&tableName="+currentTable,true);
	xmlHttp.send(null);
}

function saveMapping(formId)
{
	var currentTable=document.getElementById('rsform_mapping_table').value;
	var currentColumn=document.getElementById('rsform_mapping_column').value;
	var currentComponent=document.getElementById('rsform_mapping_component').value;
	
	xmlHttp = buildXmlHttp();
	xmlHttp.onreadystatechange=function()
	{
		if(xmlHttp.readyState==4)
		{
			if(xmlHttp.responseText=='1')
			{
				alert("You can't add the same mapping twice");
				return;
			}
			document.getElementById('rsform_html_mappings_table').innerHTML=xmlHttp.responseText;
		}
	}
	
	xmlHttp.open("GET","index.php?option=com_rsform&task=plugin&plugin_task=mappings.saveMapping&ComponentId="+currentComponent+"&MappingTable="+currentTable+"&MappingColumn="+currentColumn+"&FormId="+formId,true);
	xmlHttp.send(null);
	
}
function deleteMapping(mappingId,formId)
{
	xmlHttp = buildXmlHttp();
	xmlHttp.onreadystatechange=function()
	{
		if(xmlHttp.readyState==4)
		{
			if(xmlHttp.responseText=='1')
			{
				alert("You can't add the same mapping twice");
				return;
			}
			document.getElementById('rsform_html_mappings_table').innerHTML=xmlHttp.responseText;
		}
	}
	xmlHttp.open("GET","index.php?option=com_rsform&task=plugin&plugin_task=mappings.deleteMapping&MappingId="+mappingId+"&FormId="+formId,true);
	xmlHttp.send(null);
}

function exportProcess(start, limit, total)
{
	xml=buildXmlHttp();
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			post = xml.responseText;
			if(post.indexOf('END') != -1)
			{
				document.getElementById('progressBar').style.width = document.getElementById('progressBar').innerHTML = '100%';
				document.location = 'index.php?option=com_rsform&task=submissions.export.file&ExportFile=' + document.getElementById('ExportFile').value + '&ExportType=' + document.getElementById('exportType').value;
			}
			else
			{
				document.getElementById('progressBar').style.width = Math.ceil(start*100/total) + '%';
				document.getElementById('progressBar').innerHTML = Math.ceil(start*100/total) + '%';
				start = start + limit;
				exportProcess(start, limit, total);
			}
		}
    }
		
	xml.open('GET','index.php?option=com_rsform&task=submissions.export.process&exportStart=' + start + '&exportLimit=' + limit + '&randomTime=' + Math.random(),true);
	xml.send(null);
}

function number_format(number, decimals, dec_point, thousands_sep)
{
    var n = number, prec = decimals;
    n = !isFinite(+n) ? 0 : +n;
    prec = !isFinite(+prec) ? 0 : Math.abs(prec);
    var sep = (typeof thousands_sep == "undefined") ? ',' : thousands_sep;
    var dec = (typeof dec_point == "undefined") ? '.' : dec_point;
 
    var s = (prec > 0) ? n.toFixed(prec) : Math.round(n).toFixed(prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;
 
    var abs = Math.abs(n).toFixed(prec);
    var _, i;
 
    if (abs >= 1000) {
        _ = abs.split(/\D/);
        i = _[0].length % 3 || 3;
 
        _[0] = s.slice(0,i + (n < 0)) +
              _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
 
        s = _.join(dec);
    } else {
        s = s.replace('.', dec);
    }
 
    return s;
}

function changeValidation(elem)
{
	if (elem == null) return;
	if(elem.id == 'VALIDATIONRULE')
	{
		if (document.getElementById('idVALIDATIONEXTRA'))
		{
			if(elem.value == 'custom' || elem.value == 'numeric' || elem.value == 'alphanumeric' || elem.value == 'alpha' )
				document.getElementById('idVALIDATIONEXTRA').className='showVALIDATIONEXTRA';
			else
				document.getElementById('idVALIDATIONEXTRA').className='hideVALIDATIONEXTRA';
		}
	}
}

function submissionChangeForm(formId)
{
	document.location = 'index.php?option=com_rsform&task=submissions.manage&formId=' + formId;
}

function toggleCustomizeColumns()
{
	var el = jQuery('#columnsDiv');
	
	if (el.is(':hidden'))
		el.slideDown('fast');
	else
		el.slideUp('fast');
}

function closeAllDropdowns(except)
{
	var dropdowns = jQuery('.dropdownContainer');
	var except 	  = jQuery('#dropdown' + except);
	
	for (var i=0; i<dropdowns.length; i++)
	{
		var dropdown = jQuery(dropdowns[i]).children('div');
		if (dropdown.attr('id') != except.attr('id'))
			jQuery(dropdowns[i]).children('div').hide();
	}
}

function toggleDropdown(what)
{
	var name		= what.name;
	closeAllDropdowns(name);
	var parent		= jQuery('#' + name).parent();
	var quickfields = returnQuickFields();
	
	if (jQuery('#dropdown' + name).length == 0)
	{
		var divContainer = document.createElement('div');
		jQuery(divContainer).click(function(e) { e.stopPropagation(); e.preventDefault(); });
		divContainer.className = 'dropdownContainer';
		
		var divDropdown = document.createElement('div');
		divDropdown.id = 'dropdown' + name;
		divDropdown.setAttribute('id', 'dropdown' + name);
		divContainer.appendChild(divDropdown);
		
		for (var i=0; i<quickfields.length; i++)
		{
			var a = document.createElement('a');
			a.innerHTML = '{' + quickfields[i] + ':value}';
			a.href = 'javascript: void(0);';
			a.onclick = function() { dropdownClick(name, this); };
			
			divDropdown.appendChild(a);
		}
		parent.append(divContainer);
	}
	
	var dropdown = jQuery('#dropdown' + name);
	
	if (dropdown.is(':hidden'))
		dropdown.slideDown('fast');
	else
		dropdown.slideUp('fast');
}

function dropdownClick(what, a)
{
	var input 	 = jQuery('#' + what);
	var dropdown = jQuery('#dropdown' + what);
	var value    = jQuery(a).html();
	
	if (input.val().replace(/^\s+|\s+$/g,'').length > 0)
	{
		input.val(input.val().replace(/^\s+|\s+$/g,''));
		if (input.val().substring(input.val().length - 1) != ',' && (what != 'AdminEmailFromName' && what != 'UserEmailFromName' && what != 'AdminEmailSubject' && what != 'UserEmailSubject'))
			value = input.val() + ',' + value;
		else
			value = input.val() + ' ' + value;
	}
		
	input.val(value);
	
	dropdown.slideUp('fast');
}

function toggleQuickAdd()
{
	var what = 'none';
	if (document.getElementById('QuickAdd1').style.display == 'none')
		what = '';
	
	document.getElementById('QuickAdd1').style.display = what;
	document.getElementById('QuickAdd2').style.display = what;
	document.getElementById('QuickAdd3').style.display = what;
	document.getElementById('QuickAdd4').style.display = what;
}