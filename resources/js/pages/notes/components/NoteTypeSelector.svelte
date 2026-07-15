<script lang="ts">
    import FileText from 'lucide-svelte/icons/file-text';
    import ListChecks from 'lucide-svelte/icons/list-checks';
    import List from 'lucide-svelte/icons/list';
    import Code from 'lucide-svelte/icons/code';
    import { cn } from '@/lib/utils';

    let {
        selected = 'text',
        onSelect,
    }: {
        selected?: string;
        onSelect?: (type: string) => void;
    } = $props();

    const types = [
        { value: 'text', label: 'Text', icon: FileText, description: 'Catatan teks bebas dengan formatting kaya' },
        { value: 'checklist', label: 'Checklist', icon: ListChecks, description: 'Daftar tugas yang bisa dicentang' },
        { value: 'list', label: 'List', icon: List, description: 'Daftar bernomor atau poin-poin' },
        { value: 'code', label: 'Code', icon: Code, description: 'Kode program dengan syntax highlighting' },
    ];
</script>

<div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
    {#each types as type (type.value)}
        <button
            type="button"
            onclick={() => onSelect?.(type.value)}
            class={cn(
                'flex flex-col items-center gap-2 rounded-xl border-2 p-4 transition-all hover:border-primary/50',
                selected === type.value
                    ? 'border-primary bg-primary/5 text-primary'
                    : 'border-border text-muted-foreground hover:text-foreground',
            )}
        >
            <type.icon class="h-6 w-6" />
            <span class="text-sm font-medium">{type.label}</span>
            <span class="text-center text-xs text-muted-foreground">{type.description}</span>
        </button>
    {/each}
</div>
