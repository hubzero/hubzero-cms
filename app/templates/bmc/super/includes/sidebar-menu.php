<?php
/**
 * Sidebar Menu
 *
 * Template used for Special Groups. Will now be auto-created
 * when admin switches group from type HUB to type Special.
 *
 * @author     M. Drew LaMar
 * @copyright  December 2016
 */

use Components\Groups\Helpers\View;

// Rather than rewrite this code, gonna do some string magic.
$something = View::displaySections($this->group, 'class="cf"');
$pos1 = strpos($something, 'group-overview-tab');	// Find overview-tab
$pos2 = strpos($something, 'group-', $pos1 + strlen('group-overview-tab')); // Find next group area
$fred = substr($something, 0, $pos2); // Consider string up to that point
$pos3 = strrpos($fred, "<li class=");	// Search backwards for beginning of list item
$community = substr($something, $pos3);	// Cut off string up to this point
?>

<ul class="sidebar-nav nav-pills nav-stacked js" id="page-menu">
  <!-- Hamburger menu for mobile -->
  <button class="more-menu"></button>
<?php echo $community; ?>
  <!-- Menu for additional group links for mobile -->
  <ul class="more-links"></ul>
</ul>
