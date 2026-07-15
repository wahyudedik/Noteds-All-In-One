<script lang="ts">
    let {
        class: className = '',
        ...rest
    }: {
        class?: string;
        [key: string]: unknown;
    } = $props();
</script>

<img
    src="/logo.jpeg"
    alt="Noteds Logo"
    class={className}
    {...rest}
/>
