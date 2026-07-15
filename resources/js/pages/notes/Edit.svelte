<script module lang="ts">
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Catatan',
                href: props.currentTeam ? `/${props.currentTeam.slug}/notes` : '/',
            },
            {
                title: 'Edit',
                href: '#',
            },
        ],
    });
</script>

<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import ArrowLeft from 'lucide-svelte/icons/arrow-left';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
    import RichTextEditor from '@/components/editor/RichTextEditor.svelte';
    import ChecklistEditor from '@/components/editor/ChecklistEditor.svelte';
    import ListEditor from '@/components/editor/ListEditor.svelte';
    import CodeEditor from '@/components/editor/CodeEditor.svelte';
    import type { Category, Label, Note, NoteType } from '@/types/notes';

    let {
        note,
        categories = [],
        labels = [],
        types,
        team,
    }: {
        note: Note;
        categories?: Category[];
        labels?: Label[];
        types: { value: string; label: string; icon: string }[];
        team: { id: number; name: string; slug: string };
    } = $props();

    let title = $state(note.title);
    let type = $state<NoteType>(note.type);
    let categoryId = $state<string>(note.category_id ? String(note.category_id) : '');
    let selectedLabelIds = $state<number[]>(note.labels.map((l) => l.id));

    // Content states based on existing note
    let textContent = $state({
        html: note.type === 'text' ? (note.content?.html || '') : '',
        text: note.type === 'text' ? (note.content?.text || '') : '',
    });
    let checklistContent = $state({
        items: note.type === 'checklist' ? (note.content?.items as { text: string; checked: boolean }[] || []) : [],
    });
    let listContent = $state({
        items: note.type === 'list' ? (note.content?.items as string[] || []) : [],
    });
    let codeContent = $state({
        code: note.type === 'code' ? (note.content?.code || '') : '',
        language: note.type === 'code' ? (note.content?.language || 'javascript') : 'javascript',
    });

    function getContent() {
        switch (type) {
            case 'text': return textContent;
            case 'checklist': return checklistContent;
            case 'list': return listContent;
            case 'code': return codeContent;
            default: return {};
        }
    }

    function toggleLabel(labelId: number) {
        if (selectedLabelIds.includes(labelId)) {
            selectedLabelIds = selectedLabelIds.filter((id) => id !== labelId);
        } else {
            selectedLabelIds = [...selectedLabelIds, labelId];
        }
    }

    function submit() {
        router.put(`/${team.slug}/notes/${note.id}`, {
            title,
            type,
            content: getContent(),
            category_id: categoryId || null,
            label_ids: selectedLabelIds,
        });
    }
</script>

<AppHead title={`Edit: ${note.title}`} />

<div class="mx-auto max-w-3xl space-y-6 p-4">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href={`/${team.slug}/notes/${note.id}`} class="inline-flex h-9 w-9 items-center justify-center rounded-md text-muted-foreground hover:bg-accent">
            <ArrowLeft class="h-5 w-5" />
        </a>
        <div>
            <h1 class="text-2xl font-bold text-foreground">Edit Catatan</h1>
            <p class="text-sm text-muted-foreground">Edit "{note.title}"</p>
        </div>
    </div>

    <!-- Title -->
    <div>
        <label class="mb-2 block text-sm font-medium text-foreground" for="note-title">Judul</label>
        <Input
            id="note-title"
            bind:value={title}
            placeholder="Judul catatan..."
            class="text-lg"
        />
    </div>

    <!-- Content Editor -->
    <div>
        <label class="mb-2 block text-sm font-medium text-foreground">Konten</label>
        {#if type === 'text'}
            <RichTextEditor content={textContent} onUpdate={(data) => (textContent = data)} placeholder="Mulai menulis catatan..." />
        {:else if type === 'checklist'}
            <ChecklistEditor bind:items={checklistContent.items} />
        {:else if type === 'list'}
            <ListEditor bind:items={listContent.items} />
        {:else if type === 'code'}
            <CodeEditor bind:code={codeContent.code} bind:language={codeContent.language} />
        {/if}
    </div>

    <!-- Category -->
    {#if categories.length > 0}
        <div>
            <label class="mb-2 block text-sm font-medium text-foreground">Kategori</label>
            <Select bind:value={categoryId}>
                <SelectTrigger>
                    <SelectValue placeholder="Pilih kategori..." />
                </SelectTrigger>
                <SelectContent>
                    {#each categories as cat (cat.id)}
                        <SelectItem value={String(cat.id)}>
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded-full" style="background-color: {cat.color}"></div>
                                {cat.name}
                            </div>
                        </SelectItem>
                    {/each}
                </SelectContent>
            </Select>
        </div>
    {/if}

    <!-- Labels -->
    {#if labels.length > 0}
        <div>
            <label class="mb-2 block text-sm font-medium text-foreground">Label</label>
            <div class="flex flex-wrap gap-2">
                {#each labels as label (label.id)}
                    <button
                        type="button"
                        onclick={() => toggleLabel(label.id)}
                        class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-sm transition-colors {selectedLabelIds.includes(label.id) ? 'border-transparent text-white' : 'border-border hover:border-primary/50'}"
                        style={selectedLabelIds.includes(label.id) ? `background-color: {label.color}` : ''}
                    >
                        <div class="h-2 w-2 rounded-full" style="background-color: {label.color}"></div>
                        {label.name}
                    </button>
                {/each}
            </div>
        </div>
    {/if}

    <!-- Actions -->
    <div class="flex items-center justify-end gap-2 pt-4">
        <a href={`/${team.slug}/notes/${note.id}`}>
            <Button variant="outline">Batal</Button>
        </a>
        <Button onclick={submit} disabled={!title.trim()}>Simpan Perubahan</Button>
    </div>
</div>
