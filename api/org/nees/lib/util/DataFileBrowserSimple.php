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

require_once "lib/common/browser.php";
require_once "lib/interface/Data.php";
require_once "lib/data/DataFile.php";
require_once 'lib/data/curation/NCCuratedNCIDCrossRef.php';

class DataFileBrowserSimple {

  private $entity;
  private $datafiles;
  private $queryStr;
  private $canView   = false;
  private $canEdit   = false;
  private $canCreate = false;
  private $canDelete = false;
  private $canCurate = false;
  private $isError = false;
  private $viewOnly = false;
  private $orderNum = 0;
  private $printedJS = false;

  /**
   * Construct for DataFileBrowserSimple class
   *
   * @param BaseObject $entity
   * @param array[DataFile] $datafiles
   * @return DataFileBrowserSimple
   */
  public function __construct($entity, $viewOnly=false) {
    $this->entity    = $entity;

    $auth = Authorizer::getInstance();
    $this->canView = $entity ? $auth->canView($entity) : true;

    if(!$this->canView) {
      $this->isError = true;
      return;
    }

    if($this->entity) {
      $entityTypeId = DomainEntityType::getEntityTypeId($this->entity);
      if($entityTypeId == DomainEntityType::ENTITY_TYPE_PROJECT ) {
        $this->queryStr = "projid=" . $this->entity->getId();
      }
      elseif($entityTypeId == DomainEntityType::ENTITY_TYPE_EXPERIMENT ) {
        $this->queryStr = "expid=" . $this->entity->getId();
      }
      elseif($entityTypeId == DomainEntityType::ENTITY_TYPE_FACILITY ) {
        $this->queryStr = "facid=" . $this->entity->getId();
      }

      if($viewOnly) {
        $this->canEdit   = false;
        $this->canCreate = false;
        $this->canDelete = false;
      }
      else {
        $this->canEdit   = $auth->canEdit($entity);
        //$this->canCreate = $auth->canCreate($entity);
        $this->canDelete = $auth->canDelete($entity);
      }

    }

    $this->canCurate = $auth->canCurate();

    //if (isset($_REQUEST['action']) )
    //	$this->action = $_REQUEST['action'];
 
  }


  /**
   * Set a list of data files
   *
   * @param array[DataFile]
   */
  function setDataFiles($datafiles) {
    $this->datafiles = $datafiles;
  }


  /**
   * Set persission
   *
   * @param boolean $canView
   */
  function setCanView($canView) {
    $this->canView = $canView;
  }


  /**
   * Set persission canEdit
   *
   * @param boolean $canEdit
   */
  function setCanEdit($canEdit) {
    $this->canEdit = $canEdit;
  }


  /**
   * Set persission canDelete
   *
   * @param boolean $canDelete
   */
  function setCanDelete($canDelete) {
    $this->canDelete = $canDelete;
  }


  /**
   * Set persission canCreate
   *
   * @param boolean $canCreate
   */
  function setCanCreate($canCreate) {
    $this->canCreate = $canCreate;
  }


  /**
   * Get persission
   *
   * @return boolean $canView
   */
  function getCanView() {
    return $this->canView;
  }


  /**
   * Get persission canEdit
   *
   * @return boolean $canEdit
   */
  function getCanEdit() {
    return $this->canEdit;
  }


  /**
   * Get persission canDelete
   *
   * @return boolean $canDelete
   */
  function getCanDelete() {
    return $this->canDelete;
  }


  /**
   * Get persission canCreate
   *
   * @return boolean $canCreate
   */
  function getCanCreate() {
    return $this->canCreate;
  }


