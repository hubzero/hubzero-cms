<?php

// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.calendar'); ?>

<div id="mainpage-facilities-header">
	<h2> <?php if ($this->experimentid > -1) echo 'Edit'; else echo 'Add' ?>  Experiment</h2>
</div>


<?php echo $this->tabs;?>

<div id="mainpage-facilities-main">

    <h3> <?php if ($this->experimentid > -1) echo 'Edit'; else echo 'Add' ?>  Experiment</h3>
    <hr>

    <?php if(JRequest::getVar('msg'))
            echo '<p class="passed">' . JRequest::getVar('msg', '', 'default', 'string', JREQUEST_ALLOWRAW) . '</p>';
    ?>

    <?php if(JRequest::getVar('errorMsg'))
            echo '<p class="failed">' . JRequest::getVar('errorMsg', '', 'default', 'string', JREQUEST_ALLOWRAW) . '</p>';
    ?>


    <form method="post">

        <input type="hidden" name="experimentid" value="<?php echo $this->experimentid?>">
        <input type="hidden" name="id" value="<?php echo $this->facilityid?>">
        <input type="hidden" name="task" value="updateexperiment">
        <input type="hidden" name="reEdit" value ="<?php if(strlen(JRequest::getVar('errorMsg')>0)) echo '1'; else echo '0';?>">

        <table id="form" style="border:0px; margin-bottom:25px; margin-top:25px;" cellpadding="0" cellspacing="0" width="800px" >

            <tr>
                <td nowrap="nowrap">Experiment Name<span class="requiredfieldmarker">*</span></td>
                <td width="100%">
                    <input style="width:600px;" maxlength="255" class="textentry" type="text" name="exp_name" value="<?php echo $this->name?>"/>
                </td>
            </tr>

            <tr>
                <td>Experiment Phase</td>
                <td>
                    <select name="exp_phase" class="selectbox">
                        <option value="DESIGN" <?php if($this->expphase == 'DESIGN' ) echo 'selected' ?>>Experimental Design</option>
                        <option value="FABRIC" <?php if($this->expphase == 'FABRIC' ) echo 'selected' ?>>Specimen Fabrication</option>
                        <option value="INSTRUMENT" <?php if($this->expphase == 'INSTRUMENT' ) echo 'selected' ?>>Specimen Instrumentation</option>
                        <option value="TESTING" <?php if($this->expphase == 'TESTING' ) echo 'selected' ?>>Active Testing</option>
                        <option value="DEMOLITION" <?php if($this->expphase == 'DEMOLITION' ) echo 'selected' ?>>Specimen Demolition</option>
                        <option value="ANALYSIS" <?php if($this->expphase == 'ANALYSIS' ) echo 'selected' ?>>Data Review/Interpretation</option>
                        <option value="COMPLETE" <?php if($this->expphase == 'COMPLETE' ) echo 'selected' ?>>Completed</option>
                    </select>
                </td>
            </tr>

            <tr>
                <td>Description</td>
                <td>
                    <textarea class="textentry" name="exp_descript" rows="10" style="width:600px;" wrap="virtual"><?php echo $this->exp_descript;?></textarea>
                </td>
            </tr>

            <tr>
                <td>Test Date/Time<span class="requiredfieldmarker">*</span></td>
                <td><input size="16" maxlength="16" class="textentry" type="text" id="test_dt" name="test_dt" value="<?php echo $this->testDate; ?>"/>
                <img align="absmiddle" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="Calendar" onclick="return showCalendar('test_dt', '%m-%d-%Y');" />

                (MM-DD-YYYY)

                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input size="5" maxlength="5" type="text" name="test_time" value="<?php echo $this->testTime;?>">  (HH:MM)

                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Time Zone
                <select name="test_tz" class="selectbox">
                  <option value="PST" <?php if($this->timezone == 'PST' ) echo 'selected' ?>>PST</option>
                  <option value="MST" <?php if($this->timezone == 'MST' ) echo 'selected' ?>>MST</option>
                  <option value="CST" <?php if($this->timezone == 'CST' ) echo 'selected' ?>>CST</option>
                  <option value="EST" <?php if($this->timezone == 'EST' ) echo 'selected' ?>>EST</option>
                  <option value="GMT" <?php if($this->timezone == 'GMT' ) echo 'selected' ?>>GMT</option>
                </select>
              </td>
            </tr>

            <tr>
                <td>Start Date<span class="requiredfieldmarker">*</span></td>

                <td>
                    <input size="10" maxlength="10" class="textentry" type="text" id="test_start" name="test_start" value="<?php echo $this->startDate; ?>"/>
                    <img align="absmiddle" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="Calendar" onclick="return showCalendar('test_start', '%m-%d-%Y');" />
                    (MM-DD-YYYY)
                </td>
            </tr>
            <tr>
                <td>End Date</td>
                <td><input size="10" maxlength="10" class="textentry" type="text" id="test_end" name="test_end" value="<?php echo $this->endDate; ?>"/>
                    <img align="absmiddle" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="Calendar" onclick="return showCalendar('test_end', '%m-%d-%Y');" />
                    (MM-DD-YYYY)</td>
            </tr>
            <tr>
                <td>Contact Name<span class="requiredfieldmarker">*</span></td>
                <td>
                    <input size="36" maxlength="254" class="textentry" type="text" name="contact_name" value="<?php echo $this->contactName; ?>"/>
                </td>
            </tr>

            <tr>
                <td>Contact Email<span class="requiredfieldmarker">*</span></td>
                <td><input size="36" maxlength="254" class="textentry" type="text" name="contact_email" value="<?php echo $this->contactEmail; ?>"/></td>
            </tr>

            <tr>
                <td>Movie URL</td>
                <td><input size="36" maxlength="254" class="textentry" type="text" name="movie_url" value="<?php echo $this->movieURL; ?>"/></td>
            </tr>

            <tr>
                <td>Feeds available</td>
                <td><input name="active" type="checkbox" <?php if ($this->feedsAvailable == 1) echo 'checked'; ?>  /></td>

            </tr>

        </table>

            <input type="submit" value="Save" />
            <input type="button" value="Cancel" onclick="history.back();" />

    </form>



</div>







