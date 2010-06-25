<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xna="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" exclude-result-prefixes="xna">
<xsl:output indent="yes" method="html" omit-xml-declaration="yes"/>
<xsl:template match="/">
<html>
<head>
<title>Google Sitemap File</title>
<script src="media/system/js/mootools.js" type="text/javascript"></script>
<style type="text/css">
    <![CDATA[
  	<!--
  	h1 { 
  		font-weight:bold;
  		font-size:1.5em;
  		margin-bottom:0;
  		margin-top:1px; }
  	
  	h2 { 
  		font-weight:bold;
  		font-size:1.2em;
  		margin-bottom:0; 
  		color:#707070;
  		margin-top:1px; }
  	 	
  	p.sml { 
  		font-size:0.8em;
  		margin-top:0; }
  	
  	.sortup {
  		background-position: right center;
  		background-image: url(http://www.google.com/webmasters/sitemaps/images/sortup.gif);
  		background-repeat: no-repeat;
  		font-style:italic;
  		white-space:pre; }
  		
  	.sortdown {
  		background-position: right center;
  		background-image: url(http://www.google.com/webmasters/sitemaps/images/sortdown.gif);
  		background-repeat: no-repeat;
  		font-style:italic;
  		white-space:pre; }
  	
  	table.copyright {
  		width:100%;
  		border-top:1px solid #ddad08;
  		margin-top:1em;
  		text-align:center;
  		padding-top:1em;
  		vertical-align:top; }
	table.data {
		font-size: 12px;
		width: 100%;
		border: 1px solid #000000;
	}
	table.data tr.header td{
		background-color: #CCCCCC;
		color: #FFFFFF;
		font-weight: bold;
		font-size: 14px;
	}
	.divoptions{
		background:#fff;
		border:1px solid #ccc;
		position:absolute;
		padding:5px;
	}
	.divoptions table{
		width:100%;
	}
	.divoptions table td {
		padding:0px;
		border: 1px solid #ffffff;
		border-bottom:1px solid #ccc;
		font-size: 12px;
	}
	.divoptions table td:hover {
		border: 1px solid blue;
	}
	.divoptions table td a {
		text-decoration:none;
		display:block;
		width:100%;
	}
	.editable {
		cursor:pointer;
		background: url(components/com_xmap/images/arrow.gif) top right no-repeat;
		padding-right:18px;
		padding-right:18px;
		border:1px solid #ffffff;
	}
	.editable:hover {
		border-color:#cccccc;
	}
    div.imagelist {
        border: 1px solid #ccc;
        background-color: #eee;
        padding: 5px;
        width: auto;float:left;
    }
    span.images_count {
        border: 1px solid #004080;
        background-color: #0000FF;
        color: #fff;
        margin: 0 5px;
        cursor: pointer;
    }
    

  	-->
    ]]>
</style>
<script language="JavaScript">
    <![CDATA[
  	var selectedColor = "blue";
  	var defaultColor = "black";
  	var hdrRows = 1;
  	var numeric = '..';
  	var desc = '..';
  	var html = '..';
  	var freq = '..';
  	
  	function initXsl(tabName,fileType) {
  		hdrRows = 1;
  	
  	  if(fileType=="sitemap") {
  	  	numeric = ".3.";
  	  	desc = ".1.";
  	  	html = ".0.";
  	  	freq = ".2.";
  	  	initTable(tabName);
  		  setSort(tabName, 3, 1);
  	  }
  	  else {
  	  	desc = ".1.";
  	  	html = ".0.";
  	  	initTable(tabName);
  		  setSort(tabName, 1, 1);
  	  }
  	
  		var theURL = document.getElementById("head1");
  		theURL.innerHTML += ' ' + location;
  		document.title += ': ' + location;
  	}
  	
  	function initTable(tabName) {
  	  var theTab = document.getElementById(tabName);
  	  for(r=0;r<hdrRows;r++)
  	   for(c=0;c<theTab.rows[r].cells.length;c++)
  	     if((r+theTab.rows[r].cells[c].rowSpan)>hdrRows)
  	       hdrRows=r+theTab.rows[r].cells[c].rowSpan;
  	  for(r=0;r<hdrRows; r++){
  	    colNum = 0;
  	    for(c=0;c<theTab.rows[r].cells.length;c++, colNum++){
  	      if(theTab.rows[r].cells[c].colSpan<2){
  	        theCell = theTab.rows[r].cells[c];
  	        rTitle = theCell.innerHTML.replace(/<[^>]+>|&nbsp;/g,'');
  	        if(rTitle>""){
  	          theCell.title = "Change sort order for " + rTitle;
  	          theCell.onmouseover = function(){setCursor(this, "selected")};
  	          theCell.onmouseout = function(){setCursor(this, "default")};
  	          var sortParams = 15; // bitmapped: numeric|desc|html|freq
  	          if(numeric.indexOf("."+colNum+".")>-1) sortParams -= 1;
  	          if(desc.indexOf("."+colNum+".")>-1) sortParams -= 2;
  	          if(html.indexOf("."+colNum+".")>-1) sortParams -= 4;
  	          if(freq.indexOf("."+colNum+".")>-1) sortParams -= 8;
  	          theCell.onclick = new Function("sortTable(this,"+(colNum+r)+","+hdrRows+","+sortParams+")");
  	        }
  	      } else {
  	        colNum = colNum+theTab.rows[r].cells[c].colSpan-1;
  	      }
  	    }
  	  }
  	}
  	
  	function setSort(tabName, colNum, sortDir) {
  		var theTab = document.getElementById(tabName);
  		theTab.rows[0].sCol = colNum;
  		theTab.rows[0].sDir = sortDir;
  		if (sortDir) 
  			theTab.rows[0].cells[colNum].className='sortdown'
  		else
  			theTab.rows[0].cells[colNum].className='sortup';
  	}
  	
  	function setCursor(theCell, mode){
  	  rTitle = theCell.innerHTML.replace(/<[^>]+>|&nbsp;|\W/g,'');
  	  if(mode=="selected"){
  	    if(theCell.style.color!=selectedColor) 
  	      defaultColor = theCell.style.color;
  	    theCell.style.color = selectedColor;
  	    theCell.style.cursor = "pointer";
  	    window.status = "Click to sort by '"+rTitle+"'";
  	  } else {
  	    theCell.style.color = defaultColor;
  	    theCell.style.cursor = "";
  	    window.status = "";
  	  }
  	}
  	
  	function sortTable(theCell, colNum, hdrRows, sortParams){
  	  var typnum = !(sortParams & 1);
  	  sDir = !(sortParams & 2);
  	  var typhtml = !(sortParams & 4);
  	  var typfreq = !(sortParams & 8);
  	  var tBody = theCell.parentNode;
  	  while(tBody.nodeName!="TBODY"){
  	    tBody = tBody.parentNode;
  	  }
  	  var tabOrd = new Array();
  	  if(tBody.rows[0].sCol==colNum) sDir = !tBody.rows[0].sDir;
  	  if (tBody.rows[0].sCol>=0)
  	    tBody.rows[0].cells[tBody.rows[0].sCol].className='';
  	  tBody.rows[0].sCol = colNum;
  	  tBody.rows[0].sDir = sDir;
  	  if (sDir) 
  	  	 tBody.rows[0].cells[colNum].className='sortdown'
  	  else 
  	     tBody.rows[0].cells[colNum].className='sortup';
  	  for(i=0,r=hdrRows;r<tBody.rows.length;i++,r++){
  	    colCont = tBody.rows[r].cells[colNum].innerHTML;
  	    if(typhtml) colCont = colCont.replace(/<[^>]+>/g,'');
  	    if(typnum) {
  	      colCont*=1;
  	      if(isNaN(colCont)) colCont = 0;
  	    }
  	    if(typfreq) {
  			switch(colCont.toLowerCase()) {
  				case "always":  { colCont=0; break; }
  				case "hourly":  { colCont=1; break; }
  				case "daily":   { colCont=2; break; }
  				case "weekly":  { colCont=3; break; }
  				case "monthly": { colCont=4; break; }
  				case "yearly":  { colCont=5; break; }
  				case "never":   { colCont=6; break; }
  			}
  		}
  	    tabOrd[i] = [r, tBody.rows[r], colCont];
  	  }
  	  tabOrd.sort(compRows);
  	  for(i=0,r=hdrRows;r<tBody.rows.length;i++,r++){
  	    tBody.insertBefore(tabOrd[i][1],tBody.rows[r]);
  	  } 
  	  window.status = ""; 
  	}
  	
  	function compRows(a, b){
  	  if(sDir){
  	    if(a[2]>b[2]) return -1;
  	    if(a[2]<b[2]) return 1;
  	  } else {
  	    if(a[2]>b[2]) return 1;
  	    if(a[2]<b[2]) return -1;
  	  }
  	  return 0;
  	}

	var divOptions=null;
	function showOptions (cell,options,uid,itemid,e) {
		// var div = document.getElementById('div'+options);
		var div = $('div'+options);
		pos = div.getPosition();
		if ( divOptions != null && div != divOptions ) {
			closeOptions();
		}
		var myCell = $(cell);
		div.style.top = (myCell.getTop()+20)+'px';
		div.style.left = myCell.getLeft()+'px';
		var dimensions = myCell.getSize();
		div.style.width=dimensions.size.x+'px';
		div.style.display='';
		div.uid=uid;
		div.itemid=itemid;
		div.cell=myCell;
		divOptions=div;
		
	}
	function closeOptions() {
		divOptions.style.display='none';
		divOptions=null;
	}

	function changeProperty(el,property) {
		var myAjax = new Ajax('index.php?option=com_xmap&tmpl=component&task=editElement&action=changeProperty&sitemap='+sitemapid+'&uid='+divOptions.uid+'&itemid='+divOptions.itemid+'&property='+property+'&value='+el.innerHTML,{
                                      onComplete: checkChangeResult.bind(divOptions)
                }).request();
		divOptions.cell.innerHTML=el.innerHTML;
		divOptions.style.display='none';
		return false;
	}
	function checkChangeResult(result,xmlResponse) {
	}

	function getURLparam( name ) {
  		name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
  		var regexS = "[\\?&]"+name+"=([^&#]*)";
  		var regex = new RegExp( regexS );
  		var results = regex.exec( window.location.href );
  		if( results == null )
    			return "";
  		else
    			return results[1];
	}
	var sitemapid=getURLparam('sitemap');

        
    window.addEvent('domready',function(){
        $$('div.imagelist').each(function(div){
            div.slide = new Fx.Slide(div).hide();
        })
        $$('span.images_count').each(function(span){
            span.addEvent('click',function(){
                $E('div.imagelist',this.parentNode).slide.toggle();
            });
        })
    });
    ]]>
</script>
</head>
<body onLoad="initXsl('table0','sitemap');">
<h1 id="head1">Site Map</h1>
<h2>Number of URLs in this Sitemap: <xsl:value-of select="count(xna:urlset/xna:url)"></xsl:value-of></h2>
<table id="table0" class="data">
<tr class="header">
  <td>Sitemap URL</td>
    <td>Last modification date</td>
    <td>Change freq.</td>
    <td>Priority</td>
  </tr>
<xsl:for-each select="xna:urlset/xna:url">
     <xsl:variable name="UID"><xsl:value-of select="xna:uid"/></xsl:variable>
     <xsl:variable name="ItemID"><xsl:value-of select="xna:itemid"/></xsl:variable>
<tr>
<td><xsl:variable name="sitemapURL"><xsl:value-of select="xna:loc"/></xsl:variable>
    <xsl:if test="count(image:image/image:loc) &gt; 0">
    <span class="images_count"><xsl:value-of select="count(image:image/image:loc)"></xsl:value-of> Images</span>
    </xsl:if>
    <a href="{$sitemapURL}" target="_blank" ref="nofollow"><xsl:value-of select="$sitemapURL"></xsl:value-of></a>
        <xsl:if test="count(image:image/image:loc) &gt; 0">
    <div class="imagelist">
    <xsl:for-each select="image:image">
    <xsl:value-of select="image:loc"/>  - <xsl:value-of select="image:title"/><br />
    </xsl:for-each>
    </div>
    </xsl:if>
    </td>
<td><xsl:value-of select="xna:lastmod"/></td>
<td class="editable" onClick="showOptions(this,'changefreq','{$UID}','{$ItemID}',event);" ><xsl:value-of select="xna:changefreq"/></td>
<td class="editable" onClick="showOptions(this,'priority','{$UID}','{$ItemID}',event);"><xsl:value-of select="xna:priority"/></td>
</tr>
</xsl:for-each>
</table>
<div id="divchangefreq" class="divoptions" style="display:none;">
<div align="right"><a href="javascript:closeOptions();">x</a></div>
<table>
 <tr><td><a href="#" onClick="return changeProperty(this,'changefreq');">always</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'changefreq');">hourly</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'changefreq');">daily</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'changefreq');">weekly</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'changefreq');">monthly</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'changefreq');">yearly</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'changefreq');">never</a></td></tr>
</table>
</div>
<div id="divpriority" class="divoptions" style="display:none;">
<div align="right"><a href="#" onClick="return closeOptions();">x</a></div>
<table>
 <tr><td><a href="#" onClick="return changeProperty(this,'priority');">0</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'priority');">0.1</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'priority');">0.2</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'priority');">0.3</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'priority');">0.4</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'priority');">0.5</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'priority');">0.6</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'priority');">0.7</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'priority');">0.8</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'priority');">0.9</a></td></tr>
 <tr><td><a href="#" onClick="return changeProperty(this,'priority');">1</a></td></tr>
</table>
</div>
</body>
</html>
</xsl:template>
</xsl:stylesheet>
