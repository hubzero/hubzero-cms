<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<?php echo $this->tabs; ?>

<div class="siteReportsMainPageConent">

<h3>Welcome to the NEES Site Reporting Module</h3>

<?php

// The bootstrap sitereports.php file for this component will always redirect here.
// This code will check for user rights
$SiteReportsSite = SiteReportsHelper::getSiteReportsSite(1002);
$can_run = SiteReportsHelper::canRun($SiteReportsSite);

if(!$can_run)
{
    echo "<br/>Access denied";
}

?>

</div>