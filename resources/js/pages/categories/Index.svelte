<script module lang="ts">
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Kategori',
                href: props.currentTeam ? `/${props.currentTeam.slug}/categories` : '/',
            },
        ],
    });
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import CategoryManager from '@/pages/notes/components/CategoryManager.svelte';
    import type { Category } from '@/types/notes';

    let {
        categories = [],
        team,
    }: {
        categories?: Category[];
        team: { id: number; name: string; slug: string };
    } = $props();
</script>

<AppHead title="Kategori" />

<div class="mx-auto max-w-2xl p-4">
    <CategoryManager {categories} teamSlug={team.slug} />
</div>
