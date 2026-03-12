{{--
  Citation Sponsors — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Citations\Helpers\Permissions::getActions('sponsor');

  Toolbar::title(Lang::txt('CITATIONS') . ': ' . Lang::txt('CITATION_SPONSORS'), 'citations');
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  Toolbar::spacer();
  Toolbar::help('sponsors');
@endphp

<form action="{{ Route::url('index.php?option=' . $option, false) }}"
      method="post"
      name="adminForm"
      id="adminForm">

  <table class="admin-table">
    <thead>
      <tr>
        <th scope="col">{{ Lang::txt('CITATION_ID') }}</th>
        <th scope="col">{{ Lang::txt('CITATION_SPONSORS') }}</th>
        <th scope="col">{{ Lang::txt('CITATION_SPONSORS_LINK') }}</th>
        <th scope="col">{{ Lang::txt('CITATION_SPONSORS_IMAGE') }}</th>
        <th scope="col">{{ Lang::txt('CITATION_SPONSORS_ACTIONS') }}</th>
      </tr>
    </thead>
    <tbody>
      @php
        // Controller passes Sponsor::all() which returns a blank model (query builder).
        // Unwrap: if it's a Relational model, call ->rows() to execute the query.
        $sponsorRows = $sponsors;
        if (is_array($sponsorRows)) {
            // Controller wraps in array() — unwrap first element
            $first = reset($sponsorRows);
            if ($first instanceof \Hubzero\Database\Relational) {
                $sponsorRows = $first->rows()->raw();
            } elseif ($first instanceof \Hubzero\Database\Rows) {
                $sponsorRows = $first->raw();
            }
        } elseif ($sponsorRows instanceof \Hubzero\Database\Relational) {
            $sponsorRows = $sponsorRows->rows()->raw();
        } elseif ($sponsorRows instanceof \Hubzero\Database\Rows) {
            $sponsorRows = $sponsorRows->raw();
        } elseif (!is_array($sponsorRows)) {
            $sponsorRows = [];
        }
      @endphp
      @if(count($sponsorRows) > 0)
        @foreach($sponsorRows as $sponsor)
          @php
            $sid = is_object($sponsor) ? $sponsor->id : ($sponsor['id'] ?? '');
            $editUrl = Route::url(
                'index.php?option=' . $option . '&controller=' . $controller
                . '&task=edit&id=' . $sid,
                false, false
            );
            $removeUrl = Route::url(
                'index.php?option=' . $option . '&controller=' . $controller
                . '&task=remove&id=' . $sid,
                false, false
            );
          @endphp
          <tr>
            <td>{{ is_object($sponsor) ? $sponsor->id : ($sponsor['id'] ?? '') }}</td>
            <td>{{ is_object($sponsor) ? $sponsor->sponsor : ($sponsor['sponsor'] ?? '') }}</td>
            <td>{{ is_object($sponsor) ? $sponsor->link : ($sponsor['link'] ?? '') }}</td>
            <td>{{ is_object($sponsor) ? $sponsor->image : ($sponsor['image'] ?? '') }}</td>
            <td class="space-x-2">
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}" class="btn btn-xs btn-ghost">{{ Lang::txt('JACTION_EDIT') }}</a>
              @endif
              @if($canDo->get('core.delete'))
                <a href="{{ $removeUrl }}" class="btn btn-xs btn-ghost text-error">{{ Lang::txt('JACTION_DELETE') }}</a>
              @endif
            </td>
          </tr>
        @endforeach
      @else
        <tr>
          <td colspan="5" class="text-center text-muted-foreground">
            {{ Lang::txt('COM_CITATIONS_SPONSORS_NO_RESULTS') }}
          </td>
        </tr>
      @endif
    </tbody>
  </table>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" autocomplete="off" />

  {!! Html::input('token') !!}
</form>
