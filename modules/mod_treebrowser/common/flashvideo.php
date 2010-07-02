<?php

$video = isset($_REQUEST['video']) ? $_REQUEST['video'] : "";
$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : "";

if(empty($video) || empty($url)) {
  exit("Missing parameter: using: flashvideo.php?video=<Video_Name>&url=<Video_URL>");
}

?>


<html>
<head>
<title>Flash</title>
</head>
<body>

<script type="text/javascript" src="/common/swfobject.js"></script>
<div id="<?= $video ?>"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>
<script type="text/javascript">
<!--
var sd = new SWFObject('/common/mediaplayer.swf','mpl','320','240','8');
sd.addParam('allowscriptaccess','always');
sd.addParam('allowfullscreen','true');
sd.addParam('start','true');
sd.addVariable('height','240');
sd.addVariable('width','320');
sd.addVariable('file','<?= $url ?>');
sd.write('<?= $video ?>');
//-->
</script>

</body>
</html>
