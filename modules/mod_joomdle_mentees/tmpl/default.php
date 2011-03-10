<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$itemid = JoomdleHelperContent::getMenuItem();

if ($linkstarget == "new")
	$target = " target='_blank'";
else $target = "";

if ($linkstarget == 'wrapper')
	$open_in_wrapper = 1;
else
	$open_in_wrapper = 0;

	echo '<ul class="menu">';

	if (is_array($mentees))
	foreach ($mentees as $id => $mentee) {
		$id = $mentee['id'];
			if ($username)
			{
				echo "<li><a $target href=\"".$moodle_auth_land_url."?username=$username&token=$token&mtype=user&id=$id&use_wrapper=$open_in_wrapper&create_user=1&Itemid=$itemid\">".$mentee['name']."</a></li>";
			}
	}

	echo "</ul>";
?>
