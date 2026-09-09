{{--
 * Wiki special page — citation formats for a wiki page
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Config;
    use Hubzero\Facades\Date;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;

    $citePage = $book->pages()
        ->whereEquals('pagename', Request::getString('page', ''))
        ->whereEquals('path', Request::getString('scope', ''))
        ->row();

    if ($v = Request::getInt('version', 0)) {
        $revision = $citePage->versions()
            ->whereEquals('id', $v)
            ->row();
    } else {
        $revision = $citePage->version();
    }

    $yFormat             = 'Y';
    $apaFormat           = 'Y, M d';
    $apaFormatRetrieved  = 'H:i, M d, Y';
    $mlaFormat           = 'd M. Y';
    $mlaFormatRetrieved  = 'd M. Y';
    $mhraFormat          = 'd M Y H:i';
    $mhraFormatRetrieved = 'd M Y';
    $cbeFormat           = 'Y M d';
    $bluebookFormat      = 'M. d, Y';
    $amaFormat           = 'M d, Y, H:i';
    $amaFormatRetrieved  = 'M d, Y';

    $now = Date::toSql();

    $permalink = rtrim(Request::base(), '/') . '/'
        . ltrim(Route::url(
            $citePage->link() . '&version=' . $revision->get('version'),
            false
        ), '/');

    $citeTitle      = e(stripslashes($citePage->get('title', '') ?? ''));
    $citeSite       = e(Config::get('sitename'));
    $citeRevCreated = $revision->get('created');
    $citeApaDate    = Date::of($citeRevCreated)->format($apaFormat);
    $citeMhraDate   = Date::of($citeRevCreated)->format($mhraFormat);
    $citeAmaDate    = Date::of($citeRevCreated)->format($amaFormat);
    $citeBluebookDate = Date::of($citeRevCreated)->format($bluebookFormat);
    $citeApaRetr    = Date::of($now)->format($apaFormatRetrieved);
    $citeMlaRetr    = Date::of($now)->format($mlaFormatRetrieved);
    $citeMhraRetr   = Date::of($now)->format($mhraFormatRetrieved);
    $citeCbeDate    = Date::of($now)->format($cbeFormat);
    $citeAmaRetr    = Date::of($now)->format($amaFormatRetrieved);
    $citeYear       = Date::of($citeRevCreated)->format($yFormat);
    $historyUrl     = Route::url($citePage->link('history'), false);
@endphp

<x-page-container :title="e($page->title)">
    @if(!$sub)
        @slot('sidebar')
            {!! $__view->view('_wikimenu', 'pages')
                ->set('option', $option)
                ->set('controller', $controller)
                ->set('page', $page)
                ->set('task', $task)
                ->set('sub', $sub)
                ->loadTemplate() !!}
        @endslot
    @endif

    {!! $__view->view('_submenu', 'pages')
        ->set('option', $option)
        ->set('controller', $controller)
        ->set('page', $page)
        ->set('task', $task)
        ->set('sub', $sub)
        ->loadTemplate() !!}

    <div role="alert" class="alert alert-warning mb-6">
        <div>
            <p class="font-bold">IMPORTANT NOTE</p>
            <p>
                Most educators and professionals do not consider it appropriate to use
                tertiary sources such as encyclopedias as a sole source for any
                information&mdash;citing an encyclopedia as an important reference in
                footnotes or bibliographies may result in censure or a failing grade.
                Wiki articles should be used for background information, as a reference
                for correct terminology and search terms, and as a starting point for
                further research.
            </p>
            <p class="mt-2">
                As with any community-built reference, there is a possibility for error
                in content&ndash;please check your facts against multiple sources and
                read our disclaimers for more information.
            </p>
        </div>
    </div>

    <div class="card bg-base-200 mb-6">
        <div class="card-body">
            <h3 class="card-title">
                Bibliographic details for &ldquo;{{ $citeTitle }}&rdquo;
            </h3>
            <ul class="list-disc list-inside space-y-1">
                <li>
                    Page name: {{ e(stripslashes($citePage->get('pagename', '') ?? '')) }}
                </li>
                <li>
                    Author: {{ $citeSite }} contributors
                </li>
                <li>
                    Publisher: <em>{{ $citeSite }}</em>
                </li>
                <li>
                    Date of last revision:
                    {{ e(stripslashes($revision->get('created', '') ?? '')) }}
                </li>
                <li>
                    Date retrieved: {{ $now }}
                </li>
                <li>
                    Permanent link:
                    <a class="link link-hover" href="{{ $permalink }}">
                        {{ $permalink }}
                    </a>
                </li>
                <li>
                    Primary contributors:
                    <a class="link link-hover" href="{{ $historyUrl }}">
                        Revision history
                    </a>
                </li>
                <li>
                    Page version ID: {{ e($revision->get('id')) }}
                </li>
            </ul>
            <p class="mt-4 text-sm text-base-content/70">
                Please remember to check your manual of style, standards guide or
                instructor's guidelines for the exact syntax to suit your needs.
            </p>
        </div>
    </div>

    <div class="card bg-base-100 border border-base-300">
        <div class="card-body">
            <h3 class="card-title">
                Citation styles for &ldquo;{{ $citeTitle }}&rdquo;
            </h3>

            <h4 class="font-semibold mt-4">APA style</h4>
            <p>
                {{ $citeTitle }}. ({{ $citeApaDate }}).
                In <em>{{ $citeSite }}</em>.
                Retrieved {{ $citeApaRetr }},
                from <a class="link link-hover" href="{{ $permalink }}">{{ $permalink }}</a>
            </p>

            <h4 class="font-semibold mt-4">MLA style</h4>
            <p>
                {{ $citeSite }} contributors.
                &ldquo;{{ $citeTitle }}.&rdquo;
                <em>{{ $citeSite }}</em>.
                {{ $citeSite }}, {{ $citeApaDate }}. Web. {{ $citeMlaRetr }}
            </p>

            <h4 class="font-semibold mt-4">MHRA style</h4>
            <p>
                {{ $citeSite }} contributors,
                '{{ $citeTitle }},'
                <em>{{ $citeSite }}</em>, {{ $citeMhraDate }},
                &lt;<a class="link link-hover" href="{{ $permalink }}">{{ $permalink }}</a>&gt;
                [accessed {{ $citeMhraRetr }}]
            </p>

            <h4 class="font-semibold mt-4">Chicago style</h4>
            <p>
                {{ $citeSite }} contributors,
                &ldquo;{{ $citeTitle }},&rdquo;
                <em>{{ $citeSite }}</em>,
                <a class="link link-hover" href="{{ $permalink }}">{{ $permalink }}</a>
                (accessed {{ $citeMhraRetr }}).
            </p>

            <h4 class="font-semibold mt-4">CBE/CSE style</h4>
            <p>
                {{ $citeSite }} contributors.
                {{ $citeTitle }} [Internet].
                {{ $citeSite }}; {{ $citeBluebookDate }}
                [cited {{ $citeCbeDate }}].
                Available from:
                <a class="link link-hover" href="{{ $permalink }}">{{ $permalink }}</a>.
            </p>

            <h4 class="font-semibold mt-4">Bluebook style</h4>
            <p>
                {{ $citeTitle }},
                <a class="link link-hover" href="{{ $permalink }}">{{ $permalink }}</a>
                (last visited {{ $citeBluebookDate }}).
            </p>

            <h4 class="font-semibold mt-4">Bluebook: Harvard JOLT style</h4>
            <p>
                {{ $citeSite }},
                <em>{{ $citeTitle }}</em>,
                <a class="link link-hover" href="{{ $permalink }}">{{ $permalink }}</a>
                (optional description here) (as of {{ $citeBluebookDate }}).
            </p>

            <h4 class="font-semibold mt-4">AMA style</h4>
            <p>
                {{ $citeSite }} contributors.
                {{ $citeTitle }}.
                {{ $citeSite }}. {{ $citeAmaDate }}.
                Available at
                <a class="link link-hover" href="{{ $permalink }}">{{ $permalink }}</a>.
                Accessed {{ $citeAmaRetr }}.
            </p>

            <h4 class="font-semibold mt-4">BibTeX entry</h4>
<pre class="bg-base-200 p-4 rounded-lg text-sm overflow-x-auto">
@{{misc{ wiki:xxx,
    author = "{{ $citeSite }}",
    title = "{{ $citeTitle }} --- {{ $citeSite }}",
    year = "{{ $citeYear }}",
    url = "{{ $permalink }}",
    note = "[Online; accessed {{ $now }}]"
}
</pre>
        </div>
    </div>
</x-page-container>
