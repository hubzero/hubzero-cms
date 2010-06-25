<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<select id="cboRepetitions" name="repetition" onChange="loadingContent('dataList');getData(this.id, 'dataList', 'repetition');getTools(this.id, 'interactiveList', 'repetition');">
  <option value="">Select Repetition</option>
  <?php 
    $oRepititionArray = unserialize($_REQUEST[RepetitionPeer::TABLE_NAME]);
    foreach($oRepititionArray as $oRepetition){
      $strDisplay = $oRepetition->getName();
      $strDisplay = StringHelper::hasText($oRepetition->getStartDate()) ? $strDisplay . ': '.$oRepetition->getStartDate() : $strDisplay;
      $strDisplay = StringHelper::hasText($oRepetition->getEndDate()) ? $strDisplay . ' - '.$oRepetition->getEndDate() : $strDisplay;	
    ?>
      
      <option value="<?php echo $oRepetition->getId(); ?>"><?php echo $strDisplay; ?></option>
    <?php 
    }   
  ?>
</select>
