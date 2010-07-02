/**
* nlstreeext_inc.js v1.0
* Copyright 2005-2006, addobject.com. All Rights Reserved
* Author Jack Hermanto, www.addobject.com
*/
NLSTREE.ajaxLoadChildNodes=function(id) {
  var nd=this.getNodeById(id);
  var req=NlsTree.AJAX.createRequest();
  var url=$nlsAddParam((nd.chUrl?nd.chUrl:this.chUrl), "nid="+id);
  var me=this;
  req.open("get", url, true);
  req.onreadystatechange=function() {
    if(req.readyState==4) {
      if(req.status==200 || req.status==304) {
        var de=req.responseXML.documentElement;
        if(!de||de.childNodes.length==0) { //if no submenu
        } else {
          me.removeChilds(id);
          me.addChildNodesXML(de, true);
          me.expandNode(id);
        }
        nd.loaded=2;
      }
    }
  };
  //animate icon here, change the icon or text.
  if(this.opt.icon) {
    var sElm=NlsGetElementById(nd.id);
    var ic=sElm.childNodes[0].childNodes[0].childNodes[0].childNodes[1];
    if (ic.childNodes.length==2) {ic=ic.childNodes[1];} else {ic=ic.childNodes[0];} 
    ic.src=nlsTreeIc[this.ico.lod].src;
  }// else {
  var oNd=$getAnchor(NlsGetElementById(nd.id)); 
  oNd.innerHTML="(Loading...) - "+oNd.innerHTML;
  //};
  nd.loaded=1; //loading
  req.send(null);
};

NLSTREE.setServerLoad=function(id, url) {
  n=this.getNodeById(id);
  n.svrLoad=true;
  n.loaded=0;
  n.chUrl=url;
};

NlsNode.prototype.loaded=0;
NlsNode.prototype.chUrl=null;

/*utility*/
function $nlsAddParam(url, par) {
  var s=(url.indexOf("?")!=-1?"&":"?");
  return url+s+par;
};