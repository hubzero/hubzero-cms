{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Plugins\Members\Messages\Messages as plgMembersMessages;

$__view->css();
$__view->js();
@endphp

@if (!$components->count())
  <div class="alert alert-error">
    <span>{{ Lang::txt('PLG_MEMBERS_MESSAGES_NO_COMPONENTS_FOUND') }}</span>
  </div>
@else
  <form action="{{ Route::url($member->link() . '&active=messages') }}"
        method="post"
        id="hubForm">
    <input type="hidden" name="action" value="savesettings" />

    <div class="overflow-x-auto">
      <table class="table w-full">
        <caption class="text-left py-2">
          <button type="submit" class="btn btn-primary btn-sm">
            {{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_SAVE_SETTINGS') }}
          </button>
        </caption>
        <thead>
          <tr>
            <th scope="col">{{ Lang::txt('PLG_MEMBERS_MESSAGES_SENT_WHEN') }}</th>
            @foreach ($notimethods as $notimethod)
              <th scope="col" class="text-center">
                <label class="flex flex-col items-center gap-1 cursor-pointer">
                  <input type="checkbox"
                         class="checkbox checkbox-sm"
                         name="override[{{ $notimethod }}]"
                         value="all"
                         data-check-group="opt-{{ $notimethod }}" />
                  <span>{{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_' . strtoupper($notimethod)) }}</span>
                </label>
              </th>
            @endforeach
          </tr>
        </thead>
        <tfoot>
          <tr>
            <td colspan="{{ count($notimethods) + 1 }}">
              <button type="submit" class="btn btn-primary btn-sm">
                {{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_SAVE_SETTINGS') }}
              </button>
            </td>
          </tr>
        </tfoot>
        <tbody>
          @php
            $currentSection = '';
          @endphp
          @foreach ($components as $component)
            @if ($component->name != $currentSection)
              @php
                $currentSection = $component->name;
                Lang::load($component->name);
                $displayHeader = Lang::hasKey($component->name)
                    ? Lang::txt($component->name)
                    : ucfirst(str_replace('com_', '', $component->name));
              @endphp
              <tr class="bg-base-200 font-semibold">
                <th scope="col">{{ e($displayHeader) }}</th>
                @foreach ($notimethods as $notimethod)
                  <th scope="col" class="text-center">
                    <span class="sr-only">
                      {{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_' . strtoupper($notimethod)) }}
                    </span>
                  </th>
                @endforeach
              </tr>
            @endif
            <tr>
              <th scope="row">{{ e($component->title) }}</th>
              @foreach ($notimethods as $notimethod)
                <td class="text-center">
                  <label for="setting-{{ $component->action }}-{{ $notimethod }}" class="sr-only">
                    {{ e($component->title) }} - {{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_' . strtoupper($notimethod)) }}
                  </label>
                  <input type="checkbox"
                         class="checkbox checkbox-sm opt-{{ $notimethod }}"
                         id="setting-{{ $component->action }}-{{ $notimethod }}"
                         name="settings[{{ $component->action }}][]"
                         value="{{ $notimethod }}"
                         {{ in_array($notimethod, $settings[$component->action]['methods']) ? 'checked' : '' }} />
                  <input type="hidden"
                         name="ids[{{ $component->action }}][{{ $notimethod }}]"
                         value="{{ $settings[$component->action]['ids'][$notimethod] ?? '0' }}" />
                </td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! Html::input('token') !!}
  </form>
@endif
