/**
* nlstree.js v2.0(LIMITED)
* ==============================
* ====NOT FOR COMMERCIAL USE====
* ==============================
* Author Jack Hermanto, www.addobject.com
* Copyright 2005-2006, addobject.com. All Rights Reserved
*/
var nlsTree=new Object();
var nlsTreeIc=new Object();
var nls_isIE=(window.navigator.userAgent.indexOf("MSIE") >=0);
var nls_isSafari=(window.navigator.userAgent.indexOf("Safari") >=0);
var nls_isOpera=(window.navigator.userAgent.indexOf("Opera") >=0);
if(nls_isOpera) nls_isIE=false;
var NlsTreeStd=NlsTree.prototype;

var ico_e = "/tree_browser/img/e.gif";
var ico_t = "/tree_browser/img/t.gif";
var ico_p = "/tree_browser/img/p.gif";
var ico_r = "/tree_browser/img/r.gif";
var ico_f = "/tree_browser/img/folder.gif";

var ico_ec = "/tree_browser/img/e_color.gif";
var ico_tc = "/tree_browser/img/t_color.gif";
var ico_pc = "/tree_browser/img/p_color.gif";
var ico_sc = "/tree_browser/img/s_color.gif";
var ico_rc = "/tree_browser/img/r_color.gif";

var ico_folder = "/tree_browser/img/folder.gif, /tree_browser/img/folderopen.gif";

function NlsTree(tId){
  this.tId=tId;
  this.opt=new StdOpt();
  this.nLst=new Object();
  this.rt=null;
  this.selNd=null;
  this.selElm=null;
  this.nCnt=0;
  this.defImgPath="";
  if(nlsTree[this.tId]!=null){
    alert("The tree with id " + this.tId + " already exist, please change the tree id.");
    return null;
  }

  nlsTree[this.tId]=this;

  var allScs=(document.getElementsByTagName ? document.getElementsByTagName("SCRIPT"): document.scripts);

  for(var i=0; i<allScs.length; i++){
    if(allScs[i].src.toLowerCase().indexOf("nlstree.js")>=0){
      this.defImgPath=allScs[i].src.replace(/nlstree.js/gi, "img/");
      break;
    }
  }

  this.useIconSet(new StdIco(this.defImgPath));
  this.renderer=new DefRenderer(tId);
  return this;
};

function StdIco(path){
  this.pnb=path+"plusnb.gif";
  this.pb=path+"plusb.gif";
  this.mnb=path+"minusnb.gif";
  this.mb=path+"minusb.gif";
  this.opf=path+"p_color.gif";
  this.clf=path+"p.gif";
  this.chd=path+"leaf.gif";
  this.rot=path+"root.gif";
  this.lnb=path+"lineang.gif";
  this.lb=path+"lineints.gif";
  this.lin=path+"line.gif";
  this.bln=path+"blank.gif";
  this.toString=function(){return "Standard Icons"};
  return this;
};

function StdOpt(){
  this.trg="_self";
  this.stlprf="";
  this.icon=true;
  this.selRow=false;
  this.oneClick=false;
  this.mntState=false;
  this.icAsSel=false;
  this.evDblClick=false;
  return this;
};

function NlsNode(orgId, capt, url, ic, exp, xtra, title){
  this.orgId=orgId;
  this.id="";
  this.capt=capt;
  this.url=(url==null || url=="") ? "" : url;
  this.ic=(ic==null || ic=="")?null:ic.split(",");
  this.exp=exp==null ? false : exp;
  this.xtra=xtra==null ? false : xtra;
  this.cstStyle="";
  this.trg=null;
  this.title=title==null?capt:title;
  if(this.ic){
    preloadIcon(this.ic[0]);
    if(this.ic.length>1) preloadIcon(this.ic[1]);
  };

  this.nx=null;
  this.pv=null;
  this.fc=null;
  this.lc=null;
  this.pr=null;
  this.equals=function(nd){return(this.id == nd.id);};
};

NlsTreeStd.genIntId=function(id){return this.tId+id;};

NlsTreeStd.genOrgId=function(intId){return intId.substr(this.tId.length);};

NlsTreeStd.add=function(id, prn, capt, url, ic, exp, xtra, title){
  var nNd=new NlsNode(((id==null||String(id)=="")?("int"+(++this.nCnt)):id), capt, url, ic, exp, xtra, title);
  nNd.id=this.genIntId(nNd.orgId);
  if(this.nLst[nNd.id]!=null){return;}
  if(this.rt==null){this.rt=nNd;}
  else{
    var pnd=this.nLst[this.genIntId(prn)];
    if(pnd==null ){return;}
    nNd.pr=pnd;
    if(pnd.lc==null){
      pnd.fc=nNd;
      pnd.lc=nNd;
    }
    else{
      nNd.pv=pnd.lc;
      pnd.lc.nx=nNd;
      pnd.lc=nNd;
    }
  }
  this.nLst[nNd.id]=nNd;
  return nNd;
};

