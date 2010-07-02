/**
* nlstreeext_state.js v2.3
* To use with NlsTree Professional only.
* Copyright 2005-2006, addobject.com. All Rights Reserved
* Author Jack Hermanto, www.addobject.com
*/

function nls_setCookie(key, value, expire) {
  if (value==null) return;
  var v = value;
  if (v!="") v=escape(v);
  document.cookie = escape(key)+"="+ v + (expire?"; expires="+expire:"");
}

function nls_getCookie(key) {
  if (document.cookie) {
    var cp=document.cookie.split(";");
    var c=null;
    for (var i=0; i<cp.length; i++) {
      c=cp[i].split("=");
      if (unescape(c[0].replace(/\s*/gi,""))==key) { return (c.length>1?unescape(c[1]):""); }
    }
  }
  return ""; 
}

function nls_removeCookie(key) {
  nls_setCookie(key, "-1", "Fri, 31 Dec 1999 23:59:59 GMT;");
}

function nls_addExpandedId(key, nId) {
  var v=nls_getCookie(key);
  if (v=="") {
    v="/"+nId+"/";
  } else {
    if (v.indexOf("/"+nId+"/")==-1) v=v+nId+"/";
  }
  nls_setCookie(key, v);
}

function nls_delExpandedId(key, nId) {
  var v=nls_getCookie(key);
  var idx=v.indexOf("/"+nId+"/");
  var rgx=new RegExp("/"+nId, "gi");
  if (idx > -1) {
    v=v.replace(rgx, "");
    if (v=="/") v="";
  }
  nls_setCookie(key, v);
}

function nls_maintainNodeState(tId, rt) {
  var t=nlsTree[tId];
  var exps=nls_getCookie(tId+"_ndstate");
  if (exps!="") {
    var aexp=exps.split("/"); var nd=null;
    for (var i=0; i<aexp.length; i++) {
      if (aexp[i]!="") {
        nd=t.getNodeById(aexp[i]);
        if (nd) {
          if (rt) {
            t.expandNode(aexp[i]);
          } else {
            nd.exp=true;
          }        
        }
      }
    }
  }
}

NlsTree.prototype.resetState=function() {
  nls_removeCookie(this.tId+"_ndstate");
  nls_removeCookie(this.tId+"_selnd");
}