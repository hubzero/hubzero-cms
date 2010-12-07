function setValue(p_strTargetId, p_strValue){
  document.getElementById(p_strTargetId).value = p_strValue;
}

function showElement(p_strElementId){
  document.getElementById(p_strElementId).style.display='';
}

function hideElement(p_strElementId){
  document.getElementById(p_strElementId).style.display='none';
}

/**
 * Perform an ajax request.  
 * @param p_sRequestUrl  - URL to process
 * @param p_sResonseDivId - The div to place the results
 * @return
 */
function addNewMaterial(p_sRequestUrl, p_sResonseDivId){
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
  
  var strName = document.getElementById('txtMaterial').value;
  var strType = document.getElementById('txtMaterialType').value;
  var strDesc = document.getElementById('taMaterialDesc').value;
  var oFile = document.getElementById('materialFile').value;
  
  var parameters="material="+strName+"&type="+strType+"&desc="+strDesc+"&file="+oFile+"&format=ajax";
  
  xmlHttp.open("POST", p_sRequestUrl, true)
  xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
  xmlHttp.setRequestHeader("Connection", "close");
  xmlHttp.setRequestHeader("Content-length", parameters.length);
  xmlHttp.send(parameters);
}

/**
 * Perform an ajax request.  
 * @param p_sRequestUrl  - URL to process
 * @param p_sResonseDivId - The div to place the results
 * @return
 */
function addTeamMember(p_sRequestUrl, p_sResonseDivId){
  xmlHttp = getAjaxRequest();
  if (xmlHttp==null){
    alert ("Your browser does not support AJAX!");
    return;
  }

  /*
   * Unfortunately because of IE, I had to come up with a hack for
   * adding new members.  Originally, I would just set the innerHTML
   * of a row.  This works fine for all browsers but IE.  
   */
  xmlHttp.onreadystatechange = function(){
    if (xmlHttp.readyState==4){
      var iIndex = 0;  //insert new member into first row.

      var oMemberTable = document.getElementById("members-list");
      var oRow0 = oMemberTable.rows[0];
      var strRowText = oRow0.innerHTML; //Skip the headers "NameRoleEmailPermissionsExperiments"
      if((strRowText.indexOf("Name") >= 0) && (strRowText.indexOf("Role") > 0)){
        iIndex = iIndex+1;
      }

      var strResponseText = xmlHttp.responseText;
      if(strResponseText=="Team Member not found."){
        document.getElementById("memberError").innerHTML="<p class='error' style='margin-top:20px;'>"+strResponseText+"</p>";
        return;
      }

      var oNewMember = oMemberTable.insertRow(iIndex);
      oNewMember.style.background = "#FFFDEF";
      var strTextArray = strResponseText.split("***");
      for(i=0; i < strTextArray.length - 1; i++) {
        var oCell = oNewMember.insertCell(i);
        if(i==0){
          oCell.setAttribute("class", "photo");
          oCell.setAttribute("width", "60");
        }else if(i==2){
          oCell.setAttribute("style", "padding:0px;");
          oCell.setAttribute("id", "selectRole");
        }else if(i==4){
          oCell.setAttribute("nowrap", "nowrap");
        }
        oCell.innerHTML = strTextArray[i];
      }

      var iNewCount = document.getElementById('iNewMemberCount').value;
      ++iNewCount;
      document.getElementById('iNewMemberCount').value = iNewCount;
    }
  }
  
  var strUser = document.getElementById('newMember').value;
  var iProjectId = document.getElementById('projectId').value;
  var strParameters="user="+strUser+"&format=ajax&projectId="+iProjectId;
  
  xmlHttp.open("POST", p_sRequestUrl, true)
  xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
  xmlHttp.setRequestHeader("Connection", "close");
  xmlHttp.setRequestHeader("Content-length", strParameters.length);
  xmlHttp.send(strParameters);  
}

/**
 * Perform an ajax request.
 * @param p_sRequestUrl  - URL to process
 * @param p_iRowIndex - Row to update in members-list table
 * @return
 */
function editTeamMember(p_sRequestUrl, p_iRowIndex){
  xmlHttp = getAjaxRequest();
  if (xmlHttp==null){
    alert ("Your browser does not support AJAX!");
    return;
  }

  xmlHttp.onreadystatechange = function(){
    if (xmlHttp.readyState==4){
      var iIndex = p_iRowIndex;
      
      var oMemberTable = document.getElementById("members-list");
      var oRow0 = oMemberTable.rows[0];
      var strRowText = oRow0.innerHTML; //Skip the headers "NameRoleEmailPermissionsExperiments"
      if((strRowText.indexOf("Name") >= 0) && (strRowText.indexOf("Role") > 0)){
        iIndex = p_iRowIndex+1;
      }

      //check for new users
      var iNewCount = document.getElementById('iNewMemberCount').value;
      if(iNewCount > 0){
        iIndex = iIndex + 1;
      }
      
      //drop current row @ iIndex
      oMemberTable.deleteRow(iIndex);

      //add new row @ iIndex
      var oNewMember = oMemberTable.insertRow(iIndex);
      oNewMember.style.background = "#FFFDEF";
      var strTextArray = xmlHttp.responseText.split("***");
      for(i=0; i < strTextArray.length - 1; i++) {
        var oCell = oNewMember.insertCell(i);
        if(i==0){
          oCell.setAttribute("class", "photo");
          oCell.setAttribute("width", "60");
        }else if(i==2){
          oCell.setAttribute("style", "padding:0px;");
          oCell.setAttribute("id", "selectRole");
        }else if(i==4){
          oCell.setAttribute("nowrap", "nowrap");
        }
        oCell.innerHTML = strTextArray[i];
      }
    }
  }

  var iPersonId = document.getElementById('personId').value;
  var iProjectId = document.getElementById('projectId').value;
  var strParameters="personId="+iPersonId+"&format=ajax&projectId="+iProjectId;

  xmlHttp.open("POST", p_sRequestUrl, true)
  xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
  xmlHttp.setRequestHeader("Connection", "close");
  xmlHttp.setRequestHeader("Content-length", strParameters.length);
  xmlHttp.send(strParameters);
}

