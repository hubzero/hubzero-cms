<?php

// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>



<div id="mainpage-facilities-header">
	<h2>Edit Site Status</h2>
</div>


<?php echo $this->tabs;?>

<div id="mainpage-facilities-main">


    <h3 style="margin-bottom:28px;">Update status for <?php echo $this->facilityName; ?></h3>

    <form method="post">

        <input type="hidden" name="id" value="<?php echo $this->facilityid?>">
        <input type="hidden" name="task" value="updatesitestatus">

        <select class="selectbox" name="optstat">
            <option value="NEES" <?php echo (($this->status == 'NEES') ? 'selected=' : '')?>>NEES Experiment Today</option>
            <option value="FLEX" <?php echo (($this->status == 'FLEX') ? 'selected' : '')?>>NEES Research Activities</option>
            <option value="SHARED" <?php echo (($this->status == 'SHARED') ? 'selected' : '')?>>NEES Support Activities</option>
            <option value="NON_NEES" <?php echo (($this->status == 'NON_NEES') ? 'selected' : '')?>>Non-NEES Activities</option>
        </select>

        <input type="submit" value="Save" />
        <input type="button" value="Cancel" onclick="history.back();" />
    </form>

</div>


<div id="event-info" class="event-info">
  	<div id="event-info-header" class="event-info-header"></div>
  	<div id="event-info-body" class="event-info-body"></div>
</div>





