<?php

// First try to get GAsession
$GAsession = isset($_REQUEST['GAsession']) ? $_REQUEST['GAsession'] : "";

// Second try
if(empty($GAsession)) $GAsession = Authenticator::getInstance()->getGAsession();

##
## set cache version
$cache_version = "1.0.";
$jarname = "BulkDownload.jar";
if (file_exists($jarname)) {
  $mtime = filemtime($jarname);
  if ($mtime == FALSE)
  $cache_version .= "0";
  else
  {
    $mtime = dechex($mtime);
    if (strlen($mtime) > 4)
    {
      $cache_version .= substr($mtime,0,4) . "." . substr($mtime,4,strlen($mtime));
    }
    else
    {
      $cache_version .= $mtime;
    }
  }
}


$fileNames = $_REQUEST['fileNames'];
$fileSizes = $_REQUEST['fileSizes'];
$folders = $_REQUEST['folders'];

if (strlen($folders) > 0) {
  $foldersArr = explode('|', $folders);
  foreach($foldersArr as $folder) {

    $source = trim ($folder, "/");
    $dirs = explode("/", $source);
    $dirs[0] = "/nees/home/" . $dirs[0] . ".groups";
    $folder =  implode("/", $dirs);

    //$folder = substr_replace($folder, "%", 0, 1);
    //$folder = substr_replace($folder, "%", strpos($folder, "/"), 1);
    $tmp = DataFilePeer::findFilesInDirs($folder);

    foreach ($tmp as $obj) {
      $fullPath = $obj->getFullPath();
      // not all of out files have good sizes
      if(! file_exists($fullPath)) continue;

      $fileSizeInt = filesize($fullPath);

      if ($fileSizeInt) {
        //$fullPath = preg_replace('@^/nees/home@', '', $fullPath, 1);
        //$fullPath = preg_replace('@([^/]*).groups/(.*)@', '$1/$2', $fullPath, 1);

        $fileNames .= "/" . $obj->getFriendlyPath() . "|";
        $fileSizes .= sprintf("%d|", $fileSizeInt);
      }
    }
  }
}

$downloadapplet = <<<ENDHTML

<script type='text/javascript' src='/common/downloader.js'></script>
<script type='text/javascript'>
<!--
  writeDownloadApplet('$cache_version', '$fileNames', '$fileSizes', '$GAsession');
//-->
</script>

ENDHTML;

?>

