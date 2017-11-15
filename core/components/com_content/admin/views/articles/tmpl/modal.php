<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_HZEXEC_') or die();

if (App::isSite())
{
	Session::checkToken('get') or die(Lang::txt('JINVALID_TOKEN'));
}

require_once PATH_CORE . '/components/com_content/site/helpers/route.php';

Html::addIncludePath(PATH_CORE . '/components/com_content/admin/helpers/html');
Html::behavior('tooltip');

$function  = Request::getCmd('function', 'jSelectArticle');
$listOrder = $this->escape($this->filters['sort']);
$listDirn  = $this->escape($this->filters['sort_Dir']);
?>
<h2 class="modal-title"><?php echo Lang::txt('Select Article'); ?></h2>
<form action="<?php echo Route::url('index.php?option=com_content&view=articles&layout=modal&tmpl=component&function='.$function.'&'.Session::getFormToken().'=1');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar" class="filter clearfix">
		<div class="grid">
			<div class="col span5">
				<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" size="30" placeholder="<?php echo Lang::txt('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />

				<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="col span7">
				<select name="filter_access" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
					<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->filters['access']);?>
				</select>

				<select name="filter_published" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
					<?php echo Html::select('options', Html::grid('publishedOptions'), 'value', 'text', $this->filters['published'], true);?>
				</select>

				<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('JOPTION_SELECT_CATEGORY');?></option>
					<?php echo Html::select('options', Html::category('options', 'com_content'), 'value', 'text', $this->filters['category_id']);?>
				</select>

				<select name="filter_language" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('JOPTION_SELECT_LANGUAGE');?></option>
					<?php echo Html::select('options', Html::contentlanguage('existing', true, true), 'value', 'text', $this->filters['language']);?>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th class="title">
					<?php echo Html::grid('sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'JCATEGORY', 'a.catid', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->items as $i => $item) : ?>
				<?php
				if ($item->language && Lang::isMultilang()) {
					$tag = strlen($item->language);
					if ($tag == 5) {
						$lang = substr($item->language, 0, 2);
					}
					elseif ($tag == 6) {
						$lang = substr($item->language, 0, 3);
					}
					else {
						$lang = "";
					}
				}
				elseif (!Lang::isMultilang()) {
					$lang = "";
				}
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>', '<?php echo $this->escape($item->catid); ?>', null, '<?php echo $this->escape(ContentHelperRoute::getArticleRoute($item->id, $item->catid, $item->language)); ?>', '<?php echo $this->escape($lang); ?>', null);">
							<?php echo $this->escape($item->title); ?>
						</a>
					</td>
					<td class="center">
						<?php echo $this->escape($item->accessLevel->title); ?>
					</td>
					<td class="center">
						<?php echo $this->escape($item->category->title); ?>
					</td>
					<td class="center">
						<?php if ($item->language=='*'):?>
							<?php echo Lang::txt('JALL', 'language'); ?>
						<?php else:?>
							<?php echo $item->language_title ? $this->escape($item->language_title) : Lang::txt('JUNDEFINED'); ?>
						<?php endif;?>
					</td>
					<td class="center nowrap">
						<?php echo Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_LC4')); ?>
					</td>
					<td class="center">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

	<?php echo Html::input('token'); ?>
</form>
