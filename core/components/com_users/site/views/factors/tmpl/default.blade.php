{{--
 * Multi-factor authentication challenge page.
 *
 * Renders factor challenge HTML from authfactors plugins.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}
@php
    use Hubzero\Facades\Lang;
@endphp

<x-page-container :title="Lang::txt('COM_USERS_FACTOR_VERIFICATION')">
    <div class="max-w-lg mx-auto space-y-6">
        @foreach($factors as $factor)
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    {!! $factor->html !!}
                </div>
            </div>
        @endforeach
    </div>
</x-page-container>
