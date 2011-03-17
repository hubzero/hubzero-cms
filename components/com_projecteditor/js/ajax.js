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

  xmlHttp.open("GET", p_sRequestUrl, true);
  xmlHttp.send();
}

/**
 * Perform an ajax request.
 * @param p_sRequestUrl  - URL to process
 * @param p_sResonseDivId - The div to place the results
 * @param p_sValue
 * @return
 */
function suggest(p_sRequestUrl, p_sResonseDivId, p_sInputValue, p_sInputFieldId){
  if(p_sInputValue.length==0){
	return;
  }

  xmlHttp = getAjaxRequest();
  if (xmlHttp==null){
    alert ("Your browser does not support AJAX!");
    return;
  }

  xmlHttp.onreadystatechange = function(){
    if (xmlHttp.readyState==4 && xmlHttp.status==200){
      //document.getElementById(p_sResonseDivId).innerHTML = xmlHttp.responseText;
      var ss = document.getElementById(p_sResonseDivId);
	  ss.innerHTML = '';
	  var str = xmlHttp.responseText.split("\n");
	  //alert("results: "+xmlHttp.responseText);
	  for(i=0; i < str.length - 1; i++) {
		if(str[i].length > 0){
	      //Build our element string.  This is cleaner using the DOM, but
              //IE doesn't support dynamically added attributes.
              var suggest = "";
              if(str[i] != "more..."){
	        suggest = '<div onmouseover="javascript:suggestOver(this);" ';
	      suggest += 'onmouseout="javascript:suggestOut(this);" ';
	      suggest += "onclick=\"javascript:setSuggestedValue('"+p_sResonseDivId+"','"+p_sInputFieldId+"',this.innerHTML);\" ";
	      //suggest += "onClick=\"javascript:alert(this.innerHTML);\" ";
	      suggest += 'class="suggest_link">' + str[i] + '</div>';
	      ss.innerHTML += suggest;
              }else{
                suggest = '<div class="suggest_link suggest_more">' + str[i] + '</div>';
                ss.innerHTML += suggest;
		}
	  }
	  }
	  //alert(ss.innerHTML);
    }
  }

  var strQuery = p_sRequestUrl+"&term="+p_sInputValue;
  xmlHttp.open("GET", strQuery, true);
  xmlHttp.send();
}

/**
 * Perform an ajax request.
 * @param p_sRequestUrl  - URL to process
 * @param p_sResonseDivId - The div to place the results
 * @param p_sValue
 * @return
 */
function suggestTrial(p_sRequestUrl, p_sResonseDivId, p_sInputValue, p_iExperimentId, p_sInputFieldId){
  if(p_sInputValue.length==0){
	return;
  }

  xmlHttp = getAjaxRequest();
  if (xmlHttp==null){
    alert ("Your browser does not support AJAX!");
    return;
  }

  xmlHttp.onreadystatechange = function(){
    if (xmlHttp.readyState==4 && xmlHttp.status==200){
      var ss = document.getElementById(p_sResonseDivId);
      ss.innerHTML = '';
      var str = xmlHttp.responseText.split("\n");
      for(i=0; i < str.length - 1; i++) {
        if(str[i].length > 0){
          //Build our element string.  This is cleaner using the DOM, but			//IE doesn't support dynamically added attributes.
          var suggest = '<div onmouseover="javascript:suggestOverTrial(this, '+p_iExperimentId+');" ';
          suggest += 'onmouseout="javascript:suggestOut(this);" ';
          suggest += "onclick=\"javascript:setSuggestedValue('"+p_sResonseDivId+"','"+p_sInputFieldId+"',this.innerHTML);\" ";
          //suggest += "onClick=\"javascript:alert(this.innerHTML);\" ";
          suggest += 'class="suggest_link">' + str[i] + '</div>';
          ss.innerHTML += suggest;
        }
      }
      //alert(ss.innerHTML);
    }
  }

  var strQuery = p_sRequestUrl+"&term="+p_sInputValue+"&experimentId="+p_iExperimentId;
  xmlHttp.open("GET", strQuery, true);
  xmlHttp.send();
}

/**
 * Perform an ajax request.
 * @param p_sRequestUrl  - URL to process
 * @param p_sResonseDivId - The div to place the results
 * @param p_sValue
 * @return
 */
