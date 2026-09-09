@php
$editoroptions = isset($params->show_publishing_options);
if (!$editoroptions) {
    $params->show_urls_images_frontend = '0';
}

$formAction = Route::url('index.php?option=com_content&a_id=' . (int) $item->id);
@endphp

<x-page-container :title="$params->get('show_page_heading') ? e($params->get('page_heading')) : ''">

    <form action="{{ $formAction }}"
          method="post"
          name="adminForm"
          id="hubForm"
          class="space-y-6">

        {{-- Editor --}}
        <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
            <legend class="fieldset-legend">{{ Lang::txt('JEDITOR') }}</legend>

            <div class="form-control w-full">
                {!! $form->getLabel('title') !!}
                {!! $form->getInput('title') !!}
            </div>

            @if (is_null($item->id))
                <div class="form-control w-full">
                    {!! $form->getLabel('alias') !!}
                    {!! $form->getInput('alias') !!}
                </div>
            @endif

            <div class="form-control w-full">
                {!! $form->getInput('articletext') !!}
            </div>
        </fieldset>

        {{-- Images & URLs --}}
        @if ($params->get('show_urls_images_frontend'))
            <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
                <legend class="fieldset-legend">{{ Lang::txt('COM_CONTENT_IMAGES_AND_URLS') }}</legend>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control w-full">
                        {!! $form->getLabel('image_intro', 'images') !!}
                        {!! $form->getInput('image_intro', 'images') !!}
                    </div>
                    <div class="form-control w-full">
                        {!! $form->getLabel('image_intro_alt', 'images') !!}
                        {!! $form->getInput('image_intro_alt', 'images') !!}
                    </div>
                    <div class="form-control w-full">
                        {!! $form->getLabel('image_intro_caption', 'images') !!}
                        {!! $form->getInput('image_intro_caption', 'images') !!}
                    </div>
                    <div class="form-control w-full">
                        {!! $form->getLabel('float_intro', 'images') !!}
                        {!! $form->getInput('float_intro', 'images') !!}
                    </div>
                    <div class="form-control w-full">
                        {!! $form->getLabel('image_fulltext', 'images') !!}
                        {!! $form->getInput('image_fulltext', 'images') !!}
                    </div>
                    <div class="form-control w-full">
                        {!! $form->getLabel('image_fulltext_alt', 'images') !!}
                        {!! $form->getInput('image_fulltext_alt', 'images') !!}
                    </div>
                    <div class="form-control w-full">
                        {!! $form->getLabel('image_fulltext_caption', 'images') !!}
                        {!! $form->getInput('image_fulltext_caption', 'images') !!}
                    </div>
                    <div class="form-control w-full">
                        {!! $form->getLabel('float_fulltext', 'images') !!}
                        {!! $form->getInput('float_fulltext', 'images') !!}
                    </div>
                </div>

                <div class="divider">{{ Lang::txt('COM_CONTENT_URLS') ?? 'URLs' }}</div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control w-full">
                        {!! $form->getLabel('urla', 'urls') !!}
                        {!! $form->getInput('urla', 'urls') !!}
                    </div>
                    <div class="form-control w-full">
                        {!! $form->getLabel('urlatext', 'urls') !!}
                        {!! $form->getInput('urlatext', 'urls') !!}
                    </div>
                    {!! $form->getInput('targeta', 'urls') !!}
                    <div class="form-control w-full">
                        {!! $form->getLabel('urlb', 'urls') !!}
                        {!! $form->getInput('urlb', 'urls') !!}
                    </div>
                    <div class="form-control w-full">
                        {!! $form->getLabel('urlbtext', 'urls') !!}
                        {!! $form->getInput('urlbtext', 'urls') !!}
                    </div>
                    {!! $form->getInput('targetb', 'urls') !!}
                    <div class="form-control w-full">
                        {!! $form->getLabel('urlc', 'urls') !!}
                        {!! $form->getInput('urlc', 'urls') !!}
                    </div>
                    <div class="form-control w-full">
                        {!! $form->getLabel('urlctext', 'urls') !!}
                        {!! $form->getInput('urlctext', 'urls') !!}
                    </div>
                    {!! $form->getInput('targetc', 'urls') !!}
                </div>
            </fieldset>
        @endif

        {{-- Publishing --}}
        <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
            <legend class="fieldset-legend">{{ Lang::txt('COM_CONTENT_PUBLISHING') }}</legend>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control w-full">
                    {!! $form->getLabel('catid') !!}
                    {!! $form->getInput('catid') !!}
                </div>
                <div class="form-control w-full">
                    {!! $form->getLabel('created_by_alias') !!}
                    {!! $form->getInput('created_by_alias') !!}
                </div>

                @if ($item->params->get('access-change'))
                    <div class="form-control w-full">
                        {!! $form->getLabel('state') !!}
                        {!! $form->getInput('state') !!}
                    </div>
                    <div class="form-control w-full">
                        {!! $form->getLabel('featured') !!}
                        {!! $form->getInput('featured') !!}
                    </div>
                    <div class="form-control w-full">
                        {!! $form->getLabel('publish_up') !!}
                        {!! $form->getInput('publish_up') !!}
                    </div>
                    <div class="form-control w-full">
                        {!! $form->getLabel('publish_down') !!}
                        {!! $form->getInput('publish_down') !!}
                    </div>
                @endif

                <div class="form-control w-full">
                    {!! $form->getLabel('access') !!}
                    {!! $form->getInput('access') !!}
                </div>
            </div>

            @if (is_null($item->id))
                <div class="text-sm text-base-content/60 mt-2">
                    {{ Lang::txt('COM_CONTENT_ORDERING') }}
                </div>
            @endif
        </fieldset>

        {{-- Language --}}
        <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
            <legend class="fieldset-legend">{{ Lang::txt('JFIELD_LANGUAGE_LABEL') }}</legend>

            <div class="form-control w-full">
                {!! $form->getLabel('language') !!}
                {!! $form->getInput('language') !!}
            </div>
        </fieldset>

        {{-- Metadata --}}
        <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
            <legend class="fieldset-legend">{{ Lang::txt('COM_CONTENT_METADATA') }}</legend>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control w-full">
                    {!! $form->getLabel('metadesc') !!}
                    {!! $form->getInput('metadesc') !!}
                </div>
                <div class="form-control w-full">
                    {!! $form->getLabel('metakey') !!}
                    {!! $form->getInput('metakey') !!}
                </div>
            </div>
        </fieldset>

        {{-- Hidden fields --}}
        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="return" value="{{ $return_page }}" />
        @if ($params->get('enable_category', 0) == 1)
            <input type="hidden" name="fields[catid]" value="{{ $params->get('catid', 1) }}" />
        @endif
        {!! Html::input('token') !!}

        {{-- Buttons --}}
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary">
                {{ Lang::txt('JSAVE') }}
            </button>
            <a class="btn btn-ghost"
               href="{{ Route::url('index.php?option=com_content&view=article&id=' . (int) $item->id) }}">
                {{ Lang::txt('JCANCEL') }}
            </a>
        </div>
    </form>

</x-page-container>
