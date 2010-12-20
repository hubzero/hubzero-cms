function getAjaxRequest(){
  var xmlHttp;
  try{
    // Firefox, Opera 8.0+, Safari
    xmlHttp = new XMLHttpRequest();
  }catch (e){
    // Internet Explorer
    try{
      xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
    }catch (e){
      try{
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
      }catch (e){
        xmlHttp = null;
      }
    }
  }
  return xmlHttp;
}

/**
 * Perform an ajax request.  
 * @param p_sRequestUrl  - URL to process
 * @param p_sResonseDivId - The div to place the results
 * @return
 */
function getAjaxGet(p_sRequestUrl, p_sResonseDivId){
  xmlHttp = getAjaxRequest();
  if (xmlHttp==null){
    alert ("Your browser does not support AJAX!");
    return;
  }
  
  xmlHttp.onreadystatechange = function(){
    if (xmlHttp.readyState==4){
      document.getElementById(p_sResonseDivId).innerHTML = xmlHttp.responseText;
    }
  }

  xmlHttp.open("POST", p_sRequestUrl, true);
  xmlHttp.send(null);
}

function getMootools(p_sUrl, p_sTargetId){
	$(p_sTargetId).empty().addClass('ajax-loading');
    var a = new Ajax( p_sUrl, {
            method: 'get',
            onComplete: function( response ) {
              // Other code to execute when the request completes.
              $(p_sTargetId).removeClass('ajax-loading').setHTML( response );
            }
    }).request();
}

function getMootoolsForm(p_sFormId, p_sTargetId, p_sAction){
  $(p_sFormId).addEvent(p_sAction, function(e) {
	/**
	 * Prevent the submit event
	 */
	new Event(e).stop();
 
	var bFormIsChildOfTarget = false;
	var oTarget = document.getElementById(p_sTargetId);
	if(oTarget.getElementById(p_sFormId)!=null){
	  bFormIsChildOfTarget = true;
	}
	
	/**
	 * If the form is not a child of the target, 
	 * this empties the result div and shows 
	 * the spinning indicator
     */
	var sResultsDiv = null;
	if(!bFormIsChildOfTarget){
	  sResultsDiv = $(p_sTargetId).empty().addClass('ajax-loading');	
	}else{
	  sResultsDiv = $(p_sTargetId);
	}
	
	/**
	 * send takes care of encoding and returns the Ajax instance.
	 * if the form is not a child of the target, onComplete 
	 * removes the spinner from the resulting div.
	 */
	this.send({
		update: sResultsDiv,
		onComplete: function(response) {
		  if(!bFormIsChildOfTarget){
		    sResultsDiv.removeClass('ajax-loading');
		  }
		}
	});
  });
}

function saveInput(p_sUrlPrefix, p_sInputField, p_sResultsDivId){
  var sUrl = p_sUrlPrefix+"&"+p_sInputField+"="+document.getElementById(p_sInputField).value;
  getMootools(sUrl, p_sResultsDivId);  
}

function computeDownloadSize(field, p_strCurrentValueId, p_iMaxSize, p_strMaxSize, p_sRequestUrl, p_sResonseDivId){
  iDataFileId = field.value;
  iCurrentSum = document.getElementById(p_strCurrentValueId).value;
  strAction = "";
  if(field.checked==false){
    strAction = "subtract";
  }else{
    strAction = "add";
  }

  xmlHttp = getAjaxRequest();
  if (xmlHttp==null){
    alert ("Your browser does not support AJAX!");
    return;
  }

  xmlHttp.onreadystatechange = function(){
    if (xmlHttp.readyState==4 && xmlHttp.status==200){
      strResponse = xmlHttp.responseText;
      strResponseArray = strResponse.split(":");
      //alert(strResponseArray[0]+", "+strResponseArray[1]);

      iCurrentSum = strResponseArray[0];
      document.getElementById(p_strCurrentValueId).value = iCurrentSum;
      
      strMessage = "Approximate Download File: "+strResponseArray[1]+" (max is "+p_strMaxSize+")";
      document.getElementById(p_sResonseDivId).innerHTML = strMessage;
    }
  }

  var strQuery = p_sRequestUrl+"&sum="+iCurrentSum+"&action="+strAction+"&id="+iDataFileId;
  xmlHttp.open("GET", strQuery, true);
  xmlHttp.send();
}

function computeDownloadSizeBulk(p_bAdd, p_strDataFileIds, p_strCurrentValueId, p_iMaxSize, p_strMaxSize, p_sRequestUrl, p_sResonseDivId){
  iDataFileId = p_strDataFileIds;
  iCurrentSum = document.getElementById(p_strCurrentValueId).value;
  strAction = "add";
  if(!p_bAdd){
    strAction = "subtract";
  }
  //alert(strAction);

  xmlHttp = getAjaxRequest();
  if (xmlHttp==null){
    alert ("Your browser does not support AJAX!");
    return;
  }

  xmlHttp.onreadystatechange = function(){
    if (xmlHttp.readyState==4 && xmlHttp.status==200){
      strResponse = xmlHttp.responseText;
      strResponseArray = strResponse.split(":");

      iCurrentSum = strResponseArray[0];
      document.getElementById(p_strCurrentValueId).value = iCurrentSum;

      strMessage = "Approximate Download File: "+strResponseArray[1]+" (max is "+p_strMaxSize+")";
      document.getElementById(p_sResonseDivId).innerHTML = strMessage;
    }
  }

  var strQuery = p_sRequestUrl+"&sum="+iCurrentSum+"&action="+strAction+"&id="+iDataFileId;
  xmlHttp.open("GET", strQuery, true);
  xmlHttp.send();
}