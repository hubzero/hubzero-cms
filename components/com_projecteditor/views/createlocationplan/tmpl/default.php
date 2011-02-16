<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_projecteditor/css/projecteditor.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addStyleSheet($this->baseurl."/templates/fresh/html/com_groups/groups.css",'text/css');
  $document->addStyleSheet($this->baseurl."/plugins/tageditor/autocompleter.css",'text/css');

  $document->addScript($this->baseurl."/components/com_projecteditor/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/tips.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/projecteditor.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/resources.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/textboxlist.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/observer.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/autocompleter.js", 'text/javascript');
?>

<?php

$iLocationPlanId = 0;
$strLocationPlanName = StringHelper::EMPTY_STRING;

$oLocationPlan = null;
if(isset($_REQUEST[LocationPlanPeer::TABLE_NAME])){
  $oLocationPlan = unserialize($_REQUEST[LocationPlanPeer::TABLE_NAME]);
  $iLocationPlanId = $oLocationPlan->getId();
  $strLocationPlanName = $oLocationPlan->getName();
}
?>

<form id="frmPopout" action="/warehouse/projecteditor/savelocationplan" method="post">
    <input type="hidden" name="experimentId" value="<?php echo $this->iExperimentId; ?>"/>
    <input type="hidden" name="locationPlanId" value="<?php echo $iLocationPlanId; ?>"/>

    <div><h2>Create Sensor List</h2></div>
    <div class="information">Create a group for individual sensors.</div>

    <table style="border: 0px;width: 320px;">
        <tr>
            <td>Sensor List Name <span class="requiredfieldmarker">*</span></td>
            <td><input type="text" name="lpName" autocomplete="off" value="<?php echo $strLocationPlanName; ?>"/></td>
        </tr>
    </table>

    <div class="sectheaderbtn">
      <a href="javascript:void(0);" class="button2"  onClick="document.getElementById('frmPopout').submit();window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close()', 700);">Save Sensor List</a>
    </div>
</form>