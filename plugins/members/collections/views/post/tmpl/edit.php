<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$item = $this->entry->item();

if (!$this->entry->exists())
{
	$this->entry->set('original', 1);
}

//tag editor
JPluginHelper::importPlugin('hubzero');
$dispatcher = JDispatcher::getInstance();

$tf = $dispatcher->trigger('onGetMultiEntry', array(array('tags', 'tags', 'actags','', $item->tags('string'))));

$type = 'file'; //strtolower(JRequest::getWord('type', $item->get('type')));
if (!$type)
{
	$type = 'file';
}
if ($type && !in_array($type, array('file', 'image', 'text', 'link')))
{
	$type = 'link';
}

$base = 'index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=' . $this->name;

$dir = $item->get('id');
if (!$dir)
{
	$dir = 'tmp' . time(); // . rand(0, 100);
}

$jbase = rtrim(JURI::getInstance()->base(true), '/');

$this->css()
     ->js();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
<form action="<?php echo JRoute::_($base . '&task=post/save' . ($this->no_html ? '&no_html=' . $this->no_html : '')); ?>" method="post" id="hubForm" class="full" enctype="multipart/form-data">
	<fieldset>
		<legend><?php echo $item->get('id') ? JText::_('Edit post') : JText::_('New post'); ?></legend>

		<?php if ($this->entry->get('original')) { ?>
			<div class="field-wrap">
				<div class="asset-uploader">
					<div class="grid">
						<div class="col span-half">
							<div id="ajax-uploader" data-txt-instructions="<?php echo JText::_('Click or drop file'); ?>" data-action="<?php echo $jbase; ?>/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=upload<?php //echo &amp;dir=$dir; ?>" data-list="<?php echo $jbase; ?>/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=list&amp;dir=<?php //echo $dir; ?>">
								<noscript>
									<label for="upload">
										<?php echo JText::_('File:'); ?>
										<input type="file" name="upload" id="upload" />
									</label>
								</noscript>
							</div>
							<script src="<?php echo $jbase; ?>/media/system/js/jquery.fileuploader.js"></script>
							<script src="<?php echo $jbase; ?>/plugins/members/collections/fileupload.js"></script>
						</div><!-- / .col span-half -->
						<div class="col span-half omega">
							<div id="link-adder" data-base="<?php echo rtrim(JURI::base(true), '/'); ?>" data-txt-delete="<?php echo JText::_('JACTION_DELETE'); ?>" data-txt-instructions="<?php echo JText::_('Click to add link'); ?>" data-action="<?php echo $jbase; ?>/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=create&amp;dir=<?php //echo $dir; ?>" data-list="<?php echo $jbase; ?>/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=list&amp;dir=<?php //echo $dir; ?>">
								<noscript>
									<label for="add-link">
										<?php echo JText::_('Add a link:'); ?>
										<input type="text" name="assets[-1][filename]" id="add-link" value="http://" />
										<input type="hidden" name="assets[-1][id]" value="0" />
										<input type="hidden" name="assets[-1][type]" value="link" />
									</label>
								</noscript>
							</div>
						</div><!-- / .col span-half -->
					</div>
				</div><!-- / .asset-uploader -->
			</div><!-- / .field-wrap -->
		<?php } ?>

		<div id="post-type-form">
			<div id="post-file" class="fieldset">

				<?php if ($this->entry->get('original')) { ?>
					<div class="field-wrap" id="ajax-uploader-list">
					<?php
						$assets = $item->assets();
						if ($assets->total() > 0)
						{
							$i = 0;
							foreach ($assets as $asset)
							{
					?>
						<p class="item-asset">
							<span class="asset-handle">
							</span>
							<span class="asset-file">
							<?php if ($asset->get('type') == 'link') { ?>
								<input type="text" name="assets[<?php echo $i; ?>][filename]" size="35" value="<?php echo $this->escape(stripslashes($asset->get('filename'))); ?>" placeholder="http://" />
							<?php } else { ?>
								<?php echo $this->escape(stripslashes($asset->get('filename'))); ?>
								<input type="hidden" name="assets[<?php echo $i; ?>][filename]" value="<?php echo $this->escape(stripslashes($asset->get('filename'))); ?>" />
							<?php } ?>
							</span>
							<span class="asset-description">
								<input type="hidden" name="assets[<?php echo $i; ?>][type]" value="<?php echo $this->escape(stripslashes($asset->get('type'))); ?>" />
								<input type="hidden" name="assets[<?php echo $i; ?>][id]" value="<?php echo $this->escape($asset->get('id')); ?>" />
								<a class="delete" data-id="<?php echo $this->escape($asset->get('id')); ?>" href="<?php echo JRoute::_($base . '&task=post/' . $this->entry->get('id') . '/edit&remove=' . $asset->get('id')); ?>" title="<?php echo JText::_('Delete this asset'); ?>">
									<?php echo JText::_('delete'); ?>
								</a>
								<!-- <input type="text" name="assets[<?php echo $i; ?>][description]" size="35" value="<?php echo $this->escape(stripslashes($asset->get('description'))); ?>" placeholder="Brief description" /> -->
							</span>
						</p>
					<?php
								$i++;
							}
						}
					?>
					</div><!-- / .field-wrap -->

					<label for="field-title">
						<?php echo JText::_('Title'); ?> <!-- <span class="optional">optional</span> -->
						<input type="text" name="fields[title]" id="field-title" size="35" value="<?php echo $this->escape(stripslashes($item->get('title'))); ?>" />
					</label>
				<?php } ?>

				<label for="field_description">
					<?php echo JText::_('Description'); ?> <!-- <span class="optional">optional</span> -->
				<?php if ($this->entry->get('original')) { ?>
					<?php echo \JFactory::getEditor()->display('fields[description]', $this->escape(stripslashes($item->description('raw'))), '', '', 35, 5, false, 'field_description', null, null, array('class' => 'minimal no-footer')); ?>
				<?php } else { ?>
					<?php echo \JFactory::getEditor()->display('post[description]', $this->escape(stripslashes($this->entry->description('raw'))), '', '', 35, 5, false, 'field_description', null, null, array('class' => 'minimal no-footer')); ?>
				<?php } ?>
				</label>
			<?php if ($this->task == 'save' && !$item->get('description')) { ?>
				<p class="error"><?php echo JText::_('PLG_MEMBERS_' . strtoupper($this->name) . '_ERROR_PROVIDE_CONTENT'); ?></p>
			<?php } ?>
				<input type="hidden" name="fields[type]" value="file" />
			</div><!-- / #post-file -->
		</div><!-- / #post-type-form -->

	<?php if ($this->entry->get('original')) { ?>
		<div class="grid">
			<div class="col span6">
	<?php } ?>

		<?php if ($this->collections->total() > 0) { ?>
			<label for="post-collection_id">
				<?php echo JText::_('Select collection'); ?>
				<select name="post[collection_id]" id="post-collection_id">
				<?php foreach ($this->collections as $collection) { ?>
					<option value="<?php echo $this->escape($collection->get('id')); ?>"<?php if ($this->collection->get('id') == $collection->get('id')) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($collection->get('title'))); ?></option>
				<?php } ?>
				</select>
				<span class="hint"><?php echo JText::_('Select from the list of collections you have access to.'); ?></span>
			</label>
		<?php } else { ?>
			<label for="post-collection_title">
				<?php echo JText::_('Create collection'); ?>
				<input type="text" name="collection_title" id="post-collection_title" value="" />
				<span class="hint"><?php echo JText::_('Create a collection for this post to go in.'); ?></span>
			</label>
		<?php } ?>

	<?php if ($this->entry->get('original')) { ?>
			</div>
			<div class="col span6 omega">
				<label>
					<?php echo JText::_('PLG_MEMBERS_' . strtoupper($this->name) . '_FIELD_TAGS'); ?>
					<?php
					if (count($tf) > 0) {
						echo $tf[0];
					} else { ?>
						<input type="text" name="tags" value="<?php echo $item->tags('string'); ?>" />
					<?php } ?>
					<span class="hint"><?php echo JText::_('PLG_MEMBERS_' . strtoupper($this->name) . '_FIELD_TAGS_HINT'); ?></span>
				</label>
			</div>
		</div>
	<?php } ?>
	</fieldset>

	<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $item->get('id'); ?>" />
	<input type="hidden" name="fields[created]" value="<?php echo $item->get('created'); ?>" />
	<input type="hidden" name="fields[created_by]" value="<?php echo $item->get('created_by'); ?>" />
	<input type="hidden" name="fields[dir]" id="field-dir" value="<?php echo $dir; ?>" />
	<input type="hidden" name="fields[access]" id="field-dir" value="<?php echo $item->get('access', 0); ?>" />

	<input type="hidden" name="post[id]" value="<?php echo $this->entry->get('id'); ?>" />
	<input type="hidden" name="post[item_id]" id="post-item_id" value="<?php echo $this->entry->get('item_id'); ?>" />

	<input type="hidden" name="id" value="<?php echo $this->member->get('uidNumber'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="active" value="<?php echo $this->name; ?>" />
	<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />
	<input type="hidden" name="action" value="save" />

	<?php echo JHTML::_('form.token'); ?>

	<p class="submit">
		<input class="btn btn-success" type="submit" value="<?php echo JText::_('PLG_MEMBERS_' . strtoupper($this->name) . '_SAVE'); ?>" />

		<?php if ($item->get('id')) { ?>
			<a class="btn btn-secondary" href="<?php echo JRoute::_($base . ($item->get('id') ? '&task=' . $this->collection->get('alias') : '')); ?>">
				<?php echo JText::_('Cancel'); ?>
			</a>
		<?php } ?>
	</p>
</form>