NlsTreeStd.genTree=function(){return this.renderer.genTree();};

NlsTreeStd.render=function(plc){
  this.renderer.render(plc);
  var sid=null;
  if(this.opt.mntState && nls_getCookie){
    var sid=nls_getCookie(this.tId+"_selnd");
    nls_maintainNodeState(this.tId, true);
  }
  if(sid && sid!="") this.selectNodeById(sid);
};

function DefRenderer(tId){
  var tr=nlsTree[tId];
  this.rat=new Object();
  this.dsp=new Object();

  this.initRender=function(){
    var opt=tr.opt;
    var ico=tr.ico;
    var ev="onclick=\"nls_c2(event, '"+tId+"','@id')\" ";
    if(opt.evDblClick) ev+="ondblclick=\"nls_c3(event, '"+tId+"', '@id')\" ";
    var ex=["<img src='@expdr' onclick=\"nls_c1(event, '"+tId+"', '@id')\"/>", "<img src='@expdr'/>", "<img src='@ic' "+ev+"/>"];
    this.rat["cnt"]=["<div id='@id' class='"+opt.stlprf+"row' title=\"@title\">", "</div><div style='display:block' id='ch_@id'>", "</div><div style='display:none' id='ch_@id'>"];
    this.rat["ex"]=[[[ex[1].replace(/@expdr/gi,ico.lnb),ex[1].replace(/@expdr/gi,ico.lb)],[ex[1].replace(/@expdr/gi,ico.lnb),ex[1].replace(/@expdr/gi,ico.lb)]], [[ex[0].replace(/@expdr/gi,ico.pnb),ex[0].replace(/@expdr/gi,ico.pb)],[ex[0].replace(/@expdr/gi,ico.mnb),ex[0].replace(/@expdr/gi,ico.mb)]]];
    this.rat["mn"]=["<table cellspacing=0 cellpadding=0><tr><td nowrap>","</td><td nowrap>", "</td><td nowrap>", "</td></tr></table>"];
    this.rat["ics"]=ex[2];
    this.rat["ic"]=[[ex[2].replace(/@ic/gi, ico.chd), ex[2].replace(/@ic/gi, ico.chd)], [ex[2].replace(/@ic/gi, ico.clf), ex[2].replace(/@ic/gi,(tr.opt.icAsSel?ico.clf:ico.opf))]];
    this.rat["rt"]=ex[2].replace(/@ic/gi, ico.rot);
    this.rat["ln"]=["<img src=\""+ico.bln+"\"/>", "<img src=\""+ico.lin+"\"/>"];
    this.rat["ac"]=["<a class=\"@nstyle\" style='display:block' href=\"javascript:void(0);\" "+ev+" >@capt</a>", "<a class=\"@nstyle\" style='display:block' target='@trg' "+ev+" href=\""];
    this.rat["st"]=[opt.stlprf+"node", opt.stlprf+"prnnode"];
    this.rat["wt"]=["<div style='float:right; font-family:arial;font-size:7pt;color:#666666;padding-top:5px'><br/><br/><br/><br/>&nbsp;&nbsp;NEEScentral&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/></div>"];
  };

  this.genANode=function(sNd){
    var n=sNd.pr;
    this.dsp["ln"]="";
    this.dsp["ip"]="";
    this.dsp["ic"]="";
    while(n!=null && !n.equals(tr.rt)){
      this.dsp["ln"]=this.rat["ln"][(n.nx!=null?1:0)]+this.dsp["ln"];
      n=n.pr;
    }
    var fc=1,ex=0,nx=1, rt=0;
    if(!sNd.fc){
      fc=0;
      sNd.exp=false;
    };

    if(sNd.exp) ex=1;
    if(!sNd.nx) nx=0;
    if(sNd.id==tr.rt.id) rt=1;
    if(sNd.id!=tr.rt.id){this.dsp["ip"]=this.rat["ex"][fc][ex][nx];}

    if(tr.opt.icon||rt==1){
      if(sNd.ic!=null){this.dsp["ic"]=this.rat["ics"].replace(/@ic/gi,sNd.ic[(!tr.opt.icAsSel && sNd.ic[ex]?ex:0)]);}
      else{
        this.dsp["ic"]=this.rat["ic"][fc][ex];
        if(rt==1) this.dsp["ic"]=this.rat["rt"];
      }
    }

    if(sNd.url){this.dsp["nd"]=this.rat["ac"][1].replace(/@trg/gi,(sNd.trg==null?tr.opt.trg:sNd.trg))+ sNd.url+"\">"+sNd.capt+"</a>";}
    else{this.dsp["nd"]=this.rat["ac"][0].replace(/@capt/gi, sNd.capt);}

    if(sNd.cstStyle!=""){this.dsp["nd"]=this.dsp["nd"].replace(/@nstyle/gi, sNd.cstStyle);}
    else{this.dsp["nd"]=this.dsp["nd"].replace(/@nstyle/gi, this.rat["st"][fc]);}

    return this.rat["mn"][0]+this.dsp["ln"]+this.rat["mn"][1]+this.dsp["ip"]+this.dsp["ic"]+this.rat["mn"][2]+this.dsp["nd"]+this.rat["mn"][3];
  };

  this.genNodes=function(sNd, incpar, wrt){
    var s="";
    if(incpar){s=this.rat["cnt"][0]+this.genANode(sNd)+this.rat["cnt"][(sNd.fc && sNd.exp?1:2)];};

    s=s.replace(/@id/gi,sNd.id).replace(/@title/gi, sNd.title);
    if(wrt) document.write(s);

    if(sNd.fc !=null){
      var chNode=sNd.fc;
      do{
        if(wrt) this.genNodes(chNode, true, wrt);
        else s=s+this.genNodes(chNode, true, wrt);
        chNode=chNode.nx;
      } while(chNode != null)
    }

    if(wrt){
      if(incpar) document.write("</div>");
      return "";
    }
    else{
      s= incpar ?(s+"</div>") : s;
      return s;
    }
  };

  this.genTree=function(){
    this.initRender();
    return this.genNodes(tr.rt, true, false);
  };

  this.render=function(plc){
    if(plc && plc!=""){
      NlsGetElementById(plc).innerHTML="<div id=\""+tId+"\">" + this.genTree() + "</div>";
    }
    else{
      this.initRender();
      document.write("<div id=\""+tId+"\">");
      this.genNodes(tr.rt, true, true);
      document.write(this.rat["wt"]);
      document.write("</div>");
    }
  };
  return this;
};

