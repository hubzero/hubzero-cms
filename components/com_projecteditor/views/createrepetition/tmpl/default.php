<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: 0"); // Date in the past
?>

<?php
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_projecteditor/css/projecteditor.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addStyleSheet($this->baseurl."/plugins/tageditor/autocompleter.css",'text/css');

  $document->addScript($this->baseurl."/components/com_projecteditor/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/tips.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/projecteditor.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/textboxlist.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/observer.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/autocompleter.js", 'text/javascript');

    $document->addScript($this->baseurl."/media/system/js/calendar-setup.js", 'text/javascript');  
  
  $document->addScript($this->baseurl."/includes/js/calendar/calendar_mini.js", 'text/javascript'); 
  //$document->addScript($this->baseurl."/includes/js/calendar/calendar-setup.js", 'text/javascript');  
   $document->addScript($this->baseurl."/includes/js/calendar/lang/calendar-en-GB.js", 'text/javascript'); 
  $document->addScript($this->baseurl."/includes/js/calendar/calendar.js", 'text/javascript'); 

  $document->addScript($this->baseurl."/includes/js/joomla.javascript.js", 'text/javascript');
  
  ?>


<?php JHTML::_('behavior.calendar'); ?>



<?php
$oTrialArray = unserialize($_REQUEST[TrialPeer::TABLE_NAME]);
$oExperiment = unserialize($_REQUEST[ExperimentPeer::TABLE_NAME]);
$oAuthorizer = Authorizer::getInstance();
?>

<div id="frmWrapper">
<form id="frmRepetition" action="/warehouse/projecteditor/saverepetition" method="post">
<input type="hidden" name="path" value="<?php echo $this->strPath; ?>" id="path"/>
<input type="hidden" name="referer" value="<?php echo $this->strReferer; ?>" id="referer"/>
<input type="hidden" name="repetition" value="0" id="repId"/>
<input type="hidden" name="eid" value="0" id="entityId"/>
<input type="hidden" name="etid" value="5" id="entityTypeId"/>

<div><h2>Create Repetition</h2></div>

<div class="information"><b>Destination:</b> <?php echo $this->strPath; ?></div>

<table style="border:0px;">
  <tr>
    <td nowrap="">
      <p class="editorParagraph">
         <label for="trial" class="editorLabel">Trial:<span class="requiredfieldmarker">*</span></label>
         <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Trial :: Please select the trial of interest."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
      </p>
    </td>
    <td>
      <select id="trial" name="trial" class="editorInputSize" onChange="getRepetitionList(this.id, 'repetitions', 'repetitionList');">
        <?php  
          /* @var $oTrial Trial */
          foreach($oTrialArray as $oTrial){
            $strTrialName = $oTrial->getName();
            $strTrialTitle = $oTrial->getTitle();    
          ?>
            <option value="<?php echo $oTrial->getId(); ?>"><?php echo $strTrialName .": ". $strTrialTitle; ?></option>
          <?php
          }
        ?>
      </select>    
    </td>
  </tr>
  <tr>
    <td width="1" nowrap>
      <p class="editorParagraph">
         <label for="txtTitle" class="editorLabel">Name:</label>
         <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Name :: Select an existing repetition to prefill start and end dates (if available)."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
      </p>
    </td>
    <td>
      <div id="repetitionList">
        <select id="cboRepId" name="id" onchange="suggestRepetition('/warehouse/projecteditor/getrepetitioninfo?format=ajax', this.value);">
          <option value="0">New Repetition</option>
          <?php
            /* @var $oRepetition Repetition */
            $oRepititionArray = unserialize($_REQUEST[RepetitionPeer::TABLE_NAME]);
            foreach($oRepititionArray as $oRepetition){
              $strDisplay = $oRepetition->getName();
            ?>

              <option value="<?php echo $oRepetition->getId(); ?>"><?php echo $strDisplay; ?></option>
            <?php
            }
          ?>
        </select>
      </div>
    </td>
  </tr>
  <tr id="start_date">
    <td nowrap="">
      <p class="editorParagraph">
         <label for="strStartDate" class="editorLabel">Start Date:<span class="requiredfieldmarker">*</span></label>
         <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Start Date :: Please provide the date you started the repetition."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
      </p>
    </td>
    <td>
        <div id="startDateInput" class="editorInputFloat editorInputSize">
        <input type="text" id="strStartDate" name="startdate" style="width:100%;" value="" />
    </div>
        <div id="startDateCalendar" class="editorInputFloat editorInputButton">
        <img class="calendar" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="calendar" onclick="return showCalendar('strStartDate', '%m/%d/%Y');" />
    </div>
        <div class="clear"></div>
    </td>
  </tr>

  <tr id="end_date">
   <td nowrap="">
     <p class="editorParagraph">
        <label for="strEndDate" class="editorLabel">End Date:</label>
        <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="End Date :: Please provide the date you completed the repetition."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
     </p>
     </td>
     <td>
            <div id="endDateInput" class="editorInputFloat editorInputSize">
        <input type="text" id="strEndDate" name="enddate" style="width:100%;" value="" />
        </div>
<!--           <div id="endDateCalendar" class="editorInputFloat editorInputButton">-->
    <div id="endDateCalendar" class="editorInputFloat editorInputButton">
        <img class="calendar" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="calendar" onclick="return showCalendar('strEndDate', '%m/%d/%Y');" />
             </div>
      <div class="clear"></div>
    </td>
  </tr>
   

  <tr>
    <td colspan="2">
      <!--
      <input type="submit" value="Save Repetition"/>
      <a title="Delete repetition." class="modal"
         onClick="deleteEntity('frmRepetition', '/warehouse/projecteditor/removeentity', 'entityId', 'repId')"
         href="javascript:void(0)">
        Delete Repetition
      </a>
      -->

      <div class="sectheaderbtn">
        <a title="Save repetition." href="javascript:void(0);" class="button2"  onClick="document.getElementById('frmRepetition').submit();">Save Repetition</a>
        <?php if($oExperiment && $oAuthorizer->canDelete($oExperiment)){ ?>
          <a title="Delete repetition." class="button2"
             onClick="getEntityDeleteForm('repetition', 'entityId', 'repId', 'frmWrapper');"
             href="javascript:void(0)">
            Delete Repetition
          </a>
        <?php } ?>
      </div>
    </td>
  </tr>
</table>
</form>
</div>


