{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@if ($version == 'dev')
    @php
        $authorsFormAction = 'index.php?option=' . $option . '&controller=' . $controller;
    @endphp
    <form action="{{ $authorsFormAction }}" id="authors-form" method="post" enctype="multipart/form-data">
        <fieldset>
            @if ($__view->getError())
                <div class="alert alert-error">
                    {!! implode('<br>', $__view->getErrors()) !!}
                </div>
            @endif

            <div class="grid nobreak">
                <div class="col span7">
                    <label for="acmembers">
                        {{ Lang::txt('COM_TOOLS_AUTHORS_ENTER_LOGINS') }}
                        @php
                            $mc = Event::trigger(
                                'hubzero.onGetMultiEntry',
                                [['members', 'new_authors', 'acmembers']]
                            );
                        @endphp
                        @if (count($mc) > 0)
                            {!! $mc[0] !!}
                        @else
                            <span class="hint">
                                {{ Lang::txt('COM_TOOLS_ADD_AUTHORS_INSTRUCTIONS') }}
                            </span>
                            <input type="text" name="new_authors" id="acmembers" value="" class="input input-bordered w-full" />
                        @endif
                    </label>
                </div>
                <div class="col span3">
                    <label>
                        <span id="new-authors-role-label">
                            {{ Lang::txt('COM_TOOLS_AUTHORS_ROLE') }}
                        </span><br />
                        <select name="role" id="new-authors-role" class="select select-bordered">
                            <option value="">{{ Lang::txt('COM_TOOLS_AUTHOR') }}</option>
                            @if ($roles)
                                @foreach ($roles as $role)
                                    <option value="{{ $__view->escape($role->alias) }}">
                                        {{ $__view->escape($role->title) }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </label>
                </div>
                <div class="col span2 omega">
                    <p class="submit">
                        <input type="submit" class="btn btn-primary" value="{{ Lang::txt('COM_TOOLS_ADD') }}" />
                    </p>
                </div>
            </div>

            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="controller" value="{{ $controller }}" />
            <input type="hidden" name="tmpl" value="component" />
            <input type="hidden" name="pid" id="pid" value="{{ $id }}" />
            <input type="hidden" name="task" value="save" />
        </fieldset>
    </form>
@else
    <div class="alert alert-warning">
        {{ Lang::txt('COM_TOOLS_AUTHORS_CANT_CHANGE') }}
    </div>
@endif

@if ($contributors)
    @php
        $n = count($contributors);
        $listFormAction = 'index.php?option=' . $option
            . '&controller=' . $controller
            . '&task=update&tmpl=component';
    @endphp
    <form action="{{ $listFormAction }}" id="authors-list" method="post" enctype="multipart/form-data">
        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="pid" id="pid" value="{{ $id }}" />
        <input type="hidden" name="task" value="update" />

        <table class="table table-zebra">
            <tbody>
                @foreach ($contributors as $i => $contributor)
                    @php
                        if ($contributor->lastname || $contributor->firstname) {
                            $name = stripslashes($contributor->firstname) . ' ';
                            if ($contributor->middlename != null) {
                                $name .= stripslashes($contributor->middlename) . ' ';
                            }
                            $name .= stripslashes($contributor->lastname);
                        } else {
                            $name = stripslashes($contributor->name);
                        }
                        $authorid = $contributor->authorid ?? '';
                        $orgVal = $__view->escape(stripslashes($contributor->organization));
                    @endphp
                    <tr>
                        <td width="100%">
                            {{ $__view->escape($name) }}<br />
                            <input
                                type="text"
                                name="authors[{{ $authorid }}][organization]"
                                size="35"
                                value="{{ $orgVal }}"
                                placeholder="{{ Lang::txt('COM_TOOLS_AUTHOR_ORGANIZATION') }}"
                                class="input input-bordered input-sm"
                            />
                        </td>
                        <td>
                            <select
                                name="authors[{{ $authorid }}][role]"
                                id="role-{{ $authorid }}"
                                class="select select-bordered select-sm"
                            >
                                <option value=""@if ($contributor->role == '') selected @endif>
                                    {{ Lang::txt('COM_TOOLS_AUTHOR') }}
                                </option>
                                @if ($roles)
                                    @foreach ($roles as $role)
                                        <option
                                            value="{{ $__view->escape($role->alias) }}"
                                            @if ($contributor->role == $role->alias) selected @endif
                                        >
                                            {{ $__view->escape(stripslashes($role->title)) }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </td>
                        @if ($version == 'dev')
                            <td class="u">
                                @if ($i > 0)
                                    @php
                                        $upUrl = 'index.php?option=' . $option
                                            . '&controller=' . $controller
                                            . '&tmpl=component&pid=' . $id
                                            . '&id=' . $contributor->authorid
                                            . '&task=reorder&move=up';
                                    @endphp
                                    <a href="{{ $upUrl }}" class="btn btn-ghost btn-xs order up" title="{{ Lang::txt('COM_TOOLS_MOVE_UP') }}">
                                        <span>{{ Lang::txt('COM_TOOLS_MOVE_UP') }}</span>
                                    </a>
                                @endif
                            </td>
                            <td class="d">
                                @if ($i < $n - 1)
                                    @php
                                        $downUrl = 'index.php?option=' . $option
                                            . '&controller=' . $controller
                                            . '&tmpl=component&pid=' . $id
                                            . '&id=' . $contributor->authorid
                                            . '&task=reorder&move=down';
                                    @endphp
                                    <a href="{{ $downUrl }}" class="btn btn-ghost btn-xs order down" title="{{ Lang::txt('COM_TOOLS_MOVE_DOWN') }}">
                                        <span>{{ Lang::txt('COM_TOOLS_MOVE_DOWN') }}</span>
                                    </a>
                                @endif
                            </td>
                        @endif
                        <td class="t">
                            @php
                                $deleteUrl = 'index.php?option=' . $option
                                    . '&controller=' . $controller
                                    . '&task=remove&tmpl=component'
                                    . '&id=' . $authorid
                                    . '&pid=' . $id;
                            @endphp
                            <a
                                class="btn btn-error btn-xs icon-delete delete"
                                href="{{ $deleteUrl }}"
                                title="{{ Lang::txt('COM_TOOLS_DELETE') }}"
                            >
                                <span>{{ Lang::txt('COM_TOOLS_DELETE') }}</span>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>
                        <span class="caption">
                            {{ Lang::txt('COM_TOOLS_AUTHORS_MUST_SAVE_CHANGES') }}
                        </span>
                    </td>
                    <td>
                        <input type="submit" class="btn btn-primary btn-sm" value="{{ Lang::txt('COM_TOOLS_SAVE_CHANGES') }}" />
                    </td>
                    @if ($version == 'dev')
                        <td></td>
                        <td></td>
                    @endif
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </form>
@else
    <p>{{ Lang::txt('COM_TOOLS_AUTHORS_NONE_FOUND') }}</p>
@endif
