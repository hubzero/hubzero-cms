<?php
// No direct access
defined('_JEXEC') or die('Restricted access'); 
?>

<?php echo $this->tabs; ?>

<div class="siteReportsMainPageConent">



<form method="post">


    <table style="width:400px; border:0px; margin-top:25px;">
        <tr>
            <td>
                Report:
            </td>
            <td>
                <select name="report">
                    <option value="generateQFRSummary">Generate QFR Summary</option>
                    <option value="generateQARSummary">Generate QAR Summary</option>
                </select>
            </td>
        </tr>


        <tr>
            <td>
                Year:
            </td>
            <td>
                <select name="year">
                    <option value="2010">2010</option>
                    <option value="2011">2011</option>
                    <option value="2012">2012</option>
                    <option value="2013">2013</option>
                    <option value="2014">2014</option>
                </select>
            </td>
        </tr>

        <tr>
            <td>
                Period:
            </td>
            <td>
                <select name="period">
                    <option value="1">1st Quarter</option>
                    <option value="2">2nd Quarter</option>
                    <option value="3">3rd Quarter</option>
                    <option value="4">4th Quarter</option>
                </select>
            </td>
        </tr>


        <tr>
            <td colspan="2" style="border-top:1px #ccc solid; padding-top:10px; padding-bottom:10px;">
                <input type="submit" value="Run">
            </td>
        </tr>



    </table>

    <input type="hidden" name="task" value="runreport">
</form>



</div>






