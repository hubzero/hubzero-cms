<?php
################################################################################
##   lib/common/ExportUtility.php
##   by Minh Phan (c) 2007
##
##   Export Utility
##
################################################################################

################################################################################
## The main part to create the zip file, include the XML metadata
################################################################################

// Get data file content directly by sql query, not good for design but faster performance.
function makezip($zipfilename, $objExport, $permission_obj) {

  $objStr = get_class($objExport);

  if($objStr == "HybridProject" || $objStr == "StructuredProject") {
    return makezipProject($zipfilename, $objExport, $permission_obj);
  }

  $auth = Authorizer::getInstance();
  if ( ! $auth->canView($permission_obj)) {

    require 'lib/nees.php';  // Required Login
    print_error($objStr);
  }

  // Set unlimit script time out for the zipping process
  set_time_limit(0);

  $zipfilename .= ".gz";
  $fulldirpath = $objExport->getPathname();

  header("Content-Type: application/x-gzip");
  header('Content-Disposition: attachment; filename="' . $zipfilename . '"');
  header("Cache-Control: cache, must-revalidate");  // Do not remove this, if removed, IE won't work
  header("Pragma: cache");                          // Do not remove this, if removed, IE won't work
  header("Content-Transfer-Encoding: binary\n");
  ob_clean();

  $sql = "SELECT ID, PATH, NAME, DIRECTORY FROM Data_File WHERE concat(path,'/') LIKE ? AND deleted = 0 ORDER BY DIRECTORY DESC, PATH, NAME";

  $conn = Propel::getConnection();
  $stmt = $conn->prepareStatement($sql);
  $stmt->setString(1, "$fulldirpath/%");
  $rs = $stmt->executeQuery();
  $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

  $filelog = "";
  $filelist = array();
  $totalfiles = 0;

	while ($rs->next()) {
		$datafile = $rs->getRow();

    $df_path = $datafile["PATH"];
    $df_name = $datafile["NAME"];
    $df_fullpath = $df_path  . "/" . $df_name;

    if( ! is_file($df_fullpath)) continue;

    if( ! file_exists($df_fullpath)) continue;

    $contentZipFile = getCleanedUpPath($df_fullpath);

    $filelog .= (++$totalfiles) . "\t" . convertSize(filesize($df_fullpath)) . "\t" . $contentZipFile . "\n";
    $filelist[] = $contentZipFile;
  }

  $tmpdir =  "/tmp/" . uniqid();
  mkdir($tmpdir);

  write_to_file("$tmpdir/fileList.txt", $filelog);

  $includefile = $tmpdir . "/include";
  $includefile_fh = fopen($includefile, 'w');
  fwrite($includefile_fh, implode("\n", $filelist));
  fclose($includefile_fh);

  # -c: create a new archive
  # -z: filter the archive through gzip
  # -O: extract files to standard output
  # -T include-file: get names to extract or create from include-file list
  # -C DIR: Change to directory DIR
  $cmd = "/bin/tar -czO -C $tmpdir fileList.txt -C /nees/home -T $includefile";

  $fh = popen($cmd, 'r');
  while (!feof($fh)) {
    print fread($fh, 8192);
  }
  pclose($fh);
}


/**
 * Export Structured Project
 *
 * @param String $zipfilename
 * @param Project $project
 * @param Project $project
 */
