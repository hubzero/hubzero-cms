{{--
 * Storefront overview/landing page — login gate with optional custom article
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\Event;

    $customLandingPage = $config->get('landingPage', 0);
    $return = base64_encode(Route::url('index.php?option=storefront'));
    $loginUrl = Route::url(
        'index.php?option=com_users&view=login&return=' . $return
    );

    if ($customLandingPage && is_numeric($customLandingPage)) {
        $article = $content;
        if ($article->fulltext) {
            $article->text = $article->fulltext;
        } else {
            $article->text = $article->introtext;
        }
        Event::trigger(
            'content.onContentPrepare',
            array('com_content.article', &$article, array())
        );
    }
@endphp

@if($customLandingPage && is_numeric($customLandingPage))
    <x-page-container :title="$article->title">
        @slot('actions')
            <a class="btn btn-primary btn-sm" href="{{ $loginUrl }}">
                {{ Lang::txt('COM_STOREFRONT_LOGIN') }}
            </a>
        @endslot

        <div class="prose max-w-none">
            {!! $article->text !!}
        </div>
    </x-page-container>
@else
    <x-page-container :title="Lang::txt('COM_STOREFRONT')">
        @slot('actions')
            <a class="btn btn-primary btn-sm" href="{{ $loginUrl }}">
                {{ Lang::txt('COM_STOREFRONT_LOGIN') }}
            </a>
        @endslot

        <p class="text-lg">
            {{ Lang::txt('COM_STOREFRONT_GUEST_MESSAGE') }}
        </p>
    </x-page-container>
@endif
