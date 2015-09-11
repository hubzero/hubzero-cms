<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

// pagination
$total = $this->application->accessTokens()->count();
$filters = array(
	'limit' => Request::getInt('limit', 25),
	'start' => Request::getInt('limitstart', Request::getInt('start', 0))
);
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
					<?php foreach ($this->application->accessTokens($filters) as $token) : ?>
						<li>
							<h4>
								<?php echo Hubzero\User\Profile::getInstance($token->get('uidNumber'))->get('name'); ?>
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
				$pageNav = $this->pagination(
					$total,
					$filters['start'],
					$filters['limit']
				);
				echo $pageNav->render();
			?>
		</div>
	</form>
</div>