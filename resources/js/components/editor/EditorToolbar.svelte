<script lang="ts">
    import type { Editor } from '@tiptap/core';
    import Bold from 'lucide-svelte/icons/bold';
    import Italic from 'lucide-svelte/icons/italic';
    import Strikethrough from 'lucide-svelte/icons/strikethrough';
    import Code from 'lucide-svelte/icons/code';
    import Heading1 from 'lucide-svelte/icons/heading-1';
    import Heading2 from 'lucide-svelte/icons/heading-2';
    import Heading3 from 'lucide-svelte/icons/heading-3';
    import List from 'lucide-svelte/icons/list';
    import ListOrdered from 'lucide-svelte/icons/list-ordered';
    import ListChecks from 'lucide-svelte/icons/list-checks';
    import Quote from 'lucide-svelte/icons/quote';
    import Minus from 'lucide-svelte/icons/minus';
    import Undo from 'lucide-svelte/icons/undo';
    import Redo from 'lucide-svelte/icons/redo';
    import Highlighter from 'lucide-svelte/icons/highlighter';
    import CodeBlock from 'lucide-svelte/icons/code-square';
    import { cn } from '@/lib/utils';

    let { editor }: { editor: Editor } = $props();

    function runCommand(command: () => void) {
        command();
    }

    const toolbarGroups = $derived([
        {
            items: [
                {
                    icon: Undo,
                    label: 'Undo',
                    action: () => editor.chain().focus().undo().run(),
                    disabled: !editor.can().undo(),
                },
                {
                    icon: Redo,
                    label: 'Redo',
                    action: () => editor.chain().focus().redo().run(),
                    disabled: !editor.can().redo(),
                },
            ],
        },
        {
            items: [
                {
                    icon: Heading1,
                    label: 'Heading 1',
                    action: () => editor.chain().focus().toggleHeading({ level: 1 }).run(),
                    active: editor.isActive('heading', { level: 1 }),
                },
                {
                    icon: Heading2,
                    label: 'Heading 2',
                    action: () => editor.chain().focus().toggleHeading({ level: 2 }).run(),
                    active: editor.isActive('heading', { level: 2 }),
                },
                {
                    icon: Heading3,
                    label: 'Heading 3',
                    action: () => editor.chain().focus().toggleHeading({ level: 3 }).run(),
                    active: editor.isActive('heading', { level: 3 }),
                },
            ],
        },
        {
            items: [
                {
                    icon: Bold,
                    label: 'Bold',
                    action: () => editor.chain().focus().toggleBold().run(),
                    active: editor.isActive('bold'),
                },
                {
                    icon: Italic,
                    label: 'Italic',
                    action: () => editor.chain().focus().toggleItalic().run(),
                    active: editor.isActive('italic'),
                },
                {
                    icon: Strikethrough,
                    label: 'Strikethrough',
                    action: () => editor.chain().focus().toggleStrike().run(),
                    active: editor.isActive('strike'),
                },
                {
                    icon: Code,
                    label: 'Code',
                    action: () => editor.chain().focus().toggleCode().run(),
                    active: editor.isActive('code'),
                },
                {
                    icon: Highlighter,
                    label: 'Highlight',
                    action: () => editor.chain().focus().toggleHighlight().run(),
                    active: editor.isActive('highlight'),
                },
            ],
        },
        {
            items: [
                {
                    icon: List,
                    label: 'Bullet List',
                    action: () => editor.chain().focus().toggleBulletList().run(),
                    active: editor.isActive('bulletList'),
                },
                {
                    icon: ListOrdered,
                    label: 'Ordered List',
                    action: () => editor.chain().focus().toggleOrderedList().run(),
                    active: editor.isActive('orderedList'),
                },
                {
                    icon: ListChecks,
                    label: 'Task List',
                    action: () => editor.chain().focus().toggleTaskList().run(),
                    active: editor.isActive('taskList'),
                },
            ],
        },
        {
            items: [
                {
                    icon: Quote,
                    label: 'Blockquote',
                    action: () => editor.chain().focus().toggleBlockquote().run(),
                    active: editor.isActive('blockquote'),
                },
                {
                    icon: CodeBlock,
                    label: 'Code Block',
                    action: () => editor.chain().focus().toggleCodeBlock().run(),
                    active: editor.isActive('codeBlock'),
                },
                {
                    icon: Minus,
                    label: 'Horizontal Rule',
                    action: () => editor.chain().focus().setHorizontalRule().run(),
                },
            ],
        },
    ]);
</script>

<div class="flex flex-wrap items-center gap-1 border-b border-border p-1">
    {#each toolbarGroups as group, groupIndex}
        <div class="flex items-center gap-0.5">
            {#each group.items as item}
                <button
                    type="button"
                    title={item.label}
                    disabled={item.disabled}
                    onclick={() => runCommand(item.action)}
                    class={cn(
                        'inline-flex h-8 w-8 items-center justify-center rounded-md transition-colors hover:bg-accent hover:text-accent-foreground',
                        'disabled:pointer-events-none disabled:opacity-50',
                        item.active && 'bg-accent text-accent-foreground',
                    )}
                >
                    <item.icon class="h-4 w-4" />
                </button>
            {/each}
        </div>
        {#if groupIndex < toolbarGroups.length - 1}
            <div class="mx-1 h-6 w-px bg-border"></div>
        {/if}
    {/each}
</div>
