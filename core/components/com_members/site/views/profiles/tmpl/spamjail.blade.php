{{--
 * Spam jail message
 *
 * Variables:
 *   (none — uses Plugin::params for configuration)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Plugin;

    $params = Plugin::params('system', 'spamjail');
    $video = $params->get('spam_video', false);
@endphp

<x-page-container :title="Lang::txt('COM_MEMBERS_SPAM_DETECTED')">
    <p>{{ Lang::txt('COM_MEMBERS_SPAM_MESSAGE') }}</p>

    @if($video)
        <p>{{ Lang::txt('COM_MEMBERS_SPAM_VIDEO') }}</p>
        <div class="flex justify-center">
            <iframe
                width="420"
                height="315"
                src="https://www.youtube.com/embed/{{ e($video) }}"
                frameborder="0"
                allowfullscreen></iframe>
        </div>
    @endif
</x-page-container>
