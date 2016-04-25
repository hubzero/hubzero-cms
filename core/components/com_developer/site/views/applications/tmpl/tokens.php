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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// pagination
$total = $this->application->accessTokens()->count();
$filters = array(
	'limit' => Request::getInt('limit', 25),
	'start' => Request::getInt('limitstart', Request::getInt('start', 0))
);
$tokens = $this->application->accessTokens()
	->limit($filters['limit'], $filters['start'])
	->ordered()
	->paginated()
	->rows();
?>

<div class="subject full">
	<form action="<?php echo Route::url($this->application->link() . '&active=tokens'); ?>" method="get" name="adminForm" id="adminForm">
		<div class="container cf">
			<h3>
				<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_ACCESS_TOKENS'); ?> <span><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_AUTHORIZED_TO'); ?>:</span>

				<a class="btn btn-secondary revoke-all confirm btn-danger" data-txt-confirm="<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_REVOKE_ALL_TOKEN_CONFIRM'); ?>" href="<?php echo Route::url($this->application->link('revokeall')); ?>">
					<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_REVOKE_ALL_TOKEN'); ?>
				</a>
			</h3>
			<ul class="entries-list tokens access-tokens">
				<?php if ($total > 0) : ?>
					<?php foreach ($tokens as $token) : ?>
						<li>
							<h4>
								<?php echo Hubzero\User\User::oneOrNew($token->get('uidNumber'))->get('name'); ?>
							</h4>

							<a class="btn btn-secondary revoke confirm" data-txt-confirm="<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_REVOKE_TOKEN_CONFIRM'); ?>" href="<?php echo Route::url($this->application->link('revoke').'&token=' . $token->get('id').'&return=tokens'); ?>">
								<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_REVOKE_TOKEN'); ?>
							</a>

							<dl class="meta">
								<dd><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_ACCESS_TOKEN_CREATED', $token->created('m/d/Y @ g:ia')); ?></dd>
								<dd><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_ACCESS_TOKEN_EXPIRES', $token->expires('m/d/Y @ g:ia')); ?></dd>
							</dl>
						</li>
					<?php endforeach; ?>
				<?php else : ?>
					<li class="empty">
						<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_ACCESS_TOKENS_NONE'); ?>
					</li>
				<?php endif; ?>
			</ul>
			<?php
				// Initiate paging
				echo $tokens->pagination;
			?>
		</div>
	</form>
</div>