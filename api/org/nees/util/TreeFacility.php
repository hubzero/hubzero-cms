<?php

class TreeFacility {

  private $NEESEquipmentSiteList = array(
    "Field Experiments/Monitoring" => array(
      226 => "University of CA, Los Angeles",
      228 => "University of CA, Santa Barbara",
      280 => "University of TX, Austin"
    ),

    "Geotechnical Centrifuges" => array(
      205 => "Rensselaer Polytechnic Institute",
      276 => "University of CA, Davis"
    ),

    "Large Scale Laboratories" => array(
      180 => "Cornell University",
      191 => "Lehigh University",
      275 => "University of CA, Berkeley",
      236 => "University of IL, Urbana",
      244 => "University of Minnesota"
    ),

    "Shake Tables" => array(
      274 => "University at Buffalo, SUNY",
      277 => "University of CA, San Diego",
      279 => "University of Nevada, Reno"
    ),

    "Tsunami Wave Basins" => array(
      200 => "Oregon State University"
    )
  );

  private $facilityMap = array(
    226 => "UCLA",
    228 => "UCSB",
    280 => "UTexas",
    205 => "RPI",
    276 => "UCDavis",
    180 => "Cornell",
    191 => "Lehigh",
    275 => "Berkeley",
    236 => "UIUC",
    244 => "UMN",
    274 => "Buffalo",
    277 => "UCSD",
    279 => "UNR",
    200 => "OrSt"
  );

  private $facid;
  private $action;
  private $equipid;
  private $subEquipId;
  private $sensor_model_id;
  private $sensor_id;
  private $is_equip;
  private $is_sensor;
  private $is_staff;

  public function __construct() {

    $facid = isset($_REQUEST['facid']) ? $_REQUEST['facid'] : null;

    if($facid) {
      // example: facid=UCSD
      if(in_array($facid, $this->facilityMap)) {
        $this->facid = array_search($facid, $this->facilityMap);
      }
      // example: facid=277
      elseif(array_key_exists($facid, $this->facilityMap)) {
        $this->facid = $facid;
      }
    }

    $this->action = isset($_REQUEST["action"]) ? $_REQUEST["action"] : "";

    $this->equipid = isset($_REQUEST["equipid"]) ? $_REQUEST["equipid"] : null;
    if(! $this->equipid && isset($_REQUEST["parentid"])) $this->equipid = $_REQUEST["parentid"];

    if($this->equipid) {
      $equip = EquipmentPeer::find($this->equipid);

      // In case equipid is a sub component, not a major component
      if($equip) {
        if($equip->getParent()) {
          $this->subEquipId = $this->equipid;
          $this->equipid = $equip->getParent()->getId();
        }
      }
    }

    $this->is_staff = $this->action == "DisplayFacilityStaff";
    $this->sensor_id = isset($_REQUEST["sensor"]) ? $_REQUEST["sensor"] : null;
    $this->sensor_model_id = isset($_REQUEST["sensorModel"]) ? $_REQUEST["sensorModel"] : null;

    if($this->sensor_id) {
      $sensor = SensorPeer::find($this->sensor_id);
      if($sensor) $this->sensor_model_id = $sensor->getSensorModelId();
    }

    $this->is_equip   = (strpos($this->action, "Equipment") !== false || $this->equipid > 0);
    $this->is_sensor  = (strpos($this->action, "Sensor") !== false || strpos($this->action, "Calibration") !== false || $this->sensor_id > 0 || $this->sensor_model_id > 0);
  }


