@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
@endphp

@php
    $exec_pu = $config->get('exec_pu', 1);

    $execChoices = [];
    $execChoices[''] = Lang::txt('COM_TOOLS_SELECT_TOP');
    $execChoices['@OPEN'] = ucfirst(Lang::txt('COM_TOOLS_TOOLACCESS_OPEN'));
    $execChoices['@US'] = ucfirst(Lang::txt('COM_TOOLS_TOOLACCESS_US'));
    $execChoices['@D1'] = ucfirst(Lang::txt('COM_TOOLS_TOOLACCESS_D1'));
    if ($exec_pu) {
        $execChoices['@PU'] = ucfirst(Lang::txt('COM_TOOLS_TOOLACCESS_PU'));
    }
    $execChoices['@GROUP'] = ucfirst(Lang::txt('COM_TOOLS_RESTRICTED'))
        . ' ' . Lang::txt('COM_TOOLS_TO')
        . ' ' . Lang::txt('COM_TOOLS_GROUP_OR_GROUPS');

    $codeChoices = [];
    $codeChoices[''] = Lang::txt('COM_TOOLS_SELECT_TOP');
    $codeChoices['@OPEN'] = ucfirst(Lang::txt('COM_TOOLS_OPEN_SOURCE'))
        . ' (' . Lang::txt('COM_TOOLS_OPEN_SOURCE_TIPS') . ')';
    $codeChoices['@DEV'] = ucfirst(Lang::txt('COM_TOOLS_ACCESS_RESTRICTED'));

    $wikiChoices = [];
    $wikiChoices[''] = Lang::txt('COM_TOOLS_SELECT_TOP');
    $wikiChoices['@OPEN'] = ucfirst(Lang::txt('COM_TOOLS_ACCESS_OPEN'));
    $wikiChoices['@DEV'] = ucfirst(Lang::txt('COM_TOOLS_ACCESS_RESTRICTED'));

    if (!is_array($defaults['developers'])) {
        $defaults['developers'] = explode(',', $defaults['developers']);
        $defaults['developers'] = array_map('trim', $defaults['developers']);
    }

    $formAction = Route::url('index.php?option=' . $option);
    $pipelineUrl = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller
        . '&task=pipeline'
    );
@endphp

