{{--
 Copyright © 2005-2026 Purdue University. All Rights Reserved.
--}}

@php
    $oauthToken = $__view->get('oauth_token', '');
@endphp

<x-page-container :title="Lang::txt('COM_OAUTH_AUTHORIZE')">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <form action="{{ Route::url('index.php?option=com_oauth&task=authorize') }}"
                      id="oauth_form"
                      method="post">

                    <input type="hidden" name="oauth_token" value="{{ $oauthToken }}" />

                    <fieldset>
                        <legend class="text-lg font-semibold mb-4">
                            {{ Lang::txt('COM_OAUTH_SIGN_IN') }}
                        </legend>

                        <x-form-field name="username"
                                      :label="Lang::txt('COM_OAUTH_USERNAME')"
                                      :required="true">
                            <input type="text"
                                   id="username"
                                   name="username"
                                   class="input input-bordered w-full"
                                   autocapitalize="off"
                                   autocorrect="off"
                                   autofocus
                                   required
                                   aria-required="true" />
                        </x-form-field>

                        <x-form-field name="password"
                                      :label="Lang::txt('COM_OAUTH_PASSWORD')"
                                      :required="true">
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="input input-bordered w-full"
                                   required
                                   aria-required="true" />
                        </x-form-field>
                    </fieldset>

                    <fieldset class="mt-6">
                        <legend class="text-base font-medium mb-3">
                            {{ Lang::txt('COM_OAUTH_AUTHORIZE_ACCESS') }}
                        </legend>

                        <div class="flex gap-2">
                            <button type="submit"
                                    id="allow"
                                    class="btn btn-primary">
                                {{ Lang::txt('COM_OAUTH_AUTHORIZE_APP') }}
                            </button>
                            <button type="submit"
                                    name="deny"
                                    id="deny"
                                    class="btn btn-ghost">
                                {{ Lang::txt('COM_OAUTH_NO_THANKS') }}
                            </button>
                        </div>
                    </fieldset>

                    {!! Html::input('token') !!}
                </form>
            </div>
        </div>
    </div>
</x-page-container>
