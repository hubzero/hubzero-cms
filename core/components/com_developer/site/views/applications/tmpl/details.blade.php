{{--
  Application details sub-template — client credentials, description, team.

  Variables (set by parent view.blade.php):
    $application  — Application model

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
$team = $application->team()->rows();
@endphp

<div class="overflow-x-auto mb-6">
  <table class="table">
    <tbody>
      <tr>
        <th class="w-48">{{ Lang::txt('COM_DEVELOPER_API_APPLICATION_CLIENT_ID') }}</th>
        <td><code class="text-sm">{{ $application->get('client_id') }}</code></td>
      </tr>
      <tr>
        <th>{{ Lang::txt('COM_DEVELOPER_API_APPLICATION_CLIENT_SECRET') }}</th>
        <td><code class="text-sm">{{ $application->get('client_secret') }}</code></td>
      </tr>
      <tr>
        <th>{{ Lang::txt('COM_DEVELOPER_API_APPLICATION_REDIRECT_URI') }}</th>
        <td>
          @foreach (explode(' ', $application->get('redirect_uri')) as $uri)
            <code class="text-sm">{{ $uri }}</code>
          @endforeach
        </td>
      </tr>
    </tbody>
  </table>
</div>

<div class="divider"></div>

<h3 class="text-lg font-semibold mb-2">
  {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_DESCRIPTION') }}
</h3>
<p class="mb-6">
  {!! nl2br(e($application->get('description'))) !!}
</p>

<div class="divider"></div>

<h3 class="text-lg font-semibold mb-3">
  {{ Lang::txt('COM_DEVELOPER_API_APPLICATION_TEAM_MEMBERS') }}
</h3>

{!! $__view->view('_team')
      ->set('members', $team)
      ->set('cls', 'compact')
      ->loadTemplate() !!}
