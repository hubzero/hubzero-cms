<?php

set_include_path("/usr/local/propel/runtime/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/phpdb/propel/central/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("/www/neeshub/api/org/nees" . PATH_SEPARATOR . get_include_path());

require_once 'propel/Propel.php';
Propel::init("/www/neeshub/api/org/phpdb/propel/central/conf/central-conf.php");

$nodeId = isset($_REQUEST["nid"]) ? $_REQUEST["nid"] : "";

function getTreeNodes($nodeId) {

  $tokenArr = explode("_", $nodeId);

  if(sizeof($tokenArr) < 2) return "";

  $typeStr = $tokenArr[0];

  $treeHTML = "";

  /**
   * *******************************************************************
   * nid=projid_<projid>
   * *******************************************************************
   */
  
  require_once 'lib/data/ProjectPeer.php';
  require_once 'lib/data/AuthorizationPeer.php';
  require_once 'lib/data/Authorization.php';
  require_once 'lib/security/Authorizer.php';
  
  if($typeStr == "projid") {
    $projid = $tokenArr[1];
	$p = ProjectPeer::find($projid);
    if(!$p) return "";

    $pnodeID = $nodeId;
    //$url = "/?projid=$projid";
  	$url = "/warehouse";
    $treeHTML .= "tree.add('" . $pnodeID . "_Experiments',   '$pnodeID', 'Experiment List',       '$url/experiments/$projid',      ico_e);\n";
    if($p->isExperimentalProject()) {
//      if($p->isStructuredProject()) {
//        $treeHTML .= "tree.add('" . $pnodeID . "_Experiments',   '$pnodeID', 'Experiment List',       '$url&action=ListProjectExperiments',      ico_e);\n";
//      }
//      else {
//        $treeHTML .= "tree.add('" . $pnodeID . "_ProjectCoordinator', '$pnodeID', 'Project Coordinator', '$url&action=DisplayProjectCoordinator', ico_project_coordinator);\n";
//      }
//      //$treeHTML .= "tree.add('" . $pnodeID . "_ProjectSpecimen', '$pnodeID','Project Specimen', '$url&action=DisplayProjectSpecimen', ico_project_specimen);\n";
      //$treeHTML .= "tree.add('DataFile_Project_Analysis_$projid', '$pnodeID', 'Project Analysis', '$url&action=DisplayProjectAnalysis', ico_folder);\n";
      //$treeHTML .= "tree.add('DataFile_Project_Documentation_$projid', '$pnodeID', 'Project Documentation', '$url&action=DisplayProjectDocumentation', ico_folder);\n";
      //$treeHTML .= "tree.add('DataFile_Project_Public_$projid', '$pnodeID', 'Project Public', '$url&action=DisplayProjectPublic', ico_folder);\n";
      $treeHTML .= "tree.add('ProjectMembers_$projid', '$pnodeID', 'Project Members', '$url/members/$projid', ico_member);\n";
	  $treeHTML .= "tree.add('ProjectImages_$projid', '$pnodeID', 'Project Images', '$url/images/$projid', ico_folder);\n";
      
     // $treeHTML .= "tree.setServerLoad('DataFile_Project_Analysis_$projid');\n";
     // $treeHTML .= "tree.setServerLoad('DataFile_Project_Documentation_$projid');\n";
     // $treeHTML .= "tree.setServerLoad('DataFile_Project_Public_$projid');\n";

      if($p->isStructuredProject()) {
        $auth = Authorizer::getInstance();
        $uid = $auth->getUserId();

        $exps = ExperimentPeer::findViewableExperimentsWithInProject($projid, $uid);
        $curatedExperimentsMap = NCCuratedObjectsPeer::getCuratedExperimentsMap();

        foreach($exps as $e) {
          $expTypeId = $e->getExperimentTypeId();
          $exp_action = $expTypeId == ExperimentPeer::CLASSKEY_SIMULATION ? "DisplaySimulationMain" : "DisplayExperimentMain";

          $expid = $e->getId();
          $title = clean_newlines($e->getTitle());
          //$url   = "/?projid=$projid&expid=$expid";
          
			$url = "/warehouse/experiment";
          $exp_ico = $e->isSimulation() ? 'ico_sc' : 'ico_ec';

          if($e->isPublished()) {
            $exp_ico = $e->isSimulation() ? 'ico_sp' : 'ico_ep';
            $title .= " (publicly accessible)";
          }

          if(in_array($expid, $curatedExperimentsMap)) {
            $exp_ico= $e->isSimulation() ? 'ico_cs' : 'ico_ce';
            $title .= " (curated)";
          }
			
          $enodeID = "expid_" . $expid;
          //$treeHTML .= "tree.add('$enodeID', '" . $pnodeID . "_Experiments',   '$title', '$url&action=$exp_action', $exp_ico);\n";
          // for the fist demo, set $expid=28
           $treeHTML .= "tree.add('$enodeID', '" . $pnodeID . "_Experiments',   '$title', '$url/$expid/project/$projid', $exp_ico);\n";
          $treeHTML .= "tree.setServerLoad('$enodeID');\n";
        }
      }

      include_once 'lib/data/Specimen.php';
      $specimen = SpecimenPeer::findByProject($projid);

      if($specimen) {
        $treeHTML .= printSpecimen($specimen, $projid, $pnodeID . "_ProjectSpecimen");
      }

      if($p->isHybridProject()) {
        include_once 'lib/data/Coordinator.php';
        $coordinator = CoordinatorPeer::findByProject($projid);

        if($coordinator) {
          $treeHTML .= printCoordinator($coordinator, $projid, $pnodeID . "_ProjectCoordinator");
        }
      }
    }
    elseif($p->isUnstructuredProject()) {
      $treeHTML .= "tree.add('DataFile_Project_Categories_$projid', '$pnodeID', 'Project Categories', '$url&action=DisplayProjectCategories', ico_folder);\n";
      $treeHTML .= "tree.setServerLoad('DataFile_Project_Categories_$projid');\n";

      $treeHTML .= "tree.add('ProjectMembers_" . $projid . "', '$pnodeID', 'Project Members', '$url&action=DisplayProjectMembers', ico_member);\n";
    }
    elseif($p->isSuperProject()) {
      $treeHTML .= "tree.add('" . $pnodeID . "_SubProjects', '$pnodeID', 'Sub-Projects', '$url&action=DisplaySubProjects', '');\n";
      $treeHTML .= "tree.add('DataFile_Project_Documentation_$projid', '$pnodeID', 'Project Documentation', '$url&action=DisplayProjectDocumentation', ico_folder);\n";
      $treeHTML .= "tree.add('DataFile_Project_Public_$projid', '$pnodeID', 'Project Public', '$url&action=DisplayProjectPublic', ico_folder);\n";
      $treeHTML .= "tree.add('ProjectMembers_$projid', '$pnodeID', 'Project Members', '$url&action=DisplayProjectMembers', ico_member);\n";

      $treeHTML .= "tree.setServerLoad('DataFile_Project_Documentation_$projid');\n";
      $treeHTML .= "tree.setServerLoad('DataFile_Project_Public_$projid');\n";
    }

    $treeHTML .= "tree.setServerLoad('ProjectMembers_" . $projid . "');\n";
  }
  /**
   * *******************************************************************
   * nid=ProjectMembers_<projid>
   * *******************************************************************
   */
  elseif($typeStr == "ProjectMembers") {
    $projid = $tokenArr[1];
    $p = ProjectPeer::find($projid);
    if(!$p) return "";

    if($projid != 354) {
      $member = PersonPeer::findMembersPermissionsForEntity($projid, 1);
    }
    else {
      $member = PersonPeer::findMembersWithFullPermissionsForEntity($projid, 1);
    }

    $member->setFetchmode(ResultSet::FETCHMODE_ASSOC);

    $auth = Authorizer::getInstance();
    $canGrant = $auth->canGrant($p);

    while($member->next()) {

      $member_id       = $member->getInt("PERSON_ID");
      $member_fullname = htmlspecialchars($member->get("FIRST_NAME") . " " . $member->get("LAST_NAME"), ENT_QUOTES);
      $member_email    = $member->get("E_MAIL");
      $member_link = $canGrant ? "/?projid=$projid&action=DisplayProjectMembers&personId=$member_id" : "";

      $treeHTML .= "tree.add('pMemberId_" . $projid . "_" . $member_id . "', 'ProjectMembers_" . $projid . "', '$member_fullname', '$member_link', ico_person);\n";
    }
  }
  /**
   * *******************************************************************
   * nid=expid_<expid>
   * *******************************************************************
   */
//  elseif($typeStr == "expid") {
//
//    $expid = $tokenArr[1];
//    $e = ExperimentPeer::find($expid);
//    if(!$e) return "";
//
//    $projid = $e->getProjectId();
//
//    $enodeID = $nodeId;
//    $url = "/?projid=$projid&expid=$expid";
//    $title = clean_newlines($e->getTitle());
//
//    $expTypeId = $e->getExperimentTypeId();

    // since right now, we donot have any trial list, comment it out
//    if($expTypeId == ExperimentPeer::CLASSKEY_STRUCTUREDEXPERIMENT) {
    //  $treeHTML .= "tree.add('" . $enodeID . "_Trials', '$enodeID', 'Trial List', '$url&action=DisplayExperimentTrials', ico_t);\n";
    // $treeHTML .= "tree.add('expSetup_$expid', '$enodeID', 'Experiment Setup', '$url&action=DisplayExperimentSetup', ico_setup);\n";
    //  $treeHTML .= "tree.add('DataFile_Experiment_Analysis_$expid', '$enodeID', 'Experiment Analysis', '$url&action=DisplayExperimentAnalysis', ico_folder);\n";
    //  $treeHTML .= "tree.add('DataFile_Experiment_Documentation_$expid', '$enodeID', 'Experiment Documentation', '$url&action=DisplayExperimentDocumentation', ico_folder);\n";
    //  $treeHTML .= "tree.add('DataFile_Experiment_Public_$expid', '$enodeID', 'Experiment Public', '$url&action=DisplayExperimentPublic', ico_folder);\n";
    //  $treeHTML .= "tree.add('" . $enodeID . "_DataViewers',   '$enodeID', 'Data Viewers', '$url&action=DisplayDataViewers', ico_n3dv);\n";
    //  $treeHTML .= "tree.add('ExpMembers_" . $expid ."', '$enodeID', 'Experiment Members', '$url&action=DisplayExperimentMembers', ico_member);\n";

    //  $treeHTML .= "tree.setServerLoad('expSetup_$expid');\n";
    //  $treeHTML .= "tree.setServerLoad('ExpMembers_$expid');\n";
    //  $treeHTML .= "tree.setServerLoad('DataFile_Experiment_Analysis_$expid');\n";
    //  $treeHTML .= "tree.setServerLoad('DataFile_Experiment_Documentation_$expid');\n";
    //  $treeHTML .= "tree.setServerLoad('DataFile_Experiment_Public_$expid');\n";

    //  $trials = TrialPeer::findByExperiment($expid);

//      foreach($trials as $t) {
//        $trialid = $t->getId();
//        $title = clean_newlines($t->getTitle());
//        $url   = "/?projid=$projid&expid=$expid&trialid=$trialid";
//
//        $tnodeID = "trialid_" . $trialid;
//        $treeHTML .= "\n// TrialID: $trialid \n";
//        $treeHTML .= "tree.add('$tnodeID', '" . $enodeID . "_Trials',   '$title', '$url&action=DisplayTrialMain', ico_tc);\n";
//
//        $treeHTML .= "tree.setServerLoad('$tnodeID');\n";
//      }
//    }
//    elseif($expTypeId == ExperimentPeer::CLASSKEY_SIMULATION) 
//    {
//      $treeHTML .= "tree.add('" . $enodeID . "_Runs', '$enodeID', 'Simulation-Run List', '$url&action=DisplaySimulationRuns', ico_r);\n";
//      $treeHTML .= "tree.add('simSetup_" . $expid . "', '$enodeID', 'Simulation Setup', '$url&action=DisplaySimulationSetup', ico_setup);\n";
//      $treeHTML .= "tree.add('DataFile_Experiment_Analysis_$expid', '$enodeID', 'Simulation Analysis', '$url&action=DisplaySimulationAnalysis', ico_folder);\n";
//      $treeHTML .= "tree.add('DataFile_Experiment_Documentation_$expid', '$enodeID', 'Simulation Documentation', '$url&action=DisplaySimulationDocumentation', ico_folder);\n";
//      $treeHTML .= "tree.add('DataFile_Experiment_Public_$expid', '$enodeID', 'Simulation Public', '$url&action=DisplaySimulationPublic', ico_folder);\n";
//      $treeHTML .= "tree.add('ExpMembers_" . $expid ."',       '$enodeID', 'Simulation Members', '$url&action=DisplayExperimentMembers', ico_member);\n";
//
//      $treeHTML .= "tree.setServerLoad('simSetup_$expid');\n";
//      $treeHTML .= "tree.setServerLoad('ExpMembers_$expid');\n";
//      $treeHTML .= "tree.setServerLoad('DataFile_Experiment_Analysis_$expid');\n";
//      $treeHTML .= "tree.setServerLoad('DataFile_Experiment_Documentation_$expid');\n";
//      $treeHTML .= "tree.setServerLoad('DataFile_Experiment_Public_$expid');\n";
//
//      $runs = TrialPeer::findBySimulation($expid);
//
//      foreach($runs as $r) 
//      {
//        $runid = $r->getId();
//        $title = clean_newlines($r->getTitle());
//        $url   = "/?projid=$projid&expid=$expid&trialid=$runid";
//
//        $rnodeID = "runid_" . $runid;
//        $treeHTML .= "\n// runID: $runid \n";
//        $treeHTML .= "tree.add('$rnodeID', '" . $enodeID . "_Runs',   '$title', '$url&action=DisplaySimulationRunMain', ico_rc);\n";
//
//        $treeHTML .= "tree.setServerLoad('$rnodeID');\n";
//      }
//    }
//    elseif($expTypeId == ExperimentPeer::CLASSKEY_UNSTRUCTUREDEXPERIMENT) {
//      $treeHTML .= "tree.add('" . $enodeID . "_Files',    '$enodeID', 'Experiment Files',   '$url&action=DisplayExperimentFiles',  ico_folder);\n";
//      $treeHTML .= "tree.add('" . $enodeID . "_Public',   '$enodeID', 'Experiment Public',  '$url&action=DisplayExperimentPublic', ico_folder);\n";
//      $treeHTML .= "tree.add('ExpMembers_" . $expid ."',  '$enodeID', 'Experiment Members', '$url&action=DisplayExperimentMembers',   ico_member);\n";
//
//      $treeHTML .= "tree.setServerLoad('ExpMembers_$expid');\n";
//    }
//  }
  /**
   * *******************************************************************
   * nid=expSetup_<expid>
   * nid=expSetup_<expid>_<setupType>
   * *******************************************************************
   */
//  elseif($typeStr == "expSetup") {
//    $expid = $tokenArr[1];
//    $e = ExperimentPeer::find($expid);
//    if(!$e) return "";
//
//    $project = $e->getProject();
//    $projid = $project->getId();
//    $isHybrid = $project->isHybridProject();
//
//    $url = "/?projid=$projid&expid=$expid";
//
//    if(sizeof($tokenArr) == 2) {
//
//      $expSetupNode = $nodeId;
//
//      $treeHTML .= "tree.add('" . $expSetupNode . "_Units', '$expSetupNode', 'Measurement Units', '$url&action=DisplayExperimentSetup&subset=Units#s1', ico_setup_section);\n";
//
//      if($isHybrid) {
//        $treeHTML .= "tree.add('" . $expSetupNode . "_SpecimenComponents', '$expSetupNode', 'Specimen Components', '$url&action=DisplayExperimentSetup&subset=SpecimenComponents#s9', ico_setup_section);\n";
//        $treeHTML .= "tree.setServerLoad('" . $expSetupNode . "_SpecimenComponents');\n";
//      }
//      else {
//        $treeHTML .= "tree.add('" . $expSetupNode . "_Materials',   '$expSetupNode', 'Material Properties',   '$url&action=DisplayExperimentSetup&subset=Materials#s2',    ico_setup_section);\n";
//        $treeHTML .= "tree.setServerLoad('" . $expSetupNode . "_Materials');\n";
//      }
//      $treeHTML .= "tree.add('" . $expSetupNode . "_Coors', '$expSetupNode', 'Coordinate Spaces', '$url&action=DisplayExperimentSetup&subset=Coors#s3', ico_setup_section);\n";
//      $treeHTML .= "tree.add('" . $expSetupNode . "_SensorLPs', '$expSetupNode', 'Sensor Location Plans', '$url&action=DisplayExperimentSetup&subset=SensorLPs#s4', ico_setup_section);\n";
//      $treeHTML .= "tree.add('" . $expSetupNode . "_SourceLPs', '$expSetupNode', 'Source Location Plans', '$url&action=DisplayExperimentSetup&subset=SourceLPs#s5', ico_setup_section);\n";
//      $treeHTML .= "tree.add('" . $expSetupNode . "_Equipment', '$expSetupNode', 'Equipment Inventory', '$url&action=DisplayExperimentSetup&subset=Equipment#s6', ico_setup_section);\n";
//      $treeHTML .= "tree.add('" . $expSetupNode . "_ScaleFactors','$expSetupNode', 'Scale Factors', '$url&action=DisplayExperimentSetup&subset=ScaleFactors#s7', ico_setup_section);\n";
//      $treeHTML .= "tree.add('DataFile_Experiment_Models_$expid', '$expSetupNode', 'Models', '$url&action=DisplayExperimentSetup&subset=Models#s8', ico_setup_section);\n";
//
//      $treeHTML .= "tree.setServerLoad('" . $expSetupNode . "_Coors');\n";
//      $treeHTML .= "tree.setServerLoad('" . $expSetupNode . "_SensorLPs');\n";
//      $treeHTML .= "tree.setServerLoad('" . $expSetupNode . "_SourceLPs');\n";
//      $treeHTML .= "tree.setServerLoad('" . $expSetupNode . "_Equipment');\n";
//      $treeHTML .= "tree.setServerLoad('DataFile_Experiment_Models_$expid');\n";
//    }
//    elseif(sizeof($tokenArr) == 3) {
//      $setupType = $tokenArr[2];
//
//      $expSetupItemNode = $nodeId;
//
//      if($setupType == "Materials") {
//        require_once 'lib/data/Material.php';
//        $materials = MaterialPeer::findByExperiment($expid);
//
//        foreach($materials as $material) {
//          $materialId = $material->getId();
//          $materialName = htmlspecialchars($material->getName(), ENT_QUOTES);
//          $materialUrl = $url . "&materialid=$materialId&action=ViewMaterial";
//
//          $treeHTML .= "tree.add('materialId_" . $materialId . "', '$expSetupItemNode', '$materialName', '$materialUrl',  ico_setup_item);\n";
//        }
//      }
//      elseif($setupType == "SpecimenComponents") {
//        require_once 'lib/data/SpecimenComponentExperiment.php';
//
//        $sces = SpecimenComponentExperimentPeer::findByExperiment($expid);
//
//        foreach($sces as $sce) {
//          /* @var $sce SpecimenComponentExperiment */
//          $sc = $sce->getSpecimenComponent();
//          $specimenId = $sc->getSpecimenId();
//          $specCompId = $sc->getId();
//          $specCompName = htmlspecialchars($sc->getName(), ENT_QUOTES);
//          $specCompUrl = "/?projid=$projid&specimenId=$specimenId&specCompId=$specCompId&action=DisplaySpecimenComponent";
//
//          $treeHTML .= "tree.add('SpecimenComponentExperimentId_" . $specCompId . "', '$expSetupItemNode', '$specCompName', '$specCompUrl',  ico_setup_item);\n";
//        }
//      }
//      elseif($setupType == "Coors") {
//        include_once 'lib/data/CoordinateSpace.php';
//        $coors = CoordinateSpacePeer::findByExperiment($expid);
//
//        foreach($coors as $coor) {
//          $coorId = $coor->getId();
//          $coorName = htmlspecialchars($coor->getName(), ENT_QUOTES);
//          $coorUrl = $url . "&cspaceid=$coorId&action=DisplayCoordinateSpace";
//
//          $treeHTML .= "tree.add('coorId_" . $coorId . "', '$expSetupItemNode', '$coorName', '$coorUrl',  ico_setup_item);\n";
//        }
//      }
//      elseif($setupType == "SensorLPs" || $setupType == "SourceLPs") {
//        include_once 'lib/data/LocationPlan.php';
//
//        if($setupType == "SensorLPs") {
//          $classkey = LocationPlanPeer::CLASSKEY_SENSORLOCATIONPLAN;
//          $action = "DisplaySensorLocationPlan";
//        }
//        else {
//          $classkey = LocationPlanPeer::CLASSKEY_SOURCELOCATIONPLAN;
//          $action = "DisplaySourceLocationPlan";
//        }
//        $lps = LocationPlanPeer::findByExperimentAndPlanTypeID($expid, $classkey);
//
//        foreach($lps as $lp) {
//          $lpId = $lp->getId();
//          $lpName = htmlspecialchars($lp->getName(), ENT_QUOTES);
//          $lpUrl = $url . "&lpid=$lpId&action=$action";
//
//          $treeHTML .= "tree.add('lpId_" . $lpId . "', '$expSetupItemNode', '$lpName', '$lpUrl',  ico_setup_item);\n";
//        }
//      }
//      elseif($setupType == "Equipment") {
//        require_once 'lib/data/ExperimentEquipment.php';
//
//        $expEquipment = ExperimentEquipmentPeer::findByExperiment($expid);
//
//        foreach($expEquipment as $expEquip) {
//          $equip = $expEquip->getEquipment();
//          $equipId = $equip->getId();
//          $equipName = htmlspecialchars($equip->getName(), ENT_QUOTES);
//          $facid = $equip->getOrganization()->getId();
//          $equipUrl = $url . "&equipid=$equipId&facid=$facid&action=DisplayEquipmentList";
//
//          $treeHTML .= "tree.add('equipId_" . $equipId . "', '$expSetupItemNode', '$equipName', '$equipUrl',  ico_setup_item);\n";
//        }
//      }
//    }
//  }
  /**
   * *******************************************************************
   * nid=simSetup_<expid>
   * nid=simSetup_<expid>_<setupType>
   * *******************************************************************
   */
  /*
  elseif($typeStr == "simSetup") {
    $expid = $tokenArr[1];
    $e = ExperimentPeer::find($expid);
    if(!$e) return "";

    $project = $e->getProject();
    $projid = $project->getId();
    $isHybrid = $project->isHybridProject();

    $url = "/?projid=$projid&expid=$expid";

    if(sizeof($tokenArr) == 2) {

      $simSetupNode = $nodeId;

      $treeHTML .= "tree.add('" . $simSetupNode . "_Computers', '$simSetupNode', 'Computer Systems',    '$url&action=DisplaySimulationSetup&subset=Computers#s1', ico_setup_section);\n";
      $treeHTML .= "tree.setServerLoad('" . $simSetupNode . "_Computers');\n";

      if($isHybrid) {
        $treeHTML .= "tree.add('" . $simSetupNode . "_SpecimenComponents', '$simSetupNode', 'Specimen Components', '$url&action=DisplaySimulationSetup&subset=SpecimenComponents#s9', ico_setup_section);\n";
        $treeHTML .= "tree.setServerLoad('" . $simSetupNode . "_SpecimenComponents');\n";
      }
      else {
        $treeHTML .= "tree.add('" . $simSetupNode . "_Materials',   '$simSetupNode', 'Material Properties',   '$url&action=DisplaySimulationSetup&subset=Materials#s2',    ico_setup_section);\n";
        $treeHTML .= "tree.setServerLoad('" . $simSetupNode . "_Materials');\n";
      }

      $treeHTML .= "tree.add('" . $simSetupNode . "_Models', '$simSetupNode', 'Model Types', '$url&action=DisplaySimulationSetup&subset=Models#s3', ico_setup_section);\n";
      $treeHTML .= "tree.setServerLoad('" . $simSetupNode . "_Models');\n";
    }
    elseif(sizeof($tokenArr) == 3) {

      $setupType = $tokenArr[2];

      $simSetupItemNode= $nodeId;

      if($setupType == "Computers") {
        require_once 'lib/data/Equipment.php';
        $computers = EquipmentPeer::findByExperimentEquipmentClass($expid, 'Computer System');

        foreach($computers as $computer) {
          $computerId = $computer->getId();
          $computerName = htmlspecialchars($computer->getName(), ENT_QUOTES);
          $computerUrl = $url . "&computersysid=$computerId&action=DisplayComputerSystem";
          $treeHTML .= "tree.add('computersysid_" . $computerId . "', '$simSetupItemNode', '$computerName', '$computerUrl',  ico_setup_item);\n";
        }
      }
      elseif($setupType == "SpecimenComponents") {
        require_once 'lib/data/SpecimenComponentExperiment.php';

        $sces = SpecimenComponentExperimentPeer::findByExperiment($expid);

        foreach($sces as $sce) {
          // //@var $sce SpecimenComponentExperiment 
          $sc = $sce->getSpecimenComponent();
          $specimenId = $sc->getSpecimenId();
          $specCompId = $sc->getId();
          $specCompName = htmlspecialchars($sc->getName(), ENT_QUOTES);
          $specCompUrl = "/?projid=$projid&specimenId=$specimenId&specCompId=$specCompId&action=DisplaySpecimenComponent";

          $treeHTML .= "tree.add('SpecimenComponentExperimentId_" . $specCompId . "', '$simSetupItemNode', '$specCompName', '$specCompUrl',  ico_setup_item);\n";
        }
      }
      elseif($setupType == "Materials") {
        require_once 'lib/data/Material.php';
        $materials = MaterialPeer::findByExperiment($expid);

        foreach($materials as $material) {
          $materialId = $material->getId();
          $materialName = htmlspecialchars($material->getName(), ENT_QUOTES);
          $materialUrl = $url . "&materialid=$materialId&action=ViewMaterial";

          $treeHTML .= "tree.add('materialId_" . $materialId . "', '$simSetupItemNode', '$materialName', '$materialUrl',  ico_setup_item);\n";
        }
      }
      elseif($setupType == "Models") {
        require_once 'lib/data/ExperimentModel.php';
        $models = ExperimentModelPeer::findByExperiment($expid);

        foreach($models as $model) {
          $modelId = $model->getId();
          $modelName = htmlspecialchars($model->getName(), ENT_QUOTES);
          $modelUrl = $url . "&modelid=$modelId&action=DisplaySimModel";

          $treeHTML .= "tree.add('modelId_" . $modelId . "', '$simSetupItemNode', '$modelName', '$modelUrl',  ico_setup_item);\n";
        }
      }
    }
  }
  */
  /**
   * *******************************************************************
   * nid=ExpMembers_<expid>
   * *******************************************************************
   */
  /*
  elseif($typeStr == "ExpMembers") {
    $expid = $tokenArr[1];

    $e = ExperimentPeer::find($expid);
    if(!$e) return "";

    $auth = Authorizer::getInstance();
    $canGrant = $auth->canGrant($e);

    $projid = $e->getProjectId();

    if($projid != 354) {
      $member = PersonPeer::findMembersPermissionsForEntity($expid, 3);
    }
    else {
      $member = PersonPeer::findMembersWithFullPermissionsForEntity($expid, 3);
    }

    while($member->next()) {
      $member_id       = $member->getInt("PERSON_ID");
      $member_fullname = htmlspecialchars($member->get("FIRST_NAME") . " " . $member->get("LAST_NAME"), ENT_QUOTES);
      $member_email    = $member->get("E_MAIL");
      $member_link = $canGrant ? "/?projid=$projid&expid=$expid&action=DisplayExperimentMembers&personId=$member_id" : "";

      $treeHTML .= "tree.add('eMemberId_" . $expid . "_" . $member_id . "', 'ExpMembers_" . $expid . "', '$member_fullname', '$member_link', ico_person);\n";
    }
  }
  */
  /**
   * *******************************************************************
   * nid=trialid_<trialid>
   * nid=runid_<trialid>
   * *******************************************************************
   */
  /*
  elseif($typeStr == "trialid" || $typeStr == "runid") {
    $trialid = $tokenArr[1];
    $t = TrialPeer::find($trialid);
    if(!$t) return "";

    $expid = $t->getExperimentId();
    $exp = $t->getExperiment();
    $projid = $exp->getProjectId();

    $tnodeID = $nodeId;
    $url = "/?projid=$projid&expid=$expid&trialid=$trialid";
    $title = clean_newlines($t->getTitle());

    $trialTypeId = $t->getTrialTypeId();

    if($trialTypeId == TrialPeer::CLASSKEY_TRIAL) {

      $trialSetupNode = "trialSetup_" . $trialid . "_" . $expid . "_" . $projid;

      $treeHTML .= "tree.add('$trialSetupNode', '$tnodeID', 'Trial Setup', '$url&action=DisplayTrialSetup', ico_setup);\n";
      $treeHTML .= "tree.add('TrialData_$trialid', '$tnodeID', 'Trial Data', '$url&action=DisplayTrialData', ico_folder);\n";
      $treeHTML .= "tree.add('DataFile_Trial_Analysis_$trialid', '$tnodeID', 'Trial Analysis', '$url&action=DisplayTrialAnalysis', ico_folder);\n";
      $treeHTML .= "tree.add('DataFile_Trial_Documentation_$trialid', '$tnodeID', 'Trial Documentation', '$url&action=DisplayTrialDocumentation', ico_folder);\n";

      $treeHTML .= "tree.setServerLoad('TrialData_$trialid');\n";
      $treeHTML .= "tree.setServerLoad('DataFile_Trial_Analysis_$trialid');\n";
      $treeHTML .= "tree.setServerLoad('DataFile_Trial_Documentation_$trialid');\n";
      $treeHTML .= "tree.setServerLoad('$trialSetupNode');\n";
    }
    elseif($trialTypeId == TrialPeer::CLASSKEY_SIMULATIONRUN) {
      $treeHTML .= "tree.add('" . $tnodeID . "_Files', '$tnodeID', 'Run Files', '$url&action=DisplaySimFiles', ico_folder);\n";
    }
  }
  */
  /**
   * *******************************************************************
   * nid=trialSetup_<trialid>_<expid>_<projid>
   * nid=trialSetup_<trialid>_<expid>_<projid>_<setupType>
   * *******************************************************************
   */
  /*
  elseif($typeStr == "trialSetup") {
    if(sizeof($tokenArr) < 4) return "";

    $trialid = $tokenArr[1];
    $expid   = $tokenArr[2];
    $projid  = $tokenArr[3];

    $url = "/?projid=$projid&expid=$expid&trialid=$trialid";

    if(sizeof($tokenArr) == 4){
      $trialSetupNode = $nodeId;

      $treeHTML .= "tree.add('" . $trialSetupNode . "_Source', '$trialSetupNode', 'Source Controller Configurations', '$url&action=DisplayTrialSetup&subset=Source#s1', ico_setup_section);\n";
      $treeHTML .= "tree.add('" . $trialSetupNode . "_DAQ',    '$trialSetupNode', 'DAQ Configurations',               '$url&action=DisplayTrialSetup&subset=DAQ#s2',    ico_setup_section);\n";
      $treeHTML .= "tree.add('" . $trialSetupNode . "_TrialSensorLocation', '$trialSetupNode', 'Trial Sensor Location Plans', '$url&action=DisplayTrialSetup&subset=TrialSensorLocation#s3', ico_setup_section);\n";
      $treeHTML .= "tree.add('" . $trialSetupNode . "_TrialSourceLocation', '$trialSetupNode', 'Trial Source Location Plans', '$url&action=DisplayTrialSetup&subset=TrialSourceLocation#s4', ico_setup_section);\n";

      $treeHTML .= "tree.setServerLoad('" . $trialSetupNode . "_Source');\n";
      $treeHTML .= "tree.setServerLoad('" . $trialSetupNode . "_DAQ');\n";
      $treeHTML .= "tree.setServerLoad('" . $trialSetupNode . "_TrialSensorLocation');\n";
      $treeHTML .= "tree.setServerLoad('" . $trialSetupNode . "_TrialSourceLocation');\n";
    }
    elseif(sizeof($tokenArr) == 5){
      $trialSetupItemNode = $nodeId;
      $setupType = $tokenArr[4];

      if($setupType == "Source") {
        require_once 'lib/data/ControllerConfig.php';

        $configs = ControllerConfigPeer::findByTrial($trialid);

        foreach($configs as $config) {
          $configId = $config->getId();
          $configName = htmlspecialchars($config->getName(), ENT_QUOTES);

          $treeHTML .= "tree.add('sourceConfigId_$configId', '$trialSetupItemNode', '$configName', '$url&clid=$configId&action=DisplaySourceControllerConfiguration', ico_setup_item);\n";
        }
      }
      elseif($setupType == "DAQ") {
        require_once 'lib/data/DAQConfig.php';

        $configs = DAQConfigPeer::findByTrial($trialid);

        foreach($configs as $config) {
          $configId = $config->getId();
          $configName = htmlspecialchars($config->getName(), ENT_QUOTES);

          $treeHTML .= "tree.add('DAQConfigId_$configId', '$trialSetupItemNode', '$configName', '$url&clid=$configId&action=DisplayDAQConfiguration', ico_setup_item);\n";
        }
      }
      elseif($setupType == "TrialSensorLocation") {
        require_once 'lib/data/LocationPlan.php';

        $slps = LocationPlanPeer::findSensorLocationPlanByTrial($trialid);

        foreach($slps as $slp) {
          $lpid = $slp->getId();
          $slpName = htmlspecialchars($slp->getName(), ENT_QUOTES);

          $treeHTML .= "tree.add('TrialSensorLocationId_$lpid', '$trialSetupItemNode', '$slpName', '$url&lpid=$lpid&action=DisplayTrialSensorLocationPlan', ico_setup_item);\n";
        }
      }
      elseif($setupType == "TrialSourceLocation") {
        require_once 'lib/data/LocationPlan.php';

        $slps = LocationPlanPeer::findSourceLocationPlanByTrial($trialid);

        foreach($slps as $slp) {
          $lpid = $slp->getId();
          $slpName = htmlspecialchars($slp->getName(), ENT_QUOTES);

          $treeHTML .= "tree.add('TrialSourceLocationId_$lpid', '$trialSetupItemNode', '$slpName', '$url&lpid=$lpid&action=DisplayTrialSourceLocationPlan', ico_setup_item);\n";
        }
      }
    }
  }
  */
  /**
   * *******************************************************************
   * nid=specimenId_<specimenId>
   * *******************************************************************
   */
  /*
  elseif($typeStr == "specimenId") {
    $specimenNodeID = $nodeId;
    require_once 'lib/data/Specimen.php';

    $specimenId = $tokenArr[1];

    $specimen = SpecimenPeer::find($specimenId);
    if(!$specimen) return "";

    $project = $specimen->getProject();
    if(!$project) return "";

    $projid = $project->getId();

    $url   = "/?projid=$projid&specimenId=$specimenId";

    $treeHTML .= "tree.add('specimenComponents_$specimenId', '$specimenNodeID', 'Specimen-Component List', '$url&action=ListSpecimenComponents', ico_specCompList);\n";
    $treeHTML .= "tree.add('DataFile_Specimen_Documentation_$specimenId', '$specimenNodeID', 'Specimen Documentation', '$url&action=DisplaySpecimenDocumentation', ico_folder);\n";

    $treeHTML .= "tree.setServerLoad('specimenComponents_$specimenId');\n";
    $treeHTML .= "tree.setServerLoad('DataFile_Specimen_Documentation_$specimenId');\n";
  }
*/
  /**
   * *******************************************************************
   * nid=specimenComponents_<specimenId>
   * *******************************************************************
   */
  /*
  elseif($typeStr == "specimenComponents") {

    require_once 'lib/data/Specimen.php';
    require_once 'lib/data/SpecimenComponent.php';

    $specimenId = $tokenArr[1];

    $specimen = SpecimenPeer::find($specimenId);
    if(!$specimen) return "";

    $project = $specimen->getProject();
    if(!$project) return "";

    $projid = $project->getId();

    $specComps = SpecimenComponentPeer::findBySpecimen($specimenId);

    foreach($specComps as $specComp) {
      $specCompId = $specComp->getId();
      $specCompName = clean_newlines($specComp->getName());
      $url   = "/?projid=$projid&specimenId=$specimenId&specCompId=$specCompId";

      $materialsNodeId = "SpecimenComponentMaterials_" . $specCompId;

      $specCompNodeID = "specCompId_" . $specCompId;
      $treeHTML .= "tree.add('$specCompNodeID', '$nodeId', '$specCompName', '$url&action=DisplaySpecimenComponent', ico_specimen_component);\n";
      $treeHTML .= "tree.add('$materialsNodeId', '$specCompNodeID', 'Properties', '$url&action=ListSpecimenComponentProperties', ico_setup);\n";
      $treeHTML .= "tree.add('DataFile_SpecimenComponent_Documentation_$specCompId', '$specCompNodeID', 'Specimen-Component Documentation', '$url&action=DisplaySpecimenComponentDocumentation', ico_folder);\n";

      $treeHTML .= "tree.setServerLoad('$materialsNodeId');\n";
      $treeHTML .= "tree.setServerLoad('DataFile_SpecimenComponent_Documentation_$specCompId');\n";
    }
  }
*/
  /**
   * *******************************************************************
   * nid=SpecimenComponentMaterials_<specimenComponentId>
   * *******************************************************************
   */
  /*
  elseif($typeStr == "SpecimenComponentMaterials") {

    require_once 'lib/data/Specimen.php';
    require_once 'lib/data/SpecimenComponent.php';
    require_once 'lib/data/SpecimenComponentMaterial.php';

    $specCompId = $tokenArr[1];

    $materials = SpecimenComponentMaterialPeer::findByComponent($specCompId);

    if(sizeof($materials) == 0) return;

    $specComp = SpecimenComponentPeer::find($specCompId);
    if(!$specComp) return;

    $specimen = $specComp->getSpecimen();
    if(!$specimen) return;

    $projid = $specimen->getProjectId();

    $url = "/?projid=$projid&specimenId=" . $specimen->getId() . "&specCompId=$specCompId";

    foreach($materials as $material) {
      ////@var $material SpecimenComponentMaterial 
      $materialId = $material->getId();
      $treeHTML .= "tree.add('SpecimenComponentMaterialId_" . $materialId . "', '$nodeId', '" . $material->getName() . "', '$url&materialId=$materialId&action=DisplaySpecimenComponentProperty', ico_setup_section);\n";
    }
  }
*/

  //

  /**
   * *******************************************************************
   * nid=coordinatorId_<coordinatorId>
   * *******************************************************************
   *
  elseif($typeStr == "coordinatorId") {
    $coordinatorNodeID = $nodeId;
    require_once 'lib/data/Coordinator.php';

    $coordinatorId = $tokenArr[1];

    $coordinator = CoordinatorPeer::find($coordinatorId);
    if(!$coordinator) return "";

    $project = $coordinator->getProject();
    if(!$project) return "";
    if(!$project->isHybridProject()) return "";

    $projid = $project->getId();

    $url   = "/?projid=$projid&coordinatorId=$coordinatorId";

    $treeHTML .= "tree.add('coordinatorRuns_" . $coordinatorId . "', '$coordinatorNodeID', 'Coordinator-Run List', '$url&action=ListCoordinatorRuns', ico_coordinatorRunList);\n";
    $treeHTML .= "tree.add('DataFile_Coordinator_Documentation_$coordinatorId', '$coordinatorNodeID', 'Coordinator Documentation', '$url&action=DisplayCoordinatorDocumentation', ico_folder);\n";

    $treeHTML .= "tree.setServerLoad('coordinatorRuns_$coordinatorId');\n";
    $treeHTML .= "tree.setServerLoad('DataFile_Coordinator_Documentation_$coordinatorId');\n";
  }

  **
   * *******************************************************************
   * nid=coordinatorRuns_<coordinatorId>
   * *******************************************************************
   *
  elseif($typeStr == "coordinatorRuns") {

    require_once 'lib/data/Coordinator.php';
    require_once 'lib/data/CoordinatorRun.php';

    $coordinatorId = $tokenArr[1];

    $coordinator = CoordinatorPeer::find($coordinatorId);
    if(!$coordinator) return "";

    $project = $coordinator->getProject();
    if(!$project) return "";
    if(!$project->isHybridProject()) return "";

    $projid = $project->getId();

    $coordinatorRuns = CoordinatorRunPeer::findByCoordinator($coordinatorId);

    foreach($coordinatorRuns as $coordinatorRun) {
      $coordinatorRunId = $coordinatorRun->getId();
      $coordinatorRunName = clean_newlines($coordinatorRun->getName());
      $url   = "/?projid=$projid&coordinatorId=$coordinatorId&coordinatorRunId=$coordinatorRunId";

      $coordinatorRunNodeID = "coordinatorRunId_" . $coordinatorRunId;
      $treeHTML .= "tree.add('$coordinatorRunNodeID', '$nodeId', '$coordinatorRunName', '$url&action=DisplayCoordinatorRun', ico_coordinatorRun);\n";
      $treeHTML .= "tree.setServerLoad('$coordinatorRunNodeID');\n";
    }
  }

  **
   * *******************************************************************
   * nid=coordinatorRunId_<coordinatorRunId>
   * *******************************************************************
   *
  elseif($typeStr == "coordinatorRunId") {

    require_once 'lib/data/Coordinator.php';
    require_once 'lib/data/CoordinatorRun.php';

    $coordinatorRunId = $tokenArr[1];
    $coordinatorRunNodeID = $nodeId;

    $coordinatorRun = CoordinatorRunPeer::find($coordinatorRunId);
    if(!$coordinatorRun) return "";

    $coordinator = $coordinatorRun->getCoordinator();
    if(!$coordinator) return "";

    $project = $coordinator->getProject();
    if(!$project) return "";
    if(!$project->isHybridProject()) return "";

    $coordinatorId = $coordinator->getId();
    $projid = $project->getId();

    $url   = "/?projid=$projid&coordinatorId=$coordinatorId&coordinatorRunId=$coordinatorRunId";

    $treeHTML .= "tree.add('" . $coordinatorRunNodeID . "_PhysicalSubstructures', '$coordinatorRunNodeID', 'Physical Substructures', '$url&action=ListPhysicalSubstructures', ico_e);\n";
    $treeHTML .= "tree.add('" . $coordinatorRunNodeID . "_AnalyticalSubstructures', '$coordinatorRunNodeID', 'Analytical Substructures', '$url&action=ListAnalyticalSubstructures', ico_s);\n";
    $treeHTML .= "tree.add('DataFile_CoordinatorRun_Files_$coordinatorRunId', '$coordinatorRunNodeID', 'Coordinator-Run Files', '$url&action=DisplayCoordinatorRunFiles', ico_folder);\n";
    $treeHTML .= "tree.add('DataFile_CoordinatorRun_Analysis_$coordinatorRunId', '$coordinatorRunNodeID', 'Coordinator-Run Analysis', '$url&action=DisplayCoordinatorRunAnalysis', ico_folder);\n";
    $treeHTML .= "tree.add('DataFile_CoordinatorRun_Documentation_$coordinatorRunId', '$coordinatorRunNodeID', 'Coordinator-Run Documentation', '$url&action=DisplayCoordinatorRunDocumentation', ico_folder);\n";

    $treeHTML .= "tree.setServerLoad('DataFile_CoordinatorRun_Files_$coordinatorRunId');\n";
    $treeHTML .= "tree.setServerLoad('DataFile_CoordinatorRun_Analysis_$coordinatorRunId');\n";
    $treeHTML .= "tree.setServerLoad('DataFile_CoordinatorRun_Documentation_$coordinatorRunId');\n";

    $auth = Authorizer::getInstance();
    $uid = $auth->getUserId();

    $physicalSubstructures = $coordinatorRun->getPhysicalSubstructures();
    $analyticalSubstructures = $coordinatorRun->getAnalyticalSubstructures();

    $curatedExperimentsMap = NCCuratedObjectsPeer::getCuratedExperimentsMap();

    foreach($physicalSubstructures as $e) {
      $exp_action = "DisplayExperimentMain";
      $exp_ico = 'ico_ec';

      $expid = $e->getId();
      $title = clean_newlines($e->getTitle());
      $url   = "/?projid=$projid&expid=$expid";

      if($e->isPublished()) {
        $exp_ico = 'ico_ep';
        $title .= " (publicly accessible)";
      }

      if(in_array($expid, $curatedExperimentsMap)) {
        $exp_ico = 'ico_ce';
        $title .= " (curated)";
      }

      $enodeID = "expid_" . $expid;
      $treeHTML .= "tree.add('$enodeID', '" . $coordinatorRunNodeID . "_PhysicalSubstructures',   '$title', '$url&action=$exp_action', $exp_ico);\n";
      $treeHTML .= "tree.setServerLoad('$enodeID');\n";
    }

    foreach($analyticalSubstructures as $e) {
      $exp_action = "DisplaySimulationMain";
      $exp_ico = 'ico_sc';

      $expid = $e->getId();
      $title = clean_newlines($e->getTitle());
      $url   = "/?projid=$projid&expid=$expid";

      if($e->isPublished()) {
        $exp_ico = 'ico_sp';
        $title .= " (publicly accessible)";
      }

      if(in_array($expid, $curatedExperimentsMap)) {
        $exp_ico = 'ico_cs';
        $title .= " (curated)";
      }

      $enodeID = "expid_" . $expid;
      $treeHTML .= "tree.add('$enodeID', '" . $coordinatorRunNodeID . "_AnalyticalSubstructures',   '$title', '$url&action=$exp_action', $exp_ico);\n";
      $treeHTML .= "tree.setServerLoad('$enodeID');\n";
    }

  }
  elseif ($typeStr == "TrialData") {
    $trialid = $tokenArr[1];
    $trial = TrialPeer::find($trialid);
    $auth = Authorizer::getInstance();

    if($trial) {
      $experiment = $trial->getExperiment();
      $expid = $experiment->getId();
      $projid = $experiment->getProjectId();

      if($auth->canView($experiment)) {
        $reps = RepetitionPeer::findByTrial($trialid);
        foreach($reps as $rep) {
          ///// @var $rep Repetition 

          $repid = $rep->getId();
          $repName = $rep->getName();
          $repURL = "/?projid=$projid&expid=$expid&trialid=$trialid&Rep=$repName&action=DisplayTrialData";

          $treeHTML .= "tree.add('Repetition_$repid', '$nodeId', '$repName', '$repURL', ico_folder);\n";

          $dfs = DataFilePeer::findByDirectoryWithOrderBy($rep->getPathname());

          foreach($dfs as $df) {
            //// @var $df DataFile 
            $df_name = $df->getName();
            $df_id = $df->getId();
            $is_dir = $df->isDirectory();

            if(file_exists($df->getFullPath())) {
              if($is_dir) {
                $df_url = $df->get_url();
                $treeHTML .= "tree.add('DataFile_$df_id', 'Repetition_$repid', '$df_name', '$df_url', ico_dir);\n";
                $treeHTML .= "tree.setServerLoad('DataFile_$df_id');\n";
              }
              else {
                $df_url = $df->get_url();
                $treeHTML .= "tree.add('DataFile_$df_id', 'Repetition_$repid', '$df_name', '$df_url', '');\n";
                $treeHTML .= "tree.setNodeTarget('DataFile_$df_id', '_blank');\n";
              }
            }
          }
        }
      }
    }
  }
  elseif ($typeStr == "DataFile") {

    $dfs = array();

    if(sizeof($tokenArr) == 2) {
      $df_dir_id = $tokenArr[1];

      $df_dir = DataFilePeer::find($df_dir_id);

      if($df_dir) {
        $dfs = DataFilePeer::findByDirectoryWithOrderBy($df_dir->getFullPath());
      }
    }
    elseif (sizeof($tokenArr) == 4) {
      $entityType = $tokenArr[1];
      $folderType = $tokenArr[2];
      $entityId = $tokenArr[3];

      $entity = null;
      $permissionEntity = null;

      $auth = Authorizer::getInstance();

      if($entityType == "Project") {
        $entity = ProjectPeer::find($entityId);
        $permissionEntity = $entity;
      }
      elseif($entityType == "Experiment") {
        $entity = ExperimentPeer::find($entityId);
        $permissionEntity = $entity;
      }
      elseif($entityType == "Trial") {
        $entity = TrialPeer::find($entityId);
        if($entity) {
          $permissionEntity = $entity->getExperiment();
        }
      }
      elseif($entityType == "Repetition") {
        $entity = RepetitionPeer::find($entityId);
        if($entity) {
          $permissionEntity = $entity->getTrial()->getExperiment();
        }
      }
      elseif($entityType == "Specimen") {
        include_once 'lib/data/Specimen.php';

        $entity = SpecimenPeer::find($entityId);
        if($entity) {
          $permissionEntity = $entity->getProject();
        }
      }
      elseif($entityType == "SpecimenComponent") {
        include_once 'lib/data/SpecimenComponent.php';

        $entity = SpecimenComponentPeer::find($entityId);
        if($entity) {
          $permissionEntity = $entity->getSpecimen()->getProject();
        }
      }
      elseif($entityType == "Coordinator") {
        include_once 'lib/data/Coordinator.php';

        $entity = CoordinatorPeer::find($entityId);
        if($entity) {
          $permissionEntity = $entity->getProject();
        }
      }
      elseif($entityType == "CoordinatorRun") {
        include_once 'lib/data/CoordinatorRun.php';

        $entity = CoordinatorRunPeer::find($entityId);
        if($entity) {
          $permissionEntity = $entity->getCoordinator()->getProject();
        }
      }

      if(!is_null($entity) && !is_null($permissionEntity)) {
        $canview = $auth->canView($permissionEntity);

        if($canview || $folderType == "Public") {
          $dfs = DataFilePeer::findByDirectoryWithOrderBy($entity->getPathname() . "/" . $folderType);
        }
        elseif($canview && $folderType == "Categories") {
          $dfs = DataFilePeer::findByDirectoryWithOrderBy($entity->getPathname());
        }
      }
    }

    foreach($dfs as $df) {
      // @var $df DataFile 
      $df_name = htmlentities($df->getName(),ENT_QUOTES);
      $df_id = $df->getId();
      $is_dir = $df->isDirectory();

      if(file_exists($df->getFullPath())) {
        $df_url = htmlentities($df->get_url(),ENT_QUOTES);
        if($is_dir) {
          $treeHTML .= "tree.add('DataFile_$df_id', '$nodeId', '$df_name', '$df_url', ico_dir);\n";
          $treeHTML .= "tree.setServerLoad('DataFile_$df_id');\n";
        }
        else {
          $treeHTML .= "tree.add('DataFile_$df_id', '$nodeId', '$df_name', '$df_url', '');\n";
          $treeHTML .= "tree.setNodeTarget('DataFile_$df_id', '_blank');\n";
        }
      }
    }
  }

  **
   * *******************************************************************
   * nid=facid_<facid>
   * *******************************************************************
   *
  elseif($typeStr == "facid") {
    $facid = $tokenArr[1];
    $fNode = $nodeId;

    $treeHTML .= "tree.add('$fNode" . "_contact',   '$fNode', 'Contact Information',              '/?facid=$facid&action=DisplayFacilityContact',                  '');\n";
    $treeHTML .= "tree.add('facStaff_$facid',       '$fNode', 'Staff List',                       '/?facid=$facid&action=DisplayFacilityStaff',                    ico_member);\n";
    $treeHTML .= "tree.add('facEquip_$facid',       '$fNode', 'Major Equipment',                  '/?facid=$facid&action=DisplayFacilityEquipment',                ico_equiplist);\n";
    $treeHTML .= "tree.add('facSensor_$facid',      '$fNode', 'Sensors List by SensorModel',      '/?facid=$facid&action=ListFacilitySensors',                     ico_sensors_list);\n";
    $treeHTML .= "tree.add('$fNode" . "_training',  '$fNode', 'Training And Certification',       '/?facid=$facid&action=DisplayFacilityTrainingAndCertification', ico_cert);\n";
    $treeHTML .= "tree.add('$fNode" . "_education', '$fNode', 'Education and Outreach Documents', '/?facid=$facid&action=DisplayFacilityEducationOutreach',        '');\n";
    $treeHTML .= "tree.setServerLoad('facEquip_$facid');\n";
    $treeHTML .= "tree.setServerLoad('facSensor_$facid');\n";
    $treeHTML .= "tree.setServerLoad('facStaff_$facid');\n";
  }
*/
  /**
   * *******************************************************************
   * nid=facStaff_<facid>
   * *******************************************************************
   *
  elseif($typeStr == "facStaff") {
    $facid = $tokenArr[1];
    $staffNode = $nodeId;

    $members = PersonPeer::findMembersPermissionsForEntity($facid, 20);

    while($members->next()) {
      $personid = $members->get("PERSON_ID");
      $lastname = $members->get("LAST_NAME");
      $firstname = $members->get("FIRST_NAME");
      $firstlast = htmlspecialchars($firstname . " " . $lastname);
      $staffLink = "/?action=DisplayFacilityStaff&facid=$facid&personId=$personid&viewDetail=1";

      $treeHTML .= "tree.add('$staffNode" . "_$personid', '$staffNode', '$firstlast', '$staffLink', ico_person);\n";
    }
  }
*/
  /**
   * *******************************************************************
   * nid=facEquip_<facid>
   * *******************************************************************
   */
  /*
  elseif($typeStr == "facEquip") {
    $facid = $tokenArr[1];
    $equipNode = $nodeId;

    $sql = "SELECT o.orgid, e.name as eqname, e.equipment_id
          FROM
            Equipment e,
            Organization o
          WHERE
            e.major=1 AND
            o.orgid = e.orgid AND
            o.orgid = $facid
          ORDER BY
            e.name";

    $conn = Propel::getConnection();
    $stmt = $conn->createStatement();
    $rs = $stmt->executeQuery($sql, ResultSet::FETCHMODE_ASSOC);

    while($rs->next()) {
      $eqId = $rs->getInt('EQUIPMENT_ID');
      $eqname = htmlspecialchars($rs->getString('EQNAME'), ENT_QUOTES);

      $treeHTML .= "tree.add('MajorEquipId_" . $eqId . "', '$equipNode', '$eqname', '/?facid=$facid&equipid=$eqId&action=DisplayEquipmentList', ico_equip);\n";
      $treeHTML .= "tree.setServerLoad('MajorEquipId_" . $eqId . "');\n";
    }
  }
  */
  /**
   * *******************************************************************
   * nid=MajorEquipId_<equipmentId>
   * *******************************************************************
   */
  /*
  elseif($typeStr == "MajorEquipId") {
    $equipid = $tokenArr[1];
    $majorEquipNode = $nodeId;

    $majorEq = EquipmentPeer::find($equipid);
    $facid = $majorEq->getOrganizationId();
    $subComponents = EquipmentPeer::findAllByParent($equipid);

    foreach($subComponents as $subcomp) {
      $subId = $subcomp->getId();
      $subcompLink = "/?facid=$facid&equipid=$subId&action=DisplayEquipmentList";
      $subName = htmlspecialchars($subcomp->getName(), ENT_QUOTES);

      $treeHTML .= "tree.add('subEquip_$subId', '$majorEquipNode', '$subName', '$subcompLink', ico_sub_equip);\n";
    }
  }
  */
  /**
   * *******************************************************************
   * nid=facSensor_<facid>
   * *******************************************************************
   *
  elseif($typeStr == "facSensor") {
    $facid = $tokenArr[1];
    $smListNode = $nodeId;

    $sql = "SELECT
              o.short_name,
              smod.name,
              smod.sensor_model_id,
              count(smod.sensor_model_id) as quantity
            FROM
              sensor s,
              Sensor_manifest sm,
              organization o,
              sensor_sensor_manifest ssm,
              sensor_model smod
            WHERE
              ssm.sensor_id=s.sensor_id AND
              s.sensor_model_id = smod.sensor_model_id AND
              ssm.manifest_id=sm.id AND
              o.sensor_manifest_id=sm.id AND
              s.deleted = 0 AND
              o.orgid= '$facid'
            GROUP BY
              o.short_name,
              smod.name,
              smod.sensor_model_id
            ORDER BY
              smod.name";

    $conn = Propel::getConnection();
    $stmt = $conn->prepareStatement($sql);
    $rs = $stmt->executeQuery(ResultSet::FETCHMODE_ASSOC);

    while($rs->next()) {
      $smId = $rs->getInt('SENSOR_MODEL_ID');
      $smName = htmlspecialchars($rs->getString('NAME'), ENT_QUOTES);
      $quantity = $rs->getInt('QUANTITY');
      $smNode = "smid_$smId" . "_facid_$facid";

      $treeHTML .= "tree.add('$smNode', '$smListNode', '($quantity) $smName', '/?facid=$facid&sensorModel=$smId&action=ListFacilitySensors', ico_sensor_model);\n";
      $treeHTML .= "tree.setServerLoad('$smNode');\n";
    }
  }
  */
  /**
   * *******************************************************************
   * nid=smid_<sensorModelId>
   * *******************************************************************
   *
  elseif($typeStr == "smid") {

    if(sizeof($tokenArr) != 4) return "";
    $smNode = $nodeId;
    $smid = $tokenArr[1];
    $facid = $tokenArr[3];

    $sensors = SensorPeer::findByFacilityAndSensorModel($facid, $smid);

    foreach($sensors as $sensor) {
      $sensorName = htmlspecialchars($sensor->getName(), ENT_QUOTES);
      $sensorId = $sensor->getId();
      $sensorLink = "/?action=DisplaySensor&sensor=$sensorId&facid=$facid";

      $treeHTML .= "tree.add('sensor_$sensorId', '$smNode', '$sensorName', '$sensorLink', ico_sensor);\n";
    }
  }
  */
  /**
   * *******************************************************************
   * nid=activityFacid_<facid>
   * *******************************************************************
   *
  elseif($typeStr == "activityFacid") {
    if(sizeof($tokenArr) != 3) return "";

    $facid = $tokenArr[1];
    $isAdmin = $tokenArr[2];
    $fNode = $nodeId;

    include_once 'lib/data/Facility.php';
    $fac = FacilityPeer::find($facid);

    $flexURL = $fac->getFlexTpsUrl();
    preg_match("/^(https?:\/\/)?([a-zA-Z0-9\\-\\.\\/]+)(\/?site\/?|\/?feeds\/?|\/?collaboration\/?|\/?portal\/?|\/?dvr\/?)?$/Ui", $flexURL, $matches);
    $cleanflexURL = isset($matches[2]) ? "http://" . rtrim($matches[2], '/') : "";

    if(!empty($cleanflexURL)) {
      ini_set('default_socket_timeout', 5);
      $xmlresult = @file_get_contents("$cleanflexURL/feeds");

      if($xmlresult !== false) {
        preg_match_all("/<stream\s+id=\"([^\"]*)\"\s+xlink:href=\"([^\"]*)\">/", $xmlresult, $matches, PREG_SET_ORDER);
        $feedcount = sizeof($matches);

        if($feedcount > 0) {
          $activeFeedsNode = "activityFeeds_$facid";
          $treeHTML .= "tree.add('$activeFeedsNode', '$fNode', 'Active Feeds (" . $feedcount . ")', '/activities/?facid=$facid&eloc=Feeds', ico_videofeed);\n";
        }
      }
    }

    if(!$isAdmin) {
      $userAdmins = $fac->getNawiAdminUsers();
      $userSOM    = $fac->getSiteOpUser();
      $userSysAd  = $fac->getSysadminUser();

      $authenticator = Authenticator::getInstance();
      $username = $authenticator->getUserName();

      $acceptList = array();
      if( ! empty($userSOM)) $acceptList[] = $userSOM;
      if( ! empty($userSysAd)) $acceptList[] = $userSysAd;

      foreach(explode(",", $userAdmins) as $userAdmin) {
        if( ! in_array($userAdmin, $acceptList)) {
          if(!empty($userAdmin)) {
            $acceptList[] = $userAdmin;
          }
        }
      }

      if($username && in_array($username, $acceptList)) {
        $isAdmin = true;
      }
    }
    if($isAdmin) {
      $configNode = "activityConfig_$facid";
      $treeHTML .= "tree.add('$configNode', '$fNode', 'Configure Site Experiments (Admin only)', '/activities/?facid=$facid&eloc=Config', ico_equiplist);\n";
      $treeHTML .= "tree.setServerLoad('$configNode');\n";
    }
  }
  */
  /**
   * *******************************************************************
   * nid=activityConfig_<facid>
   * *******************************************************************
   *
  elseif($typeStr == "activityConfig") {
    $facid = $tokenArr[1];
    $configNode = $nodeId;

    $configSiteNode = "activityConfigSite_$facid";
    $treeHTML .= "tree.add('$configSiteNode', '$configNode', 'Facility Configuration', '/activities/?facid=$facid&eloc=Facility', ico_fc);\n";

    $configNewNode = "activityConfigNew_$facid";
    $treeHTML .= "tree.add('$configNewNode', '$configNode', 'Add New Experiment', '/activities/?facid=$facid&eloc=New', ico_plus);\n";

    include_once 'lib/data/NAWIFacility.php';
    $nawifacs = NAWIFacilityPeer::findByFacility($facid);

    foreach ( $nawifacs as $nawifac ) {
      $nawi = $nawifac->getNAWI();
      $exp_name = stripslashes( $nawi->getExperimentName() );
      $nawiid = $nawi->getId();

      $nawiNode = "nawiid_$nawiid";
      $treeHTML .= "tree.add('$nawiNode', '$configNode', 'Edit: $exp_name', '/activities/?facid=$facid&nawiid=$nawiid&eloc=Edit', ico_equip);\n";
    }
  }
*/
  return $treeHTML;
}

/**
 * Output Javascript Code for Specimen
 *
 * @param Specimen $specimen
 * @param int $projid
 * @param String $parentNodeID
 * @return String $html
 */
function printSpecimen(Specimen $specimen, $projid, $parentNodeID) {

  $specimenId = $specimen->getId();
  $specimenName = clean_newlines($specimen->getName());
  $url   = "/?projid=$projid&specimenId=$specimenId";

  $specimenNodeID = "specimenId_" . $specimenId;
  $treeHTML = "";
  $treeHTML .= "tree.add('$specimenNodeID', '$parentNodeID', '$specimenName', '$url&action=DisplaySpecimen', ico_specimen);\n";
  $treeHTML .= "tree.add('specimenComponents_" . $specimenId . "', '$specimenNodeID', 'Specimen-Component List', '$url&action=ListSpecimenComponents', ico_specCompList);\n";
  $treeHTML .= "tree.add('DataFile_Specimen_Documentation_$specimenId', '$specimenNodeID', 'Specimen Documentation', '$url&action=DisplaySpecimenDocumentation', ico_folder);\n";

  $treeHTML .= "tree.setServerLoad('specimenComponents_$specimenId');\n";
  $treeHTML .= "tree.setServerLoad('DataFile_Specimen_Documentation_$specimenId');\n";

  return $treeHTML;
}

/**
 * Output Javascript Code for Coordinator
 *
 * @param Coordinator $coordinator
 * @param int $projid
 * @param String $parentNodeID
 * @return String $html
 */
function printCoordinator(Coordinator $coordinator, $projid, $parentNodeID) {

  $coordinatorId = $coordinator->getId();
  $coordinatorName = clean_newlines($coordinator->getName());
  $url   = "/?projid=$projid&coordinatorId=$coordinatorId";

  $coordinatorNodeID = "coordinatorId_" . $coordinatorId;
  $treeHTML = "";
  $treeHTML .= "tree.add('$coordinatorNodeID', '$parentNodeID', '$coordinatorName', '$url&action=DisplayCoordinator', ico_coordinator);\n";
  $treeHTML .= "tree.add('coordinatorRuns_" . $coordinatorId . "', '$coordinatorNodeID', 'Coordinator-Run List', '$url&action=ListCoordinatorRuns', ico_coordinatorRunList);\n";
  $treeHTML .= "tree.add('DataFile_Coordinator_Documentation_$coordinatorId', '$coordinatorNodeID', 'Coordinator Documentation', '$url&action=DisplayCoordinatorDocumentation', ico_folder);\n";

  $treeHTML .= "tree.setServerLoad('coordinatorRuns_$coordinatorId');\n";
  $treeHTML .= "tree.setServerLoad('DataFile_Coordinator_Documentation_$coordinatorId');\n";

  return $treeHTML;
}


function clean_newlines($string) {
  return preg_replace(array ("/\n/","/(?<=\S)\n(?=\S)/","/(?<=\S)\ +\n(?=\S)/","/(?<=\S)\r\n(?=\S)/","/(?<=\S)\ +\r\n(?=\S)/"), " ", htmlspecialchars($string, ENT_QUOTES));
}

Header("Cache-Control: no-cache");

?>
<head>
</head>
<body>

<script type="text/javascript">
<!--
  //parent refer to main window since the tree definition is in parent window.
with (parent) {

  var nId = '<?= $nodeId ?>';

  //remove existing child nodes in parent frame
  //tree.removeChilds(nId, false);

  //add new item loaded from database to the node in parent frame
  <?= getTreeNodes($nodeId) ?>

   //reload the node to render the new nodes
  tree.reloadNode(nId);
  tree.expandNode(nId);
}
//-->
</script>
</body>
</html>
