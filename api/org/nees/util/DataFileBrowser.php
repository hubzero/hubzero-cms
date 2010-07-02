<?php
/** ****************************************************************************
 * @title
 *   View Data File Browser
 *
 * @author
 *   Minh Phan
 *
 * @abstract
 *   File Browser function for Action pages
 *
 * @desc
 *   Create file browser and file management, including upload, new directory,
 *   details and other file operations
 *
 ******************************************************************************/

require_once "lib/common/browser.php";  //Functions for creating file browsers and other helper functions
require_once "lib/interface/Data.php";
require_once "lib/data/DataFile.php";

class DataFileBrowser {

  private $entity;
  private $baselink;
  private $currentLink;
  private $currentPath;
  private $currentSystemPath;
  private $basepath;
  private $friendly_basepath;
  private $rootname;
  private $canView = false;
  private $canEdit = false;
  private $canCreate = false;
  private $canDelete = false;
  private $canCurate = false;
  private $namelink;
  private $timelink;
  private $sizelink;
  private $ascending_order;
  private $path;
  private $file;
  private $floc;
  private $formId;

  /**
   * Construct for DataFileBrowser class
   *
   * @param BaseObject $entity
   * @param String $baselink
   * @param String $basepath
   * @param String $rootname
   * @return DataFileBrowser
   */
  public function __construct($entity, $baselink, $basepath, $rootname="Files:") {
    $this->entity    = $entity;

    $auth = Authorizer::getInstance();

    $action = $_REQUEST['action'];

    if($entity) {
      $this->canView   = $auth->canView($entity);
      $this->canEdit   = $auth->canEdit($entity);
      $this->canCreate = $auth->canCreate($entity);
      $this->canDelete = $auth->canDelete($entity);
    }
    else {
      $this->canView = true;

      $userid = $auth->getUserId();
      if(strpos($action, "SensorModel") || strpos($action, "EquipmentModel")) {
        $this->canEdit   = AuthorizationPeer::CanDoInAnyFacility($userid, "edit");
        $this->canCreate = AuthorizationPeer::CanDoInAnyFacility($userid, "create");
        $this->canDelete = AuthorizationPeer::CanDoInAnyFacility($userid, "delete");
      }
    }

    // second change to get the view permission
    if(!$this->canView) {
      if(
        $action == "DisplayProjectPublic" ||
        $action == "DisplayExperimentPublic" ||
        $action == "DisplaySimulationPublic")
      {
        $this->canView = true;
      }
    }

    if(!$this->canView) return "";

    $this->formId = uniqid("formId_");

    $this->canCurate = $auth->canCurate();

    $this->baselink  = $baselink;
    $this->basepath  = $basepath;
    $this->rootname  = $rootname;
    $this->friendly_basepath = rawurlencode(get_friendlyPath($basepath));

    $this->path = isset($_REQUEST['path']) ? rawurldecode($_REQUEST['path']) : "";
    if(!empty($this->path) && strpos($this->path, "/") !== 0) {
      $this->path = "/" . $this->path;
    }

    $this->file = isset($_REQUEST['file']) ? rawurldecode($_REQUEST['file']) : null;

    $this->currentLink = $this->baselink . "&basepath=" . $this->friendly_basepath . (empty($this->path) ? "" : "&path=" . $this->path);
    $this->currentPath = $this->basepath . $this->path;
    $this->currentSystemPath = get_systemPath($this->currentPath);

    $this->floc = isset($_REQUEST['floc']) ? $_REQUEST['floc'] : "ViewFileBrowser";
  }

  //**********************************************************************************************
  /**
   *
   * @desc
   *   Main UI for DataFile Browser
   *
   * @param $entity: the entity object that you are working on, so that I can check the
   *      permission, for example: Project, Experiment, Facility
   * @param $tname (optional) Trial Name for upload window if applicable
   *
   * @return
   *   html code for data file browser
   */
   //**********************************************************************************************

  function makeDataFileBrowser() {

    $formId = $this->formId;

    if(!$this->canView) return "";

    if($this->floc == "Delete") {
      $this->doDelete();
    }
    elseif($this->floc == "EditGroupMetadata") {
      $this->doEditGroupMetadata();
    }
    elseif($this->floc == "EditSingleMetadata") {
      $this->doEditSingleMetadata();
    }
    elseif($this->floc == "EditSingleMetadataAjax") {
      $this->doEditSingleMetadataAjax();
    }
    elseif($this->floc == "Mkdir") {
      $this->doMkdir();
    }
    elseif($this->floc == "Rename") {
      $this->doRename();
    }
    elseif($this->floc == "Copy") {
      return $this->printContent($this->doCopyMove("Copy"));
    }
    elseif($this->floc == "Move") {
      return $this->printContent($this->doCopyMove("Move"));
    }
    elseif($this->floc == "ViewFileManager") {
      return $this->printContent($this->get_viewFileManager(), $this->printJSFunctions());
    }
    elseif($this->floc == "ViewFileBrowser") {
      return $this->printMainContent($this->getViewFileBrowser(), $this->printJSFunctions());
    }
  }



