<?php 
// no direct access
defined('_JEXEC') or die('Restricted access');

$itemid = JoomdleHelperContent::getMenuItem();

/*
if (!$itemid)
{
	$itemid = $default_itemid;
	$menu = &JSite::getMenu();
	$menu->setActive ($itemid);
}
*/

if ($linkstarget == "new")
	$target = " target='_blank'";
else $target = "";

if ($linkstarget == 'wrapper')
	$open_in_wrapper = 1;
else
	$open_in_wrapper = 0;

	echo '<ul class="menu">';

	$i = 0;
	if (is_array($cursos))
	foreach ($cursos as $id => $curso) {
		$id = $curso['remoteid'];
		if ($linkto == 'moodle')
		{
			if ($username)
			{
				echo "<li><a $target href=\"".$moodle_auth_land_url."?username=$username&token=$token&mtype=course&id=$id&use_wrapper=$open_in_wrapper&create_user=1&Itemid=$itemid\">".$curso['fullname']."</a></li>";
			}
			else
				if ($open_in_wrapper)
					echo "<li><a $target href=\"".$moodle_auth_land_url."?username=guest&mtype=course&id=$id&use_wrapper=$open_in_wrapper&Itemid=$itemid\">".$curso['fullname']."</a></li>";
				else
					echo "<li><a $target href=\"".$moodle_url."/course/view.php?id=$id\">".$curso['fullname']."</a></li>";
		} else {
			$url = JRoute::_("index.php?option=com_joomdle&view=detail&cat_id=".$curso['cat_id'].":".JFilterOutput::stringURLSafe($curso['cat_name'])."&course_id=".$curso['remoteid'].':'.JFilterOutput::stringURLSafe($curso['fullname'])."&Itemid=$itemid"); 
		//	$url = JRoute::_("index.php?option=com_joomdle&view=detail&cat_id=".$curso['cat_id'].":".JFilterOutput::stringURLSafe($curso['cat_name'])."&course_id=".$curso['remoteid'].':'.JFilterOutput::stringURLSafe($curso['fullname'])); 
			echo "<li><a href=\"".$url."\">".$curso['fullname']."</a></li>";
		}
		$i++;
		if ($i >= $limit) // Show only this number of latest courses
			break; 
	}

	echo "</ul>";
?>
