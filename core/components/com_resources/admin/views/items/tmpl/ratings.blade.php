{{--
  Resource Ratings — Display (no_html=1 popup)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}

<table class="admin-table">
  <thead>
    <tr>
      <th colspan="3">{{ Lang::txt('COM_RESOURCES_RATINGS_TITLE') }}</th>
    </tr>
  </thead>
  <tbody>
    @forelse($rows as $row)
      @php
        $thedate = (intval($row->created) != 0)
            ? Date::of($row->created)->toLocal()
            : '';
        $user = User::getInstance($row->user_id);

        $ratingClasses = [
            0.5 => 'half', 1 => 'one', 1.5 => 'onehalf',
            2 => 'two', 2.5 => 'twohalf', 3 => 'three',
            3.5 => 'threehalf', 4 => 'four', 4.5 => 'fourhalf',
            5 => 'five',
        ];
        $class = $ratingClasses[$row->rating] ?? 'none';
      @endphp
      <tr>
        <th>{{ Lang::txt('COM_RESOURCES_RATING_USER') }}:</th>
        <td>{{ $user->get('name') }}</td>
      </tr>
      <tr>
        <th>{{ Lang::txt('COM_RESOURCES_RATING_VALUE') }}:</th>
        <td>
          <p class="avgrating {{ $class }}">
            <span>Rating: {{ $row->rating }} out of 5 stars</span>
          </p>
        </td>
      </tr>
      <tr>
        <th>{{ Lang::txt('COM_RESOURCES_RATING_CREATED') }}:</th>
        <td>{{ $thedate }}</td>
      </tr>
      <tr>
        <th>{{ Lang::txt('COM_RESOURCES_RATING_COMMENT') }}:</th>
        <td>
          @if($row->comment)
            {{ $row->comment }}
          @else
            {{ Lang::txt('COM_RESOURCES_NONE') }}
          @endif
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="2">{{ Lang::txt('COM_RESOURCES_NONE') }}</td>
      </tr>
    @endforelse
  </tbody>
</table>
