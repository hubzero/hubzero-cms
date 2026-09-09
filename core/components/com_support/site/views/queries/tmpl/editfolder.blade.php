{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $tmpl = \Hubzero\Facades\Request::getString('tmpl', '');
    $no_html = \Hubzero\Facades\Request::getInt('no_html', 0);
@endphp

@if (!$tmpl && !$no_html)
<header id="content-header">
    <h2>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_EDIT_FOLDER') }}</h2>
</header>

<section class="main section">
@endif
    <div class="section-inner">
        @if ($__view->getError())
            <p class="error">{!! $__view->getError() !!}</p>
        @endif
        @php
            $formAction = \Hubzero\Facades\Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller . '&task=savefolder'
            );
        @endphp
        <form action="{{ $formAction }}" method="post" id="hubForm">
            <fieldset>
                <legend>{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_REPORT_ABUSE') }}</legend>

                <label for="field-title">{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_FIELD_TITLE') }}</label>
                <input
                    type="text"
                    name="fields[title]"
                    id="field-title"
                    value="{{ $__view->escape(stripslashes($row->title)) }}"
                />

                <input type="hidden" name="fields[id]" value="{{ $__view->escape($row->id) }}" />

                <input type="hidden" name="option" value="{{ $option }}" />
                <input type="hidden" name="controller" value="{{ $controller }}" />
                <input
                    type="hidden"
                    name="no_html"
                    value="{{ ($tmpl) ? 1 : \Hubzero\Facades\Request::getInt('no_html', 0) }}"
                />
                <input type="hidden" name="tmpl" value="{{ $__view->escape($tmpl) }}" />
                <input type="hidden" name="task" value="savefolder" />

                {!! \Hubzero\Facades\Html::input('token') !!}
            </fieldset>
            <p class="submit">
                <input
                    type="submit"
                    class="btn btn-success"
                    value="{{ \Hubzero\Facades\Lang::txt('COM_SUPPORT_SUBMIT') }}"
                />
            </p>
        </form>
    </div>
@if (!$no_html)
</section>
@endif
