function getDataDropDownByEntity(p_strElementId, p_strAlias, p_strTargetId){
  var iEntityIndex = document.getElementById(p_strElementId).selectedIndex;
  var strEntityText = document.getElementById(p_strElementId).options[iEntityIndex].text;
  var iEntityId = document.getElementById(p_strElementId).options[iEntityIndex].value;

  if(iEntityId != ""){
	var strUrl = p_strAlias+'/'+iEntityId+'?format=ajax';
    getMootools(strUrl, p_strTargetId);
  }
}

function getTrialInfo(p_strElementId, p_strAlias, p_strTargetId){
  var iTrialIndex = document.getElementById(p_strElementId).selectedIndex;
  var strTrialText = document.getElementById(p_strElementId).options[iTrialIndex].text;
  var iTrialId = document.getElementById(p_strElementId).options[iTrialIndex].value;

  if(iTrialId != ""){
    getMootools('/warehouse/'+p_strAlias+'/'+iTrialId+'?format=ajax', p_strTargetId);
  }
}

function getTrialDesc(p_strElementId, p_strTargetId){
  var iTrialIndex = document.getElementById(p_strElementId).selectedIndex;
  var strTrialText = document.getElementById(p_strElementId).options[iTrialIndex].text;
  var iTrialId = document.getElementById(p_strElementId).options[iTrialIndex].value;

  if(iTrialId != ""){
    getMootools('/warehouse/trial/'+iTrialId+'?format=ajax', p_strTargetId);
  }
}

function getRepetitionListByTrial(p_strElementId, p_strTargetId){
  var iTrialIndex = document.getElementById(p_strElementId).selectedIndex;
  var strTrialText = document.getElementById(p_strElementId).options[iTrialIndex].text;
  var iTrialId = document.getElementById(p_strElementId).options[iTrialIndex].value;

  if(iTrialId != ""){
    getMootools('/warehouse/repetitions/'+iTrialId+'?format=ajax', p_strTargetId);
  }
}

function getData(p_strElementId, p_strTargetId, p_strReferer){
  var iObjectIndex = document.getElementById(p_strElementId).selectedIndex;
  var strObjectText = document.getElementById(p_strElementId).options[iObjectIndex].text;
  var iObjectId = document.getElementById(p_strElementId).options[iObjectIndex].value;

  if(iObjectId != ""){
    getMootools('/warehouse/datafiles/'+iObjectId+'?format=ajax&referer='+p_strReferer, p_strTargetId);
  }
}

function getTools(p_strElementId, p_strTargetId, p_strReferer, p_iProjectId, p_iExperimentId){
  var iObjectIndex = document.getElementById(p_strElementId).selectedIndex;
  var strObjectText = document.getElementById(p_strElementId).options[iObjectIndex].text;
  var iObjectId = document.getElementById(p_strElementId).options[iObjectIndex].value;

  if(iObjectId != ""){
	  getMootools('/warehouse/tools/'+iObjectId+'?format=ajax&referer='+p_strReferer, p_strTargetId);
  }
}

function loadingContent(p_strTargetId){
	document.getElementById(p_strTargetId).innerHTML="<img src='/components/com_warehouse/images/loading.gif'/>";
}

function showElement(p_strElementId){
  document.getElementById(p_strElementId).style.display='';
}

function hideElement(p_strElementId){
  document.getElementById(p_strElementId).style.display='none';
}

function validateFileBrowser(p_strFormId, p_strCheckboxId, p_iNumberOfCheckboxes){
  var bValid = true;
  var iCount = 0;

  for(var i=0; i < p_iNumberOfCheckboxes; i++){
	var strCheckboxId = p_strCheckboxId+""+i;
	var oThisCheckbox = document.getElementById(strCheckboxId);
	if(oThisCheckbox != null){
	  if(oThisCheckbox.checked){
	    ++iCount;
	  }
	}
  }

  if(iCount==0){
	bValid = false;
  }

  if(bValid){
    document.getElementById(p_strFormId).submit();
  }else{
	alert("Please select a folder or file.");
  }
}

