/**
** nlstreeext_ctx.js v2.3
** To use with NlsTree Professional only.
** Copyright 2005-2006, www.addobject.com
** Note: nlstree.js is required
*/

var nlsClipboard = new NlsClipboard();
function NlsClipboard(act, cnt) {
  this.clAction=act;
  this.clContent=cnt;
  this.consume = function() {
    this.clAction=null;
    this.clContent=null; /*array of sel nodes*/
  }
  return this;
}

function isInClipboard(id) {
  return _isInArray(id, nlsClipboard.clContent);
}

function _isInArray(id, arr) {
  if (arr==null) return false;
  for (var i=0; i<arr.length;i++) {
    if (arr[i].id==id) return true;
  }
  return false;
}

function cut(nd) {
  nlsClipboard.clAction="CUT";
  nlsClipboard.clContent=nd;
}

function copy(nd) {
  nlsClipboard.clAction="COPY";
  nlsClipboard.clContent=[];
  for (var i=0;i<nd.length; i++) {
    var newNd = new NlsNode("", nd[i].capt, nd[i].url);
    newNd.ic = nd[i].ic;
    newNd.xtra = nd[i].xtra;
    newNd.chk = nd[i].chk;
    newNd.ctxMenu = nd[i].CtxMenu;
    nlsClipboard.clContent[i]=newNd;
  }
}

function insertBefore(tree, nd) {
  if (!nlsClipboard || !nlsClipboard.clAction) return;
  var clCnt=nlsClipboard.clContent;
  switch(nlsClipboard.clAction) {
    case "CUT":
      var rNd = tree.ctx_moveChild(clCnt, nd,  2);  
      if (rNd) { if (rNd.length==1) {tree.selectNode(rNd[0].id);}; nlsClipboard.consume(); }
      break;
    case "COPY":
      var newNd = tree.ctx_appendChild(clCnt, nd, 2);
      //if (newNd) tree.selectNode(newNd.id);
      break;
  }
}

function insertAfter(tree, nd) {
  if (!nlsClipboard || !nlsClipboard.clAction) return;
  var clCnt=nlsClipboard.clContent;
  switch(nlsClipboard.clAction) {
    case "CUT":
      var rNd = tree.ctx_moveChild(clCnt, nd,  3);
      if (rNd) { if (rNd.length==1) {tree.selectNode(rNd[0].id);}; nlsClipboard.consume(); }
      break;
    case "COPY":
      var newNd = tree.ctx_appendChild(clCnt, nd, 3);
      //if (newNd) tree.selectNode(newNd.id);
      break;
  }
}

function insertAsChild(tree, nd) {
  if (!nlsClipboard || !nlsClipboard.clAction) return;
  var clCnt=nlsClipboard.clContent;
  switch(nlsClipboard.clAction) {
    case "CUT":
      var rNd = tree.ctx_moveChild(clCnt, nd, 1);
      if (rNd) { if (rNd.length==1) {tree.selectNode(rNd[0].id);}; nlsClipboard.consume(); }
      break;
    case "COPY":
      var newNd = tree.ctx_appendChild(clCnt, nd, 1);
      tree.expandNode(nd.orgId);
      //if (newNd) tree.selectNode(newNd.id);
      break;
  }
}

NLSTREE.ctx_liveAdd = function (prId) {
  var selNode=this.nLst[this.genIntId(prId)];
  var newNode = this.append(
      "",
      selNode.orgId,
      "New Node",
      "",
      "",
      false
  );
  this.expandNode(selNode.orgId);

  this.selectNode(newNode.id);
  this.liveNodeEditStart(newNode.id);  
}

NLSTREE.ctx_unloadChild = function(src) {
  var pr = src.pr;
  if (pr.lc.equals(src)) pr.lc=src.pv; 
  if (pr.fc.equals(src)) pr.fc=src.nx;
  if (src.pv!=null) src.pv.nx=src.nx; 
  if (src.nx!=null) src.nx.pv=src.pv;
  src.nx=null;src.pv=null;src.pr=null;
  if (this.selNd) { this.selNd=null; this.selElm=null; }
  if (this.opt.multiSel) { this.msRemove(src.orgId); }
}

