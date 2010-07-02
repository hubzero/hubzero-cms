/**
** nlsctxmenu.js v2.3
** To use with NlsTree Professional only.
** Copyright 2005-2006, addobject.com
*/
var nlsctxmenu = new Object();

function NlsCtxMenuItem(key, capt, url, ico, enb) {
  this.key = key;
  this.intKey = "";
  this.capt = capt;
  this.url = (!url || url==""? "" : url);
  this.ico = ico;
  this.enable=(enb==null?true:enb);
  this.target="";
  return this;
}

function NlsCtxMenu(mId) {
  //private
  this.lsItm=null;
  this.genMenu = genMenu;
  this.reloadMenu=reloadMenu;

  //public
  this.mId = mId;
  this.items = new Object();
  this.container = null;
  this.count=0;
  this.stlprf="";
  this.active = false;
  this.target="";

  this.absWidth=0;
  this.add = addItem;
  this.addSeparator=addSeparator;
  this.enableItem=enableItem;
  this.setItemTarget=setItemTarget;
  this.itemClick=itemClick;
  this.showMenu=showMenu;
  this.hideMenu=hideMenu;
  this.menuOnClick=menuOnClick;
  this.menuOnShow=menuOnShow;
  this.menuOnHide=menuOnHide;

  nlsctxmenu[mId] = this;
  return this;
}

function itemClick(itemId) {
  if (!this.items[itemId].enable) return;
  //hide
  this.hideMenu();
  var ids = itemId.split("_");
  return this.menuOnClick(this.container.selNd, ids[0], ids[1]);
}

function genMenu() {
  var smenu=""; var trg="";
  for (it in this.items) {
    if(!(this.items[it] instanceof NlsCtxMenuItem)) continue;
    if (this.items[it].capt=="-") {
      smenu+="<tr><td class=\""+this.stlprf+"ctxsidebar\" align=\"center\" style=\"font-size:3px\">&nbsp;</td><td style=\"height:7px;vertical-align:middle;padding-top:3px\"><div class=\"ctxseparator\">&nbsp;</div></td></tr>";
    } else {
      trg=this.items[it].target; if (trg=="") trg=this.target;
      smenu+="<tr id=\""+it+"\" onmouseover=\"ctxItemOver('"+it+"')\" onclick=\"return nlsctxmenu."+this.mId+".itemClick('"+it+"');\">" +
        "<td class=\""+this.stlprf+"ctxsidebar\" align=\"center\" nowrap>"+(this.items[it].ico?"<img src='"+this.items[it].ico+"' valign=middle/>":"&nbsp;")+"</td>" +
        "<td class=\""+this.stlprf+"ctxitem\" nowrap><a style='display:block;' class=\""+this.stlprf+"ctxtext"+(this.items[it].enable?"":"disable")+"\" "+(this.items[it].url==""?"":"href=\""+this.items[it].url+"\" " + (trg!=""?"target=\""+trg+"\" ":"") )+">"+this.items[it].capt+"</a></td></tr>";
    }
  }
  smenu = "<table border=0 cellpadding=0 cellspacing=0 "+(this.absWidth==0?"":"width='"+this.absWidth+"'")+">" + smenu + "</table>";

  if (arguments[0] && arguments[0]=="content") return smenu;
  smenu = "<div id='"+this.mId+"' class='"+this.stlprf+"ctxmenu' style='display:none'>"+smenu+"</div>";

  //body onclick event.
  var isIE = (window.navigator.userAgent.indexOf("MSIE") >=0);
  var orgEvent = (isIE?document.body.onclick:window.onclick)
  if (!orgEvent || orgEvent.toString().search(/orgEvent/gi) < 0) {
    var newEvent = function() { if (orgEvent) orgEvent(); hideAllMenu();}
    if (isIE) document.body.onclick=newEvent; else window.onclick=newEvent;
  }
  //
  return smenu;
}

function reloadMenu() {
  var m=NlsGetElementById(this.mId);
  m.innerHTML=this.genMenu("content");
}

function addItem(key, capt, url, ico, enb) {
  var intKey = this.mId+"_"+key;
  var it = new NlsCtxMenuItem(key, capt, url, ico, enb);
  it.intKey = intKey;
  this.items[intKey] = it;
  this.count++;
}

function addSeparator() {
  this.add("auto"+this.count, "-", "", "");
}

