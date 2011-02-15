<?php
// No direct access
defined('_JEXEC') or die('Restricted access'); 
?>

<?php echo $this->tabs; ?>

<div class="siteReportsMainPageConent" style="margin-top: 25px;">

    <div style="margin-bottom: 25px; margin-top: 25px;">
        <form id="filter" id="filter" "method="get">

            <input type="hidden" name="option" value="com_sitereports">
            <input type="hidden" name="view" value="sitesubmissions">

            <select name="reporttype">
                <option value="QAR"<?php if(JRequest::getvar('reporttype') == 'QAR') echo ' selected'; ?>>QAR</option>
                <option value="QFR"<?php if(JRequest::getvar('reporttype') == 'QFR') echo ' selected'; ?>>QFR</option>
            </select>

            <select name="year">
                <option value="2010"<?php if(JRequest::getvar('year') == '2010') echo ' selected'; ?>>2010</option>
                <option value="2011"<?php if(JRequest::getvar('year') == '2011') echo ' selected'; ?>>2011</option>
                <option value="2012"<?php if(JRequest::getvar('year') == '2012') echo ' selected'; ?>>2012</option>
                <option value="2013"<?php if(JRequest::getvar('year') == '2013') echo ' selected'; ?>>2013</option>
                <option value="2014"<?php if(JRequest::getvar('year') == '2014') echo ' selected'; ?>>2014</option>
            </select>

            <select name="period">
                <option value="1"<?php if(JRequest::getvar('period') == '1') echo ' selected'; ?>>Q1</option>
                <option value="2"<?php if(JRequest::getvar('period') == '2') echo ' selected'; ?>>Q2</option>
                <option value="3"<?php if(JRequest::getvar('period') == '3') echo ' selected'; ?>>Q3</option>
                <option value="4"<?php if(JRequest::getvar('period') == '4') echo ' selected'; ?>>Q4</option>
            </select>

            <input type="submit" value="Retrieve"/>

        </form>

        <hr>

    </div>


    <form id="injest" name="injest" method="post">
        <input type="hidden" name="option" value="com_sitereports">
        <input type="hidden" name="task" value="injest">

        <?php echo $this->htmlFileListing;?>

        <br/><br/><hr/>
        <input type="submit" value="Injest"/>

    </form>

</div>
