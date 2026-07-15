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
                title: 'Buat Baru',
                href: '#',
            },
        ],
    });
</script>

<script lang="ts">
    import { router, usePage } from '@inertiajs/svelte';
    import ArrowLeft from 'lucide-svelte/icons/arrow-left';
    import AppHead from '@/components/AppHead.svelte';
    import ChecklistEditor from '@/components/editor/ChecklistEditor.svelte';
    import CodeEditor from '@/components/editor/CodeEditor.svelte';
    import ListEditor from '@/components/editor/ListEditor.svelte';
    import RichTextEditor from '@/components/editor/RichTextEditor.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import {
        Select,
        SelectContent,
        SelectItem,
        SelectTrigger,
        SelectValue,
    } from '@/components/ui/select';
    import type { Category, Label } from '@/types/notes';
    import NoteTypeSelector from './components/NoteTypeSelector.svelte';

    const page = usePage<{
        errors: Record<string, string[]>;
    }>();

    let {
        categories = [],
        labels = [],
        types: _types,
        team,
    }: {
        categories?: Category[];
        labels?: Label[];
        types: {
            value: string;
            label: string;
            icon: string;
            defaultContent: Record<string, unknown>;
        }[];
        team: { id: number; name: string; slug: string };
    } = $props();

    let title = $state('');
    let type = $state('text');
    let categoryId = $state<string>('');
    let selectedLabelIds = $state<number[]>([]);

    // Content states
    let textContent = $state({ html: '', text: '' });
    let checklistContent = $state<{
        items: { text: string; checked: boolean }[];
    }>({ items: [] });
    let listContent = $state<{ items: string[] }>({ items: [] });
    let codeContent = $state({ code: '', language: 'javascript' });

    // Helper to get error message for a field
    function fieldError(field: string): string | undefined {
        const errors = page.props.errors;

        return errors?.[field]?.[0];
    }

    function getContent() {
        switch (type) {
            case 'text':
                return textContent;
            case 'checklist':
                return checklistContent;
            case 'list':
                return listContent;
            case 'code':
                return codeContent;
            default:
                return {};
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
        router.post(`/${team.slug}/notes`, {
            title,
            type,
            content: getContent(),
            category_id: categoryId || null,
            label_ids: selectedLabelIds,
        });
    }
</script>

<AppHead title="Buat Catatan" />

<div class="mx-auto max-w-3xl space-y-6 p-4">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a
            href={`/${team.slug}/notes`}
            class="inline-flex h-9 w-9 items-center justify-center rounded-md text-muted-foreground hover:bg-accent"
        >
            <ArrowLeft class="h-5 w-5" />
        </a>
        <div>
            <h1 class="text-2xl font-bold text-foreground">
                Buat Catatan Baru
            </h1>
            <p class="text-sm text-muted-foreground">
                Pilih tipe dan mulai menulis
            </p>
        </div>
    </div>

    <!-- Type Selector -->
    <div>
        <label class="mb-2 block text-sm font-medium text-foreground">
            Tipe Catatan
        </label>
        <NoteTypeSelector selected={type} onSelect={(t) => (type = t)} />
        <InputError message={fieldError('type')} class="mt-1" />
    </div>

    <!-- Title -->
    <div>
        <label
            class="mb-2 block text-sm font-medium text-foreground"
            for="note-title"
        >
            Judul
        </label>
        <Input
            id="note-title"
            bind:value={title}
            placeholder="Judul catatan..."
            class="text-lg"
        />
        <InputError message={fieldError('title')} class="mt-1" />
    </div>

    <!-- Content Editor -->
    <div>
        <label class="mb-2 block text-sm font-medium text-foreground">
            Konten
        </label>
        {#if type === 'text'}
            <RichTextEditor
                content={textContent}
                onUpdate={(data) => (textContent = data)}
                placeholder="Mulai menulis catatan..."
            />
        {:else if type === 'checklist'}
            <ChecklistEditor bind:items={checklistContent.items} />
        {:else if type === 'list'}
            <ListEditor bind:items={listContent.items} />
        {:else if type === 'code'}
            <CodeEditor
                bind:code={codeContent.code}
                bind:language={codeContent.language}
            />
        {/if}
        <InputError message={fieldError('content')} class="mt-1" />
    </div>

    <!-- Category -->
    {#if categories.length > 0}
        <div>
            <label class="mb-2 block text-sm font-medium text-foreground">
                Kategori
            </label>
            <Select bind:value={categoryId}>
                <SelectTrigger>
                    <SelectValue placeholder="Pilih kategori..." />
                </SelectTrigger>
                <SelectContent>
                    {#each categories as cat (cat.id)}
                        <SelectItem value={String(cat.id)}>
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-3 w-3 rounded-full"
                                    style="background-color: {cat.color}"
                                ></div>
                                {cat.name}
                            </div>
                        </SelectItem>
                    {/each}
                </SelectContent>
            </Select>
            <InputError message={fieldError('category_id')} class="mt-1" />
        </div>
    {/if}

    <!-- Labels -->
    {#if labels.length > 0}
        <div>
            <label class="mb-2 block text-sm font-medium text-foreground">
                Label
            </label>
            <div class="flex flex-wrap gap-2">
                {#each labels as label (label.id)}
                    <button
                        type="button"
                        onclick={() => toggleLabel(label.id)}
                        class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-sm transition-colors {selectedLabelIds.includes(
                            label.id,
                        )
                            ? 'border-transparent text-white'
                            : 'border-border hover:border-primary/50'}"
                        style={selectedLabelIds.includes(label.id)
                            ? `background-color: ${label.color}`
                            : ''}
                    >
                        <div
                            class="h-2 w-2 rounded-full"
                            style="background-color: {label.color}"
                        ></div>
                        {label.name}
                    </button>
                {/each}
            </div>
            <InputError message={fieldError('label_ids')} class="mt-1" />
        </div>
    {/if}

    <!-- Actions -->
    <div class="flex items-center justify-end gap-2 pt-4">
        <a href={`/${team.slug}/notes`}>
            <Button variant="outline">Batal</Button>
        </a>
        <Button onclick={submit} disabled={!title.trim()}>Simpan Catatan</Button
        >
    </div>
</div>
