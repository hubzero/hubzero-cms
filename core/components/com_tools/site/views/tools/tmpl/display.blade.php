@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
@endphp

<x-page-container :title="$forgeName">
    <div class="prose max-w-none mb-8">
        <h3>{{ Lang::txt('COM_TOOLS_TOOL_DEVELOPMENT') }}</h3>
        <p>{{ Lang::txt('COM_TOOLS_TOOL_DEVELOPMENT_INTRO', e($forgeName), e(Config::get('sitename'))) }}</p>
    </div>

    @if(User::isGuest())
        @php $registerUrl = Route::url('index.php?option=com_members&controller=register'); @endphp
        <p>
            <a href="{{ $registerUrl }}" class="link link-primary">{{ Lang::txt('COM_TOOLS_SIGN_UP_FREE') }}</a>
        </p>
    @endif

    <div class="overflow-x-auto">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>{{ Lang::txt('COM_TOOLS_TITLE') }}</th>
                    <th>{{ Lang::txt('COM_TOOLS_ALIAS') }}</th>
                    <th>{{ Lang::txt('COM_TOOLS_STATUS') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($apps as $project)
                    @if($project->tool_state != 8)
                        @php
                            $status = ($project->codeaccess == '@OPEN')
                                ? Lang::txt('COM_TOOLS_OPEN_SOURCE')
                                : Lang::txt('COM_TOOLS_CLOSED_SOURCE');
                            $wikiUrl = Route::url('index.php?option=' . $option . '&app=' . $project->toolname . '&task=wiki');
                            $truncTitle = \Hubzero\Utility\Str::truncate(stripslashes($project->title), 50);
                        @endphp
                        <tr>
                            <td><a href="{{ $wikiUrl }}" class="link">{{ $truncTitle }}</a></td>
                            <td><a href="{{ $wikiUrl }}" class="link">{{ e($project->toolname) }}</a></td>
                            <td><span class="badge badge-ghost">{{ $status }}</span></td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="3">{{ Lang::txt('COM_TOOLS_NONE') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-page-container>
