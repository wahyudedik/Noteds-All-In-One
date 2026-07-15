<script module lang="ts">
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Catatan',
                href: props.currentTeam
                    ? `/${props.currentTeam.slug}/notes`
                    : '/',
            },
            {
                title: props.note?.title || 'Detail',
                href: '#',
            },
        ],
    });
</script>

<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import ArrowLeft from 'lucide-svelte/icons/arrow-left';
    import Pencil from 'lucide-svelte/icons/pencil';
    import Trash2 from 'lucide-svelte/icons/trash-2';
    import Pin from 'lucide-svelte/icons/pin';
    import PinOff from 'lucide-svelte/icons/pin-off';
    import Archive from 'lucide-svelte/icons/archive';
    import ArchiveRestore from 'lucide-svelte/icons/archive-restore';
    import FileText from 'lucide-svelte/icons/file-text';
    import ListChecks from 'lucide-svelte/icons/list-checks';
    import List from 'lucide-svelte/icons/list';
    import Code from 'lucide-svelte/icons/code';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import { Badge } from '@/components/ui/badge';
    import { Separator } from '@/components/ui/separator';
    import { Checkbox } from '@/components/ui/checkbox';
    import type { Note } from '@/types/notes';

    let {
        note,
        team,
    }: {
        note: Note;
        team: { id: number; name: string; slug: string };
    } = $props();

    const typeIcons: Record<string, typeof FileText> = {
        text: FileText,
        checklist: ListChecks,
        list: List,
        code: Code,
    };

    let TypeIcon = $derived(typeIcons[note.type] || FileText);

    function handlePin() {
        router.patch(
            `/${team.slug}/notes/${note.id}/pin`,
            {},
            { preserveScroll: true },
        );
    }

    function handleArchive() {
        router.patch(
            `/${team.slug}/notes/${note.id}/archive`,
            {},
            { preserveScroll: true },
        );
    }

    function handleDelete() {
        if (confirm('Hapus catatan ini?')) {
            router.delete(`/${team.slug}/notes/${note.id}`);
        }
    }
</script>

<AppHead title={note.title} />

<div class="mx-auto max-w-3xl space-y-6 p-4">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a
            href={`/${team.slug}/notes`}
            class="inline-flex h-9 w-9 items-center justify-center rounded-md text-muted-foreground hover:bg-accent"
        >
            <ArrowLeft class="h-5 w-5" />
        </a>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <TypeIcon class="h-5 w-5 text-muted-foreground" />
                <h1 class="text-2xl font-bold text-foreground">{note.title}</h1>
                {#if note.is_pinned}
                    <Pin class="h-5 w-5 text-primary fill-primary" />
                {/if}
            </div>
            <div
                class="mt-1 flex items-center gap-3 text-sm text-muted-foreground"
            >
                <span>Oleh {note.author?.name}</span>
                <span>•</span>
                <span
                    >{new Date(note.updated_at).toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                    })}</span
                >
            </div>
        </div>
        <div class="flex items-center gap-1">
            <Button
                variant="ghost"
                size="sm"
                onclick={handlePin}
                title={note.is_pinned ? 'Unpin' : 'Pin'}
            >
                {#if note.is_pinned}
                    <PinOff class="h-4 w-4" />
                {:else}
                    <Pin class="h-4 w-4" />
                {/if}
            </Button>
            <Button
                variant="ghost"
                size="sm"
                onclick={handleArchive}
                title={note.is_archived ? 'Restore' : 'Archive'}
            >
                {#if note.is_archived}
                    <ArchiveRestore class="h-4 w-4" />
                {:else}
                    <Archive class="h-4 w-4" />
                {/if}
            </Button>
            <a href={`/${team.slug}/notes/${note.id}/edit`}>
                <Button variant="ghost" size="sm" title="Edit">
                    <Pencil class="h-4 w-4" />
                </Button>
            </a>
            <Button
                variant="ghost"
                size="sm"
                onclick={handleDelete}
                title="Hapus"
                class="text-destructive hover:text-destructive"
            >
                <Trash2 class="h-4 w-4" />
            </Button>
        </div>
    </div>

    <!-- Category & Labels -->
    {#if note.category || note.labels.length > 0}
        <div class="flex flex-wrap items-center gap-2">
            {#if note.category}
                <Badge
                    variant="secondary"
                    style="background-color: {note.category
                        .color}20; color: {note.category
                        .color}; border-color: {note.category.color}40"
                >
                    {note.category.name}
                </Badge>
            {/if}
            {#each note.labels as label}
                <Badge
                    variant="outline"
                    style="border-color: {label.color}60; color: {label.color}"
                >
                    {label.name}
                </Badge>
            {/each}
        </div>
    {/if}

    <Separator />

    <!-- Content -->
    <div class="prose prose-neutral dark:prose-invert max-w-none">
        {#if note.type === 'text'}
            {@html note.content?.html ||
                '<p class="text-muted-foreground">Tidak ada konten</p>'}
        {:else if note.type === 'checklist'}
            <div class="space-y-2">
                {#each note.content?.items || [] as item, idx (idx)}
                    {#if typeof item === 'object'}
                        <div class="flex items-center gap-2">
                            <Checkbox checked={item.checked} disabled />
                            <span
                                class={item.checked
                                    ? 'line-through text-muted-foreground'
                                    : ''}>{item.text}</span
                            >
                        </div>
                    {/if}
                {/each}
            </div>
        {:else if note.type === 'list'}
            <ol class="list-decimal pl-5 space-y-1">
                {#each note.content?.items || [] as item, idx (idx)}
                    {#if typeof item === 'string'}
                        <li>{item}</li>
                    {/if}
                {/each}
            </ol>
        {:else if note.type === 'code'}
            <div class="rounded-lg bg-muted p-4">
                <div class="mb-2 text-xs text-muted-foreground">
                    {note.content?.language || 'plaintext'}
                </div>
                <pre class="overflow-x-auto font-mono text-sm"><code
                        >{note.content?.code || ''}</code
                    ></pre>
            </div>
        {/if}
    </div>

    <!-- Footer -->
    <Separator />
    <div
        class="flex items-center justify-between text-xs text-muted-foreground"
    >
        <span
            >Dibuat: {new Date(note.created_at).toLocaleDateString(
                'id-ID',
            )}</span
        >
        <span
            >Diperbarui: {new Date(note.updated_at).toLocaleDateString(
                'id-ID',
            )}</span
        >
    </div>
</div>
