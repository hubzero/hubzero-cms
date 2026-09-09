{{--
  Resources contribution — step navigation and resource meta summary.

  Variables from controller:
    $option    — component option string
    $id        — resource ID
    $step      — current step number or 'discard'
    $steps     — array of step names (index 0 = start)
    $progress  — associative array of step completion status
    $resource  — Resource model instance
    $group_cn  — group CN (optional)

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $attachments = 0;
  $authors     = 0;
  $tags        = 0;
  $state       = 'draft';

  if (!isset($progress) || $progress === null) {
      $progress = ['submitted' => 0];
  }

  if ($resource->get('id')) {
      switch ($resource->get('published')) {
          case 1:  $state = 'published';   break;
          case 2:  $state = 'draft';       break;
          case 3:  $state = 'pending';     break;
          case 0:
          default: $state = 'unpublished'; break;
      }

      $attachments = $resource->children()->total();
      $authors     = $resource->authors()->total();
      $tags        = count($resource->tags());
  }

  $group_cn = $group_cn ?? Request::getString('group', '');

  $isSubmitted = ($progress['submitted'] ?? 0) == 1;

  // Badge color mapping for status
  $stateBadge = match ($state) {
      'published'   => 'badge-success',
      'pending'     => 'badge-warning',
      'draft'       => 'badge-info',
      'unpublished' => 'badge-ghost',
      default       => 'badge-ghost',
  };

  // Start URL
  $startUrl = $isSubmitted
      ? Route::url('index.php?option=com_resources&id=' . $id)
      : Route::url('index.php?option=' . $option . '&task=new');

  // Build step-nav data for <x-step-nav>
  // Start with the "Start" step (index 0 in the nav)
  $stepNavItems = [];
  $stepNavItems[] = [
      'label' => Lang::txt('COM_CONTRIBUTE_START'),
      'url'   => $startUrl,
  ];

  for ($i = 1, $n = count($steps); $i < $n; $i++) {
      $stepLabel = Lang::txt('COM_CONTRIBUTE_STEP_' . strtoupper($steps[$i]));
      $stepUrl   = null;

      $isCompletedStep = (isset($progress[$steps[$i]]) && $progress[$steps[$i]] == 1);

      if ($step == $i) {
          // Current step — no URL
          $stepUrl = null;
      } elseif ($isCompletedStep || $step > $i) {
          $stepUrl = Route::url(
              'index.php?option=' . $option
              . '&task=draft&step=' . $i
              . '&id=' . $id
              . '&group=' . $group_cn
          );
      } elseif ($isSubmitted) {
          $stepUrl = Route::url(
              'index.php?option=' . $option
              . '&task=draft&step=' . $i
              . '&id=' . $id
          );
      }

      $stepNavItems[] = [
          'label' => $stepLabel ?: $steps[$i],
          'url'   => $stepUrl,
      ];
  }

  // Current step index for <x-step-nav> (0 = start, 1+ = numbered steps)
  $currentStepIndex = ($step === 0 || $step === 'start') ? 0 : (int) $step;

  // Discard URL
  $discardUrl = Route::url(
      'index.php?option=' . $option . '&task=discard&id=' . $id
  );

  // Title truncation
  $titleStr = '';
  if ($resource->get('title')) {
      $titleStr = \Hubzero\Utility\Str::truncate(
          stripslashes($resource->get('title')),
          150
      );
  }
@endphp

{{-- Resource meta summary --}}
<div class="overflow-x-auto mb-6">
  <table class="table table-sm">
    <thead>
      <tr>
        <th scope="col">{{ Lang::txt('COM_CONTRIBUTE_STEP_TYPE') }}</th>
        <th scope="col">{{ Lang::txt('COM_CONTRIBUTE_TITLE') }}</th>
        <th scope="col" colspan="3">{{ Lang::txt('COM_CONTRIBUTE_ASSOCIATIONS') }}</th>
        <th scope="col">{{ Lang::txt('COM_CONTRIBUTE_STATUS') }}</th>
        @if(!$isSubmitted)
          <th scope="col">
            <span class="sr-only">{{ Lang::txt('JACTION') }}</span>
          </th>
        @endif
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          {{ $resource->type()->get('type', Lang::txt('COM_CONTRIBUTE_NONE')) }}
        </td>
        <td>
          @if($titleStr)
            {{ e($titleStr) }}
          @else
            {{ Lang::txt('COM_CONTRIBUTE_NONE') }}
          @endif
        </td>
        <td>{{ Lang::txt('%s attachment(s)', $attachments) }}</td>
        <td>{{ Lang::txt('%s author(s)', $authors) }}</td>
        <td>{{ Lang::txt('%s tag(s)', $tags) }}</td>
        <td>
          <span class="badge {{ $stateBadge }}">{{ $state }}</span>
        </td>
        @if(!$isSubmitted)
          <td>
            @if($step === 'discard')
              <strong>{{ Lang::txt('JCANCEL') }}</strong>
            @else
              <a class="btn btn-error btn-xs btn-outline"
                 href="{{ $discardUrl }}">
                {{ Lang::txt('JCANCEL') }}
              </a>
            @endif
          </td>
        @endif
      </tr>
    </tbody>
  </table>
</div>

{{-- Step navigation --}}
<nav aria-label="{{ Lang::txt('COM_CONTRIBUTE_STEPS') }}">
  <x-step-nav :steps="$stepNavItems" :current="$currentStepIndex" />
</nav>