function suggestRepetition(p_sRequestUrl, p_iRepId){
  if(p_iRepId.length==0){
    return;
  }

  xmlHttp = getAjaxRequest();
  if (xmlHttp==null){
    alert ("Your browser does not support AJAX!");
    return;
  }

  xmlHttp.onreadystatechange = function(){
    if (xmlHttp.readyState==4 && xmlHttp.status==200){
      var strArray = xmlHttp.responseText.split("[repinfo]");
      document.getElementById('strStartDate').value = strArray[0];
      document.getElementById('strEndDate').value = strArray[1];
      document.getElementById('repId').value = strArray[2];
      //alert(ss.innerHTML);
    }
  }

  var strQuery = p_sRequestUrl+"&id="+p_iRepId;
  xmlHttp.open("GET", strQuery, true);
  xmlHttp.send();
}

/**
 * Perform an ajax request.
 * @param p_sRequestUrl  - URL to process
 * @param p_sResonseDivId - The div to place the results
 * @param p_sValue
 * @return
 */
function suggestFacility(p_sRequestUrl, p_sResonseDivId, p_sInputValue, p_sInputFieldId, p_strEquipmentUrl, p_strEquipmentTargetId){
  if(p_sInputValue.length==0){
	return;
  }

  xmlHttp = getAjaxRequest();
  if (xmlHttp==null){
    alert ("Your browser does not support AJAX!");
    return;
  }

  xmlHttp.onreadystatechange = function(){
    if (xmlHttp.readyState==4 && xmlHttp.status==200){
      //document.getElementById(p_sResonseDivId).innerHTML = xmlHttp.responseText;
      var ss = document.getElementById(p_sResonseDivId);
	  ss.innerHTML = '';
	  var str = xmlHttp.responseText.split("\n");
	  //alert("results: "+xmlHttp.responseText);
	  for(i=0; i < str.length - 1; i++) {
		if(str[i].length > 0){
	      //Build our element string.  This is cleaner using the DOM, but			//IE doesn't support dynamically added attributes.
	      var suggest = '<div onmouseover="javascript:suggestOver(this);" ';
	      suggest += 'onmouseout="javascript:suggestOut(this);" ';
	      suggest += "onclick=\"javascript:setSuggestedFacilityValue('"+p_sResonseDivId+"','"+p_sInputFieldId+"',this.innerHTML, '"+p_strEquipmentUrl+"', '"+p_strEquipmentTargetId+"');\" ";
	      suggest += 'class="suggest_link">' + str[i] + '</div>';
	      ss.innerHTML += suggest;
		}
	  }
	  //alert(ss.innerHTML);
    }
  }

  var strQuery = p_sRequestUrl+"&term="+p_sInputValue;
  xmlHttp.open("GET", strQuery, true);
  xmlHttp.send();
}

function suggestLocationPlanByExperimentId(p_sRequestUrl, p_iExperimentId, p_sResonseDivId, p_sInputValue, p_sInputFieldId){
  if(p_sInputValue.length==0){
	return;
  }

  xmlHttp = getAjaxRequest();
  if (xmlHttp==null){
    alert ("Your browser does not support AJAX!");
    return;
  }

  xmlHttp.onreadystatechange = function(){
    if (xmlHttp.readyState==4 && xmlHttp.status==200){
      //document.getElementById(p_sResonseDivId).innerHTML = xmlHttp.responseText;
      var ss = document.getElementById(p_sResonseDivId);
	  ss.innerHTML = '';
	  var str = xmlHttp.responseText.split("\n");
	  //alert("results: "+xmlHttp.responseText);
	  for(i=0; i < str.length - 1; i++) {
	    if(str[i].length > 0){
	      //Build our element string.  This is cleaner using the DOM, but			//IE doesn't support dynamically added attributes.
	      var suggest = '<div onmouseover="javascript:suggestOver(this);" ';
	      suggest += 'onmouseout="javascript:suggestOut(this);" ';
	      suggest += "onclick=\"javascript:setSuggestedValue('"+p_sResonseDivId+"','"+p_sInputFieldId+"',this.innerHTML);\" ";
	      //suggest += "onClick=\"javascript:alert(this.innerHTML);\" ";
	      suggest += 'class="suggest_link">' + str[i] + '</div>';
	      ss.innerHTML += suggest;
	    }
	  }
	  //alert(ss.innerHTML);
    }
  }

  var strQuery = p_sRequestUrl+"&term="+p_sInputValue+"&experimentId="+p_iExperimentId;
  xmlHttp.open("GET", strQuery, true);
  xmlHttp.send();
}

