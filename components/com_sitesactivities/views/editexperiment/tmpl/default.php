<?php

// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>

<?php JHTML::_('behavior.calendar'); ?>

<div id="mainpage-facilities-header">
	<h2>Edit Experiment</h2>
</div>


<?php echo $this->tabs;?>

<div id="mainpage-facilities-main">

    <h3 style="margin-bottom:28px;">Update experiment</h3>
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

            </tr>

            <tr>
                <td>Test Date/Time<span class="requiredfieldmarker">*</span></td>
                <td><input size="16" maxlength="16" class="textentry" type="text" id="test_dt" name="test_dt" value=""/>
                <img align="absmiddle" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="Calendar" onclick="return showCalendar('test_dt', '%m-%d-%Y');" />

                (MM-DD-YYYY)

                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input size="5" maxlength="5" type="text" name="test_time">  (HH:MM)

                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Time Zone
                <select name="test_tz" class="selectbox">
                  <option value="PST" >PST</option>
                  <option value="EST" >EST</option>
                  <option value="MST" >MST</option>
                  <option value="CST" >CST</option>
                  <option value="GMT" >GMT</option>
                </select>
              </td>
            </tr>

            <tr>
                <td>Start Date<span class="requiredfieldmarker">*</span></td>

                <td>
                    <input size="10" maxlength="10" class="textentry" type="text" id="test_start" name="test_start" value=""/>
                    <img align="absmiddle" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="Calendar" onclick="return showCalendar('test_start', '%m-%d-%Y');" />
                    (MM-DD-YYYY)
                </td>
            </tr>
            <tr>
                <td>End Date</td>
                <td><input size="10" maxlength="10" class="textentry" type="text" id="test_end" name="test_end" value=""/>
                    <img align="absmiddle" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="Calendar" onclick="return showCalendar('test_end', '%m-%d-%Y');" />
                    (MM-DD-YYYY)</td>
            </tr>
            <tr>
                <td>Contact Name<span class="requiredfieldmarker">*</span></td>
                <td>
                    <input size="36" maxlength="254" class="textentry" type="text" name="contact_name" value=""/>
                </td>
            </tr>

            <tr>
                <td>Contact Email<span class="requiredfieldmarker">*</span></td>
                <td><input size="36" maxlength="254" class="textentry" type="text" name="contact_email" value=""/></td>
            </tr>

            <tr>
                <td>Movie URL</td>
                <td><input size="36" maxlength="254" class="textentry" type="text" name="movie_url" value=""/></td>
            </tr>

            <tr>
                <td>Feeds available</td>
                <td><input name="active" type="checkbox"  checked /></td>

            </tr>

        </table>

            <input type="submit" value="Save" />
            <input type="button" value="Cancel" onclick="history.back();" />

    </form>



</div>