function setItemTarget(key, target) {
  var intKey = this.mId+"_"+key;
  this.items[intKey].target=target;
}

//show menu base on mouse click (clientX and clientY)
function showMenu(x, y) {
  //hide all other menu
  hideAllMenu();
  if (this.lsItm!=null) {setStyle(this.lsItm, "N"); this.lsItm=null;}

  var flag= this.menuOnShow(this.container.selNd); //onshow event
  if (flag==false) return;

  var ctx = NlsGetElementById(this.mId);
  ctx.style.left=-500+"px";
  ctx.style.visibility="hidden";
  ctx.style.display="";
  //reposition
  var scrOffX = window.scrollX?window.scrollX:(document.documentElement.scrollLeft?document.documentElement.scrollLeft:document.body.scrollLeft); //scrollLeft:IE, scrollX:MOZ
  var scrOffY = window.scrollY?window.scrollY:(document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop); //scrollLeft:IE, scrollX:MOZ

  var cW=(window.innerWidth?window.innerWidth:document.body.clientWidth);//clientWidth:IE, innerWidth:MOZ, clientWidth in MOZ include non visible area
  var cH=(window.innerHeight?window.innerHeight:document.body.clientHeight);
  var mW=ctx.childNodes[0].offsetWidth, mH=ctx.childNodes[0].offsetHeight; //using clientWidth is OK on IE and FF, but not in NS/MOZ
  //var mW=ctx.clientWidth, mH=ctx.clientHeight; //use this if items > screen height
  if (x+mW+5>cW) { //iff
    if (x>=mW) { ctx.style.left=x-mW+scrOffX+"px"; } else { ctx.style.left=cW-mW-5+scrOffX+"px"; }
  } else {
    ctx.style.left=x+scrOffX+"px";
  }
  if (y+mH+5>cH) {
    if (y>=mH) { ctx.style.top=y-mH+scrOffY+"px"; } else { ctx.style.top=cH-mH-5+scrOffY+"px"; }
  } else {
    ctx.style.top=y+scrOffY+"px";
  }
  ctx.style.visibility="visible";
  this.active=true;
}

function showMenuAbs(x, y) {
  hideAllMenu();
  var ctx = NlsGetElementById(this.mId);
  ctx.style.top=y+"px"; ctx.style.left=y+"px";
  ctx.style.display="";
  this.active=true;
}

function hideMenu() {
  var ctx = NlsGetElementById(this.mId);
  ctx.style.display="none";
  this.active=false;
  if (this.lsItm!=null) {setStyle(this.lsItm, "N"); this.lsItm=null;}
  this.menuOnHide();
}

function hideAllMenu() {
  for (it in nlsctxmenu) {if (nlsctxmenu[it].active) nlsctxmenu[it].hideMenu();}
}

function enableItem(key, b) {
 var intKey = this.mId+"_"+key;
 this.items[intKey].enable=b;
 setStyle(NlsGetElementById(intKey), (b?"N":"D"))
}

function setStyle(it, s) {
  var suff=(s=="O"?"over":"");
  it.cells[0].className="ctxsidebar"+suff;
  it.cells[1].className="ctxitem"+suff;
  it.cells[1].childNodes[0].className="ctxtext"+(s=="D"?"disable":(s=="OD"?"overdisable":suff));
}

function ctxItemOver(it) {
  var m=it.split("_");
  var oIt = NlsGetElementById(it);
  var li = nlsctxmenu[m[0]].lsItm;

  if (li!=null && li.intKey==it) return;
  if (li!=null) setStyle(li, (nlsctxmenu[m[0]].items[li.id].enable ? "N" : "D"));

  setStyle(oIt, (nlsctxmenu[m[0]].items[it].enable ? "O" : "OD")); //select
  nlsctxmenu[m[0]].lsItm=oIt;
}

NlsCtxMenu.prototype.setCaption=function(key, capt) {
  var intKey = this.mId+"_"+key;
  this.items[intKey].capt=capt;
  var tr=NlsGetElementById(intKey);
  if (!tr) return;
  tr.cells[1].childNodes[0].innerHTML=capt;
}

//menu item click event, PUT YOUR CODE HERE
function menuOnClick(selNode, menuId, itemId) {}
function menuOnShow(selNode) {}
function menuOnHide(){}