//Mouse over function
function suggestOver(div_value) {
  div_value.className = 'suggest_link_over';
}

//Mouse out function
function suggestOut(div_value) {
  div_value.className = 'suggest_link';
}

//Click function
function setSuggestedValue(p_sResonseDivId, p_sInputFieldId, p_sValue) {
  //var strShowMe = p_sResonseDivId+", input="+p_sInputFieldId+", value="+p_sValue;
  //alert(strShowMe);
  p_sValue = p_sValue.replace('&amp;','&');
  document.getElementById(p_sInputFieldId).value = p_sValue;
  document.getElementById(p_sResonseDivId).innerHTML = '';
}

//Click function
function setSuggestedFacilityValue(p_sResonseDivId, p_sInputFieldId, p_sValue, p_strEquipmentUrl, p_strEquipmentTargetId) {
  //var strShowMe = p_sResonseDivId+", input="+p_sInputFieldId+", value="+p_sValue;
  //alert(strShowMe);
  document.getElementById(p_sInputFieldId).value = p_sValue;
  document.getElementById(p_sResonseDivId).innerHTML = '';

  var strSelectedEquipment = "";
  var oEquipmentIdList = document.getElementById('equipmentlist');
  if(oEquipmentIdList != null){
    strSelectedEquipment = oEquipmentIdList.value;
  }

  //find the equipment list for the site
  getMootools(p_strEquipmentUrl+"&term="+p_sValue+"&equipmentId="+strSelectedEquipment, p_strEquipmentTargetId);
}

function editFacilityEquipment(p_sValue, p_strEquipmentUrl, p_strEquipmentTargetId) {

  //find the equipment list for the site
  getMootools(p_strEquipmentUrl+"&term="+p_sValue, p_strEquipmentTargetId);
}

function appendEquipment(p_strEquipmentList, p_iEquipmentId){
  var oEquipmentIdList = document.getElementById(p_strEquipmentList);
  if(oEquipmentIdList != null){
    strEquipmentIdList = oEquipmentIdList.value;
    if(strEquipmentIdList != null && strEquipmentIdList != ""){
      strEquipmentIdList += ","+p_iEquipmentId;
    }else{
      strEquipmentIdList += p_iEquipmentId;
    }
    document.getElementById(p_strEquipmentList).value = strEquipmentIdList;
  }
}

//Click function
function setSuggestedLocationPlanValue(p_sResonseDivId, p_sInputFieldId, p_sValue) {
  document.getElementById(p_sInputFieldId).value = p_sValue;
  document.getElementById(p_sResonseDivId).innerHTML = '';

  //find the xyz units

  //get the sensors for the plan
}

//Mouse over function
function suggestOverTrial(div_value, p_iExperimentId) {
  div_value.className = 'suggest_link_over';

  strDivValue = div_value.innerHTML;

  if(strDivValue.length==0){
    return;
  }

  if(p_iExperimentId == null){
    return;
  }

  xmlHttp = getAjaxRequest();
  if (xmlHttp==null){
    alert ("Your browser does not support AJAX!");
    return;
  }

  xmlHttp.onreadystatechange = function(){
    if (xmlHttp.readyState==4 && xmlHttp.status==200){
      var strTrialArray = xmlHttp.responseText.split("[trialinfo]");
      document.getElementById('strStartDate').value = strTrialArray[0];
      document.getElementById('strEndDate').value = strTrialArray[1];
      document.getElementById('objective').value = strTrialArray[2];
      document.getElementById('description').value = strTrialArray[3];
      document.getElementById('trialId').value = strTrialArray[4];
      //document.getElementById('title').value = strTrialArray[5];
    }
  }

  var strQuery = "/projecteditor/gettrialinfo?format=ajax&term="+strDivValue+"&experimentId="+p_iExperimentId;
  xmlHttp.open("GET", strQuery, true);
  xmlHttp.send();
}

/**
 * Process an ajax request via mootools.
 * @param p_sUrl
 * @param p_sTargetId
 * @return
 */
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

/**
 * Adds the current value to the user's session array.  Upon completion,
 * show the results in the div p_sTargetId.
 * @param p_sUrl - request url
 * @param p_strFieldName - name of field tag (also serves as session key)
 * @param p_strFieldId - id of field tag
 * @param p_sTargetId - where to display results
 * @return
 */
