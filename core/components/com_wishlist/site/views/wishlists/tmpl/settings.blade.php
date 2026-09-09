{{--
 * Wishlist settings — title, description, visibility, owner management
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Html;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;
    use Hubzero\Facades\Event;
@endphp

@if(!$wishlist->isPublic() && !$wishlist->access('manage'))
    <x-page-container :title="Lang::txt('COM_WISHLIST_PRIVATE_LIST')">
        <div role="alert" class="alert alert-error">
            <span>{{ Lang::txt('COM_WISHLIST_ALERTNOTAUTH_PRIVATE_LIST') }}</span>
        </div>
    </x-page-container>
@else
    <x-page-container :title="$__view->title">
        @slot('actions')
            <a class="btn btn-sm"
               href="{{ Route::url($wishlist->link(), false) }}">
                {{ Lang::txt('COM_WISHLIST_WISHES_ALL') }}
            </a>
        @endslot

        @slot('sidebar')
            <x-sidebar-card :title="Lang::txt('COM_WISHLIST_HELP')">
                <p>{{ Lang::txt('COM_WISHLIST_SETTINGS_INFO') }}</p>
            </x-sidebar-card>
        @endslot

        <form id="hubForm" method="post"
              action="{{ Route::url($wishlist->link('savesettings'), false) }}"
              class="max-w-3xl">

            {{-- Basic information --}}
            <x-form-section :heading="Lang::txt('COM_WISHLIST_INFORMATION')">
                @if($wishlist->get('category') == 'resource')
                    <x-form-field name="fields[title]" inputId="field-title"
                                  :label="Lang::txt('COM_WISHLIST_TITLE')"
                                  :hint="Lang::txt('COM_WISHLIST_TITLE_NOTE')">
                        <span class="font-medium">{{ $wishlist->get('title') }}</span>
                        <input name="fields[title]" id="field-title" type="hidden"
                               value="{{ e($wishlist->get('title')) }}" />
                    </x-form-field>
                @else
                    <x-form-field name="fields[title]" inputId="field-title"
                                  :label="Lang::txt('COM_WISHLIST_TITLE')">
                        <input name="fields[title]" id="field-title" type="text"
                               class="input input-bordered w-full"
                               value="{{ e($wishlist->get('title')) }}" />
                    </x-form-field>
                @endif

                <x-form-field name="fields[description]" inputId="field-description"
                              :label="Lang::txt('COM_WISHLIST_DESC') . ' (' . Lang::txt('COM_WISHLIST_OPTIONAL') . ')'">
                    <textarea name="fields[description]" id="field-description"
                              class="textarea textarea-bordered w-full"
                              rows="6">{{ e($wishlist->get('description')) }}</textarea>
                </x-form-field>

                @php
                    $isResource = ($wishlist->get('category') == 'resource');
                    $isGeneral1 = ($wishlist->get('category') == 'general'
                        && $wishlist->get('referenceid') == 1);
                    $isDisabled = ($isResource || $isGeneral1);
                @endphp
                <fieldset class="space-y-2">
                    <legend class="font-medium">{{ Lang::txt('COM_WISHLIST_THIS_LIST_IS') }}:</legend>

                    <x-form-field name="fields[public]" inputId="field-public-yes"
                                  :label="Lang::txt('COM_WISHLIST_PUBLIC')" type="checkbox">
                        <input type="radio" name="fields[public]" id="field-public-yes"
                               class="radio radio-sm" value="1"
                               {{ $wishlist->get('public') == 1 ? 'checked' : '' }}
                               {{ $isDisabled ? 'disabled' : '' }} />
                    </x-form-field>

                    <x-form-field name="fields[public]" inputId="field-public-no"
                                  :label="Lang::txt('COM_WISHLIST_PRIVATE')" type="checkbox">
                        <input type="radio" name="fields[public]" id="field-public-no"
                               class="radio radio-sm" value="0"
                               {{ $wishlist->get('public') == 0 ? 'checked' : '' }}
                               {{ $isDisabled ? 'disabled' : '' }} />
                    </x-form-field>
                </fieldset>
            </x-form-section>

            {{-- Owner groups --}}
            @php $owners = $wishlist->getOwners(); @endphp

            <x-form-section :heading="Lang::txt('COM_WISHLIST_OWNER_GROUPS')">
                <p class="text-sm text-base-content/60 mb-3">
                    {{ Lang::txt('COM_WISHLIST_SETTINGS_EDIT_GROUPS') }}
                </p>

                <div class="overflow-x-auto mb-4">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th></th>
                                <th>{{ Lang::txt('COM_WISHLIST_SETTINGS_GROUP_CN') }}</th>
                                <th>{{ Lang::txt('COM_WISHLIST_GROUP_NUM_MEMBERS') }}</th>
                                <th>{{ Lang::txt('COM_WISHLIST_GROUP_OPTIONS') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $allmembers = [];
                                $groups = $owners['groups'];
                                $nativeGroups = $wishlist->owners('groups', 1);
                            @endphp
                            @if(count($groups) > 0)
                                @foreach($groups as $k => $gid)
                                    @php
                                        $instance = \Hubzero\User\Group::getInstance($gid);
                                        $members = $instance->get('members');
                                        $allmembers = array_merge($allmembers, $members);
                                        $canRemove = (count($groups) > 1 && !in_array($gid, $nativeGroups));
                                    @endphp
                                    <tr>
                                        <th>{{ $k + 1 }}.</th>
                                        <td>{{ e($instance->get('cn')) }}</td>
                                        <td>{{ count($members) }}</td>
                                        <td>
                                            @if($canRemove)
                                                <a class="btn btn-xs btn-ghost text-error"
                                                   href="{{ Route::url($wishlist->link('savesettings') . '&action=delete&group=' . $gid, false) }}">
                                                    {{ Lang::txt('COM_WISHLIST_OPTION_REMOVE') }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-base-content/60">
                                        {{ Lang::txt('COM_WISHLIST_NO_OWNER_GROUPS_FOUND') }}.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <x-form-field name="newgroups" inputId="field_newgroups"
                              :label="Lang::txt('COM_WISHLIST_SETTINGS_ADD_GROUPS')"
                              :hint="Lang::txt('COM_WISHLIST_GROUP_HINT')">
                    @php
                        $mc = Event::trigger(
                            'hubzero.onGetMultiEntry',
                            [['groups', 'newgroups', 'field_newgroups', '', '']]
                        );
                    @endphp
                    @if(count($mc) > 0)
                        {!! $mc[0] !!}
                    @else
                        <input type="text" name="newgroups" id="field_newgroups"
                               class="input input-bordered w-full" value="" />
                    @endif
                </x-form-field>
            </x-form-section>

            {{-- Individual owners --}}
            <x-form-section :heading="Lang::txt('COM_WISHLIST_INDIVIDUALS')">
                <p class="text-sm text-base-content/60 mb-3">
                    {{ Lang::txt('COM_WISHLIST_INDIVIDUALS_HINT') }}
                </p>

                <div class="overflow-x-auto mb-4">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th></th>
                                <th>{{ Lang::txt('COM_WISHLIST_IND_NAME') }}</th>
                                <th>{{ Lang::txt('COM_WISHLIST_IND_LOGIN') }}</th>
                                <th>{{ Lang::txt('COM_WISHLIST_GROUP_OPTIONS') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $allmembers = array_unique($allmembers);
                                $individuals = $owners['individuals'];
                                $native = $wishlist->getOwners(null, 1);
                            @endphp
                            @if(count($individuals) > count($allmembers))
                                @php $k = 1; @endphp
                                @foreach($individuals as $indId)
                                    @if(!in_array($indId, $allmembers))
                                        @php
                                            $kuser = User::getInstance($indId);
                                            $canRemove = (count($individuals) > 1
                                                && !in_array($indId, $native['individuals']));
                                        @endphp
                                        <tr>
                                            <td>{{ $k }}.</td>
                                            <td>{{ e($kuser->get('name')) }}</td>
                                            <td>{{ e($kuser->get('username')) }}</td>
                                            <td>
                                                @if($canRemove)
                                                    <a class="btn btn-xs btn-ghost text-error"
                                                       href="{{ Route::url($wishlist->link('savesettings') . '&action=delete&user=' . $indId, false) }}">
                                                        {{ Lang::txt('COM_WISHLIST_OPTION_REMOVE') }}
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                        @php $k++; @endphp
                                    @endif
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-base-content/60">
                                        {{ Lang::txt('COM_WISHLIST_NO_IND_FOUND') }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <x-form-field name="newowners" inputId="field_newowners"
                              :label="Lang::txt('COM_WISHLIST_ADD_IND')"
                              :hint="Lang::txt('COM_WISHLIST_ENTER_LOGINS')">
                    @php
                        $mc = Event::trigger(
                            'hubzero.onGetMultiEntry',
                            [['members', 'newowners', 'field_newowners', '', '']]
                        );
                    @endphp
                    @if(count($mc) > 0)
                        {!! $mc[0] !!}
                    @else
                        <input type="text" name="newowners" id="field_newowners"
                               class="input input-bordered w-full" value="" />
                    @endif
                </x-form-field>
            </x-form-section>

            {{-- Advisory committee --}}
            @if($wishlist->config('allow_advisory', 0))
                <x-form-section :heading="Lang::txt('COM_WISHLIST_ADVISORY')">
                    <p class="text-sm text-base-content/60 mb-3">
                        {{ Lang::txt('COM_WISHLIST_ADD_ADVISORY_INFO') }}
                    </p>

                    <div class="overflow-x-auto mb-4">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>{{ Lang::txt('COM_WISHLIST_IND_NAME') }}</th>
                                    <th>{{ Lang::txt('COM_WISHLIST_IND_LOGIN') }}</th>
                                    <th>{{ Lang::txt('COM_WISHLIST_GROUP_OPTIONS') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $advisory = $owners['advisory']; @endphp
                                @if(count($advisory) > 0)
                                    @php $k = 1; @endphp
                                    @foreach($advisory as $advId)
                                        @if(!in_array($advId, $allmembers))
                                            @php $quser = User::getInstance($advId); @endphp
                                            <tr>
                                                <td>{{ $k }}.</td>
                                                <td>{{ e($quser->get('name')) }}</td>
                                                <td>{{ e($quser->get('username')) }}</td>
                                                <td>
                                                    <a class="btn btn-xs btn-ghost text-error"
                                                       href="{{ Route::url($wishlist->link('savesettings') . '&action=delete&user=' . $advId, false) }}">
                                                        {{ Lang::txt('COM_WISHLIST_OPTION_REMOVE') }}
                                                    </a>
                                                </td>
                                            </tr>
                                            @php $k++; @endphp
                                        @endif
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-base-content/60">
                                            {{ Lang::txt('COM_WISHLIST_NO_ADVISORY_FOUND') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <x-form-field name="newadvisory" inputId="field_newadvisory"
                                  :label="Lang::txt('COM_WISHLIST_ADD_ADVISORY_MEMBERS')"
                                  :hint="Lang::txt('COM_WISHLIST_ENTER_LOGINS')">
                        @php
                            $mc = Event::trigger(
                                'hubzero.onGetMultiEntry',
                                [['members', 'newadvisory', 'field_newadvisory', '', '']]
                            );
                        @endphp
                        @if(count($mc) > 0)
                            {!! $mc[0] !!}
                        @else
                            <input type="text" name="newadvisory" id="field_newadvisory"
                                   class="input input-bordered w-full" value="" />
                        @endif
                    </x-form-field>

                    @if($isResource || $isGeneral1)
                        <input type="hidden" name="fields[public]"
                               value="{{ $wishlist->get('public') }}" />
                    @endif
                </x-form-section>
            @endif

            <div class="flex gap-2 mt-6">
                <button type="submit" class="btn btn-primary">
                    {{ Lang::txt('COM_WISHLIST_SAVE') }}
                </button>
                <a class="btn" href="{{ Route::url($wishlist->link(), false) }}">
                    {{ Lang::txt('JCANCEL') }}
                </a>
            </div>

            <input type="hidden" name="listid" value="{{ $wishlist->get('id') }}" />
            <input type="hidden" name="fields[id]" value="{{ $wishlist->get('id') }}" />
            {!! Html::input('token') !!}
        </form>
    </x-page-container>
@endif
