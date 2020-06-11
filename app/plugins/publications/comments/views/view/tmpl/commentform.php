<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<div class="addcomment hide" id="<?php echo $this->context; ?>-form<?php echo $this->comment->get('id'); ?>">
    <form action="<?php echo Route::url($this->comment->link('base')); ?>" method="post" enctype="multipart/form-data">
        <fieldset>
            <legend>
                <span><?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_YOUR_' . strtoupper($this->context)); ?></span>
            </legend>

            <input type="hidden" name="comment[id]" value="<?php echo ($this->context != 'edit' ? 0 : $this->comment->get('id')); ?>" />
            <input type="hidden" name="comment[item_id]" value="<?php echo $this->escape($this->comment->get('item_id')); ?>" />
            <input type="hidden" name="comment[item_type]" value="<?php echo $this->escape($this->comment->get('item_type')); ?>" />
            <input type="hidden" name="comment[parent]" value="<?php echo ($this->context != 'edit' ? $this->comment->get('id') : $this->comment->get('parent')); ?>" />
            <input type="hidden" name="comment[created]" value="<?php echo ($this->context != 'edit' ? '' : $this->comment->get('created')); ?>" />
            <input type="hidden" name="comment[created_by]" value="<?php echo ($this->context != 'edit' ? $this->escape(User::get('id')) : $this->comment->get('created_by')); ?>" />
            <input type="hidden" name="comment[state]" value="1" />
            <input type="hidden" name="comment[access]" value="1" />
            <input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
            <input type="hidden" name="id" value="<?php echo $this->obj->get('id'); ?>" />
            <input type="hidden" name="v" value="<?php echo $this->obj->get('version_number'); ?>" />
            <input type="hidden" name="active" value="comments" />
            <input type="hidden" name="action" value="commentsave" />
            <input type="hidden" name="no_html" value="1" />

            <?php echo Html::input('token'); ?>

            <label for="<?php echo $this->context; ?>_<?php echo $this->comment->get('id'); ?>_content">
                <?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_YOUR_' . strtoupper($this->context)); ?>:
                <?php
                echo $this->editor('comment[content]', ($this->context != 'edit' ? '' : $this->comment->get('content')), 35, 4, $this->context . '_' . $this->comment->get('id') . '_content', array('class' => 'minimal no-footer'));
                ?>
            </label>

            <div class="file-inputs">
                <label class="<?php echo $this->context; ?>-<?php echo $this->comment->get('id'); ?>-file" for="<?php echo $this->context; ?>-<?php echo $this->comment->get('id'); ?>-file">
                    <?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_ATTACH_FILE'); ?>
                    <input type="file" name="comment_file" id="<?php echo $this->context; ?>-<?php echo $this->comment->get('id'); ?>-file" />
                </label>
                <a href="#" class="detach_file" style="display: none;"></a>
            </div>

            <label class="reply-anonymous-label" for="<?php echo $this->context; ?>-<?php echo $this->comment->get('id'); ?>-anonymous">
                <input class="option" type="checkbox" name="comment[anonymous]" id="<?php echo $this->context; ?>-<?php echo $this->comment->get('id'); ?>-anonymous" value="1" <?php echo (($this->context == 'edit') && $this->comment->get('anonymous') ? 'checked' : ''); ?>/>
                <?php echo ($this->context != 'edit' ? Lang::txt('PLG_PUBLICATIONS_COMMENTS_POST_COMMENT_ANONYMOUSLY') : Lang::txt('PLG_PUBLICATIONS_COMMENTS_MAKE_COMMENT_ANONYMOUS')); ?>
            </label>

            <p class="submit">
                <input type="submit" value="<?php echo Lang::txt('PLG_PUBLICATIONS_COMMENTS_POST_' . strtoupper($this->context)); ?>" />
            </p>
        </fieldset>
    </form>
</div><!-- / .addcomment -->