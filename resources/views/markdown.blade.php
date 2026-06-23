<div x-data="markdownViewer()" x-init="init()" wire:ignore class="space-y-4">
    <div class="text-lg font-semibold">
        Help
    </div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/github-markdown-css/github-markdown.min.css">

<style>
.markdown-body {
    background-color: transparent !important;
    color: inherit !important;
}
</style>

    <div class="markdown-body">
        {!! str($content)->markdown()->sanitizeHtml() !!}
    </div>


</div>
