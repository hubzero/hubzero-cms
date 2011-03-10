<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $category = (stripos($_SERVER['REQUEST_URI'],'/metrics') !== false)? "Metrics" : "Course" ?>
<?php 
		$teachersString = ($category == "Metrics")? "Section Managers" : "Course Teachers";
		$topicsString = ($category == "Metrics")? "Section Information" : "Course Topics";
		$joinString = ($category == "Metrics")? "Enroll in Metric Reporting Section" : "Enroll into Course" 


?>
<?php
$assignment = $this->assignment_info;
$itemid = JoomdleHelperContent::getMenuItem();

$user = &JFactory::getUser();
$user_logged = $user->id;

?>

<?php 

?>

<h2 id="joomdlesectionheader" class="joomdlecourseheader"> <?php echo $assignment['fullname']; ?></h2>

<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr><td><em>Assigment Details</em></td></tr>
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">       
        </td>
</tr>

<tr>
        <td width="60%" colspan="2">
        <b><?php echo "Summary" ?>:&nbsp;</b><?php echo $assignment['summary']; ?>
        </td>
</tr>
</table>