NlsTreeStd.selNToggle=function(id){
  this.toggleNode(id);
  if(!this.selNd || this.selNd.id!=id) this.selectNode(id);
};

function nls_setStyle(tree, sNd, sElm, selected){
  var prf=(selected?"sel":"");
  if(tree.opt.selRow) sElm.className=tree.opt.stlprf+prf+"row";
  ac=sElm.childNodes[0].childNodes[0].childNodes[0].childNodes[2].childNodes[0];
  ac.className=(sNd.cstStyle!=""?prf+sNd.cstStyle:tree.opt.stlprf+prf+(sNd.fc?"prnnode":"node"));
};

function nls_setNodeIcon(tree, sNd, sElm, selected){
  if(tree.opt.icon){
    ic=sElm.childNodes[0].childNodes[0].childNodes[0].childNodes[1];

    if(ic.childNodes.length==2){ic=ic.childNodes[1];}
    else{ic=ic.childNodes[0];}

    if(selected){
      if(sNd.ic!=null){ic.src=nlsTreeIc[sNd.ic[1]?sNd.ic[1]:sNd.ic[0]].src }
      else{ic.src=nlsTreeIc[(sNd.id==tree.rt.id?tree.ico.rot:(sNd.fc?tree.ico.opf:tree.ico.chd))].src;}
    }
    else{
      if(sNd.ic!=null){ic.src=nlsTreeIc[sNd.ic[0]].src }
      else{ic.src=nlsTreeIc[(sNd.id==tree.rt.id?tree.ico.rot:(sNd.fc?tree.ico.clf:tree.ico.chd))].src;}
    }
  }
};

NlsTreeStd.selectNode=function(id){/*id is internal id*/
  nlsTree.selectedTree=this;
  var ac=null;
  var ic=null;
  var sNd=this.selNd;
  var sElm=this.selElm;
  if(sElm!=null){
    nls_setStyle(this, sNd, sElm, false);
    if(this.opt.icAsSel) nls_setNodeIcon(this, sNd, sElm, false);
  }
  sNd=this.nLst[id];
  this.selNd=sNd;
  this.selElm= NlsGetElementById(id);
  sElm=this.selElm;
  nls_setStyle(this, sNd, sElm, true);
  if(this.opt.icAsSel) nls_setNodeIcon(this, sNd, sElm, true);
  if(this.opt.mntState && nls_setCookie) nls_setCookie(this.tId+"_selnd", sNd.orgId);
};

NlsTreeStd.selectNodeById=function(id){
  var node=this.getNodeById(id);
  if(!node) return;
  var tmp=node;
  while(tmp.pr!=null){
    this.expandNode(tmp.orgId);
    tmp=tmp.pr;
  }
  this.selectNode(node.id);
  /*select the node*/
};