  //**********************************************************************************************
  /**
   * @desc
   *   Main UI for DataFile Browser
   *
   * @return
   *   html code for data file browser
   */
  //**********************************************************************************************
  function getViewFileBrowser(){

    $formId = $this->formId;
    $browser_dir = $browser_file = "";
    $count_files = 0;
    $count_dirs = 0;
    $orderNum = 0;

    /**
     * **********************************************************************************
     * Directory Listing
     * **********************************************************************************
     */

    $listJS = "";

    ## draw file browser header and optional backlink
    $this->namelink = "$this->currentLink&sort=name";
    $this->timelink = "$this->currentLink&sort=time";
    $this->sizelink = "$this->currentLink&sort=size";
    $this->ascending_order = false;
    $orderby = $this->getOrderBy();


    $datafiles = DataFilePeer::findDataFileBrowser($this->currentPath, $orderby, $this->ascending_order);

    foreach($datafiles as $df) {
      /* @var $df DataFile */
      //name, directory, created, filesize
      $df_id          = $df->getId();
      $df_path        = $df->getPath();
      $df_name        = $df->getName();
      $df_created     = $this->cleantimestamp($df->getCreated());
      $df_filesize    = $df->getFilesize();
      $df_thumbid     = $df->getThumbId();

      $df_description = str_replace("\r\n", " ", htmlspecialchars($df->getDescription()));
      $df_title       = str_replace("\r\n", " ", htmlspecialchars($df->getTitle()));
      $df_authors     = str_replace("\r\n", " ", htmlspecialchars($df->getAuthors()));
      $df_emails      = str_replace("\r\n", " ", htmlspecialchars($df->getAuthorEmails()));
      $df_cite        = str_replace("\r\n", " ", htmlspecialchars($df->getHowToCite()));

      $df_fullpath = $df_path . "/" . $df_name;

      if(!file_exists($df_fullpath)) continue;

      $df_directory = is_dir($df_fullpath) ? 1 : 0;
      $df_directory ? $count_dirs++ : $count_files++;

      $orderNum++;
      $rowId = $orderNum - 1;

      $name_truncate = $this->str_truncate($df_name, 35);
      $name_encode = rawurlencode($df_name);

      $displayMetadata = "";

      if(empty($df_description) && empty($df_title) && empty($df_authors) && empty($df_emails) && empty($df_cite)) {
        $displayMetadata = " style='display: none;'";
      }

      $metadataLink = "<span id='metadataRow_$rowId' $displayMetadata><a href='javascript:void(0);' style='display: inline; cursor:default;' onmouseover=\"displayTooltip(dataFileJSList[$rowId]);\" onmouseout='UnTip();'><img src='/images/file_view.gif' width='16' height='16' style='vertical-align:middle;' alt='' /></a></span>";


      $editMetadataLink = "";
      $extraSpace = "<img src='/images/pixel.gif' width='16' height='16' id='image_loading_" . $df_id . "'/>&nbsp;&nbsp;";

      if($this->canEdit) {
        $editMetadataLink = "<a href='javascript:void(0);' onclick=\"javascript:editMetadataAjax($rowId);\"><img src='/images/file_edit.gif' width='16' height='16' style='vertical-align:middle;' alt='' onmouseover=\"Tip('Edit Metadata');\" onmouseout='UnTip()' /></a>";
      }

      if($df_directory) {
        $file_url = $this->baselink . "&basepath=" . $this->friendly_basepath . (empty($this->path) ? "&path=/" . rawurlencode($df_name) : "&path=" . rawurlencode($this->path) . "/" . rawurlencode($df_name));
        $cleansize = "";
        $checkbox = "<input type='checkbox' name='chkFolders[]' id='chkRow_" . $rowId . "_" . $formId . "' value='$df_id' onclick=\"javascript:checkSingle('$formId');\" />";
      }
      else {
        // Second chance to get the correct filesize if database value is null
        if(empty($df_filesize)) {
          $df_filesize = filesize($df_fullpath);
        }

        $cleansize = cleanSize($df_filesize);
        $file_url = $df->get_url();
        $checkbox = "<input type='checkbox' name='chkFiles[]' id='chkRow_" . $rowId . "_" . $formId . "' value='$df_id' onclick=\"javascript:checkSingle('$formId');\" />";
      }

      $mime_icon = $df->getMimeIcon();

      $canDeleteLink = $this->canDelete ? "<a href='JavaScript:delete_fileid($rowId, $df_id);' onmouseover=\"Tip('Delete this file or directory');\" onmouseout='UnTip()'><img src='/images/icons/silk/cross.png' width='16' height='16' id='delete_$df_id' style='vertical-align: middle;' alt='' /></a>" : "";

      if($this->canCurate || $this->canEdit || $this->canDelete) {
        $fileManagerLink = "<a style='display: inline;' href='$this->currentLink&file=$name_encode&floc=ViewFileManager' onmouseover=\"Tip('File Manager: File action by owner');\" onmouseout='UnTip()'><img src='/images/file_manager.gif' width='16' height='16' style='vertical-align:middle;' alt='' /></a>";
      }
      else {
        $fileManagerLink = "";
      }

      $getsizeDir = $df_directory ? "onmouseover=\"getAjaxDirInfoById(" . $rowId . ", " . $df_id . ", this);\" onmouseout='UnTip()'" : "";
      $target = $df_directory ? "" : "target='_blank'";

      $graphButton = $df->isAsciiFile() ? "<a href='javascript:graphMe($df_id);' onmouseover=\"Tip('View Graph if this is a valid format.');\" onmouseout='UnTip()'><img src='/images/file_graph.gif' width='16' height='16' style='vertical-align:middle;' alt='' /></a>" : "";

      $listing = <<<ENDHTML

      <tr bgcolor="#f7fcfd" id="rowId_$rowId">
        <td class="orderNum">$orderNum</td>
        <td>$checkbox</td>
        <td><a href="$file_url" $target><img src="$mime_icon" width="16" height="16" id="mimeIcon_$rowId" alt="" $getsizeDir/></a></td>
        <td><a href="$file_url" $target>$name_truncate</a></td>
        <td><a href="$file_url" $target>$df_created</a></td>
        <td><a href="$file_url" $target>$cleansize</a></td>
        <td style="text-align: right;">
          $extraSpace &nbsp; $graphButton &nbsp; $metadataLink &nbsp; $editMetadataLink &nbsp; $fileManagerLink &nbsp; $canDeleteLink
        </td>
      </tr>

ENDHTML;

      $df_directory ? ($browser_dir .= $listing) : ($browser_file .= $listing);
      $df_description = str_replace("\n", "", $df_description);

      $listJS .= "\ndataFileJSList[$rowId] = new dataFileJS($df_id, '" . $this->replace_single_quote($df_name) . "', $df_directory, '" . $this->replace_single_quote($df_title) . "', '" . $this->replace_single_quote($df_description) . "', '" . $this->replace_single_quote($df_authors) . "', '" . $this->replace_single_quote($df_emails) . "', '" . $this->replace_single_quote($df_cite) . "');";
    }

    $browser_file.= "<tr id='lastrow'><td colspan='7'>&nbsp;</td></tr>";

    $browser = "";
    $checkAllButton = "&nbsp;";
    $gobackRow = "";
    $downloadRow = "";
    $buttonsRow = "";

    $browser .= <<<ENDHTML

<script type="text/javascript">
<!--

var dataFileJSList = new Array();
$listJS

function displayTooltip(df) {
  var tooltip = "";
  var tt_fileTypeName = (df.directory ? "Directory: " : "File: ") + df.name;
  var tt_title = df.title == "" ? "" : "<tr><td nowrap=\'nowrap\'><b>Title:<\/b><\/td><td>" + df.title + "<\/td><\/tr>";
  var tt_description = df.description == "" ? "" : "<tr><td nowrap=\'nowrap\'><b>Description:<\/b><\/td><td>" + df.description + "<\/td><\/tr>";
  var tt_authors = df.authors == "" ? "" : "<tr><td nowrap=\'nowrap\'><b>Authors:<\/b><\/td><td>" + df.authors + "<\/td><\/tr>";
  var tt_cite = df.cite == "" ? "" : "<tr><td nowrap=\'nowrap\'><b>Cite:<\/b><\/td><td>" + df.cite + "<\/td><\/tr>";

  if(tt_title!="" || tt_description!="" || tt_authors!="" || tt_cite!="") {
    tooltip = "<table>" + tt_title + tt_description + tt_authors + tt_cite + "<\/table>";
    Tip(tooltip, WIDTH, -500, TITLE, tt_fileTypeName);
  }
}


function deleteRow(metadataRowId) {
  tr = document.getElementById(metadataRowId);
  document.getElementById('datafilebrowser').deleteRow(tr.rowIndex);
}


function applyMetadataAjax(fid, rowId) {

  var metadata_title = document.getElementById('title_' + fid).value;
  var metadata_description = document.getElementById('description_' + fid).value;
  var metadata_authors = document.getElementById('authors_' + fid).value;
  var metadata_emails = document.getElementById('emails_' + fid).value;
  var metadata_cite = document.getElementById('cite_' + fid).value;

  xmlHttp=GetXmlHttpObject();

  if (xmlHttp==null)
  {
    alert ("Browser does not support HTTP Request");
    return;
  }

  var url="$this->currentLink&fileId=" + fid + "&floc=EditSingleMetadataAjax&title=" + escape(metadata_title) + "&description=" + escape(metadata_description) + "&authors=" + escape(metadata_authors) + "&emails=" + escape(metadata_emails) + "&cite=" + escape(metadata_cite) + "&doit=ajax&sid="+Math.random();

  current_fileid = fid;
  current_rowId = rowId;

  xmlHttp.onreadystatechange=stateChangedOnEditSingleMetadataAjax;
  xmlHttp.open("GET",url,true);
  xmlHttp.send(null);
}

function stateChangedOnEditSingleMetadataAjax()
{
  loading_id = document.getElementById("image_loading_" + current_fileid);

  if (xmlHttp.readyState < 4)
  {
    if(loading_id) loading_id.src = '/tree_browser/img/loading.gif';
  }
  else if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
  {
    ret = xmlHttp.responseText;

    if (ret == current_fileid) {
      var metadata_title = document.getElementById('title_' + current_fileid).value;
      var metadata_description = document.getElementById('description_' + current_fileid).value;
      var metadata_authors = document.getElementById('authors_' + current_fileid).value;
      var metadata_emails = document.getElementById('emails_' + current_fileid).value;
      var metadata_cite = document.getElementById('cite_' + current_fileid).value;

      dfJS = dataFileJSList[current_rowId];

      dfJS.title       = metadata_title;
      dfJS.description = metadata_description;
      dfJS.authors     = metadata_authors;
      dfJS.emails      = metadata_emails;
      dfJS.cite        = metadata_cite;

      if(document.getElementById('metadata_' + current_fileid)) {
        deleteRow('metadata_' + current_fileid);
      }

      if(loading_id) loading_id.src = '/images/done.gif';

      var metadataRowId = document.getElementById("metadataRow_" + current_rowId);
      if(metadataRowId) metadataRowId.style.display='';
    }
    else {
      alert("Cannot edit metadata. the error is: " + ret);

      if(loading_id) loading_id.src = '/images/pixel.gif';
    }
  }
}


function editMetadataAjax(rowId) {

  dfJS = dataFileJSList[rowId];

  fid = dfJS.id;

  if(document.getElementById('metadata_' + fid)) {
    deleteRow('metadata_' + fid);
    return;
  }

  UnTip();
  tr = document.getElementById("rowId_" + rowId);

  var new_row = document.getElementById('datafilebrowser').insertRow(tr.rowIndex + 1);
  new_row.setAttribute('id', 'metadata_' + fid);
  var cell1 = new_row.insertCell(0);
  var cell2 = new_row.insertCell(1);
  cell1.colSpan="2";
  cell2.colSpan="5";

  tb  = "<div id='metadata_" + fid + "' style='padding-left:35px; border:1px solid #0081A0; background-color:#E6E6E6; font-size:11px;'><br\/>";
  tb += "<table cellpadding='0' cellspacing='0' width='100%' style='margin:0 0; border-left:1px solid #EEEEEE;'>";
  tb += "  <tr>";
  tb += "    <td nowrap='nowrap'><b>Title:<\/b><\/td>";
  tb += "    <td width='100%'><input type='text' name='title_" + fid + "' id='title_" + fid + "' value='" + dfJS.title + "' style='width:95%' \/><\/td>";
  tb += "  <\/tr>";
  tb += "  <tr>";
  tb += "    <td nowrap='nowrap'><b>Description:<\/b><\/td>";
  tb += "    <td width='100%'><textarea name='description_" + fid + "' id='description_" + fid + "' style='width:95%'>" + dfJS.description + "<\/textarea><\/td>";
  tb += "  <\/tr>";
  tb += "  <tr>";
  tb += "    <td nowrap='nowrap'><b>Authors:<\/b><\/td>";
  tb += "    <td width='100%'><input type='text' name='authors_" + fid + "' id='authors_" + fid + "' value='" + dfJS.authors + "' style='width:95%' \/><\/td>";
  tb += "  <\/tr>";
  tb += "  <tr>";
  tb += "    <td nowrap='nowrap'><b>Author Emails:<\/b><\/td>";
  tb += "    <td width='100%'><input type='text' name='emails_" + fid + "' id='emails_" + fid + "' value='" + dfJS.emails + "' style='width:95%' \/><\/td>";
  tb += "  <\/tr>";
  tb += "  <tr>";
  tb += "    <td nowrap='nowrap'><b>How to Cite:<\/b><\/td>";
  tb += "    <td width='100%'><input type='text' name='cite_" + fid + "' id='cite_" + fid + "' value='" + dfJS.cite + "' style='width:95%' \/><\/td>";
  tb += "  <\/tr>";
  tb += "  <tr><td colspan='2' style='text-align:right;'><br\/><a href=\"javascript:deleteRow('metadata_" + fid + "');\" class='btn mini'>Cancel<\/a>&nbsp;&nbsp;<a href='javascript:void(0);' onclick='javascript:applyMetadataAjax(" + fid + ", " + rowId + ");' class='btn mini'>Apply Metadata<\/a>&nbsp;&nbsp;&nbsp;<input type='hidden' name='floc' value='EditSingleMetadataAjax' \/><br\/><br\/><\/td><\/tr>";
  tb += "<\/table>";
  tb += "<\/div>";

  cell2.innerHTML = tb;
}



/////////////////////////////////////////////////////////////////////////
//Function to edit multiple metadata at once.
/////////////////////////////////////////////////////////////////////////
function submitMetadata(formId) {
  if( document.getElementById('Title_' + formId).disabled &&
      document.getElementById('Description_' + formId).disabled &&
      document.getElementById('Authors_' + formId).disabled &&
      document.getElementById('AuthorEmails_' + formId).disabled &&
      document.getElementById('HowToCite_' + formId).disabled) {

    alert("All metadata fields are disabled. Nothing to update now. \\n\\nPlease select at least one checkbox next to each metadata text field to enable\\nthe field that you want to update. Only enabled fields will be updated.");

    return;
  }

  this_form = document.getElementById(formId);

  this_form.target="_self";
  this_form.action="$this->currentLink&floc=EditGroupMetadata";
  this_form.submit();
}

function atLeastOneCheckBox(formId) {
  for(var i=0; i<dataFileJSList.length; i++) {
    chkbox = document.getElementById("chkRow_" + i + "_" + formId);
    if(chkbox && chkbox.checked) {
      return true;
    }
  }

  alert("Please select files or folders to edit by checking the box(es)!");

  return false;
}

// This function to edit metadata on multiple data files at once
function toggleEditMetadata(formId) {
  toggleDiv('EditMetadataID_' + formId, 'MkdirID_' + formId, 'applyMetadata_' + formId);
}


function doMultipleMove(formId) {
    myform = document.getElementById(formId);
    myform.action = '$this->currentLink&floc=Move&multiple=1';
    myform.submit();
}


function doMultipleCopy(formId) {
    myform = document.getElementById(formId);
    myform.action = '$this->currentLink&floc=Copy&multiple=1';
    myform.submit();
}


function doMultipleDelete(formId) {

    if(!confirm("Please note: This action cannot be undone. \\nAre you sure to delete all selected data files?")) return;

    myform = document.getElementById(formId);
    myform.action = '$this->currentLink&floc=Delete&multiple=1';
    myform.submit();
}



/////////////////////////////////////////////////////////////////////////
//Function to delete a data file by id
/////////////////////////////////////////////////////////////////////////

var xmlHttp;
var current_fileid = -1;
var current_rowId = -1;

function delete_fileid(rowId, id) {

  dfJS = dataFileJSList[rowId];
  if(dfJS.id != id) {
    alert("Row ID and Data file ID mismatch");
    return;
  }

  if(dfJS.directory) {
    confirmMsg = "Please note: This action cannot be undone. \\n\\n" +
               "Are you sure you want to delete this directory \"" + dfJS.name + "\" and all of its contents?";
  }
  else {
    confirmMsg = "Please note: This action cannot be undone. \\n\\n" +
               "Are you sure you want to delete this data file \"" + dfJS.name + "\"?";
  }

  if(!confirm(confirmMsg)) {
    return;
  }

  xmlHttp=GetXmlHttpObject();

  if (xmlHttp==null)
  {
    alert ("Browser does not support HTTP Request");
    return;
  }

  var url="$this->currentLink&file=" + escape(dfJS.name) + "&fileId=" + dfJS.id + "&floc=Delete&doit=ajax&sid="+Math.random();

  current_fileid = id;
  current_rowId = rowId;

  xmlHttp.onreadystatechange=stateChangedOnDeleteFile;
  xmlHttp.open("GET",url,true);
  xmlHttp.send(null);
}


function stateChangedOnDeleteFile()
{
  delete_id = document.getElementById("delete_" + current_fileid);

  if (xmlHttp.readyState < 4)
  {
    if(delete_id) delete_id.src = '/tree_browser/img/loading.gif';
  }
  else if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
  {
    ret = xmlHttp.responseText;
    row_fileid = document.getElementById("rowId_" + current_rowId);

    if(ret.indexOf("Error") >= 0) {
      alert("Cannot delete this file. The error is: " + ret);
      if(delete_id) delete_id.src = '/images/icons/silk/cross.png';
      return;
    }
    else if (ret.toString().search(/^[0-9]+$/) == 0) {
      rowIndex = row_fileid.sectionRowIndex;
      document.getElementById("datafilebrowser").deleteRow(rowIndex);

      //Close the expanded metadata if it is open
      expandRowId = document.getElementById("metadata_" + current_rowId);

      if(expandRowId) {
        expandRowId.style.display = "none";
      }
    }
    else {
      alert("Cannot delete data file. File browser has some unknown problems.! Error is: " + ret);
      if(delete_id) delete_id.src = '/images/icons/silk/cross.png';
    }
  }
}


function checkedAll(formId) {

  chkAllbox = document.getElementById("checkAll_" + formId);
  chkAllValue = chkAllbox.checked;

  for(var i=0; i<dataFileJSList.length; i++) {
    chkbox = document.getElementById("chkRow_" + i + "_" + formId);
    if(chkbox) chkbox.checked = chkAllValue;
  }
}

function checkSingle(formId) {

  chkAllbox = document.getElementById("checkAll_" + formId);
  if(!chkAllbox) return;

  for(var i=0; i<dataFileJSList.length; i++) {
    chkbox = document.getElementById("chkRow_" + i + "_" + formId);
    if(chkbox && !chkbox.checked) {
      chkAllbox.checked = false;
      return;
    }
  }
  chkAllbox.checked = true;
}

function getFileIds(formId) {

  ids = "";
  for(var i=0; i<dataFileJSList.length; i++) {
    chkbox = document.getElementById("chkRow_" + i + "_" + formId);
    if(chkbox && chkbox.checked) {
      if(chkbox.name = 'chkFiles[]') {
        ids += chkbox.value + ",";
      }
    }
  }

  return ids;
}
//-->
</script>

ENDHTML;

    $checkAllButton = $orderNum > 0 ? "<input name='CheckAll' id='checkAll_$formId' type='checkbox' value='CheckAll' onclick=\"javascript:checkedAll('$formId');\" />" : "";
    $buttonsRow = $this->getButtonsRow($count_dirs, $orderNum);


    $gobackRow = $this->getGoBackRow();

    $browser .= <<<ENDHTML

    <div class="filebrowser">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="datafilebrowser" >
      <tr style="background:url(/images/tile_button.jpg);" class="bgcolor_light">
        <td class="blabels" width="1">&nbsp;</td>
        <td class="blabels" width="1">$checkAllButton</td>
        <td class="blabels">&nbsp;</td>
        <td class="blabels"><em><a href="$this->namelink" title="Sort by Name"> Name </a></em></td>
        <td class="blabels"><em><a href="$this->timelink" title="Sort by Timestamp"> Timestamp </a></em></td>
        <td class="blabels"><em><a href="$this->sizelink" title="Sort by Size"> Size </a></em></td>
        <td class="blabels">&nbsp;</td>
      </tr>
      $gobackRow
      $browser_dir
      $browser_file
      $buttonsRow
    </table>
  </div>

ENDHTML;


    return $browser;

  }