  //**********************************************************************************************
  /**
   * Main UI for DataFile Browser
   *
   * @param array[DataFile] $datafiles
   * @param String $rootname
   *
   * @return
   *   html code for data file browser
   */
  //**********************************************************************************************
  function getViewSimpleFileBrowser($datafiles, $rootname){
    $this->setDataFiles($datafiles);
    $browser_file = "";
    $count_files = 0;
    $uniqueId = uniqid();

    //$orderNum = 0;

    /**
     * **********************************************************************************
     * Directory Listing
     * **********************************************************************************
     */

    $listJS = "";

//    print_r($datafiles);
    
    foreach($this->datafiles as $df) {
      /* @var $df DataFile */
      //name, directory, created, filesize

      if(is_null($df))
      {
      	continue;
      }

      $df_id          = $df->getId();
      $df_path        = $df->getPath();
      $df_name        = $df->getName();
      $df_created     = $df->getCreated();

      //$df_created     = $this->cleantimestamp($df->getCreated());
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

      if($df_directory) continue;

      if(empty($df_created)) $df_created = date("Y m d H:i:s", filemtime($df_fullpath));
      $df_created = $this->cleantimestamp($df_created);

      $count_files++;

      $this->orderNum++;
      $rowId = $this->orderNum - 1;

      $name_truncate = $this->str_truncate($df_name, 60);
      $name_encode = rawurlencode($df_name);

      $displayMetadata = "";

      if(empty($df_description) && empty($df_title) && empty($df_authors) && empty($df_emails) && empty($df_cite)) {
        $displayMetadata = " style='display: none;'";
      }

      $metadataLink = "<span id='metadataRow_$rowId' $displayMetadata><a href='javascript:void(0);' style='display: inline; cursor:default;' onmouseover=\"displayTooltip_simple(dataFileJSList_simple[$rowId]);\" onmouseout='UnTip();'><img src='/images/file_view.gif' width='16' height='16' style='vertical-align:middle;' alt='' /></a></span>";

      $editMetadataLink = "";
      $extraSpace = "<img src='/images/pixel.gif' width='16' height='16' id='edit_metadata_loading_" . $rowId . "'/>&nbsp;&nbsp;";

      if($this->canEdit) {
        $editMetadataLink = "<a href='javascript:void(0);' onclick=\"javascript:editMetadataAjax_simple('tableId_$uniqueId', $rowId);\"><img src='/images/file_edit.gif' width='16' height='16' style='vertical-align:middle;' alt='' onmouseover=\"Tip('Edit Metadata');\" onmouseout='UnTip()' /></a>";
      }

      if(empty($df_filesize)) {
        $df_filesize = filesize($df_fullpath);
      }

      $cleansize = cleanSize($df_filesize);
      $file_url = $df->get_url();

      $mime_icon = $df->getMimeIcon();

      $canDeleteLink = $this->canDelete ? "<a href=\"JavaScript:delete_fileid_simple('tableId_$uniqueId', $rowId, $df_id);\" onmouseover=\"Tip('Delete this file');\" onmouseout='UnTip()'><img src='/images/icons/silk/cross.png' width='16' height='16' id='delete_$df_id' style='vertical-align: middle;' alt='' /></a>" : "";

      $viewCurateLink = $canCurateLink = "";

      $crossRef  = NCCuratedNCIDCrossRefPeer::findByCentralIdAndTableSource($df_id, "DataFile");

      ## Document already curated. Enable Curation View interface.
      if(count($crossRef) > 0){
        $_SESSION['NEEScentralReturnPath'] = $_SERVER['REQUEST_URI'];
        $curatedId = $crossRef[0]->getCuratedEntityID();
        $viewCurateLink = "<a href='/curation/run.php/CatalogListing?cmd=curate&id=$curatedId' class='button mini'><img src='/images/file_curated.gif' width='16' height='16' style='vertical-align: middle;' alt='' onmouseover=\"Tip('View data file curation');\" onmouseout='UnTip()'/></a>";
      }
      elseif ($this->canCurate) {
        $expid  = isset($_REQUEST['expid']) ? $_REQUEST['expid'] : null;
        $projid = isset($_REQUEST['projid']) ? $_REQUEST['projid'] : null;
        $ready2Curate = false;

        if($expid && count(NCCuratedNCIDCrossRefPeer::findByCentralIdAndTableSource($expid, 'Experiment')) > 0) {
          $ready2Curate = true;
        }
        elseif(!$expid && $projid && count(NCCuratedNCIDCrossRefPeer::findByCentralIdAndTableSource($projid, 'Project')) > 0) {
          $ready2Curate = true;
        }

        if($ready2Curate) {
          $_SESSION['NEEScentralReturnPath'] = $_SERVER['REQUEST_URI'];
          $canCurateLink = "<a href='/curation/run.php/Curation?id=$df_id&projid=$projid&expid=$expid&cmd=curate&table=DataFile'><img src='/images/file_curator.gif' width='16' height='16' style='vertical-align: middle;' alt='' onmouseover=\"Tip('For curator only');\" onmouseout='UnTip()'/></a>";
        }
      }

      $orderNum = $this->orderNum;

      $listing = <<<ENDHTML

        <tr id="rowId_simple_$rowId">
          <td nowrap="nowrap" class="orderNum">$orderNum</td>
          <td nowrap="nowrap"><a href="$file_url" target="_blank"><img src="$mime_icon" width="16" height="16" alt=""/></a></td>
          <td nowrap="nowrap"><a href="$file_url" target="_blank">$name_truncate</a></td>
          <td nowrap="nowrap">$df_created</td>
          <td nowrap="nowrap">$cleansize</td>
          <td style="text-align: right;" nowrap="nowrap">
            $extraSpace &nbsp; $metadataLink &nbsp; $editMetadataLink &nbsp; $canDeleteLink &nbsp; $viewCurateLink &nbsp; $canCurateLink
          </td>
        </tr>

ENDHTML;

      $browser_file .= $listing;
      $df_description = str_replace("\n", "", $df_description);

      $listJS .= "\ndataFileJSList_simple[$rowId] = new dataFileJS_simple($df_id, '" . $this->replace_single_quote($df_name) . "', 0, '" . $this->replace_single_quote($df_title) . "', '" . $this->replace_single_quote($df_description) . "', '" . $this->replace_single_quote($df_authors) . "', '" . $this->replace_single_quote($df_emails) . "', '" . $this->replace_single_quote($df_cite) . "');";
    }

    if($count_files == 0) {
      $browser = <<<ENDHTML

<div id="MainFileBrowserDiv_$uniqueId">
  <div class="miniportlet">
    <div class="">
      <h3>$rootname</h3>
    </div>
    <div class="contentpadding">
      <i>No files uploaded.</i>
    </div>
  </div>
</div>

ENDHTML;

      return $browser;

    }

    //$browser_file.= "<tr><td>&nbsp;</td></tr>";

    $queryStr = $this->queryStr;

    if($this->printedJS) $jsFunctions = "";

    else {
      $jsFunctions = <<<ENDHTML

<script type="text/javascript">
<!--

var dataFileJSList_simple = new Array();
var xmlHttp;
var current_fileid_simple = -1;
var current_rowId_simple = -1;
var current_tableId_simple = -1;

function dataFileJS_simple(id, name, directory, title, description, authors, emails, cite)
{
  this.id          = id;
  this.name        = name;
  this.directory   = directory;
  this.title       = title;
  this.description = description;
  this.authors     = authors;
  this.emails      = emails;
  this.cite        = cite;
}


function displayTooltip_simple(df) {
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
  else {
    Tip('<br/>This data file does not have any metadata.<br/><br/>', TITLE, tt_fileTypeName);
  }
}

ENDHTML;


      if($this->canEdit) {

        $jsFunctions .= <<<ENDHTML

function deleteRow_simple(tableId, metadataRowId) {
  tr = document.getElementById(metadataRowId);
  document.getElementById(tableId).deleteRow(tr.rowIndex);
}


function applyMetadataAjax_simple(tableId, fid, rowId) {

  var metadata_title       = document.getElementById('title_' + fid).value;
  var metadata_description = document.getElementById('description_' + fid).value;
  var metadata_authors     = document.getElementById('authors_' + fid).value;
  var metadata_emails      = document.getElementById('emails_' + fid).value;
  var metadata_cite        = document.getElementById('cite_' + fid).value;

  xmlHttp=GetXmlHttpObject();

  if (xmlHttp==null)
  {
    alert ("Browser does not support HTTP Request");
    return;
  }

  var url="/ajax/ajaxDataFileBrowser.php?$queryStr&fileId=" + fid + "&fileAction=EditSingleMetadataAjax&title=" + escape(metadata_title) + "&description=" + escape(metadata_description) + "&authors=" + escape(metadata_authors) + "&emails=" + escape(metadata_emails) + "&cite=" + escape(metadata_cite) + "&doit=ajax&sid="+Math.random();

  current_fileid_simple = fid;
  current_rowId_simple = rowId;
  current_tableId_simple = tableId;

  xmlHttp.onreadystatechange=stateChangedOnEditSingleMetadataAjax_simple;
  xmlHttp.open("GET",url,true);
  xmlHttp.send(null);
}

function stateChangedOnEditSingleMetadataAjax_simple()
{
  loading_id = document.getElementById("edit_metadata_loading_" + current_rowId_simple);

  if (xmlHttp.readyState < 4)
  {
    if(loading_id) loading_id.src = '/tree_browser/img/loading.gif';
  }
  else if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
  {
    ret = xmlHttp.responseText;

    if (ret == current_fileid_simple) {
      var metadata_title = document.getElementById('title_' + current_fileid_simple).value;
      var metadata_description = document.getElementById('description_' + current_fileid_simple).value;
      var metadata_authors = document.getElementById('authors_' + current_fileid_simple).value;
      var metadata_emails = document.getElementById('emails_' + current_fileid_simple).value;
      var metadata_cite = document.getElementById('cite_' + current_fileid_simple).value;

      dfJS = dataFileJSList_simple[current_rowId_simple];

      dfJS.title       = metadata_title;
      dfJS.description = metadata_description;
      dfJS.authors     = metadata_authors;
      dfJS.emails      = metadata_emails;
      dfJS.cite        = metadata_cite;

      if(document.getElementById('metadata_' + current_rowId_simple)) {
        deleteRow_simple(current_tableId_simple, 'metadata_' + current_rowId_simple);
      }

      if(loading_id) loading_id.src = '/images/done.gif';

      var metadataRowId = document.getElementById("metadataRow_" + current_rowId_simple);
      if(metadataRowId) metadataRowId.style.display='';
    }
    else {
      alert("Cannot edit metadata. the error is: " + ret);

      if(loading_id) loading_id.src = '/images/pixel.gif';
    }
  }
}


function editMetadataAjax_simple(tableId, rowId) {

  dfJS = dataFileJSList_simple[rowId];

  fid = dfJS.id;

  if(document.getElementById('metadata_' + rowId)) {
    deleteRow_simple(tableId, 'metadata_' + rowId);
    return;
  }

  UnTip();
  tr = document.getElementById("rowId_simple_" + rowId);

  var new_row = document.getElementById(tableId).insertRow(tr.rowIndex + 1);
  new_row.setAttribute('id', 'metadata_' + rowId);
  var cell1 = new_row.insertCell(0);
  var cell2 = new_row.insertCell(1);
  cell1.colSpan="2";
  cell2.colSpan="4";

  tb  = "<div id='metadata_" + rowId + "' style='padding-left:35px; border:1px solid #0081A0; background-color:#E6E6E6; font-size:11px;'><br\/>";
  tb += "<table cellpadding='0' cellspacing='0' width='100%' style='margin:0 0; border-left:1px solid #EEEEEE;'>";
  tb += "  <tr>";
  tb += "    <td nowrap='nowrap'><b>Title:<\/b><\/td>";
  tb += "    <td width='100%'><input type='text' name='title_" + fid + "' id='title_" + fid + "' value='" + dfJS.title + "' style='width:95%'><\/td>";
  tb += "  <\/tr>";
  tb += "  <tr>";
  tb += "    <td nowrap='nowrap'><b>Description:<\/b><\/td>";
  tb += "    <td width='100%'><textarea name='description_" + fid + "' id='description_" + fid + "' style='width:95%'>" + dfJS.description + "<\/textarea><\/td>";
  tb += "  <\/tr>";
  tb += "  <tr>";
  tb += "    <td nowrap='nowrap'><b>Authors:<\/b><\/td>";
  tb += "    <td width='100%'><input type='text' name='authors_" + fid + "' id='authors_" + fid + "' value='" + dfJS.authors + "' style='width:95%'><\/td>";
  tb += "  <\/tr>";
  tb += "  <tr>";
  tb += "    <td nowrap='nowrap'><b>Author Emails:<\/b><\/td>";
  tb += "    <td width='100%'><input type='text' name='emails_" + fid + "' id='emails_" + fid + "' value='" + dfJS.emails + "' style='width:95%'><\/td>";
  tb += "  <\/tr>";
  tb += "  <tr>";
  tb += "    <td nowrap='nowrap'><b>How to Cite:<\/b><\/td>";
  tb += "    <td width='100%'><input type='text' name='cite_" + fid + "' id='cite_" + fid + "' value='" + dfJS.cite + "' style='width:95%'><\/td>";
  tb += "  <\/tr>";
  tb += "  <tr><td colspan='2' style='text-align:right;'><br\/><a href=\"javascript:deleteRow_simple('" + tableId + "','metadata_" + rowId + "');\" class='btn mini'>Cancel<\/a>&nbsp;&nbsp;<a href='javascript:void(0);' onclick=\"javascript:applyMetadataAjax_simple('" + tableId + "'," + fid + ", " + rowId + ");\" class='btn mini'\/>Apply Metadata<\/a>&nbsp;&nbsp;&nbsp;<input type='hidden' name='fileAction' value='EditSingleMetadataAjax' \/><br\/><br\/><\/td><\/tr>";
  tb += "<\/table>";
  tb += "<\/div>";

  cell2.innerHTML = tb;
}

ENDHTML;

      }
      if($this->canDelete) {

        $jsFunctions .= <<<ENDHTML

/////////////////////////////////////////////////////////////////////////
//Function to delete a data file by id
/////////////////////////////////////////////////////////////////////////

function delete_fileid_simple(tableId, rowId, id) {

  dfJS = dataFileJSList_simple[rowId];
  if(dfJS.id != id) {
    alert("Row ID and Data file ID mismatch");
    return;
  }

  confirmMsg = "Please note: This action cannot be undone. \\n\\n" +
               "Are you sure you want to delete this data file \"" + dfJS.name + "\"?";

  if(!confirm(confirmMsg)) {
    return;
  }

  xmlHttp=GetXmlHttpObject();

  if (xmlHttp==null)
  {
    alert ("Browser does not support HTTP Request");
    return;
  }

  var url="/ajax/ajaxDataFileBrowser.php?$queryStr&fileId=" + dfJS.id + "&fileAction=Delete&doit=ajax&sid="+Math.random();

  current_fileid_simple = id;
  current_rowId_simple = rowId;
  current_tableId_simple = tableId;

  xmlHttp.onreadystatechange=stateChangedOnDeleteFile_simple;
  xmlHttp.open("GET",url,true);
  xmlHttp.send(null);
}


function stateChangedOnDeleteFile_simple()
{
  delete_id = document.getElementById("delete_" + current_fileid_simple);

  if (xmlHttp.readyState < 4)
  {
    if(delete_id) delete_id.src = '/tree_browser/img/loading.gif';
  }
  else if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
  {
    ret = xmlHttp.responseText;
    row_fileid = document.getElementById("rowId_simple_" + current_rowId_simple);

    if(ret.indexOf("Error") >= 0) {
      alert("Cannot delete this file. The error is: " + ret);
      if(delete_id) delete_id.src = '/images/icons/silk/cross.png';
      return;
    }
    else if (ret.toString().search(/^[0-9]+$/) == 0) {
      rowIndex = row_fileid.sectionRowIndex;
      document.getElementById(current_tableId_simple).deleteRow(rowIndex);

      //Close the expanded metadata if it is open
      expandRowId = document.getElementById("metadata_" + current_rowId_simple);

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

ENDHTML;

      }

      $jsFunctions .= <<<ENDHTML
//-->
</script>

ENDHTML;

    }

    $browser = <<<ENDHTML

<!-- Start Simple DataFile Browser -->

<div id="MainFileBrowserDiv_$uniqueId">
  <div class="miniportlet">
    <div class="miniportlet_h3" style="padding-bottom:5px;">
      <h3>$rootname</h3>
    </div>

$jsFunctions

<script type="text/javascript">
<!--

$listJS

ENDHTML;

    $browser .= <<<ENDHTML

//-->
</script>
    <div class="filebrowser">
      <table border="0" cellpadding="0" cellspacing="0" style="width: 750px; border-width: 0px;" id="tableId_$uniqueId" >
        <tr style="background:#eee;" >
          <td class="blabels" width="1">&nbsp;</td>
          <td class="blabels">&nbsp;</td>
          <td class="blabels">Name</td>
          <td class="blabels">Timestamp</td>
          <td class="blabels">Size</td>
          <td class="blabels">&nbsp;</td>
        </tr>
        $browser_file
      </table>
    </div>
    <!-- End File Content -->
  </div>
</div>

ENDHTML;

    $this->printedJS = true;

    return $browser;

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

}

?>