<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php
$oTrialArray = unserialize($_REQUEST[TrialPeer::TABLE_NAME]);
?>

<form action="/warehouse/projecteditor/saverepetition" method="post">
<input type="hidden" name="path" value="<?php echo $this->strPath; ?>" id="path"/>
<input type="hidden" name="referer" value="<?php echo $this->strReferer; ?>" id="referer"/>

<div><h2>Create Repetition</h2></div>

<div class="information"><b>Destination:</b> <?php echo $this->strPath; ?></div>

<table style="border:0px;">
  <tr>
    <td width="1" nowrap="">
      <label for="trial" class="editorLabel">Trial:<span class="requiredfieldmarker">*</span></label>
    </td>
    <td>
      <select id="trial" name="trial" class="editorInputSize">
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
      <label for="title" class="editorLabel">Title:</label>
    </td>
    <td><input id="title" type="text" name="title" class="editorInputSize"/></td>
  </tr>
  <tr>
    <td width="1" nowrap>
      <label for="strStartDate" class="editorLabel">Start Date:</label>
    </td>
    <td>
       <input id="strStartDate" type="text" name="startdate" class="editorInputSize" value="mm/dd/yyyy" onClick="this.value=''"/>
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
    <td colspan="2">
      <input type="submit" value="Create Repetition"/>
    </td>
  </tr>
</table>
</form>


