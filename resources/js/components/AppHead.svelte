<script lang="ts">
    let { title }: { title?: string } = $props();
</script>

<svelte:head>
    <title>{title ? `${title} — Noteds` : 'Noteds'}</title>
</svelte:head>
