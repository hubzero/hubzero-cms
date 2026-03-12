{{--
  com_modules — Module type picker (tmpl=component popup)

  Variables: $items (array of extension rows with extension_id, name, desc, module)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

<div class="p-4">
  <h2 class="text-lg font-semibold mb-4">{{ Lang::txt('COM_MODULES_TYPE_CHOOSE') }}</h2>

  <table class="admin-table">
    <thead>
      <tr>
        <th scope="col">{{ Lang::txt('JGLOBAL_TITLE') }}</th>
        <th scope="col">{{ Lang::txt('COM_MODULES_HEADING_MODULE') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($items as $item)
        @php
          $link = Route::url('index.php?option=com_modules&task=add&eid=' . $item->extension_id, false);
        @endphp
        <tr>
          <td>
            <a href="{{ $link }}" target="_top" class="link link-hover text-primary font-medium"
               title="{{ $item->name }} :: {{ $item->desc }}">
              {{ $item->name }}
            </a>
            @if ($item->desc)
              <p class="text-xs text-muted-foreground mt-0.5">{{ $item->desc }}</p>
            @endif
          </td>
          <td class="text-sm text-muted-foreground">{{ $item->module }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
