<script module lang="ts">
    import type { Team } from '@/types';

    export const layout = (props: { currentTeam?: Team | null }) => ({
        breadcrumbs: [
            {
                title: 'Label',
                href: props.currentTeam ? `/${props.currentTeam.slug}/labels` : '/',
            },
        ],
    });
</script>

<script lang="ts">
    import AppHead from '@/components/AppHead.svelte';
    import LabelManager from '@/pages/notes/components/LabelManager.svelte';
    import type { Label } from '@/types/notes';

    let {
        labels = [],
        team,
    }: {
        labels?: Label[];
        team: { id: number; name: string; slug: string };
    } = $props();
</script>

<AppHead title="Label" />

<div class="mx-auto max-w-2xl p-4">
    <LabelManager {labels} teamSlug={team.slug} />
</div>
