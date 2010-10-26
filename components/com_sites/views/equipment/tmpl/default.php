<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo $this->facility->getName(); ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">

	<h2><?= $this->isMajor ?>: <?= $this->equipName ?></h2>

        <?php
            if($this->canedit)
                echo '<span style="padding-right:20px;"><a href="' .JRoute::_('/index.php?option=com_sites&id=' . $this->facilityID . '&view=editequipment&equipmentid=' . $this->equipmentID) . '">[edit]</a></span>';
        ?>

        <?php
            if($this->isMajor && $this->canedit)
                echo '<a href="' .JRoute::_('/index.php?option=com_sites&id=' . $this->facilityID . '&view=editequipment&equipmentid=-1&parentequipmentid=' . $this->equipmentID) . '">[add subcomponent]</a>';
        ?>


        <hr>
	
	<div style="padding-left:25px; padding-bottom:10px; margin-top:10px; width:850px;">
		
		<div style="padding-top:10px;">
			<table style="width:700px; border: 0px;">
				<?php echo $this->showEquipInfo($this->equipment); ?>
			</table>
		</div>
			
	</div>

        <div>
            <?php
                echo FacilityHelper::getViewSimpleFileBrowser($this->fileSection, "Equipment Documentation", $this->redirectURL)
            ?>
        </div>

        <?php if($this->allowCreate)
            echo '<a  style="padding-left: 0px; float: left;"href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $this->facilityID . '&equipmentid=' . $this->equipmentID . '&redirectURL=' . $this->redirectURL ) . '">[Add Document]</a>';
        ?>



	<?php 
        if (count($this->subequipList) > 0) {


            echo'<div style="padding-top:30px;"><h3>Subcomponent Listing</h3></div> <hr>';
            echo'<ul>';
            foreach ($this->subequipList as $subequip) {

                //FacilityHelper::CreateHideMoreSection('');

                //print $header;
                $subEquipmentId = $subequip->getId();
                $subsubEquip = EquipmentPeer::findAllByParent($subEquipmentId);

                echo '<li><a href="' . JRoute::_('index.php?option=com_sites&view=equipment&id=' . $this->facilityID . '&equipmentid=' . $subEquipmentId) . '">' . $subequip->getName() . '</a>';

                // Take out the subcomponent detail, re-add it if someone asks for it, this page was a sloppy nightmare
                // before
                //echo $this->showEquipInfo($subequip);
                //echo $this->printDocumentationList($subequip, $this->facility);

            } // end for each
            echo'</ul>';

        } // end if
?>


















	
</div>