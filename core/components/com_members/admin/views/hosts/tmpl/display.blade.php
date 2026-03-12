{{--
  com_members — Hosts component view (iframe content)

  Variables: $id (user id), $rows (host records), $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\User;

  $canDo = (User::authorise('core.admin', $option)
      || User::authorise('core.edit', $option));
@endphp

<div id="hosts">
  <form action="{!! Route::url('index.php?option=' . $option, false) !!}" method="post">
    @if ($canDo)
      <table>
        <tbody>
          <tr>
            <td>
              <input type="hidden" name="option" value="{{ $option }}" />
              <input type="hidden" name="controller" value="{{ $controller }}" />
              <input type="hidden" name="tmpl" value="component" />
              <input type="hidden" name="id" value="{{ (int) $id }}" />
              <input type="hidden" name="task" value="add" />

              <input type="text"
                     name="host"
                     value=""
                     class="input input-bordered input-sm"
                     placeholder="{{ Lang::txt('COM_MEMBERS_HOSTS_ADD') }}" />
              <button type="submit" class="btn btn-sm btn-primary">
                {{ Lang::txt('COM_MEMBERS_HOSTS_ADD') }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <br />
    @endif

    <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
      <table class="admin-table">
        <tbody>
          @forelse ($rows as $row)
            <tr>
              <td>{{ $row->get('host') }}</td>
              @if ($canDo)
                <td>
                  @php
                    $removeUrl = Route::url(
                        'index.php?option=' . $option
                        . '&controller=' . $controller
                        . '&tmpl=component&task=remove&host=' . $row->get('host')
                        . '&id=' . $id
                        . '&' . Session::getFormToken() . '=1', false
                    );
                  @endphp
                  <a href="{!! $removeUrl !!}" class="link link-hover text-error">
                    {{ Lang::txt('JACTION_DELETE') }}
                  </a>
                </td>
              @endif
            </tr>
          @empty
            <tr>
              <td colspan="2" class="text-center text-muted-foreground">
                {{ Lang::txt('COM_MEMBERS_HOSTS_NONE') }}
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {!! Html::input('token') !!}
  </form>
</div>
