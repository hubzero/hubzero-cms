<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
	->rows()
	->sort('expires', false);
?>

<div class="subject full">
	<form action="<?php echo Route::url($this->application->link() . '&active=tokens'); ?>" method="get" name="adminForm" id="adminForm">
		<div class="container cf">
			<h3>
				<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_ACCESS_TOKENS'); ?> <span><?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_AUTHORIZED_TO'); ?>:</span>

				<a class="btn btn-secondary add-permanent confirm btn-success" data-txt-confirm="<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_ADD_PERSONAL_ACCESS_TOKEN_CONFIRM'); ?>" href="<?php echo Route::url($this->application->link('createpat')); ?>">
					<?php echo Lang::txt('COM_DEVELOPER_API_APPLICATION_TOKENS_ADD_PERSONAL_ACCESS_TOKEN'); ?>
				</a>
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