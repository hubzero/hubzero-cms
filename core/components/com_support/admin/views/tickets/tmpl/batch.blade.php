{{--
  Support — Batch process tickets

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $tf = Event::trigger('hubzero.onGetMultiEntry', [['tags', 'tags', 'actags', '', '']]);
  $gc = Event::trigger('hubzero.onGetSingleEntryWithSelect', [['groups', 'fields[group]', 'acgroup', '', '', '', 'owner']]);

  $openStatuses = \Components\Support\Models\Status::allOpen()->rows();
  $closedStatuses = \Components\Support\Models\Status::allClosed()->rows();

  $formAction = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
  $formId = ($tmpl == 'component') ? 'component-form' : 'item-form';
@endphp

@if (!$tmpl)
  @php
    \Hubzero\Facades\Toolbar::title(
        Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_TICKET') . ': ' . Lang::txt('Batch Process'),
        'support'
    );
    \Hubzero\Facades\Toolbar::custom('process', 'save', 'save', 'JTOOLBAR_SAVE', false);
    \Hubzero\Facades\Toolbar::cancel();
    \Hubzero\Facades\Toolbar::spacer();
    \Hubzero\Facades\Toolbar::help('ticket');
  @endphp
@endif

@php $__view->css()->css('support.blade')->js('tickets.blade.js'); @endphp

<form
    action="{{ $formAction }}"
    method="post"
    name="adminForm"
    id="{{ $formId }}"
    enctype="multipart/form-data"
>
    @if ($tmpl == 'component')
        <fieldset>
            <div class="configuration">
                <div class="configuration-options">
                    <button
                        type="button"
                        id="btn-save"
                        data-action="{{ $formAction }}"
                    >{{ Lang::txt('Save') }}</button>
                    <button type="button" id="btn-cancel">
                        {{ Lang::txt('Cancel') }}
                    </button>
                </div>
                {{ Lang::txt('Batch Process') }}
            </div>
        </fieldset>
        <input type="hidden" name="no_html" value="1" />
    @endif

    <div class="w-full">
        <fieldset class="adminform">
            <legend><span>{{ Lang::txt('JDETAILS') }}</span></legend>

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
                                name="group"
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
                        <label for="owner">{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_OWNER') }}</label>
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
                        <select name="fields[severity]" id="field-severity">
                            <option value="">{{ Lang::txt('Select...') }}</option>
                            <option value="critical">
                                {{ Lang::txt('COM_SUPPORT_TICKET_SEVERITY_CRITICAL') }}
                            </option>
                            <option value="major">
                                {{ Lang::txt('COM_SUPPORT_TICKET_SEVERITY_MAJOR') }}
                            </option>
                            <option value="normal">
                                {{ Lang::txt('COM_SUPPORT_TICKET_SEVERITY_NORMAL') }}
                            </option>
                            <option value="minor">
                                {{ Lang::txt('COM_SUPPORT_TICKET_SEVERITY_MINOR') }}
                            </option>
                            <option value="trivial">
                                {{ Lang::txt('COM_SUPPORT_TICKET_SEVERITY_TRIVIAL') }}
                            </option>
                        </select>
                    </div>
                </div>
                <div>
                    <div class="input-wrap">
                        <label for="field-status">
                            {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_STATUS') }}
                        </label>
                        <select name="fields[status]" id="field-status">
                            <option value="">{{ Lang::txt('Select...') }}</option>
                            <optgroup label="{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_OPEN') }}">
                                @foreach ($openStatuses as $status)
                                    <option value="{{ $status->get('id') }}">
                                        {{ $status->get('title') }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="{{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPTGROUP_CLOSED') }}">
                                <option value="0">
                                    {{ Lang::txt('COM_SUPPORT_TICKET_COMMENT_OPT_CLOSED') }}
                                </option>
                                @foreach ($closedStatuses as $status)
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
                    <label for="field-category">
                        {{ Lang::txt('COM_SUPPORT_TICKET_FIELD_CATEGORY') }}
                        <select name="fields[category]" id="field-category">
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
        </fieldset>
    </div>

    @foreach ($ids as $id)
        <input type="hidden" name="id[]" value="{{ $id }}" />
    @endforeach

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="process" />

    {!! Html::input('token') !!}
</form>
