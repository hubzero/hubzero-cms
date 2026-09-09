{{--
 Copyright © 2005-2026 Purdue University. All Rights Reserved.
--}}

<div class="flex items-center justify-center min-h-screen p-4">
    <div class="card bg-base-100 shadow-lg w-full max-w-md">
        <div class="card-body text-center">
            <div class="text-success mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h2 class="text-lg font-semibold mb-4">
                {{ Lang::txt('COM_MAILTO_EMAIL_SENT') }}
            </h2>

            <button type="button" onclick="window.close()" class="btn btn-primary btn-sm">
                {{ Lang::txt('COM_MAILTO_CLOSE_WINDOW') }}
            </button>
        </div>
    </div>
</div>
