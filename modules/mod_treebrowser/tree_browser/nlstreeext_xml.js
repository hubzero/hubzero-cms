/**
* nlstreeext_xml.js v2.3
* To use with NlsTree Professional only.
* Copyright 2005-2006, addobject.com. All Rights Reserved
* Author Jack Hermanto, www.addobject.com
*/

NlsTree.AJAX={};

//NlsNode extension functions for XML
NlsNode.prototype.xmlOpn=function() {
  if(this.custom) {
    return "<customnode id=\""+this.orgId+"\">"+this.custom+"</customnode>";
  } else {
    return "<node id=\""+this.orgId+"\" caption=\""+this.capt+"\" url=\""+this.url+"\" ic=\""+(this.ic==null?"":this.ic)+"\" exp=\""+this.exp+"\" chk=\""+this.chk+"\" cststyle=\""+this.cstStyle+"\" target=\""+this.trg+"\" title=\""+this.title+"\">";
  }
};

NlsNode.prototype.xmlCls=function() { return "</node>"; };

//StdOpt extension function for XML
StdOpt.$prop=["enbScroll","height","width","trg","stlprf","sort","icon","check","editable","selRow","editKey",
"oneExp","enableCtx","oneClick","mntState","icAsSel","checkIncSub","checkOnLeaf","hideRoot","indent","evDblClick",
"evCtxMenu","evMouseUp","evMouseDown","evMouseMove","evMouseOut","evMouseOver"];

StdOpt.prototype.toXML=function() {
  var s=[], j=0, pr=StdOpt.$prop;
  s[j++]="<options>\n"; 
  for(var i=0;i<pr.length;i++) {
    s[j++]="  <opt name=\""+(pr[i]=="trg"?"target":pr[i].toLowerCase())+"\" value=\""+this[pr[i]]+"\"></opt>\n";
  }
  s[j++]="</options>\n";
  return s.join("");
};

//StdIco extension function for XML
StdIco.$prop=["pnb","pb","pnl","mnb","mb","mnl","opf","clf","chd",
  "rot","lnb","lb","lin", "bln","lod"];

StdIco.prototype.toXML=function() {
  var s=[], j=0, pr=StdIco.$prop;
  s[j++]="<icons>\n";
  for(var i=0;i<pr.length;i++) {
    s[j++]="  <ico name=\""+pr[i]+"\" value=\""+this[pr[i]]+"\"></ico>\n";  
  }
  s[j++]="</icons>\n";
    
  return s.join("");
};

//NlsTree extension functions for XML
NLSTREE.nodeXML = function(sNd) {
  sNd=(sNd==null?this.rt:sNd);
  var n=sNd; var spc="";
  while (n != null && !n.equals(this.rt)) { spc+="  "; n=n.pr;}    
  var s=(spc+sNd.xmlOpn()+"\n");
  if (sNd.fc !=null) {
      var chNode = sNd.fc;
      do {
          s+=this.nodeXML(chNode);
          chNode = chNode.nx;
      } while (chNode != null)
  }
  s+=(spc+sNd.xmlCls()+"\n");
  return s;
};

NLSTREE.toXML=function() {
  return "<tree id=\""+this.tId+"\">\n" + this.opt.toXML() + this.ico.toXML() + this.nodeXML(this.rt) + "</tree>";
};

function nls_addNodeXML(tree, prnId, xnd) {
  for (var i=0; i<xnd.childNodes.length; i++) {
    var nd=xnd.childNodes[i];
    if (nd.nodeType!=1) continue;
    if(nd.nodeName.toUpperCase()=="CUSTOMNODE") {
      tree.addCustomNode(nd.getAttribute("id"), prnId, nd.childNodes[0].nodeValue);
      continue;
    }    
    var newNd=tree.add(nd.getAttribute("id"), prnId, nd.getAttribute("caption"), nd.getAttribute("url"), nd.getAttribute("ic"), nd.getAttribute("exp")=="true", nd.getAttribute("chk")=="true", null, nd.getAttribute("title"));
    newNd.cstStyle=nd.getAttribute("cststyle");
    newNd.trg=nd.getAttribute("target");
    if(nd.getAttribute("svrload")=="true") {
      tree.setServerLoad(nd.getAttribute("id"), (nd.getAttribute("churl")=="null"?null:nd.getAttribute("churl")));
    }
    if (nd.firstChild!=null) nls_addNodeXML(tree, newNd.orgId, nd);
  }
};

NLSTREE.addNodesXML=function(prn, rn, reload) {
  if (rn==null) return;
  if(rn.nodeName.toUpperCase()=="CUSTOMNODE") {
    this.addCustomNode(rn.getAttribute("id"), prn, rn.childNodes[0].nodeValue);    
  } else {
    var newNd=this.add(rn.getAttribute("id"), prn, rn.getAttribute("caption"), rn.getAttribute("url"), rn.getAttribute("ic"), rn.getAttribute("exp")=="true", rn.getAttribute("chk")=="true", null, rn.getAttribute("title"));
    newNd.cstStyle=rn.getAttribute("cststyle");
    newNd.trg=rn.getAttribute("target");

    nls_addNodeXML(this, newNd.orgId, rn);
  }
  if (reload) {
    this.reloadNode(prn);
  }
};

