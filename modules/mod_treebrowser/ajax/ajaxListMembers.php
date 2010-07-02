<?php

$projid = isset($_REQUEST['projid']) ? $_REQUEST['projid'] : null;
$expid = isset($_REQUEST['expid']) ? $_REQUEST['expid'] : null;

$members = null;

if($projid) {
  $project = ProjectPeer::find($projid);

  if(!$project) {
    exit("Error: project does not exist.");
  }

  $members = getMemberList($projid, 1, ($projid==354));
}
elseif($expid) {
  $experiment = ExperimentPeer::find($expid);

  if(!$experiment) {
    exit("Error: experiment does not exist.");
  }

  $projid = $experiment->getProjectId();

  $members = getMemberList($expid, 3, ($projid==354));
}

$membersListStr = "";

while($members->next()) {
  $lastname = $members->get("LAST_NAME");
  $firstname = $members->get("FIRST_NAME");

  $membersListStr .= "<li>" . $firstname . " " . $lastname . "</li>";
}

exit ("<ol style='padding-right:20px;'>" . $membersListStr . "</ol>");



/**
 * Get the list of member for a project or experiment
 *
 * @param int $entityId
 * @param int $entityTypeId
 * @param boolean $fullPermission
 * @return ResultSet
 */
function getMemberList($entityId, $entityTypeId, $fullPermission=false) {
  if($fullPermission) {
    return PersonPeer::findMembersWithFullPermissionsForEntity($entityId, $entityTypeId);
  }
  else {
    return PersonPeer::findMembersPermissionsForEntity($entityId, $entityTypeId);
  }
}

?>
