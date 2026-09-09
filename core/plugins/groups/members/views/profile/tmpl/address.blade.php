{{--
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @if (count($addresses) < 1)
        <div class="text-base-content/60">
            {{ Lang::txt('PLG_MEMBERS_PROFILE_ADDRESS_ENTER') }}
        </div>
    @else
        @foreach ($addresses as $k => $address)
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body p-4">
                    @if (!empty($address->addressTo))
                        <strong>{{ $address->addressTo }}</strong><br />
                    @endif

                    @if (!empty($address->address1))
                        {{ $address->address1 }}<br />
                    @endif

                    @if (!empty($address->address2))
                        {{ $address->address2 }}<br />
                    @endif

                    {{ $address->addressCity }}
                    {{ $address->addressRegion }},
                    {{ $address->addressPostal }}<br />

                    @if (
                        !empty($address->addressCountry)
                        && !in_array($address->addressCountry, ['US', 'USA', 'United States', 'United States of America'])
                    )
                        {{ $address->addressCountry }}<br />
                    @endif

                    @if ($displayEditLinks)
                        <div class="card-actions mt-2">
                            <a class="btn btn-ghost btn-xs"
                                href="{{ Route::url($profile->link() . '&active=profile&action=editaddress&addressid=' . $address->id) }}">
                                {{ Lang::txt('JACTION_EDIT') }}
                            </a>
                            <a class="btn btn-ghost btn-xs text-error"
                                href="{{ Route::url($profile->link() . '&active=profile&action=deleteaddress&addressid=' . $address->id) }}">
                                {{ Lang::txt('JACTION_DELETE') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
