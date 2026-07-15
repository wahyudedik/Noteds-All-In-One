<script lang="ts">
    import { onMount, onDestroy } from 'svelte';
    import { Editor } from '@tiptap/core';
    import StarterKit from '@tiptap/starter-kit';
    import Placeholder from '@tiptap/extension-placeholder';
    import TaskList from '@tiptap/extension-task-list';
    import TaskItem from '@tiptap/extension-task-item';
    import Highlight from '@tiptap/extension-highlight';
    import EditorToolbar from './EditorToolbar.svelte';

    let {
        content = { html: '', text: '' },
        placeholder = 'Mulai menulis...',
        onUpdate,
    }: {
        content?: { html: string; text: string };
        placeholder?: string;
        onUpdate?: (data: { html: string; text: string }) => void;
    } = $props();

    let editorElement: HTMLDivElement;
    let editor: Editor | undefined = $state(undefined);

    onMount(() => {
        editor = new Editor({
            element: editorElement,
            extensions: [
                StarterKit,
                Placeholder.configure({
                    placeholder,
                }),
                TaskList,
                TaskItem.configure({
                    nested: true,
                }),
                Highlight,
            ],
            content: content.html || '',
            onUpdate: ({ editor: ed }) => {
                onUpdate?.({
                    html: ed.getHTML(),
                    text: ed.getText(),
                });
            },
        });
    });

    onDestroy(() => {
        editor?.destroy();
    });

    export function getHTML(): string {
        return editor?.getHTML() ?? '';
    }

    export function getText(): string {
        return editor?.getText() ?? '';
    }
</script>

<div class="rounded-md border border-border">
    {#if editor}
        <EditorToolbar {editor} />
    {/if}
    <div bind:this={editorElement} class="min-h-[200px] max-h-[500px] overflow-y-auto px-3 py-2 focus:outline-none"></div>
</div>

<style>
    :global(.tiptap) {
        outline: none;
    }

    :global(.tiptap p.is-editor-empty:first-child::before) {
        content: attr(data-placeholder);
        float: left;
        color: hsl(var(--muted-foreground));
        pointer-events: none;
        height: 0;
    }

    :global(.tiptap h1) {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0.5rem 0;
    }

    :global(.tiptap h2) {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0.5rem 0;
    }

    :global(.tiptap h3) {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0.5rem 0;
    }

    :global(.tiptap ul) {
        list-style-type: disc;
        padding-left: 1.5rem;
        margin: 0.5rem 0;
    }

    :global(.tiptap ol) {
        list-style-type: decimal;
        padding-left: 1.5rem;
        margin: 0.5rem 0;
    }

    :global(.tiptap blockquote) {
        border-left: 3px solid hsl(var(--primary));
        padding-left: 1rem;
        margin: 0.5rem 0;
        color: hsl(var(--muted-foreground));
    }

    :global(.tiptap pre) {
        background: hsl(var(--muted));
        border-radius: 0.375rem;
        padding: 0.75rem 1rem;
        margin: 0.5rem 0;
        overflow-x: auto;
    }

    :global(.tiptap code) {
        background: hsl(var(--muted));
        border-radius: 0.25rem;
        padding: 0.15rem 0.3rem;
        font-size: 0.875rem;
    }

    :global(.tiptap pre code) {
        background: none;
        padding: 0;
    }

    :global(.tiptap ul[data-type="taskList"]) {
        list-style: none;
        padding-left: 0;
    }

    :global(.tiptap ul[data-type="taskList"] li) {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
    }

    :global(.tiptap ul[data-type="taskList"] li label) {
        margin-top: 0.25rem;
    }

    :global(.tiptap hr) {
        border: none;
        border-top: 1px solid hsl(var(--border));
        margin: 1rem 0;
    }

    :global(.tiptap mark) {
        background-color: #fef08a;
        border-radius: 0.25rem;
        padding: 0 0.15rem;
    }
</style>
