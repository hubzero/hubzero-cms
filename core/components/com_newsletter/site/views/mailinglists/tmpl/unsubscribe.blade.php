{{--
 Copyright © 2005-2026 Purdue University. All Rights Reserved.
--}}

@php
    $title = $__view->get('title', '');
    $option = $__view->get('option', 'com_newsletter');
    $controller = $__view->get('controller', 'mailinglists');
    $mailinglist = $__view->get('mailinglist');
@endphp

<x-page-container :title="$title">
    @slot('actions')
        <a class="btn btn-sm"
           href="{{ Route::url('index.php?option=com_newsletter') }}">
            {{ Lang::txt('COM_NEWSLETTER_BROWSE') }}
        </a>
    @endslot

    <div class="max-w-lg mx-auto">
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <form action="{{ Route::url('index.php?option=' . $option) }}"
                      method="post">
                    <x-form-section :heading="Lang::txt('COM_NEWSLETTER_MAILINGLISTS_UNSUBSCRIBE')">
                        <p class="text-sm text-base-content/70 mb-4">
                            {{ Lang::txt('COM_NEWSLETTER_MAILINGLISTS_UNSUBSCRIBE_DESC') }}
                        </p>

                        <div class="mb-4">
                            <p class="font-semibold">{{ e($mailinglist->name) }}</p>
                            <p class="text-sm text-base-content/60">
                                {{ e($mailinglist->description) }}
                            </p>
                        </div>

                        <input type="hidden"
                               name="t"
                               value="{{ e(Request::getString('t', '')) }}" />
                        <input type="hidden"
                               name="e"
                               value="{{ e(Request::getString('e', '')) }}" />

                        @if($mailinglist->id == '-1' && User::get('guest') == 1)
                            @if(User::isGuest())
                                @php
                                    $returnUrl = Request::getString('REQUEST_URI', Route::url('index.php?option=com_newsletter'), 'server');
                                    $loginUrl = Route::url('index.php?option=com_users&view=login&return=' . base64_encode($returnUrl));
                                @endphp
                                <a href="{{ $loginUrl }}"
                                   class="btn btn-primary btn-sm">
                                    {{ Lang::txt('COM_NEWSLETTER_MAILINGLISTS_LOGIN_TO_UNSUBSCRIBE') }}
                                </a>
                            @else
                                <div class="alert alert-success">
                                    {{ Lang::txt('COM_NEWSLETTER_MAILINGLISTS_LOGGEDIN_AS', User::get('username')) }}
                                </div>
                            @endif
                        @else
                            <x-form-field name="reason" inputId="reason"
                                          :label="Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON')">
                                <select name="reason" id="reason"
                                        class="select select-bordered w-full">
                                    <option value="">
                                        {{ Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_DEFAULT') }}
                                    </option>
                                    <option value="Too many emails">
                                        {{ Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_TOOMANY') }}
                                    </option>
                                    <option value="Content isn't relevant to me">
                                        {{ Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_NOTRELEVANT') }}
                                    </option>
                                    <option value="I don't remember signing up">
                                        {{ Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_NOTSIGNEDUP') }}
                                    </option>
                                    <option value="Privacy concerns">
                                        {{ Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_PRIVACY') }}
                                    </option>
                                    <option value="Other">
                                        {{ Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_OTHER') }}
                                    </option>
                                </select>
                            </x-form-field>

                            <x-form-field name="reason-alt" inputId="reason-alt">
                                <textarea rows="4" name="reason-alt" id="reason-alt"
                                          class="textarea textarea-bordered w-full"
                                          placeholder="{{ Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE_REASON_OTHER_OTHER') }}"
                                ></textarea>
                            </x-form-field>
                        @endif
                    </x-form-section>

                    @if(!User::isGuest() || $mailinglist->id != '-1')
                        <div class="mt-4">
                            <button type="submit" class="btn btn-error">
                                {{ Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE') }}
                            </button>
                        </div>
                    @endif

                    <input type="hidden" name="option" value="{{ $option }}" />
                    <input type="hidden" name="controller" value="{{ $controller }}" />
                    <input type="hidden" name="task" value="dounsubscribe" />
                    {!! Html::input('token') !!}
                </form>
            </div>
        </div>
    </div>
</x-page-container>
