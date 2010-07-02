/**
* nlstreeext_htm.js v1.0
* Copyright 2005-2006, addobject.com. All Rights Reserved
* Author Jack Hermanto, www.addobject.com
*/

/*Create new tree instances and load from html.*/
NlsTreeUtil.loadFromHTMLById=function(hId, rep) {
  var r=NlsGetElementById(hId);
  return NlsTreeUtil.loadFromHTML(r, rep);
};

NlsTreeUtil.loadFromHTML=function(r, rep) {
    var ch, t=new NlsTree("htmlTree_"+r.id), o=t.opt;
    for(var i=0;i<r.childNodes.length;i++) {
      ch=r.childNodes[i];
      if(ch.nodeType!=1) continue;
      switch(ch.nodeName.toUpperCase()){
        case "INPUT":
          var vl=ch.value.split(";"), kvp, v;
          for (var j=0;j<vl.length;j++){
            kvp=vl[j].split("="); v=kvp[1];
            switch(kvp[0]) {
              case "enbscroll":o.enbScroll=(v=="true");break;
              case "width":o.width=v; break;
              case "height":o.height=v; break;
              case "trg": o.trg=v; break;
              case "stlprf": o.stlprf=v; break;
              case "sort": o.sort=v; break;
              case "icon": o.icon=(v=="true"); break;
              case "check": o.check=(v=="true"); break;
              case "editable": o.editable=(v=="true"); break;
              case "selrow": o.selRow=(v=="true"); break;
              case "editkey": o.editKey=v; break;
              case "oneexp": o.oneExp=(v=="true"); break;
              case "enablectx": o.enableCtx=(v=="true"); break;
              case "oneclick": o.oneClick=(v=="true"); break;
              case "mntstate": o.mnState=(v=="true"); break;
              case "icassel": o.icAsSel=(v=="true"); break;
              case "checkincsub": o.checkIncSub=(v=="true"); break;
              case "checkparents": o.checkParents=(v=="true"); break;
              case "checkonleaf": o.checkOnLeaf=(v=="true"); break;
              case "hideroot": o.hideRoot=(v=="true"); break;
              case "indent": o.indent=(v=="true"); break;
              case "showexpdr": o.showExpdr=(v=="true"); break;
              case "renderondemand": o.renderOnDemand=(v=="true"); break;              
              case "evdblclick": o.evDblClick=(v=="true"); break;
              case "evctxmenu": o.evCtxMenu=(v=="true"); break;
              case "evmouseup": o.evMouseUp=(v=="true"); break;
              case "evmousedown": o.evMouseDown=(v=="true"); break;
              case "evmousemove": o.evMouseMove=(v=="true"); break;
              case "evmouseout": o.evMouseOut=(v=="true"); break;
              case "evmouseover": o.evMouseOver=(v=="true"); break; 
            }
          }          
          break;
        case "UL":t.$buildTreeFromHTML(ch, this.rt);break;
      }
    }
    if(rep==true) {t.render(hId);} else {r.style.display="";t.render();}
    return t;
};


/*
*parse the html and construct the tree from html
*n=ul element, p=parent node, null if root.
*/
NLSTREE.$buildTreeFromHTML=function(n, p) {
  var li=null, t, prId, nNd;
  if(!p) p="root";
  for(var i=0;i<n.childNodes.length;i++){
    li=n.childNodes[i];
    if(li.nodeType!=1 || li.nodeName.toUpperCase()!="LI") continue;
    prId=null;
    for(var j=0;j<li.childNodes.length;j++){
      t=li.childNodes[j];
      if(t.nodeType!=1)continue;
      switch(t.nodeName.toUpperCase()) {
        case "A":
          nNd=this.add(t.id, p, t.innerHTML, t.href, t.getAttribute("ic"), $aonevl(t.getAttribute("exp"), false), $aonevl(t.getAttribute("chk")), null, t.title);
          if(t.target) nNd.trg=t.target;
          prId=nNd.orgId;
          break;
        case "INPUT":
          var vl=t.value.split(";"), v=null;
          for(var i=0;i<vl.length;i++) {
            v=vl[i].split("=");
            switch(v[0]) {
              case "ic": nNd.setIcon(v[1]);break;
              case "exp":nNd.exp=(v[1]=="true");break;
              case "chk":nNd.chk=(v[1]=="true");break;
              case "xtra": nNd.xtra=v[1];break;
              case "trg":nNd.trg=v[1];break;
            }
          }
          break;
        case "UL": 
          this.$buildTreeFromHTML(t, prId); 
          break;
      }
    }
  };
};

NLSTREE.loadFromHTML=function(hId, prId) {
  var r=NlsGetElementById(hId);
  var pr=(prId?this.getNodeById(prId):this.rt);
  for(var i=0;i<r.childNodes.length;i++) {
    if(r.childNodes[i].nodeType!=1) continue;
    this.$buildTreeFromHTML(r.childNodes[i], (pr?pr:this.rt));
    return;
  }
  return null;
};

function $aonvl(v, c) { if(v)return v; else return c; };
function $aonevl(v, c) { if(!v || v=="")return c; else return v; };
