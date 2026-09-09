{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<div id="output">
    @if ($__view->getError())
        <div class="alert alert-error">
            <strong>{{ Lang::txt('COM_TOOLS_NOTICE_PROBLEMS') }}</strong><br>
            {!! implode('<br>* ', $__view->getErrors()) !!}
        </div>
    @endif

    @if (!empty($messages))
        <div class="alert alert-success">
            <strong>{{ Lang::txt('COM_TOOLS_NOTICE_OK_ACTIONS') }}</strong><br>
            {!! implode('<br>* ', $messages) !!}
        </div>
    @endif
</div>
