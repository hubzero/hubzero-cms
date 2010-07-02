<?php

class DataFilePicker {

  public function __construct() {
  }


  /**
   * Create a Tree data file Javascript
   *
   * @param BaseObject $entity
   * @param String $dirName
   * @param String $displayName
   * @param String $tree_name
   * @param String $selected_field_id
   * @param boolean $file_select
   * @param array $filterArr
   * @param boolean $expand
   * @param boolean $isCheckbox
   * @param boolean $selectRow
   * @param String $fileType
   * @return String
   */
  function makeTreeDataFile($entity, $dirName, $displayName = "", $tree_name = null, $selected_field_id, $file_select = true, $filterArr = null, $expand = true, $isCheckbox = true, $selectRow = true, $fileType = "") {
    if(is_null($entity)) return;

    //if(empty($displayName)) $displayName = $dirName;
    if(!empty($displayName)) $displayName = "<b>" . $displayName . "</b>";

    $jstree = "<div style='padding-bottom:10px;'>" . $displayName . "</div>";

    if(empty($tree_name)) $tree_name = "tree_picker";

    if(empty($dirName)) {
      $root_path = $entity->getPathname();
    }
    else {
      $root_path = $entity->getPathname() . "/" . $dirName;
    }

    $friendlypath = get_friendlyPath($root_path);

    $selectednode = 1;

    $parentnode = array();
    $parentnode[$root_path] = 1;

    $cond = "";
    if(is_array($filterArr) && count($filterArr) > 0) {
      $arr = array();
      foreach($filterArr as $ext) {
        $arr[] = "'" . strtolower($ext) . "'";
      }

      $cond = " and lower(substr(name,1+regexp_instr(name, '(.[^\.]+$)'))) in (" . implode(",", $arr) . ")";
    }

    $sql = "SELECT id, path, name FROM Data_File WHERE concat(path,'/') LIKE '" . $root_path . "%' AND deleted = 0 AND directory = " . ($file_select ? "0" : "1") . $cond . "ORDER BY directory desc, path, name";

    $conn = Propel::getConnection();
    $stmt = $conn->prepareStatement($sql);
    $rs = $stmt->executeQuery(ResultSet::FETCHMODE_ASSOC);
    $cound_files = 0;
    $treeNodes = "";

    $current_node_id = 0.5; // Make sure not equal any integer Id

    $df_path_list = array($root_path);

    $rootPathLen = strlen($root_path);

    while($rs->next()) {
      $df_path = $rs->get('PATH');
      $df_name = $rs->get('NAME');
      $df_id = $rs->get('ID');

      $df_fullpath = $df_path  . "/" . $df_name;

      if( ! file_exists($df_fullpath)) continue;

      if(!in_array($df_path, $df_path_list)) {
        $path_tokens = explode("/", trim(substr($df_path,$rootPathLen), "/"));
        $current_path = $root_path;

        foreach($path_tokens as $path_token) {
          if(empty($path_token)) continue;

          $parent_path = $current_path;
          $current_path .= "/" . $path_token;

          if(isset($parentnode[$parent_path])) {
            if(!isset($parentnode[$current_path])) {
              $treeNodes .= $tree_name . ".add(" . $current_node_id . ", " . $parentnode[$parent_path] . ", \"" . $path_token . "\", \"\", ico_folder);\n";
              $parentnode[$current_path] = $current_node_id;
              $current_node_id++;
            }
          }
        }

        $df_path_list[] = $df_path;
      }

      $df_ico = $file_select ? "\"\"" : "ico_folder";

      // A safe way to avoit missing parent node.
      if(isset($parentnode[$df_path])) {
        $df_relative_path = str_replace($entity->getPathname(), "", $df_fullpath);
        $treeNodes .= "{$tree_name}.add(". $df_id . ", " . $parentnode[$df_path] . ", \"" . $df_name . "\", \"javaScript:selectFileDir('" . preg_replace('/\'/', '\\\\\\\'', $df_relative_path) . "')\", $df_ico);\n";

        $cound_files++;
      }
    }

    if($cound_files > 0) {

      $expandLink = $expand ? $tree_name . ".expandAll();" : "";
      $isCheckboxValue = $isCheckbox ? "true" : "false";

      $js_func = "";

      if(!empty($selected_field_id)) {
        $js_func = "root = '$friendlypath';";
        $js_func .= "select_field = document.getElementById('$selected_field_id');";
        $js_func .= "if(select_field) select_field.value = root + id;";
      }

      $selRow = $selectRow ? "true" : "false";

      $jstree .= <<<ENDHTML

  <script type="text/javascript">
<!--
    var $tree_name = new NlsTree("{$tree_name}Browser");
    {$tree_name}.opt.hideRoot=false;
    {$tree_name}.opt.selRow = $selRow;
    {$tree_name}.opt.check=$isCheckboxValue;
    {$tree_name}.opt.checkOnLeaf = true;
    {$tree_name}.opt.renderOnDemand = true;

    var ico_folder = "/tree_browser/img/folder.gif, /tree_browser/img/folderopen.gif";
    var ico_pc = "/tree_browser/img/pc.gif";

    function init{$tree_name}()
    {

ENDHTML;

      $jstree .= "{$tree_name}.add(1, 0, \"" . $friendlypath . "\/\", \"\", ico_pc, true);\n";
      $jstree .= $treeNodes;

      $jstree .= <<<ENDHTML

    }

    init{$tree_name}();


    function selectFileDir(id){
      $js_func
    }
// -->
  </script>

  <div class="treeExpand">
    <a href="javascript:{$tree_name}.expandAll();void(0);">Expand All</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="javascript:{$tree_name}.collapseAll();void(0);">Collapse All</a>
  </div>
  <div id="tree_browser">
    <script type="text/javascript">
<!--
    {$tree_name}.render(); $expandLink
// -->
    </script>
    <div style='padding-top:10px'></div>
  </div>

ENDHTML;

    }
    else {
      $fileTypeStr = empty($fileType) ? "data" : $fileType;

      $jstree .= "<div style='font-size:11px; padding-left:20px;'>No supported $fileTypeStr files found in this project</div>";
    }
    return $jstree;
  }


  function quoteString($s){
    return("'" . $s . "'");
  }
}
?>
