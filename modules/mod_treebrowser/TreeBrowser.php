<?php

include_once 'lib/security/PermissionsView.php';
include_once 'lib/data/Specimen.php';
include_once 'lib/data/SpecimenComponent.php';
include_once 'lib/data/Coordinator.php';
include_once 'lib/data/CoordinatorRun.php';
include_once 'lib/common/portlet.php';
//require_once 'ajax/ProjectPeer.php';

define('MY_PROJECTS', 0);
define('ALL_PROJECTS', 1);
define('PUBLICLY_ACCESSIBLE_PROJECTS', 2);
define('CURATED_PROJECTS', 3);
define('DEMO_PROJECT', 4);

class TreeBrowser {

    private $projid;
    private $expid;
//  private $trialid;
//  private $coordinatorId;
//  private $coordinatorRunId;
    private $specimenId;
    private $specCompId;
    //private $sort;
    //private $action;
    private $publishedExperimentProjids = null;
    private $curatedProjectsMap = array();
    private $curatedExperimentsMap = array();
    private $projectSpecimenMap = array();
    private $projectCoordinatorMap = array();
    private $selectedNodeID = "";

    public function __construct() {
        $this->projid = isset($_SESSION['projid']) ? $_SESSION['projid'] : null;
        $this->expid = isset($_SESSION['expid']) ? $_SESSION['expid'] : null;
        //$this->coordinatorId    = isset($_REQUEST['coordinatorId'])     ? $_REQUEST['coordinatorId']     : null;
        //$this->coordinatorRunId = isset($_REQUEST['coordinatorRunId'])  ? $_REQUEST['coordinatorRunId']  : null;
        //$this->specimenId       = isset($_REQUEST['specimenId'])        ? $_REQUEST['specimenId']        : null;
        //$this->specCompId       = isset($_REQUEST['specCompId'])        ? $_REQUEST['specCompId']        : null;
        //  $this->trialid          = isset($_REQUEST['trialid'])           ? $_REQUEST['trialid']           : null;
//$this->sort	= 'projid';
        $this->sort = isset($_SESSION['sort']) ? $_SESSION['sort'] : null;
        //$this->action = isset($_REQUEST['action'])  ? $_REQUEST['action']  : null;
        // change action to 'searchresults'
        $this->curatedProjectsMap = NCCuratedObjectsPeer::getCuratedProjectsMap();
        //$this->projectSpecimenMap = $this->getProjectSpecimenMap();
        //$this->projectCoordinatorMap = $this->getProjectCoordinatorMap();

        $this->selectedNodeID = "root";
    }

    /**
     * Search and replace any new line '\n', '\r' to space
     *
     * @param String $string
     * @return String $new_string
     */
    private function clean_newlines($string) {
        return preg_replace(array("/\n/", "/(?<=\S)\n(?=\S)/", "/(?<=\S)\ +\n(?=\S)/", "/(?<=\S)\r\n(?=\S)/", "/(?<=\S)\ +\r\n(?=\S)/"), " ", htmlspecialchars($string, ENT_QUOTES));
    }

    /**
     * Get the complet Javascript Tree browser code from the completed nodes
     *
     * @param String $treeHTML: the completed nodes
     * @return String $html_code
     */
    private function addTreeJSCode($treeHTML, $treeType) {



    	$selectednodeJS = "";
        if ($this->selectedNodeID > -1) {
            $selectednodeJS = "tree.selectNodeById('" . $this->selectedNodeID . "');";
            $selectednodeJS .= "tree.expandNode('" . $this->selectedNodeID . "');";
            //echo "selectedNodeID = ".$this->selectedNodeID;
        } else {
            $selectednodeJS = "tree.selectNodeById('root');";
            $selectednodeJS .= "tree.expandNode('root');";
        }

        $expandAll = $treeType == ALL_PROJECTS ? '<span title="This function is disabled for All Projects tree">Expand All</span>' : '<a href="javascript:tree.expandAll();void(0);">Expand All</a>';

        global $ini_array;
        $centralhost = $ini_array['centralhost'];

        $jstree = <<<ENDHTML
	<div style="margin-top:10px;margin-bottom:10px">

	<img src="/modules/mod_treebrowser/tree_browser/img/p_curated.gif" /> Curated project<br />
	<img src="/modules/mod_treebrowser/tree_browser/img/p_published.gif" /> Project with at least one public experiment<br />
	<img src="/modules/mod_treebrowser/tree_browser/img/p_color.gif" /> Project with only private experiments <br />

<!--	<img src="/modules/mod_treebrowser/tree_browser/img/e_curated.gif" /> Published & Curated Experiment <br />
	<img src="/modules/mod_treebrowser/tree_browser/img/e_published.gif" /> Published & Not Curated Experiment <br />
	<img src="/modules/mod_treebrowser/tree_browser/img/e_color.gif" /> Published and Not Curated Experiment <br />
	-->

	</div>

    <div class="treeExpand" style="border-top:0;">$expandAll&nbsp;&nbsp;&nbsp;<a href="javascript:tree.collapseAll();void(0);">CollapseAll</a></div>

    <script language="javasript" type="text/javascript">

      var tree=new NlsTree("treeBrowser");
<!--
      tree.chUrl="/modules/mod_treebrowser/ajax/ajaxLoading.php";

      tree.opt.renderOnDemand = true;
      tree.opt.hideRoot=false;
      tree.opt.selRow = true;

      var ico_e                   = "/modules/mod_treebrowser/tree_browser/img/e.gif";
      var ico_ec                  = "/modules/mod_treebrowser/tree_browser/img/e_color.gif";
      var ico_ep                  = "/modules/mod_treebrowser/tree_browser/img/e_published.gif";
      var ico_ce                  = "/modules/mod_treebrowser/tree_browser/img/e_curated.gif";

      var ico_s                   = "/modules/mod_treebrowser/tree_browser/img/s.gif";
      var ico_sc                  = "/modules/mod_treebrowser/tree_browser/img/s_color.gif";
      var ico_sp                  = "/modules/mod_treebrowser/tree_browser/img/s_published.gif";
      var ico_cs                  = "/modules/mod_treebrowser/tree_browser/img/s_curated.gif";

      var ico_project_coordinator = "/modules/mod_treebrowser/tree_browser/img/project_coordinator.gif";
      var ico_coordinator         = "/modules/mod_treebrowser/tree_browser/img/coordinator.gif";

      var ico_coordinatorRunList  = "/modules/mod_treebrowser/tree_browser/img/coordinatorRunList.gif";
      var ico_coordinatorRun      = "/modules/mod_treebrowser/tree_browser/img/coordinatorRun.gif";

      var ico_project_specimen    = "/modules/mod_treebrowser/tree_browser/img/project_specimen.gif";
      var ico_specimen            = "/modules/mod_treebrowser/tree_browser/img/specimen.gif";

      var ico_specimen_component  = "/modules/mod_treebrowser/tree_browser/img/specimen_component.gif";
      var ico_specCompList        = "/modules/mod_treebrowser/tree_browser/img/specimen_component_list.gif";

      var ico_t                   = "/modules/mod_treebrowser/tree_browser/img/t.gif";
      var ico_tc                  = "/modules/mod_treebrowser/tree_browser/img/t_color.gif";

      var ico_p                   = "/modules/mod_treebrowser/tree_browser/img/p.gif";
      var ico_pc                  = "/modules/mod_treebrowser/tree_browser/img/p_color.gif";
      var ico_cp                  = "/modules/mod_treebrowser/tree_browser/img/p_curated.gif";
      var ico_pp                  = "/modules/mod_treebrowser/tree_browser/img/p_published.gif";

      var ico_r                   = "/modules/mod_treebrowser/tree_browser/img/r.gif";
      var ico_rc                  = "/modules/mod_treebrowser/tree_browser/img/r_color.gif";

      var ico_person              = "/modules/mod_treebrowser/tree_browser/img/ico_person.gif";
      var ico_setup               = "/modules/mod_treebrowser/tree_browser/img/ico_equiplist.gif";
      var ico_setup_section       = "/modules/mod_treebrowser/tree_browser/img/ico_equip.gif";
      var ico_setup_item          = "/modules/mod_treebrowser/tree_browser/img/ico_sub_equip.gif";
      var ico_loading             = "/modules/mod_treebrowser/tree_browser/img/loading.gif";
      var ico_member              = "/modules/mod_treebrowser/tree_browser/img/ico_member.gif";
      var ico_folder              = "/modules/mod_treebrowser/tree_browser/img/bluefolder.gif";
      var ico_dir                 = "/modules/mod_treebrowser/tree_browser/img/folder.gif";
      var ico_n3dv                = "/modules/mod_treebrowser/tree_browser/img/ico_n3dv.gif";
      var ico_data                = "/modules/mod_treebrowser/tree_browser/img/ico_data.gif";

      preloadIcon(ico_e, ico_ec, ico_ep, ico_ce, ico_s, ico_sc, ico_sp, ico_cs, ico_project_coordinator, ico_coordinator, ico_coordinatorRunList, ico_coordinatorRun, ico_project_specimen, ico_specimen, ico_specimen_component, ico_specCompList, ico_t, ico_tc, ico_p, ico_pc, ico_cp, ico_pp, ico_r, ico_rc,ico_person, ico_setup, ico_setup_section, ico_setup_item, ico_loading, ico_member, ico_folder, ico_dir, ico_n3dv, ico_data);
        // ADding the legends

      function initTree()
      {
        $treeHTML
      }
      initTree();
// -->
      </script>


      <div class="contentpadding">
        <div id="tree_browser">
          <script type="text/javascript">
<!--
          tree.render(); $selectednodeJS
// -->
          </script>
<!--
          <div class='floatright' style='font-family:arial;font-size:7pt;color:#666666;padding-top:5px'><br/><br/><br/>&nbsp;&nbsp;NEEShub&nbsp;https://$centralhost&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/><br/><br/></div>
-->
	</div>
      </div>
      <div style="clear: both;"></div>

ENDHTML;

        return $jstree;
    }