function addInputViaMootools(p_sUrl, p_strFieldName, p_strFieldId, p_sTargetId){
  var strValue = document.getElementById(p_strFieldId).value;
  if(strValue.length > 0){
    $(p_sTargetId).empty().addClass('ajax-loading');
    var strUrl = p_sUrl+"&name="+p_strFieldName+"&value="+escape(strValue);
    var a = new Ajax( strUrl, {
      method: 'get',
      onComplete: function( response ) {
        // Other code to execute when the request completes.
        $(p_sTargetId).removeClass('ajax-loading').setHTML( response );
        if( response != null ){
          document.getElementById(p_strFieldId).value="";
        }
      }
    }).request();
  }else{
    alert(p_strFieldName+" should not be blank");
  }
}

/**
 * Adds the current value to the user's session array.  Upon completion,
 * show the results in the div p_sTargetId.
 * @param p_sUrl - request url
 * @param p_strFieldName - name of field tag (also serves as session key)
 * @param p_strFieldId - id of field tag
 * @param p_strFieldName2 - name of field tag (also serves as session key)
 * @param p_strFieldId2 - id of field tag
 * @param p_sTargetId - where to display results
 * @return
 */
function addInputPairViaMootools(p_sUrl, p_strFieldName, p_strFieldId, p_strFieldName2, p_strFieldId2, p_sTargetId){
	var strValue = document.getElementById(p_strFieldId).value;
	var strValue2 = document.getElementById(p_strFieldId2).value;
  if(p_strFieldName=="sponsor"){
    if(strValue.toLowerCase()=="nsf" && (strValue2.length==0 || strValue2.toLowerCase()=="award number")){
      alert(p_strFieldName+" and "+p_strFieldName2+" should not be blank for NSF awards. "+strValue);
      return;
    }

    if(strValue2.toLowerCase()=="award number"){
      strValue2 = "";
    }

    addInputPair(p_sUrl, p_strFieldName, p_strFieldId, p_strFieldName2, p_strFieldId2, p_sTargetId, strValue, strValue2);
  }else{
	  if(strValue.length > 0 && strValue2.length > 0){
      addInputPair(p_sUrl, p_strFieldName, p_strFieldId, p_strFieldName2, p_strFieldId2, p_sTargetId, strValue, strValue2);
    }else{
      alert(p_strFieldName+" and "+p_strFieldName2+" should not be blank.");
    }
  }
//  if(strValue.length > 0 && strValue2.length > 0){
//    $(p_sTargetId).empty().addClass('ajax-loading');
//	  var strUrl = p_sUrl+"&field1="+p_strFieldName+"&value1="+strValue+"&field2="+p_strFieldName2+"&value2="+strValue2;
//      var a = new Ajax( strUrl, {
//            method: 'get',
//            onComplete: function( response ) {
//              // Other code to execute when the request completes.
//              $(p_sTargetId).removeClass('ajax-loading').setHTML( response );
//              if( response != null ){
//                document.getElementById(p_strFieldId).value="";
//                document.getElementById(p_strFieldId2).value="";
//              }
//            }
//      }).request();
//  }else{
//    alert(p_strFieldName+" and "+p_strFieldName2+" should not be blank");
//  }
}

function addInputPair(p_sUrl, p_strFieldName, p_strFieldId, p_strFieldName2, p_strFieldId2, p_sTargetId, p_strValue, p_strValue2){
	  $(p_sTargetId).empty().addClass('ajax-loading');
  var strUrl = p_sUrl+"&field1="+p_strFieldName+"&value1="+p_strValue+"&field2="+p_strFieldName2+"&value2="+p_strValue2;
      var a = new Ajax( strUrl, {
            method: 'get',
            onComplete: function( response ) {
              // Other code to execute when the request completes.
              $(p_sTargetId).removeClass('ajax-loading').setHTML( response );
              if( response != null ){
                document.getElementById(p_strFieldId).value="";
                document.getElementById(p_strFieldId2).value="";
              }
            }
      }).request();
	}

/**
 * Adds the current value to the user's session array.  Upon completion,
 * show the results in the div p_sTargetId.
 * @param p_sUrl - request url
 * @param p_strFieldName - name of field tag (also serves as session key)
 * @param p_strFieldId - id of field tag
 * @param p_sTargetId - where to display results
 * @return
 */