  //**********************************************************************************************
  /**
   * @desc
   *   UI for View File Detail function
   *
   * @return
   *   html code for data file browser
   */
  //**********************************************************************************************
  function get_viewFileManager() {

    $formId = $this->formId;

    if(!$this->file) {
      header("Location: /" . $this->currentLink . "&error=" . rawurlencode("Missing parameter(s)"));
      exit;
    }

    require_once 'lib/data/curation/NCCuratedNCIDCrossRef.php';

    $fullpath = $this->currentPath . "/" . $this->file;
    $systemPath = get_systemPath($fullpath);

    if(!file_exists($systemPath)) {
      header("Location: /" . $this->currentLink . "&error=" . rawurlencode("Data file not found on disc."));
      exit;
    }

    $datafile = DataFilePeer::findByFullPath($systemPath);

    if( ! $datafile ) {
      header("Location: /" . $this->currentLink . "&error=" . rawurlencode("Data file not found in database."));
      exit;
    }

    $isdir = is_dir($systemPath);

    $dirInfo = "";

    if($isdir) {
      $dfs = DataFilePeer::findAllInDir($systemPath);

      $numDirs = 0;
      $numFiles = 0;
      $totalSize = 0;

      foreach($dfs as $df) {
        /* @var $df DataFile */
        $fullPath = $df->getFullPath();

        if(file_exists($fullPath)) {
          if(is_dir($fullPath)) {
            $numDirs++;
          }
          else {
            $numFiles++;
            $totalSize += filesize($fullPath);
          }
        }
      }

      if($numDirs == 0 && $numFiles == 0) $dirInfo ="Folder is empty";
      else {
        $cleansize = cleanSize($totalSize);

        $numFilesStr = $numFiles . " File" . ($numFiles > 1 ? "s" : "");
        $numDirsStr = $numDirs . " Folder" . ($numDirs > 1 ? "s" : "");

        $dirInfo = "Size: " . $cleansize . "&nbsp;&nbsp;-&nbsp;&nbsp;(" . $totalSize . " bytes)<br>Contains: " . $numFilesStr . ", " . $numDirsStr;
      }
    }

    $output = $this->get_metadata($datafile);
    $title = $output['Title'];
    $desc  = $output['Description'];
    $auth  = $output['Authors'];
    $authE = $output['AuthorEmails'];
    $cite  = $output['HowToCite'];

    $df_name = $this->replace_single_quote($this->file);
    $df_directory = $isdir ? "1" : "0";
    $df_title = $this->replace_single_quote($title);
    $df_description = str_replace("\r\n", " ", $this->replace_single_quote($desc));
    $df_authors = $this->replace_single_quote($auth);
    $df_emails = $this->replace_single_quote($authE);
    $df_cite = $this->replace_single_quote($cite);

    $basedetails = $this->currentLink . "&file=" . rawurlencode($this->file);

    $working_with_project = isset($_REQUEST['projid']);

    $renameButton = $this->canEdit ? "<input type='button' value='Rename' class='btn' onclick=\"javascript:renameBox('$formId');\" />" : "";
    $editButton = $this->canEdit ? "<input type='button' value='Edit Metadata' class='btn' onclick=\"javascript:toggleEditMetadata('$formId')\" />" : "";
    $moveButton = ($this->canCreate && $this->canDelete && $working_with_project) ? "<input type='button' value='Move' class='btn' onclick=\"javascript:parent.location = '" . $basedetails . "&floc=Move';\" />" : "";
    $copyButton = ($this->canCreate && $working_with_project) ? "<input type='button' value='Copy' class='btn' onclick=\"javascript:parent.location = '" . $basedetails . "&floc=Copy';\" />" : "";
    $curateEditButton = "";
    $curateViewButton = "";

    $tblSource = 'DataFile';
    $curateLnk = "javascript:return false;";

    $expid  = isset($_REQUEST['expid']) ? $_REQUEST['expid'] : null;
    $projid = isset($_REQUEST['projid']) ? $_REQUEST['projid'] : null;

    $docid  = $datafile->getId();

    $graphButton = $datafile->isAsciiFile() ? "<input type='button' value='View Graph' class='btn' onclick=\"javascript:graphMe($docid);\" />" : "";

    ## For trial data need to curate Experiment first

    $hierarOk  = false;

    if($expid && count(NCCuratedNCIDCrossRefPeer::findByCentralIdAndTableSource($expid, 'Experiment')) > 0) {
      $hierarOk = true;
    }

    if(!$expid && $projid && count(NCCuratedNCIDCrossRefPeer::findByCentralIdAndTableSource($projid, 'Project')) > 0) {
      $hierarOk = true;
    }

    if(!empty($docid) && $hierarOk){
      ## Try to fetch entry for the DataFile from CuratedNCIDCrossRef table
      $crossRef  = NCCuratedNCIDCrossRefPeer::findByCentralIdAndTableSource($docid, $tblSource);

      ## Document already curated. Enable Curation View interface.
      if(count($crossRef) > 0){
        $curatedId = $crossRef[0]->getCuratedEntityID();
        $curateViewButton = "<input type='button' value='View Curation' class='btn' onclick=\"javascript:parent.location = '/curation/run.php/CatalogListing?cmd=curate&id=$curatedId'\" />";
      }

      ## Document is not curated. Enable Curation interface.
      else {
        $curateLnk = "javascript:parent.location = '/curation/run.php/Curation?id=$docid&projid=$projid&expid=$expid&cmd=curate&table=$tblSource'";
      }
    }

    ## User with Curator Role can curate Project
    if ($this->canCurate) {
      $_SESSION['NEEScentralReturnPath'] = '/' . $basedetails;
      $curateEditButton = "<input type='button' value='Curate' class='btn' onclick=\"$curateLnk\" />";
    }

    $auth_with_email = $auth;

    ## set author email link
    if ( strpos($authE, '@') ) {
      $auth_with_email = "<a class='bluelt' href='mailto:$authE'>$auth</a>";
    }

    $mime_icon = $datafile->getMimeIcon();

    if ( $isdir ) {
      $fileLink = $this->currentLink . "/" . rawurlencode($this->file);
    }
    else {
      $fileLink = str_replace("%2F", "/", rawurlencode("/data/get$fullpath"));
    }

    $target = $isdir ? "" : "target='_blank'";
    $exhide = getexhidden( $basedetails );

    $editMetadataTable = $this->printEditMetadata($isdir, $title, $desc, $auth, $authE, $cite);

    $datafile_name = $datafile->getName();

    $fileOrDirName = $isdir ? "Folder Name" : "File Name";

    $view_file_details = <<<ENDHTML

    <!-- Start ViewFileManager -->
    <input type="hidden" name="fileId" value="$docid">

    <div class="contentpadding">

<script type="text/javascript">
<!--

var dfJS = new dataFileJS($docid, '$df_name', $df_directory, '$df_title', '$df_description', '$df_authors', '$df_emails', '$df_cite');

// This function is for editing metadata on a single data file
function toggleEditMetadata(formId) {
  toggleDiv('EditMetadataID_' + formId, 'RenameID_' + formId, 'applyMetadata_' + formId);
}


function submitMetadata(formId) {

  if( document.getElementById('Title_' + formId).disabled &&
      document.getElementById('Description_' + formId).disabled &&
      document.getElementById('Authors_' + formId).disabled &&
      document.getElementById('AuthorEmails_' + formId).disabled &&
      document.getElementById('HowToCite_' + formId).disabled) {

    alert("All metadata fields are disabled. Nothing to update now. \\n\\nPlease select at least one checkbox next to each metadata text field to enable\\nthe field that you want to update. Only enabled fields will be updated.");

    return;
  }

  this_form = document.getElementById(formId);

  this_form.action="$this->currentLink&fileId=$docid&floc=EditSingleMetadata";

  this_form.submit();
}

//-->
</script>

      <div class="info">$dirInfo</div>
      <table cellpadding="0" cellspacing="0" width="100%" style="border:#CCCCCC 1px solid;">
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
          <td nowrap="nowrap"><b>$fileOrDirName:</b></td>
          <td width="100%">
            <img src="$mime_icon" width='16' height='16' alt="" />&nbsp;&nbsp;&nbsp;<span id="fileName_$formId"><a $target href="$fileLink" class="bluelt">$datafile_name</a></span>
            <!-- Start Rename -->
            <div id="RenameID_$formId" style="padding:0; display: none">
              <br/>
              <div class="miniportlet">
                <div class="contentpadding">
                  <br/>
                  <div id="rename_error"></div>
                  New file name:<span class="orange">*</span>
                  <br/>
                  <input type="text" id="new_name_$formId" maxlength="80" class="textentry" value="" style="width:300px" />
                  <img src='/images/pixel.gif' id='rename_loading_$formId' width='16' height='16'  alt='' />
                  <input class="btn" type="button" value="Save Changes" onclick="javaScript:submitRename('$formId')" />
                  <input class="btn" type="button" value="  Cancel  " onclick="javaScript:hiddenDiv('RenameID_$formId');" />
                  <br/><br/>
                </div>
              </div>
              <br/>
            </div>
            <!-- End Rename -->

          </td>
        </tr>
        <tr>
          <td nowrap="nowrap"><b>Title:</b></td>
          <td width="100%">$title</td>
        </tr>
        <tr>
          <td nowrap="nowrap"><b>Description:</b></td>
          <td width="100%">$desc</td>
        </tr>
        <tr>
          <td nowrap="nowrap"><b>Authors:</b></td>
          <td width="100%">$auth_with_email</td>
        </tr>
        <tr>
          <td nowrap="nowrap"><b>How to Cite:</b></td>
          <td width="100%">$cite</td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
          <td colspan="2" class="sectheaderbtn">
            $graphButton
            $editButton
            $renameButton
            $copyButton
            $moveButton
            $curateEditButton
            $curateViewButton
          </td>
        </tr>
      </table>

      <!-- Start Metadata Editor -->
      <div id="EditMetadataID_$formId" style="padding:0; display: none">
        <input type="hidden" name="floc" value="EditSingleMetadata" />
        $exhide
        $editMetadataTable
      </div>
      <!-- End Metadata Editor -->

<script type="text/javascript">
<!--

function renameBox(formId) {
  toggleDiv('RenameID_' + formId, 'EditMetadataID_' + formId, 'new_name_' + formId);
}


var xmlHttp;
var rename_fileid = -1;
var rename_newname = "";
var rename_formId = "";

function submitRename(formId) {
  var newname = document.getElementById('new_name_' + formId).value.trim();

  if( newname.length == 0) {
    alert("Data file name can not be empty.");
    return false;
  }
  if( newname == dfJS.name) {
    alert("Original file name and new name are the same.");
    return false;
  }
  if( newname.indexOf('\\\\') >= 0 ||
      newname.indexOf('/') >= 0 ||
      newname.indexOf(':') >= 0 ||
      newname.indexOf('*') >= 0 ||
      newname.indexOf('?') >= 0 ||
      newname.indexOf('"') >= 0 ||
      newname.indexOf('<') >= 0 ||
      newname.indexOf('>') >= 0 ||
      newname.indexOf('|') >= 0 ) {

    alert('A file name cannot contain any of the following characters: \\\\ / : * ? " < > |');
    return false;
  }

  xmlHttp=GetXmlHttpObject();

  if (xmlHttp==null)
  {
    alert ("Browser does not support HTTP Request");
    return;
  }

  var url="$this->currentLink&file=" + escape(dfJS.name) + "&fileId=$docid&floc=Rename&new_name=" + escape(newname) + "&doit=ajax&sid="+Math.random();
  rename_fileid = $docid;
  rename_newname = newname;
  rename_formId = formId;

  xmlHttp.onreadystatechange=stateChangedOnRenameFile;
  xmlHttp.open("GET",url,true);
  xmlHttp.send(null);

}


function stateChangedOnRenameFile()
{
  if (xmlHttp.readyState < 4)
  {
    document.getElementById("rename_loading_" + rename_formId).src = '/tree_browser/img/loading.gif';
  }
  else if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
  {
    document.getElementById("rename_loading_" + rename_formId).src = "/images/pixel.gif";
    ret = xmlHttp.responseText;
    if(ret == rename_newname) {
      var url="$this->currentLink&file=" + escape(rename_newname) + "&floc=ViewFileManager";
      window.location = url;
    }
    else {
      document.getElementById('rename_error').innerHTML = "<div class='error'>" + ret + "</div>";
    }
    return;
  }
}

//-->
</script>

    </div>
    <!-- End ViewFileManager -->

ENDHTML;

    return $view_file_details;
  }


