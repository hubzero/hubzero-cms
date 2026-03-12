{{--
  Support — Abuse Report detail/action view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $toolbarTitle = Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_ABUSE_REPORTS');

  $reporter = User::getInstance($report->created_by);
  $link     = '';

  if (is_object($reported)) {
      $author = User::getInstance($reported->author);

      if (is_object($author) && $author->get('username')) {
          $title .= $author->get('username');
      } else {
          $title .= Lang::txt('COM_SUPPORT_UNKNOWN');
      }
      $title .= ($reported->anon) ? '(' . Lang::txt('JANONYMOUS') . ')' : '';

      $link = str_replace('/administrator', '', $reported->href);
  }

  $reporterName = (is_object($reporter) && $reporter->get('username'))
      ? $reporter->get('username')
      : Lang::txt('COM_SUPPORT_UNKNOWN');

  $reason = $report->report ? $report->report : $report->subject;

  $formAction = Route::url('index.php?option=' . $option, false);
@endphp

@php
  \Hubzero\Facades\Toolbar::title($toolbarTitle, 'support');
  \Hubzero\Facades\Toolbar::save();
  \Hubzero\Facades\Toolbar::spacer();
  \Hubzero\Facades\Toolbar::help('abusereports');
@endphp

<form action="{{ $formAction }}" method="post" name="adminForm" id="item-form">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-7">
            <fieldset class="adminform">
                <legend>
                    <span>{{ Lang::txt('COM_SUPPORT_REPORT_ITEM_REPORTED_AS_ABUSIVE') }}</span>
                </legend>

                <table class="admintable">
                    <tbody>
                        <tr>
                            <td>
                                <h4>
                                    <a class="modals" href="{{ $link }}">
                                        {{ $title }}
                                    </a>:
                                </h4>
                                <p>
                                    {!! is_object($reported) ? $reported->text : '' !!}
                                </p>
                                @if(is_object($reported) && isset($reported->subject) && $reported->subject != '')
                                    <p>{{ $reported->subject }}</p>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>

            <table class="meta">
                <tbody>
                    <tr>
                        <th scope="row">{{ Lang::txt('COM_SUPPORT_COL_DATE') }}</th>
                        <td>{{ $report->created }}</td>
                    </tr>
                    <tr>
                        <th scope="row">{{ Lang::txt('COM_SUPPORT_REPORT_REPORTED_BY') }}</th>
                        <td>{{ $reporterName }}</td>
                    </tr>
                    <tr>
                        <th scope="row">{{ Lang::txt('COM_SUPPORT_COL_REASON') }}</th>
                        <td>{{ $reason }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="md:col-span-5">
            <fieldset class="adminform">
                <legend>
                    <span>{{ Lang::txt('COM_SUPPORT_REPORT_TAKE_ACTION') }}</span>
                </legend>

                @if($report->state == 0)
                    @php
                        $releaseHint = Lang::txt('COM_SUPPORT_REPORT_RELEASE_ITEM_HINT');
                        $spamHint    = Lang::txt('COM_SUPPORT_REPORT_MARK_AS_SPAM_HINT');
                        $deleteHint  = Lang::txt('COM_SUPPORT_REPORT_DELETE_ITEM_HINT');
                    @endphp
                    <div class="input-wrap" data-hint="{{ $releaseHint }}">
                        <input type="radio" name="task" id="field-task-release" value="release" />
                        <label for="field-task-release">
                            {{ Lang::txt('COM_SUPPORT_REPORT_RELEASE_ITEM') }}
                        </label>
                    </div>

                    <div class="input-wrap" data-hint="{{ $spamHint }}">
                        <input type="radio" name="task" id="field-task-spam" value="spam" />
                        <label for="field-task-spam">
                            {{ Lang::txt('COM_SUPPORT_REPORT_MARK_AS_SPAM') }}
                        </label>
                    </div>

                    <div class="input-wrap" data-hint="{{ $deleteHint }}">
                        <input type="radio" name="task" id="field-task-remove" value="remove" />
                        <label for="field-task-remove">
                            {{ Lang::txt('COM_SUPPORT_REPORT_DELETE_ITEM') }}
                        </label>
                        <span class="hint">{{ $deleteHint }}</span><br />
                        <textarea name="note" id="note" rows="5" cols="25"></textarea>
                    </div>

                    <div class="input-wrap">
                        <input
                            type="radio"
                            name="task"
                            value="cancel"
                            id="field-task-cancel"
                            checked="checked"
                        />
                        <label for="field-task-cancel">
                            {{ Lang::txt('COM_SUPPORT_REPORT_DECIDE_LATER') }}
                        </label>
                    </div>
                @else
                    <p class="warning">{{ Lang::txt('COM_SUPPORT_REPORT_ACTION_TAKEN') }}</p>
                    <input type="hidden" name="task" value="view" />
                @endif
            </fieldset>
        </div>
    </div>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="id" value="{{ $report->id }}" />
    <input type="hidden" name="parentid" value="{{ $parentid }}" />

    {!! Html::input('token') !!}
</form>
