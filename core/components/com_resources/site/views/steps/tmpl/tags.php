<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

include_once Component::path('com_resources') . DS . 'helpers' . DS . 'recommendedtags.php';

if (!function_exists('stem'))
{
	function stem($str)
	{
		return preg_replace('/^(?:a[bdcfglnpst]?|ant[ei]?|be|co[mlnr]?|de|di[as]?|e[nmxf]|extra|hemi|hyper|hypo|over|peri|post|pr[eo]|re|semi|su[bcfgprs]|sy[nm]|trans|ultra|un|under)+/', '', preg_replace('/(?:e[dr]|ing|e?s|or|ator|able|ible|acious|ary|ate|ation|cy|eer|or|escent|fic|fy|iferous|ile?|ism|ist|ity|ive|ise|ize|oid|ose|osis|ous|tude)+$/', '', $str));
	}
}

$this->css('create.css')
     ->js('create.js')
     ->js('tags.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft'); ?>">
				<?php echo Lang::txt('COM_CONTRIBUTE_NEW_SUBMISSION'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<section class="main section">
	<?php
		$this->view('steps')
		     ->set('option', $this->option)
		     ->set('step', $this->step)
		     ->set('steps', $this->steps)
		     ->set('id', $this->id)
		     ->set('resource', $this->row)
		     ->set('progress', $this->progress)
		     ->display();

	$recommended = new \Components\Resources\Helpers\RecommendedTags($this->id, $this->existing);

	function fa_controls($idx, $fas, $fa_props, $existing, $parent = null, $depth = 1)
	{
		foreach ($fas as $fa)
		{
			$props = $fa_props[$fa['label']];
			$multiple = !is_null($props['multiple_depth']) && $props['multiple_depth'] <= $depth;
			echo '<div class="fa'.($depth === 1 ? ' top-level' : '').'">';
			echo '<input class="option" class="'.($multiple ? 'checkbox' : 'radio').'" type="'.($multiple ? 'checkbox' : 'radio').'" '.(isset($existing[strtolower($fa['raw_tag'])]) ? 'checked="checked" ' : '' ).'id="tagfa-'.$idx.'-'.$fa['tag'].'" name="tagfa-'.$idx.($parent ? '-'.$parent : '').'[]" value="' . $fa['tag'] . '"';
			echo ' /><label for="tagfa-'.$idx.'-'.$fa['tag'].'"' . ($fa['description'] ? ' title="' . htmlentities($fa['description']) . '" class="tooltips"' : '') . '>'.$fa['raw_tag'].'</label>';
			if ($fa['children'])
			{
				echo fa_controls($idx, $fa['children'], $fa_props, $existing, $fa['tag'], $depth + 1);
			}
			echo '</div>';
		}
	}
	?>
<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft&step=' . $this->next_step . '&id=' . $this->id); ?>" method="post" id="hubForm">
		<div class="explaination">
			<h4><?php echo Lang::txt('COM_CONTRIBUTE_TAGS_WHAT_ARE_TAGS'); ?></h4>
			<p><?php echo Lang::txt('COM_CONTRIBUTE_TAGS_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
			<input type="hidden" name="step" value="<?php echo $this->next_step; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->id; ?>" />

			<legend><?php echo Lang::txt('COM_CONTRIBUTE_TAGS_ADD'); ?></legend>
			<?php
				if (count($this->fas) > 0):
					$fa_existing = $recommended->get_existing_focus_areas_map();
					$fa_props = $recommended->get_focus_area_properties();
					$idx = 0;
					foreach ($this->fas as $label => $fas):
					?>
						<fieldset>
							<legend><?php echo 'Select '.$label.': '.($fa_props[$label]['mandatory_depth'] ? '<span class="required">required</span>' : ''); ?></legend>
							<?php fa_controls(++$idx, $fas, $fa_props, $fa_existing); ?>
						</fieldset>
					<?php
					endforeach;
				endif;
			?>
			<label>
				<?php echo Lang::txt('COM_CONTRIBUTE_TAGS_ASSIGNED'); ?>:
				<?php
				$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags','',$recommended->get_existing_tags_value_list())));

				if (count($tf) > 0) {
					echo $tf[0];
				} else {
					echo '<textarea name="tags" id="tags-men" rows="6" cols="35">'. $recommended->get_existing_tags_value_list() .'</textarea>'."\n";
				}
				//echo '<input type="text" name="tags" rel="tags,multi," id="actags" class="autocomplete " value="'.$recommended->get_existing_tags_value_list().'" autocomplete="off" />';
				?>
			</label>
			<p><?php echo Lang::txt('COM_CONTRIBUTE_TAGS_NEW_EXPLANATION'); ?></p>
			<?php if (($rec = $recommended->get_tags())): ?>
			<p>Suggested tags: <span class="js-only">(click to add to your contribution)</span></p>
			<ul class="suggested tags">
				<?php foreach ($rec as $tag): ?>
				<li><a class="suggested-tag" href=""><?php echo $tag['text']; ?></a></li>
				<?php endforeach; ?>
			</ul>
			<div class="clear"></div>
			<?php endif; ?>
		</fieldset><div class="clear"></div>

		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('COM_CONTRIBUTE_NEXT'); ?>" />
		</p>
	</form>
</section><!-- / .main section -->
