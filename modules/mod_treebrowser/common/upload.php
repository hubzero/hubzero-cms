<?php
/** ****************************************************************************
 * @title
 *   Upload Popup
 *
 * @author
 *   Tim Warnock
 *
 * @abstract
 *   Popup file and directory upload
 *
 * @description
 *   Upload files and directories
 *
 ******************************************************************************/
require "lib/nees.php";
require_once 'lib/filesystem/FileCommandAPI.php';

$upload_timeout = 14400; // 14400 seconds = 240 minutes = 4 hours
ini_set('session.gc_maxlifetime', $upload_timeout);

/*
basepath=/NEES-2007-0354/Experiment-1/Trial-1/Rep-1&
path=&
ptitle=NEEScentral%20Demo%28NEES-2007-0354%29&
pname=NEES-2007-0354&
rootname=Data:
*/
## get variables
$basepath = isset($_REQUEST['basepath']) ? $_REQUEST['basepath'] : "";
$path     = isset($_REQUEST['path'])     ? $_REQUEST['path'] : "";
$rootname = isset($_REQUEST['rootname']) ? $_REQUEST['rootname'] : "";

$upload_dir = $basepath . $path;
$paths = str_replace("/", " :: ", $upload_dir);

$upload_dir = rawurlencode($upload_dir);
$upload_dir = str_replace("%2F", "/", $upload_dir);

// First try to get GAsession
$GAsession = isset($_REQUEST['GAsession']) ? $_REQUEST['GAsession'] : "";

// Second try
if(empty($GAsession)) $GAsession = Authenticator::getInstance()->getGAsession();

if(empty($GAsession)) {
  $uploadapplet = "<div class='error'>Your session has expired. Please close this window, re-login and try again.</div>";
}
else {
  $post = "https://{$_SERVER['HTTP_HOST']}/data/put/?GAsession=$GAsession&base=$upload_dir";
  $max_size = ini_get("post_max_size") . "B";

  //exit("GAsession=$_REQUEST[GAsession]");

  ##
  ## set Redirect URL
  $redirectURL = "https://{$_SERVER['HTTP_HOST']}/common/uploadresults.php";

  $return = <<<ENDHTML

<script type="text/javascript">
<!--
function updateParent() {
  alert("Update Parent");
  window.opener.document.location = window.opener.document.location;
  self.close();
  return true;
}
//-->
</script>

<div class="miniportlet" style="border-bottom:none; border-left:none; border-right:none;">
  <div class="miniportlet_h3" style="border-left:none; border-right:none; border-bottom:none;">
    <div class="miniportlet_title">
      Upload Session: $rootname
    </div>
  </div>
  <div class="bpath">
      Upload to <strong><span class="highlight">$paths</span> </strong>
  </div>
  <div style="margin:10px;">
    <applet code = "wjhk.jupload.JUploadApplet" archive = "/applets/jupload.jar" width="630" height="300">
      <param name = "code"        value = "wjhk.jupload.JUploadApplet" >
      <param name = "type"        value="application/x-java-applet;version=1.4">
      <param name = "scriptable"  value="false">
      <param name = "postURL"     value ="$post">
      <param name = "redirectURL" value ="$redirectURL">
      <param name = "debug"       value="on">
      <param name = "maxPostSize" value="$max_size">
      <param name = "postCharset" value="UTF-8">
      Java 1.4 or higher plugin required.
    </applet>

    <br/><br/>
    <div class="info">
      <ol>
        <li>Choose <strong>Browse Locally...</strong> to open the file selection window.</li>
        <li>Select the files and/or directories to upload.  To select multiple
          items hold CTRL/<img src="/images/icon-applecommand.gif" width="10" height="10" alt="" />
          key and click each item (Tip: Selecting an entire folder will upload
          its entire contents.)
        </li>
        <li>Select the <strong>Upload</strong> button to initiate the transfer.</li>
        <li>Closing the window while a transfer is in progress will result in a failed upload.</li>
      </ol>
    </div>
  </div>
</div>

ENDHTML;

}

$reload_on_close = true;
print_popup("$rootname - NEEScentral Upload", $return, $reload_on_close);
?>
