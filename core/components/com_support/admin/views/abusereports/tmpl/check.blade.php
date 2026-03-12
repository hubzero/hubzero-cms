{{--
  Support — Abuse spam-check tool

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $toolbarTitle = Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_ABUSE_CHECK');

  $formAction = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller . '&task=check', false
  );
@endphp

@php
  \Hubzero\Facades\Toolbar::title($toolbarTitle, 'support');
  \Hubzero\Facades\Toolbar::custom('check', 'purge', '', 'COM_SUPPORT_CHECK', false);
  \Hubzero\Facades\Toolbar::spacer();
  \Hubzero\Facades\Toolbar::help('abusereports');
@endphp

@include('com_support::admin/views/abusereports/tmpl/_submenu')

<form action="{{ $formAction }}" method="post" name="adminForm" id="item-form">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-7">
            <fieldset class="adminform">
                <legend><span>{{ Lang::txt('JDETAILS') }}</span></legend>

                <div class="input-wrap">
                    <label for="field-sample">
                        {{ Lang::txt('COM_SUPPORT_ABUSE_SAMPLE') }}:
                        <span class="required">{{ Lang::txt('JOPTION_REQUIRED') }}</span>
                    </label><br />
                    <textarea
                        name="sample"
                        id="field-sample"
                        cols="35"
                        rows="20"
                    >{{ $sample }}</textarea>
                </div>
            </fieldset>
        </div>
        <div class="md:col-span-5">
            @if($results)
                <fieldset class="adminform">
                    <legend>
                        <span>{{ Lang::txt('COM_SUPPORT_ABUSE_SPAM_REPORT') }}</span>
                    </legend>
                    <table>
                        <tbody>
                            @foreach($results as $result)
                                @php
                                    if (strstr($result['service'], '\\')) {
                                        $parts = explode('\\', $result['service']);
                                        $result['service'] = isset($parts[2]) ? $parts[2] : $result['service'];
                                    }
                                @endphp
                                <tr>
                                    <th>{{ $result['service'] }}</th>
                                    <td>
                                        @if($result['is_spam'])
                                            <span class="text-error font-semibold">spam</span>
                                        @else
                                            <span class="text-success font-semibold">ham</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($result['message'])
                                            <span class="detector-message">{{ $result['message'] }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </fieldset>
            @else
                <p class="info">{{ Lang::txt('COM_SUPPORT_ABUSE_CHECK_ABOUT') }}</p>
            @endif
        </div>
    </div>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="check" />

    {!! Html::input('token') !!}
</form>
