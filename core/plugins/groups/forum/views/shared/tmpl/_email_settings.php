<?php

$base = $this->base;

if (Component::params('com_groups')->get('email_comment_processing') && $this->config->get('access-view-section')) : ?>

	<div class="container relative-container">
		<h3><?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_SETTINGS'); ?></h3>

		<?php

			if (Component::params('com_groups')->get('enable_forum_email_digest', 0))
			{
				$emailSettingsMenu = '_email_settings_digest';
			}
			else if (Component::params('com_groups')->get('enable_forum_email_categories', 0))
			{
				$emailSettingsMenu = '_email_settings_categories';
			}
			else
			{
				$emailSettingsMenu = '_email_settings_simple';
			}

			$this->view($emailSettingsMenu)
				->set('base', $base)
				->set('categories', $this->categories)
				->set('recvEmailOptionID', $this->recvEmailOptionID)
				->set('recvEmailOptionValue', $this->recvEmailOptionValue)
				->display();
		?>
	</div>

<?php endif; ?>
