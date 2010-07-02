<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->facility->getName(); ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

	<h3><?= $this->isMajor ?>: <?= $this->equipName ?></h3>
 	
	
	<div style="padding-left:25px; padding-bottom:10px; margin-top:10px; border: thin solid black; width:850px;">
		
		<div style="padding-top:10px;">
			<table style="width:700px; border: 0px;">
				<?php echo $this->showEquipInfo($this->equipment); ?>
			</table>
		</div>
			
		<div style="padding-left:0px;">
			<?php echo $this->fileSection; ?>
		</div>
	</div>
	
	
	<?php 
	if (count($this->subequipList) > 0) 
	{
		
		echo '<div style="padding-top:25px;">';
		echo '	<h3 style=\"margin-top:10px;\">Subcomponent Listing</h3>';
    	echo '</div>';
    	

		foreach($this->subequipList as $subequip) {

			echo '<div style="margin-top:10px; margin-bottom:20px; border: thin solid black; width:850px;">';
			
			//print $header;
			$subEquipId = $subequip->getId();
			$subsubEquip = EquipmentPeer::findAllByParent($subEquipId);
			
			echo 	'<div style="padding-left:10px; padding-bottom:10px; padding-top:10px; margin-bottom:10px; font-weight:bold; background-color:#eee;"><a href="/sites/equipment/' . $this->facilityID . 
					'?equipid=' . $subEquipId . '">' . $subequip->getName() . '</a></div>';
			
					
		/***	
			if(count($subsubEquip) > 0) {
	        	echo 	'&nbsp;<a href="/?facid='. $this->facilityID .
	        			'&equipid='. $subEquipId .
	       			'&action=DisplayEquipmentList">(view subcomponents)</a>';
			}
		***/

			
			echo '<div style="padding-bottom: 20px; padding-left:20px">';
			echo '<table style="width:500px; border-width: 0px;">';
			echo $this->showEquipInfo($subequip);
			echo '</table>';
			
			echo $this->printDocumentationList($subequip, $this->facility);
			echo '</div>';
			    
			echo '</div>';
		
			//echo '</td>';
			//echo '<td>' . $subequip->getEquipmentModel()->getEquipmentClass()->getClassName() . '</td>';
			//echo '</table>';
			
		} // end for each

		
		
	} // end if

	?>




















	
</div>