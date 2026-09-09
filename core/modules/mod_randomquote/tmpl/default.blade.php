{{--
  Random Quote module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if ($quote)
  @php
    $base = rtrim(Request::base(true), '/');
    $text = stripslashes(e($quote->get('quote'))) . ' ';
    $text = substr($text, 0, $charlimit);
    $text = substr($text, 0, strrpos($text, ' '));
  @endphp
  <div class="{{ $module->module }}"{!! $params->get('moduleid') ? ' id="' . $params->get('moduleid') . '"' : '' !!}>
    <blockquote class="border-l-4 border-primary pl-4 italic text-base-content/80">
      <p>
        {{ $text }}
        @if (strlen($quote->get('quote')) > $charlimit)
          @php
            $quoteUrl = $base . '/about/$quotes/?quoteid=' . $quote->get('id');
            $quoteTitle = Lang::txt(
                'MOD_RANDOMQUOTE_VIEW_FULL',
                stripslashes($quote->get('fullname'))
            );
          @endphp
          <a href="{{ $quoteUrl }}"
             $title="{{ $quoteTitle }}"
             class="link link-primary not-italic text-sm">
            {{ Lang::txt('MOD_RANDOMQUOTE_VIEW') }}
          </a>
        @endif
      </p>
    </blockquote>
    <p class="mt-2 text-sm">
      <cite class="font-semibold not-italic">{{ stripslashes($quote->get('fullname')) }}</cite>,
      {{ stripslashes($quote->get('org')) }}
      <span class="text-base-content/40">&mdash;</span>
      <span>{!! Lang::txt('MOD_RANDOMQUOTE_IN', $base . '/about/$quotes') !!}</span>
    </p>
  </div>
@endif
