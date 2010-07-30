<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<p>Drawing List</p>

<p><a href="/warehouse/experiment/<?php echo $this->experimentId; ?>/project/<?php echo $this->projectId; ?>">Return</a></p>

<div style="border: 0px solid rgb(102, 102, 102); overflow: auto; width: 90%; padding: 0px; margin: 0px;">
  <table cellpadding="1" cellspacing="1">
    <thead>
      <th>Title</th>
      <th>Description</th>
    </thead>
    <?php
      $oDrawingArray =  unserialize($_REQUEST["Drawings"]);
      foreach($oDrawingArray as $iDrawingIndex=>$oDrawing){
        //$strBgColor = "";
        $strBgColor = "odd";
      	if($iDrawingIndex%2 === 0){
      	  //$strBgColor = "#EFEFEF";
          $strBgColor = "even";
      	}
      	$strDrawingUrl = $oDrawing->getPath()."/".$oDrawing->getName();
        $strDrawingUrl = str_replace("/nees/home/",  "",  $strDrawingUrl);
        $strDrawingUrl = str_replace(".groups",  "",  $strDrawingUrl);
      ?>
        <tr class="<?php echo $strBgColor; ?>">
          <td><a rel="lightbox"  title="<?php echo $oDrawing->getTitle(); ?>" href="/data/get/<?php echo $strDrawingUrl; ?>" title=""><?php echo $oDrawing->getTitle(); ?></a></td>
          <td><?php echo $oDrawing->getDescription(); ?></td>
        </tr>  
      <?php 
      }
    ?>
  </table>
</div>