    /**
     * Detect type of tree browser (MY_PROJECTS, ALL PROJECTS, PUBLICLY ACCESSIBLE PROJECTS)
     * based on the action page or permission on the current working project
     *
     * @return String $html
     */
    public function makeTreeBrowser() {

        $strView = JRequest::getVar('view');
        //echo $strView."<br>";
        if ($strView == "get") {
            return $this->makeMyTree();
        } else {
            return $this->makePubTree();
        }

//    if ($this->action == "ListMyProjects") {
//      return $this->makeMyTree();
//    }
//    elseif ($this->action == "ListPubProjects") {

        /*
         * P1 and P2: show pub projects
         * return $this->makePubTree();
         */
//    }
//    elseif ($this->action == "ListAllProjects") {
//      return $this->makeFullTree();
//    }
//    elseif ($this->action == "ListCuratedProjects") {
//      return $this->makeCuratedTree();
//    }
//   $auth = Authorizer::getInstance();
//    $uid = $auth->getUserId();
//    if($uid && $this->projid) {
//      $canview = PermissionsViewPeer::canDo($uid, $this->projid, 1, "VIEW");
//      if($canview) {
//        return $this->makeMyTree();
//      }
        //   }

        if ($this->projid && in_array($this->projid, $this->curatedProjectsMap)) {
            return $this->makeCuratedTree();
        }

        if ($this->projid && in_array($this->projid, $this->getPublishedExperimentProjids())) {
            return $this->makePubTree();
        }

        return $this->makeFullTree();
    }

    /**
     * Get Order By for the list of Projects for the sql query
     *
     * @return String $orderBy
     */
    private function getOrderBy() {
        if ($this->sort) {
            if ($this->sort == "projid")
                return ProjectPeer::PROJID;
            if ($this->sort == "fundorg")
                return ProjectPeer::FUNDORG;
            if ($this->sort == "contact")
                return ProjectPeer::CONTACT_NAME;
            if ($this->sort == "nickname")
                return ProjectPeer::NICKNAME;
            if ($this->sort == "type")
                return ProjectPeer::PROJECT_TYPE_ID;
        }
        return ProjectPeer::NICKNAME;
    }

    /**
     * Get MY_PROJECTS Tree Browser
     *
     * @return String $html
     */
    public function makeMyTree() {
        return $this->makeTree(MY_PROJECTS);
    }

    /**
     * Get Publicly Accessible Projects Tree Browser
     *
     * @return String $html
     */
    public function makePubTree() {
        return $this->makeTree(PUBLICLY_ACCESSIBLE_PROJECTS);
    }

    /**
     * Get Publicly Accessible Projects Tree Browser
     *
     * @return String $html
     */
    public function makeCuratedTree() {
        return $this->makeTree(CURATED_PROJECTS);
    }

    /**
     * Get All Projects Tree Browser
     *
     * @return String $html
     */
    public function makeFullTree() {
        return $this->makeTree(ALL_PROJECTS);
    }

    /**
     * Main function to build the complete nodes
     *
     * @param int $treeType
     * @param boolean $portlet: option to return tree within/without portlet
     * @return String $html
     */
    public function makeTree($treeType, $portlet = true) {
        //echo 'TREE BROWSER';
        //$projects = unserialize($_SESSION[Search::RESULTS]);
        // get view == search
        if (JRequest::getVar('view') == 'search' && $treeType == PUBLICLY_ACCESSIBLE_PROJECTS) {
            //$projects = unserialize($_SESSION[Search::RESULTS]);

            $projects = ProjectPeer::getViewableProjectsWithOrder($this->getOrderBy());
//      $projslink = "warehouse/index.php?task=find";
//      $treeTypeStr = "Publicly Accessible Projects";
        }else if(JRequest::getVar('view') == 'advancedsearch' && $treeType == PUBLICLY_ACCESSIBLE_PROJECTS){
            $projects = ProjectPeer::getViewableProjectsWithOrder($this->getOrderBy());
        }else
            $projects = unserialize($_SESSION[Search::RESULTS]);


        $treeHTML='';

        $projslink = "warehouse/index.php?task=find";
        $treeTypeStr = (JRequest::getVar('view') == 'get') ? "My Projects" :"Publicly Accessible Projects";

        $treeHTML .= "tree.add('root', 0, '&nbsp;&nbsp;NEEShub :: $treeTypeStr', '$projslink', '', true);\n";
        //$treeHTML = "tree.add('legend', 0, 'Published and Curated :: $treeTypeStr', '$projslink', '', true);\n";

        $publishedMap = $this->getPublishedExperimentProjids();

        foreach ($projects as $p) {

            $projid = $p->getId();
            //echo $projid."\n\r";

            $title = $this->clean_newlines($p->getNickname());
            //echo $title;
            $url = "/warehouse/project/$projid";

            $marker = "ico_pc";

            if (in_array($projid, $publishedMap)) {
                $title .= " (publicly accessible)";
                $marker = "ico_pp";
            }

            if (in_array($projid, $this->curatedProjectsMap)) {
                $title .= " (curated)";
                $marker = "ico_cp";
            }

            $pnodeID = "projid_" . $projid;

            $treeHTML .= "\n// Project ID: $projid \n";
            $treeHTML .= "tree.add('$pnodeID', 'root', '$title', '$url', $marker);\n";
            if ($this->projid != $projid) {

                $treeHTML .= "tree.setServerLoad('$pnodeID');\n";
            }
            // Current Project you are working on, load full child
            else {
                /**
                 * *******************************************************************
                 * Structured Project
                 * *******************************************************************
                 */
                if ($p->isExperimentalProject()) {
                    if ($p->isStructuredProject()) {
                        $treeHTML .= "tree.add('" . $pnodeID . "_Experiments',     '$pnodeID', 'Experiment List',       '$url&task=ListProjectExperiments',      ico_e);\n";
                    }
                    //echo $treeHTML;
                }
            }
        }

        //echo $treeHTML;
        $ptree = $this->addTreeJSCode($treeHTML, $treeType);
        $portlet = "";
        if ($portlet) {
            return make_portlet("<div class='mainportlet_title'><a href='$projslink'>Browser: $treeTypeStr</a></div>", $ptree, "mainportlet", null, "column_left_main");
        } else {
            return $ptree;
        }
    }

    private function getPublishedExperimentProjids() {
        if (is_null($this->publishedExperimentProjids)) {
            $this->publishedExperimentProjids = array_keys(ProjectPeer::findViewableProjectIdsHasPublishedExperiment());
        }

        return $this->publishedExperimentProjids;
    }

    private function getProjectSpecimenMap() {
        include_once 'lib/data/Specimen.php';
        return SpecimenPeer::getProjectSpecimenMap();
    }

    private function getProjectCoordinatorMap() {
        include_once 'lib/data/Coordinator.php';
        return CoordinatorPeer::getProjectCoordinatorMap();
    }

