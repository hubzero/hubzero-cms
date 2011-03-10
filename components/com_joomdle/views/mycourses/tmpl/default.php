<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $category = (stripos($_SERVER['REQUEST_URI'],'/metrics') !== false)? "Metrics" : "Course" ?>
<?php 
		$titleString = ($category == "Metrics")? "My Sections/Courses" : "My Sections/Courses";
		$subTitleString = ($category == "Metrics")? "Sections/Courses" : "Sections/Courses";
?>
<?php
$itemid = JoomdleHelperContent::getMenuItem();
$linkstarget = $this->params->get( 'linkstarget' );
if ($linkstarget == "new")
	 $target = " target='_blank'";
 else $target = "";
?>
<?php //echo JText::_('CJ MY COURSES'); ?>
<h2 id="joomdlesectionheader"><?php echo $titleString;?></h2>
<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<tr>
		<td><em>This page will list all the courses/sections that you are currently enrolled in.</em></td>
</tr>
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<h3><?php echo $subTitleString;?></h3>
        </td>
</tr>

<?php
$odd = 0;
if (is_array ($this->my_courses) && !empty($this->my_courses)) {
foreach ($this->my_courses as $id => $curso) :
?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td align="left">
                <?php 
			$link = $this->jump_url."&mtype=course&id=".$curso['id']."&Itemid=$itemid";
			echo "<a $target href=\"$link\">".$curso['fullname']."</a>";
		?>

        </td>
</tr>


<?php endforeach; } else { ?>
	<tr><td><em>You are not enrolled in any courses</em></td></tr>
<?php }?>
</table>
