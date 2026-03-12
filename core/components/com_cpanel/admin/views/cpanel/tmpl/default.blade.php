{{--
  com_cpanel admin dashboard

  Two-column grid of module cards.
    Left column  (icon position):   stat/icon modules
    Right column (cpanel position): list/table modules

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  Toolbar::title(Lang::txt('COM_CPANEL'), 'cpanel');
  Toolbar::help('cpanel');

  $iconModules   = Module::byPosition('icon');
  $cpanelModules = Module::byPosition('cpanel');
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

  {{-- Left column: stat/icon modules --}}
  <div class="space-y-4">
    @forelse ($iconModules as $module)
      @php $rendered = Module::render($module, ['style' => 'none']); @endphp
      @if (trim($rendered) !== '')
        <div class="admin-fieldset">
          <h3 class="admin-fieldset-heading">{{ $module->title }}</h3>
          <div class="admin-fieldset-body">
            {!! $rendered !!}
          </div>
        </div>
      @endif
    @empty
      <p class="text-sm text-muted-foreground p-4">
        {{ Lang::txt('MOD_CPANEL_NO_MODULES') }}
      </p>
    @endforelse
  </div>

  {{-- Right column: list/table modules --}}
  <div class="space-y-4">
    @forelse ($cpanelModules as $module)
      @php $rendered = Module::render($module, ['style' => 'none']); @endphp
      @if (trim($rendered) !== '')
        <div class="admin-fieldset">
          <h3 class="admin-fieldset-heading">{{ $module->title }}</h3>
          <div class="admin-fieldset-body">
            {!! $rendered !!}
          </div>
        </div>
      @endif
    @empty
      <p class="text-sm text-muted-foreground p-4">
        {{ Lang::txt('MOD_CPANEL_NO_MODULES') }}
      </p>
    @endforelse
  </div>

</div>
