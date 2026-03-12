{{--
  Support — Create new ticket

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;
  use Hubzero\Facades\Request;

  $text = ($task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

  $browser = new \Hubzero\Browser\Detector();
  $referer  = Request::getString('HTTP_REFERER', null, 'server');
  $hostname = gethostbyaddr(Request::getString('REMOTE_ADDR', '', 'server'));
  $userAgent = Request::getString('HTTP_USER_AGENT', '', 'server');

  $tf = Event::trigger('hubzero.onGetMultiEntry', [['tags', 'tags', 'actags', '', '']]);
  $gc = Event::trigger('hubzero.onGetSingleEntryWithSelect', [['groups', 'ticket[group_id]', 'acgroup', '', '', '', 'owner']]);
  $mc = Event::trigger('hubzero.onGetMultiEntry', [['members', 'cc', 'acmembers', '', '']]);
@endphp

@php
  \Hubzero\Facades\Toolbar::title(
      Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_TICKET') . ': ' . $text,
      'support'
  );
  \Hubzero\Facades\Toolbar::save();
  \Hubzero\Facades\Toolbar::cancel();
  \Hubzero\Facades\Toolbar::spacer();
  \Hubzero\Facades\Toolbar::help('ticket');
@endphp

@php $__view->css()->css('support.blade')->js('support.blade.js'); @endphp

<form
    action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
    method="post"
    name="adminForm"
    id="item-form"
    enctype="multipart/form-data"
>
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-8">
            <fieldset class="adminform">
                <legend><span>{{ Lang::txt('JDETAILS') }}</span></legend>

                <input
                    type="hidden"
                    name="ticket[summary]"
                    id="field-summary"
                    value="{{ $row->get('summary') }}"
                    size="50"
                />

                <div class="input-wrap">
                    <label for="field-login">
                        {{ Lang::txt('COM_SUPPORT_TICKET_FIELD_LOGIN') }}:
                    </label>
                    <input
                        type="text"
                        name="ticket[login]"
                        id="field-login"
                        value="{{ trim($row->get('login')) }}"
                        size="50"
                    />
                </div>

                <div class="input-wrap">
                    <label for="field-name">
                        {{ Lang::txt('COM_SUPPORT_TICKET_FIELD_NAME') }}:
                    </label>
                    <input
                        type="text"
                        name="ticket[name]"
                        id="field-name"
                        value="{{ trim($row->get('name')) }}"
                        size="50"
                    />
                </div>

                <div class="input-wrap">
                    <label for="field-email">
                        {{ Lang::txt('COM_SUPPORT_TICKET_FIELD_EMAIL') }}:
                    </label>
                    <input
                        type="email"
                        name="ticket[email]"
                        id="field-email"
                        value="{{ $row->get('email') }}"
                        size="50"
                    />
                </div>

                <div class="input-wrap">
                    <label for="field-report">
                        {{ Lang::txt('COM_SUPPORT_TICKET_FIELD_DESCRIPTION') }}:
                    </label>
                    <textarea
                        name="ticket[report]"
                        id="field-report"
                        cols="75"
                        rows="15"
                    >{{ trim($row->get('report') == null ? '' : $row->get('report')) }}</textarea>
                </div>

                <div class="input-wrap">
                    <label for="actags">
                        {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_TAGS') }}
                    </label>
                    @if (count($tf) > 0)
                        {!! $tf[0] !!}
                    @else
                        <input type="text" name="tags" id="actags" value="" />
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="input-wrap">
                            <label for="acgroup">
                                {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_GROUP') }}:
                            </label>
                            @if (count($gc) > 0)
                                {!! $gc[0] !!}
                            @else
                                <input
                                    type="text"
                                    name="ticket[group_id]"
                                    value=""
                                    id="acgroup"
                                    size="30"
                                    autocomplete="off"
                                />
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="input-wrap">
                            <label for="ticketowner">{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_OWNER') }}</label>
                            {!! $lists['owner'] !!}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="input-wrap">
                            <label for="field-severity">
                                {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEVERITY') }}
                            </label>
                            <select name="ticket[severity]" id="field-severity">
                                @foreach (\Components\Support\Helpers\Utilities::getSeverities() as $severity)
                                    <option
                                        value="{{ $severity }}"
                                        @selected($severity == 'normal')
                                    >{{ Lang::txt('COM_SUPPORT_TICKET_SEVERITY_' . strtoupper($severity)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <div class="input-wrap">
                            <label for="field-status">
                                {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_STATUS') }}
                            </label>
                            <select name="ticket[status]" id="field-status">
                                <optgroup label="{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_OPEN') }}">
                                    <option value="0" selected="selected">
                                        {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_NEW') }}
                                    </option>
                                    @foreach (\Components\Support\Models\Status::allOpen()->rows() as $status)
                                        <option value="{{ $status->get('id') }}">
                                            {{ $status->get('title') }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPTGROUP_CLOSED') }}">
                                    <option value="0">
                                        {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_CLOSED') }}
                                    </option>
                                    @foreach (\Components\Support\Models\Status::allClosed()->rows() as $status)
                                        <option value="{{ $status->get('id') }}">
                                            {{ $status->get('title') }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    </div>
                </div>

                @if (isset($lists['categories']) && $lists['categories'])
                    <div class="input-wrap">
                        <label for="ticket-field-category">
                            {{ Lang::txt('COM_SUPPORT_TICKET_FIELD_CATEGORY') }}
                            <select name="ticket[category]" id="ticket-field-category">
                                <option value="">{{ Lang::txt('COM_SUPPORT_NONE') }}</option>
                                @foreach ($lists['categories'] as $category)
                                    <option value="{{ $category->alias }}">
                                        {{ $category->title }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                @endif

                <div class="input-wrap">
                    <label for="field-message">
                        {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_SEND_EMAIL_CC') }}
                    </label>
                    @if (count($mc) > 0)
                        {!! $mc[0] !!}
                    @else
                        <input
                            type="text"
                            name="cc"
                            id="acmembers"
                            value=""
                            size="35"
                        />
                    @endif
                </div>

                <input type="hidden" name="ticket[section]" value="1" />
            </fieldset>
        </div>
        <div class="md:col-span-4">
            <p>{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_FORM_EXPLANATION') }}</p>
        </div>
    </div>

    <input type="hidden" name="ticket[referrer]" value="{{ $referer }}" />
    <input type="hidden" name="ticket[os]" value="{{ $browser->platform() }}" />
    <input type="hidden" name="osver" value="{{ $browser->platformVersion() }}" />
    <input type="hidden" name="ticket[browser]" value="{{ $browser->name() }}" />
    <input type="hidden" name="browserver" value="{{ $browser->version() }}" />
    <input type="hidden" name="ticket[hostname]" value="{{ $hostname }}" />
    <input type="hidden" name="ticket[uas]" value="{{ $userAgent }}" />
    <input type="hidden" name="ticket[open]" value="1" />

    <input type="hidden" name="id" id="ticketid" value="{{ $row->get('id') }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="username" value="{{ User::get('username') }}" />
    <input type="hidden" name="task" value="save" />

    {!! Html::input('token') !!}
</form>
