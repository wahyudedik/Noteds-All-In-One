<script lang="ts">
    import GripVertical from 'lucide-svelte/icons/grip-vertical';
    import Plus from 'lucide-svelte/icons/plus';
    import Trash2 from 'lucide-svelte/icons/trash-2';
    import { Button } from '@/components/ui/button';
    import { Checkbox } from '@/components/ui/checkbox';
    import { Input } from '@/components/ui/input';

    let {
        items = $bindable([]),
        onUpdate,
    }: {
        items?: { text: string; checked: boolean }[];
        onUpdate?: (data: {
            items: { text: string; checked: boolean }[];
        }) => void;
    } = $props();

    function addItem() {
        items = [...items, { text: '', checked: false }];
        onUpdate?.({ items });
    }

    function removeItem(index: number) {
        items = items.filter((_, i) => i !== index);
        onUpdate?.({ items });
    }

    function updateItemText(index: number, text: string) {
        items = items.map((item, i) =>
            i === index ? { ...item, text } : item,
        );
        onUpdate?.({ items });
    }

    function toggleItemChecked(index: number) {
        items = items.map((item, i) =>
            i === index ? { ...item, checked: !item.checked } : item,
        );
        onUpdate?.({ items });
    }

    function handleKeydown(event: KeyboardEvent, index: number) {
        if (event.key === 'Enter') {
            event.preventDefault();
            addItem();
        } else if (
            event.key === 'Backspace' &&
            items[index].text === '' &&
            items.length > 1
        ) {
            event.preventDefault();
            removeItem(index);
        }
    }
</script>

<div class="space-y-2">
    {#each items as item, index (index)}
        <div class="flex items-center gap-2 group">
            <GripVertical
                class="h-4 w-4 text-muted-foreground opacity-0 group-hover:opacity-100 transition-opacity cursor-grab"
            />
            <Checkbox
                checked={item.checked}
                onCheckedChange={() => toggleItemChecked(index)}
            />
            <Input
                type="text"
                value={item.text}
                oninput={(e) =>
                    updateItemText(index, (e.target as HTMLInputElement).value)}
                onkeydown={(e) => handleKeydown(e, index)}
                placeholder="Item..."
                class="flex-1 {item.checked
                    ? 'line-through text-muted-foreground'
                    : ''}"
            />
            <button
                type="button"
                onclick={() => removeItem(index)}
                class="inline-flex h-8 w-8 items-center justify-center rounded-md text-muted-foreground opacity-0 group-hover:opacity-100 hover:bg-destructive/10 hover:text-destructive transition-all"
            >
                <Trash2 class="h-4 w-4" />
            </button>
        </div>
    {/each}

    <Button
        type="button"
        variant="ghost"
        size="sm"
        onclick={addItem}
        class="w-full text-muted-foreground"
    >
        <Plus class="mr-2 h-4 w-4" />
        Tambah item
    </Button>
</div>
