<?php
$host = $_SERVER['HTTP_HOST'];
$ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

if(strpos($ref, $host) === false) return;

$fileAction = isset($_REQUEST['fileAction']) ? $_REQUEST['fileAction'] : null;
if(!$fileAction) return;

$entity = null;

if(isset($_REQUEST['projid'])) {
  $entity = ProjectPeer::find($_REQUEST['projid']);
}
elseif(isset($_REQUEST['expid'])) {
  $entity = ExperimentPeer::find($_REQUEST['expid']);
}
elseif(isset($_REQUEST['facid'])) {
  $entity = FacilityPeer::find($_REQUEST['facid']);
}

$auth = Authorizer::getInstance();

if($fileAction == "Delete") {
  $canDelete = $auth->canDelete($entity);

  if( ! $canDelete ) {
    exit("Error: You do not have permission to delete data file in this section.");
  }

  $fileId =   isset($_REQUEST['fileId']) ? $_REQUEST['fileId'] : null;
  $doit =     isset($_REQUEST['doit'])   ? $_REQUEST['doit']   : null;

  if(!$fileId || $doit != 'ajax') {
    exit("Error: Missing parameter(s)");
  }

  $df = DataFilePeer::find($fileId);

  if(!$df) {
    exit("Error: data file not dound in database.");
  }

  $df->fullDeleteSingleFile();

  ob_clean();
  exit($fileId);
}

elseif($fileAction == "EditSingleMetadataAjax") {

  $fileId = isset($_REQUEST['fileId']) ? $_REQUEST['fileId'] : null;

  if(!$fileId) {
    exit("Error: Missing Parameter(s).");
  }

  $df = DataFilePeer::find($fileId);

  if(!$df) {
    exit("Error: Data file not found in database.");
  }

  if(!file_exists($df->getFullPath())) {
    exit("Error: Data file not found on disc.");
  }

  $canEdit = $auth->canEdit($entity);

  // Special case of EquipmentModel
  if(!$canEdit) {
    if(strpos($df->getFullPath(), "/nees/home/facility.groups/EquipmentModel/") === 0) {
      $auth = Authorizer::getInstance();
      $canEdit = AuthorizationPeer::CanDoInAnyFacility($auth->getUserId(), "edit");
    }
  }

  if(!$canEdit) {
    exit("Error: You do not have permission to edit metadata in this section.");
  }

  $description = isset($_REQUEST['description']) ? trim($_REQUEST['description']) : null;
  $title =       isset($_REQUEST['title'])       ? trim($_REQUEST['title'])       : null;
  $authors =     isset($_REQUEST['authors'])     ? trim($_REQUEST['authors'])     : null;
  $emails =      isset($_REQUEST['emails'])      ? trim($_REQUEST['emails'])      : null;
  $cite =        isset($_REQUEST['cite'])        ? trim($_REQUEST['cite'])        : null;

  if(!is_null($description)) $df->setDescription($description);
  if(!is_null($title)) $df->setTitle($title);
  if(!is_null($authors)) $df->setAuthors($authors);
  if(!is_null($emails)) $df->setAuthorEmails($emails);
  if(!is_null($cite)) $df->setHowToCite($cite);

  $df->save();

  ob_clean();
  exit($fileId);
}


?>
