function setCurationDone(p_iRowNumber, p_bAlert){
  var bValid = true;
  
  var strTitle = document.getElementById("txtDocumentTitle"+p_iRowNumber).value;
  
  var iSelectedIndex = document.getElementById("cboObjectType"+p_iRowNumber).selectedIndex;
  var strObjectType  = document.getElementById("cboObjectType"+p_iRowNumber).options[iSelectedIndex].value;
  
  if(strTitle.length==0 && strObjectType==0){
	bValid = false;
	if(p_bAlert){
	  alert("Title and File Category must not be blank.");
	}
  }
  
  if(bValid){
    if(document.getElementById("cbxCurateDone"+p_iRowNumber).checked){
	  document.getElementById("cbxCurateDone"+p_iRowNumber).value=1;
    }else if(!document.getElementById("cbxCurateDone"+p_iRowNumber).checked){
	  document.getElementById("cbxCurateDone"+p_iRowNumber).value=0;
    }
  }
}

function setExtension(p_strFileElementId, p_strExtElementId){
  var strFilename = document.getElementById(p_strFileElementId).value;
  var strFilenameArray = strFilename.split(".");
  
  document.getElementById(p_strExtElementId).value=strFilenameArray[1];
}

function setCurateSelect(p_iRowNumber){
  if(document.getElementById("cbxCurateSelect"+p_iRowNumber).checked){
	document.getElementById("cbxCurateDone"+p_iRowNumber).disabled=false;
	document.getElementById("txtDocumentTitle"+p_iRowNumber).disabled=false;
	document.getElementById("txtDocumentDescription"+p_iRowNumber).disabled=false;
	document.getElementById("cboObjectType"+p_iRowNumber).disabled=false;
	if(document.getElementById("txtDocumentExt"+p_iRowNumber)!=null){
	  document.getElementById("txtDocumentExt"+p_iRowNumber).disabled=false;
	}
	document.getElementById("txtDocumentCurateDate"+p_iRowNumber).disabled=false;
	document.getElementById("txtDocumentCurateVersion"+p_iRowNumber).disabled=false;
  }else{
    document.getElementById("cbxCurateDone"+p_iRowNumber).disabled=true;
	document.getElementById("txtDocumentTitle"+p_iRowNumber).disabled=true;
	document.getElementById("txtDocumentDescription"+p_iRowNumber).disabled=true;
	document.getElementById("cboObjectType"+p_iRowNumber).disabled=true;
	if(document.getElementById("txtDocumentExt"+p_iRowNumber)!=null){
	  document.getElementById("txtDocumentExt"+p_iRowNumber).disabled=true;
	}  
	document.getElementById("txtDocumentCurateDate"+p_iRowNumber).disabled=true;
	document.getElementById("txtDocumentCurateVersion"+p_iRowNumber).disabled=true;
  }
}

function curateAll(p_iDocumentCount){
  //get a count of how many documents are set for curation
  var iCount = 0;
  for(var i=0; i < p_iDocumentCount; i++){
	if(document.getElementById("cbxCurateSelect"+i).checked){
	  ++iCount;
	}
  }
  
  //if any document is marked for curation, uncheck all.
  //otherwise, if not documents are selected, check all.
  if(iCount > 0){
	for(var i=0; i < p_iDocumentCount; i++){
	  if(document.getElementById("cbxCurateSelect"+i).checked){
		document.getElementById("cbxCurateSelect"+i).checked = false;
		setCurateSelect(i);
	  }
    }
  }else{
    for(var i=0; i < p_iDocumentCount; i++){
	  if(!document.getElementById("cbxCurateSelect"+i).checked){
		document.getElementById("cbxCurateSelect"+i).checked = true;
		setCurateSelect(i);
	  }
    }
  }
}

function completeAll(p_iDocumentCount){
  //get a count of how many documents are set for curation
  var iCount = 0;
  for(var i=0; i < p_iDocumentCount; i++){
	if(document.getElementById("cbxCurateDone"+i).checked){
	  ++iCount;
	}
  }
  
  //if any document is marked for curation, uncheck all.
  //otherwise, if not documents are selected, check all.
  if(iCount > 0){
	for(var i=0; i < p_iDocumentCount; i++){
	  if(document.getElementById("cbxCurateDone"+i).checked){
		document.getElementById("cbxCurateDone"+i).checked = false;
		setCurationDone(i, false);
	  }
    }
  }else{
    for(var i=0; i < p_iDocumentCount; i++){
	  if(!document.getElementById("cbxCurateDone"+i).checked){
		document.getElementById("cbxCurateDone"+i).checked = true;
		setCurationDone(i, false);
	  }
    }
  }
}