  /**
   * Get the request value
   *
   * @param String $key : Request key name
   * @param int $limitChars: Limit number of char
   * @return String Request Key Value
   */
  function getRequestValue($key) {
    $ret = preg_replace("/[\\\|]/", "", htmlspecialchars($_REQUEST[$key]) );
    $ret = trim($ret);
    if($key != "Description") $ret = substr($ret, 0, 255);
    if(strlen($ret) == 0) $ret = null;
    return $ret;
  }



  //**********************************************************************************************
  /**
   * @desc
   *   UI for Edit Metadata function
   *
   * @return
   *   html code for data file browser
   */
  //**********************************************************************************************
  function doEditSingleMetadata() {

    if(!$this->canEdit) return;

    $fileId = isset($_REQUEST['fileId']) ? $_REQUEST['fileId'] : null;

    if(!$this->file || !$fileId) {
      exit("Error: Missing Parameter(s).");
    }

    $df = DataFilePeer::find($fileId);

    if(!$df) {
      exit("Error: Data file not found in database.");
    }

    if(!file_exists($df->getFullPath())) {
      exit("Error: Data file not found on disc.");
    }

    $fullpath = $this->currentPath . "/" . $this->file;
    $systemPath = get_systemPath($fullpath);

    if($systemPath != $df->getFullPath()) {
      exit("Error: File on disc and file in database are not matched.");
    }

    $meta = array(
      "Title"        => DataFilePeer::TITLE,
      "Description"  => DataFilePeer::DESCRIPTION,
      "Authors"      => DataFilePeer::AUTHORS,
      "AuthorEmails" => DataFilePeer::AUTHOR_EMAILS,
      "HowToCite"    => DataFilePeer::HOW_TO_CITE
    );

    $update = false;
    $updateArr = array();

    foreach($meta as $key => $column) {
      if(isset($_REQUEST[$key])) {
        $updateArr[$column . " = ? "] = $this->getRequestValue($key);
        $update = true;
      }
    }

    // Nothing to update
    if(!$update) {
      exit("Error: Nothing to update");
    }

    $update_clause = implode(", ", array_keys($updateArr));

    $is_recursive = (isset($_REQUEST["recursive"]) && $_REQUEST["recursive"] == "true" && is_dir($systemPath));

    $where_params = array();

    if($is_recursive) {
      $where_clause = DataFilePeer::ID . " = " . $fileId;
      $where_clause .= " OR (" . DataFilePeer::PATH . " = ?) OR (CONCAT(" . DataFilePeer::PATH . ", '/') LIKE ?)";
      $where_params[] = $systemPath;
      $where_params[] = $systemPath . "/%";
    }
    else {
      $where_clause = DataFilePeer::ID . " = " . $fileId;
    }

    $sql = "UPDATE DATA_FILE SET " . $update_clause . " WHERE " . $where_clause;

    $conn = Propel::getConnection();
    $stmt = $conn->prepareStatement($sql);

    $i = 1;
    foreach ($updateArr as $value) {
      $stmt->setString($i, $value);
      $i++;
    }
    foreach ($where_params as $param) {
      $stmt->setString($i, $param);
      $i++;
    }

    $stmt->executeUpdate();

    header("Location: $this->currentLink&file=" . rawurlencode($this->file) . "&floc=ViewFileManager");
    exit;
  }

  //**********************************************************************************************
  /**
   * @desc
   *   UI for Edit Metadata function
   *
   * @return
   *   html code for data file browser
   */
  //**********************************************************************************************
  function doEditSingleMetadataAjax() {

    if(!$this->canEdit) return;

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

    exit($fileId);
  }


