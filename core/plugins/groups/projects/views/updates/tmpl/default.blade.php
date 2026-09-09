{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$__view->css();

$__view->view('submenu', 'partials')
    ->set('group', $group)
    ->set('projectcount', $projectcount)
    ->set('newcount', $newcount)
    ->set('tab', 'updates')
    ->display();
@endphp

<section class="main section" id="s-projects">
    @if ($content && in_array(User::get('id'), $group->get('managers')))
        @php
        $blogFormUrl = Route::url(
            'index.php?option=com_groups&cn='
            . $group->get('cn')
            . '&active=projects'
        );
        @endphp
        <div id="blab" class="card bg-base-100 shadow-sm mb-4">
            <div class="card-body">
                <form id="blogForm"
                    method="post"
                    class="focused"
                    action="{{ $blogFormUrl }}">
                    <fieldset>
                        <input type="hidden" name="option" value="com_groups" />
                        <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
                        <input type="hidden" name="task" value="view" />
                        <input type="hidden" name="active" value="projects" />
                        <input type="hidden" name="action" value="update" />
                        <input type="hidden" name="managers_only" value="0" />
                        {!! Html::input('token') !!}

                        {!! $__view->editor(
                            'blogentry',
                            '',
                            5,
                            3,
                            'blogentry',
                            array('class' => 'minimal no-footer')
                        ) !!}

                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="label" for="projectid">
                                    <span class="label-text">{{ Lang::txt('Post to:') }}</span>
                                </label>
                                <select name="projectid" id="projectid" class="select select-bordered w-full">
                                    <option value="0">{{ Lang::txt('PLG_GROUPS_PROJECTS_ALL') }}</option>
                                    @foreach ($projects as $project)
                                        @php
                                        $p = new \Components\Projects\Models\Project($project);
                                        @endphp
                                        <option value="{{ $project }}">{{ $p->get('title') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end justify-end">
                                <input type="submit"
                                    value="{{ Lang::txt('PLG_GROUPS_PROJECTS_SHARE') }}"
                                    id="blog-submit"
                                    class="btn btn-primary" />
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    @endif

    <div id="project-updates">
        {!! $content !!}
    </div>
</section>
