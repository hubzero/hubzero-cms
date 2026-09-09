{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@if (Component::params('com_groups')->get('email_forum_comments') && $config->get('access-view-section'))
    <div class="container relative-container">
        <h3>{{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_SETTINGS') }}</h3>

        @php
        if (Component::params('com_groups')->get('enable_forum_email_digest', 0)) {
            $emailSettingsMenu = '_email_settings_digest';
        } elseif (Component::params('com_groups')->get('enable_forum_email_categories', 0)) {
            $emailSettingsMenu = '_email_settings_categories';
        } else {
            $emailSettingsMenu = '_email_settings_simple';
        }

        $__view->view($emailSettingsMenu)
            ->set('base', $base)
            ->set('categories', $categories)
            ->set('recvEmailOptionID', $recvEmailOptionID)
            ->set('recvEmailOptionValue', $recvEmailOptionValue)
            ->display();
        @endphp
    </div>
@endif
