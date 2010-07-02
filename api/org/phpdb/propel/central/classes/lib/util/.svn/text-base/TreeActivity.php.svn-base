<?php

class TreeActivity {

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
  private $eloc;
  private $admin;
  private $cleanflexURL;

  public function __construct($admin = false, $cleanflexURL = "") {

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

    $this->admin = $admin;
    $this->cleanflexURL = $cleanflexURL;
    $this->eloc = isset($_REQUEST["eloc"]) ? $_REQUEST["eloc"] : "";
  }


  public function get_tree_activity()
  {
    $statusMap = FacilityPeer::getAllNawiStatus();

    $selectedNode = 'root';

    $treeHTML = "\ntree.add('root', 0, '&nbsp;&nbsp;NEES Equipment Site', '', '', true);\n";

    $groupId = 0;

    foreach ($this->NEESEquipmentSiteList as $sitekey => $sitevalue) {

      $treeHTML .= "tree.add('group_" . ++$groupId . "', 'root', '$sitekey', '', ico_nees, true);\n";

      foreach($sitevalue as $facid => $facvalue) {

        $fNode = "activityFacid_" . $facid . "_" . ($this->admin ? "1" : "0");

        $treeHTML .= "tree.add('$fNode', 'group_" . $groupId . "', '$facvalue', '/activities/?facid=$facid', '" . $statusMap[$facid] . "');\n";

        if($this->facid != $facid) {
          $treeHTML .= "tree.setServerLoad('$fNode');\n";
        }
        else {
          if($facid == $this->facid) $selectedNode = $fNode;
          $activeFeedsNode = "activityFeeds_$facid";

          if(!empty($this->cleanflexURL)) {
            ini_set('default_socket_timeout', 5);
            $xmlresult = @file_get_contents("$this->cleanflexURL/feeds");

            if($xmlresult !== false) {
              preg_match_all("/<stream\s+id=\"([^\"]*)\"\s+xlink:href=\"([^\"]*)\">/", $xmlresult, $matches, PREG_SET_ORDER);
              $feedcount = sizeof($matches);

              if($feedcount > 0) {
                $treeHTML .= "tree.add('$activeFeedsNode', '$fNode', 'Active Feeds (" . $feedcount . ")', '/activities/?facid=$facid&eloc=Feeds', ico_videofeed);\n";
              }
            }
          }

          if($this->eloc == "Feeds") {
            $selectedNode = $activeFeedsNode;
          }

          if($this->admin) {
            $configNode = "activityConfig_$facid";

            $treeHTML .= "tree.add('$configNode', '$fNode', 'Configure Site Experiments (Admin only)', '/activities/?facid=$facid&eloc=Config', ico_equiplist);\n";

            if($this->eloc == "Config" || $this->eloc == "Edit" || $this->eloc == "Facility" || $this->eloc == "New") {
              $selectedNode = $configNode;

              // Facility Configuration
              $configSiteNode = "activityConfigSite_$facid";
              $treeHTML .= "tree.add('$configSiteNode', '$configNode', 'Facility Configuration', '/activities/?facid=$facid&eloc=Facility', ico_fc);\n";
              if($this->eloc == "Facility") {
                $selectedNode = $configSiteNode;
              }

              // Add New Experiment
              $configNewNode = "activityConfigNew_$facid";
              $treeHTML .= "tree.add('$configNewNode', '$configNode', 'Add New Experiment', '/activities/?facid=$facid&eloc=New', ico_plus);\n";
              if($this->eloc == "New") {
                $selectedNode = $configNewNode;
              }

              // Edit Experiments
              $sel_nawiid = isset($_REQUEST['nawiid']) ? $_REQUEST['nawiid'] : null;

              $nawifacs = NAWIFacilityPeer::findByFacility($facid);

              foreach ( $nawifacs as $nawifac ) {
                $nawi = $nawifac->getNAWI();
                $exp_name = stripslashes( $nawi->getExperimentName() );
                $nawiid = $nawi->getId();

                $nawiNode = "nawiid_$nawiid";

                if($sel_nawiid == $nawiid) {
                  $selectedNode = $nawiNode;
                }
                $treeHTML .= "tree.add('$nawiNode', '$configNode', 'Edit: $exp_name', '/activities/?facid=$facid&nawiid=$nawiid&eloc=Edit', ico_equip);\n";
              }
            }
            else {
              $treeHTML .= "tree.setServerLoad('$configNode');\n";
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
    var ico_videofeed    = "/activities/common/images/video_feed.gif";
    var ico_equiplist    = "/tree_browser/img/ico_equiplist.gif";
    var ico_equip        = "/tree_browser/img/ico_equip.gif";
    var ico_nees         = "/tree_browser/img/ico_nees.gif";
    var ico_fc           = "/tree_browser/img/f_color.gif";
    var ico_plus         = "/images/add_sign.gif";

    preloadIcon(ico_videofeed, ico_equiplist, ico_equip, ico_fc, ico_plus);

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

    return make_portlet("<div class='mainportlet_title'>NEES Activity Sites</div>", $left_content, "mainportlet", null, "column_left_main");
  }

}

?>
