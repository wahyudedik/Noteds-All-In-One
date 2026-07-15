<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import Pin from 'lucide-svelte/icons/pin';
    import PinOff from 'lucide-svelte/icons/pin-off';
    import Archive from 'lucide-svelte/icons/archive';
    import ArchiveRestore from 'lucide-svelte/icons/archive-restore';
    import FileText from 'lucide-svelte/icons/file-text';
    import ListChecks from 'lucide-svelte/icons/list-checks';
    import List from 'lucide-svelte/icons/list';
    import Code from 'lucide-svelte/icons/code';
    import { Badge } from '@/components/ui/badge';
    import type { Note } from '@/types/notes';

    let { note, teamSlug }: { note: Note; teamSlug: string } = $props();

    const typeIcons: Record<string, typeof FileText> = {
        text: FileText,
        checklist: ListChecks,
        list: List,
        code: Code,
    };

    let TypeIcon = $derived(typeIcons[note.type] || FileText);

    function togglePin(e: MouseEvent) {
        e.preventDefault();
        e.stopPropagation();
        router.patch(
            `/${teamSlug}/notes/${note.id}/pin`,
            {},
            { preserveScroll: true },
        );
    }

    function toggleArchive(e: MouseEvent) {
        e.preventDefault();
        e.stopPropagation();
        router.patch(
            `/${teamSlug}/notes/${note.id}/archive`,
            {},
            { preserveScroll: true },
        );
    }
</script>

<Link
    href={`/${teamSlug}/notes/${note.id}`}
    class="group relative flex flex-col rounded-xl border border-border bg-card p-4 transition-all hover:shadow-md hover:border-primary/30"
>
    <!-- Pin button -->
    <button
        type="button"
        onclick={togglePin}
        class="absolute top-2 right-2 inline-flex h-7 w-7 items-center justify-center rounded-md text-muted-foreground opacity-0 group-hover:opacity-100 hover:bg-accent transition-all {note.is_pinned
            ? 'text-primary opacity-100'
            : ''}"
        title={note.is_pinned ? 'Unpin' : 'Pin'}
    >
        {#if note.is_pinned}
            <Pin class="h-4 w-4 fill-current" />
        {:else}
            <PinOff class="h-4 w-4" />
        {/if}
    </button>

    <!-- Archive button -->
    <button
        type="button"
        onclick={toggleArchive}
        class="absolute top-2 right-10 inline-flex h-7 w-7 items-center justify-center rounded-md text-muted-foreground opacity-0 group-hover:opacity-100 hover:bg-accent transition-all"
        title={note.is_archived ? 'Restore' : 'Archive'}
    >
        {#if note.is_archived}
            <ArchiveRestore class="h-4 w-4" />
        {:else}
            <Archive class="h-4 w-4" />
        {/if}
    </button>

    <!-- Type icon -->
    <div class="mb-2 flex items-center gap-2">
        <TypeIcon class="h-4 w-4 text-muted-foreground" />
        <span class="text-xs text-muted-foreground capitalize">{note.type}</span
        >
    </div>

    <!-- Title -->
    <h3 class="mb-1 line-clamp-2 font-semibold text-foreground">
        {note.title}
    </h3>

    <!-- Excerpt -->
    <p class="mb-3 line-clamp-3 text-sm text-muted-foreground">
        {note.excerpt || 'Tanpa konten'}
    </p>

    <!-- Category & Labels -->
    <div class="mt-auto flex flex-wrap gap-1">
        {#if note.category}
            <Badge
                variant="secondary"
                class="text-xs"
                style="background-color: {note.category.color}20; color: {note
                    .category.color}; border-color: {note.category.color}40"
            >
                {note.category.name}
            </Badge>
        {/if}
        {#each note.labels as label}
            <Badge
                variant="outline"
                class="text-xs"
                style="border-color: {label.color}60; color: {label.color}"
            >
                {label.name}
            </Badge>
        {/each}
    </div>

    <!-- Author & Date -->
    <div
        class="mt-3 flex items-center justify-between text-xs text-muted-foreground"
    >
        <span>{note.author?.name}</span>
        <span
            >{new Date(note.updated_at).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
            })}</span
        >
    </div>
</Link>
