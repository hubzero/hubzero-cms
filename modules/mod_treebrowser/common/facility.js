<!--
// usage: print_flash("facility_map.swf", 462, 322);

function print_flash(flash_file, width, height)
{
	document.write('<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="https://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="' + width + '" height="' + height + '" id="map" align="middle">');
	document.write('<param name="allowScriptAccess" value="sameDomain" />');
	document.write('<param name="movie" value="' + flash_file + '" />');
	document.write('<param name="quality" value="best" />');
	document.write('<param name="bgcolor" value="#b3b3b3" />');
	document.write('<embed src="' + flash_file + '" quality="best" bgcolor="#b3b3b3" width="' + width + '" height="' + height + '" name="map" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="https://www.macromedia.com/go/getflashplayer" />');
	document.write('</object>');
}
//-->