<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$itemid = JoomdleHelperContent::getMenuItem();

if ($linkstarget == 'wrapper')
	$open_in_wrapper = 1;
else
	$open_in_wrapper = 0;

if ($linkstarget == "new")
	$target = " target='_blank'";
else $target = "";

$moodle_auth_land_url = $comp_params->get( 'MOODLE_URL' ).'/auth/joomdle/land.php';

        echo '<ul class="menu">';

        if (is_array($cursos)) {
		foreach ($cursos as $id => $curso) {
			$id = $curso['id'];

		echo "<li><a $target href=\"".$moodle_auth_land_url."?username=$username&token=$token&mtype=course&id=$id&use_wrapper=$open_in_wrapper&Itemid=$itemid\">".$curso['fullname']."</a></li>";
		}
	}

        echo "</ul>";


?>
