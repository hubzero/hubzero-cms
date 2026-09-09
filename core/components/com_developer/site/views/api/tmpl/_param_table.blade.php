{{--
  Reusable parameter table for API documentation.

  Variables:
    $params — Array of [name, type, required, description] tuples

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div class="overflow-x-auto">
  <table class="table table-sm">
    <thead>
      <tr><th>Name</th><th>Type</th><th>Description</th></tr>
    </thead>
    <tbody>
      @foreach ($params as $param)
        <tr>
          <td class="font-mono text-sm">{{ $param[0] }}</td>
          <td class="text-sm text-base-content/60">{{ $param[1] }}</td>
          <td>
            @if ($param[2])
              <span class="badge badge-error badge-xs mr-1">Required</span>
            @endif
            {{ $param[3] }}
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
