{{--
 * Wiki special page — newest pages listing with sortable columns
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Config;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Pathway;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    Pathway::append(
        Lang::txt('COM_WIKI_SPECIAL_NEW_PAGES'),
        $page->link()
    );

    $sort = strtolower(Request::getString('sort', 'created'));
    if (!in_array($sort, ['created', 'title', 'summary', 'created_by'])) {
        $sort = 'created';
    }
    $dir = strtoupper(Request::getString('dir', 'DESC'));
    if (!in_array($dir, ['ASC', 'DESC'])) {
        $dir = 'DESC';
    }

    $limit = Request::getInt('limit', Config::get('list_limit'));
    $start = Request::getInt('limitstart', 0);

    $filters = ['state' => [0, 1]];

    if ($space = Request::getString('namespace', '')) {
        $filters['namespace'] = urldecode($space);
    }

    $rows = $book->pages($filters)
        ->including([
            'versions',
            function ($version) {
                $version
                    ->select('id')
                    ->select('page_id')
                    ->select('version')
                    ->select('created_by')
                    ->select('summary');
            }
        ])
        ->order('created', $dir)
        ->paginated()
        ->rows();

    $altdir = ($dir == 'ASC') ? 'DESC' : 'ASC';
    $pageLink = $page->link();
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

    <p class="mb-4">
        {{ Lang::txt('COM_WIKI_SPECIAL_NEW_PAGES_ABOUT') }}
    </p>

    <form method="get" action="{{ Route::url($pageLink, false) }}">
        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th scope="col">
                            <a class="{{ $sort == 'created' ? 'font-bold' : '' }}"
                               href="{{ Route::url($pageLink . '&sort=created&dir=' . $altdir, false) }}">
                                @if($sort == 'created')
                                    {!! $dir == 'ASC' ? '&uarr;' : '&darr;' !!}
                                @endif
                                {{ Lang::txt('COM_WIKI_COL_DATE') }}
                            </a>
                        </th>
                        <th scope="col">
                            <a class="{{ $sort == 'title' ? 'font-bold' : '' }}"
                               href="{{ Route::url($pageLink . '&sort=title&dir=' . $altdir, false) }}">
                                @if($sort == 'title')
                                    {!! $dir == 'ASC' ? '&uarr;' : '&darr;' !!}
                                @endif
                                {{ Lang::txt('COM_WIKI_COL_TITLE') }}
                            </a>
                        </th>
                        <th scope="col">
                            <a class="{{ $sort == 'created_by' ? 'font-bold' : '' }}"
                               href="{{ Route::url($pageLink . '&sort=created_by&dir=' . $altdir, false) }}">
                                @if($sort == 'created_by')
                                    {!! $dir == 'ASC' ? '&uarr;' : '&darr;' !!}
                                @endif
                                {{ Lang::txt('COM_WIKI_COL_CREATOR') }}
                            </a>
                        </th>
                        <th scope="col">
                            <a class="{{ $sort == 'summary' ? 'font-bold' : '' }}"
                               href="{{ Route::url($pageLink . '&sort=summary&dir=' . $altdir, false) }}">
                                @if($sort == 'summary')
                                    {!! $dir == 'ASC' ? '&uarr;' : '&darr;' !!}
                                @endif
                                {{ Lang::txt('COM_WIKI_COL_EDIT_SUMMARY') }}
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php
                            $creatorName = $row->creator
                                ->get('name', Lang::txt('COM_WIKI_UNKNOWN')) ?? '';
                            $nameEscaped = e(stripslashes($creatorName));
                            $nameLinked  = $nameEscaped;
                            if (in_array(
                                $row->creator->get('access'),
                                User::getAuthorisedViewLevels()
                            )) {
                                $creatorUrl = Route::url(
                                    $row->creator->link(),
                                    false
                                );
                                $nameLinked = '<a class="link link-hover" href="'
                                    . $creatorUrl . '">' . $nameEscaped . '</a>';
                            }
                        @endphp
                        <tr>
                            <td>
                                <time datetime="{{ $row->get('created') }}">
                                    {{ $row->get('created') }}
                                </time>
                            </td>
                            <td>
                                <a class="link link-hover"
                                   href="{{ Route::url($row->link(), false) }}">
                                    {{ e(stripslashes($row->title ?? '')) }}
                                </a>
                            </td>
                            <td>
                                {!! $nameLinked !!}
                            </td>
                            <td>
                                <span>{{ e(stripslashes($row->version->get('summary', '') ?? '')) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
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
    </form>
</x-page-container>
