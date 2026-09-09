{{--
 * Wiki page — page does not exist (404-like with create suggestion)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    $templates = $book->templates()
        ->whereEquals('state', \Components\Wiki\Models\Page::STATE_PUBLISHED)
        ->rows();

    $url = Route::url($page->link('new'), false);
    if (User::isGuest()) {
        $return = base64_encode(Route::url($page->link('new'), false, true));
        $url = Route::url('index.php?option=com_users&view=login&return=' . $return, false);
    }
@endphp

<x-page-container :title="e($page->title)">
    @if(!$sub)
        @slot('sidebar')
            {!! $__view->view('_wikimenu')
                ->set('option', $option)
                ->set('controller', $controller)
                ->set('page', $page)
                ->set('task', $task)
                ->set('sub', $sub)
                ->loadTemplate() !!}
        @endslot
    @endif

    {!! $__view->view('_submenu')
        ->set('option', $option)
        ->set('controller', $controller)
        ->set('page', $page)
        ->set('task', $task)
        ->set('sub', $sub)
        ->loadTemplate() !!}

    <div role="alert" class="alert alert-warning mb-4">
        <span>{!! Lang::txt('COM_WIKI_WARNING_PAGE_DOES_NOT_EXIST_CREATE_IT', $url) !!}</span>
    </div>

    @if($templates->count())
        <p>{{ Lang::txt('COM_WIKI_CHOOSE_TEMPLATE') }}</p>
        <ul class="list-disc list-inside mt-2">
            @foreach($templates as $template)
                @php
                    $tplate = stripslashes($template->get('pagename'));
                    $tplUrl = Route::url($page->link('new') . '&tplate=' . $tplate, false);
                    if (User::isGuest()) {
                        $tplReturn = base64_encode(
                            Route::url($page->link('new') . '&tplate=' . $tplate, false, true)
                        );
                        $tplUrl = Route::url(
                            'index.php?option=com_users&view=login&return=' . $tplReturn,
                            false
                        );
                    }
                @endphp
                <li>
                    <a class="link" href="{{ $tplUrl }}">
                        {{ e(stripslashes($template->title)) }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</x-page-container>
