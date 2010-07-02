
function updateTn(k,n)
{

    var flashTitle = document.getElementById("flash-title");
    //alert(flashTitle.innerHTML);
    flashTitle.innerHTML = "<div id=\"flash-title\" style=\"width:320px;\">"+n+"</div>";

    var h=document.getElementsByTagName("img");

    for(var d=0;d<h.length;d++)
    {
        if(h[d].className=="feed-active")
        {
            h[d].className="feed-inactive";
        }
    }

    var i = document.getElementById("i_"+k);
    i.className = "feed-active";

}

function updateStream(k, n, url, to)
{
    var flashObj = "<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"https://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\" width=\"320\" height=\"240\" id=\"stream\" align=\"middle\"><param name=\"allowScriptAccess\" value=\"sameDomain\" /><param name=\"movie\" value=\"/components/com_sitesactivities/flash/stream.swf?streamURL="+url+"/jpeg&timeout="+to+"\" /><param name=\"quality\" value=\"high\" /><param name=\"bgcolor\" value=\"#cccccc\" /><embed src=\"/components/com_sitesactivities/flash/stream.swf?streamURL="+url+"/jpeg&timeout="+to+"\" quality=\"high\" bgcolor=\"#cccccc\" width=\"320\" height=\"240\" name=\"stream\" align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"https://www.macromedia.com/go/getflashplayer\" /></object>";
    var f = document.getElementById("flashHTML");
    f.innerHTML = flashObj;
    updateTn(k,n);
}


