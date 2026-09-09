{{--
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $default = User::getInstance(0)->picture(0, false);
    $picture = $profile->picture(0, false);
@endphp

<div id="ajax-upload-container">
    <form action="{{ Route::url('index.php?option=' . $option) }}" method="post" enctype="multipart/form-data">
        <h2>{{ Lang::txt('Upload a New Profile Picture') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div id="ajax-upload-left">
                <img
                    id="picture-src"
                    src="{{ $picture }}"
                    alt=""
                    data-default-pic="{{ e($default) }}" />
                @if ($profile->picture() != $default)
                    @php
                        $removeHref = Request::base(true)
                            . '/index.php?option=' . $option
                            . '&amp;controller=' . $controller
                            . '&amp;id=' . $profile->get('id')
                            . '&amp;task=delete&amp;no_html=1&amp;'
                            . Session::getFormToken() . '=1';
                    @endphp
                    <a href="{{ $removeHref }}" id="remove-picture">{{ Lang::txt('[Remove Picture]') }}</a>
                @endif
            </div>
            <div id="ajax-upload-right">
                @php
                    $uploadAction = Request::base(true)
                        . '/index.php?option=' . $option
                        . '&amp;controller=' . $controller
                        . '&amp;id=' . $profile->get('id')
                        . '&amp;task=doajaxupload&amp;no_html=1&amp;'
                        . Session::getFormToken() . '=1';
                @endphp
                <div id="ajax-uploader" data-action="{{ $uploadAction }}"></div>
            </div>
        </div>

        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="task" value="ajaxuploadsave" />
        <input type="hidden" name="id" value="{{ $profile->get('id') }}" />
        <input type="hidden" name="no_html" value="1" />

        {!! Html::input('token') !!}
    </form>
</div>