NlsTreeStd.expandNode=function(id){
  var sNd=this.nLst[this.genIntId(id)];
  if(!sNd.exp && sNd.fc) this.toggleNode(sNd.id);
};
NlsTreeStd.collapseNode=function(id){
  var sNd=this.nLst[this.genIntId(id)];
  if(sNd.exp && sNd.fc) this.toggleNode(sNd.id);
};

NlsTreeStd.prepareToggle=function(id){
  var sNd=this.selNd;
  if(sNd==null){
    this.selectNode(id);
    return;
  }
  if(sNd.id==id) return;
  while(sNd!=null && sNd.id!=id){
    sNd=sNd.pr;
  }
  if(sNd==null) return;
  if(sNd.id==id) this.selectNode(id);
};

NlsTreeStd.toggleNode=function(id){
  var nd=NlsGetElementById("ch_" + id);
  var ip=null;
  if(id!=this.rt.id){ip=NlsGetElementById(id).childNodes[0].childNodes[0].childNodes[0].childNodes[1].childNodes[0];}
  var sNd=this.nLst[id];
  if(sNd.exp){
    sNd.exp=false;
    nd.style.display="none";
    if(ip!=null && sNd.fc!=null) ip.src=sNd.nx ? this.ico.pb : this.ico.pnb;
    if(!this.opt.icAsSel) nls_setNodeIcon(this, sNd, NlsGetElementById(id), false);
    if(this.opt.mntState && nls_delExpandedId) nls_delExpandedId(this.tId+"_ndstate", sNd.orgId);
  }
  else{
    sNd.exp=true;
    nd.style.display="block";
    if(ip!=null && sNd.fc!=null) ip.src=sNd.nx ? this.ico.mb : this.ico.mnb;
    if(!this.opt.icAsSel) nls_setNodeIcon(this, sNd, NlsGetElementById(id), true);
    if(this.opt.mntState && nls_addExpandedId) nls_addExpandedId(this.tId+"_ndstate", sNd.orgId);
  }
};

NlsTreeStd.expandAll=function(){
  var treeId=this.tId;
  this.loopTree(this.rt, function(n){if(n.fc) nlsTree[treeId].expandNode(n.orgId);});
};

NlsTreeStd.collapseAll=function(incPr){
  var treeId=this.tId;
  this.loopTree(this.rt, function(n){if(n.fc &&(!nlsTree[treeId].rt.equals(n) || incPr)) nlsTree[treeId].collapseNode(n.orgId);});
};

NlsTreeStd.setNodeStyle=function(id, cls, rt){
  var nd=this.getNodeById(id);
  nd.cstStyle=cls;
  if(rt){
    var oNd=NlsGetElementById("cstl_" + nd.id);
    if(oNd) oNd.className=cls;
  }
};

NlsTreeStd.getNodeById=function(id){return this.nLst[this.genIntId(id)] };
NlsTreeStd.setNodeTarget=function(id, trg){
  var nd=this.nLst[this.genIntId(id)];
  nd.trg=trg;
};

NlsTreeStd.useIconSet=function(icSet){
  this.ico=icSet;
  preloadIcon(this.ico.pnb,this.ico.pb,this.ico.pnl,this.ico.mnb, this.ico.mb,this.ico.mnl,this.ico.opf,this.ico.clf,this.ico.chd, this.ico.rot,this.ico.lnb,this.ico.lb,this.ico.lin,this.ico.bln);
};

NlsTreeStd.loopTree=function(sNd, act){
  act(sNd);

  if(sNd.fc !=null){
    var chNode=sNd.fc;
    do{
      this.loopTree(chNode, act);
      chNode=chNode.nx;
    } while(chNode != null)
  }
};

function nls_c1(e, tId, nId){
  nlsTree[tId].prepareToggle(nId);
  nlsTree[tId].toggleNode(nId);
};

function nls_c2(e, tId, nId){
  var t=nlsTree[tId];
  if(t.opt.oneClick && t.nLst[nId].fc){t.selNToggle(nId);}
  else{t.selectNode(nId);}
  return t.treeOnClick(e, t.genOrgId(nId));
};

function nls_c3(e, tId, nId){
  var t=nlsTree[tId];
  if(t.nLst[nId].fc) t.selNToggle(nId);
  return t.treeOnDblClick(e, t.genOrgId(nId));
};

NlsTreeStd.treeOnClick=function(e, id){return true;};
NlsTreeStd.treeOnDblClick=function(e, id){};

function preloadIcon(){
  var arg=preloadIcon.arguments;
  for(var i=0;i<arg.length;i++){
    if(!nlsTreeIc[arg[i]]){
      nlsTreeIc[arg[i]]=new Image();
      nlsTreeIc[arg[i]].src=arg[i];
    }
  }
};

/**Cross browser related methods*/
function NlsGetElementById(id){
  if(document.all){return document.all(id);}
  else if(document.getElementById){return document.getElementById(id);}
};