NLSTREE.ctx_appendChild = function (srcs, dest, type) {
  if (!srcs || !dest || srcs.length==0) return;
  var newNd = null; var src=null;
  for (var i=0;i<srcs.length;i++) {
    src=srcs[i];
    switch(type) {
      case 1:
        newNd = this.append(src.orgId, dest.orgId, src.capt, src.url, (src.ic?src.ic.join(","):""), src.exp, src.chk, src.xtra);
        if (src.ctxMenu) this.setNodeCtxMenu(newNd.orgId, src.ctxMenu);
        break;
      case 2:
        if (dest.equals(this.rt)) return;
        var newNd = new NlsNode("int"+ (++this.nCnt), src.capt, src.url, (src.ic?src.ic.join(","):""), src.exp, src.chk, src.xtra);
        newNd.id = this.genIntId(newNd.orgId);
        this.nLst[newNd.id] = newNd;

        newNd.pr=dest.pr;
        if (dest.pv==null) { dest.pv=newNd; dest.pr.fc=newNd; } else { newNd.pv=dest.pv; dest.pv.nx=newNd; dest.pv=newNd; } newNd.nx=dest;
        this.reloadNode(dest.pr.orgId);
        break;
      case 3:
        if (dest.equals(this.rt)) return;
        var newNd = new NlsNode("int"+ (++this.nCnt), src.capt, src.url, (src.ic?src.ic.join(","):""), src.exp, src.chk, src.xtra);
        newNd.id = this.genIntId(newNd.orgId);
        this.nLst[newNd.id] = newNd;

        newNd.pr=dest.pr;
        if (dest.nx==null) { dest.nx=newNd; dest.pr.lc=newNd; } else { newNd.nx=dest.nx; dest.nx.pv=newNd; dest.nx=newNd; } newNd.pv=dest;
        this.reloadNode(dest.pr.orgId);
        break;
    }
  }
  return newNd;
}

//type: 1 append child 2: insert before, 3: insert after
NLSTREE.ctx_moveChild = function (src, dest, type) {
  if (!src || !dest || src.length==0) return;  
  if (isInClipboard(dest.id)) return;

  switch (type) {
    case 1:
      if (isInClipboard(this.rt.id)) return;
      for (var i=0; i<src.length; i++) { if (dest.equals(src[i].pr)) return false; }

      var tmp=dest;
      while(tmp.pr) { if (isInClipboard(tmp.id)) return; tmp=tmp.pr; }
      
      for (var i=0; i<src.length; i++) {
        /*unreference source node*/
        var srcPr=src[i].pr;
        this.ctx_unloadChild(src[i]);
        this.reloadNode(srcPr.orgId);

        src[i].pr=dest;
        if (dest.lc==null) {dest.fc=src[i];dest.lc=src[i];} else {
          var t=dest.fc;
          if (this.opt.sort!="no") { 
            do { if (this.opt.sort=="asc" ? this.compareNode(t, src[i]) : this.compareNode(src[i], t)) break; t = t.nx;
            } while (t!=null);
            if (t!=null) { if (t.pv==null) { t.pv=src[i]; dest.fc=src[i]; } else { src[i].pv=t.pv; t.pv.nx=src[i]; t.pv=src[i]; } src[i].nx=t; }
          }
          if (this.opt.sort=="no" || t==null) { src[i].pv = dest.lc; dest.lc.nx = src[i]; dest.lc = src[i]; }
        }
      }
      this.reloadNode(dest.orgId);
      this.expandNode(dest.orgId);
      break;
    case 2: /*before*/
    case 3: /*after*/
      var sCh="pv"; var dCh="nx"; var ch="lc";
      if (type==2) {sCh="nx"; dCh="pv"; ch="fc";}
      if (dest.equals(this.rt)) return;
      for (var i=0; i<src.length; i++) { if (src[i][sCh] && dest.equals(src[i][sCh])) return; }

      for (var i=0; i<src.length; i++) {
        var srcPr=src[i].pr;
        this.ctx_unloadChild(src[i]);
        this.reloadNode(srcPr.orgId);

        src[i].pr=dest.pr;
        if (dest[dCh]==null) { dest[dCh]=src[i]; dest.pr[ch]=src[i]; } else { src[i][dCh]=dest[dCh]; dest[dCh][sCh]=src[i]; dest[dCh]=src[i]; } src[i][sCh]=dest;
      }
      this.reloadNode(dest.pr.orgId);
      break;
  }
  return src;
}