  public function get_tree_facility()
  {

    $selectedNode = 'root';

    $treeHTML = "\ntree.add('root', 0, '&nbsp;&nbsp;NEES Equipment Site', '', '', true);\n";

    $groupId = 0;

    foreach ($this->NEESEquipmentSiteList as $sitekey => $sitevalue) {

      $treeHTML .= "tree.add('group_" . ++$groupId . "', 'root', '$sitekey', '', ico_nees, true);\n";

      foreach($sitevalue as $facid => $facvalue) {

        $fNode = "facid_" . $facid;

        $treeHTML .= "tree.add('$fNode', 'group_" . $groupId . "', '$facvalue', '/?facid=$facid&action=DisplayFacility', ico_fc);\n";

        if($this->facid != $facid) {
          $treeHTML .= "tree.setServerLoad('$fNode');\n";
        }
        else {
          if($facid == $this->facid) $selectedNode = $fNode;

          $equipNode = "facEquip_$facid";
          $staffNode = "facStaff_$facid";

          $treeHTML .= "tree.add('$fNode" . "_contact', '$fNode', 'Contact Information', '/?facid=$facid&action=DisplayFacilityContact', '');\n";
          $treeHTML .= "tree.add('$staffNode', '$fNode', 'Staff List', '/?facid=$facid&action=DisplayFacilityStaff', ico_member);\n";
          $treeHTML .= "tree.add('$equipNode', '$fNode', 'Major Equipment', '/?facid=$facid&action=DisplayFacilityEquipment', ico_equiplist);\n";

          if($this->is_staff){
            $selectedNode = $staffNode;

            $r_personId = isset($_REQUEST['personId']) ? $_REQUEST['personId'] : -1;
            $members = PersonPeer::findMembersPermissionsForEntity($facid, 20);

            while($members->next()) {
              $personid = $members->get("PERSON_ID");
              $lastname = $members->get("LAST_NAME");
              $firstname = $members->get("FIRST_NAME");
              $firstlast = htmlspecialchars($firstname . " " . $lastname);
              $staffLink = "/?action=DisplayFacilityStaff&facid=$facid&personId=$personid&viewDetail=1";

              $treeHTML .= "tree.add('$staffNode" . "_$personid', '$staffNode', '$firstlast', '$staffLink', ico_person);\n";

              if($r_personId == $personid) {
                $selectedNode = $staffNode . "_" . $personid;
              }
            }
          }
          else {
            $treeHTML .= "tree.setServerLoad('$staffNode');\n";
          }

          if(!$this->is_equip) {
            $treeHTML .= "tree.setServerLoad('$equipNode');\n";
          }
          else {
            $selectedNode = $equipNode;

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

              if($this->equipid == $eqId) {
                $selectedNode = "MajorEquipId_$eqId";

                $subComponents = EquipmentPeer::findAllByParent($this->equipid);

                foreach($subComponents as $subcomp) {
                  $subId = $subcomp->getId();
                  $subcompLink = "/?facid=$facid&equipid=$subId&action=DisplayEquipmentList";
                  $subName = htmlspecialchars($subcomp->getName(), ENT_QUOTES);

                  $treeHTML .= "tree.add('subEquip_$subId', 'MajorEquipId_$eqId', '$subName', '$subcompLink', ico_sub_equip);\n";

                  if($this->subEquipId == $subId) {
                    $selectedNode = "subEquip_$subId";
                  }
                }
              }
              else {
                $treeHTML .= "tree.setServerLoad('MajorEquipId_" . $eqId . "');\n";
              }
            }
          }

          $smListNode = "facSensor_$facid";

          $treeHTML .= "tree.add('$smListNode', '$fNode', 'Sensors List by SensorModel', '/?facid=$facid&action=ListFacilitySensors', ico_sensors_list);\n";

          if(!$this->is_sensor) {
            $treeHTML .= "tree.setServerLoad('$smListNode');\n";
          }
          else {

            $selectedNode = $smListNode;

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
              $smid = $rs->getInt('SENSOR_MODEL_ID');
              $smName = htmlspecialchars($rs->getString('NAME'), ENT_QUOTES);
              $quantity = $rs->getInt('QUANTITY');
              $smNode = "smid_$smid" . "_facid_$facid";

              $treeHTML .= "tree.add('$smNode', '$smListNode', '($quantity) $smName', '/?facid=$facid&sensorModel=$smid&action=ListFacilitySensors', ico_sensor_model);\n";

              if($this->sensor_model_id == $smid) $selectedNode = $smNode;

              if(($this->sensor_id && !$this->sensor_model_id) || $this->sensor_model_id == $smid) {
                $sensors = SensorPeer::findByFacilityAndSensorModel($facid, $smid);

                foreach($sensors as $sensor) {
                  $sensorName = htmlspecialchars($sensor->getName(), ENT_QUOTES);
                  $sensorId = $sensor->getId();
                  $sensorLink = "/?action=DisplaySensor&sensor=$sensorId&facid=$facid";

                  $treeHTML .= "tree.add('sensor_$sensorId', '$smNode', '$sensorName', '$sensorLink', ico_sensor);\n";
                  if($this->sensor_id == $sensorId) $selectedNode = "sensor_$sensorId";
                }
              }
              else {
                $treeHTML .= "tree.setServerLoad('$smNode');\n";
              }
            }
          }

          $treeHTML .= "tree.add('$fNode" . "_training', '$fNode', 'Training And Certification', '/?facid=$facid&action=DisplayFacilityTrainingAndCertification', ico_cert);\n";
          $treeHTML .= "tree.add('$fNode" . "_education', '$fNode', 'Education and Outreach Documents', '/?facid=$facid&action=DisplayFacilityEducationOutreach', '');\n";

          if(!$this->is_equip && !$this->is_sensor && !$this->is_staff) {
            if(strpos($this->action, "Contact") !== false || isset($_REQUEST["contact"])) {
              $selectedNode = $fNode . "_contact";
            }
            elseif($this->action == "DisplayFacilityTrainingAndCertification") {
              $selectedNode = $fNode . "_training";
            }
            elseif($this->action == "DisplayFacilityEducationOutreach"){
              $selectedNode = $fNode . "_education";
            }
          }
        }
      }
    }

    $selectedNodeStr = $selectedNode ? "tree.selectNodeById('$selectedNode'); tree.expandNode('$selectedNode');" : "";

    $left_content = <<<ENDHTML