function validateDocuments(p_strFormId, p_strUrl, p_iDocumentCount){
  var bValid = true;
  var strError = "";
  var iCount=0;
  
  //if cbxCurateDone# is checked, make sure file categories and titles are provided
  var i=0;
  for(i=0; i < p_iDocumentCount; i++){
	if(document.getElementById("cbxCurateSelect"+i).checked){
	  var strTitle = document.getElementById("txtDocumentTitle"+i).value;
		  
	  var iSelectedIndex = document.getElementById("cboObjectType"+i).selectedIndex;
	  var strObjectType  = document.getElementById("cboObjectType"+i).options[iSelectedIndex].value;
		  
	  if(strTitle.length==0 && strObjectType==0){
		bValid = false;
		strError = "Title and File Category must not be blank.";
	  }
	  ++iCount;
	}
  }
  
  //check the 3 new documents
  /*
  for(ii=i; ii < p_iDocumentCount+3; ii++){
	if(document.getElementById("cbxCurateSelect"+ii).checked){
	  var strTitle = document.getElementById("txtDocumentTitle"+ii).value;
		  
	  var iSelectedIndex = document.getElementById("cboObjectType"+ii).selectedIndex;
	  var strObjectType  = document.getElementById("cboObjectType"+ii).options[iSelectedIndex].value;
		  
	  if(strTitle.length==0 && strObjectType==0){
		bValid = false;
		strError = "Title and File Category must not be blank.";
	  }
	  ++iCount;
	}
  }
  */
  
  if(iCount==0){
	bValid=false;
	strError = "Please select one or more Curate checkboxes.";
  }
  
  //if every row has a title and object type, proceed.
  //otherwise, tell the user NO!
  if(bValid){
	document.getElementById(p_strFormId).action = p_strUrl;
	document.getElementById(p_strFormId).submit();
  }else{
	alert(strError);
  }
}

/**
 * Validates if a project or experiment object has the required curation fields set.
 * @param p_strFormId
 * @param p_strUrl
 * @return
 */
function validateObject(p_strFormId, p_strUrl){
  var bValid = true;
  var strError = "";
  
  var strShortTitle = document.getElementById("txtProjectShortTitle").value;
  if(strShortTitle.length==0){
	strError = "Please enter short title.";
	bValid = false;
  }
  var strDescription = document.getElementById("txtProjectDescription").value;
  if(strDescription.length==0){
	strError = "Please enter description.";
	bValid = false;
  }
  var strCurationDate = document.getElementById("txtProjectCurated").value;
  if(strCurationDate.length==0){
	strError = "Please enter curation date.";
	bValid = false;
  }
  var strCurationState = document.getElementById("txtProjectCurationState").value;
  if(strCurationState.length==0){
	strError = "Please enter curation state.";
	bValid = false;
  }
  
  if(bValid){
	document.getElementById(p_strFormId).action = p_strUrl;
	document.getElementById(p_strFormId).submit();
  }else{
	alert(strError);
  }
}

function validateDownload(p_strFormId, p_strUrl, p_iDocumentCount){
  //get a count of how many documents are set for curation
  var iCount = 0;
  for(var i=0; i < p_iDocumentCount; i++){
	if(document.getElementById("cbxCurateSelect"+i).checked){
	  ++iCount;
	}
  }
  
  //submit form
  if(iCount > 0){
	document.getElementById(p_strFormId).action = p_strUrl;
	document.getElementById(p_strFormId).submit();
  }else{
	alert("Please select at least one Curate checkbox to download file(s).");
  }
}

function changeView(p_strFormId, p_strUrl){
  var bValid = true;
  var iSelectedIndex = document.getElementById("cboViewObject").selectedIndex;
  var strViewObject  = document.getElementById("cboViewObject").options[iSelectedIndex].value;
	  
  if(strViewObject.length > 0){
    document.getElementById(p_strFormId).action = p_strUrl;
    document.getElementById(p_strFormId).submit();
  }else{
	alert("Please select an experiment.");
  }
}