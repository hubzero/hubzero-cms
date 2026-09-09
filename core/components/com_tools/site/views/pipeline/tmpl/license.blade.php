@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
@endphp

@php
    $open       = ($code == '@OPEN') ? 1 : 0;
    $codeaccess = ($code == '@OPEN') ? 'open' : 'closed';
    $newstate   = ($action == 'confirm') ? 'Approved' : $status['state'];

    $codeChoices = [
        '@OPEN' => Lang::txt('COM_TOOLS_OPEN_SOURCE'),
        '@DEV'  => Lang::txt('COM_TOOLS_CLOSED_SOURCE'),
    ];

    $licenseChoices = [
        '0' => Lang::txt('Choose a template'),
    ];

    if ($licenses) {
        foreach ($licenses as $l) {
            if ($l->name != 'default') {
                $licenseChoices[$l->name] = $l->title;
            }
        }
    }

    $statusUrl = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller
        . '&task=status&app=' . $status['toolname']
    );
    $newUrl = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller
        . '&task=create'
    );
    $licenseFormAction = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller
        . '&task=license&app=' . $status['toolname']
    );
    $licTemplate  = $license_choice['template'];
    $licText      = $__view->escape(stripslashes($license_choice['text']));
    $enterLicTxt  = Lang::txt('COM_TOOLS_ENTER_LICENSE_TEXT');
@endphp

