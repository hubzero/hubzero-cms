function show_hide_top() {

  var topdiv = document.getElementById('mainheader_toggle');
  var showhideImage = document.getElementById('showhideImg');

  if(!topdiv) return;
  show = !show;

  if(show) {
    topdiv.style.display = '';
    collapse_value = 'off';
    showhideImage.src = '/images/showhide_header.gif';
  }
  else {
    topdiv.style.display = 'none';
    collapse_value = 'on';
    showhideImage.src = '/images/show_header.gif';
  }

  if(window.sessionStorage != null) {
    window.sessionStorage.collapse = collapse_value;
  }
}



function createCookie(name,value,days,path)
{
  var date;
  var expires;

  if (path == null)
    path = '/';

  if (days)
  {
    date = new Date();
    date.setTime( date.getTime() + (days * 86400000));
    expires = '; expires=' + date.toGMTString();
  }
  else
    expires = '';

  document.cookie = name + '=' + value + expires + '; path=' + path;
}


function clearDefault(el) {
  if (el.defaultValue==el.value) el.value = ""
}


function swapImgRestore() { //v3.0
  var i,x,a=document.sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function swapImage() { //v3.0
  var i,j=0,x,a=swapImage.arguments; document.sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=findObj(a[i]))!=null){document.sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.p) d.p=new Array();
    var i,j=d.p.length,a=preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.p[j]=new Image; d.p[j++].src=a[i];}}
}


function map(fun,arr) {
  a = new Array(arr.length);
  for(i=0; i < arr.length; ++i)
    a[i] = fun(arr[i]);
  return a;
}

function filter(fun,arr) {
  a = new Array();
  for(i=0; i < arr.length; ++i)
    if (fun(arr[i]))
      a.push(arr[i]);
  return a;
}

function foldl(fun,arr,init) {
  acc = init;
  for (i=0; i < arr.length; ++i)
    acc = fun(arr[i],acc);
  return acc;
}

function foldr(fun,arr,init) {
  acc = init;
  for (i=arr.length-1;  i > -1; i--)
    acc = fun(arr[i],acc);
  return acc;
}

function exists(elem,arr) {
  for (i=0; i<arr.length; ++i)
    if (arr[i] == x)
      return true;
  return false;
}

Array.prototype.map    = function(f) { return map(f,this); }
Array.prototype.filter = function(f) { return filter(f,this); }
Array.prototype.foldl  = function(f,init) { return foldl(f,this,init); }
Array.prototype.foldr  = function(f,init) { return foldr(f,this,init); }
Array.prototype.exists = function(el) { return exists(el,this); }


String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}
String.prototype.ltrim = function() {
	return this.replace(/^\s+/,"");
}
String.prototype.rtrim = function() {
	return this.replace(/\s+$/,"");
}


/*
    Written by Jonathan Snook, http://www.snook.ca/jonathan
    Add-ons by Robert Nyman, http://www.robertnyman.com
*/

function getElementsByClassName(oElm, strTagName, strClassName){
    var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
    var arrReturnElements = new Array();
    strClassName = strClassName.replace(/\-/g, "\\-");
    var oRegExp = new RegExp("(^|\\s)" + strClassName + "(\\s|$)");
    var oElement;
    for(var i=0; i<arrElements.length; i++){
        oElement = arrElements[i];
        if(oRegExp.test(oElement.className)){
            arrReturnElements.push(oElement);
        }
    }
    return (arrReturnElements)
}


var allowSubmit=true;

function subOnce( ){
  var numberForms = document.forms.length;
  if(allowSubmit) {
    allowSubmit=false;
  } else {
    var formIndex;
    for (formIndex = 0; formIndex < numberForms; formIndex++) {
      var elIndex;
      for (elIndex = 0; elIndex < document.forms[formIndex].elements.length; elIndex++)
        document.forms[formIndex].elements[elIndex].disabled = true;
    }
    return false;
  }
  return true;
}


var winOpen;
var screenWidth = screen.availWidth;
var screenHeight = screen.availHeight;


function windowOpen(filename,width,height)
{
	if (!width || width  == -1 || width  > screenWidth)  width  = screenWidth * 0.9;
	if (height == -1 || height > screenHeight) height = screenHeight * 0.9;

	if (winOpen != null && !winOpen.closed) winOpen.close();

	winOpen = window.open(filename,"myWindow","width=" + width + ",innerWidth=" + width + ",height=" + height + ",innerHeight=" + height + ",scrollbars=yes,resizable=yes,menubar=yes,status=no,toolbar=no,location=no,top=10,left=" + (screenWidth - width - 20) + ",screenX=" + (screenWidth - width - 20) + ",screenY=10");
}

