<script module lang="ts">
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Catatan',
                href: props.currentTeam ? `/${props.currentTeam.slug}/notes` : '/',
            },
        ],
    });
</script>

<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import Plus from 'lucide-svelte/icons/plus';
    import LayoutGrid from 'lucide-svelte/icons/layout-grid';
    import List from 'lucide-svelte/icons/list';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import NoteCard from './components/NoteCard.svelte';
    import FilterSidebar from './components/FilterSidebar.svelte';
    import type { Category, Label, NotesFilters, Note, PaginatedNotes } from '@/types/notes';

    let {
        notes,
        categories = [],
        labels = [],
        filters = {},
        team,
    }: {
        notes: PaginatedNotes;
        categories?: Category[];
        labels?: Label[];
        filters?: NotesFilters;
        team: { id: number; name: string; slug: string };
    } = $props();

    let viewMode = $state<'grid' | 'list'>('grid');
    let showFilters = $state(true);
</script>

<AppHead title="Catatan" />

<div class="flex h-full">
    <!-- Filter Sidebar -->
    {#if showFilters}
        <aside class="hidden w-64 shrink-0 border-r border-border p-4 lg:block">
            <FilterSidebar {categories} {labels} {filters} teamSlug={team.slug} />
        </aside>
    {/if}

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto p-4">
        <!-- Header -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Catatan</h1>
                <p class="text-sm text-muted-foreground">
                    {notes.total} catatan
                    {#if filters.q} — hasil pencarian "{filters.q}"{/if}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <Button
                    variant="ghost"
                    size="sm"
                    onclick={() => (showFilters = !showFilters)}
                    class="hidden lg:inline-flex"
                >
                    {showFilters ? 'Sembunyikan Filter' : 'Tampilkan Filter'}
                </Button>
                <div class="flex rounded-md border border-border">
                    <button
                        type="button"
                        onclick={() => (viewMode = 'grid')}
                        class="inline-flex h-9 items-center justify-center px-3 transition-colors {viewMode === 'grid' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:text-foreground'}"
                    >
                        <LayoutGrid class="h-4 w-4" />
                    </button>
                    <button
                        type="button"
                        onclick={() => (viewMode = 'list')}
                        class="inline-flex h-9 items-center justify-center px-3 transition-colors {viewMode === 'list' ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:text-foreground'}"
                    >
                        <List class="h-4 w-4" />
                    </button>
                </div>
                <Link href={`/${team.slug}/notes/create`}>
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Buat Catatan
                    </Button>
                </Link>
            </div>
        </div>

        <!-- Notes Grid/List -->
        {#if notes.data.length > 0}
            <div class={viewMode === 'grid' ? 'grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4' : 'space-y-3'}>
                {#each notes.data as note (note.id)}
                    <NoteCard {note} teamSlug={team.slug} />
                {/each}
            </div>

            <!-- Pagination -->
            {#if notes.last_page > 1}
                <div class="mt-6 flex items-center justify-center gap-2">
                    {#each Array.from({ length: notes.last_page }, (_, i) => i + 1) as page}
                        <Link
                            href={`/${team.slug}/notes?page=${page}`}
                            class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-border text-sm transition-colors hover:bg-accent {page === notes.current_page ? 'bg-primary text-primary-foreground border-primary' : ''}"
                        >
                            {page}
                        </Link>
                    {/each}
                </div>
            {/if}
        {:else}
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="mb-4 rounded-full bg-muted p-4">
                    <Plus class="h-8 w-8 text-muted-foreground" />
                </div>
                <h3 class="mb-1 text-lg font-semibold">Belum ada catatan</h3>
                <p class="mb-4 text-sm text-muted-foreground">
                    {#if filters.q}
                        Tidak ditemukan catatan yang cocok dengan pencarian Anda.
                    {:else}
                        Mulai buat catatan pertama Anda!
                    {/if}
                </p>
                <Link href={`/${team.slug}/notes/create`}>
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Buat Catatan
                    </Button>
                </Link>
            </div>
        {/if}
    </div>
</div>