<x-page-container :title="$title">
    <x-slot:actions>
        <a class="btn btn-sm btn-outline" href="{{ $statusUrl }}">
            {{ Lang::txt('COM_TOOLS_TOOL_STATUS') }}
        </a>
        <a class="btn btn-sm btn-outline" href="{{ $newUrl }}">
            {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL') }}
        </a>
    </x-slot:actions>

    @if($__view->getError())
        <div role="alert" class="alert alert-error mb-4">
            <span>{!! implode('<br />', $__view->getErrors()) !!}</span>
        </div>
    @endif

    @if($action == 'confirm')
        {!! \Components\Tools\Helpers\Html::writeApproval('Confirm license') !!}
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Left column: License form --}}
        <div>
            <h3 class="text-lg font-semibold mb-4">
                @if($action == 'edit')
                    {{ Lang::txt('Specify license for next tool release:') }}
                @else
                    {{ Lang::txt('Please confirm your license for this tool release:') }}
                @endif
            </h3>

            <form action="{{ $licenseFormAction }}" method="post"
                  id="licenseForm" name="licenseForm">
                <fieldset class="space-y-4">
                    {{-- Code access --}}
                    <div>
                        <label class="label">
                            <span class="label-text font-medium">
                                {{ Lang::txt('COM_TOOLS_CODE_ACCESS') }}:
                            </span>
                        </label>
                        {!! \Components\Tools\Helpers\Html::formSelect(
                            't_code',
                            't_code',
                            $codeChoices,
                            $code,
                            'select select-bordered w-full',
                            ''
                        ) !!}
                    </div>

                    {{-- Closed source section (hidden by default) --}}
                    <div id="closed-source" class="hidden">
                        <h4 class="font-semibold mb-2">
                            {{ Lang::txt('COM_TOOLS_LICENSE_ARE_YOU_SURE') }}
                        </h4>
                        <div class="alert alert-warning mb-4">
                            <span>{!! Lang::txt('COM_TOOLS_LICENSE_WHY_OPEN_SOURCE') !!}</span>
                        </div>
                        <div class="form-control">
                            <label class="label" for="reason">
                                <span class="label-text">
                                    {{ Lang::txt('COM_TOOLS_LICENSE_CLOSED_REASON') }}
                                    <span class="text-error">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
                                </span>
                            </label>
                            <textarea name="reason" id="reason" cols="30" rows="5"
                                      class="textarea textarea-bordered w-full"></textarea>
                        </div>
                    </div>

                    {{-- Open source section --}}
                    <div id="open-source">
                        <div id="lic" class="form-control mb-4">
                            <label class="label" for="templates">
                                <span class="label-text font-medium">
                                    {{ Lang::txt('COM_TOOLS_LICENSE_TEMPLATE') }}:
                                </span>
                            </label>
                            {!! \Components\Tools\Helpers\Html::formSelect(
                                'templates',
                                'templates',
                                $licenseChoices,
                                $licTemplate,
                                'select select-bordered w-full',
                                ''
                            ) !!}
                        </div>

                        <div class="licinput">
                            <div class="form-control mb-4">
                                <label class="label" for="license">
                                    <span class="label-text">
                                        {{ Lang::txt('COM_TOOLS_LICENSE_TEXT') }}
                                        <span class="text-error">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
                                    </span>
                                </label>
                                <textarea name="license" id="license" cols="50" rows="15"
                                          class="textarea textarea-bordered w-full"
                                          placeholder="{{ $enterLicTxt }}">{{ $licText }}</textarea>
                            </div>

                            @if($licenses)
                                @foreach($licenses as $l)
                                    <div class="hidden" id="{{ $l->name }}">{{ $__view->escape(stripslashes($l->text)) }}</div>
                                @endforeach
                            @endif

                            <input type="hidden" name="option" value="{{ $option }}" />
                            <input type="hidden" name="controller" value="{{ $controller }}" />
                            <input type="hidden" name="task" value="savelicense" />
                            <input type="hidden" name="curcode" id="curcode" value="{{ $open }}" />
                            <input type="hidden" name="newstate" value="{{ $newstate }}" />
                            <input type="hidden" name="action" value="{{ $action }}" />
                            <input type="hidden" name="toolid" value="{{ $status['toolid'] }}" />
                            <input type="hidden" name="alias" value="{{ $status['toolname'] }}" />
                            {!! Html::input('token') !!}
                        </div>

                        <div id="legendnotes" class="bg-base-200 rounded-lg p-4 mb-4">
                            <h3 class="font-semibold mb-2">
                                {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_LICENSE_TEMPLATE_CHOICE') }}
                            </h3>
                            <p class="text-sm">
                                {{ Lang::txt('COM_TOOLS_LICENSE_TEMPLATE_TIP') }}:
                                [{{ strtoupper(Lang::txt('COM_TOOLS_YEAR')) }}],
                                [{{ strtoupper(Lang::txt('COM_TOOLS_OWNER')) }}],
                                [{{ strtoupper(Lang::txt('COM_TOOLS_ORGANIZATION')) }}],
                                [{{ strtoupper(Lang::txt('COM_TOOLS_ONE_LINE_DESCRIPTION')) }}],
                                [{{ strtoupper(Lang::txt('COM_TOOLS_URL')) }}]
                            </p>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label cursor-pointer justify-start gap-3" for="field-authorize">
                                <input type="checkbox" name="authorize" id="field-authorize"
                                       value="1" class="checkbox checkbox-primary" />
                                <span class="label-text">
                                    <span class="text-error">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
                                    {{ Lang::txt('COM_TOOLS_LICENSE_CERTIFY') }}
                                    {{ Lang::txt('COM_TOOLS_LICENSE_UNDER_SPECIFIED') }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <input type="submit" class="btn btn-primary"
                               value="{{ Lang::txt('COM_TOOLS_SAVE') }}" />
                    </div>
                </fieldset>
            </form>
        </div>

        {{-- Right column: Help text --}}
        <div>
            <h3 class="text-lg font-semibold mb-4">
                {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_LICENSE_WHAT_OPTIONS') }}
            </h3>
            <div class="space-y-4">
                <p>
                    <strong>{{ ucfirst(Lang::txt('COM_TOOLS_OPEN_SOURCE')) }}</strong><br />
                    {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_LICENSE_IF_YOU_CHOOSE') }}
                    <a href="http://www.opensource.org/" rel="external" title="Open Source Initiative"
                       class="link link-primary">{{ strtolower(Lang::txt('COM_TOOLS_OPEN_SOURCE')) }}</a>,
                    {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_LICENSE_OPEN_TXT') }}
                </p>
                <p>
                    <strong>{{ ucfirst(Lang::txt('COM_TOOLS_CLOSED_SOURCE')) }}</strong><br />
                    {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_LICENSE_CLOSED_TXT') }}
                </p>
                <h4 class="font-semibold">Need help selecting a license?</h4>
                <p>
                    Find more information at:
                    <a href="https://choosealicense.com" class="link link-primary">https://choosealicense.com</a>
                </p>
            </div>
        </div>
    </div>
</x-page-container>
