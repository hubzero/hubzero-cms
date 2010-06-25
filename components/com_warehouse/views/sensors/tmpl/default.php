<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<p>Sensor List</p>

<?php 
  $oLocationCollectionArray = $this->locationArray;
  
?>

<p><a href="/warehouse/experiment/<?php echo $this->experimentId; ?>/project/<?php echo $this->projectId; ?>">Return</a></p>


<div style="border: 1px solid rgb(102, 102, 102); overflow: auto; width: 90%; padding: 0px; margin: 0px;">
  <table cellpadding="1" cellspacing="1" style="width:100%;border-bottom:0px;border-top:0px;">
    <tr valign="top" style="background: #CCCCCC;">
      <td class="columnHead" style="width:25%;">Sensor ID</td>
      <td class="columnHead" style="width:25%;">Type</td>
      <td class="columnHead" style="width:25%;">Orientation</td>
      <td class="columnHead" style="width:25%;">XYZ Coordinates</td>
    </tr>
    
    <?php 
      foreach($oLocationCollectionArray as $iIndex=>$strLocationArray){
      	$strBackgroundColor = "";
      	if($iIndex %2 ==0){
      	  $strBackgroundColor = "#EFEFEF";
      	}
      	?>
      	  <tr valign="top" style="background: <?php echo $strBackgroundColor; ?>;">
		    <td><?php echo $strLocationArray["LABEL"]; ?></td>
		    <td><?php echo $strLocationArray["TYPE"]; ?></td>
		    <td><?php echo $strLocationArray["ORIENTATION0"].", ".$strLocationArray["ORIENTATION1"].", ".$strLocationArray["ORIENTATION2"]; ?></td>
		    <td><?php echo $strLocationArray["X"].", ".$strLocationArray["Y"].", ".$strLocationArray["Z"]; ?></td>
		  </tr>
      	<?php 
      }
    ?>
    
  </table>
</div>  