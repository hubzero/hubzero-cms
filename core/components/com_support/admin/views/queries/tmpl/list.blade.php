{{--
  Support — Query folders/queries sidebar list partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
@endphp

@if(count($folders) > 0)
    @foreach($folders as $folder)
        <li id="folder_{{ $folder->id }}" class="open">
            <span
                class="icon-folder folder"
                id="{{ $folder->id }}-title"
                data-id="{{ $folder->id }}"
            >{{ $folder->title }}</span>
            <span class="folder-options">
                @php
                    $deleteFolderUrl = Route::url(
                        'index.php?option=' . $option
                        . '&controller=queries&task=removefolder&id=' . $folder->id
                        . '&' . Session::getFormToken() . '=1', false
                    );
                    $editFolderUrl = Route::url(
                        'index.php?option=' . $option
                        . '&controller=queries&task=editfolder&id=' . $folder->id
                        . '&tmpl=component&' . Session::getFormToken() . '=1', false
                    );
                    $saveFolderUrl = Route::url(
                        'index.php?option=' . $option
                        . '&controller=queries&task=savefolder&' . Session::getFormToken()
                        . '=1&fields[id]=' . $folder->id, false
                    );
                @endphp
                <a
                    class="delete"
                    href="{{ $deleteFolderUrl }}"
                    title="{{ Lang::txt('JACTION_DELETE') }}"
                >{{ Lang::txt('COM_SUPPORT_DELETE') }}</a>
                <a
                    class="edit editfolder"
                    data-id="{{ $folder->id }}"
                    href="{{ $editFolderUrl }}"
                    data-href="{{ $saveFolderUrl }}"
                    title="{{ Lang::txt('JACTION_EDIT') }}"
                >{{ Lang::txt('COM_SUPPORT_EDIT') }}</a>
            </span>
            <ul id="queries_{{ $folder->id }}" class="queries">
                @foreach($folder->queries()->order('ordering', 'asc')->rows() as $query)
                    <li
                        id="query_{{ $query->id }}"
                        @if($show == $query->id) class="active"@endif
                    >
                        @php
                            $searchParam  = (intval($show) != $query->id) ? '&search=' : '';
                            $queryUrl     = Route::url(
                                'index.php?option=' . $option
                                . '&controller=tickets&show=' . $query->id . $searchParam, false
                            );
                            $ticketCount  = \Components\Support\Models\Ticket::countWithQuery(
                                $query,
                                []
                            );
                            $deleteQueryUrl = Route::url(
                                'index.php?option=' . $option
                                . '&controller=queries&task=remove&id=' . $query->id
                                . '&' . Session::getFormToken() . '=1', false
                            );
                            $editQueryUrl = Route::url(
                                'index.php?option=' . $option
                                . '&controller=queries&task=edit&id=' . $query->id
                                . '&tmpl=component&' . Session::getFormToken() . '=1', false
                            );
                        @endphp
                        <a class="query" href="{{ $queryUrl }}">
                            {{ $query->title }}
                            <span>{{ $ticketCount }}</span>
                        </a>
                        <span class="query-options">
                            <a
                                class="delete"
                                href="{{ $deleteQueryUrl }}"
                                title="{{ Lang::txt('JACTION_DELETE') }}"
                            >{{ Lang::txt('JACTION_DELETE') }}</a>
                            <a
                                class="modal edit"
                                href="{{ $editQueryUrl }}"
                                title="{{ Lang::txt('JACTION_EDIT') }}"
                                rel="{handler: 'iframe', size: {x: 570, y: 550}}"
                            >{{ Lang::txt('JACTION_EDIT') }}</a>
                        </span>
                    </li>
                @endforeach
            </ul>
        </li>
    @endforeach
@endif
