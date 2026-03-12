{{--
  Admin flash messages — renders HubZero notifications as daisyUI alerts.
--}}
@php
  $messages = [];
  if (app()->bound('notification')) {
      foreach (app('notification')->messages() as $msg) {
          $messages[] = ['type' => $msg['type'] ?? 'info', 'text' => $msg['message'] ?? ''];
      }
  }
  $cls  = [
      'error'   => 'alert-error',
      'warning' => 'alert-warning',
      'info'    => 'alert-info',
      'success' => 'alert-success',
  ];
  $role = [
      'error'   => 'alert',
      'warning' => 'alert',
      'info'    => 'status',
      'success' => 'status',
  ];
@endphp
@if(count($messages))
  <div class="px-6 pt-4 space-y-2" id="system-message-container">
    @foreach($messages as $msg)
      <div class="alert {{ $cls[$msg['type']] ?? 'alert-info' }}"
           role="{{ $role[$msg['type']] ?? 'status' }}">
        {{ $msg['text'] }}
      </div>
    @endforeach
  </div>
@endif