    /**
     * Output Javascript Code for Experiment
     *
     * @param Experiment $e
     * @param int $projid
     * @param String $parentNodeID
     * @return String $html
     */
    private function printExperiment($e, $projid, $parentNodeID, $isHybrid=false) {
        $expid = $e->getId();
        $title = $this->clean_newlines($e->getTitle());
        $url = "/?projid=$projid&expid=$expid";

        $exp_ico = $e->isSimulation() ? 'ico_sc' : 'ico_ec';
        $treeHTML = "\n// ExpID: $expid \n";

        if ($e->isPublished()) {
            $exp_ico = $e->isSimulation() ? 'ico_sp' : 'ico_ep';
            $title .= " (publicly accessible)";
        }

        if (in_array($expid, $this->curatedExperimentsMap)) {
            $exp_ico = $e->isSimulation() ? 'ico_cs' : 'ico_ce';
            $title .= " (curated)";
        }

        $expTypeId = $e->getExperimentTypeId();
        $exp_action = $expTypeId == ExperimentPeer::CLASSKEY_SIMULATION ? "DisplaySimulationMain" : "DisplayExperimentMain";

        $enodeID = "expid_" . $expid;
        $treeHTML .= "tree.add('$enodeID', '" . $parentNodeID . "',   '$title', '$url&action=$exp_action', $exp_ico);\n";

        if ($this->expid != $expid) {
            $treeHTML .= "tree.setServerLoad('$enodeID');\n";
        } else {
            /**
             * *******************************************************************
             * Structured Experiment
             * *******************************************************************
             */
            if ($expTypeId == ExperimentPeer::CLASSKEY_STRUCTUREDEXPERIMENT) {

                $expSetupNode = "expSetup_$expid";

                $treeHTML .= "tree.add('" . $enodeID . "_Trials', '$enodeID', 'Trial List', '$url&action=DisplayExperimentTrials', ico_t);\n";
                $treeHTML .= "tree.add('$expSetupNode', '$enodeID', 'Experiment Setup', '$url&action=DisplayExperimentSetup', ico_setup);\n";
                $treeHTML .= "tree.add('DataFile_Experiment_Analysis_$expid', '$enodeID', 'Experiment Analysis', '$url&action=DisplayExperimentAnalysis', ico_folder);\n";
                $treeHTML .= "tree.add('DataFile_Experiment_Documentation_$expid', '$enodeID', 'Experiment Documentation', '$url&action=DisplayExperimentDocumentation', ico_folder);\n";
                $treeHTML .= "tree.add('DataFile_Experiment_Public_$expid', '$enodeID', 'Experiment Public', '$url&action=DisplayExperimentPublic', ico_folder);\n";
                $treeHTML .= "tree.add('" . $enodeID . "_DataViewers', '$enodeID', 'Data Viewers', '$url&action=DisplayDataViewers', ico_n3dv);\n";
                $treeHTML .= "tree.add('ExpMembers_" . $expid . "', '$enodeID', 'Experiment Members', '$url&action=DisplayExperimentMembers', ico_member);\n";

                $treeHTML .= "tree.setServerLoad('DataFile_Experiment_Analysis_$expid');\n";
                $treeHTML .= "tree.setServerLoad('DataFile_Experiment_Documentation_$expid');\n";
                $treeHTML .= "tree.setServerLoad('DataFile_Experiment_Public_$expid');\n";

                if (strpos($this->action, "Trial") !== false) {
                    $this->selectedNodeID = $enodeID . '_Trials';
                } elseif (
                        isset($_REQUEST['subset']) ||
                        strpos($this->action, "Setup") !== false ||
                        strpos($this->action, "Measurement") !== false ||
                        strpos($this->action, "Material") !== false ||
                        strpos($this->action, "CoordinateSpace") !== false ||
                        strpos($this->action, "Location") !== false ||
                        strpos($this->action, "Equipment") !== false ||
                        strpos($this->action, "ScaleFactor") !== false ||
                        strpos($this->action, "Model") !== false ||
                        strpos($this->action, "SpecimenComponent") !== false
                ) {
                    $this->selectedNodeID = $expSetupNode;
                } elseif ($this->action == "DisplayExperimentAnalysis") {
                    $this->selectedNodeID = "DataFile_Experiment_Analysis_$expid";
                } elseif ($this->action == "DisplayExperimentDocumentation") {
                    $this->selectedNodeID = "DataFile_Experiment_Documentation_$expid";
                } elseif ($this->action == "DisplayExperimentPublic") {
                    $this->selectedNodeID = "DataFile_Experiment_Public_$expid";
                } elseif ($this->action == "DisplayDataViewers") {
                    $this->selectedNodeID = $enodeID . '_DataViewers';
                } elseif (strpos($this->action, "Member") !== false) {
                    $this->selectedNodeID = "ExpMembers_" . $expid;
                } else {
                    $this->selectedNodeID = $enodeID;
                }

                /**
                 * *******************************************************************
                 * Structured Experiment Setup
                 * *******************************************************************
                 */
                if ($this->selectedNodeID == $expSetupNode) {

                    $treeHTML .= "tree.add('" . $expSetupNode . "_Units',       '$expSetupNode', 'Measurement Units',     '$url&action=DisplayExperimentSetup&subset=Units#s1',        ico_setup_section);\n";

                    if ($isHybrid) {
                        $treeHTML .= "tree.add('" . $expSetupNode . "_SpecimenComponents',   '$expSetupNode', 'Specimen Components',   '$url&action=DisplayExperimentSetup&subset=SpecimenComponents#s9',    ico_setup_section);\n";
                    } else {
                        $treeHTML .= "tree.add('" . $expSetupNode . "_Materials',   '$expSetupNode', 'Material Properties',   '$url&action=DisplayExperimentSetup&subset=Materials#s2',    ico_setup_section);\n";
                    }

                    $treeHTML .= "tree.add('" . $expSetupNode . "_Coors', '$expSetupNode', 'Coordinate Spaces', '$url&action=DisplayExperimentSetup&subset=Coors#s3', ico_setup_section);\n";
                    $treeHTML .= "tree.add('" . $expSetupNode . "_SensorLPs', '$expSetupNode', 'Sensor Location Plans', '$url&action=DisplayExperimentSetup&subset=SensorLPs#s4', ico_setup_section);\n";
                    $treeHTML .= "tree.add('" . $expSetupNode . "_SourceLPs', '$expSetupNode', 'Source Location Plans', '$url&action=DisplayExperimentSetup&subset=SourceLPs#s5', ico_setup_section);\n";
                    $treeHTML .= "tree.add('" . $expSetupNode . "_Equipment', '$expSetupNode', 'Equipment Inventory', '$url&action=DisplayExperimentSetup&subset=Equipment#s6', ico_setup_section);\n";
                    $treeHTML .= "tree.add('" . $expSetupNode . "_ScaleFactors','$expSetupNode', 'Scale Factors', '$url&action=DisplayExperimentSetup&subset=ScaleFactors#s7', ico_setup_section);\n";
                    $treeHTML .= "tree.add('DataFile_Experiment_Models_$expid', '$expSetupNode', 'Models', '$url&action=DisplayExperimentSetup&subset=Models#s8', ico_setup_section);\n";

                    $subset = null;
                    if (isset($_REQUEST['subset'])) {
                        $subset = $_REQUEST['subset'];

                        if ($subset == "Units")
                            $this->selectedNodeID = $expSetupNode . "_Units";
                        elseif ($subset == "Materials")
                            $this->selectedNodeID = $expSetupNode . "_Materials";
                        elseif ($subset == "Coors")
                            $this->selectedNodeID = $expSetupNode . "_Coors";
                        elseif ($subset == "SensorLPs")
                            $this->selectedNodeID = $expSetupNode . "_SensorLPs";
                        elseif ($subset == "SourceLPs")
                            $this->selectedNodeID = $expSetupNode . "_SourceLPs";
                        elseif ($subset == "Equipment")
                            $this->selectedNodeID = $expSetupNode . "_Equipment";
                        elseif ($subset == "ScaleFactors")
                            $this->selectedNodeID = $expSetupNode . "_ScaleFactors";
                        //elseif($subset == "Models")       $this->selectedNodeID = $expSetupNode . "_Models";
                        elseif ($subset == "Models")
                            $this->selectedNodeID = "DataFile_Experiment_Models_$expid";
                        elseif ($subset == "SpecimenComponents")
                            $this->selectedNodeID = $expSetupNode . "_SpecimenComponents";
                    }

                    /**
                     * *******************************************************************
                     * Structured Experiment Setup: Measurement Units
                     * *******************************************************************
                     */
                    if (strpos($this->action, "MeasurementUnit") !== false) {
                        $this->selectedNodeID = $expSetupNode . "_Units";
                    }

                    /**
                     * *******************************************************************
                     * Structured Experiment Setup: Measurement Units
                     * *******************************************************************
                     */
                    if (strpos($this->action, "ScaleFactors") !== false) {
                        $this->selectedNodeID = $expSetupNode . "_ScaleFactors";
                    }

                    /**
                     * *******************************************************************
                     * Structured Experiment Setup: Material
                     * *******************************************************************
                     */
                    if ($isHybrid) {
                        if (strpos($this->action, "SpecimenComponent") === false && $subset != "SpecimenComponents") {
                            $treeHTML .= "tree.setServerLoad('" . $expSetupNode . "_SpecimenComponents');\n";
                        } else {
                            require_once 'lib/data/SpecimenComponentExperiment.php';

                            $this->selectedNodeID = $expSetupNode . "_SpecimenComponents";

                            $sces = SpecimenComponentExperimentPeer::findByExperiment($expid);

                            foreach ($sces as $sce) {
                                /* @var $sce SpecimenComponentExperiment */
                                $sc = $sce->getSpecimenComponent();
                                $specimenId = $sc->getSpecimenId();
                                $specCompId = $sc->getId();
                                $specCompName = htmlspecialchars($sc->getName(), ENT_QUOTES);
                                $specCompUrl = "/?projid=$projid&specimenId=$specimenId&specCompId=$specCompId&action=DisplaySpecimenComponent";

                                $treeHTML .= "tree.add('SpecimenComponentExperimentId_" . $specCompId . "', '$expSetupNode" . "_SpecimenComponents', '$specCompName', '$specCompUrl',  ico_setup_item);\n";
                                $this->selectedNodeID = "SpecimenComponentExperimentId_" . $specCompId;
                            }
                        }
                    } else {
                        if (strpos($this->action, "Material") === false && $subset != "Materials") {
                            $treeHTML .= "tree.setServerLoad('" . $expSetupNode . "_Materials');\n";
                        } else {
                            require_once 'lib/data/Material.php';

                            $this->selectedNodeID = $expSetupNode . "_Materials";

                            $r_materialId = isset($_REQUEST['materialid']) ? $_REQUEST['materialid'] : -1;

                            $materials = MaterialPeer::findByExperiment($expid);

                            foreach ($materials as $material) {
                                $materialId = $material->getId();
                                $materialName = htmlspecialchars($material->getName(), ENT_QUOTES);
                                $materialUrl = $url . "&materialid=$materialId&action=ViewMaterial";

                                $treeHTML .= "tree.add('materialId_" . $materialId . "', '$expSetupNode" . "_Materials', '$materialName', '$materialUrl',  ico_setup_item);\n";

                                if ($r_materialId == $materialId) {
                                    $this->selectedNodeID = "materialId_" . $materialId;
                                }
                            }
                        }
                    }

                    /**
                     * *******************************************************************
                     * Structured Experiment Setup: CoordinateSpace
                     * *******************************************************************
                     */
                    if (strpos($this->action, "CoordinateSpace") === false && $subset != "Coors") {
                        $treeHTML .= "tree.setServerLoad('" . $expSetupNode . "_Coors');\n";
                    } else {
                        include_once 'lib/data/CoordinateSpace.php';

                        $this->selectedNodeID = $expSetupNode . "_Coors";

                        $r_coorId = isset($_REQUEST['cspaceid']) ? $_REQUEST['cspaceid'] : -1;

                        $coors = CoordinateSpacePeer::findByExperiment($expid);

                        foreach ($coors as $coor) {
                            $coorId = $coor->getId();
                            $coorName = htmlspecialchars($coor->getName(), ENT_QUOTES);
                            $coorUrl = $url . "&cspaceid=$coorId&action=DisplayCoordinateSpace";

                            $treeHTML .= "tree.add('coorId_" . $coorId . "', '$expSetupNode" . "_Coors', '$coorName', '$coorUrl',  ico_setup_item);\n";

                            if ($r_coorId == $coorId) {
                                $this->selectedNodeID = "coorId_" . $coorId;
                            }
                        }
                    }

                    /**
                     * *******************************************************************
                     * Structured Experiment Setup: SensorLocationPlan & SourceLocationPlan
                     * *******************************************************************
                     */
                    if (strpos($this->action, "Location") === false && $subset != "SensorLPs" && $subset != "SourceLPs") {
                        $treeHTML .= "tree.setServerLoad('" . $expSetupNode . "_SensorLPs');\n";
                        $treeHTML .= "tree.setServerLoad('" . $expSetupNode . "_SourceLPs');\n";
                    } else {
                        include_once 'lib/data/LocationPlan.php';

                        $r_lpId = isset($_REQUEST['lpid']) ? $_REQUEST['lpid'] : -1;

                        $sensorlps = LocationPlanPeer::findByExperimentAndPlanTypeID($expid, LocationPlanPeer::CLASSKEY_SENSORLOCATIONPLAN);

                        foreach ($sensorlps as $lp) {
                            $lpId = $lp->getId();
                            $lpName = htmlspecialchars($lp->getName(), ENT_QUOTES);
                            $lpUrl = $url . "&lpid=$lpId&action=DisplaySensorLocationPlan";

                            $treeHTML .= "tree.add('lpId_" . $lpId . "', '$expSetupNode" . "_SensorLPs', '$lpName', '$lpUrl',  ico_setup_item);\n";

                            if ($r_lpId == $lpId) {
                                $this->selectedNodeID = "lpId_" . $lpId;
                            }
                        }

                        $sourcelps = LocationPlanPeer::findByExperimentAndPlanTypeID($expid, LocationPlanPeer::CLASSKEY_SOURCELOCATIONPLAN);

                        foreach ($sourcelps as $lp) {
                            $lpId = $lp->getId();
                            $lpName = htmlspecialchars($lp->getName(), ENT_QUOTES);
                            $lpUrl = $url . "&lpid=$lpId&action=DisplaySourceLocationPlan";

                            $treeHTML .= "tree.add('lpId_" . $lpId . "', '$expSetupNode" . "_SourceLPs', '$lpName', '$lpUrl',  ico_setup_item);\n";

                            if ($r_lpId == $lpId) {
                                $this->selectedNodeID = "lpId_" . $lpId;
                            }
                        }
                    }

                    /**
                     * *******************************************************************
                     * Structured Experiment Setup: Equipment Inventories
                     * *******************************************************************
                     */
                    if (strpos($this->action, "Equipment") === false && $subset != "Equipment") {
                        $treeHTML .= "tree.setServerLoad('" . $expSetupNode . "_Equipment');\n";
                    } else {
                        require_once 'lib/data/ExperimentEquipment.php';

                        $this->selectedNodeID = $expSetupNode . "_Equipment";

                        $expEquipment = ExperimentEquipmentPeer::findByExperiment($expid);

                        $r_equipId = isset($_REQUEST['equipid']) ? $_REQUEST['equipid'] : -1;

                        foreach ($expEquipment as $expEquip) {
                            $equip = $expEquip->getEquipment();
                            $equipId = $equip->getId();
                            $equipName = htmlspecialchars($equip->getName(), ENT_QUOTES);
                            $facid = $equip->getOrganization()->getId();
                            $equipUrl = $url . "&equipid=$equipId&facid=$facid&action=DisplayEquipmentList";

                            $treeHTML .= "tree.add('equipId_" . $equipId . "', '$expSetupNode" . "_Equipment', '$equipName', '$equipUrl',  ico_setup_item);\n";

                            if ($r_equipId == $equipId) {
                                $this->selectedNodeID = "equipId_" . $equipId;
                            }
                        }
                    }

                    /**
                     * *******************************************************************
                     * Structured Experiment Setup: Models Files
                     * *******************************************************************
                     */
                    if ($subset != "Models") {
                        $treeHTML .= "tree.setServerLoad('DataFile_Experiment_Models_$expid');\n";
                    } else {
                        $treeHTML .= $this->getDataFilesByDir($e->getPathname() . "/Models", "DataFile_Experiment_Models_$expid");
                    }
                } else {
                    $treeHTML .= "tree.setServerLoad('$expSetupNode');\n";
                }
                /**
                 * *******************************************************************
                 * Trial
                 * *******************************************************************
                 */
                $trials = TrialPeer::findByExperiment($expid);

                foreach ($trials as $t) {
                    /* @var $t Trial */
                    $trialid = $t->getId();
                    $title = $this->clean_newlines($t->getTitle());
                    $url = "/?projid=$projid&expid=$expid&trialid=$trialid";

                    $tnodeID = "trialid_" . $trialid;
                    $treeHTML .= "\n// TrialID: $trialid \n";
                    $treeHTML .= "tree.add('$tnodeID', '" . $enodeID . "_Trials',   '$title', '$url&action=DisplayTrialMain', ico_tc);\n";

                    if ($this->trialid != $trialid) {
                        $treeHTML .= "tree.setServerLoad('$tnodeID');\n";
                    } else {
                        $trialSetupNode = "trialSetup_" . $trialid . "_" . $expid . "_" . $projid;

                        $treeHTML .= "tree.add('$trialSetupNode', '$tnodeID', 'Trial Setup', '$url&action=DisplayTrialSetup', ico_setup);\n";
                        $treeHTML .= "tree.add('TrialData_$trialid', '$tnodeID', 'Trial Data', '$url&action=DisplayTrialData', ico_folder);\n";
                        $treeHTML .= "tree.add('DataFile_Trial_Analysis_$trialid', '$tnodeID', 'Trial Analysis', '$url&action=DisplayTrialAnalysis', ico_folder);\n";
                        $treeHTML .= "tree.add('DataFile_Trial_Documentation_$trialid', '$tnodeID', 'Trial Documentation', '$url&action=DisplayTrialDocumentation', ico_folder);\n";

                        $treeHTML .= "tree.setServerLoad('DataFile_Trial_Analysis_$trialid');\n";
                        $treeHTML .= "tree.setServerLoad('DataFile_Trial_Documentation_$trialid');\n";

                        if (
                                isset($_REQUEST['subset']) ||
                                strpos($this->action, "Setup") !== false ||
                                strpos($this->action, "Channel") !== false ||
                                strpos($this->action, "TrialSensorLocation") !== false ||
                                strpos($this->action, "TrialSourceLocation") !== false ||
                                strpos($this->action, "TrialLocation") !== false ||
                                strpos($this->action, "Source") !== false ||
                                strpos($this->action, "DAQ") !== false) {

                            $this->selectedNodeID = $trialSetupNode;
                        }

                        if (strpos($this->action, "Data") !== false || strpos($this->action, "Repetition") !== false) {
                            $this->selectedNodeID = "TrialData_$trialid";

                            $reps = RepetitionPeer::findByTrial($trialid);

                            foreach ($reps as $rep) {
                                /* @var $rep Repetition */

                                $repid = $rep->getId();
                                $repName = $rep->getName();
                                $repURL = "/?projid=$projid&expid=$expid&trialid=$trialid&Rep=$repName&action=DisplayTrialData";

                                $treeHTML .= "tree.add('Repetition_$repid', 'TrialData_$trialid', '$repName', '$repURL', ico_folder);\n";
                                $treeHTML .= $this->getDataFilesByDir($rep->getPathname(), "Repetition_$repid");
                            }
                        } else {
                            $treeHTML .= "tree.setServerLoad('TrialData_$trialid');\n";
                        }

                        if ($this->action == "DisplayTrialAnalysis") {
                            $this->selectedNodeID = "DataFile_Trial_Analysis_$trialid";
                        } elseif ($this->action == "DisplayTrialDocumentation") {
                            $this->selectedNodeID = "DataFile_Trial_Documentation_$trialid";
                        } else {
                            $this->selectedNodeID = $tnodeID;
                        }

                        /**
                         * *******************************************************************
                         * Trial Setup
                         * *******************************************************************
                         */
                        if ($this->selectedNodeID == $trialSetupNode) {
                            $treeHTML .= "tree.add('" . $trialSetupNode . "_Source', '$trialSetupNode', 'Source Controller Configurations', '$url&action=DisplayTrialSetup&subset=Source#s1', ico_setup_section);\n";
                            $treeHTML .= "tree.add('" . $trialSetupNode . "_DAQ', '$trialSetupNode', 'DAQ Configurations', '$url&action=DisplayTrialSetup&subset=DAQ#s2', ico_setup_section);\n";
                            $treeHTML .= "tree.add('" . $trialSetupNode . "_TrialSensorLocation', '$trialSetupNode', 'Trial Sensor Location Plans', '$url&action=DisplayTrialSetup&subset=TrialSensorLocation#s3', ico_setup_section);\n";
                            $treeHTML .= "tree.add('" . $trialSetupNode . "_TrialSourceLocation', '$trialSetupNode', 'Trial Source Location Plans', '$url&action=DisplayTrialSetup&subset=TrialSourceLocation#s4', ico_setup_section);\n";

                            $subset = null;
                            if (isset($_REQUEST['subset'])) {
                                $subset = $_REQUEST['subset'];
                                if ($subset == "Source")
                                    $this->selectedNodeID = $trialSetupNode . "_Source";
                                elseif ($subset == "DAQ")
                                    $this->selectedNodeID = $trialSetupNode . "_DAQ";
                                elseif ($subset == "TrialSourceLocation")
                                    $this->selectedNodeID = $trialSetupNode . "_TrialSourceLocation";
                                elseif ($subset == "TrialSensorLocation")
                                    $this->selectedNodeID = $trialSetupNode . "_TrialSensorLocation";
                            }

                            /**
                             * *******************************************************************
                             * Trial Setup: Source Controller Config
                             * *******************************************************************
                             */
                            if (strpos($this->action, "ChannelList") === false && strpos($this->action, "Source") === false && $subset != "Source") {
                                $treeHTML .= "tree.setServerLoad('" . $trialSetupNode . "_Source');\n";
                            } else {
                                require_once 'lib/data/ControllerConfig.php';

                                $configs = ControllerConfigPeer::findByTrial($trialid);

                                $r_configId = isset($_REQUEST['clid']) ? $_REQUEST['clid'] : -1;

                                foreach ($configs as $config) {
                                    $configId = $config->getId();
                                    $configName = htmlspecialchars($config->getName(), ENT_QUOTES);

                                    $treeHTML .= "tree.add('sourceConfigId_$configId', '$trialSetupNode" . "_Source', '$configName', '$url&clid=$configId&action=DisplaySourceControllerConfiguration', ico_setup_item);\n";

                                    if ($r_configId == $configId) {
                                        $this->selectedNodeID = "sourceConfigId_" . $configId;
                                    }
                                }
                            }

                            /**
                             * *******************************************************************
                             * Trial Setup: DAQ Controller Config
                             * *******************************************************************
                             */
                            if (strpos(strpos($this->action, "ChannelList") === false && $this->action, "Sensor") === false && strpos($this->action, "DAQ") === false && $subset != "DAQ") {
                                $treeHTML .= "tree.setServerLoad('" . $trialSetupNode . "_DAQ');\n";
                            } else {
                                require_once 'lib/data/DAQConfig.php';

                                $configs = DAQConfigPeer::findByTrial($trialid);

                                $r_configId = isset($_REQUEST['clid']) ? $_REQUEST['clid'] : -1;

                                foreach ($configs as $config) {
                                    $configId = $config->getId();
                                    $configName = htmlspecialchars($config->getName(), ENT_QUOTES);

                                    $treeHTML .= "tree.add('DAQConfigId_$configId', '$trialSetupNode" . "_DAQ', '$configName', '$url&clid=$configId&action=DisplayDAQConfiguration', ico_setup_item);\n";

                                    if ($r_configId == $configId) {
                                        $this->selectedNodeID = "DAQConfigId_" . $configId;
                                    }
                                }
                            }

                            /**
                             * *******************************************************************
                             * Trial Setup: Trial Source Location Plans
                             * *******************************************************************
                             */
                            if (strpos($this->action, "TrialSourceLocation") === false && strpos($this->action, "TrialLocation") === false && $subset != "TrialSourceLocation") {
                                $treeHTML .= "tree.setServerLoad('" . $trialSetupNode . "_TrialSourceLocation');\n";
                            } else {
                                require_once 'lib/data/LocationPlan.php';

                                $slps = LocationPlanPeer::findSourceLocationPlanByTrial($trialid);

                                $r_lpid = isset($_REQUEST['lpid']) ? $_REQUEST['lpid'] : -1;

                                foreach ($slps as $slp) {
                                    $lpid = $slp->getId();
                                    $slpName = htmlspecialchars($slp->getName(), ENT_QUOTES);

                                    $treeHTML .= "tree.add('TrialSourceLocationId_$lpid', '$trialSetupNode" . "_TrialSourceLocation', '$slpName', '$url&lpid=$lpid&action=DisplayTrialSourceLocationPlan', ico_setup_item);\n";

                                    if ($r_lpid == $lpid) {
                                        $this->selectedNodeID = "TrialSourceLocationId_" . $lpid;
                                    }
                                }
                            }


                            /**
                             * *******************************************************************
                             * Trial Setup: Trial Sensor Location Plans
                             * *******************************************************************
                             */
                            if (strpos($this->action, "TrialSensorLocation") === false && strpos($this->action, "TrialLocation") === false && $subset != "TrialSensorLocation") {
                                $treeHTML .= "tree.setServerLoad('" . $trialSetupNode . "_TrialSensorLocation');\n";
                            } else {
                                require_once 'lib/data/LocationPlan.php';

                                $slps = LocationPlanPeer::findSensorLocationPlanByTrial($trialid);

                                $r_lpid = isset($_REQUEST['lpid']) ? $_REQUEST['lpid'] : -1;

                                foreach ($slps as $slp) {
                                    $lpid = $slp->getId();
                                    $slpName = htmlspecialchars($slp->getName(), ENT_QUOTES);

                                    $treeHTML .= "tree.add('TrialSensorLocationId_$lpid', '$trialSetupNode" . "_TrialSensorLocation', '$slpName', '$url&lpid=$lpid&action=DisplayTrialSensorLocationPlan', ico_setup_item);\n";

                                    if ($r_lpid == $lpid) {
                                        $this->selectedNodeID = "TrialSensorLocationId_" . $lpid;
                                    }
                                }
                            }
                        } else {
                            $treeHTML .= "tree.setServerLoad('$trialSetupNode');\n";
                        }
                    }
                }
            }
            /**
             * *******************************************************************
             * Simulation
             * *******************************************************************
             */ elseif ($expTypeId == ExperimentPeer::CLASSKEY_SIMULATION) {

                $simSetupNode = "simSetup_$expid";

                $treeHTML .= "tree.add('" . $enodeID . "_Runs', '$enodeID', 'Simulation-Run List', '$url&action=DisplaySimulationRuns', ico_r);\n";
                $treeHTML .= "tree.add('$simSetupNode', '$enodeID', 'Simulation Setup', '$url&action=DisplaySimulationSetup', ico_setup);\n";
                $treeHTML .= "tree.add('DataFile_Experiment_Analysis_$expid', '$enodeID', 'Simulation Analysis', '$url&action=DisplaySimulationAnalysis', ico_folder);\n";
                $treeHTML .= "tree.add('DataFile_Experiment_Documentation_$expid', '$enodeID', 'Simulation Documentation', '$url&action=DisplaySimulationDocumentation', ico_folder);\n";
                $treeHTML .= "tree.add('DataFile_Experiment_Public_$expid', '$enodeID', 'Simulation Public', '$url&action=DisplaySimulationPublic', ico_folder);\n";
                $treeHTML .= "tree.add('ExpMembers_" . $expid . "', '$enodeID', 'Simulation Members', '$url&action=DisplayExperimentMembers', ico_member);\n";

                $treeHTML .= "tree.setServerLoad('DataFile_Experiment_Analysis_$expid');\n";
                $treeHTML .= "tree.setServerLoad('DataFile_Experiment_Documentation_$expid');\n";
                $treeHTML .= "tree.setServerLoad('DataFile_Experiment_Public_$expid');\n";

                $simSetupNode = "simSetup_" . $expid;

                if (strpos($this->action, "Run") !== false) {
                    $this->selectedNodeID = $enodeID . '_Runs';
                } elseif (
                        isset($_REQUEST['subset']) ||
                        strpos($this->action, "Setup") !== false ||
                        strpos($this->action, "ComputerSystem") !== false ||
                        strpos($this->action, "Material") !== false ||
                        strpos($this->action, "Model") !== false
                ) {
                    $this->selectedNodeID = $simSetupNode;
                } elseif ($this->action == "DisplaySimulationAnalysis") {
                    $this->selectedNodeID = "DataFile_Experiment_Analysis_$expid";
                } elseif ($this->action == "DisplaySimulationDocumentation") {
                    $this->selectedNodeID = "DataFile_Experiment_Documentation_$expid";
                } elseif ($this->action == "DisplaySimulationPublic") {
                    $this->selectedNodeID = "DataFile_Experiment_Public_$expid";
                } elseif (strpos($this->action, "Member") !== false) {
                    $this->selectedNodeID = "ExpMembers_" . $expid;
                } else {
                    $this->selectedNodeID = $enodeID;
                }

                /**
                 * *******************************************************************
                 * Simulation Setup
                 * *******************************************************************
                 */
                if ($this->selectedNodeID == $simSetupNode) {

                    $treeHTML .= "tree.add('" . $simSetupNode . "_Computers', '$simSetupNode', 'Computer Systems',    '$url&action=DisplaySimulationSetup&subset=Computers#s1', ico_setup_section);\n";

                    if ($isHybrid) {
                        $treeHTML .= "tree.add('" . $simSetupNode . "_SpecimenComponents',   '$simSetupNode', 'Specimen Components', '$url&action=DisplaySimulationSetup&subset=SpecimenComponents#s9', ico_setup_section);\n";
                    } else {
                        $treeHTML .= "tree.add('" . $simSetupNode . "_Materials',   '$simSetupNode', 'Material Properties', '$url&action=DisplaySimulationSetup&subset=Materials#s2', ico_setup_section);\n";
                    }

                    $treeHTML .= "tree.add('" . $simSetupNode . "_Models', '$simSetupNode', 'Model Types', '$url&action=DisplaySimulationSetup&subset=Models#s3', ico_setup_section);\n";

                    $subset = null;
                    if (isset($_REQUEST['subset'])) {
                        $subset = $_REQUEST['subset'];

                        if ($subset == "Computers")
                            $this->selectedNodeID = $simSetupNode . "_Computers";
                        elseif ($subset == "Materials")
                            $this->selectedNodeID = $simSetupNode . "_Materials";
                        elseif ($subset == "Models")
                            $this->selectedNodeID = $simSetupNode . "_Models";
                        elseif ($subset == "SpecimenComponents")
                            $this->selectedNodeID = $simSetupNode . "_SpecimenComponents";
                    }

                    /**
                     * *******************************************************************
                     * Simulation Setup: Computer System
                     * *******************************************************************
                     */
                    if (strpos($this->action, "ComputerSystem") === false && $subset != "Computers") {
                        $treeHTML .= "tree.setServerLoad('" . $simSetupNode . "_Computers');\n";
                    } else {
                        require_once 'lib/data/Equipment.php';

                        $r_computerId = isset($_REQUEST['computersysid']) ? $_REQUEST['computersysid'] : -1;

                        $computers = EquipmentPeer::findByExperimentEquipmentClass($expid, 'Computer System');

                        foreach ($computers as $computer) {
                            $computerId = $computer->getId();
                            $computerName = htmlspecialchars($computer->getName(), ENT_QUOTES);
                            $computerUrl = $url . "&computersysid=$computerId&action=DisplayComputerSystem";
                            $treeHTML .= "tree.add('computersysId_" . $computerId . "', '$simSetupNode" . "_Computers', '$computerName', '$computerUrl',  ico_setup_item);\n";

                            if ($r_computerId == $computerId) {
                                $this->selectedNodeID = "computersysId_" . $computerId;
                            }
                        }
                    }


                    if ($isHybrid) {
                        if (strpos($this->action, "SpecimenComponent") === false && $subset != "SpecimenComponents") {
                            $treeHTML .= "tree.setServerLoad('" . $simSetupNode . "_SpecimenComponents');\n";
                        } else {
                            require_once 'lib/data/SpecimenComponentExperiment.php';

                            $this->selectedNodeID = $simSetupNode . "_SpecimenComponents";

                            $sces = SpecimenComponentExperimentPeer::findByExperiment($expid);

                            foreach ($sces as $sce) {
                                /* @var $sce SpecimenComponentExperiment */
                                $sc = $sce->getSpecimenComponent();
                                $specimenId = $sc->getSpecimenId();
                                $specCompId = $sc->getId();
                                $specCompName = htmlspecialchars($sc->getName(), ENT_QUOTES);
                                $specCompUrl = "/?projid=$projid&specimenId=$specimenId&specCompId=$specCompId&action=DisplaySpecimenComponent";

                                $treeHTML .= "tree.add('SpecimenComponentExperimentId_" . $specCompId . "', '$simSetupNode" . "_SpecimenComponents', '$specCompName', '$specCompUrl',  ico_setup_item);\n";
                            }
                        }
                    } else {
                        /**
                         * *******************************************************************
                         * Simulation Setup: Material
                         * *******************************************************************
                         */
                        if (strpos($this->action, "Material") === false && $subset != "Materials") {
                            $treeHTML .= "tree.setServerLoad('" . $simSetupNode . "_Materials');\n";
                        } else {
                            require_once 'lib/data/Material.php';

                            $r_materialId = isset($_REQUEST['materialid']) ? $_REQUEST['materialid'] : -1;

                            $materials = MaterialPeer::findByExperiment($expid);

                            foreach ($materials as $material) {
                                $materialId = $material->getId();
                                $materialName = htmlspecialchars($material->getName(), ENT_QUOTES);
                                $materialUrl = $url . "&materialid=$materialId&action=ViewMaterial";

                                $treeHTML .= "tree.add('materialId_" . $materialId . "', '$simSetupNode" . "_Materials', '$materialName', '$materialUrl',  ico_setup_item);\n";

                                if ($r_materialId == $materialId) {
                                    $this->selectedNodeID = "materialId_" . $materialId;
                                }
                            }
                        }
                    }

                    /**
                     * *******************************************************************
                     * Simulation Setup: Model Types
                     * *******************************************************************
                     */
                    if (strpos($this->action, "Model") === false && $subset != "Models") {
                        $treeHTML .= "tree.setServerLoad('" . $simSetupNode . "_Models');\n";
                    } else {

                        require_once 'lib/data/ExperimentModel.php';

                        $r_modelId = isset($_REQUEST['modelid']) ? $_REQUEST['modelid'] : -1;

                        $models = ExperimentModelPeer::findByExperiment($expid);

                        foreach ($models as $model) {
                            $modelId = $model->getId();
                            $modelName = htmlspecialchars($model->getName(), ENT_QUOTES);
                            $modelUrl = $url . "&modelid=$modelId&action=DisplaySimModel";

                            $treeHTML .= "tree.add('modelId_" . $modelId . "', '$simSetupNode" . "_Models', '$modelName', '$modelUrl',  ico_setup_item);\n";

                            if ($r_modelId == $modelId) {
                                $this->selectedNodeID = "modelId_" . $modelId;
                            }
                        }
                    }
                } else {
                    $treeHTML .= "tree.setServerLoad('simSetup_$expid');\n";
                }

                /**
                 * *******************************************************************
                 * SimulationRun
                 * *******************************************************************
                 */
                $runs = TrialPeer::findBySimulation($expid);

                foreach ($runs as $r) {
                    $runid = $r->getId();
                    $title = $this->clean_newlines($r->getTitle());
                    $url = "/?projid=$projid&expid=$expid&trialid=$runid";

                    $rnodeID = "runid_" . $runid;
                    $treeHTML .= "\n// runID: $runid \n";
                    $treeHTML .= "tree.add('$rnodeID', '" . $enodeID . "_Runs',   '$title', '$url&action=DisplaySimulationRunMain', ico_rc);\n";

                    if ($this->trialid != $runid) {
                        $treeHTML .= "tree.setServerLoad('$rnodeID');\n";
                    } else {
                        $treeHTML .= "tree.add('" . $rnodeID . "_Files', '$rnodeID', 'Run Files', '$url&action=DisplaySimFiles', ico_folder);\n";

                        if ($this->action == "DisplaySimFiles") {
                            $this->selectedNodeID = $rnodeID . '_Files';
                        } else {
                            $this->selectedNodeID = $rnodeID;
                        }
                    }
                }
            }
            /**
             * *******************************************************************
             * Unstructured Experiment
             * *******************************************************************
             */ elseif ($expTypeId == ExperimentPeer::CLASSKEY_UNSTRUCTUREDEXPERIMENT) {
                $treeHTML .= "tree.add('" . $enodeID . "_Files',    '$enodeID', 'Experiment Files',   '$url&action=DisplayExperimentFiles',  ico_folder);\n";
                $treeHTML .= "tree.add('" . $enodeID . "_Public',   '$enodeID', 'Experiment Public',  '$url&action=DisplayExperimentPublic', ico_folder);\n";
                $treeHTML .= "tree.add('ExpMembers_" . $expid . "',  '$enodeID', 'Experiment Members', '$url&action=DisplayExperimentMembers',   ico_member);\n";

                if ($this->action == "DisplayExperimentFiles") {
                    $this->selectedNodeID = $enodeID . '_Files';
                } elseif ($this->action == "DisplayExperimentPublic") {
                    $this->selectedNodeID = $enodeID . '_Public';
                } elseif (strpos($this->action, "Member") !== false) {
                    $this->selectedNodeID = "ExpMembers_" . $expid;
                } else {
                    $this->selectedNodeID = $enodeID;
                }
            }

            /**
             * *******************************************************************
             * Experiment Members
             * *******************************************************************
             */
            if ($this->selectedNodeID == "ExpMembers_" . $expid) {
                if ($projid != 354) {
                    $member = PersonPeer::findMembersPermissionsForEntity($expid, 3);
                } else {
                    $member = PersonPeer::findMembersWithFullPermissionsForEntity($expid, 3);
                }

                $member->setFetchmode(ResultSet::FETCHMODE_ASSOC);

                $r_personId = isset($_REQUEST['personId']) ? $_REQUEST['personId'] : null;

                $auth = Authorizer::getInstance();
                $canGrant = $auth->canGrant($e);

                while ($member->next()) {

                    $member_id = $member->getInt("PERSON_ID");
                    $member_fullname = htmlspecialchars($member->get("FIRST_NAME") . " " . $member->get("LAST_NAME"), ENT_QUOTES);
                    $member_email = $member->get("E_MAIL");
                    $member_link = $canGrant ? "/?projid=$projid&expid=$expid&action=DisplayExperimentMembers&personId=$member_id" : "";

                    $treeHTML .= "tree.add('eMemberId_" . $expid . "_" . $member_id . "', 'ExpMembers_" . $expid . "', '$member_fullname', '$member_link', ico_person);\n";

                    if ($r_personId == $member_id) {
                        $this->selectedNodeID = "eMemberId_" . $expid . "_" . $member_id;
                    }
                }
            } else {
                $treeHTML .= "tree.setServerLoad('ExpMembers_" . $expid . "');\n";
            }
        }

        return $treeHTML;
    }

