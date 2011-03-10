<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
require_once(JPATH_SITE.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'content.php');


$moodle_xmlrpc_server_url = $this->params->get( 'MOODLE_URL' ).'/mnet/xmlrpc/server.php';


$user = & JFactory::getUser();
$id = $user->get('id');
$username = $user->get('username');

?>
<h2 id="joomdlesectionheader" class="joomdlecourse">Course Grades</h2>
<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<?php

$itemID = JoomdleHelperContent::getMenuItem();
$jumpURL = JoomdleHelperContent::getJumpURL ();
$course_info = JoomdleHelperContent::call_method ("get_course_info", $this->course_id);
$link = $jumpURL."&mtype=course&id=".$this->course_id."&Itemid=$itemID";

			
?>
<tr>
        <td width="90%" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<?php echo JText::_('CJ COURSE TASKS'); ?>:
               <?php  echo "<a href=\"$link\">".$course_info['fullname']."</a>"; ?>
        </td>
        <td width="30" height="20" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>" style="text-align:center;" nowrap="nowrap">
                <?php echo JText::_('CJ GRADE'); ?>
        </td>

<?php
$tareas = JoomdleHelperContent::call_method ("get_user_grades", $username, $this->course_id);

$odd = 0;
if (!$tareas)
	echo "<tr><td><br></td></tr>";
foreach ($tareas as  $tarea) :
if ($tarea['itemname']) : 
?>
<tr class="sectiontableentry<?php echo $odd + 1; ?>">
                <?php $odd++; $odd = $odd % 2; ?>
        <td height="20">
                <?php  echo $tarea['itemname']; ?>
        </td>
        <td height="20">
                <?php echo $tarea['finalgrade']; ?>

        </td>
</tr>
<?php endif; ?>
<?php endforeach; ?>


</table>

