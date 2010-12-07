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
   $document->addScript($this->baseurl."/includes/js/calendar/lang/calendar-en-GB.js", 'text/javascript'); 
  $document->addScript($this->baseurl."/includes/js/calendar/calendar.js", 'text/javascript'); 
      
  $document->addScript($this->baseurl."/includes/js/joomla.javascript.js", 'text/javascript');
//  echo $this->baseurl;
  ?>

<?php JHTML::_('behavior.calendar'); ?>

<form action="/warehouse/projecteditor/savetrial" method="post">
<input type="hidden" name="path" value="<?php echo $this->strPath; ?>" id="path"/>
<input type="hidden" name="referer" value="<?php echo $this->strReferer; ?>" id="referer"/>
<input type="hidden" name="experimentId" value="<?php echo $this->iExperimentId; ?>" id="experiment"/>
<input type="hidden" name="trialId" value="0" id="trialId"/>


<div><h2>Create Trial</h2></div>
<div class="information"><b>Destination:</b> <?php echo $this->strPath; ?></div>

<table style="border:0px;">
  <tr>
    <td width="1" nowrap>
      <p class="editorParagraph">
         <label for="txtTitle" class="editorLabel">Title:<span class="requiredfieldmarker">*</span></label>
         <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Title :: For an existing trial, type its title and mouse over the suggestion."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
      </p>
    </td>
    <td>
      <input id="txtTitle" type="text" name="title" class="editorInputSize"autocomplete="off" value="" onkeyup="suggestTrial('/projecteditor/trialsearch?format=ajax', 'trialSearch', this.value, <?php echo $this->iExperimentId; ?>, this.id)"/>
      <div id="trialSearch" class="suggestResults"></div>
    </td>
  </tr>
  
    <tr id="start_date">
  	<td nowrap="">
    	<p class="editorParagraph">
             <label for="strStartDate" class="editorLabel">Start Date:<span class="requiredfieldmarker">*</span></label>
             <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Start Date :: Please provide the date you started the trial."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
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
        <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="End Date :: Please provide the date you completed the trial."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
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
    <td width="1" nowrap>
      <label for="objective" class="editorLabel">Objective:</label>
    </td>
    <td><textarea id="objective" name="objective" class="editorInputSize"></textarea></td>
  </tr>
  <tr>
    <td width="1" nowrap>
      <label for="description" class="editorLabel">Description:</label>
    </td>
    <td><textarea id="description" name="description" class="editorInputSize"></textarea></td>
  </tr>
  <tr>
    <td colspan="2">
      <input type="submit" value="Save Trial"/>
    </td>
  </tr>
</table>
</form>

