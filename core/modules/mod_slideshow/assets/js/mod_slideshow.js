/**
 * @package     hubzero-cms
 * @file        modules/mod_slideshow/mod_slideshow.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

//----------------------------------------------------------
// Establish the namespace if it doesn't exist
//----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Flash Player Version Detection - Rev 1.5
//----------------------------------------------------------
HUB.ModSlideshow = {
	src: '',
	height: '',
	width: '',
	alias: '',
	isIE: false,
	isWin: false,
	isOpera: false,

	detectBrowser: function() {
		HUB.ModSlideshow.isIE  = (navigator.appVersion.indexOf("MSIE") != -1) ? true : false;
		HUB.ModSlideshow.isWin = (navigator.appVersion.toLowerCase().indexOf("win") != -1) ? true : false;
		HUB.ModSlideshow.isOpera = (navigator.userAgent.indexOf("Opera") != -1) ? true : false;	
	},

	initialize: function() {
		HUB.ModSlideshow.detectBrowser();

		if (HUB.ModSlideshow.DetectFlashVer(8, 8, 8)) {
			var xfc = $('#xflash-container');

			// embed the Flash Content SWF when all tests are passed
			var flashobj = HUB.ModSlideshow.FL_RunContentFP(
				"src", HUB.ModSlideshow.src,
				"play", "true",
				"width", HUB.ModSlideshow.width,
				"height", HUB.ModSlideshow.height,
				"align", "middle",
				"wmode", "transparent",
				"id", "flashrotation",
				"quality", "high",
				"name", "flashrotation",
				"allowScriptAccess","sameDomain",
				"type", "application/x-shockwave-flash",
				'codebase', 'https://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab',
				"pluginspage", "https://www.macromedia.com/go/getflashplayer"
			);

			if (flashobj) {
				xfc.empty();
				xfc.innerHTML = flashobj;
			}
		}
	},

	ControlVersion: function() {
		var version;
		var axo;
		var e;

		// NOTE : new ActiveXObject(strFoo) throws an exception if strFoo isn't in the registry
		try {
			// version will be set for 7.X or greater players
			axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");
			version = axo.GetVariable("$version");
		} catch (e) {
		}

		if (!version) {
			try {
				// version will be set for 6.X players only
				axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");
			
				// installed player is some revision of 6.0
				// GetVariable("$version") crashes for versions 6.0.22 through 6.0.29,
				// so we have to be careful. 
			
				// default to the first public version
				version = "WIN 6,0,21,0";

				// throws if AllowScripAccess does not exist (introduced in 6.0r47)		
				axo.AllowScriptAccess = "always";

				// safe to call for 6.0r47 or greater
				version = axo.GetVariable("$version");

			} catch (e) {
			}
		}

		if (!version) {
			try {
				// version will be set for 4.X or 5.X player
				axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
				version = axo.GetVariable("$version");
			} catch (e) {
			}
		}

		if (!version) {
			try {
				// version will be set for 3.X player
				axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
				version = "WIN 3,0,18,0";
			} catch (e) {
			}
		}

		if (!version) {
			try {
				// version will be set for 2.X player
				axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
				version = "WIN 2,0,0,11";
			} catch (e) {
				version = -1;
			}
		}

		return version;
	},

	// JavaScript helper required to detect Flash Player PlugIn version information
	GetSwfVer: function() {
		// NS/Opera version >= 3 check for Flash plugin in plugin array
		var flashVer = -1;

		if (navigator.plugins != null && navigator.plugins.length > 0) {
			if (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]) {
				var swVer2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
				var flashDescription = navigator.plugins["Shockwave Flash" + swVer2].description;
				var descArray = flashDescription.split(" ");
				var tempArrayMajor = descArray[2].split(".");
				var versionMajor = tempArrayMajor[0];
				var versionMinor = tempArrayMajor[1];
				if ( descArray[3] != "" ) {
					tempArrayMinor = descArray[3].split("r");
				} else {
					tempArrayMinor = descArray[4].split("r");
				}
				var versionRevision = tempArrayMinor[1] > 0 ? tempArrayMinor[1] : 0;
				var flashVer = versionMajor + "." + versionMinor + "." + versionRevision;
			}
		}
		// MSN/WebTV 2.6 supports Flash 4
		else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.6") != -1) flashVer = 4;
		// WebTV 2.5 supports Flash 3
		else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.5") != -1) flashVer = 3;
		// older WebTV supports Flash 2
		else if (navigator.userAgent.toLowerCase().indexOf("webtv") != -1) flashVer = 2;
		else if ( HUB.ModSlideshow.isIE && HUB.ModSlideshow.isWin && !HUB.ModSlideshow.isOpera ) {
			flashVer = HUB.ModSlideshow.ControlVersion();
		}
		return flashVer;
	},

	// When called with reqMajorVer, reqMinorVer, reqRevision returns true if that version or greater is available
	DetectFlashVer: function(reqMajorVer, reqMinorVer, reqRevision) {
		versionStr = HUB.ModSlideshow.GetSwfVer();
		if (versionStr == -1 ) {
			return false;
		} else if (versionStr != 0) {
			if (HUB.ModSlideshow.isIE && HUB.ModSlideshow.isWin && !HUB.ModSlideshow.isOpera) {
				// Given "WIN 2,0,0,11"
				tempArray         = versionStr.split(" ");  // ["WIN", "2,0,0,11"]
				tempString        = tempArray[1];           // "2,0,0,11"
				versionArray      = tempString.split(",");  // ['2', '0', '0', '11']
			} else {
				versionArray      = versionStr.split(".");
			}
			var versionMajor      = versionArray[0];
			var versionMinor      = versionArray[1];
			var versionRevision   = versionArray[2];

			// is the major.revision >= requested major.revision AND the minor version >= requested minor
			if (versionMajor > parseFloat(reqMajorVer)) {
				return true;
			} else if (versionMajor == parseFloat(reqMajorVer)) {
				if (versionMinor > parseFloat(reqMinorVer))
					return true;
				else if (versionMinor == parseFloat(reqMinorVer)) {
					if (versionRevision >= parseFloat(reqRevision))
						return true;
				}
			}
			return false;
		}
	},

	AddExtension: function(src, ext) {
		if (src.indexOf('?') != -1)
			return src.replace(/\?/, ext+'?'); 
		else
			return src + ext;
	},

	Generateobj: function(objAttrs, params, embedAttrs) { 
		var str = '';
		if (HUB.ModSlideshow.isIE && HUB.ModSlideshow.isWin && !HUB.ModSlideshow.isOpera) {
			str += '<object ';
			for (var i in objAttrs)
				str += i + '="' + objAttrs[i] + '" ';
			for (var i in params)
				str += '><param name="WMode" value="Transparent"/><param name="' + i + '" value="' + params[i] + '" /> ';
			str += '></object>';
		} else {
			str += '<embed wmode="transparent" ';
			for (var i in embedAttrs)
				str += i + '="' + embedAttrs[i] + '" ';
			str += '> </embed>';
		}
		return str;
	},

	FL_RunContent: function() {
		var ret = HUB.ModSlideshow.GetArgs(arguments, ".swf", "movie", "clsid:d27cdb6e-ae6d-11cf-96b8-444553540000", "application/x-shockwave-flash");
		return HUB.ModSlideshow.Generateobj(ret.objAttrs, ret.params, ret.embedAttrs);
	},

	FL_RunContentFP: function() {
		var swfurl = '.swf';
		if (HUB.ModSlideshow.alias != '') {
			var swfurl = ".swf?alias="+ HUB.ModSlideshow.alias;	
		}

		var ret = HUB.ModSlideshow.GetArgs(arguments, swfurl, "movie", "clsid:d27cdb6e-ae6d-11cf-96b8-444553540000", "application/x-shockwave-flash");
		return HUB.ModSlideshow.Generateobj(ret.objAttrs, ret.params, ret.embedAttrs);
	},

	GetArgs: function(args, ext, srcParamName, classid, mimeType) {
		var ret = new Object();
		ret.embedAttrs = new Object();
		ret.params     = new Object();
		ret.objAttrs   = new Object();
		for (var i=0; i < args.length; i=i+2)
		{
			var currArg = args[i].toLowerCase();

			switch (currArg)
			{
				case "classid":
					break;
				case "pluginspage":
					ret.embedAttrs[args[i]] = args[i+1];
					break;
				case "src":
				case "movie":
					args[i+1] = HUB.ModSlideshow.AddExtension(args[i+1], ext);
					ret.embedAttrs["src"] = args[i+1];
					ret.params[srcParamName] = args[i+1];
					break;
				case "onafterupdate":
				case "onbeforeupdate":
				case "onblur":
				case "oncellchange":
				case "onclick":
				case "ondblClick":
				case "ondrag":
				case "ondragend":
				case "ondragenter":
				case "ondragleave":
				case "ondragover":
				case "ondrop":
				case "onfinish":
				case "onfocus":
				case "onhelp":
				case "onmousedown":
				case "onmouseup":
				case "onmouseover":
				case "onmousemove":
				case "onmouseout":
				case "onkeypress":
				case "onkeydown":
				case "onkeyup":
				case "onload":
				case "onlosecapture":
				case "onpropertychange":
				case "onreadystatechange":
				case "onrowsdelete":
				case "onrowenter":
				case "onrowexit":
				case "onrowsinserted":
				case "onstart":
				case "onscroll":
				case "onbeforeeditfocus":
				case "onactivate":
				case "onbeforedeactivate":
				case "ondeactivate":
				case "type":
				//case "wmode":
				case "codebase":
				case "id":
					ret.objAttrs[args[i]] = args[i+1];
					break;
				case "width":
				case "height":
				case "align":
				case "vspace": 
				case "hspace":
				//case "wmode":
				case "class":
				case "title":
				case "accesskey":
				case "name":
				case "tabindex":
					ret.embedAttrs[args[i]] = ret.objAttrs[args[i]] = args[i+1];
					break;
				default:
					ret.embedAttrs[args[i]] = ret.params[args[i]] = args[i+1];
			}
		}
		ret.objAttrs["classid"] = classid;
		if (mimeType) ret.embedAttrs["type"] = mimeType;
		return ret;
	}
}

//----------------------------------------------------------

jQuery(document).ready(function($){
	HUB.ModSlideshow.initialize();
});