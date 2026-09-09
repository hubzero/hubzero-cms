{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@if ($__view->getError())
    <li class="error">Error: {!! $__view->getError() !!}</li>
@endif
@if (count($folders) > 0)
    @foreach ($folders as $folder)
        @php
            $folderId    = $__view->escape($folder->id);
            $folderTitle = $__view->escape($folder->title);
            $tok         = \Hubzero\Facades\Session::getFormToken();
            $delFolderUrl = \Hubzero\Facades\Route::url(
                'index.php?option=' . $option
                . '&controller=queries&task=removefolder&id=' . $folder->id . '&' . $tok . '=1'
            );
            $editFolderUrl = \Hubzero\Facades\Route::url(
                'index.php?option=' . $option
                . '&controller=queries&task=editfolder&id=' . $folder->id . '&tmpl=component&' . $tok . '=1'
            );
            $saveFolderUrl = \Hubzero\Facades\Route::url(
                'index.php?option=' . $option
                . '&controller=queries&task=savefolder&' . $tok . '=1&fields[id]=' . $folder->id
            );
        @endphp
        <li id="folder_{{ $folderId }}" class="open">
            <span
                class="icon-folder folder"
                id="{{ $folderId }}-title"
                data-id="{{ $folderId }}"
            >{{ $folderTitle }}</span>
            <span class="folder-options">
                <a
                    class="delete"
                    href="{{ $delFolderUrl }}"
                    title="{{ \Hubzero\Facades\Lang::txt('JACTION_DELETE') }}"
                >
                    {{ \Hubzero\Facades\Lang::txt('JACTION_DELETE') }}
                </a>
                <a
                    class="edit editfolder"
                    data-id="{{ $folderId }}"
                    href="{{ $editFolderUrl }}"
                    data-href="{{ $saveFolderUrl }}"
                    title="{{ \Hubzero\Facades\Lang::txt('JACTION_EDIT') }}"
                >
                    {{ \Hubzero\Facades\Lang::txt('JACTION_EDIT') }}
                </a>
            </span>
            <ul id="queries_{{ $folderId }}" class="queries">
                @foreach ($folder->queries()->order('ordering', 'asc')->rows() as $query)
                    @php
                        $isActive = ($show == $query->id);
                        $qUrl = \Hubzero\Facades\Route::url(
                            'index.php?option=' . $option
                            . '&controller=tickets&task=display&show=' . $query->id
                            . (!$isActive ? '&search=' : '')
                        );
                        $delQueryUrl = \Hubzero\Facades\Route::url(
                            'index.php?option=' . $option
                            . '&controller=queries&task=remove&id=' . $query->id . '&' . $tok . '=1'
                        );
                        $editQueryUrl = \Hubzero\Facades\Route::url(
                            'index.php?option=' . $option
                            . '&controller=queries&task=edit&id=' . $query->id
                            . '&tmpl=component&' . $tok . '=1'
                        );
                        $ticketCount = \Components\Support\Models\Ticket::countWithQuery($query, array());
                    @endphp
                    <li
                        id="query_{{ $__view->escape($query->id) }}"
                        @if ($isActive) class="active" @endif
                    >
                        <a class="aquery" href="{{ $qUrl }}">
                            {{ $__view->escape(stripslashes($query->title)) }}
                            <span>{{ $ticketCount }}</span>
                        </a>
                        <span class="query-options">
                            <a
                                class="delete"
                                href="{{ $delQueryUrl }}"
                                title="{{ \Hubzero\Facades\Lang::txt('JACTION_DELETE') }}"
                            >
                                {{ \Hubzero\Facades\Lang::txt('JACTION_DELETE') }}
                            </a>
                            <a
                                class="modal edit"
                                href="{{ $editQueryUrl }}"
                                title="{{ \Hubzero\Facades\Lang::txt('JACTION_EDIT') }}"
                                rel="{handler: 'iframe', size: {x: 570, y: 550}}"
                            >
                                {{ \Hubzero\Facades\Lang::txt('JACTION_EDIT') }}
                            </a>
                        </span>
                    </li>
                @endforeach
            </ul>
        </li>
    @endforeach
@endif
