@props([
    'notifications' => [],
])

@foreach($notifications as $n)
  @php
    $msg  = is_array($n) ? ($n['message'] ?? $n[0] ?? '') : $n;
    $type = is_array($n) ? ($n['type'] ?? $n[1] ?? '') : '';
    $cls  = match ($type) {
        'error'   => 'alert-error',
        'warning' => 'alert-warning',
        'info'    => 'alert-info',
        default   => 'alert-success',
    };
  @endphp
  <div role="alert" class="alert {{ $cls }} mb-4">
    <span>{{ $msg }}</span>
  </div>
@endforeach
