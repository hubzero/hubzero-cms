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