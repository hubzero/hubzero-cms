{{--
 * Wiki special — pages needing link fixes with pagination
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

    Pathway::append(
        Lang::txt('COM_WIKI_SPECIAL_FIX_LINKS'),
        $page->link()
    );

    $limit = Request::getInt('limit', Config::get('list_limit'));
    $start = Request::getInt('limitstart', 0);

    $filters = array('state' => array(0, 1));

    $rows = $book->pages($filters)
        ->paginated()
        ->rows();
@endphp

<x-page-container :title="Lang::txt('COM_WIKI_SPECIAL_FIX_LINKS')">
    <form method="get" action="{{ Route::url($page->link(), false) }}">
        <p class="mb-4">
            {{ Lang::txt('COM_WIKI_SPECIAL_FIX_LINKS_ABOUT') }}
        </p>

        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_REVISION_ID') }}
                        </th>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_REVISION_TIME') }}
                        </th>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_PAGE_ID') }}
                        </th>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_PAGE') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if($rows->count())
                        @foreach($rows as $row)
                            @php
                                $content = $row->version()->content();
                            @endphp
                            <tr>
                                <td>
                                    {{ $row->get('version_id') }}
                                </td>
                                <td>
                                    <time datetime="{{ $row->get('created') }}">
                                        {{ $row->get('created') }}
                                    </time>
                                </td>
                                <td>
                                    {{ $row->get('id') }}
                                </td>
                                <td>
                                    <a href="{{ Route::url($row->link(), false) }}">
                                        {{ e(stripslashes($row->title ?? '')) }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4">
                                {{ Lang::txt('COM_WIKI_NONE') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @php
            $pageNav = $rows->pagination;
            $pageNav->setAdditionalUrlParam('scope', $page->get('scope'));
            $pageNav->setAdditionalUrlParam('pagename', 'Special:' . $page->get('pagename'));
        @endphp
        {!! $pageNav !!}
    </form>
</x-page-container>