NLSTREE.addNodesXMLString=function(prn, xml, reload) {
  var xmlDom=nls_parseXML(xml);
  if (xmlDom) this.addNodesXML(prn, xmlDom.documentElement, reload);
};

NLSTREE.addChildNodesXML=function(rn, reload, updateParent) {
  if (rn==null) return;
  if (updateParent) {
    var nd=this.getNodeById(rn.getAttribute("id"));
    nd.capt=rn.getAttribute("caption");
    nd.url=rn.getAttribute("url");
    nd.ic=rn.getAttribute("ic")==""?null:rn.getAttribute("ic").split(",");
    nd.exp=rn.getAttribute("exp")=="true";;
    nd.chk=rn.getAttribute("chk")=="true";
    nd.cstStyle=rn.getAttribute("cststyle");
    nd.trg=rn.getAttribute("target")=="null"?null:rn.getAttribute("target");
    nd.title=rn.getAttribute("title");
  }
  nls_addNodeXML(this, rn.getAttribute("id"), rn);
  if (reload) {
    this.reloadNode(rn.getAttribute("id"));
  }
};

NLSTREE.addChildNodesXMLString=function(xml, reload, updateParent) {
  var xmlDom=nls_parseXML(xml);
  if (xmlDom) this.addChildNodesXML(xmlDom.documentElement, reload, updateParent);
};

NLSTREE.setOptionsByXML=function(xopt) {
  var o=this.opt;
  for (var i=0; i<xopt.childNodes.length; i++) {
    var n=xopt.childNodes[i];
    if (n.nodeType!=1) continue;
    var v=n.getAttribute("value");
    switch (n.getAttribute("name")) {
      case "trg":
      case "target": o.trg=v; break;
      case "enbscroll": o.enbScroll=(v=="true"); break;
      case "height": o.height=v;break;
      case "width": o.width=v;break;
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
};

NLSTREE.setIconsByXML=function(xico) {
  var c=this.ico;
  for (var i=0; i<xico.childNodes.length; i++) {
    var n=xico.childNodes[i];
    if (n.nodeType!=1) continue;
    c[n.getAttribute("name")]=n.getAttribute("value");
  }
  this.useIconSet(c);
};

//load tree from XML Document.
NLSTREE.loadFromXML=function(xml) {
  var rt=xml;

  //set tree options
  for (var i=0; i<rt.childNodes.length; i++) {
    var n=rt.childNodes[i];
    if (n.nodeType==1) {
      if (n.nodeName=="options") {
        this.setOptionsByXML(n);
      } else
      if (n.nodeName=="icons") {
        this.setIconsByXML(n);
      } else 
      if (n.nodeName=="node") {
        //add root node
        this.addNodesXML("root", n, false);
      }
    }
  }
};

//load tree from XML string
NLSTREE.loadFromXMLString=function(sXML) {
  var txml=nls_parseXML(sXML);
  this.loadFromXML(txml.documentElement);
};

//load tree from xml file
NLSTREE.loadFromXMLFile=function(fName) {
  var me=this;
  var req=NlsTree.AJAX.createRequest();
  req.open("get", fName, false);
  req.send(null);
  this.loadFromXML(req.responseXML.documentElement);
};

function nls_parseXML(sXml) {
  var xmlDom=null;
  if (typeof(DOMParser) != "undefined") {
    var parser=new DOMParser(); //gecko browser xml dom
    xmlDom=parser.parseFromString(sXml, "text/xml");
  } else {
    xmlDom=nls_createXMLDoc();
    xmlDom.loadXML(sXml);
  }
  return xmlDom;
};

//create empty xml doc
function nls_createXMLDoc() {
  var ieXML=["MSXML2.DOMDocument.5.0", 
    "MSXML2.DOMDocument.4.0", "MSXML2.DOMDocument.3.0", 
    "MSXML2.DOMDocument", "Microsoft.XmlDom"];
  var xmlDom=null;
  if(nls_isIE) {
    for (var i=0;i<ieXML.length;i++) {
      try {
        xmlDom=new ActiveXObject(ieXML[i]);
        break;
      } catch (e) {}
    }
  } else {
    xmlDom=document.implementation.createDocument("","",null);
  }
  return xmlDom;
};

NlsTree.AJAX.createRequest=function() {
  if (typeof XMLHttpRequest != "undefined") { //for mozilla
    return (new XMLHttpRequest());
  } else {
    var arrObj=["MSXML2.XMLHttp.5.0", "MSXML2.XMLHttp.4.0", 
      "MSXML2.XMLHttp.3.0", "MSXML2.XMLHttp", "Microsoft.XMHttp"];
    var req=null;
    for (var i=0;i<arrObj.length;i++) {
      try {
        req=new ActiveXObject(arrObj[i]);
        return req;
      } catch (e) { }     
    }
  }
};