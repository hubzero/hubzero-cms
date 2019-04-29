<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get block properties
$complete = $this->pub->curation('blocks', $this->step, 'complete');
$required = $this->pub->curation('blocks', $this->step, 'required');

$elName = "authorList";
?>

<!-- Load content selection browser //-->
<div id="<?php echo $elName; ?>" class="blockelement<?php
	echo $required ? ' el-required' : ' el-optional';
	echo $complete ? ' el-complete' : ' el-incomplete';
	?> freezeblock">
	<?php if (count($this->pub->_authors) > 0) { ?>
		<div class="list-wrapper">
			<ul class="itemlist" id="author-list">
				<?php
				$i= 1;
				foreach ($this->pub->_authors as $author)
				{
					$org = $author->organization ? $author->organization : $author->p_organization;
					$name = $author->name ? $author->name : $author->p_name;
					$name = trim($name) ? $name : $author->invited_name;
					$name = trim($name) ? $name : $author->invited_email;

					$active    = in_array($author->project_owner_id, $this->teamids) ? true : false;
					$confirmed = $author->user_id ? true : false;

					$details = $author->credit ? stripslashes($author->credit) : null;
					?>
					<li>
						<span class="item-order"><?php echo $i; ?></span>
						<span class="item-title"><?php echo $name; ?> <span class="item-subtext"><?php echo $org ? ' - ' . $org : ''; ?></span></span>
						<span class="item-details"><?php echo $details; ?></span>
					</li>
					<?php
					$i++;
				}
				?>
			</ul>
		</div>
	<?php } else { ?>
		<p class="nocontent"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_NONE'); ?></p>
	<?php } ?>
</div>