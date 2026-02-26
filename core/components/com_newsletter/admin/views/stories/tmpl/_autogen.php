<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Newsletter\Helpers\Permissions::getActions('story');

$text = ($this->task == 'edit' ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));

Toolbar::title(Lang::txt('COM_NEWSLETTER_STORY_' . strtoupper($this->type)) . ': ' . $text, 'newsletter');
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();

$this->css();
$this->js('autogen-story.js');
?>
<?php if (count($this->enabledSources) > 0) : ?>
    <?php
    $formAction = Route::url('index.php?option=' . $this->option);
    $unsavedMsg = Lang::txt('COM_NEWSLETTER_WANRING_UNSAVED_CHANGES');
    ?>
    <form
        action="<?php echo $formAction; ?>"
        method="post"
        name="adminForm"
        id="autogen-form"
        data-formwatcher-message="<?php echo $unsavedMsg; ?>"
    >
        <div class="grid">
            <div class="col span6">
                <fieldset class="adminform">
                    <legend><?php echo Lang::txt('COM_NEWSLETTER_STORY_SETTINGS'); ?>:</legend>

                    <div class="input-wrap">
                        <?php $sourceLabel = Lang::txt('COM_NEWSLETTER_STORY_SOURCE'); ?>
                        <label for="newsletter-contentSource required"><?php echo $sourceLabel; ?>:</label>
                        <select id="contentSource" name="contentSource">
                            <option value='none'><?php echo Lang::txt('MAKE_A_SELECTION'); ?></option>
                        <?php foreach ($this->enabledSources as $source) : ?>
                            <option value="<?php echo $source; ?>"><?php echo $source; ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-wrap">
                        <label for="story-title"><?php echo Lang::txt('COM_NEWSLETTER_STORY_TITLE'); ?>:</label>
                        <input type="text" name="title" id="story-title">
                    </div>

                    <div class="input-wrap">
                        <?php $itemCountLabel = Lang::txt('COM_NEWSLETTER_STORY_ITEM_COUNT'); ?>
                        <label for="story-itemCount"><?php echo $itemCountLabel; ?>:</label>
                        <input type="text" name="itemCount" id="itemCount" value="5">
                    </div>

                    <div class="input-wrap">
                        <?php $layoutLabel = Lang::txt('COM_NEWSLETTER_STORY_LAYOUT_TEMPLATE'); ?>
                        <label for="storyLayout"><?php echo $layoutLabel; ?>:</label>
                        <select id="storyLayout" name="layout">
                        <?php foreach ($this->layouts as $layout) : ?>
                            <option value="<?php echo $layout; ?>"><?php echo $layout; ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>

                <input type="hidden" name="story[]" value="" />
                <input type="hidden" name="nid" value="<?php echo $this->id; ?>" />
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="type" value="autogen" />
                <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
                <input type="hidden" name="task" value="save" />

                <?php echo Html::input('token'); ?>
            </div>

            <div class="col span6">
                <fieldset class="adminform">
                    <legend><?php echo Lang::txt('COM_NEWSLETTER_STORY_PREVIEW'); ?></legend>
                    <div id="previewArea">
                        <span id="previewStoryTitle"></span>
                        <span id="previewContentArea"></span>
                    </div>
                </fieldset>
            </div>
        </div>
    </form>
<?php else : ?>
    <div class="warning">
        <?php echo Lang::txt('COM_NEWSLETTER_WANRING_NO_SOURCE_PLUGINS'); ?>
    </div>
<?php endif;
