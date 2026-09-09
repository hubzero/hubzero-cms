{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

<div class="max-w-2xl mx-auto">
    <h2 class="text-xl font-semibold mb-4">
        {{ Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_TITLE') }}
    </h2>

    <div class="alert alert-warning mb-4">
        <span>{{ Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_WARNING') }}</span>
    </div>

    <form action="index.php" method="post" class="space-y-4">
        <div class="form-control w-full">
            <label class="label">
                <span class="label-text">
                    {{ Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_MODULE_TITLE') }}
                    <span class="text-error">{{ Lang::txt('required') }}</span>
                </span>
            </label>
            <select name="module" class="select select-bordered w-full" required>
                <option value="">{{ Lang::txt('- Select Module to Push -') }}</option>
                @foreach ($modules as $module)
                    <option value="{{ $module->id }}">{{ $module->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">{{ Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_MODULE_COLUMN') }}</span>
                </label>
                <select name="column" class="select select-bordered w-full">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">{{ Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_MODULE_POSITION') }}</span>
                </label>
                <select name="position" class="select select-bordered w-full">
                    <option value="first">{{ Lang::txt('First') }}</option>
                    <option value="last">{{ Lang::txt('Last') }}</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">{{ Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_MODULE_WIDTH') }}</span>
                </label>
                <select name="width" class="select select-bordered w-full">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">{{ Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_MODULE_HEIGHT') }}</span>
                </label>
                <select name="height" class="select select-bordered w-full">
                    <option value="1">1</option>
                    <option value="2" selected>2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
                <label class="label">
                    <span class="label-text-alt text-base-content/60">{{ Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_HEIGHT_HINT') }}</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end pt-2">
            <button class="btn btn-primary" type="submit">
                {{ Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_BUTTON') }}
            </button>
        </div>

        <input type="hidden" name="option" value="com_members" />
        <input type="hidden" name="controller" value="plugins" />
        <input type="hidden" name="plugin" value="dashboard" />
        <input type="hidden" name="task" value="dopush" />
    </form>
</div>