function showNCGuide(section) {
  var url = "http://www.nees.org/research/dl_basic/neescentral_users_guide_1.8/";

  if(section) url += "#" + section;
  windowOpen(url, 600, -1)
}


function setFieldError(field, errorfieldId, errmsg) {
  if(field) {
    field.focus();
    if(field.type == "text" || field.type == "textarea" || field.type == "password") {
      field.style.backgroundColor = "#ffeeee";
    }
  }
  if(errmsg) {
    if(errorfieldId && document.getElementById(errorfieldId)) {
      document.getElementById(errorfieldId).innerHTML = "<div class='error'>" + errmsg + "</div>";
    }
    else {
      alert(errmsg);
    }
  }
}

function setValidField(fieldId, fieldErrorId) {
  field = document.getElementById(fieldId);
  fieldError = document.getElementById(fieldErrorId);

  if(field && (field.type == "text" || field.type == "textarea" || field.type == "password")) {
    field.style.backgroundColor = "#ffffff";
  }
  if(fieldError) fieldError.innerHTML = "";
}


function getAjaxDirInfo(dirPath, divId) {

  xmlHttp=GetXmlHttpObject();
  wz_mouseOut = false;

  if (xmlHttp==null) return;

  if(divId) tooltipDirId = divId;

  var url="/ajax/ajaxGetDirInfo.php?dirPath=" + escape(dirPath) + "&sid="+Math.random();

  xmlHttp.onreadystatechange=stateChangedOnGetInfo;
  xmlHttp.open("GET",url,true);
  xmlHttp.send(null);
}


function getAjaxDirInfoById(rowId, dirId, divId) {

  if(dataFileJSList[rowId]) {
    datafileObj = dataFileJSList[rowId];

    if(datafileObj.dirinfo != null) {
      Tip(datafileObj.dirinfo);
      return;
    }
  }

  xmlHttp=GetXmlHttpObject();
  wz_mouseOut = false;

  if (xmlHttp==null) return;

  if(divId) tooltipDirId = divId;

  var url="/ajax/ajaxGetDirInfo.php?dirId=" + dirId + "&sid="+Math.random();

  wz_rowId = rowId;

  xmlHttp.onreadystatechange=stateChangedOnGetInfo;
  xmlHttp.open("GET",url,true);
  xmlHttp.send(null);
}


function getAjaxMembers(str, title) {

  xmlHttp=GetXmlHttpObject();
  wz_mouseOut = false;

  if (xmlHttp==null) return;
  wz_title = title;
  var url="/ajax/ajaxListMembers.php?" + str + "&sid="+Math.random();

  xmlHttp.onreadystatechange=stateChangedOnGetInfo;
  xmlHttp.open("GET",url,true);
  xmlHttp.send(null);
}


var wz_mouseOut = false;
var tooltipDirId = null;
var wz_title = null;
var wz_rowId = null;

function stateChangedOnGetInfo()
{
  if(wz_mouseOut) return;

  if (xmlHttp.readyState < 4)
  {
    Tip("<img src='/tree_browser/img/loading.gif' width='16' height='16' alt='' style='vertical-align:middle;'> (Getting information...)");
  }
  else if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
  {
    ret = xmlHttp.responseText;

    if(tooltipDirId) {
      if(wz_title) {
        Tip(ret, FIX, [tooltipDirId, 0, 0], TITLE, wz_title);
      }
      else {
        Tip(ret, FIX, [tooltipDirId, 0, 0]);
      }
    }
    else {
      if(wz_title) {
        Tip(ret, TITLE, wz_title);
      }
      else {
        Tip(ret);
      }
    }
    tooltipDirId = null;
    wz_title = null;

    if(ret.indexOf("Size:") == 0 || ret == "Folder is empty") {
      mimeIconId = document.getElementById('mimeIcon_' + wz_rowId);

      if(mimeIconId) {
        if(ret.indexOf("Size:") == 0) {
          mimeIconId.src='/images/icons/folder_not_empty.gif';
        }
        else if(ret == "Folder is empty") {
          mimeIconId.src='/images/icons/folder_empty.gif';
        }
      }

      if(typeof(dataFileJSList) != "undefined" && dataFileJSList[wz_rowId]) {
        datafileObj = dataFileJSList[wz_rowId];
        datafileObj.dirinfo = ret;
      }

      wz_rowId = null;
    }
  }
}


function GetXmlHttpObject()
{
  var xmlHttp=null;
  try
  {
    // Firefox, Opera 8.0+, Safari
    xmlHttp=new XMLHttpRequest();
  }
  catch (e)
  {
    // Internet Explorer
    try
    {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch (e)
    {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
  return xmlHttp;
}