<!-- Start Tree Facility -->

  <script type="text/javascript">
<!--
    var ico_fc           = "/tree_browser/img/f_color.gif";
    var ico_nees         = "/tree_browser/img/ico_nees.gif";
    var ico_member       = "/tree_browser/img/ico_member.gif";
    var ico_person       = "/tree_browser/img/ico_person.gif";
    var ico_equip        = "/tree_browser/img/ico_equip.gif";
    var ico_sub_equip    = "/tree_browser/img/ico_sub_equip.gif";
    var ico_equiplist    = "/tree_browser/img/ico_equiplist.gif";
    var ico_sensors_list = "/tree_browser/img/ico_sensors_list.gif";
    var ico_sensor_model = "/tree_browser/img/ico_sensor_model.gif";
    var ico_sensor       = "/tree_browser/img/ico_sensor.gif";
    var ico_cert         = "/tree_browser/img/ico_cert.gif";

    preloadIcon(ico_fc, ico_nees, ico_member, ico_person, ico_equip, ico_sub_equip, ico_equiplist, ico_sensors_list, ico_sensor_model, ico_sensor, ico_cert);

    var tree = new NlsTree("TreeFacility");

    tree.chUrl="/ajax/ajaxLoading.php";

    //tree.opt.hideRoot = true;
    //tree.opt.showExpdr = true;


    tree.opt.selRow = true;

    function initTree() {
    $treeHTML

    }
    initTree();
// -->
  </script>

  <div style="display: block; overflow: hidden;">
    <div class="treeExpand" style="border-top:0;">
      <a href="javascript:tree.expandAll();void(0);">Expand All</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="javascript:tree.collapseAll(); tree.expandNode('group_1'); tree.expandNode('group_2'); tree.expandNode('group_3'); tree.expandNode('group_4'); void(0);">Collapse All</a>
    </div>
    <div class="contentpadding">
      <div id="tree_browser">
        <script type="text/javascript">
<!--
          tree.render();
          $selectedNodeStr
// -->
        </script>
        <br/><br/>
      </div>
    </div>
  </div>

<!-- End Tree Facility -->


ENDHTML;

    return make_portlet("<div class='mainportlet_title '>NEES Equipment Site List</div>", $left_content, "mainportlet", null, "column_left_main");
  }

}

?>
