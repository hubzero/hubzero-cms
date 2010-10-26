<?php defined('_JEXEC') or die('Restricted access'); ?>


<h2><?php echo $this->FacilityName; ?></h2>

<?php echo $this->tabs;?>

<div id="facility-subpage-primarycontent">


<h2>Change Site Contact</h2>
<hr style="padding-bottom:10px;">

    <form method="post">

        <input type="hidden" name="id" value="<?php echo $this->facility->getId();?>"/>
        <input type="hidden" name="task" value="savesitecontact">

        <select name="new_contact_id">
            <?php echo $this->contactPersonOptionsList ?>
        </select>

        <input type="submit" name="submitbutton" value="Save" />
        <input type="submit" name="submitbutton" value="Cancel" />
        
    </form>

</div>