    /**
     * Output Javascript Code for Coordinator
     *
     * @param Coordinator $c
     * @param int $projid
     * @param String $pnodeID
     * @return String $html
     */
    private function printCoordinator(Coordinator $coordinator, $projid, $parentNodeID) {
        $coordinatorId = $coordinator->getId();
        $coordinatorName = $this->clean_newlines($coordinator->getName());
        $url = "/?projid=$projid&coordinatorId=$coordinatorId";
        $treeHTML = "\n// coordinatorId: $coordinatorId \n";

        $coordinatorNodeID = "coordinatorId_" . $coordinatorId;
        $treeHTML .= "tree.add('$coordinatorNodeID', '$parentNodeID',   '$coordinatorName', '$url&action=DisplayCoordinator', ico_coordinator);\n";

        if ($this->coordinatorId != $coordinatorId) {
            $treeHTML .= "tree.setServerLoad('coordinatorId_$coordinatorId');\n";
        } else {
            $treeHTML .= "tree.add('" . $coordinatorNodeID . "_CoordinatorRuns', '$coordinatorNodeID', 'Coordinator-Run List', '$url&action=ListCoordinatorRuns', ico_coordinatorRunList);\n";
            $treeHTML .= "tree.add('DataFile_Coordinator_Documentation_$coordinatorId', '$coordinatorNodeID', 'Coordinator Documentation', '$url&action=DisplayCoordinatorDocumentation', ico_folder);\n";

            $treeHTML .= "tree.setServerLoad('DataFile_Coordinator_Documentation_$coordinatorId');\n";

            if (strpos($this->action, "CoordinatorRun") !== false) {
                $this->selectedNodeID = $coordinatorNodeID . '_CoordinatorRuns';
            } elseif ($this->action == "DisplayCoordinatorDocumentation") {
                $this->selectedNodeID = "DataFile_Coordinator_Documentation_$coordinatorId";
            } else {
                $this->selectedNodeID = $coordinatorNodeID;
            }

            /**
             * *******************************************************************
             * CoordinatorRun
             * *******************************************************************
             */
            $coordinatorRuns = CoordinatorRunPeer::findByCoordinator($coordinatorId);

            foreach ($coordinatorRuns as $coordinatorRun) {
                $coordinatorRunId = $coordinatorRun->getId();
                $coordinatorRunName = $this->clean_newlines($coordinatorRun->getName());
                $url = "/?projid=$projid&coordinatorId=$coordinatorId&coordinatorRunId=$coordinatorRunId";

                $coordinatorRunNodeID = "coordinatorRunId_" . $coordinatorRunId;
                $treeHTML .= "\n// coordinatorRunId: $coordinatorRunId \n";
                $treeHTML .= "tree.add('$coordinatorRunNodeID', '" . $coordinatorNodeID . "_CoordinatorRuns', '$coordinatorRunName', '$url&action=DisplayCoordinatorRun', ico_coordinatorRun);\n";

                if ($this->coordinatorRunId != $coordinatorRunId) {
                    $treeHTML .= "tree.setServerLoad('$coordinatorRunNodeID');\n";
                } else {
                    $treeHTML .= "tree.add('" . $coordinatorRunNodeID . "_Experiments', '$coordinatorRunNodeID', 'Physical Substructures', '$url&action=ListPhysicalSubstructures', ico_e);\n";
                    $treeHTML .= "tree.add('" . $coordinatorRunNodeID . "_Simulations', '$coordinatorRunNodeID', 'Analytical Substructures', '$url&action=ListAnalyticalSubstructures', ico_s);\n";
                    $treeHTML .= "tree.add('DataFile_CoordinatorRun_Files_$coordinatorRunId', '$coordinatorRunNodeID', 'Coordinator-Run Files', '$url&action=DisplayCoordinatorRunFiles', ico_folder);\n";
                    $treeHTML .= "tree.add('DataFile_CoordinatorRun_Analysis_$coordinatorRunId', '$coordinatorRunNodeID', 'Coordinator-Run Analysis', '$url&action=DisplayCoordinatorRunAnalysis', ico_folder);\n";
                    $treeHTML .= "tree.add('DataFile_CoordinatorRun_Documentation_$coordinatorRunId',  '$coordinatorRunNodeID', 'Coordinator-Run Documentation', '$url&action=DisplayCoordinatorRunDocumentation', ico_folder);\n";

                    $treeHTML .= "tree.setServerLoad('DataFile_CoordinatorRun_Files_$coordinatorRunId');\n";
                    $treeHTML .= "tree.setServerLoad('DataFile_CoordinatorRun_Analysis_$coordinatorRunId');\n";
                    $treeHTML .= "tree.setServerLoad('DataFile_CoordinatorRun_Documentation_$coordinatorRunId');\n";

                    if ($this->action == "ListPhysicalSubstructures") {
                        $this->selectedNodeID = $coordinatorRunNodeID . '_Experiments';
                    } elseif ($this->action == "ListAnalyticalSubstructures") {
                        $this->selectedNodeID = $coordinatorRunNodeID . '_Simulations';
                    } elseif ($this->action == "DisplayCoordinatorRunFiles") {
                        $this->selectedNodeID = "DataFile_CoordinatorRun_Files_$coordinatorRunId";
                    } elseif ($this->action == "DisplayCoordinatorRunAnalysis") {
                        $this->selectedNodeID = "DataFile_CoordinatorRun_Analysis_$coordinatorRunId";
                    } elseif ($this->action == "DisplayCoordinatorRunDocumentation") {
                        $this->selectedNodeID = "DataFile_CoordinatorRun_Documentation_$coordinatorRunId";
                    } else {
                        $this->selectedNodeID = $coordinatorRunNodeID;
                    }

                    $experiments = $coordinatorRun->getPhysicalSubstructures();
                    $simulations = $coordinatorRun->getAnalyticalSubstructures();

                    foreach ($experiments as $experiment) {
                        $treeHTML .= $this->printExperiment($experiment, $projid, $coordinatorRunNodeID . '_Experiments', true);
                    }

                    foreach ($simulations as $simulation) {
                        $treeHTML .= $this->printExperiment($simulation, $projid, $coordinatorRunNodeID . '_Simulations', true);
                    }
                }
            }
        }

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
    private function printSpecimen(Specimen $specimen, $projid, $parentNodeID) {

        $specimenId = $specimen->getId();
        $specimenName = $this->clean_newlines($specimen->getName());
        $url = "/?projid=$projid&specimenId=$specimenId";
        $treeHTML = "\n// specimenId: $specimenId \n";

        $specimenNodeID = "specimenId_" . $specimenId;
        $treeHTML .= "tree.add('$specimenNodeID', '$parentNodeID', '$specimenName', '$url&action=DisplaySpecimen', ico_specimen);\n";

        if ($this->specimenId != $specimenId) {
            $treeHTML .= "tree.setServerLoad('specimenId_$specimenId');\n";
        } else {
            $treeHTML .= "tree.add('specimenComponents_" . $specimenId . "', '$specimenNodeID', 'Specimen-Component List', '$url&action=ListSpecimenComponents', ico_specCompList);\n";
            $treeHTML .= "tree.add('DataFile_Specimen_Documentation_$specimenId', '$specimenNodeID', 'Specimen Documentation', '$url&action=DisplaySpecimenDocumentation', ico_folder);\n";

            $treeHTML .= "tree.setServerLoad('DataFile_Specimen_Documentation_$specimenId');\n";

            if (strpos($this->action, "SpecimenComponent") !== false) {
                $this->selectedNodeID = "specimenComponents_" . $specimenId;
            } elseif ($this->action == "DisplaySpecimenDocumentation") {
                $this->selectedNodeID = "DataFile_Specimen_Documentation_$specimenId";
            } else {
                $this->selectedNodeID = $specimenNodeID;
            }

            /**
             * *******************************************************************
             * SpecimenComponent
             * *******************************************************************
             */
            $specComps = SpecimenComponentPeer::findBySpecimen($specimenId);

            foreach ($specComps as $specComp) {
                $specCompId = $specComp->getId();
                $specCompName = $this->clean_newlines($specComp->getName());
                $url = "/?projid=$projid&specimenId=$specimenId&specCompId=$specCompId";

                $materialsNodeId = "SpecimenComponentMaterials_" . $specCompId;

                $specCompNodeID = "specCompId_" . $specCompId;
                $treeHTML .= "\n// specCompId: $specCompId \n";
                $treeHTML .= "tree.add('$specCompNodeID', 'specimenComponents_" . $specimenId . "', '$specCompName', '$url&action=DisplaySpecimenComponent', ico_specimen_component);\n";
                $treeHTML .= "tree.add('$materialsNodeId', '$specCompNodeID', 'Properties', '$url&action=ListSpecimenComponentProperties', ico_setup);\n";
                $treeHTML .= "tree.add('DataFile_SpecimenComponent_Documentation_$specCompId', '$specCompNodeID', 'Specimen-Component Documentation', '$url&action=DisplaySpecimenComponentDocumentation', ico_folder);\n";

                $treeHTML .= "tree.setServerLoad('DataFile_SpecimenComponent_Documentation_$specCompId');\n";

                $loadedMaterial = false;

                if ($this->specCompId == $specCompId) {
                    if ($this->action == "DisplaySpecimenComponentDocumentation") {
                        $this->selectedNodeID = "DataFile_SpecimenComponent_Documentation_$specCompId";
                    } elseif (isset($_REQUEST['materialId']) || strpos($this->action, "SpecimenComponentProperties") !== false) {
                        $this->selectedNodeID = $materialsNodeId;

                        $materials = SpecimenComponentMaterialPeer::findByComponent($specCompId);

                        $current_materialId = isset($_REQUEST['materialId']) ? $_REQUEST['materialId'] : -1;

                        foreach ($materials as $material) {
                            /* @var $material SpecimenComponentMaterial */
                            $materialId = $material->getId();
                            $treeHTML .= "tree.add('SpecimenComponentMaterialId_" . $materialId . "', '$materialsNodeId', '" . $material->getName() . "', '$url&materialId=$materialId&action=DisplaySpecimenComponentProperty', ico_setup_section);\n";

                            if ($materialId == $current_materialId) {
                                $this->selectedNodeID = "SpecimenComponentMaterialId_" . $materialId;
                            }
                        }

                        $loadedMaterial = true;
                    } else {
                        $this->selectedNodeID = $specCompNodeID;
                    }
                }

                if (!$loadedMaterial) {
                    $treeHTML .= "tree.setServerLoad('$materialsNodeId');\n";
                }
            }
        }

        return $treeHTML;
    }

    private function getDataFilesByDir($dir, $parentNode) {

        $dfs = DataFilePeer::findByDirectoryWithOrderBy($dir);

        $treeHTML = "";

        foreach ($dfs as $df) {
            /* @var $df DataFile */
            $df_name = $df->getName();
            $df_id = $df->getId();
            $is_dir = $df->isDirectory();

            if (file_exists($df->getFullPath())) {
                if ($is_dir) {
                    $treeHTML .= "tree.add('DataFile_$df_id', '$parentNode', '$df_name', '', ico_dir);\n";
                    $treeHTML .= "tree.setServerLoad('DataFile_$df_id');\n";
                } else {
                    $df_url = $df->get_url();
                    $treeHTML .= "tree.add('DataFile_$df_id', '$parentNode', '$df_name', '$df_url', '');\n";
                    $treeHTML .= "tree.setNodeTarget('DataFile_$df_id', '_blank');\n";
                }
            }
        }

        return $treeHTML;
    }

}
?>
