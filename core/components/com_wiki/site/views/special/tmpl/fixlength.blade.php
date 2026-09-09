{{--
 * Wiki special — fix zero-length version entries
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Pathway;
    use Hubzero\Facades\Route;

    Pathway::append(
        Lang::txt('COM_WIKI_SPECIAL_FIX_LENGTH'),
        $page->link()
    );

    $query = \Components\Wiki\Models\Version::all();

    $v = $query->getTableName();
    $p = \Components\Wiki\Models\Page::blank()->getTableName();

    $rows = $query
        ->join($p, $p . '.id', $v . '.page_id', 'inner')
        ->whereEquals($v . '.length', 0)
        ->whereEquals($p . '.scope', $page->get('scope'))
        ->whereEquals($p . '.scope_id', $page->get('scope_id'))
        ->rows();
@endphp

<x-page-container :title="Lang::txt('COM_WIKI_SPECIAL_FIX_LENGTH')">
    <form method="get" action="{{ Route::url($page->link(), false) }}">
        <p class="mb-4">
            {{ Lang::txt('COM_WIKI_SPECIAL_FIX_LENGTH_ABOUT') }}
        </p>

        <div class="overflow-x-auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_REVISION_ID') }}
                        </th>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_PAGE_ID') }}
                        </th>
                        <th scope="col">
                            {{ Lang::txt('COM_WIKI_COL_LENGTH') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @if($rows->count())
                        @foreach($rows as $row)
                            @php
                                $row->set('length', strlen($row->get('pagetext')));
                                $row->save();
                            @endphp
                            <tr>
                                <td>
                                    {{ $row->get('id') }}
                                </td>
                                <td>
                                    {{ $row->get('page_id') }}
                                </td>
                                <td>
                                    {{ Lang::txt('COM_WIKI_HISTORY_BYTES', $row->get('length')) }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3">
                                {{ Lang::txt('COM_WIKI_NONE') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </form>
</x-page-container>
