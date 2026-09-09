{{--
 * Wiki special page — full-text search with results table
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\App;
    use Hubzero\Facades\Config;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Pathway;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;

    Pathway::append(
        Lang::txt('COM_WIKI_SEARCH'),
        $page->link('base') . '&pagename=Special:Search'
    );

    $database = App::get('db');

    $limit = Request::getInt('limit', Config::get('list_limit'));
    $start = Request::getInt('limitstart', 0);
    $term  = Request::getString('q', '');

    $filters = ['state' => [0, 1]];

    if ($space = Request::getString('namespace', '')) {
        $filters['namespace'] = urldecode($space);
    }

    $pages    = \Components\Wiki\Models\Page::blank()->getTableName();
    $versions = \Components\Wiki\Models\Version::blank()->getTableName();

    $weight = '(match(' . $pages . '.title) against ('
        . $database->Quote($term) . ')'
        . ' + match(' . $versions . '.pagetext) against ('
        . $database->Quote($term) . '))';

    $rows = $book->pages($filters)
        ->select($pages . '.*')
        ->select($versions . '.created_by')
        ->select($versions . '.summary')
        ->select($weight, 'weight')
        ->join($versions, $versions . '.id', $pages . '.version_id')
        ->whereRaw($weight . ' > 0')
        ->order('weight', 'desc')
        ->paginated()
        ->rows();

    $searchUrl = Route::url(
        $page->link('base') . '&pagename=Special:Search',
        false
    );
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

    <x-search-bar
        :action="$searchUrl"
        :query="$term"
        name="q"
        :placeholder="Lang::txt('COM_WIKI_SEARCH_PLACEHOLDER')"
        :buttonLabel="Lang::txt('COM_WIKI_SEARCH')"
    />

    <div class="overflow-x-auto">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th scope="col">
                        {{ Lang::txt('COM_WIKI_COL_TITLE') }}
                    </th>
                    <th scope="col">
                        {{ Lang::txt('COM_WIKI_COL_PATH') }}
                    </th>
                    <th scope="col">
                        {{ Lang::txt('COM_WIKI_COL_MODIFIED') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    @php
                        $rowUrl = Route::url($row->link(), false);
                    @endphp
                    <tr>
                        <td>
                            <a class="link link-hover"
                               href="{{ $rowUrl }}">
                                {{ e(stripslashes($row->title ?? '')) }}
                            </a>
                        </td>
                        <td>
                            {{ $rowUrl }}
                        </td>
                        <td>
                            <time datetime="{{ $row->get('modified') }}">
                                {{ $row->get('modified') }}
                            </time>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            {{ Lang::txt('COM_WIKI_NONE') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @php
        $pageNav = $rows->pagination;
        $pageNav->setAdditionalUrlParam('scope', $page->get('scope'));
        $pageNav->setAdditionalUrlParam('pagename', $page->get('pagename'));
    @endphp
    {!! $pageNav !!}
</x-page-container>
