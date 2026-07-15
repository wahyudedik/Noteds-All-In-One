<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import Search from 'lucide-svelte/icons/search';
    import X from 'lucide-svelte/icons/x';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import { Checkbox } from '@/components/ui/checkbox';
    import { Input } from '@/components/ui/input';
    import { Separator } from '@/components/ui/separator';
    import type { Category, Label, NotesFilters } from '@/types/notes';

    let {
        categories = [],
        labels = [],
        filters = {},
        teamSlug,
    }: {
        categories?: Category[];
        labels?: Label[];
        filters?: NotesFilters;
        teamSlug: string;
    } = $props();

    let searchQuery = $state(filters.q || '');

    function applyFilters(newFilters: NotesFilters) {
        const params: Record<string, string> = {};

        if (newFilters.q) {
            params.q = newFilters.q;
        }

        if (newFilters.type) {
            params.type = newFilters.type;
        }

        if (newFilters.category_id) {
            params.category_id = String(newFilters.category_id);
        }

        if (newFilters.label_id) {
            params.label_id = String(newFilters.label_id);
        }

        if (newFilters.archived) {
            params.archived = '1';
        }

        router.get(`/${teamSlug}/notes`, params, {
            preserveState: true,
            replace: true,
        });
    }

    function handleSearch() {
        applyFilters({ ...filters, q: searchQuery || undefined });
    }

    function handleSearchKeydown(event: KeyboardEvent) {
        if (event.key === 'Enter') {
            handleSearch();
        }
    }

    function clearFilters() {
        searchQuery = '';
        router.get(
            `/${teamSlug}/notes`,
            {},
            { preserveState: true, replace: true },
        );
    }

    function toggleType(type: string) {
        const newType = filters.type === type ? undefined : type;
        applyFilters({ ...filters, type: newType as any });
    }

    function toggleCategory(categoryId: number) {
        const newCategory =
            filters.category_id === categoryId ? undefined : categoryId;
        applyFilters({ ...filters, category_id: newCategory });
    }

    function toggleLabel(labelId: number) {
        const newLabel = filters.label_id === labelId ? undefined : labelId;
        applyFilters({ ...filters, label_id: newLabel });
    }

    function toggleArchived() {
        applyFilters({ ...filters, archived: !filters.archived });
    }

    const hasActiveFilters = $derived(
        filters.q ||
            filters.type ||
            filters.category_id ||
            filters.label_id ||
            filters.archived,
    );
</script>

<div class="space-y-6">
    <!-- Search -->
    <div>
        <h3 class="mb-2 text-sm font-semibold text-foreground">Cari</h3>
        <div class="relative">
            <Search
                class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
            />
            <Input
                type="text"
                placeholder="Cari catatan..."
                bind:value={searchQuery}
                onkeydown={handleSearchKeydown}
                class="pl-9"
            />
        </div>
    </div>

    <Separator />

    <!-- Type Filter -->
    <div>
        <h3 class="mb-2 text-sm font-semibold text-foreground">Tipe</h3>
        <div class="flex flex-wrap gap-2">
            {#each ['text', 'checklist', 'list', 'code'] as type (type)}
                <Badge
                    variant={filters.type === type ? 'default' : 'outline'}
                    class="cursor-pointer capitalize"
                    onclick={() => toggleType(type)}
                >
                    {type}
                </Badge>
            {/each}
        </div>
    </div>

    <Separator />

    <!-- Category Filter -->
    {#if categories.length > 0}
        <div>
            <h3 class="mb-2 text-sm font-semibold text-foreground">Kategori</h3>
            <div class="space-y-1">
                {#each categories as category (category.id)}
                    <button
                        type="button"
                        onclick={() => toggleCategory(category.id)}
                        class="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-sm transition-colors hover:bg-accent {filters.category_id ===
                        category.id
                            ? 'bg-accent'
                            : ''}"
                    >
                        <div
                            class="h-3 w-3 rounded-full"
                            style="background-color: {category.color}"
                        ></div>
                        <span class="flex-1 text-left">{category.name}</span>
                        <span class="text-xs text-muted-foreground"
                            >{category.notes_count}</span
                        >
                    </button>
                {/each}
            </div>
        </div>
        <Separator />
    {/if}

    <!-- Label Filter -->
    {#if labels.length > 0}
        <div>
            <h3 class="mb-2 text-sm font-semibold text-foreground">Label</h3>
            <div class="space-y-1">
                {#each labels as label (label.id)}
                    <button
                        type="button"
                        onclick={() => toggleLabel(label.id)}
                        class="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-sm transition-colors hover:bg-accent {filters.label_id ===
                        label.id
                            ? 'bg-accent'
                            : ''}"
                    >
                        <div
                            class="h-3 w-3 rounded-full"
                            style="background-color: {label.color}"
                        ></div>
                        <span class="flex-1 text-left">{label.name}</span>
                        <span class="text-xs text-muted-foreground"
                            >{label.notes_count}</span
                        >
                    </button>
                {/each}
            </div>
        </div>
        <Separator />
    {/if}

    <!-- Archived Toggle -->
    <div>
        <!-- svelte-ignore a11y_click_events_have_key_events -->
        <!-- svelte-ignore a11y_no_noninteractive_element_interactions -->
        <label
            onclick={toggleArchived}
            class="flex w-full cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 text-sm transition-colors hover:bg-accent {filters.archived
                ? 'bg-accent'
                : ''}"
        >
            <Checkbox checked={filters.archived} />
            <span>Tampilkan Arsip</span>
        </label>
    </div>

    <!-- Clear Filters -->
    {#if hasActiveFilters}
        <Button variant="ghost" size="sm" onclick={clearFilters} class="w-full">
            <X class="mr-2 h-4 w-4" />
            Hapus Filter
        </Button>
    {/if}
</div>
