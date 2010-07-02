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

## get variables
error_reporting(E_ALL & ~E_NOTICE);
require 'lib/data/tsunami/util/Catalog.php';
require 'lib/data/tsunami/util/TsunamiBase.php';

$mData=array();
$pData=array();
$uData=array();
$pId = $_POST['pId'];
$sId = $_POST['sId'];

$pDAO = TsunamiDAOFactory::newDAO("TsunamiProject","MySQL");
$p=$pDAO->getTsunamiProject($pId);
$sDAO = TsunamiDAOFactory::newDAO("TsunamiSite","MySQL");
$s=$sDAO->getTsunamiSite($sId);
$location = $s->getName();


/*
## Bit of hack, we need a doclib for the master metadata records to point to.
## We'll mark it dirty, and set it's name to 'DUMMY'
$dlDAO = TsunamiDAOFactory::newDAO("TsunamiDocLib","MySQL");
$dl=$dlDAO->newTsunamiDocLib();
$dl->setTsunamiProjectId($pId);
$dl->setTitle("DUMMY");
$dl->setName("DUMMY");
$dl->setDirty(1);
if (($dlDAO->commit($dl))==FALSE)
		print "Error Commiting Dummy DocLib<br/>".$dlDAO->getError()."<br/>\n";
*/

foreach ($_POST as $key => $val) {
	if (strstr($key,"Check_")) {
		list($junk,$cat,$subcat)=explode("_",$key);
		$mData[$subCategory[$subcat][1]]=$cat;
		$pData[$Category[$cat][0]]=1;
		$uData[]=$Category[$cat][1];
	}

	else if (strstr($key,"mtype")) {
		$mtype = $val;
	}

}

$items="";
foreach ($mData as $item => $cat)
	$items.=$item."=".$cat."&";
$items.="mtype=".$mtype;

$rdirectAddr="/common/TsunamiUploadResults.php?";
$rdirectParms="pId=".$pId."&sId=".$sId."&".$items."&";

$rdirectParms.="\\";//a ? is tacked on the end with other stuff we don't want. This should fix that

$rdirect = $rdirectAddr.$rdirectParms;

$upload_dir = "/TSUNAMI-NEES-".date("Y")."-".sprintf("%04d",$pId)."/Site-".sprintf("%04d",$sId)."/";

$upload_pname = $p->getName();
$upload_ptitle = $p->getShortTitle();

$rootname = "Files:"; //?

## build path
$buildPath="/".$location."/";

//Here's where we choose what sub-dir to put it in.
// The idea is, if there is more than one metadata type, choose
// the first; the "location" of the file in the "filesystem" is
// not important at this point.
$dataDirs= array_keys($pData);
$upload_dir .= substr($uData[0],7);

foreach ($dataDirs as $v)
	$buildPath .= $v."|";

$buildPath = substr($buildPath,0,strlen($buildpath)-1);


if(!empty($buildPath)) {
  $path = " ";
  $temp_path = preg_replace('/^\//', '', $buildPath, 1);
  $directories = preg_split('/\//', $temp_path, -1, PREG_SPLIT_NO_EMPTY);
  $i = 1;
  $length = sizeof($directories);
  foreach( $directories as $d) {
    if($i == $length) {
      $path .= "<span class=\"highlight\">".$d."</span>";
    } else {
      $path .= "$d: ";
    }
    $i++;
  }
}
if(empty($path)) {
  $rootname = "<span class=\"highlight\">".substr($rootname, 0, -1)."</span>";
}

## Draw the page
$old_value2=ini_set("upload_max_filesize","1024M");
$old_value=ini_set("post_max_size","1024M");
require "uploadapplet.php";
ini_set("upload_max_filesize",$old_value2);
ini_set("post_max_size",$old_value);
#print "POST= $POST<br/>";
#print "Post should be: ".($POST."&pId=".$pId."&tsunami=1&".$items)."<br/>";
$uploadapplet=str_replace($redirectURL,("https://{$_SERVER['HTTP_HOST']}/".$rdirect),$uploadapplet);
$uploadapplet=str_replace($POST,($POST.="&pId=".$pId."&sId=".$sId."&tsunami=1&".$items),$uploadapplet);
//Add a location string to map to a function to take car of early closure
//$close ="https://{$_SERVER['HTTP_HOST']}/tsunami?action=UploadComplete&event=UploadFailed&".$redirectParms;
/*
<script type="text/javascript">
<!--
function updateParent() {
  window.opener.document.location = window.opener.document.location;
  self.close();
  return true;
}
//--></script>
*/


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>Files: - NEEScentral Tsunami Repository Upload</title>
  <link href="/common/main.css" rel="stylesheet" type="text/css" media="screen,projection,print" />
</head>
<body onunload='window.opener.location.reload(true);' style="width:650px; min-width:650px; margin: 0 auto; border:1px solid #666666;">

<div id="mainheader_toggle">
  <div id="tagline" >
    <p>George E. Brown, Jr. Network for Earthquake Engineering Simulation</p>
  </div>

  <div id="mainheader" >
    <div class="floatleft"><img src="/images/neestsunami-logo.gif" width="320" height="73" alt="" /></div>
  </div>
</div>

<div id="maincontentwrap">
  <div id="centralbody">

    <div class="miniportlet">
      <div class="miniportlet_h3">
        <div class="miniportlet_title">
          Project: $upload_ptitle
        </div>
      </div>
      <div class="bpath">
          Upload to <strong><span class="highlight">$rootname$path</span></strong>
      </div>
      <div style="margin:10px;">
        <?= $uploadapplet ?>

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
  </div>
</div>

</body>
</html>

