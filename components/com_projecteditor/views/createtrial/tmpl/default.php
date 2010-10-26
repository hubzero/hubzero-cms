<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
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
  $document->addScript($this->baseurl."/includes/js/joomla.javascript.js", 'text/javascript');
?>

<?php JHTML::_('behavior.calendar'); ?>

<form action="/warehouse/projecteditor/savetrial" method="post">
<input type="hidden" name="path" value="<?php echo $this->strPath; ?>" id="path"/>
<input type="hidden" name="referer" value="<?php echo $this->strReferer; ?>" id="referer"/>
<input type="hidden" name="experimentId" value="<?php echo $this->iExperimentId; ?>" id="experiment"/>


<div><h2>Create Trial</h2></div>
<div class="information"><b>Destination:</b> <?php echo $this->strPath; ?></div>

<table style="border:0px;">
  <tr>
    <td width="1" nowrap>
      <label for="title" class="editorLabel">Title: <span class="requiredfieldmarker">*</span></label>
    </td>
    <td><input id="title" type="text" name="title" class="editorInputSize"/></td>
  </tr>
  <tr>
    <td width="1" nowrap>
      <label for="strStartDate" class="editorLabel">Start Date:</label>
    </td>
    <td>
      <input id="strStartDate" type="text" name="startdate" class="editorInputSize" value="mm/dd/yyyy" onClick="this.value=''"/>
      <!--
      <div class="editorInputFloat editorInputSize" id="startDateInput">
        <input type="text" value="2010-08-30" style="width: 100%;" name="startdate" id="strStartDate">
      </div>
      <div class="editorInputFloat editorInputButton" id="startDateCalendar">
        <img onclick="return showCalendar('strStartDate', '%m/%d/%Y');" alt="calendar" src="/components/com_warehouse/images/calendar/calendar-blue.png" class="calendar">
      </div>
      <div class="clear"></div>
      -->
    </td>
  </tr>
  <tr>
    <td width="1" nowrap>
      <label for="strEndDate" class="editorLabel">End Date:</label>
    </td>
    <td>
      <input id="strEndDate" type="text" name="enddate" class="editorInputSize" value="mm/dd/yyyy" onClick="this.value=''"/>
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
      <input type="submit" value="Create Trial"/>
    </td>
  </tr>
</table>
</form>

