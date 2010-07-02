 <?php
##
## set POST location

// First try to get GAsession
$GAsession = isset($_REQUEST['GAsession']) ? $_REQUEST['GAsession'] : "";

// Second try
if(empty($GAsession)) $GAsession = Authenticator::getInstance()->getGAsession();

if(empty($GAsession)) {
  $uploadapplet = "<div class='error'>Your session has expired. Please close this window, re-login and try again.</div>";
}
else {
  $POST = "https://{$_SERVER['HTTP_HOST']}/data/put/?GAsession=$GAsession&base=$upload_dir";
  $MAX_SIZE = ini_get("post_max_size") . "B";
  $ENCODE = "UTF-8";


  //exit("GAsession=$_REQUEST[GAsession]");

  ##
  ## set Redirect URL
  $redirectURL = "https://{$_SERVER['HTTP_HOST']}/common/uploadresults.php";

  ##
  ## set cache version
  $cache_version = "1.2.";
  $jarname = "jupload.jar";

  $uploadapplet = <<< ENDHTML

<APPLET  CODE = "wjhk.jupload.JUploadApplet" ARCHIVE = "/applets/jupload.jar" WIDTH = "630" HEIGHT = "300">
    <PARAM NAME = CODE VALUE = "wjhk.jupload.JUploadApplet" >
    <PARAM NAME = "type" VALUE="application/x-java-applet;version=1.4">
    <PARAM NAME = "scriptable" VALUE="false">
    <PARAM NAME = "postURL" VALUE ="$POST">
    <PARAM NAME = "redirectURL" VALUE ="$redirectURL">
    <PARAM NAME = "debug" VALUE="on">
    <PARAM NAME = "maxPostSize" VALUE="$MAX_SIZE">
    <PARAM NAME = "postCharset" VALUE="$ENCODE">
Java 1.4 or higher plugin required.
</APPLET>

ENDHTML;

}

?>

