{{--
  Newsletter Templates — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $canDo = \Components\Newsletter\Helpers\Permissions::getActions('template');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATES') }}"
    icon="template"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{{ Lang::txt('COM_NEWSLETTER_TEMPLATE') }}</th>
        </tr>
      </thead>
      <tbody>
        @php $k = 0; @endphp
        @forelse($templates as $template)
          @if($template->deleted)
            @continue
          @endif
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $template->id,
                false, false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $k }}"
                     value="{{ $template->id }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $template->name }}"
                     data-check-item />
            </td>
            <td>
              @if(!$template->editable)
                {{ $template->name }}
                <br />
                <span class="text-xs text-muted-foreground">
                  {{ Lang::txt('COM_NEWSLETTER_TEMPLATE_NOT_EDITABLE_OR_DELETABLE') }}
                </span>
              @else
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary font-medium">
                  {{ $template->name }}
                </a>
              @endif
            </td>
          </tr>
          @php $k++; @endphp
        @empty
          <tr>
            <td colspan="2" class="text-center text-muted-foreground">
              {{ Lang::txt('COM_NEWSLETTER_NO_TEMPLATES') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
