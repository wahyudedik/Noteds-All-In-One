<script module lang="ts">
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Pencarian',
                href: props.currentTeam ? `/${props.currentTeam.slug}/search` : '/',
            },
        ],
    });
</script>

<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import SearchIcon from 'lucide-svelte/icons/search';
    import AppHead from '@/components/AppHead.svelte';
    import { Input } from '@/components/ui/input';
    import NoteCard from './components/NoteCard.svelte';
    import FilterSidebar from './components/FilterSidebar.svelte';
    import type { Category, Label, NotesFilters, PaginatedNotes } from '@/types/notes';

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

    let searchQuery = $state(filters.q || '');

    function handleSearch() {
        const params: Record<string, string> = {};
        if (searchQuery) params.q = searchQuery;
        if (filters.type) params.type = filters.type;
        if (filters.category_id) params.category_id = String(filters.category_id);
        if (filters.label_id) params.label_id = String(filters.label_id);

        router.get(`/${team.slug}/search`, params, { preserveState: true, replace: true });
    }
</script>

<AppHead title="Pencarian Catatan" />

<div class="flex h-full">
    <!-- Filter Sidebar -->
    <aside class="hidden w-64 shrink-0 border-r border-border p-4 lg:block">
        <FilterSidebar {categories} {labels} {filters} teamSlug={team.slug} />
    </aside>

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto p-4">
        <!-- Search Bar -->
        <div class="mb-6">
            <h1 class="mb-4 text-2xl font-bold text-foreground">Pencarian Catatan</h1>
            <div class="relative max-w-xl">
                <SearchIcon class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                <Input
                    type="text"
                    placeholder="Cari berdasarkan judul atau konten..."
                    bind:value={searchQuery}
                    onkeydown={(e) => e.key === 'Enter' && handleSearch()}
                    class="pl-10 text-lg"
                />
            </div>
        </div>

        <!-- Results -->
        {#if filters.q}
            <p class="mb-4 text-sm text-muted-foreground">
                {notes.total} hasil untuk "{filters.q}"
            </p>
        {/if}

        {#if notes.data.length > 0}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                {#each notes.data as note (note.id)}
                    <NoteCard {note} teamSlug={team.slug} />
                {/each}
            </div>

            <!-- Pagination -->
            {#if notes.last_page > 1}
                <div class="mt-6 flex items-center justify-center gap-2">
                    {#each Array.from({ length: notes.last_page }, (_, i) => i + 1) as page}
                        <button
                            type="button"
                            onclick={() => {
                                const params = new URLSearchParams();
                                if (filters.q) params.set('q', filters.q);
                                if (filters.type) params.set('type', filters.type);
                                params.set('page', String(page));
                                router.get(`/${team.slug}/search?${params.toString()}`, {}, { preserveState: true });
                            }}
                            class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-border text-sm transition-colors hover:bg-accent {page === notes.current_page ? 'bg-primary text-primary-foreground border-primary' : ''}"
                        >
                            {page}
                        </button>
                    {/each}
                </div>
            {/if}
        {:else if filters.q}
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <SearchIcon class="mb-4 h-12 w-12 text-muted-foreground/50" />
                <h3 class="mb-1 text-lg font-semibold">Tidak ada hasil</h3>
                <p class="text-sm text-muted-foreground">Coba kata kunci yang berbeda</p>
            </div>
        {/if}
    </div>
</div>
