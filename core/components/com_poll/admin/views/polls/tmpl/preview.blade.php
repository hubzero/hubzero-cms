{{--
  Poll Preview — Blade counterpart of preview.php

  Shows a read-only preview of a poll with its options.
  Rendered in a modal/iframe context (tmpl=component).

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<form action="">
  <fieldset>
    <div class="font-semibold text-lg mb-4">
      {{ Lang::txt('COM_POLL_PREVIEW') }}
    </div>
  </fieldset>

  <table class="table table-zebra w-full">
    <caption class="text-left font-bold text-base mb-2">{{ $poll->get('title') }}</caption>
    <tbody>
      @foreach ($options as $option)
        @if ($option->get('text') != '')
          <tr>
            <td class="w-8">
              <input type="radio"
                     name="poll"
                     id="poll-option-{{ $option->get('id') }}"
                     value="{{ $option->get('text') }}"
                     class="radio radio-sm radio-primary"
                     aria-label="{{ $option->get('text') }}" />
            </td>
            <td>
              <label for="poll-option-{{ $option->get('id') }}">{{ $option->get('text') }}</label>
            </td>
          </tr>
        @endif
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2" class="pt-4">
          <button type="button" class="btn btn-primary btn-sm">{{ Lang::txt('COM_POLL_VOTE') }}</button>
          <button type="button" class="btn btn-ghost btn-sm">{{ Lang::txt('COM_POLL_RESULTS') }}</button>
        </td>
      </tr>
    </tfoot>
  </table>
</form>
