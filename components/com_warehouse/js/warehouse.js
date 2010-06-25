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
    getMootools('/warehouse/data/'+iObjectId+'?format=ajax&referer='+p_strReferer, p_strTargetId);
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