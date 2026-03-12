@props([
    'action' => '',
    'accept' => '',
    'maxSize' => '',
    'multiple' => false,
    'listUrl' => '',
])

<div class="file-manager-card">
    <div class="file-manager-header">
        <h3>Files</h3>
    </div>

    <form action="{{ $action }}" method="post" enctype="multipart/form-data" class="file-manager-upload">
        <label class="file-manager-dropzone">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="file-manager-dropzone-icon">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
            </svg>
            <span class="file-manager-dropzone-text">Click to upload or drag and drop</span>
            @if($maxSize)
                <span class="file-manager-dropzone-hint">Max {{ $maxSize }}</span>
            @endif
            <input type="file" name="upload" class="sr-only"
                   {{ $accept ? 'accept=' . $accept : '' }}
                   {{ $multiple ? 'multiple' : '' }} />
        </label>

        <div class="file-manager-actions">
            <button type="submit" class="btn btn-sm btn-primary">Upload</button>
        </div>

        {{ $hiddenFields ?? '' }}
    </form>

    @if($slot->isNotEmpty())
        <div class="file-manager-list">
            {{ $slot }}
        </div>
    @endif
</div>
