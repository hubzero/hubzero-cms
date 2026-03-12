{{--
  Citations — Admin statistics view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('CITATION') . ': ' . Lang::txt('CITATION_STATS'), 'citation.png');
@endphp

<form action="{{ Route::url('index.php?option=' . $option, false) }}"
      method="post"
      name="adminForm"
      id="adminForm">

  <table class="admin-table">
    <thead>
      <tr>
        <th scope="col">{{ Lang::txt('YEAR') }}</th>
        <th scope="col">{{ Lang::txt('AFFILIATED') }}</th>
        <th scope="col">{{ Lang::txt('NONAFFILIATED') }}</th>
        <th scope="col">{{ Lang::txt('TOTAL') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($stats as $year => $amt)
        <tr>
          <th>{{ $year }}</th>
          <td>{{ $amt['affiliate'] }}</td>
          <td>{{ $amt['non-affiliate'] }}</td>
          <td>{{ intval($amt['affiliate']) + intval($amt['non-affiliate']) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</form>
