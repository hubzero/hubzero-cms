{{--
 Copyright © 2005-2026 Purdue University. All Rights Reserved.
--}}

@php
    $data = $__view->get('data');
@endphp

<div class="flex items-center justify-center min-h-screen p-4">
    <div class="card bg-base-100 shadow-lg w-full max-w-md">
        <div class="card-body">
            <div class="flex items-center justify-between mb-2">
                <h2 class="card-title text-lg">
                    {{ Lang::txt('COM_MAILTO_EMAIL_TO_A_FRIEND') }}
                </h2>
                <button type="button" onclick="window.close()"
                    class="btn btn-ghost btn-sm btn-square"
                    aria-label="{{ Lang::txt('COM_MAILTO_CLOSE_WINDOW') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <form action="{{ Route::url('index.php?option=com_mailto') }}" method="post">
                <x-form-field name="mailto" inputId="mailto_field"
                    label="{{ Lang::txt('COM_MAILTO_EMAIL_TO') }}" required>
                    <input type="email" id="mailto_field" name="mailto"
                        class="input input-bordered w-full"
                        value="{{ e($data->mailto) }}" required />
                </x-form-field>

                <x-form-field name="sender" inputId="sender_field"
                    label="{{ Lang::txt('COM_MAILTO_SENDER') }}" required>
                    <input type="text" id="sender_field" name="sender"
                        class="input input-bordered w-full"
                        value="{{ e($data->sender) }}" required />
                </x-form-field>

                <x-form-field name="from" inputId="from_field"
                    label="{{ Lang::txt('COM_MAILTO_YOUR_EMAIL') }}" required>
                    <input type="email" id="from_field" name="from"
                        class="input input-bordered w-full"
                        value="{{ e($data->from) }}" required />
                </x-form-field>

                <x-form-field name="subject" inputId="subject_field"
                    label="{{ Lang::txt('COM_MAILTO_SUBJECT') }}">
                    <input type="text" id="subject_field" name="subject"
                        class="input input-bordered w-full"
                        value="{{ e($data->subject) }}" />
                </x-form-field>

                <div class="flex gap-2 justify-end mt-4">
                    <button type="button" onclick="window.close()"
                        class="btn btn-ghost">
                        {{ Lang::txt('JCANCEL') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ Lang::txt('COM_MAILTO_SEND') }}
                    </button>
                </div>

                <input type="hidden" name="layout" value="{{ $__view->getLayout() }}" />
                <input type="hidden" name="option" value="com_mailto" />
                <input type="hidden" name="task" value="send" />
                <input type="hidden" name="tmpl" value="component" />
                <input type="hidden" name="link" value="{{ $data->link }}" />
                {!! Html::input('token') !!}
            </form>
        </div>
    </div>
</div>
