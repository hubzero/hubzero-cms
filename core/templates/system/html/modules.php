<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

/*
 * none (output raw module content)
 */
function modChrome_none($module, &$params, &$attribs)
{
	echo $module->content;
}

/*
 * Module chrome that wraps the module in a table
 */
function modChrome_table($module, &$params, &$attribs)
{
	?>
	<table class="moduletable<?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?>">
		<?php if ($module->showtitle != 0) : ?>
			<thead>
				<tr>
					<th>
						<?php echo $module->title; ?>
					</th>
				</tr>
			</thead>
		<?php endif; ?>
		<tbody>
			<tr>
				<td>
					<?php echo $module->content; ?>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/*
 * xhtml (divs and font header tags)
 */
function modChrome_xhtml($module, &$params, &$attribs)
{
	$content = trim($module->content);
	if (!empty($module->content)) : ?>
		<div class="module<?php echo htmlspecialchars($params->get('moduleclass_sfx')); ?>">
			<?php if ($module->showtitle != 0) : ?>
				<h3><?php echo $module->title; ?></h3>
			<?php endif; ?>
			<?php echo $module->content; ?>
		</div>
	<?php endif;
}

/*
 * Module chrome that add preview information to the module
 */
function modChrome_outline($module, &$params, &$attribs)
{
	static $css = false;

	if (!$css)
	{
		$css = true;

		$browser = new \Hubzero\Browser\Detector();

		$doc = App::get('document');
		$doc->addStyleDeclaration(".mod-preview-info { padding: 2px 4px 2px 4px; border: 1px solid black; position: absolute; background-color: white; color: red;}");
		$doc->addStyleDeclaration(".mod-preview-wrapper { background-color:#eee; border: 1px dotted black; color:#700;}");
		if ($browser->name() == 'ie')
		{
			if ($browser->major() <= 7)
			{
				$doc->addStyleDeclaration(".mod-preview-info {filter: alpha(opacity=80);}");
				$doc->addStyleDeclaration(".mod-preview-wrapper {filter: alpha(opacity=50);}");
			}
			else
			{
				$doc->addStyleDeclaration(".mod-preview-info {-ms-filter: alpha(opacity=80);}");
				$doc->addStyleDeclaration(".mod-preview-wrapper {-ms-filter: alpha(opacity=50);}");
			}
		}
		else
		{
			$doc->addStyleDeclaration(".mod-preview-info {opacity: 0.8;}");
			$doc->addStyleDeclaration(".mod-preview-wrapper {opacity: 0.5;}");
		}
	}
	?>
	<div class="mod-preview">
		<div class="mod-preview-info"><?php echo $module->position."[".$module->style."]"; ?></div>
		<div class="mod-preview-wrapper">
			<?php echo $module->content; ?>
		</div>
	</div>
	<?php
}

/*
 * allows sliders
 */
function modChrome_sliders($module, &$params, &$attribs)
{
	$content = trim($module->content);
	if (!empty($content))
	{
		if ($params->get('automatic_title', '0')=='0')
		{
			echo Html::sliders('panel', $module->title, 'module'.$module->id);
		}
		elseif (method_exists('mod'.$module->name.'Helper', 'getTitle'))
		{
			echo Html::sliders('panel', call_user_func_array(array('mod'.$module->name.'Helper','getTitle'), array($params, $module)), 'module'.$module->id);
		}
		else
		{
			echo Html::sliders('panel', Lang::txt('MOD_'.$module->name.'_TITLE'), 'module'.$module->id);
		}
		echo $content;
	}
}

/*
 * allows tabs
 */
function modChrome_tabs($module, &$params, &$attribs)
{
	$content = trim($module->content);
	if (!empty($content))
	{
		if ($params->get('automatic_title', '0')=='0')
		{
			echo Html::tabs('panel', $module->title, 'module'.$module->id);
		}
		elseif (method_exists('mod'.$module->name.'Helper', 'getTitle'))
		{
			echo Html::tabs('panel', call_user_func_array(array('mod'.$module->name.'Helper', 'getTitle'), array($params)), 'module'.$module->id);
		}
		else
		{
			echo Html::tabs('panel', Lang::txt('MOD_'.$module->name.'_TITLE'), 'module'.$module->id);
		}
		echo $content;
	}
}