function makezipProject($zipfilename, $project, $project) {

  $objStr = get_class($project);

  $auth = Authorizer::getInstance();
  if ( ! $auth->canView($project)) {
    require 'lib/nees.php';  // Required Login
    print_error($objStr);
  }

  // Set unlimit script time out for the zipping process
  set_time_limit(0);

  $projectSubFolders = array("Analysis", "Documentation", "Public");

  $zipfilename .= ".gz";
  $fulldirpath = $project->getPathname();

  header("Content-Type: application/x-gzip");
  header('Content-Disposition: attachment; filename="' . $zipfilename . '"');
  header("Cache-Control: cache, must-revalidate");  // Do not remove this, if removed, IE won't work
  header("Pragma: cache");                          // Do not remove this, if removed, IE won't work
  header("Content-Transfer-Encoding: binary\n");
  ob_clean();

  $filelog = "";
  $filelist = array();
  $totalfiles = 0;

  foreach($projectSubFolders as $projectFolder) {
    $sql = "SELECT ID, PATH, NAME, DIRECTORY FROM Data_File WHERE concat(path,'/') LIKE ? AND deleted = 0 ORDER BY DIRECTORY DESC, PATH, NAME";

    $conn = Propel::getConnection();
    $stmt = $conn->prepareStatement($sql);
    $stmt->setString(1, $fulldirpath . "/". $projectFolder ."/%");
    $rs = $stmt->executeQuery();
    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

  	while ($rs->next()) {
  		$datafile = $rs->getRow();

      $df_path = $datafile["PATH"];
      $df_name = $datafile["NAME"];
      $df_fullpath = $df_path  . "/" . $df_name;

      if( ! file_exists($df_fullpath)) continue;
      if( ! is_file($df_fullpath)) continue;

      $contentZipFile = getCleanedUpPath($df_fullpath);

      $filelog .= (++$totalfiles) . "\t" . convertSize(filesize($df_fullpath)) . "\t" . $contentZipFile . "\n";

      $filelist[] = $contentZipFile;
    }
  }

  $experiments = ExperimentPeer::findByProject($project->getId());

  foreach($experiments as $experiment) {
    $exp_dir = $experiment->getPathname();

    $sql = "SELECT ID, PATH, NAME, DIRECTORY FROM Data_File WHERE concat(path,'/') LIKE ? AND deleted = 0 ORDER BY DIRECTORY DESC, PATH, NAME";

    $conn = Propel::getConnection();
    $stmt = $conn->prepareStatement($sql);
    $stmt->setString(1, $exp_dir ."/%");
    $rs = $stmt->executeQuery();
    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

  	while ($rs->next()) {
  		$datafile = $rs->getRow();

      $df_path = $datafile["PATH"];
      $df_name = $datafile["NAME"];
      $df_fullpath = $df_path  . "/" . $df_name;

      if( ! file_exists($df_fullpath)) continue;
      if( ! is_file($df_fullpath)) continue;

      $contentZipFile = getCleanedUpPath($df_fullpath);

      $filelog .= (++$totalfiles) . "\t" . convertSize(filesize($df_fullpath)) . "\t" . $contentZipFile . "\n";

      $filelist[] = $contentZipFile;
    }
  }

  $tmpdir =  "/tmp/" . uniqid();
  mkdir($tmpdir);

  write_to_file("$tmpdir/fileList.txt", $filelog);

  $includefile = $tmpdir . "/include";
  $includefile_fh = fopen($includefile, 'w');
  fwrite($includefile_fh, implode("\n", $filelist));
  fclose($includefile_fh);

  # -c: create a new archive
  # -z: filter the archive through gzip
  # -O: extract files to standard output
  # -T include-file: get names to extract or create from include-file list
  # -C DIR: Change to directory DIR
  $cmd = "/bin/tar -czO -C $tmpdir fileList.txt -C /nees/home -T $includefile";

  $fh = popen($cmd, 'r');
  while (!feof($fh)) {
    print fread($fh, 8192);
  }
  pclose($fh);
}


################################################################################
## Write content to file
################################################################################
function write_to_file($myFile, $content) {
  $fh = fopen($myFile, 'w') or die("can't open file");
  fwrite($fh, $content);
  fclose($fh);
}



################################################################################
## Remove "/nees/home/" and ".groups" from real system path
################################################################################
function getCleanedUpPath($path)
{
  return str_replace("/nees/home/","",$path);
}

################################################################################
## size cleaning function (copy and modified from lib/browser.php)
################################################################################
function convertSize( $size, $unit = 2 ) {
  if($size >= 1099511627776) {
    $size = $size / 1099511627776;
    $unit = "TB";
  } elseif($size >= 1073741824) {
    $size = $size / 1073741824;
    $unit = "GB";
  } elseif($size >= 1048576) {
    $size = $size / 1048576;
    $unit = "MB";
  } elseif($size >= 1024) {
    $size = $size / 1024;
    $unit = "KB";
  } else {
    $unit = "b";
  }
  $size = round($size, $unit);
  return "$size $unit";
}


function print_error($objStr) {

  $left_portlet = getLeftPortlet();
  $right_content = <<< ENDHTML

        <div class="contentpadding" style="width:500px;">
          <h1 class="portlet">Export Error</h1>

          <div class="notice">You do not have permission to download this $objStr.</div>
          <p class="justified">
            <br/>An error occurs when trying to export data from this $objStr.
            If you believe you have permission to export data in this $objStr, and this happen in error, Please email <a class="bluelt" href="mailto:it-support@nees.org">it-support@nees.org</a> for help.
            <br/><br/>Thank you.
            <br/><br/><br/><br/>
          </p>
        </div>

ENDHTML;


  $right_portlet = make_portlet("<div class='mainportlet_title'>NEEScentral Help</div>", $right_content, "mainportlet", null, "column_right_main");

  $content = $left_portlet . $right_portlet;
  print_to_browser("NEEScentral", $content);
  exit;
}


?>