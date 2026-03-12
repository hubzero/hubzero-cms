{{--
  com_modules — Menu assignment partial

  Variables: $item (Module model with menuAssignment() and menuAssigned())

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;

  $menuTypes  = \Components\Menus\Helpers\Menus::getMenuLinks();
  $assignment = $item->disableCaching()->purgeCache()->menuAssignment();
  $assigned   = $item->menuAssigned();
@endphp

<x-admin-fieldset legend="{{ Lang::txt('COM_MODULES_MENU_ASSIGNMENT') }}">

    <div class="admin-field">
      <label for="jform_assignment">{{ Lang::txt('COM_MODULES_MODULE_ASSIGN') }}</label>
      <select name="menu[assignment]" id="jform_assignment" class="select select-bordered select-sm">
        {!! Html::select('options',
            \Components\Modules\Helpers\Modules::getAssignmentOptions($item->client_id),
            'value', 'text',
            $assignment,
            true) !!}
      </select>
    </div>

    <div>
      {{-- Global selection buttons --}}
      <div class="flex items-center gap-2 mb-3">
        <span class="text-sm font-medium">{{ Lang::txt('JGLOBAL_MENU_SELECTION') }}</span>
        <div class="flex gap-1 ml-auto">
          <button type="button"
                  class="btn btn-xs btn-ghost"
                  data-check-toggle=".chkbox" data-check-state="true">
            {{ Lang::txt('JGLOBAL_SELECTION_ALL') }}
          </button>
          <button type="button"
                  class="btn btn-xs btn-ghost"
                  data-check-toggle=".chkbox" data-check-state="false">
            {{ Lang::txt('JGLOBAL_SELECTION_NONE') }}
          </button>
          <button type="button"
                  class="btn btn-xs btn-ghost"
                  data-check-toggle=".chkbox" data-check-state="toggle">
            {{ Lang::txt('JGLOBAL_SELECTION_INVERT') }}
          </button>
        </div>
      </div>

      {{-- Tabs: one per menu type --}}
      <div class="tabs tabs-border">
        @foreach ($menuTypes as $i => $type)
          @php $cls = 'chk-menulink-' . $type->id; @endphp
          <input type="radio"
                 name="menu_type_tab"
                 class="tab text-sm"
                 aria-label="{{ $type->title ?: $type->menutype }}"
                 @if ($i === 0) checked @endif />
          <div class="tab-content pt-3">
            {{-- Per-tab selection buttons --}}
            <div class="flex gap-1 mb-2">
              <button type="button"
                      class="btn btn-xs btn-ghost"
                      data-check-toggle=".{{ $cls }}" data-check-state="true">
                {{ Lang::txt('JGLOBAL_SELECTION_ALL') }}
              </button>
              <button type="button"
                      class="btn btn-xs btn-ghost"
                      data-check-toggle=".{{ $cls }}" data-check-state="false">
                {{ Lang::txt('JGLOBAL_SELECTION_NONE') }}
              </button>
              <button type="button"
                      class="btn btn-xs btn-ghost"
                      data-check-toggle=".{{ $cls }}" data-check-state="toggle">
                {{ Lang::txt('JGLOBAL_SELECTION_INVERT') }}
              </button>
            </div>

            @if (!empty($type->links))
              <ul class="list-none m-0 p-0 columns-2 gap-x-6">
                @foreach ($type->links as $link)
                  @php
                    $linkVal = (int) $link->value;
                    if (trim($assignment) == '-') {
                        $checked = false;
                    } elseif ($assignment == 0) {
                        $checked = true;
                    } elseif ($assignment < 0) {
                        $checked = in_array(-$linkVal, $assigned);
                    } else {
                        $checked = in_array($linkVal, $assigned);
                    }
                  @endphp
                  <li class="flex items-center gap-1.5 py-0.5 break-inside-avoid">
                    <input type="checkbox"
                           class="checkbox checkbox-xs chkbox {{ $cls }}"
                           name="menu[assigned][]"
                           value="{{ $linkVal }}"
                           id="link{{ $linkVal }}"
                           @if ($checked) checked @endif />
                    <label for="link{{ $linkVal }}" class="text-xs cursor-pointer">
                      {{ $link->text }}
                    </label>
                  </li>
                @endforeach
              </ul>
            @else
              <p class="text-xs text-muted-foreground">{{ Lang::txt('JNONE') }}</p>
            @endif
          </div>
        @endforeach
      </div>
    </div>

</x-admin-fieldset>