/*
function addTeamMember(p_sRequestUrl, p_sResonseDivId){
  xmlHttp = getAjaxRequest();
  if (xmlHttp==null){
    alert ("Your browser does not support AJAX!");
    return;
  }

  xmlHttp.onreadystatechange = function(){
    if (xmlHttp.readyState==4){
      var oNewMember = document.getElementById("setupNewMember");
      oNewMember.style.background = "#FFFDEF";
      oNewMember.innerHTML = xmlHttp.responseText;
    }
  }

  var strUser = document.getElementById('newMember').value;
  var iProjectId = document.getElementById('projectId').value;
  var strParameters="user="+strUser+"&format=ajax&projectId="+iProjectId;

  xmlHttp.open("POST", p_sRequestUrl, true)
  xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
  xmlHttp.setRequestHeader("Connection", "close");
  xmlHttp.setRequestHeader("Content-length", strParameters.length);
  xmlHttp.send(strParameters);
}
*/

function saveMember(p_strFormId, p_strUrl){
  document.getElementById(p_strFormId).action=p_strUrl;
  document.getElementById(p_strFormId).submit();
}

function saveForm(p_strFormId, p_strUrl){
  document.getElementById(p_strFormId).action=p_strUrl;
  document.getElementById(p_strFormId).submit();
}

/**
 *
 */
function uploadDataFile(p_strFormId, p_strUrl, p_strTitleId){
  bValid = true;
  if(p_strTitleId != null){
    if(document.getElementById(p_strTitleId) != null){
      strTitle = document.getElementById(p_strTitleId).value;
      if(strTitle == null || strTitle==""){
        bValid = false;
      }
    }
  }

  if(bValid){
    document.getElementById(p_strFormId).action=p_strUrl;
    document.getElementById(p_strFormId).enctype="multipart/form-data";
    document.getElementById(p_strFormId).submit();
  }else{
    alert("Title is required.");
  }

  return bValid;
}

function editorSubmit(p_strFormId, p_strSubmitId){
  document.getElementById(p_strSubmitId).value=1;
  document.getElementById(p_strFormId).submit();
}

function clearValue(p_strElementId, p_strDefaultValue){
  var oElement = document.getElementById(p_strElementId);
  if(oElement.value == p_strDefaultValue){
    oElement.value = '';
  }
}

function saveExperimentAccess(p_sRequestUrl, p_iPersonId, p_sTarget){
  xmlHttp = getAjaxRequest();
  if (xmlHttp==null){
    alert ("Your browser does not support AJAX!");
    return;
  }

  xmlHttp.onreadystatechange = function(){
    if (xmlHttp.readyState==4){
      document.getElementById(p_sTarget).innerHTML = xmlHttp.responseText;
    }
  }

  var iPersonId = p_iPersonId;
  var iProjectId = document.getElementById('projectId').value;

  var strSelectedExperiments = "";
  var cbxArray = document.frmPopout.experimentId;
  for(i=0; i < cbxArray.length; i++){
    if(cbxArray[i].checked){
      if(strSelectedExperiments==""){
        strSelectedExperiments += cbxArray[i].value;
      }else{
        strSelectedExperiments += ","+cbxArray[i].value;
      }
    }
  }
  
  var strParameters="personId="+iPersonId+"&format=ajax&projectId="+iProjectId+"&experimentId="+strSelectedExperiments;

  xmlHttp.open("POST", p_sRequestUrl, true)
  xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
  xmlHttp.setRequestHeader("Connection", "close");
  xmlHttp.setRequestHeader("Content-length", strParameters.length);
  xmlHttp.send(strParameters);
}

function getRepetitionList(p_strElementId, p_strAlias, p_strTargetId){
  var iTrialIndex = document.getElementById(p_strElementId).selectedIndex;
  var strTrialText = document.getElementById(p_strElementId).options[iTrialIndex].text;
  var iTrialId = document.getElementById(p_strElementId).options[iTrialIndex].value;

  if(iTrialId != ""){
    getMootools('/warehouse/projecteditor/'+p_strAlias+'?id='+iTrialId+'&format=ajax', p_strTargetId);
  }
}