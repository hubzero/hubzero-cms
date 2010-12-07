/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function setAllCheckBoxes(FormName, FieldName, CheckValue, ObjectID){
  if(!document.forms[FormName])
    return;

  var objCheckBoxes = document.forms[FormName].elements[FieldName];
  if(!objCheckBoxes)
    return;

  var countCheckBoxes = objCheckBoxes.length;
  if(!countCheckBoxes){
    objCheckBoxes.checked = CheckValue;
  }else{
    // set the check value for all check boxes
    for(var i = 0; i < countCheckBoxes; i++) {
      if(objCheckBoxes[i].id == ObjectID) {
	objCheckBoxes[i].checked = CheckValue;
      }
    }
  }
}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function setAllDownloadCheckBoxes(FormName, FieldName, CheckValue, ObjectID, p_strCurrentValueId, p_iMaxSize, p_strMaxSize, p_sRequestUrl, p_sResonseDivId){
  if(!document.forms[FormName])
    return;

  var objCheckBoxes = document.forms[FormName].elements[FieldName];
  if(!objCheckBoxes)
    return;

  strDataFileIds = "";
  var countCheckBoxes = objCheckBoxes.length;
  if(!countCheckBoxes){
    objCheckBoxes.checked = CheckValue;
    strDataFileIds = ""+objCheckBoxes.value;
  }else{
    // set the check value for all check boxes
    for(var i = 0; i < countCheckBoxes; i++) {
      if(objCheckBoxes[i].id == ObjectID) {
	objCheckBoxes[i].checked = CheckValue;
        if(strDataFileIds==""){
          strDataFileIds = objCheckBoxes[i].value;
        }else{
          strDataFileIds += ","+objCheckBoxes[i].value;
        }
      }
    }

    //computeDownloadSizeBulk(CheckValue, strDataFileIds, p_strCurrentValueId, p_iMaxSize, p_strMaxSize, p_sRequestUrl, p_sResonseDivId);
  }
  
  computeDownloadSizeBulk(CheckValue, strDataFileIds, p_strCurrentValueId, p_iMaxSize, p_strMaxSize, p_sRequestUrl, p_sResonseDivId);
}

/**
 * Select all of the checkboxes by a given field.
 * The field is referenced by name.  If the length
 * is null, we probably have only 1 checkbox.
 *
 */
function checkAll(field){
  if(field.length==null){
    field.checked=true;
  }

  for (i = 0; i < field.length; i++){
    field[i].checked = true ;
  }
}

/**
 * De-select all of the checkboxes by a given field.
 * The field is referenced by name.  If the length
 * is null, we probably have only 1 checkbox.
 *
 */
function uncheckAll(field){
  if(field.length==null){
    field.checked=false;
  }

  for (i = 0; i < field.length; i++){
    field[i].checked = false ;
  }
}

/**
 * Click on controlling checkbox to either select
 * or unselect children checkboxes.
 */
function selectAll(p_strFormName, p_strFieldName){
  field = document.forms[p_strFormName].elements[p_strFieldName];
  var nChexboxesSelected = 0;
  for(i = 0; i < field.length; i++){
    if(field[i].checked == true){
          ++nChexboxesSelected;
        }
  }

  //maybe there's only one checkbox
  if(field.length==null){
    if(field.checked==true){
      ++nChexboxesSelected;
    }
  }

  //if 0 are selected, check all.
  //if 1+ are selected, clear all.
  if(nChexboxesSelected==0){
    checkAll(field);
  }else{
    uncheckAll(field);
  }
}

