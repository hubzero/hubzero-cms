/**
*
* PickList Tool
* Written by Jason Lambert
*/

//----------------------------------------------------------
// Establish the namespace if it doesn't exist
//----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

function togglePickList(classname, result)
{
	if (result == 'fail')
		return;
		
		
	var sclassname = '.' + 'ritem' + classname;
	var btn = document.getElementById('pickbtn' + classname);
	var div = '.' + 'rdivitem' + classname;
	
	if (result == 'remove') {
		$jQ( formName  ).find( ".resourcepicklisticon" ).filter(sclassname).hide('fast');
		btn.value = "Add to Pick List";
		$jQ( formName  ).find(div).removeClass("inpicklist");
		$jQ( formName  ).find(div).addClass("outpicklist");
		}
	else if (result ==  'add') {
		$jQ( formName  ).find( ".resourcepicklisticon" ).filter(sclassname).show('fast');
		btn.value = "Remove from Pick List";
		$jQ( formName  ).find(div).removeClass("outpicklist");
		$jQ( formName  ).find(div).addClass("inpicklist");
		
		}

		

}

function pickList(counter, res_id)
{
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	  
	  
	xmlhttp.onreadystatechange=function()
	  {
	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		togglePickList(counter,xmlhttp.responseText);
		//document.getElementById('insertpoint' + counter).innerHTML=xmlhttp.responseText;
		}
	  }

	var pickList = '/resources/?task=picklist&res_id=' + res_id + '&no_html=1&t=' + Math.random();
	xmlhttp.open("GET",pickList,true);
	xmlhttp.send();


	
}

//window.addEvent('domready', toggledisplay);