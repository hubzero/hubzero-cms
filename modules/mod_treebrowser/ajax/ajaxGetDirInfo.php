<?php

$dirId = isset($_REQUEST['dirId']) ? $_REQUEST['dirId'] : null;
$dirPath = null;

if($dirId) {
  $dir = DataFilePeer::find($dirId);
  if($dir) {
    $dirPath = $dir->getFullPath();
  }
}
else {
  $dirPath = isset($_REQUEST['dirPath']) ? get_systemPath($_REQUEST['dirPath']) : null;
}

$dfs = DataFilePeer::findAllInDir($dirPath);

$numDirs = 0;
$numFiles = 0;
$totalSize = 0;

foreach($dfs as $df) {
  /* @var $df DataFile */
  $fullPath = $df->getFullPath();

  if(file_exists($fullPath)) {
    if(is_dir($fullPath)) {
      $numDirs++;
    }
    else {
      $numFiles++;
      $totalSize += filesize($fullPath);
    }
  }
}

if($numDirs == 0 && $numFiles == 0) exit("Folder is empty");

$cleansize = cleanSize($totalSize);

$numFilesStr = $numFiles . " File" . ($numFiles > 1 ? "s" : "");
$numDirsStr = $numDirs . " Folder" . ($numDirs > 1 ? "s" : "");

exit("Size: " . $cleansize . "&nbsp;&nbsp;-&nbsp;&nbsp;(" . $totalSize . " bytes)<br>Contains: " . $numFilesStr . ", " . $numDirsStr);


?>