<x-page-container :title="$title">
    <x-slot:header_extra>
        <ul id="useroptions" class="flex gap-2">
            @if ($id)
                @php
                    $statusUrl = Route::url(
                        'index.php?option=' . $option
                        . '&controller=' . $controller
                        . '&task=status&app=' . $defaults['toolname']
                    );
                @endphp
                <li>
                    <a class="btn btn-sm btn-outline" href="{{ $statusUrl }}">
                        {{ Lang::txt('COM_TOOLS_TOOL_STATUS') }}
                    </a>
                </li>
            @endif
            <li>
                <a class="btn btn-sm btn-outline" href="{{ $pipelineUrl }}">
                    {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_ALL_TOOLS') }}
                </a>
            </li>
        </ul>
    </x-slot:header_extra>

    @if ($__view->getError())
        <div class="alert alert-error mb-4">
            {!! implode('<br />', $__view->getErrors()) !!}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main form area --}}
        <div class="lg:col-span-2">
            <form action="{{ $formAction }}" method="post" id="hubForm"
                  enctype="multipart/form-data" class="space-y-6">

                <input type="hidden" name="toolid" value="{{ $id }}" />
                <input type="hidden" name="option" value="{{ $option }}" />
                <input type="hidden" name="controller" value="{{ $controller }}" />
                <input type="hidden" name="task"
                       value="{{ $id ? 'save' : 'register' }}" />
                <input type="hidden" name="editversion"
                       value="{{ $editversion }}" />
                {!! Html::input('token') !!}

                {{-- About fieldset --}}
                <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
                    <legend class="fieldset-legend">{{ Lang::txt('COM_TOOLS_LEGEND_ABOUT') }}:</legend>

                    {{-- Tool name --}}
                    <div class="form-control w-full mb-4">
                        <label class="label" for="t_toolname">
                            <span class="label-text">
                                {{ Lang::txt('COM_TOOLS_TOOLNAME') }}:
                                @if (!$id)
                                    <span class="text-error">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
                                @endif
                            </span>
                        </label>
                        @if ($id)
                            @php
                                $toolname = $defaults['toolname'];
                                $verLabel = ($editversion == 'current')
                                    ? Lang::txt('COM_TOOLS_CURRENT_VERSION')
                                    : Lang::txt('COM_TOOLS_DEV_VERSION');
                            @endphp
                            <input type="hidden" name="tool[toolname]" id="t_toolname"
                                   value="{{ $toolname }}" />
                            <div class="font-bold">
                                {{ $toolname }}
                                ({{ $verLabel }})
                                @if (isset($defaults['published']) && $defaults['published'])
                                    @php
                                        $allVerUrl = Route::url(
                                            'index.php?option=' . $option
                                            . '&controller=' . $controller
                                            . '&task=versions&app=' . $toolname
                                        );
                                    @endphp
                                    <a href="{{ $allVerUrl }}" class="link link-primary ml-2">
                                        {{ Lang::txt('COM_TOOLS_ALL_VERSIONS') }}
                                    </a>
                                @endif
                            </div>
                        @else
                            <input type="text" name="tool[toolname]" id="t_toolname"
                                   maxlength="15" class="input input-bordered w-full"
                                   value="{{ $__view->escape($defaults['toolname']) }}"
                                   required />
                            <label class="label">
                                <span class="label-text-alt">{{ Lang::txt('COM_TOOLS_HINT_TOOLNAME') }}</span>
                            </label>
                        @endif
                    </div>

                    {{-- Title --}}
                    <x-form-field name="t_title"
                                  :label="Lang::txt('COM_TOOLS_TITLE') . ':'"
                                  required>
                        <input type="text" name="tool[title]" id="t_title"
                               maxlength="127" class="input input-bordered w-full"
                               value="{{ $__view->escape(stripslashes($defaults['title'])) }}"
                               required />
                        <label class="label">
                            <span class="label-text-alt">{{ Lang::txt('COM_TOOLS_HINT_TITLE') }}</span>
                        </label>
                    </x-form-field>

                    {{-- Version --}}
                    <div class="form-control w-full mb-4">
                        <label class="label" for="t_version">
                            <span class="label-text">{{ Lang::txt('COM_TOOLS_VERSION') }}:</span>
                        </label>
                        @if ($editversion == 'current')
                            <input type="hidden" name="tool[version]" id="t_version"
                                   value="{{ $__view->escape($defaults['version']) }}" />
                            <div class="font-bold">{{ $defaults['version'] }}</div>
                            <label class="label">
                                <span class="label-text-alt">
                                    {{ Lang::txt('COM_TOOLS_HINT_VERSION_PUBLISHED') }}
                                </span>
                            </label>
                        @else
                            <input type="text" name="tool[version]" id="t_version"
                                   maxlength="15" class="input input-bordered w-full"
                                   value="{{ $__view->escape($defaults['version']) }}" />
                            <label class="label">
                                <span class="label-text-alt">{{ Lang::txt('COM_TOOLS_HINT_VERSION') }}</span>
                            </label>
                        @endif
                    </div>

                    {{-- Description --}}
                    <x-form-field name="t_description"
                                  :label="Lang::txt('COM_TOOLS_AT_A_GLANCE') . ':'"
                                  required>
                        <input type="text" name="tool[description]" id="t_description"
                               maxlength="256" class="input input-bordered w-full"
                               value="{{ $__view->escape(stripslashes($defaults['description'])) }}"
                               required />
                        <label class="label">
                            <span class="label-text-alt">{{ Lang::txt('COM_TOOLS_HINT_DESCRIPTION') }}</span>
                        </label>
                    </x-form-field>

                    {{-- Resource preview/edit buttons (editing existing with resourceid) --}}
                    @if ($id && isset($defaults['resourceid']))
                        @php
                            $previewUrl = Route::url(
                                'index.php?option=com_resources&id='
                                . $defaults['resourceid'] . '&rev=dev'
                            );
                            $editResUrl = Route::url(
                                'index.php?option=' . $option
                                . '&controller=' . $controller
                                . '&task=resource&app=' . $defaults['toolname']
                            );
                        @endphp
                        <div class="mb-4">
                            <span class="label-text">{{ Lang::txt('COM_TOOLS_DESCRIPTION') }}:</span>
                            <div class="flex gap-2 mt-1">
                                <a class="btn btn-sm btn-outline" href="{{ $previewUrl }}">
                                    {{ Lang::txt('COM_TOOLS_PREVIEW') }}
                                </a>
                                <a class="btn btn-sm btn-outline" href="{{ $editResUrl }}">
                                    {{ Lang::txt('Edit Resource Page') }}
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Screen size --}}
                    <fieldset class="fieldset border border-base-300 p-4 rounded-box mb-4">
                        <legend class="fieldset-legend">
                            {{ $id
                                ? Lang::txt('COM_TOOLS_APPLICATION_SCREEN_SIZE')
                                : Lang::txt('COM_TOOLS_SUGGESTED_SCREEN_SIZE')
                            }}:
                        </legend>
                        <div class="flex items-end gap-2">
                            <div class="form-control">
                                <label class="label" for="vncGeometryX">
                                    <span class="label-text">{{ Lang::txt('COM_TOOLS_MARKER_WIDTH') }}</span>
                                </label>
                                <input type="text" name="tool[vncGeometryX]" id="vncGeometryX"
                                       size="4" maxlength="4" class="input input-bordered w-24"
                                       value="{{ $defaults['vncGeometryX'] }}" />
                            </div>
                            <span class="pb-3">x</span>
                            <div class="form-control">
                                <label class="label" for="vncGeometryY">
                                    <span class="label-text">{{ Lang::txt('COM_TOOLS_MARKER_HEIGHT') }}</span>
                                </label>
                                <input type="text" name="tool[vncGeometryY]" id="vncGeometryY"
                                       size="4" maxlength="4" class="input input-bordered w-24"
                                       value="{{ $defaults['vncGeometryY'] }}" />
                            </div>
                        </div>
                        <label class="label">
                            <span class="label-text-alt">{{ Lang::txt('COM_TOOLS_HINT_VNC') }}</span>
                        </label>
                    </fieldset>

                    {{-- Host requirements --}}
                    @if ($config->get('access-admin-component'))
                        <x-form-field name="t_hostreq"
                                      :label="Lang::txt('COM_TOOLS_HOSTREQ') . ':'">
                            <input type="text" name="tool[hostreq]" id="t_hostreq"
                                   class="input input-bordered w-full"
                                   value="{{ $__view->escape(stripslashes($defaults['hostreq'])) }}" />
                            <label class="label">
                                <span class="label-text-alt">{{ Lang::txt('COM_TOOLS_HINT_HOSTREQ') }}</span>
                            </label>
                        </x-form-field>
                    @else
                        <input type="hidden" name="tool[hostreq]" id="t_hostreq"
                               value="{{ $__view->escape(stripslashes($defaults['hostreq'])) }}" />
                    @endif
                </fieldset>

                {{-- Repository host fieldset --}}
                @if ($id)
                    {{-- Editing existing tool: readonly radios --}}
                    <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box hidden">
                        <legend class="fieldset-legend">{{ Lang::txt('COM_TOOLS_EDIT_REPO_HOST') }}:</legend>

                        @if ($config->get('github', 1))
                            @php
                                $gitExtChecked = (!$defaults['repohost']
                                    || $defaults['repohost'] == 'gitExternal')
                                    ? 'checked' : 'disabled';
                            @endphp
                            <div class="form-control mb-2">
                                <label class="label cursor-pointer justify-start gap-2">
                                    <input readonly type="radio" name="tool[repohost]"
                                           id="tool_repohost_gitexternal"
                                           value="gitExternal"
                                           class="radio"
                                           {{ $gitExtChecked }} />
                                    <span class="label-text">{{ Lang::txt('COM_TOOLS_EDIT_EXT_GIT') }}</span>
                                </label>
                            </div>
                        @endif

                        @if (file_exists('/usr/bin/addrepo.sh'))
                            @php
                                $gitLocalChecked = ($defaults['repohost'] == 'gitLocal')
                                    ? 'checked' : 'disabled';
                            @endphp
                            <div class="form-control mb-2">
                                <label class="label cursor-pointer justify-start gap-2">
                                    <input readonly type="radio" name="tool[repohost]"
                                           id="tool_repohost_gitlocal"
                                           value="gitLocal"
                                           class="radio"
                                           {{ $gitLocalChecked }} />
                                    <span class="label-text">{{ Lang::txt('COM_TOOLS_EDIT_LOCAL_GIT') }}</span>
                                </label>
                            </div>
                        @endif

                        @php
                            $svnLocalChecked = ($defaults['repohost'] == 'svnLocal')
                                ? 'checked' : 'disabled';
                        @endphp
                        <div class="form-control mb-2">
                            <label class="label cursor-pointer justify-start gap-2">
                                <input readonly type="radio" name="tool[repohost]"
                                       id="tool_repohost_svnlocal"
                                       value="svnLocal"
                                       class="radio"
                                       {{ $svnLocalChecked }} />
                                <span class="label-text">{{ Lang::txt('COM_TOOLS_EDIT_LOCAL_SUBVERSION') }}</span>
                            </label>
                        </div>
                    </fieldset>

                    @if ($config->get('github', 1))
                        <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box hidden">
                            <legend class="fieldset-legend">{{ Lang::txt('COM_TOOLS_EDIT_GIT_URL') }}:</legend>
                            <div class="form-control w-full">
                                <label class="label" for="github">
                                    <span class="label-text">{{ Lang::txt('COM_TOOLS_EDIT_GIT_SOURCE') }}:</span>
                                </label>
                                <input readonly type="text" name="tool[github]" id="github"
                                       class="input input-bordered w-full"
                                       placeholder="{{ Lang::txt('COM_TOOLS_EDIT_GIT_SOURCE_PASTE') }}"
                                       value="{{ $defaults['github'] }}" />
                                <label class="label">
                                    <span class="label-text-alt">
                                        @if (file_exists('/usr/bin/addrepo.sh'))
                                            {!! Lang::txt('COM_TOOLS_EDIT_URL_GITPUBPRIV') !!}
                                        @else
                                            {{ Lang::txt('COM_TOOLS_EDIT_URL_GITPUB') }}
                                        @endif
                                    </span>
                                </label>
                            </div>
                        </fieldset>
                    @endif

                    {{-- Publish options (editing existing) --}}
                    <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
                        <legend class="fieldset-legend">{{ Lang::txt('COM_TOOLS_EDIT_PUB_OPT') }}:</legend>

                        <div class="form-control mb-2">
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="radio" name="tool[publishType]"
                                       id="tool_publishType_standard"
                                       value="standard"
                                       class="radio"
                                       @if (!$defaults['publishType'] || $defaults['publishType'] == 'standard') checked @endif />
                                <span class="label-text">{{ Lang::txt('COM_TOOLS_EDIT_PUB_OPT_RAPP') }}</span>
                            </label>
                        </div>

                        @if ($config->get('jupyter', 1))
                            <div class="form-control mb-2">
                                <label class="label cursor-pointer justify-start gap-2">
                                    <input type="radio" name="tool[publishType]"
                                           id="tool_publishType_jupyter"
                                           value="jupyter"
                                           class="radio"
                                           @if ($defaults['publishType'] == 'jupyter') checked @endif />
                                    <span class="label-text">{{ Lang::txt('COM_TOOLS_EDIT_PUB_OPT_JUP') }}</span>
                                </label>
                            </div>
                        @endif

                        @php
                            $simtoolFile = '/usr/share/hubzero-forge/svn/trunk/middleware/invoke.simtool';
                        @endphp
                        @if ($config->get('simtool', 1) && is_file($simtoolFile))
                            <div class="form-control mb-2">
                                <label class="label cursor-pointer justify-start gap-2">
                                    <input type="radio" name="tool[publishType]"
                                           id="tool_publishType_simtool"
                                           value="simtool"
                                           class="radio"
                                           @if ($defaults['publishType'] == 'simtool') checked @endif />
                                    <span class="label-text">{{ Lang::txt('COM_TOOLS_EDIT_PUB_OPT_SIM') }}</span>
                                </label>
                            </div>
                        @endif
                    </fieldset>

                @else
                    {{-- New tool: editable radios --}}
                    <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
                        <legend class="fieldset-legend">{{ Lang::txt('COM_TOOLS_EDIT_REPO_HOST') }}:</legend>

                        @if ($config->get('github', 1))
                            <div class="form-control mb-2">
                                <label class="label cursor-pointer justify-start gap-2">
                                    <input type="radio" name="tool[repohost]"
                                           id="tool_repohost_gitexternal"
                                           value="gitExternal"
                                           class="radio"
                                           @if (!$defaults['repohost'] || $defaults['repohost'] == 'gitExternal') checked @endif />
                                    <span class="label-text">{{ Lang::txt('COM_TOOLS_EDIT_EXT_GIT') }}</span>
                                </label>
                            </div>
                        @endif

                        @if (file_exists('/usr/bin/addrepo.sh'))
                            <div class="form-control mb-2">
                                <label class="label cursor-pointer justify-start gap-2">
                                    <input type="radio" name="tool[repohost]"
                                           id="tool_repohost_gitlocal"
                                           value="gitLocal"
                                           class="radio"
                                           @if ($defaults['repohost'] == 'gitLocal') checked @endif />
                                    <span class="label-text">{{ Lang::txt('COM_TOOLS_EDIT_LOCAL_GIT') }}</span>
                                </label>
                            </div>
                        @endif

                        <div class="form-control mb-2">
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="radio" name="tool[repohost]"
                                       id="tool_repohost_svnlocal"
                                       value="svnLocal"
                                       class="radio"
                                       @if ($defaults['repohost'] == 'svnLocal') checked @endif />
                                <span class="label-text">{{ Lang::txt('COM_TOOLS_EDIT_LOCAL_SUBVERSION') }}</span>
                            </label>
                        </div>
                    </fieldset>

                    @if ($config->get('github', 1))
                        <fieldset id="gitExternalInput"
                                  class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
                            <legend class="fieldset-legend">{{ Lang::txt('COM_TOOLS_EDIT_GIT_URL') }}:</legend>
                            <div class="form-control w-full">
                                <label class="label" for="github">
                                    <span class="label-text">
                                        {{ Lang::txt('COM_TOOLS_EDIT_GIT_SOURCE') }}:
                                        <span class="text-error">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
                                    </span>
                                </label>
                                <input type="text" name="tool[github]" id="github"
                                       class="input input-bordered w-full"
                                       placeholder="{{ Lang::txt('COM_TOOLS_EDIT_GIT_SOURCE_PASTE') }}"
                                       value="{{ $defaults['github'] }}" />
                                <label class="label">
                                    <span class="label-text-alt">
                                        @if (file_exists('/usr/bin/addrepo.sh'))
                                            {!! Lang::txt('COM_TOOLS_EDIT_URL_GITPUBPRIV') !!}
                                        @else
                                            {{ Lang::txt('COM_TOOLS_EDIT_URL_GITPUB') }}
                                        @endif
                                    </span>
                                </label>
                            </div>
                        </fieldset>
                    @endif

                    {{-- Publish options (new tool) --}}
                    <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
                        <legend class="fieldset-legend">{{ Lang::txt('COM_TOOLS_EDIT_PUB_OPT') }}:</legend>

                        <div class="form-control mb-2">
                            <label class="label cursor-pointer justify-start gap-2">
                                <input type="radio" name="tool[publishType]"
                                       id="tool_publishType_standard"
                                       value="standard"
                                       class="radio"
                                       @if (!$defaults['publishType'] || $defaults['publishType'] == 'standard') checked @endif />
                                <span class="label-text">{{ Lang::txt('COM_TOOLS_EDIT_PUB_OPT_RAPP') }}</span>
                            </label>
                        </div>

                        @if ($config->get('jupyter', 1))
                            <div class="form-control mb-2">
                                <label class="label cursor-pointer justify-start gap-2">
                                    <input type="radio" name="tool[publishType]"
                                           id="tool_publishType_jupyter"
                                           value="jupyter"
                                           class="radio"
                                           @if ($defaults['publishType'] == 'jupyter') checked @endif />
                                    <span class="label-text">{{ Lang::txt('COM_TOOLS_EDIT_PUB_OPT_JUP') }}</span>
                                </label>
                            </div>
                        @endif

                        @php
                            $simtoolFile = '/usr/share/hubzero-forge/svn/trunk/middleware/invoke.simtool';
                        @endphp
                        @if ($config->get('simtool', 1) && is_file($simtoolFile))
                            <div class="form-control mb-2">
                                <label class="label cursor-pointer justify-start gap-2">
                                    <input type="radio" name="tool[publishType]"
                                           id="tool_publishType_simtool"
                                           value="simtool"
                                           class="radio"
                                           @if ($defaults['publishType'] == 'simtool') checked @endif />
                                    <span class="label-text">{{ Lang::txt('COM_TOOLS_EDIT_PUB_OPT_SIM') }}</span>
                                </label>
                            </div>
                        @endif
                    </fieldset>
                @endif

                {{-- Access fieldset --}}
                <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
                    <legend class="fieldset-legend">{{ Lang::txt('COM_TOOLS_LEGEND_ACCESS') }}:</legend>

                    {{-- Tool access --}}
                    <div class="form-control w-full mb-4">
                        <label class="label" for="t_exec">
                            <span class="label-text">
                                {{ Lang::txt('COM_TOOLS_TOOL_ACCESS') }}:
                                <span class="text-error">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
                                <span class="tooltip" data-tip="{{ Lang::txt('COM_TOOLS_SIDE_TIPS_TOOLACCESS') }}">
                                    <span class="badge badge-ghost badge-sm cursor-help">?</span>
                                </span>
                            </span>
                        </label>
                        {!! \Components\Tools\Helpers\Html::formSelect(
                            'tool[exec]',
                            't_exec',
                            $execChoices,
                            $defaults['exec'],
                            'select select-bordered w-full groupchoices'
                        ) !!}
                    </div>

                    {{-- Group name (shown when exec=@GROUP) --}}
                    <div id="groupname"
                         class="mb-4 {{ $defaults['exec'] == '@GROUP' ? '' : 'hidden' }}">
                        <div class="form-control w-full">
                            @php
                                $groupsVal = \Components\Tools\Helpers\Html::getGroups(
                                    $defaults['membergroups'],
                                    $id
                                );
                            @endphp
                            <input type="text" name="tool[membergroups]" id="t_groups"
                                   class="input input-bordered w-full"
                                   value="{{ $groupsVal }}" />
                            <label class="label">
                                <span class="label-text-alt">{{ Lang::txt('COM_TOOLS_HINT_GROUPS') }}</span>
                            </label>
                        </div>
                    </div>

                    {{-- Code access --}}
                    <div class="form-control w-full mb-4">
                        <label class="label" for="t_code">
                            <span class="label-text">
                                {{ Lang::txt('COM_TOOLS_CODE_ACCESS') }}:
                                <span class="text-error">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
                                <span class="tooltip" data-tip="{{ strip_tags(Lang::txt('COM_TOOLS_SIDE_TIPS_CODEACCESS')) }}">
                                    <span class="badge badge-ghost badge-sm cursor-help">?</span>
                                </span>
                            </span>
                        </label>
                        {!! \Components\Tools\Helpers\Html::formSelect(
                            'tool[code]',
                            't_code',
                            $codeChoices,
                            $defaults['code'],
                            'select select-bordered w-full'
                        ) !!}
                    </div>

                    {{-- Wiki access --}}
                    <div class="form-control w-full mb-4">
                        <label class="label" for="t_wiki">
                            <span class="label-text">
                                {{ Lang::txt('COM_TOOLS_WIKI_ACCESS') }}:
                                <span class="text-error">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
                                <span class="tooltip" data-tip="{{ Lang::txt('COM_TOOLS_SIDE_TIPS_WIKIACCESS') }}">
                                    <span class="badge badge-ghost badge-sm cursor-help">?</span>
                                </span>
                            </span>
                        </label>
                        {!! \Components\Tools\Helpers\Html::formSelect(
                            'tool[wiki]',
                            't_wiki',
                            $wikiChoices,
                            $defaults['wiki'],
                            'select select-bordered w-full'
                        ) !!}
                    </div>

                    {{-- Development team --}}
                    <x-form-field name="t_team"
                                  :label="Lang::txt('COM_TOOLS_DEVELOPMENT_TEAM') . ':'"
                                  required>
                        @php
                            $devTeamVal = \Components\Tools\Helpers\Html::getDevTeam(
                                $defaults['developers'],
                                $id
                            );
                        @endphp
                        <input type="text" name="tool[developers]" id="t_team"
                               class="input input-bordered w-full"
                               value="{{ $devTeamVal }}" />
                        <label class="label">
                            <span class="label-text-alt">{{ Lang::txt('COM_TOOLS_HINT_TEAM') }}</span>
                        </label>
                    </x-form-field>
                </fieldset>

                {{-- Submit buttons --}}
                <div class="flex gap-2">
                    @php
                        $submitLabel = !$id
                            ? Lang::txt('COM_TOOLS_REGISTER_TOOL')
                            : Lang::txt('COM_TOOLS_SAVE_CHANGES');
                    @endphp
                    <button type="submit" class="btn btn-success">
                        {{ $submitLabel }}
                    </button>

                    @if ($id)
                        @php
                            $cancelUrl = Route::url(
                                'index.php?option=' . $option
                                . '&controller=' . $controller
                                . '&task=status&app=' . $defaults['toolname']
                            );
                        @endphp
                        <a class="btn btn-ghost"
                           href="{{ $cancelUrl }}"
                           title="{{ Lang::txt('COM_TOOLS_HINT_CANCEL') }}">
                            {{ Lang::txt('JCANCEL') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Explanation sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-base-200 p-4 rounded-box">
                @if (!$id)
                    <h3 class="font-bold text-lg mb-2">{{ Lang::txt('COM_TOOLS_SIDE_WHAT_TOOLNAME') }}</h3>
                    <p class="text-sm">{{ Lang::txt('COM_TOOLS_SIDE_TIPS_TOOLNAME') }}</p>
                @else
                    <p class="text-sm">{{ Lang::txt('COM_TOOLS_SIDE_EDIT_TOOL') }}</p>
                @endif

                @if ($config->get('github', 1))
                    <div id="gitExternalExplanation" class="mt-4">
                        <p class="text-sm">
                            @if (file_exists('/usr/bin/addrepo.sh'))
                                {{ Lang::txt('COM_TOOLS_EDIT_EXT_GIT_DESCR_WITHPRIV') }}
                            @else
                                {{ Lang::txt('COM_TOOLS_EDIT_EXT_GIT_DESCR') }}
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-page-container>