function addMaterialViaMootools(p_sUrl, p_strNameId, p_strTypeId, p_strDescId, p_sTargetId){
	var bValid = true;
	var strName = document.getElementById(p_strNameId).value;
	if(strName.length == 0){
	  bValid = false;
	  alert("Material name should not be blank.");
	}

	var strType = document.getElementById(p_strTypeId).value;
	if(bValid){
	  if(strType.length==0){
		bValid = false;
		alert("Material type should not be blank.");
	  }
	}

	var strDesc = document.getElementById(p_strDescId).value;

	if(bValid){
	  $(p_sTargetId).empty().addClass('ajax-loading');
	  var strUrl = p_sUrl+"&material="+strName+"&type="+strType+"&desc="+strDesc;
	  var a = new Ajax( strUrl, {
            method: 'get',
            onComplete: function( response ) {
              // Other code to execute when the request completes.
              $(p_sTargetId).removeClass('ajax-loading').setHTML( response );
              if( response != null ){
                document.getElementById(p_strNameId).value="";
                document.getElementById(p_strTypeId).value="";
                document.getElementById(p_strDescId).value="";
              }
            }
      }).request();
	}
}

/**
 *
 * @param p_sUrl - request url
 * @param p_strFieldName - name of field tag (also serves as session key)
 * @param p_iArrayIndex - index within $_SESSION[p_strFieldName]
 * @param p_sTargetId - where to display results
 * @return
 */
function removeInputViaMootools(p_sUrl, p_strFieldName, p_iArrayIndex, p_sTargetId){
	$(p_sTargetId).empty().addClass('ajax-loading');
	var strUrl = p_sUrl+"&name="+p_strFieldName+"&value="+p_iArrayIndex;
    var a = new Ajax( strUrl, {
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
	//new Event(e).stop();
        e.stop();

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
                  alert(response);
		}
	});
  });
}

function postMootools(p_sFormId, p_sTargetId, p_sAction){
  $(p_sFormId).addEvent(p_sAction, function(e) {
        //Prevents the default submit event from loading a new page.
        new Event(e).stop();
        //Empty the log and show the spinning indicator.
        var log = $(p_sTargetId).empty().addClass('ajax-loading');
        //Set the options of the form's Request handler.
        //("this" refers to the $('myForm') element).

        this.set('send', {onComplete: function(response) {
                alert(response);
                //log.removeClass('ajax-loading');
                //log.set('html', response);
        }});
        //Send the form.
        this.send();

        /*
        this.send({
		//update: log,
		onComplete: function(response) {
                  log.removeClass('ajax-loading');
                  alert(response);
		}
	});
        */
  });
}

function saveInput(p_sUrlPrefix, p_sInputField, p_sResultsDivId){
  var sUrl = p_sUrlPrefix+"&"+p_sInputField+"="+document.getElementById(p_sInputField).value;
  getMootools(sUrl, p_sResultsDivId);
}

function clearSuggestion(p_strInputField){
  //document.getElementById(p_strInputField).innerHTML="";
}

/**
 * Perform an ajax request.
 * @param p_sRequestUrl  - URL to process
 * @param p_sResonseDivId - The div to place the results
 * @param p_sValue
 * @return
 */
function getTrial(p_sRequestUrl, p_iTrialId){
  if(p_iTrialId.length==0){
    return;
  }

  xmlHttp = getAjaxRequest();
  if (xmlHttp==null){
    alert ("Your browser does not support AJAX!");
    return;
  }

  xmlHttp.onreadystatechange = function(){
    if (xmlHttp.readyState==4 && xmlHttp.status==200){
      var strTrialArray = xmlHttp.responseText.split("[trialinfo]");
      document.getElementById('strStartDate').value = strTrialArray[0];
      document.getElementById('strEndDate').value = strTrialArray[1];
      document.getElementById('objective').value = strTrialArray[2];
      document.getElementById('description').value = strTrialArray[3];
      document.getElementById('trialId').value = strTrialArray[4];
      document.getElementById('txtTitle').value = strTrialArray[5];
      //alert(ss.innerHTML);
    }
  }

  var strQuery = p_sRequestUrl+"&id="+p_iTrialId;
  xmlHttp.open("GET", strQuery, true);
  xmlHttp.send();
}