  //**********************************************************************************************
  /**
   * UI for Copy and Move function, using the Tree browser
   *
   * @param String $do: select one of two functions: Copy or Move
   *
   * @return
   *   html code for data file browser
   */
  //**********************************************************************************************
function doCopyMove($do) {

  // If we are working on facility stuff, i.e. projid not set, then just return
  if(!isset($_REQUEST['projid'])) return;

  if(!$do || ($do != "Copy" && $do != "Move")) return;

  if($do == "Copy" && !$this->canCreate) return;
  if($do == "Move" && (!$this->canCreate || !$this->canDelete)) return;

  $isMultiple = isset($_REQUEST['multiple']) && $_REQUEST['multiple'] == 1;

  $error = "";
  if(isset($_REQUEST['doit']) && $_REQUEST['doit'] == 1) {
    $dest_dir = get_systemPath($_REQUEST['dest_dir']);

    if (preg_match('/[^\'\"]+/', $dest_dir) && strpos( $dest_dir, "../") == FALSE ) {

      if($isMultiple) {
        $chkFilesList = $_REQUEST['chkFilesList'];
        $chkFoldersList = $_REQUEST['chkFoldersList'];

        $chkFiles = explode(",", $chkFilesList);
        $chkFolders = explode(",", $chkFoldersList);

        $chkIds = array_merge($chkFiles, $chkFolders);

        $chkDfs = DataFilePeer::retrieveByPKs($chkIds);

        $failed = false;

        foreach($chkDfs as $df) {
          /* @var $df DataFile */
          $filePath = $df->getFullPath();

          // Should not happen, just in case there is a hacker !!!
          if(strpos($filePath, $this->currentSystemPath) != 0) {
            continue;
          }

          if ($do == "Copy") {
            if(is_dir($filePath)) {
              $ret = $this->copyDir($filePath, $dest_dir);
            }
            else {
              $ret = $this->copyFile($filePath, $dest_dir . "/" . $df->getName());
            }
          }
          else {
            $ret = $this->moveDataFile($filePath, $dest_dir . "/" . $df->getName());
          }

          if(!$ret) $failed = true;
        }

        if(!$failed) {
          header("Location: $this->currentLink");
          exit;
        }
      }
      else {
        $filePath = get_systemPath($this->currentPath . "/" . $this->file);

        if ($do == "Copy") {
          if(is_dir($filePath)) {
            $ret = $this->copyDir($filePath, $dest_dir);
          }
          else {
            $ret = $this->copyFile($filePath, $dest_dir . "/" . $this->file);
          }
        }
        else {
          $ret = $this->moveDataFile($filePath, $dest_dir . "/" . $this->file);
        }
        if ($ret) {
          header("Location: $this->currentLink");
          exit;
        }
      }
    }

    $error = "<div class='error'>Failed to " . strtolower($do) . " your selected data file(s). Please check if the destination file already exists.</div>";
  }


  if($isMultiple) {
    $chkFiles = isset($_REQUEST['chkFiles']) ? $_REQUEST['chkFiles'] : array();
    $chkFolders = isset($_REQUEST['chkFolders']) ? $_REQUEST['chkFolders']: array();

    $fileNamesJS = "<input type='hidden' name='chkFilesList' value='" . implode(",", $chkFiles) . "'>";
    $folderNamesJS = "<input type='hidden' name='chkFoldersList' value='" . implode(",", $chkFolders) . "'>";
  }
  else {
    $fileNamesJS = "";
    $folderNamesJS = "";
  }

  $system_filePath = get_systemPath($this->currentPath . "/" . $this->file);

  $isdir = is_dir($system_filePath);

  $rootpath = $this->entity->getPathname();

  $friendlypath = get_friendlyPath($rootpath);

  $jstree = "treedf.add(1, 0, \"" . $friendlypath . "\/\", \"\", ico_pc, true);\n";

  $parentnode = array();
  $parentnode[$friendlypath] = 1;

  $friendlyCurrentPath = get_friendlyPath($this->currentPath);

  $entityTypeId = DomainEntityType::getEntityTypeId($this->entity);

  if($entityTypeId == DomainEntityType::ENTITY_TYPE_EXPERIMENT) {
    $dirs = DataFilePeer::findAllDirectoriesByPath($rootpath);
  }
  elseif($entityTypeId == DomainEntityType::ENTITY_TYPE_PROJECT) {
    $dirs = DataFilePeer::findAllDirectoriesInProjectLevel($rootpath);
  }

  $dirs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

  while($dirs->next()) {
    $df_id   = $dirs->get('ID');
    $df_name = $dirs->get('NAME');
    $df_path = $dirs->get('PATH');

    $df_friendlypath = get_friendlyPath($df_path);

    if(!empty($parentnode[$df_friendlypath])) {

      $df_fullpath = $df_friendlypath  . "/" . $df_name;

      if( ! file_exists($df_path . "/" . $df_name)) continue;

      if(strpos($df_fullpath . "/", $friendlyCurrentPath . "/" . $this->file) === 0) continue;

      $parentnode[$df_fullpath] = $df_id;

      if($df_fullpath != $friendlyCurrentPath) {
        $jstree .= "treedf.add(". $df_id . ", " . $parentnode[$df_friendlypath] . ", \"" . $df_name . "\", \"javascript:treedf.expandNode($df_id); selectDir('" . preg_replace('/\'/', '\\\\\\\'', $df_fullpath) . "')\", ico_folder);\n";
      }
      else{
        $jstree .= "treedf.add(". $df_id . ", " . $parentnode[$df_friendlypath] . ", \"" . $df_name . "\", \"javascript:treedf.expandNode($df_id); selectDir();\", ico_folder);\n";
      }
    }
  }

  $exhide = getexhidden($this->currentLink);

  $source = $this->currentPath . "/" . $this->file;

  $sourceLink = $this->get_dataFileLink();

  if(!$isMultiple) {
    $mime_icon = get_mimeIcon(get_systemPath($source), $isdir);
  }

  $submitbtn = "<input class='btn' type='submit' name='$do' value='$do' onclick='if(validate()) return subOnce(); else return false;' />";

  $copyMessage = ($do == "Copy") ? "<div style='font-size: 90%;'><em>*Copying will create a brand new copy of the data and also with the user-defined metadata.</em></div>" : "";

  $isdirStr = $isMultiple ? "selected data file(s)/directorie(s)" : ($isdir ? "Directory" : "File");
  $confirm_msg = "Please note that: Some kind of data files must be located in their designated directories. Moving them around could cause your project/experiment not working correctly. Be sure to understand this before you move/copy to a new location\\n\\n";

  if ($do == "Copy") {
    $confirm_msg .= "You are trying to copy your " . strtolower($isdirStr) . ": $this->file \\n\\tfrom this location: " . $friendlyCurrentPath . "\\n\\tto a new location: \" + myForm.dest_dir.value + \" \\n\\n\\tIf the destination file already exists, it will be overwritten. \\n\\nAre you sure to continue ?";
  }
  else {
    $confirm_msg .= "You are trying to move your " . strtolower($isdirStr) . ": $this->file \\n\\tfrom this location: " . $friendlyCurrentPath . "\\n\\tto a new location: \" + myForm.dest_dir.value + \" \\n\\n\\tData can not be moved if the destination file already exists. \\n\\tIn this case, you must delete the destination file first, if not it will return an error. \\n\\nAre you sure to continue ?";
  }

  $validate_js = <<<ENDHTML

    myForm = document.getElementById('$this->formId');

    function validate() {
      if(isEmptyDestination()) return false;
      if(isSamePaths()) return false;
      if(isRootPath()) return false;
      return confirm("$confirm_msg");
    }

    function isEmptyDestination() {
      if(myForm.dest_dir.value == "") {
        alert("Please select a destination path that you want to $do the source data file to!");
        return true;
      }
      return false;
    }

    function isSamePaths() {
      if(myForm.dest_dir.value == "$friendlyCurrentPath") {
        alert("Can not $do data file: '$this->file'. The source and destination paths are the same.");
        return true;
      }
      return false;
    }

    function isRootPath() {
      var inputdir = myForm.dest_dir.value.replace(/\//g,"");

      if(inputdir == "$friendlypath") {
        alert("Can not $do data file to the root path.");
        return true;
      }
      return false;
    }

ENDHTML;

  $actionDf = $isMultiple ? "" : " <img src='$mime_icon' width='16' height='16' alt='' > <a class='bluelt' href='$sourceLink' />$this->file</a>";

  $view_file_move = <<<ENDHTML

<div class="contentpadding">

  $exhide
  <input type="hidden" name="floc" value="$do" />
  <input type="hidden" name="doit" value="1" />
  $error
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td nowrap="nowrap" colspan="2">
        <strong>Action:</strong> $do $isdirStr $actionDf
      </td>
    </tr>
    <tr>
      <td nowrap="nowrap">Source Path:</td>
      <td width="100%">$friendlyCurrentPath</td>
    </tr>
    <tr>
      <td nowrap="nowrap">Destination Path:</td>
      <td width="100%"><input type="text" name="dest_dir" style="width:95%;" maxlength=250 readonly/></td>
    </tr>
  </table>

<!-- Start FileBrowser -->

  <script type="text/javascript">
<!--
    function selectDir(id){
      myForm.dest_dir.value = id;
    }
    $validate_js
    var treedf=new NlsTree("treedfBrowser");
    treedf.opt.renderOnDemand=true;
    treedf.opt.hideRoot=false;
    treedf.opt.selRow = true;
    //treedf.opt.oneClick=true;
    //treedf.opt.oneExp=false;

    var ico_folder = "/tree_browser/img/folder.gif, /tree_browser/img/folderopen.gif";
    var ico_pc = "/tree_browser/img/pc.gif";
//-->
  </script>

  <br/>

  <div class="miniportlet">
    <div class="treeExpand" style="border-top:none;">
      <a href="javascript:treedf.expandAll();void(0);">Expand All</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="javascript:treedf.collapseAll();void(0);">Collapse All</a>
    </div>
    <div class="contentpadding">
      <script type="text/javascript">
<!--
      function initTreedf()
      {
        $jstree
      }
      initTreedf();
//-->
      </script>
      <div id="tree_browser">
        <script type="text/javascript">treedf.render();</script>
      </div>
    </div>
  </div>

  <div class="sectheaderbtn">
    $fileNamesJS
    $folderNamesJS
    <input class="btn" type="button" value="Cancel" onclick="history.back();" />
    $submitbtn
  </div>

<!-- End FileBrowser -->

  <br/><br/>
  $copyMessage
</div>
ENDHTML;

  return $view_file_move;
}


  //**********************************************************************************************
  /**
   * @desc
   *   UI for Edit Metadata (Single file or Group of files
   *
   * @param $is_recusive: Recursively apply if there is a directory
   * @param $title: default value of Title
   * @param $desc: default value of Description
   * @param $auth: default value of Authors
   * @param $authE: default value of Author Emails
   * @param $cite: default value of How to Cite
   *
   * @return
   *   html code for data file browser
   */
  //**********************************************************************************************
  function printEditMetadata($is_recusive, $title = "", $desc = "", $auth = "", $authE = "", $cite = "") {

    $formId = $this->formId;

    $recusive_tr = $is_recusive ? "<tr><td>&nbsp;</td><td class='form' style='padding-right:0;'><input type='checkbox' name='recursive' value='true' checked='checked' /></td><td class='form' style='font-size: 90%;'><i>Apply metadata recursively to all sub-directories and files within selected directories</i></td></tr>" : "";

    $html = <<<ENDHTML

        <!-- Start Edit metadata -->
        <br/><br/>
        <div class="miniportlet">
          <div class="miniportlet_h3">
            <div class="miniportlet_title">Edit Metadata:</div>
          </div>
          <table id="form" border="0" cellspacing="0" cellpadding="0" width="100%" summary="Edit Metadata">
            <tr><td class="form" colspan="3">&nbsp;</td></tr>
            <tr>
              <td class="form" colspan="3">
                <div style='font-size: 90%;'><i><b>Note:</b> Check on the checkbox next to each metadata text field below to enable the field that you want to update. Only enabled fields will be updated.</i></div><br/><br/>
              </td>
            </tr>
            <tr>
              <td class="form" nowrap="nowrap">Title:</td>
              <td class="form" style="padding-right:0;"><input type="checkbox" onclick="javaScript:disable_field('Title_$formId');" id="CheckboxTitle_$formId" /></td>
              <td width="100%"><input type="text" disabled="disabled" style="background-color:#eeeeee; width:95%;" name="Title" id="Title_$formId" maxlength="80" class="textentry" value="$title" /></td>
            </tr>

            <tr>
              <td class="form" nowrap="nowrap">Description:</td>
              <td class="form" style="padding-right:0;"><input type="checkbox" onclick="javaScript:disable_field('Description_$formId');" id="CheckboxDescription_$formId" /></td>
              <td class="form"><textarea rows="3" disabled="disabled" style="background-color:#eeeeee; width:95%;" name="Description" id="Description_$formId" class="textentry">$desc</textarea></td>
            </tr>

            <tr>
              <td class="form" nowrap="nowrap">Authors:</td>
              <td class="form" style="padding-right:0;"><input type="checkbox" onclick="javaScript:disable_field('Authors_$formId');" id="CheckboxAuthors_$formId" /></td>
              <td class="form"><input type="text" disabled="disabled" style="background-color:#eeeeee; width:95%;" name="Authors" id="Authors_$formId" maxlength="255" class="textentry" value="$auth" /></td>
            </tr>

            <tr>
              <td class="form" nowrap="nowrap">Author Emails:</td>
              <td class="form" style="padding-right:0;"><input type="checkbox" onclick="javaScript:disable_field('AuthorEmails_$formId');" id="CheckboxAuthorEmails_$formId" /></td>
              <td class="form"><input type="text" disabled="disabled" style="background-color:#eeeeee; width:95%;" name="AuthorEmails" id="AuthorEmails_$formId" maxlength="255" class="textentry" value="$authE" /></td>
            </tr>

            <tr>
              <td class="form" nowrap="nowrap">How to Cite:</td>
              <td class="form" style="padding-right:0;"><input type="checkbox" onclick="javaScript:disable_field('HowToCite_$formId');" id="CheckboxHowToCite_$formId" /></td>
              <td class="form"><input type="text" disabled="disabled" style="background-color:#eeeeee; width:95%;" name="HowToCite" id="HowToCite_$formId" maxlength="255" class="textentry" value="$cite" /></td>
            </tr>
            $recusive_tr
            <tr>
              <td colspan="3" class="sectheaderbtn">
                <a class='button border' href="javaScript:toggleEditMetadata('$formId');">Cancel</a>
                <a id="applyMetadata_$formId" class='button border' href="javaScript:submitMetadata('$formId');">Apply Metadata</a>
              </td>
            </tr>
          </table>
        </div>


        <!-- End Edit metadata -->

ENDHTML;

    return $html;
  }


  function printCreateNewDir() {

    $formId = $this->formId;

    $newDirDiv = <<<ENDHTML

    <div  class="contentpadding">

    <!-- Start Mkdir -->



<script type="text/javascript">
<!--
function mkdirBox(formId) {
  toggleDiv('MkdirID_' + formId, 'EditMetadataID_' + formId, 'submit_mkdir_' + formId);

  mkdirID_box = document.getElementById('MkdirID_' + formId);
  if(mkdirID_box.style.display == "") {
    mkdir_Name_box = document.getElementById('mkdir_Name_' + formId);
    if(mkdir_Name_box) mkdir_Name_box.focus();
  }
}

var xmlHttp;
var mk_newname = "";
var mk_formId = "";
var mk_title = "";
var mk_desc = "";
var mk_authors = "";
var mk_emails = "";
var mk_cite = "";

function validateMkdir(formId) {
  var newDirName = document.getElementById('mkdir_Name_' + formId).value.trim();

  if( newDirName.length == 0) {
    alert("Directory name can not be empty.");
    return false;
  }
  if( newDirName.indexOf('\\\\') >= 0 ||
      newDirName.indexOf('/') >= 0 ||
      newDirName.indexOf(':') >= 0 ||
      newDirName.indexOf('*') >= 0 ||
      newDirName.indexOf('?') >= 0 ||
      newDirName.indexOf('"') >= 0 ||
      newDirName.indexOf('<') >= 0 ||
      newDirName.indexOf('>') >= 0 ||
      newDirName.indexOf('|') >= 0 ) {

    alert('A directory name cannot contain any of the following characters: \\\\ / : * ? " < > |');
    return false;
  }

  xmlHttp=GetXmlHttpObject();

  if (xmlHttp==null)
  {
    alert ("Browser does not support HTTP Request");
    return;
  }

  mk_title   = escape(document.getElementById('mkdir_Title').value.trim());
  mk_desc    = escape(document.getElementById('mkdir_Description').value.trim());
  mk_authors = escape(document.getElementById('mkdir_Authors').value.trim());
  mk_emails  = escape(document.getElementById('mkdir_AuthorEmails').value.trim());
  mk_cite    = escape(document.getElementById('mkdir_HowToCite').value.trim());
  mk_formId  = formId;

  var url="$this->currentLink&newdir=" + escape(newDirName) + "&title=" + mk_title + "&desc=" + mk_desc + "&authors=" + mk_authors + "&emails=" + mk_emails + "&cite=" + mk_cite + "&floc=Mkdir&doit=ajax&sid="+Math.random();

  mk_newname = newDirName;

  document.getElementById('mkdir_error').innerHTML = "";

  xmlHttp.onreadystatechange=stateChangedOnMkdir;
  xmlHttp.open("GET",url,true);
  xmlHttp.send(null);
}

function stateChangedOnMkdir()
{
  img_mkdir_loading = document.getElementById("mkdir_loading");
  if (xmlHttp.readyState < 4)
  {
    img_mkdir_loading.src = '/tree_browser/img/loading.gif';
  }
  else if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
  {
    ret = xmlHttp.responseText;

    if(ret.indexOf("Error:") < 0) {
      var new_dir_id = parseInt(ret);

      newRowId = dataFileJSList.length;
      newOrderNum = newRowId + 1;

      dataFileJSList[newRowId] = new dataFileJS(new_dir_id, unescape(mk_newname), 1, unescape(mk_title), unescape(mk_desc), unescape(mk_authors), unescape(mk_emails), unescape(mk_cite));

      last_row = document.getElementById("lastrow");
      last_row_index = last_row.sectionRowIndex;

      var new_dir_row = document.getElementById('datafilebrowser').insertRow(last_row_index);
      new_dir_row.id = "rowId_" + newRowId;
      new_dir_row.style.backgroundColor = "#f7fcfd";

      var cell_1 = new_dir_row.insertCell(0);
      var cell_2 = new_dir_row.insertCell(1);
      var cell_3 = new_dir_row.insertCell(2);
      var cell_4 = new_dir_row.insertCell(3);
      var cell_5 = new_dir_row.insertCell(4);
      var cell_6 = new_dir_row.insertCell(5);
      var cell_7 = new_dir_row.insertCell(6);

      var dir_link = "$this->currentLink/" + escape(mk_newname);

      var fileManagerLink = "<a style='display: inline;' href='$this->currentLink&file=" + escape(mk_newname) + "&floc=ViewFileManager' title='File manager: File action by ownner'><img src='/images/file_manager.gif' width='16' height='16' style='vertical-align:middle;' alt='' onmouseover=\"Tip('File Manager');\" onmouseout='UnTip()' /><\/a>";

      var canDeleteLink = "<a href='JavaScript:delete_fileid(" + newRowId + "," + new_dir_id + ");' onmouseover=\"Tip('Delete this file or directory');\" onmouseout='UnTip()' ><img src='\/images\/icons\/silk\/cross.png' width='16' height='16' id='delete_" + new_dir_id + "' style='vertical-align: middle;' alt='' \/><\/a>";

      var editMetadataLink = "<a href='javascript:void(0);' onclick='javascript:editMetadataAjax(" + newRowId + ");' onmouseover=\"Tip('Edit Metadata');\" onmouseout='UnTip()' ><img src='\/images\/file_edit.gif' width='16' height='16' style='vertical-align:middle;' alt='' \/><\/a>";

      var metadataLink = "<span id='metadataRow_" + newRowId + "'><a href='javascript:void(0);' style='display: inline; cursor:default;' onmouseover=\"displayTooltip(dataFileJSList[" + newRowId + "]);\" onmouseout='UnTip();'><img src='\/images\/file_view.gif' width='16' height='16' style='vertical-align:middle;' alt='' /><\/a><\/span>";

      cell_1.innerHTML = "" + newOrderNum;
      cell_1.className = "orderNum";
      cell_2.innerHTML = "<input type='checkbox' name='chkFolders[]' id='chkRow_" + newRowId + "_" + mk_formId + "' value='" + new_dir_id + "' onclick=\"javascript:checkSingle('" + mk_formId + "');\" \/>";
      cell_3.innerHTML = "<a href='" + dir_link + "'><img src='\/images\/icons\/folder_empty.gif' width='16' height='16' alt='' onmouseover=\"Tip('Folder is empty', FIX, [this, 0, 5]);\" onmouseout='UnTip()' /><\/a>";
      cell_4.innerHTML = "<a href='" + dir_link + "'><strong>" + mk_newname + "<\/strong><\/a>";
      cell_5.innerHTML = "&nbsp;";
      cell_6.innerHTML = "&nbsp;";
      cell_7.innerHTML = "<img src='\/images\/done.gif' style='vertical-align: middle;' width='16' height='16' id='image_loading_" + new_dir_id + "' alt=''\/>&nbsp;&nbsp;" + metadataLink + "&nbsp;&nbsp;&nbsp;" + editMetadataLink + "&nbsp;&nbsp;&nbsp;" + fileManagerLink + "&nbsp;&nbsp;&nbsp;" + canDeleteLink;
      cell_7.style.textAlign = "right";

      img_mkdir_loading.src = '\/images\/done.gif';
      hiddenDiv('MkdirID_$formId');
    }
    else {
      document.getElementById('mkdir_error').innerHTML = "<div class='error'>" + ret + "<\/div>";
      document.getElementById("mkdir_loading").src = '\/images\/pixel.gif';
    }
    return;
  }
}
//-->
</script>

      <br/><br/>
      <div class="miniportlet">
        <div class="miniportlet_h3">
          <div class="miniportlet_title">Make New Directory:</div>
        </div>
        <div class="contentpadding">
          <table cellpadding="0" cellspacing="0" width="100%" style="border:#CCCCCC 1px solid;">
            <tr><td colspan="2"><div id="mkdir_error">&nbsp;</div></td></tr>
            <tr>
              <td nowrap="nowrap">New directory name:<span class="orange">*</span></td>
              <td><input type="text" maxlength="80" id="mkdir_Name_$formId" value="" style="width:300px;" /></td>
            </tr>

            <tr>
              <td nowrap="nowrap">Title:</td>
              <td width="100%"><input type="text" style="width:95%;" id="mkdir_Title" maxlength="80" class="textentry" value="" /></td>
            </tr>

            <tr>
              <td nowrap="nowrap">Description:</td>
              <td><textarea rows="3" style="width:95%;" id="mkdir_Description" class="textentry"></textarea></td>
            </tr>

            <tr>
              <td nowrap="nowrap">Authors:</td>
              <td ><input type="text" style="width:95%;" id="mkdir_Authors" maxlength="255" class="textentry" value="" /></td>
            </tr>

            <tr>
              <td nowrap="nowrap">Author Emails:</td>
              <td ><input type="text" style="width:95%;" id="mkdir_AuthorEmails" maxlength="255" class="textentry" value="" /></td>
            </tr>

            <tr>
              <td nowrap="nowrap">How to Cite:</td>
              <td ><input type="text" style="width:95%;" id="mkdir_HowToCite" maxlength="255" class="textentry" value="" /></td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr>
              <td colspan="2" class="sectheaderbtn">
                <a class='button' href="javaScript:hiddenDiv('MkdirID_$formId');">Cancel</a>
                <a class='button' id='submit_mkdir_$formId' href="javaScript:void(0);" onclick="javascript:validateMkdir('$formId');">Make Directory</a>
                <img src='/images/pixel.gif' id='mkdir_loading' width='16' height='16' alt=''  />
              </td>
            </tr>

          </table>

          <br/><br/>
        </div>
      </div>
      <!-- End Mkdir -->
    </div>


ENDHTML;

    return $newDirDiv;
  }


  //**********************************************************************************************
  /**
   * @desc
   *   Recursively delete a directory and its sub directories
   *   (Not yet delete from database !!!)
   *
   * @param $dirname: directory to be deleted
   *
   * @return
   *   true if succeeds.
   *   false if fails.
   *
   */
  //**********************************************************************************************
  function  delete_directory($dirname) {
    if (is_dir($dirname)) {
      $dir_handle = opendir($dirname);
    }

    if  (!$dir_handle) return  false;

    while($this->file = readdir($dir_handle)) {
      if  ($this->file == "." || $this->file == "..") continue;

      if  (!is_dir($dirname."/".$this->file)) {
        unlink($dirname."/".$this->file);
      }
      else {
        $this->delete_directory($dirname.'/'.$this->file);
      }
    }
    closedir($dir_handle);
    rmdir($dirname);
    return  true;
  }



  //**********************************************************************************************
  /**
   * @desc
   *   replace a single quote to escape quotes, to make javascript happy
   *
   * @param $string: string to replaced
   *
   * @return
   *   String after replace.
   */
  //**********************************************************************************************
  function replace_single_quote($string) {
    return preg_replace('/\'/', '\\\'', $string);
  }


  //**********************************************************************************************
  /**
   * @desc
   *   replace a single quote to escape quotes, to make javascript happy
   *
   * @param $string: string to replaced
   *
   * @return
   *   String after replace.
   */
  //**********************************************************************************************
  function double_single_quote($string) {
    return preg_replace('/\'/', '\'\'', $string);
  }


  //**********************************************************************************************
  /**
   * @desc
   *   Get metadata for a data file from database
   *
   * @param $datafile: a DataFile object for that data file
   *
   * @return
   *   Array of metadata
   */
  //**********************************************************************************************
  function get_metadata($datafile) {

    $output = array();
    if(!$datafile) return $output;

    $ok_metas = array('Title', 'Description', 'Authors', 'AuthorEmails', 'HowToCite');

    foreach( $ok_metas as $key ) {
      $get = 'get' . $key;
      $output[$key] = $datafile->$get();
    }
    return $output;
  }



  //**********************************************************************************************
  /**
   * @desc
   *   Check if a data file name is valid or not
   *
   *   Limit by Windows system, a file name cannot have:
   *      Backslash "\",
   *      Forward slash "/",
   *      colon ":",
   *      question-mark "?",
   *      double quote ",
   *      lest than "<",
   *      greater than ">",
   *      pipe "|",
   *      star "*"
   *
   *   and also,
   *      A file name cannot have a trailling dot
   *      A file name cannot have a leading dot
   *      A file name cannot be blank
   *
   * @param String $filename
   * @return: NULL (empty) if passed
   *          error message for that error
   */
  //**********************************************************************************************
  function validateErrorFileName($filename) {

    $error = "";

    $pattern = "/[\\\\\/:?\"<>\|\*]/";
    if (preg_match($pattern, $filename) > 0)
    {
      $error = "File name cannot contain any of the following characters: \ / : * ? \" < > |" ;
    } elseif (strlen($filename) == 0) {
      $error = "File name cannot be blank.";
    } elseif ($filename[0] == '.') {
      $error = "File name cannot be started with a dot. (leading dot)";
    } elseif ($filename[strlen($filename)-1] == '.') {
      $error = "File name cannot be ended with a dot (trailling dot).";
    }
    return $error;
  }



  //**********************************************************************************************
  /**
   * @desc
   *   Get URL file link for a data file or directory
   *
   * @return
   *   If data file is a directory, then return the new URL address, based on current
   *     baselink, basepath, $this->path
   *   If data file is a file, return the web service call: /data/get/...
   */
  //**********************************************************************************************
  function get_dataFileLink() {
    $datafile = $this->currentPath . "/" . $this->file;

    if( is_dir(get_systemPath($datafile)) ) {
      return $this->currentLink . "/" . $this->file;
    }
    return "/data/get" . get_friendlyPath($this->currentPath) . "/" . $this->file;
  }


  //**********************************************************************************************
  /**
   * @desc
   *   Recursive copy a directory from source path to destination path then update
   *   to database.
   *   If the directory does not exist, it will create a new dir,
   *
   *
   * @param $source: source directory to copy from (/nees/home/NEES-2005-0001.groups/dir1/dir_name
   * @param $targetDir: target directory to copy to  (/nees/home/NEES-2005-0001.groups/dir2/ )
   *
   * @return
   *   true if succeeds.
   *   false if fails.
   */
  //**********************************************************************************************
  function copyDir($source, $targetDir ) {

    if(!is_dir($source)) return false; // $source must be a directory
    if(!is_dir($targetDir)) return false; // $targetDir also must be a directory

    $newDirPath = $targetDir . "/" . basename($source);

    if ( ! file_exists( $newDirPath )) {
      if (! mkdir($newDirPath,0755,true)) {
        return false; // Error: Can not make dir, may be the permission ???
      }
    }

    DataFilePeer::copyDataFileDB($source, $newDirPath, false);

    if ($hDir = opendir($source) ) {
      while (($afile = readdir($hDir)) !== false) {
        if (($afile != '.') && ($afile != '..')) {
          $fullPath = $source."/".$afile;

          if (is_dir($fullPath) ) {
            // Recursive call... copy the subdirectory
            $this->copyDir( $fullPath, $newDirPath );
          }
          else {  // This is a file
            $this->copyFile($fullPath, $newDirPath."/".$afile);
          }
        }
      }

      closedir($hDir);
      return true;
    }

    return false;
  }


  //**********************************************************************************************
  /**
   * @desc
   *   Copy a file (not directory) from source path to destination path then update
   *   to database.
   *   By php copy function, it will overwite a duplicare file if the target exist
   *    without any confim message.
   *
   *
   * @param $source: from path (/nees/home/NEES-2005-0001.groups/dir1/filename
   * @param $target: to path   (/nees/home/NEES-2005-0001.groups/dir2/another_filename
   *
   * @return
   *   true if succeeds.
   *   false if fails.
   */
  //**********************************************************************************************
  function copyFile($source, $target ) {
    return DataFilePeer::copyDataFileDB($source, $target, true);
  }


  //**********************************************************************************************
  /**
   * @desc
   *   Move data file or directory from source path to destination path,
   *   By php rename function, it will fail if there is a duplicare file
   *   (i.e $target does exist)
   *
   *
   * @param $source: from path (/nees/home/NEES-2005-0001.groups/dir1/filename
   * @param $target: to path   (/nees/home/NEES-2005-0001.groups/dir2/another_filename
   *
   * @return
   *   true if succeeds,
   *   false if fails (permission error, target does exist,...)
   */
  //**********************************************************************************************
  function moveDataFile($source, $target ) {
    // Can not move a file if the destination does exist.
    if(file_exists($target)) {
      return false;
    }

    // Invalid source
    if(!file_exists($source)) {
      return false;
    }

    $is_recusive = is_dir($source);

    if(rename($source, $target)) {
      return DataFilePeer::renameDB($source, $target, $is_recusive);
    }
    return false;

  }


  //**********************************************************************************************
  /**
   * @desc
   *   Convert timestamp from database format ( yyyy-mm-dd hh:mi:ss ) to US
   *   timestamp format ( mm/dd/yyyy hh:mi )
   *
   *
   * @param $timestamp: database timestamp format ( yyyy-mm-dd hh:mi:ss )
   *
   * @return
   *   US timestamp format ( mm/dd/yyyy hh:mi )
   */
   //**********************************************************************************************

  function cleantimestamp($timestamp) {
    if(strlen ($timestamp) != 19) return $timestamp; // Safety return if unknown timestamp format.
    return substr($timestamp, 5, 2) . '/' . substr($timestamp, 8, 2) . '/' . substr($timestamp, 0,4) . ' ' . substr($timestamp, 11, 5);
  }


  //**********************************************************************************************
  /**
   * @desc
   *   Truncate a string if its length exceed the max length
   *
   * @param $str: The string to be truncated
   * @param $max_char: the max length allowed
   *
   * @return
   *   If length < max length, return its its original string
   *   Else, cut it into three parts, then return the concat string of first and last part
   */
  //**********************************************************************************************
  function str_truncate($str, $max_char) {

    if(strlen($str) > $max_char) {
      $part1 = substr($str, 0, $max_char - 10);
      $part2 = substr($str, -6);
      return $part1."...".$part2;
    }
    return $str;
  }


  /**
   * Get OrderBy
   *
   * @return String $orderBy
   */
  function getOrderBy() {
    $orderby = DataFilePeer::NAME;

    if(isset($_REQUEST['sort'])) {
      if ( $_REQUEST['sort'] == "size" ) {
        if (!isset($_REQUEST['sortreverse']) ) {
          $this->sizelink .= "&sortreverse=1";
          $this->ascending_order = true;
        }
        $orderby = DataFilePeer::FILESIZE;
      }
      elseif ( $_REQUEST['sort'] == "time" ) {
        if (!isset($_REQUEST['sortreverse']) ) {
          $this->timelink .= "&sortreverse=1";
          $this->ascending_order = true;
        }
        $orderby = DataFilePeer::CREATED;
      }
      else {
        if (!isset($_REQUEST['sortreverse']) ) {
          $this->namelink .= "&sortreverse=1";
          $this->ascending_order = true;
        }
        $orderby = DataFilePeer::NAME;
      }
    }
    return $orderby;
  }


  function getCurrentPathBar() {

    $baseURL = $this->baselink . "&basepath=" . rawurlencode($this->friendly_basepath);
    $current_path_header = "<div class='bpath'>Current Path: <a href='$baseURL' title='Go to: " . rawurldecode($this->friendly_basepath) . "'>" . rawurldecode($this->friendly_basepath) . "</a>";

    $subdirs = explode("/", $this->path);

    $subPaths = "";
    foreach ($subdirs as $subdir) {
      if(empty($subdir)) continue;
      $subPaths .= "/$subdir";
      $current_path_header .= "/<a href='$baseURL&path=" . rawurlencode($subPaths) . "' title='Go to: " . rawurldecode($this->friendly_basepath) . rawurldecode($subPaths) . "'>$subdir</a>";
    }

    $current_path_header .= "</div>";

    return $current_path_header;
  }


  function getGoBackRow() {

    if (empty($this->path)) return "";

    $newpath = dirname($this->path);

    if ($newpath == '/') {
      $newpathLink = "";
    }
    else {
      $newpathLink = "&path=" . rawurlencode($newpath);
    }

    $href = $this->baselink . "&basepath=" . $this->basepath . $newpathLink;

    $goBackRow = <<<ENDHTML

      <tr>
        <td nowrap="nowrap" colspan="7"><a href='$href'><img src='/images/icons/folder.gif' width='16' height='16' alt='' /> .. go back</a></td>
      </tr>

ENDHTML;

    return $goBackRow;
  }


  function getButtonsRow($count_dirs, $orderNum) {

    $formId = $this->formId;
    $basepath = $this->replace_single_quote($this->basepath);
    $working_with_project = isset($_REQUEST['projid']);

    if($orderNum > 0) {
      if($this->canEdit) {
        $is_recusive = $count_dirs > 0;
        $editMetadataTable = $this->printEditMetadata($is_recusive);
        $editMetadataButton = "<a title='Edit metadata on all selected data files and directories recusively' class='button mini' href=\"javascript:toggleEditMetadata('$formId');\" onclick=\"return atLeastOneCheckBox('$formId');\">Edit Metadata on Selected Files</a>&nbsp;&nbsp;";
      }
      else {
        $editMetadataTable = "";
        $editMetadataButton = "";
      }

      if($this->canCreate && $this->canDelete && $working_with_project) {
        $moveButton = "<a title='Move selected data files and directories recusively to a new location' class='button mini' href=\"javascript:doMultipleMove('$formId')\" onClick=\"return atLeastOneCheckBox('$formId')\">Move Selected Files</a>&nbsp;&nbsp;";
      }
      else {
        $moveButton = "";
      }


      if($this->canCreate && $working_with_project) {
        $createDirTable = $this->printCreateNewDir();
        $copyButton = "<a title='Copy selected data files and directories recusively to a new location' class='button mini' href=\"javascript:doMultipleCopy('$formId')\" onClick=\"return atLeastOneCheckBox('$formId')\">Copy Selected Files</a>&nbsp;&nbsp;";
      }
      else {
        $createDirTable = "";
        $copyButton = "";
      }

      if($this->canDelete) {
        $deleteButton = "<a title='Delete selected data files and directories recusively' class='button mini' href=\"javascript:doMultipleDelete('$formId')\" onClick=\"return atLeastOneCheckBox('$formId');\">Delete Selected Files</a>&nbsp;&nbsp;";
      }
      else {
        $deleteButton = "";
      }

      $downloadButton = "<a href=\"javascript:download('$formId')\" onclick=\"return atLeastOneCheckBox('$formId');\" title='Download all selected data files and directories recusively' class='button mini'>Download</a>&nbsp;&nbsp;";

      $buttonRow = <<<ENDHTML

    <tr>
      <td colspan="7">
        <script type="text/javascript">
        <!--
        function download(formId) {
          window.open('', 'DownloadWindow', 'left=0,top=0,width=550,height=530,toolbar=no,menubar=no,status=no,scrollbars=yes,resizable=no');

          this_form = document.getElementById(formId);
          this_form.target="DownloadWindow";
          this_form.action="/common/newDownload.php";
          this_form.submit();
        }
        //-->
        </script>
        $downloadButton
        $editMetadataButton
        $moveButton
        $copyButton
        $deleteButton
        <input type='hidden' id='fileNames' name='fileNames' />
        <input type='hidden' id='fileSizes' name='fileSizes' />
        <input type='hidden' id='folders' name='folders' />
        <br/><br/>
      </td>
    </tr>
    <tr>
      <td colspan="7">
        <div id="EditMetadataID_$formId" style="padding:0; display: none">
           $editMetadataTable
        </div>
        <div id="MkdirID_$formId" style="padding:0; display: none">
           $createDirTable
        </div>
      </td>
    </tr>

ENDHTML;

    }
    else {
      $createDirTable = ($this->canCreate && $working_with_project) ? $this->printCreateNewDir() : "";
      $buttonRow = <<<ENDHTML

    <tr>
      <td colspan="7">
        <div id="MkdirID_$formId" style="padding:0; display: none">
           $createDirTable
        </div>
      </td>
    </tr>

ENDHTML;

    }

    return $buttonRow;
  }

  function doDelete() {

    if( ! $this->canDelete ) {
      exit("Error: You do not have permission to delete data file in this section.");
    }

    $isMultiple = isset($_REQUEST['multiple']) && $_REQUEST['multiple'] == 1;

    if($isMultiple) {
      $chkFiles = isset($_REQUEST['chkFiles']) ? $_REQUEST['chkFiles'] : array();
      $chkFolders = isset($_REQUEST['chkFolders']) ? $_REQUEST['chkFolders']: array();

      $chkIds = array_merge($chkFiles, $chkFolders);
      $chkDfs = DataFilePeer::retrieveByPKs($chkIds);

      foreach ($chkDfs as $df) {
        $filePath = $df->getFullPath();

        // Should not happen, just in case there is a hacker !!!
        if(strpos($filePath, $this->currentSystemPath) != 0) {
          continue;
        }

        if(is_dir($filePath)) {
          if($this->delete_directory($filePath)) {
            DataFilePeer::deleteAllIncludedDir($filePath);
          }
        }
        else {
          if(unlink($filePath)) {
            DataFilePeer::deleteSingleFileWithAbsolutePath($filePath);
          }
        }
      }

      header("Location: " . $this->currentLink);

    }
    else {
      $fileId =   isset($_REQUEST['fileId']) ? $_REQUEST['fileId'] : null;
      $doit =     isset($_REQUEST['doit'])   ? $_REQUEST['doit']   : null;

      if(!$this->file || !$fileId || $doit != 'ajax') {
        exit("Error: Missing parameter(s)");
      }

      $df = DataFilePeer::find($fileId);

      if(!$df) {
        exit("Error: data file not dound in database.");
      }

      $systemPath = get_systemPath($this->basepath . $this->path . "/" . $this->file);

      if($df->getFullPath() != $systemPath) {
        exit("Error: data file not found in database.");
      }

      ob_clean();

      if(is_dir($systemPath)) {
        if($this->delete_directory($systemPath)) {
          DataFilePeer::deleteAllIncludedDir($systemPath);
          exit($fileId);
        }
      }
      else {
        if(unlink($systemPath)) {
          DataFilePeer::deleteSingleFileWithAbsolutePath($systemPath);
          exit($fileId);
        }
      }
    }

    exit("Error: Failed to delete data file: $this->file.");

  }


  function doEditGroupMetadata() {
    if( ! $this->canEdit ) {
      exit("Error: You do not have permission to edit data files's  metadata in this section.");
    }

    try {
      $chkFiles = isset($_REQUEST['chkFiles']) ? $_REQUEST['chkFiles'] : array();
      $chkFolders = isset($_REQUEST['chkFolders']) ? $_REQUEST['chkFolders']: array();

      // No file or directory selected
      if(count($chkFiles) + count($chkFolders) == 0) {
        exit("Error: No data files was selected");
      }

      $meta = array(
        "Title"        => DataFilePeer::TITLE,
        "Description"  => DataFilePeer::DESCRIPTION,
        "Authors"      => DataFilePeer::AUTHORS,
        "AuthorEmails" => DataFilePeer::AUTHOR_EMAILS,
        "HowToCite"    => DataFilePeer::HOW_TO_CITE
      );

      $update = false;
      $updateArr = array();

      foreach($meta as $key => $column) {
        if(isset($_REQUEST[$key])) {
          $updateArr[$column . " = ? "] = $this->getRequestValue($key);
          $update = true;
        }
      }
      // Nothing to update
      if(!$update) {
        exit("Error: No updated metadata was found");
      }

      $update_clause = implode(", ", array_keys($updateArr));

      $conn = Propel::getConnection();

      $chkAll = array_merge($chkFiles, $chkFolders);
      $where_clause = DataFilePeer::ID . " IN (" . implode(",", $chkAll) . ")";

      $selectArr = array();
      $where_params = array();

      if(isset($_REQUEST["recursive"]) && $_REQUEST["recursive"] == "true" && count($chkFolders) > 0) {

        $stmt = $conn->createStatement();
        $select_sql = "SELECT PATH, NAME FROM DATA_FILE WHERE ID IN (" . implode(",", $chkFolders) . ") AND DIRECTORY = 1";

        $rs = $stmt->executeQuery($select_sql, ResultSet::FETCHMODE_ASSOC);

        while($rs->next()) {
          $selectArr[] = "(CONCAT(" . DataFilePeer::PATH . ", CONCAT('/', CONCAT(" . DataFilePeer::NAME . ",'/'))) LIKE ?)";
          $where_params[] = $rs->get('PATH') . "/" . $rs->get('NAME') . "/%";
        }

        $where_clause .= " OR " . implode(" OR ", $selectArr);
      }

      $sql = "UPDATE DATA_FILE SET " . $update_clause . " WHERE " . $where_clause;

      $stmt = $conn->prepareStatement($sql);

      $i = 1;
      foreach ($updateArr as $value) {
        $stmt->setString($i, $value);
        $i++;
      }
      foreach ($where_params as $param) {
        $stmt->setString($i, $param);
        $i++;
      }
      $stmt->executeUpdate();

      header("Location: " . $this->currentLink);
    }
    catch (Exception $e) {
      exit("Error: Cannot update data files's metadata. The error is: " . $e->getMessage());
    }
  }


  function doMkdir() {
    if( ! $this->canCreate ) {
      exit("Error: You do not have permission to make a new directory in this location.");
    }

    $newdir = isset($_REQUEST['newdir']) ? trim($_REQUEST['newdir']) : null;
    $doit =   isset($_REQUEST['doit'])   ? $_REQUEST['doit']   : null;

    $newdir = trim($newdir, ".");

    if(!$newdir || $doit != 'ajax') {
      exit("Error: Missing parameter");
    }
    $alert = $this->validateErrorFileName($newdir);

    if (!empty($alert)) {
      exit("Error: $alert");
    }

    $title   = isset($_REQUEST['title'])  ? trim($_REQUEST['title'])   : null;
    $desc    = isset($_REQUEST['desc'])   ? trim($_REQUEST['desc'])    : null;
    $authors = isset($_REQUEST['authors'])? trim($_REQUEST['authors']) : null;
    $emails  = isset($_REQUEST['emails']) ? trim($_REQUEST['emails'])  : null;
    $cite    = isset($_REQUEST['cite'])   ? trim($_REQUEST['cite'])    : null;

    $new_path = $this->currentPath . "/" . $newdir;

    try {
      if(mkdir($new_path, 0770, true)) {

        $paths = explode("/", $new_path);
        $pathDB = "";

        for ($i = 0; $i < count($paths) - 1; $i++) {
          if(empty($paths[$i])) continue;
          $pathDB .= "/" . $paths[$i];
          $nameDB = $paths[$i + 1];

          if($pathDB == "/nees") continue;

          if(is_null(DataFilePeer::findOneMatch($nameDB, $pathDB))) {
            $newfile = new DataFile($nameDB, $pathDB, date('Y-m-d H:i:s'), 1);
            $newfile->save();
          }
        }

        // Done make new dir, let check and return javascript add new row for ajax
        $new_dir_df = DataFilePeer::findByFullPath($new_path);

        if(!$new_dir_df) {
          exit("Error: cannot make new directory.");
        }

        if($title) $new_dir_df->setTitle($title);
        if($desc) $new_dir_df->setDescription($desc);
        if($authors) $new_dir_df->setAuthors($authors);
        if($emails) $new_dir_df->setAuthorEmails($emails);
        if($cite) $new_dir_df->setHowToCite($cite);

        if($title || $desc || $authors || $emails || $cite) {
          $new_dir_df->save();
        }

        $new_dir_id = $new_dir_df->getId();
        exit($new_dir_id);
      }
    }
    catch (Exception $e) {
      //exit("Error: " . $e->getMessage());
    }

    exit("Error: Unable to make a new directory. Please retry, make sure there is no duplicate name.");
  }


  function doRename() {

    if( ! $this->canEdit ) {
      exit("Error: You do not have permission to rename this file.");
    }

    $fileId = isset($_REQUEST["fileId"]) ? $_REQUEST["fileId"] : null;
    $new_name = isset($_REQUEST["new_name"]) ? trim($_REQUEST["new_name"]) : null;
    $doit = isset($_REQUEST['doit']) ? $_REQUEST['doit'] : null;

    if(!$this->file || !$fileId || !$new_name || $doit != 'ajax') {
      exit("Error: Missing parameter(s)");
    }
    $alert = $this->validateErrorFileName($new_name);

    if (!empty($alert)) {
      exit("Error: $alert");
    }
    if ($this->file == $new_name ) {
      exit("Error: source and desctination are the same.");
    }

    $df = DataFilePeer::find($fileId);
    if(!$df) {
      exit("Error: data file not dound in database.");
    }

    $systempath = get_systemPath($this->currentPath . "/" . $this->file);

    if($df->getFullPath() != $systempath) {
      exit("Error: data file on disc and in database are not matched");
    }

    if($this->moveDataFile($systempath, $df->getPath() . "/" . $new_name)) {
      exit($new_name);
    }

    exit("Error: Unable to rename file: $this->file. Make sure there is no duplicate file name.");
  }


  function printContent($files_content, $js = "") {

    $formId = $this->formId;
    $currentPathBar = $this->getCurrentPathBar();

    $view_file_browser = <<<ENDHTML

$js
<!-- Start File Browser -->
<form id="$formId" method="post">
<div id="MainFileBrowserDiv_$formId">
  <div class="miniportlet">
    <div class="miniportlet_h3">
      <div class="miniportlet_title floatleft"><a href="$this->baselink">$this->rootname</a></div>
      <input type="button" class="btn floatright" onclick="javaScript:location.href='$this->currentLink'" value="Go Back" />
    </div>
    $currentPathBar
    <!-- Start File Content -->
    $files_content
    <!-- End File Content -->
  </div>
</div>
</form>
<!-- End File Browser -->

ENDHTML;

    return $view_file_browser;
  }


  function printMainContent($files_content, $js = "") {

    $formId = $this->formId;
    $createDirButton = "";
    $uploadButton = "";

    if($this->canEdit || $this->canCreate) {

      $url = "/common/upload.php?basepath=" . $this->friendly_basepath .
                                "&path=" . rawurlencode($this->path) .
                                "&rootname=" . $this->rootname;

      $uploadButton = "<input type='button' class='btn floatright' onclick=\"window.open('" . $this->replace_single_quote($url) . "', 'UploadWindow', 'left=0,top=0,width=680,height=700,toolbar=no,menubar=no,status=no,scrollbars=yes,resizable=no');\" value='Upload' />";

      $createDirButton = "<input type='button' class='btn floatright' title='Make new directory in current path' onclick=\"javascript:mkdirBox('$formId');\" value='Create New Directory' />";
    }

    // Special case for Unstructured Project: View Categories can not allow to upload and create Directory in root path
    if($_REQUEST['action'] == "DisplayProjectCategories" && empty($this->path)) {
      $uploadButton = $createDirButton = "";
    }

    $currentPathBar = $this->getCurrentPathBar();

    $view_file_browser = <<<ENDHTML

$js

<!-- Start File Browser -->
<form id="$formId" method="post">
<div id="MainFileBrowserDiv_$formId">
  <div class="miniportlet">
    <div class="miniportlet_h3">
      <div class="miniportlet_title floatleft"><a href="$this->baselink">$this->rootname</a></div>
      $createDirButton
      $uploadButton
    </div>
    $currentPathBar
    <!-- Start File Content -->
    $files_content
    <!-- End File Content -->
  </div>
</div>
</form>

<!-- End File Browser -->


ENDHTML;

    return $view_file_browser;
  }


  function printJSFunctions() {

    $js = <<<ENDHTML

<script type="text/javascript">
<!--

function toggleDiv(showDivId, hideDivId, focusId) {
  if(!document.getElementById) { return; }

  var showDiv = document.getElementById(showDivId);
  if( !showDiv ) return;

  if(showDiv.style.display == '') {
    showDiv.style.display = 'none';
  }
  else {
    showDiv.style.display = '';

    if(focusId) {
      focusBtn = document.getElementById(focusId);
      if(focusBtn) focusBtn.focus();
    }

    hideDiv = document.getElementById(hideDivId);
    if (hideDiv) {
      hideDiv.style.display = 'none';
    }
  }
}


function hiddenDiv(hideDivId) {
  if(!document.getElementById) { return; }

  var hideDiv = document.getElementById(hideDivId);
  if( !hideDiv ) return;

  hideDiv.style.display = 'none';
}

function disable_field(fieldId) {
  checkboxfield = document.getElementById('Checkbox' + fieldId);
  field = document.getElementById(fieldId);

  if(checkboxfield.checked) {
    field.disabled = false;
    field.style.backgroundColor = "#ffffff";
  }
  else {
    field.disabled = true;
    field.style.backgroundColor = "#eeeeee";
  }
}


function dataFileJS(id, name, directory, title, description, authors, emails, cite)
{
  this.id          = id;
  this.name        = name;
  this.directory   = directory;
  this.title       = title;
  this.description = description;
  this.authors     = authors;
  this.emails      = emails;
  this.cite        = cite;
  this.dirinfo     = null;
}


function graphMe(fileid) {
  var url = "/common/graph.php?fileid=" + fileid;
  window.open(url, 'DownloadWindow', 'left=0,top=0,width=820,height=850,toolbar=no,menubar=no,status=no,scrollbars=yes,resizable=yes');
}


//-->
</script>

ENDHTML;

    return $js;

  }
}

?>