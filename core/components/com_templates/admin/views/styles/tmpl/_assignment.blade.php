{{--
  com_templates — Menu assignment partial (included by styles/edit)

  Variables: $item (Style model)

  Replaces Html::tabs() with per-menutype <details> accordions.
  The invert button toggles .chk-menulink checkboxes via templates.js.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\User;

  $menuTypes = \Components\Menus\Helpers\Menus::getMenuLinks();
@endphp

<x-admin-fieldset legend="{{ Lang::txt('COM_TEMPLATES_MENUS_ASSIGNMENT') }}">

  <div class="flex items-center justify-between mb-3">
    <span class="text-sm text-muted-foreground">
      {{ Lang::txt('JGLOBAL_MENU_SELECTION') }}
    </span>
    <button type="button" id="invertselection" class="btn btn-xs btn-ghost">
      {{ Lang::txt('JGLOBAL_SELECTION_INVERT') }}
    </button>
  </div>

  <div id="menu-assignment" class="space-y-2">
    @foreach ($menuTypes as $type)
      @php $typeTitle = $type->title ?: $type->menutype; @endphp
      <details class="admin-fieldset">
        <summary class="admin-fieldset-heading">{{ $typeTitle }}</summary>
        <div class="admin-fieldset-body">
          <ul class="menu-links space-y-1">
            @foreach ($type->links as $link)
              @php
                $linkVal  = (int) $link->value;
                $checked  = ($link->template_style_id == $item->id);
                $isLocked = $link->checked_out && $link->checked_out != User::get('id');
              @endphp
              <li class="menu-link flex items-center gap-2">
                <input type="checkbox"
                       name="jform[assigned][]"
                       value="{{ $linkVal }}"
                       id="link{{ $linkVal }}"
                       class="checkbox checkbox-sm{{ $isLocked ? '' : ' chk-menulink' }}"
                       {{ $checked ? 'checked' : '' }}
                       {{ $isLocked ? 'disabled' : '' }} />
                <label for="link{{ $linkVal }}" class="text-sm cursor-pointer">
                  {{ $link->text }}
                </label>
              </li>
            @endforeach
          </ul>
        </div>
      </details>
    @endforeach
  </div>

</x-admin-fieldset>
