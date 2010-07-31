/**
* nlstreeext_frm_inc.js v1.0
* Copyright 2005-2006, addobject.com. All Rights Reserved
* Author Jack Hermanto, www.addobject.com
*/
NlsTree.AJAX={};
NlsTree.AJAX.conPool={};
NlsTree.AJAX.createRequest=function(id) {
  var c=NlsTree.AJAX.conPool["id"];

  if (!c) {
    c=new NlsTree.AJAX.NlsConnection();
    c.callBack=function(){};
    var frm=NlsGetElementById("comfrm_"+id);
    if (!frm) {
      var frm=document.createElement("iframe");
      frm.width=0; frm.height=0; frm.frameBorder=0;
      frm.name="comfrm_" + id;
      frm.id="comfrm_" + id;
      document.body.insertBefore(frm, document.body.childNodes[0]);
      var w=frm.contentWindow;
      if(!w) w=document.frames["comfrm_" + id];
      w.onload=function() {c.callBack();};
    }
    c.id=id;
    c.frm=frm;
    c.status="idle";
    NlsTree.AJAX.conPool[id]=c;
  }
  return c;
};

NlsTree.AJAX.closeRequest=function(id) {
  var c=NlsTree.AJAX.conPool[id];
  if (!c) return;
  
  c.status="idle";
  c.clearTimeout();
  
  document.body.removeChild(c.frm);
  c.frm=null;
  
  NlsTree.AJAX.conPool[id]=null;
  delete NlsTree.AJAX.conPool[id];
};

NlsTree.AJAX.NlsConnection=function() {
  this.id; this.tmId=null; this.inId=null;
  this.status=""; this.frm=null; this.win=null; this.anim=null;
  
  var me=this;  
  this.setTimeout=function(tm) {
    this.tmId=window.setTimeout(function() {NlsTree.AJAX.closeRequest(me.id);}, tm);
  };
  
  this.clearTimeout=function() {
    if (this.tmId) { window.clearTimeout(this.tmId); this.tmId=null; }
  };
};

NLSTREE.ajaxLoadChildNodes=function(id) {
  var me=this;
  var nd=this.getNodeById(id);
  var req=NlsTree.AJAX.createRequest(id);
 
  window.setTimeout(
    function() {
      req.setTimeout(60000);
      req.callBack=function() {
        nd.loaded=2;
        NlsTree.AJAX.closeRequest(id);
      };
      req.frm.src=$nlsAddParam((nd.chUrl?nd.chUrl:me.chUrl), "nid="+id);    
    }, 
    10);
  
  //animate icon here, change the icon or text.
  if(this.opt.icon) {
    var sElm=NlsGetElementById(nd.id);
    var ic=sElm.childNodes[0].childNodes[0].childNodes[0].childNodes[1];
    if (ic.childNodes.length==2) {ic=ic.childNodes[1];} else {ic=ic.childNodes[0];} 
    ic.src=nlsTreeIc[this.ico.lod].src;
  }// else {
  var oNd=$getAnchor(NlsGetElementById(nd.id)); 
  oNd.innerHTML="(Loading...) - " + oNd.innerHTML;
  //};
  nd.loaded=1; //loading
};

NLSTREE.setServerLoad=function(id, url) {
  n=this.getNodeById(id);

  //TODO: Remove legacy nodes in tree from NEEScentral
  if(n!=null){
    n.svrLoad=true;
    n.loaded=0;
    n.chUrl=url;
  }
};

NlsNode.prototype.loaded=0;
NlsNode.prototype.chUrl=null;

/*utility*/
function $nlsAddParam(url, par) {
  var s=(url.indexOf("?")!=-1?"&":"?");
  return url+s+par;
};