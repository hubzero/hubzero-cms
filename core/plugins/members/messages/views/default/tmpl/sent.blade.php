{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$__view->css();
$__view->js();
@endphp

<form action="{{ Route::url($member->link() . '&active=messages&task=sent') }}" method="post">
  <div class="overflow-x-auto">
    <table class="table table-zebra w-full">
      <thead>
        <tr>
          <th scope="col">{{ Lang::txt('PLG_MEMBERS_MESSAGES_SUBJECT') }}</th>
          <th scope="col">{{ Lang::txt('PLG_MEMBERS_MESSAGES_TO') }}</th>
          <th scope="col">{{ Lang::txt('PLG_MEMBERS_MESSAGES_DATE_SENT') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="3">
            @php
              $pageNav = new \Hubzero\Pagination\Paginator(
                  $total,
                  $filters['start'],
                  $filters['limit']
              );
              $pageNav->setAdditionalUrlParam('id', $member->get('id'));
              $pageNav->setAdditionalUrlParam('active', 'messages');
              $pageNav->setAdditionalUrlParam('task', 'sent');
              $pageNav->setAdditionalUrlParam('action', '');
            @endphp
            {!! $pageNav->render() !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @if ($rows)
          @foreach ($rows as $row)
            @php
              $component = (substr($row->component, 0, 4) == 'com_')
                  ? substr($row->component, 4)
                  : $row->component;

              $url = Route::url($member->link() . '&active=messages&msg=' . $row->id);

              $subject = $row->subject;
              if ($component == 'support') {
                  $parts = explode(' ', $row->subject);
                  array_pop($parts);
                  $subject = implode(' ', $parts);
              }

              $date = Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
            @endphp
            <tr>
              <td>
                <a class="message-link" href="{{ $url }}">
                  {{ $subject }}
                </a>
              </td>
              <td>
                @if (strpos($row->type, '_anonymous') === false)
                  @php
                    $toUrl = Route::url(
                        'index.php?option=' . $option . '&id=' . $row->uid
                    );
                  @endphp
                  <a href="{{ $toUrl }}">{{ $row->name }}</a>
                @else
                  {{ Lang::txt('JANONYMOUS') }}
                @endif
              </td>
              <td>
                <time datetime="{{ $row->created }}">{{ $date }}</time>
              </td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="3">{{ Lang::txt('PLG_MEMBERS_MESSAGES_NONE') }}</td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>

  {!! Html::input('token') !!}
</form>