function downloadFileBrowser(p_strFormId, p_strCheckboxName, p_strRequestUrl, p_strApproxDownloadId, p_iMaxDownloadSize){
  var bValid = true;
  var iCount = 0;

  var oCheckBoxArray = document.getElementById(p_strFormId).elements[p_strCheckboxName];
  iNumberOfCheckBoxes = oCheckBoxArray.length;

  for(var i=0; i < iNumberOfCheckBoxes; i++){
    oThisCheckbox = oCheckBoxArray[i];
    if(oThisCheckbox != null){
      if(oThisCheckbox.checked){
        ++iCount;
      }
    }
  }


  if(iCount==0){
    if(!oCheckBoxArray.checked){
      bValid = false;
    }
  }

  /*
  iApproxDownloadSize = document.getElementById(p_strApproxDownloadId).value;
  if(iApproxDownloadSize > p_iMaxDownloadSize){
    bValid = false;
  }
  */

  if(bValid){
    document.getElementById(p_strFormId).action = p_strRequestUrl;
    document.getElementById(p_strFormId).submit();
    //alert("Download...");
  }else{
    if(iCount==0){
      alert("Please select a folder or file.");
    }else{
      if(iCount==1){
        alert("Your selected download directory/file is too large.");
      }else{
        alert("Your selected download directories/files are too large.");
      }
    }
  }
}

function submitDataForm(p_strFormId, p_strAction){
  var bValid = true;
  if(p_strAction.length == 0){
	 bValid = false;
	 alert("Data search form error.");
  }

  if(bValid){
    document.getElementById(p_strFormId).action=p_strAction;
    document.getElementById(p_strFormId).submit();
  }
}

function onChangeDataTab(p_strFormId, p_strToolId, p_strExperimentId, p_strTrialId, p_strRepetitionId){
  var iExperimentId = document.getElementById(p_strExperimentId).value;
  document.getElementById("txtExperiment").value = iExperimentId;

  var oTrial = document.getElementById(p_strTrialId);
  if(oTrial != null){
    var iTrialId = oTrial.value;
    document.getElementById("txtTrial").value = iTrialId;
  }

  var oRepetition = document.getElementById(p_strRepetitionId);
  if(oRepetition != null){
    var iRepetitionId = oRepetition.value;
    document.getElementById("txtRepetition").value = iRepetitionId;
  }

  var oTool = document.getElementById(p_strToolId);
  if(oTool != null){
    var strTool = oTool.value;
    if(strTool==null)strTool="";
    document.getElementById("txtTool").value = strTool;
  }

  document.getElementById(p_strFormId).submit();
}

function onChangeFileBrowser(p_strFormId, p_strExperimentId, p_strTrialId, p_strRepetitionId){
  var iExperimentId = document.getElementById(p_strExperimentId).value;
  //document.getElementById("txtExperiment").value = iExperimentId;

  var oTrial = document.getElementById(p_strTrialId);
  if(oTrial != null){
    var iTrialId = oTrial.value;
    //document.getElementById("txtTrial").value = iTrialId;
  }

  var oRepetition = document.getElementById(p_strRepetitionId);
  if(oRepetition != null){
    var iRepetitionId = oRepetition.value;
    //document.getElementById("txtRepetition").value = iRepetitionId;
  }

  document.getElementById(p_strFormId).submit();
}

function fileSearch(p_strSelectId, p_strInputId, p_iIndex, p_strTargetId){
  var oFindBy = document.getElementById(p_strSelectId);
  var oTerm = document.getElementById(p_strInputId);
  getMootools('/warehouse/searchfiles?format=ajax&term='+oTerm.value+'&findby='+oFindBy.value+'&index='+p_iIndex, p_strTargetId);
}

function displayVideoTypes(p_strElementToCheckId, p_strElementToDisplayId){
  var iVideoDisplayValue = document.getElementById(p_strElementToCheckId).value;
  if(iVideoDisplayValue==0){
    document.getElementById(p_strElementToCheckId).value = 1;
    document.getElementById(p_strElementToDisplayId).style.display='';
  }else{
    document.getElementById(p_strElementToCheckId).value = 0;
    document.getElementById(p_strElementToDisplayId).style.display='none';
  }
}