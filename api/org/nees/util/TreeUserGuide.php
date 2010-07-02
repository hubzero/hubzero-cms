<?php

function getHelpDocTree() {
  global $ini_array;
  $centralhost = $ini_array['centralhost'];
  $nodeId = 1;

  $tree = <<<ENDHTML

<div class="treeExpand"><a href="javascript:tree.expandAll();void(0);">Expand All</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="javascript:tree.collapseAll();void(0);">Collapse All</a></div>

<script type="text/javascript">
<!--
  var tree=new NlsTree("treeUserGuide");
  tree.opt.renderOnDemand = true;
  tree.opt.hideRoot=false;
  tree.opt.selRow = true;
  tree.treeOnClick = ev_click;

  var ico_p = "/tree_browser/img/p.gif";
  var ico_fc = "/tree_browser/img/f_color.gif";
  var ico_e = "/tree_browser/img/e.gif";
  var ico_t = "/tree_browser/img/t.gif";
  var ico_pc = "/tree_browser/img/p_color.gif";
  var ico_member = "/tree_browser/img/ico_member.gif";
  var ico_person = "/tree_browser/img/ico_person.gif";
  var ico_equiplist = "/tree_browser/img/ico_equiplist.gif";
  var ico_sensors_list = "/tree_browser/img/ico_sensors_list.gif";
  var ico_cert = "/tree_browser/img/ico_cert.gif";
  var ico_setup = "/tree_browser/img/ico_equiplist.gif";
  var ico_setup_section = "/tree_browser/img/ico_equip.gif";
  var ico_project_specimen = "/tree_browser/img/project_specimen.gif";
  var ico_specCompList = "/tree_browser/img/specimen_component_list.gif";
  var ico_project_coordinator = "/tree_browser/img/project_coordinator.gif";
  var ico_coordinatorRunList = "/tree_browser/img/coordinatorRunList.gif";
  var ico_plus = "/images/add_sign.gif";
  var ico_activities = "/activities/common/images/video_feed.gif";
  var ico_folder = "/tree_browser/img/bluefolder.gif";
  var ico_n3dv = "/tree_browser/img/ico_n3dv.gif";

  preloadIcon(ico_p, ico_fc, ico_e, ico_t, ico_pc, ico_member, ico_person, ico_equiplist, ico_sensors_list, ico_cert, ico_setup, ico_setup_section, ico_project_specimen, ico_specCompList, ico_project_coordinator, ico_coordinatorRunList, ico_plus, ico_activities, ico_folder, ico_n3dv);

  function ev_click(e, id) {
    tree.expandNode(id);
  }

  function initTree()
  {
tree.add(0.5, 0, "&nbsp;&nbsp;NEEScentral :: User Guide", "javascript:showNCGuide()", "", true);

// 1. Overview
tree.add(1, 0.5, "1. Overview", "javascript:showNCGuide('over')", null);

// 2. Getting Started
tree.add(2, 0.5, "2. Getting Started", "javascript:showNCGuide('start')", null);
tree.add(2.1, 2, "2.1. Software Requirements", "javascript:showNCGuide('s_req')", null);
tree.add(2.2, 2, "2.2. Getting an Account and Logging In", "javascript:showNCGuide('s_log')", null);
tree.add(2.3, 2, "2.3. Forgotten Username or Password", "javascript:showNCGuide('s_user')", null);

// 3. Managing Your Account
tree.add(3, 0.5, "3. Managing Your Account", "javascript:showNCGuide('mang')", null);
tree.add(3.1, 3, "3.1. Viewing Your Account Information", "javascript:showNCGuide('m_ai')", null);
tree.add(3.2, 3, "3.2. Changing Your Account Information", "javascript:showNCGuide('m_ac')", null);
tree.add(3.3, 3, "3.3. Changing Your Password", "javascript:showNCGuide('m_pc')", null);

// 4. Facilities
tree.add(4, 0.5,  "4. Facilities", "javascript:showNCGuide('fac')", null);
tree.add(4.1, 4, "4.1. NEES Equipment sites: Basic Information", "javascript:showNCGuide('f_bi')", ico_fc);
tree.add(4.11, 4.1, "4.1.1. Contact Information", "javascript:showNCGuide('f_ci')", null);
tree.add(4.12, 4.1, "4.1.2. Staff", "javascript:showNCGuide('f_staff')", ico_member);
tree.add(4.13, 4.1, "4.1.3. Equipment", "javascript:showNCGuide('f_equip')", ico_equiplist);
tree.add(4.14, 4.1, "4.1.4. Sensors", "javascript:showNCGuide('f_sens')", ico_sensors_list);
tree.add(4.15, 4.1, "4.1.5. Training and Certification", "javascript:showNCGuide('f_train')", ico_cert);
tree.add(4.16, 4.1, "4.1.6. Education and Outreach", "javascript:showNCGuide('f_edu')", ico_folder);
tree.add(4.2, 4, "4.2. NEES Activities: Experiments", "javascript:showNCGuide('f_act')", ico_activities);

// 5. Navigating NEEScentral
tree.add(5, 0.5, "5. Navigating NEEScentral", "javascript:showNCGuide('nav')", null);
tree.add(5.1, 5, "5.1. Finding Existing Projects", "javascript:showNCGuide('n_find')", null);
tree.add(5.11, 5.1, "5.1.1. Project Tree Browser", "javascript:showNCGuide('n_tree')", null);
tree.add(5.12, 5.1, "5.1.2. Browsing Visible Projects", "javascript:showNCGuide('n_browse')", null);
tree.add(5.13, 5.1, "5.1.3. Searching Visible Projects", "javascript:showNCGuide('n_search')", null);
tree.add(5.14, 5.1, "5.1.4. Viewing Project Information", "javascript:showNCGuide('n_view')", null);
tree.add(5.2, 5, "5.2. Finding Experiments and Simulations", "javascript:showNCGuide('n_exp')", null);
tree.add(5.21, 5.2, "5.2.1. Initially", "javascript:showNCGuide('n_init')", null);
tree.add(5.22, 5.2, "5.2.2. Returning to the List of Experiments and Simulations", "javascript:showNCGuide('n_ret')", null);
tree.add(5.3, 5, "5.3. Finding Trials and Runs", "javascript:showNCGuide('n_trial')", null);
tree.add(5.31, 5.3, "5.3.1. Initially", "javascript:showNCGuide('n_tinit')", null);
tree.add(5.32, 5.3, "5.3.2. Returning to the List of Trials or Runs", "javascript:showNCGuide('n_tret')", null);

// 6. Structuring Research Data
tree.add(6, 0.5, "6. Structuring Your Research Data", "javascript:showNCGuide('str')", null);
tree.add(6.1, 6, "6.1. Unstructured Project", "javascript:showNCGuide('s_un')", ico_pc);
tree.add(6.2, 6, "6.2. Structured Project", "javascript:showNCGuide('s_st')", ico_pc);
tree.add(6.21, 6.2, "6.2.1. Specimen", "javascript:showNCGuide('s_stspe')", ico_project_specimen);
tree.add(6.211, 6.21, "6.2.1.1. Specimen Component", "javascript:showNCGuide('s_stspecom')", ico_specCompList);
tree.add(6.3, 6, "6.3. Hybrid Project", "javascript:showNCGuide('s_hy')", ico_pc);
tree.add(6.31, 6.3, "6.3.1. Coordinator", "javascript:showNCGuide('s_cor')", ico_project_coordinator);
tree.add(6.311, 6.31, "6.3.1.1. Coordinator Run", "javascript:showNCGuide('s_corrun')", ico_coordinatorRunList);
tree.add(6.32, 6.3, "6.3.2. Specimen", "javascript:showNCGuide('s_spe')", ico_project_specimen);
tree.add(6.321, 6.32, "6.3.2.1. Specimen Component", "javascript:showNCGuide('s_specom')", ico_specCompList);
tree.add(6.4, 6, "6.4. Project Group", "javascript:showNCGuide('s_pr')", ico_pc);


// 7. Project
tree.add(7, 0.5, "7. Projects", "javascript:showNCGuide('proj')", ico_p);
tree.add(7.1, 7, "7.1. Creating a New Project", "javascript:showNCGuide('p_create')", ico_plus);
tree.add(7.2, 7, "7.2. Cloning a Project", "javascript:showNCGuide('p_clone')", null);
tree.add(7.3, 7, "7.3. Editing Project Information", "javascript:showNCGuide('p_edit')", null);
tree.add(7.4, 7, "7.4. Export Project Information", "javascript:showNCGuide('p_export')", null);
tree.add(7.5, 7, "7.5. Viewing a Project Report", "javascript:showNCGuide('p_view')", null);
tree.add(7.6, 7, "7.6. Managing Project Members", "javascript:showNCGuide('p_mang')", ico_member);
tree.add(7.61, 7.6, "7.6.1. Roles and Permissions", "javascript:showNCGuide('p_role')", ico_person);
tree.add(7.62, 7.6, "7.6.2. Adding Members to Your Project", "javascript:showNCGuide('p_add')", ico_person);
tree.add(7.63, 7.6, "7.6.3. Editing and Deleting Members", "javascript:showNCGuide('p_emem')", ico_person);
tree.add(7.7, 7, "7.7. Analysis", "javascript:showNCGuide('p_an')", ico_folder);
tree.add(7.8, 7, "7.8. Documentation", "javascript:showNCGuide('p_doc')", ico_folder);
tree.add(7.9, 7, "7.9. Public", "javascript:showNCGuide('p_pub')", ico_folder);

// 8. Experiments and Simulations
tree.add(8, 0.5, "8. Experiments and Simulations", "javascript:showNCGuide('exp_sim')", ico_e);
tree.add(8.1, 8, "8.1. Creating a New Experiment or Simulation", "javascript:showNCGuide('es_create')", ico_plus);
tree.add(8.2, 8, "8.2. Cloning an Experiment", "javascript:showNCGuide('es_clone')", null);
tree.add(8.3, 8, "8.3. Editing Experiment or Simulation Information", "javascript:showNCGuide('es_edit')", null);
tree.add(8.4, 8, "8.4. Viewing an Experiment Report", "javascript:showNCGuide('es_view')", null);
tree.add(8.5, 8, "8.5. Setup", "javascript:showNCGuide('es_set')", ico_setup);
tree.add(8.51, 8.5, "8.5.1. Measurement Units", "javascript:showNCGuide('es_meas')", ico_setup_section);
tree.add(8.52, 8.5, "8.5.2. Material Properties", "javascript:showNCGuide('es_mat')", ico_setup_section);
tree.add(8.53, 8.5, "8.5.3. Coordinate Spaces", "javascript:showNCGuide('es_coord')", ico_setup_section);
tree.add(8.54, 8.5, "8.5.4. Sensor Location Plans", "javascript:showNCGuide('es_sense')", ico_setup_section);
tree.add(8.55, 8.5, "8.5.5. Source Location Plans", "javascript:showNCGuide('es_source')", ico_setup_section);
tree.add(8.56, 8.5, "8.5.6. Equipment Inventory", "javascript:showNCGuide('es_equip')", ico_setup_section);
tree.add(8.57, 8.5, "8.5.7. Scale Factors", "javascript:showNCGuide('es_scale')", ico_setup_section);
tree.add(8.58, 8.5, "8.5.8. Models", "javascript:showNCGuide('es_mod')", ico_setup_section);
tree.add(8.59, 8.5, "8.5.9. Computer Systems", "javascript:showNCGuide('es_cs')", ico_setup_section);
tree.add(8.5105, 8.5, "8.5.10. Model Types", "javascript:showNCGuide('es_mt')", ico_setup_section);
tree.add(8.6, 8, "8.6. Analysis", "javascript:showNCGuide('es_an')", ico_folder);
tree.add(8.7, 8, "8.7. Documentation", "javascript:showNCGuide('es_doc')", ico_folder);
tree.add(8.8, 8, "8.8. Public", "javascript:showNCGuide('es_pub')", ico_folder);
tree.add(8.9, 8, "8.9. Managing Experiment or Simulation Members", "javascript:showNCGuide('es_mang')", ico_member);
tree.add(8.105, 8, "8.10. Data Viewers (N3DV)", "javascript:showNCGuide('es_exp')", ico_n3dv);
tree.add(8.11, 8, "8.11. Exporting Experiment Information and Data", "javascript:showNCGuide('es_export')", null);

// 9. Trial
tree.add(9, 0.5, "9. Trials, Runs, and Repetitions", "javascript:showNCGuide('trr')", ico_t);
tree.add(9.1, 9, "9.1. Creating a New Trial or Run", "javascript:showNCGuide('trr_n')", null);
tree.add(9.2, 9, "9.2. Creating a Trial Repetition", "javascript:showNCGuide('trr_r')", null);
tree.add(9.3, 9, "9.3. Exporting Trial Information and Data", "javascript:showNCGuide('trr_exp')", null);
tree.add(9.4, 9, "9.4. Cloning a Trial or Run", "javascript:showNCGuide('trr_cl')", null);
tree.add(9.5, 9, "9.5. Trial Setup", "javascript:showNCGuide('trr_ts')", ico_setup);
tree.add(9.51, 9.5, "9.5.1. Controller Configurations", "javascript:showNCGuide('trr_source')", ico_setup_section);
tree.add(9.52, 9.5, "9.5.2. DAQ Configurations", "javascript:showNCGuide('trr_daq')", ico_setup_section);
tree.add(9.53, 9.5, "9.5.3. Trial Sensor Location Plans", "javascript:showNCGuide('trr_sel')", ico_setup_section);
tree.add(9.54, 9.5, "9.5.4. Trial Source Location Plans", "javascript:showNCGuide('trr_sol')", ico_setup_section);
tree.add(9.6, 9, "9.6. Analysis", "javascript:showNCGuide('trr_an')", ico_folder);
tree.add(9.7, 9, "9.7. Documentation", "javascript:showNCGuide('trr_doc')", ico_folder);
tree.add(9.8, 9, "9.8. Files", "javascript:showNCGuide('trr_f')", ico_folder);

// 10. Trial Data
tree.add(10, 0.5, "10. Trial Data", "javascript:showNCGuide('dat')", null);
tree.add(10.1, 10, "10.1. Unprocessed Data", "javascript:showNCGuide('d_u')", ico_folder);
tree.add(10.2, 10, "10.2. Converted Data", "javascript:showNCGuide('d_con')", ico_folder);
tree.add(10.3, 10, "10.3. Corrected Data", "javascript:showNCGuide('d_cor')", ico_folder);
tree.add(10.4, 10, "10.4. Derived Data", "javascript:showNCGuide('d_d')", ico_folder);

// 11. File Operations
tree.add(11, 0.5, "11. File Operations", "javascript:showNCGuide('fileop')", null);
tree.add(11.1, 11, "11.1. Creating New Folders", "javascript:showNCGuide('fo_cr')", null);
tree.add(11.2, 11, "11.2. Uploading Files", "javascript:showNCGuide('fo_up')", null);
tree.add(11.3, 11, "11.3. Downloading Files", "javascript:showNCGuide('fo_down')", null);
tree.add(11.4, 11, "11.4. Viewing and Editing Metadata", "javascript:showNCGuide('fo_view')", null);
tree.add(11.5, 11, "11.5. Renaming Files", "javascript:showNCGuide('fo_name')", null);
tree.add(11.6, 11, "11.6. Moving or Copying Files", "javascript:showNCGuide('fo_move')", null);
tree.add(11.7, 11, "11.7. Deleting Files", "javascript:showNCGuide('fo_del')", null);
tree.add(11.8, 11, "11.8. Graphing Tool", "javascript:showNCGuide('fo_graph')", null);

  }
  initTree();
// -->
</script>

<div class="contentpadding">
  <div id="tree_browser">
    <script type="text/javascript">
<!--
    tree.render();
// -->
    </script>
    <div class='floatright' style='font-family:arial;font-size:7pt;color:#666666;padding-top:5px'><br/><br/><br/>&nbsp;&nbsp;NEEScentral&nbsp;https://$centralhost&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/><br/><br/><br/></div>
  </div>
</div>
<div style="clear: both;"></div>


ENDHTML;

  return $tree;

}

?>