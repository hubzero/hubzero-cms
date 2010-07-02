/** JavaScript functions to create a drop down menu for different project listings
**/


// from http://www.quirksmode.org/js/findpos.html

var hide  = true;

function showhide(obj)
{
	var x = document.getElementById('submenu');
	hide = !hide;
	x.style.visibility = (hide) ? 'hidden' : 'visible';
	setLyr(obj,'submenu');
}

function setLyr(obj,lyr)
{
	var coors = findPos(obj);
	var x = document.getElementById(lyr);
	x.style.top = 25+coors[1] + 'px';
	x.style.left = coors[0] + 'px';
}

function findPos(obj)
{
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
		}
	}
	return [curleft,curtop];
}




