<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$prov = $this->pub->_project->isProvisioned() ? 1 : 0;

// Get block properties
$step 	  = $this->step;
$block	  = $this->pub->_curationModel->_progress->blocks->$step;
$complete = $block->status->status;
$name	  = $block->name;

$props = $name . '-' . $this->step;

$required = $this->manifest->params->required;

$elName = "citationsPick";

?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="blockelement<?php echo $required ? ' el-required' : ' el-optional';
echo $complete ? ' el-complete' : ' el-incomplete'; ?> freezeblock">
<?php if (count($this->pub->_citations) > 0) {
	$i= 1;
	$formatter = new \Components\Citations\Helpers\Format;
	$formatter->setTemplate($this->pub->_citationFormat);
	?>
	<div class="list-wrapper">
		<ul class="itemlist" id="citations-list">
		<?php foreach ($this->pub->_citations as $cite) {

				$citeText = $cite->formatted
							? '<p>' . $cite->formatted . '</p>'
							: \Components\Citations\Helpers\Format::formatReference($cite, '');
			 ?>
			<li>
				<span class="item-title citation-formatted"><?php echo $citeText; ?></span>
			</li>
	<?php	$i++; } ?>
		</ul>
	</div>
	<?php  } else {
		echo '<p class="nocontent">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_NONE') . '</p>';
	} ?>
</div>
