<?php

// First try to get GAsession
$GAsession = isset($_REQUEST['GAsession']) ? $_REQUEST['GAsession'] : "";

// Second try
if(empty($GAsession)) $GAsession = Authenticator::getInstance()->getGAsession();

$chkFiles = isset($_REQUEST['chkFiles']) ? $_REQUEST['chkFiles'] : array();
$chkFolders = isset($_REQUEST['chkFolders']) ? $_REQUEST['chkFolders']: array();

$fileNames = array();
$fileSizes = array();

$conditions = array();
$params = array();

$conn = Propel::getConnection();
$stmt = $conn->createStatement();

if(count($chkFolders) > 0) {

  $select_sql = "SELECT CONCAT(PATH, CONCAT('/', NAME)) AS FULLPATH FROM DATA_FILE WHERE ID IN (" . implode(",", $chkFolders) . ")";
  $rs = $stmt->executeQuery($select_sql, ResultSet::FETCHMODE_ASSOC);

  while($rs->next()) {
    $conditions[] = "PATH = ? OR PATH LIKE ?";
    $params[] = $rs->get('FULLPATH');
    $params[] = $rs->get('FULLPATH') . "/%";
  }
  $rs->close();
  $stmt->close();

  $sql = "SELECT CONCAT(PATH, CONCAT('/', NAME)) AS FULLPATH FROM DATA_FILE WHERE DIRECTORY = 0 AND (" . implode(" OR ", $conditions) . ")";
  $stmt = $conn->prepareStatement($sql);

  $i = 1;
  foreach ($params as $param) {
    $stmt->setString($i, $param);
    $i++;
  }

  $rs = $stmt->executeQuery();
  $rs->setFetchMode(ResultSet::FETCHMODE_ASSOC);

  while($rs->next()) {
    $filename = $rs->get('FULLPATH');
    if(is_file($filename)) {
      $fileSizes[] = filesize($filename);
      $filename = str_replace("|", "%7C", get_friendlyPath($filename));
      $fileNames[] = $filename;
    }
  }
  $rs->close();
  $stmt->close();
}

if(count($chkFiles) > 0) {
  $sql = "SELECT CONCAT(PATH, CONCAT('/', NAME)) AS FULLPATH FROM DATA_FILE WHERE ID IN (" . implode(",", $chkFiles) . ")";
  $stmt = $conn->createStatement();
  $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

  while($rs->next()) {
    $filename = $rs->get('FULLPATH');
    if(is_file($filename)) {
      $fileSizes[] = filesize($filename);
      $filename = str_replace("|", "%7C", get_friendlyPath($filename));
      $fileNames[] = $filename;
    }
  }
}


if(empty($fileNames) || empty($fileSizes)) {
  $notice = <<<ENDHTML

<div class="miniportlet" style="border-bottom:none; border-left:none; border-right:none;">
  <div class="miniportlet_h3" style="border-left:none; border-right:none;">
    <div class="miniportlet_title">
      NEEScentral Bulk Download
    </div>
  </div>
  <div style="margin:10px; width:500px;">
    <div class="notice">
			There are no files to download
    </div>
  </div>
</div>


ENDHTML;

  print_popup("NEEScentral Bulk Download", $notice, 0, 520);
}
else {
  $fileNamesStr = implode("|", $fileNames);
  $fileSizesStr = implode("|", $fileSizes);

  $return = <<<ENDHTML

<div class="miniportlet" style="border-bottom:none; border-left:none; border-right:none;">
  <div class="miniportlet_h3" style="border-left:none; border-right:none;">
    <div class="miniportlet_title">
      NEEScentral Bulk Download
    </div>
  </div>
  <div style="margin:10px; width:500px;">
<script type='text/javascript'>
<!--
function writeDownloadApplet(fileNames, fileSizes, gasession)
{

	fileNames = escape(fileNames).replace(/%7C/g, "|");

	document.writeln('<APPLET CODE = "org.sdsc.nees.download.BulkDownload" ARCHIVE = "/applets/BulkDownload.jar" WIDTH = "500" HEIGHT = "180">');
	document.writeln('<PARAM NAME = "code" VALUE = "org.sdsc.nees.download.BulkDownload" >');
	document.writeln('<PARAM NAME = "cache_archive" VALUE = "BulkDownload.jar" >');
	document.writeln('<PARAM NAME = "type" VALUE="application/x-java-applet;version=1.4">');
	document.writeln('<PARAM NAME = "scriptable" VALUE="false">');
	document.writeln('<PARAM NAME = "FileNames" value="' + fileNames + '"> ');
	document.writeln('<PARAM NAME = "FileSizes" value="' + fileSizes + '"> ');
	document.writeln('<PARAM NAME = "GAsession" value="' + gasession + '"> ');
	document.writeln('<PARAM NAME = "debug" VALUE="on">');
	document.writeln('Java 1.4 or higher plugin required.');
	document.writeln('</APPLET>');

}

writeDownloadApplet('$fileNamesStr', '$fileSizesStr', '$GAsession');

//-->
</script>
    <div class="info">
				<ol>
					<li>Click the <strong>Browse</strong> button.</li>
					<li>Select the target directory to download the files/folders you have selected.</li>
					<li>Click the <strong>Start</strong> button to initiate the transfer.</li>
					<li>Closing the window while a transfer is in progress will result in a failed download.</li>
				</ol>
    </div>
  </div>
</div>


ENDHTML;

  print_popup("NEEScentral Bulk Download", $return, 0, 520);
}

?>
