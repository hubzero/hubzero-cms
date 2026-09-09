{{--
 * Wiki special — fix pages without version IDs, auto-creates versions
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Pathway;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;

    Pathway::append(
        Lang::txt('COM_WIKI_SPECIAL_FIX_VERSION'),
        $page->link()
    );

    $filters = array('state' => array(0, 1));

    $pages = $book->pages($filters);

    if (!Request::getInt('force_fix', 0)) {
        $pages->whereEquals('version_id', 0);
    }

    $rows = $pages->rows();
@endphp

<x-page-container :title="Lang::txt('COM_WIKI_SPECIAL_FIX_VERSION')">
    <form method="get" action="{{ Route::url($page->link(), false) }}">
        <p class="mb-4">
            {{ Lang::txt('COM_WIKI_SPECIAL_FIX_VERSION_ABOUT') }}
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
                                $version = $row->versions()
                                    ->whereEquals('approved', 1)
                                    ->order('version', 'desc')
                                    ->row();

                                if (!$version->get('id')) {
                                    $version->set('page_id', $row->get('id'));
                                    $version->set('pagetext', 'New page.');
                                    $version->set('approved', 1);
                                    $version->set('summary', 'Auto-created version.');
                                    $version->set('length', strlen($version->get('pagetext')));
                                    $version->save();
                                }

                                if ($version->get('id')) {
                                    $row->set('version_id', (int) $version->get('id'));
                                    $row->set('modified', $version->get('created'));
                                    $row->save();
                                }
                            @endphp
                            <tr>
                                <td>
                                    {{ $row->get('version_id') }}
                                </td>
                                <td>
                                    <time datetime="{{ $version->get('created') }}">
                                        {{ $version->get('created') }}
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
    </form>
</x-page-container>
