@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
@endphp

@php
    $open      = ($code == '@OPEN') ? 1 : 0;
    $codeaccess = ($code == '@OPEN') ? 'open' : 'closed';
    $newstate  = ($action == 'confirm') ? 'Approved' : $status['state'];

    $codeChoices = [
        '@OPEN' => 'open source (anyone can access code)',
        '@DEV'  => 'closed code',
    ];

    $licenseChoices = [
        'c1' => Lang::txt('Load a standard license'),
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

    $versionFormAction = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller
        . '&task=license'
    );

    $licTemplate = $license_choice['template'];
@endphp

<x-page-container :title="$title">
    <x-slot:actions>
        <a class="btn btn-sm btn-outline" href="{{ $statusUrl }}">
            {{ Lang::txt('TOOL_STATUS') }}
        </a>
        <a class="btn btn-sm btn-outline" href="{{ $newUrl }}">
            {{ Lang::txt('CONTRIBTOOL_NEW_TOOL') }}
        </a>
    </x-slot:actions>

    @if ($__view->getError())
        <div class="alert alert-error mb-4">
            {{ $__view->getError() }}
        </div>
    @endif

    @if ($action == 'confirm')
        @php
            \Components\Tools\Helpers\Html::writeApproval('Confirm license');
        @endphp
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left column: License form --}}
        <div>
            <h3 class="text-lg font-semibold mb-4">
                @if ($action == 'edit')
                    {{ Lang::txt('Specify license for next tool release:') }}
                @else
                    {{ Lang::txt('Please confirm your license for this tool release:') }}
                @endif
            </h3>

            <form action="{{ $versionFormAction }}" method="post"
                id="versionForm" name="versionForm" class="space-y-4">
                <fieldset>
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">{{ Lang::txt('CODE_ACCESS') }}:</span>
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

                    <div id="lic_cl" class="form-control w-full">
                        <label class="label">
                            <span class="label-text">{{ Lang::txt('LICENSE') }}:</span>
                        </label>
                        <textarea
                            name="license"
                            cols="50"
                            rows="15"
                            id="license"
                            class="textarea textarea-bordered w-full font-mono text-sm"
                        >{{ stripslashes($license_choice['text']) }}</textarea>
                    </div>

                    @if ($licenses)
                        @foreach ($licenses as $l)
                            <input
                                type="hidden"
                                name="{{ $__view->escape($l->name) }}"
                                id="{{ $__view->escape($l->name) }}"
                                value="{{ stripslashes($l->text) }}"
                            />
                        @endforeach
                    @endif

                    <div id="lic" class="form-control w-full">
                        <label class="label">
                            <span class="label-text">{{ Lang::txt('LICENSE_TEMPLATE') }}:</span>
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

                    <div id="legendnotes" class="text-sm mt-2">
                        <p>
                            {{ Lang::txt('LICENSE_TEMPLATE_TIP') }}:
                            <br />[{{ Lang::txt('YEAR') }}]
                            <br />[{{ Lang::txt('OWNER') }}]
                            <br />[{{ Lang::txt('ORGANIZATION') }}]
                            <br />[{{ strtoupper(Lang::txt('ONE_LINE_DESCRIPTION')) }}]
                            <br />[{{ Lang::txt('URL') }}]
                        </p>

                        <label class="flex items-start gap-2 cursor-pointer mt-3">
                            <input
                                type="checkbox"
                                name="authorize"
                                value="1"
                                class="checkbox checkbox-sm mt-0.5"
                            />
                            <span>
                                {{ Lang::txt('LICENSE_CERTIFY') }}
                                <strong>{{ Lang::txt('OPEN_SOURCE') }}</strong>
                                {{ Lang::txt('LICENSE_UNDER_SPECIFIED') }}
                            </span>
                        </label>
                    </div>

                    <input type="hidden" name="option" value="{{ $__view->escape($option) }}" />
                    <input type="hidden" name="controller" value="{{ $__view->escape($controller) }}" />
                    <input type="hidden" name="task" value="savelicense" />
                    <input type="hidden" name="curcode" id="curcode" value="{{ $open }}" />
                    <input type="hidden" name="newstate" value="{{ $__view->escape($newstate) }}" />
                    <input type="hidden" name="action" value="{{ $__view->escape($action) }}" />
                    <input type="hidden" name="toolid" value="{{ $__view->escape($status['toolid']) }}" />
                    <input type="hidden" name="alias" value="{{ $__view->escape($status['toolname']) }}" />
                    {!! Html::input('token') !!}

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ Lang::txt('Save') }}
                        </button>
                    </div>
                </fieldset>
            </form>
        </div>

        {{-- Right column: Help text --}}
        <div class="space-y-4">
            <h3 class="text-lg font-semibold">
                {{ Lang::txt('CONTRIBTOOL_LICENSE_WHAT_OPTIONS') }}
            </h3>

            <p>
                <strong>{{ ucfirst(Lang::txt('OPEN_SOURCE')) }}</strong><br />
                {{ Lang::txt('CONTRIBTOOL_LICENSE_IF_YOU_CHOOSE') }}
                <a href="http://www.opensource.org/"
                    rel="external"
                    title="Open Source Initiative"
                    class="link link-primary"
                >{{ strtolower(Lang::txt('OPEN_SOURCE')) }}</a>,
                {{ Lang::txt('CONTRIBTOOL_LICENSE_OPEN_TXT') }}
            </p>

            <div class="alert alert-error">
                {{ Lang::txt('CONTRIBTOOL_LICENSE_ATTENTION') }}
            </div>

            <p>
                <strong>{{ ucfirst(Lang::txt('CLOSED_SOURCE')) }}</strong><br />
                {{ Lang::txt('CONTRIBTOOL_LICENSE_CLOSED_TXT') }}
            </p>
        </div>
    </div>
</x-page-container>
