<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import Plus from 'lucide-svelte/icons/plus';
    import Pencil from 'lucide-svelte/icons/pencil';
    import Trash2 from 'lucide-svelte/icons/trash-2';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Badge } from '@/components/ui/badge';
    import {
        Dialog,
        DialogContent,
        DialogDescription,
        DialogFooter,
        DialogHeader,
        DialogTitle,
        DialogTrigger,
    } from '@/components/ui/dialog';
    import type { Label } from '@/types/notes';

    let {
        labels = [],
        teamSlug,
    }: {
        labels?: Label[];
        teamSlug: string;
    } = $props();

    let dialogOpen = $state(false);
    let editingLabel = $state<Label | null>(null);
    let formName = $state('');
    let formColor = $state('#8B5CF6');

    const presetColors = ['#8B5CF6', '#6366F1', '#EC4899', '#EF4444', '#F97316', '#EAB308', '#22C55E', '#14B8A6', '#06B6D4', '#3B82F6'];

    function openCreate() {
        editingLabel = null;
        formName = '';
        formColor = '#8B5CF6';
        dialogOpen = true;
    }

    function openEdit(label: Label) {
        editingLabel = label;
        formName = label.name;
        formColor = label.color;
        dialogOpen = true;
    }

    function submit() {
        const data = { name: formName, color: formColor };

        if (editingLabel) {
            router.put(`/${teamSlug}/labels/${editingLabel.id}`, data, {
                onSuccess: () => { dialogOpen = false; },
            });
        } else {
            router.post(`/${teamSlug}/labels`, data, {
                onSuccess: () => { dialogOpen = false; },
            });
        }
    }

    function destroy(label: Label) {
        if (confirm(`Hapus label "${label.name}"?`)) {
            router.delete(`/${teamSlug}/labels/${label.id}`);
        }
    }
</script>

<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Label</h2>
        <Dialog bind:open={dialogOpen}>
            <DialogTrigger>
                {#snippet children(props)}
                    <Button {...props} onclick={openCreate}>
                        <Plus class="mr-2 h-4 w-4" />
                        Tambah Label
                    </Button>
                {/snippet}
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{editingLabel ? 'Edit Label' : 'Tambah Label'}</DialogTitle>
                    <DialogDescription>Buat label untuk menandai catatan Anda.</DialogDescription>
                </DialogHeader>
                <div class="space-y-4 py-4">
                    <div>
                        <label class="text-sm font-medium" for="label-name">Nama</label>
                        <Input id="label-name" bind:value={formName} placeholder="Nama label..." class="mt-1" />
                    </div>
                    <div>
                        <label class="text-sm font-medium">Warna</label>
                        <div class="mt-2 flex flex-wrap gap-2">
                            {#each presetColors as color (color)}
                                <button
                                    type="button"
                                    onclick={() => (formColor = color)}
                                    class="h-8 w-8 rounded-full border-2 transition-transform hover:scale-110 {formColor === color ? 'border-foreground scale-110' : 'border-transparent'}"
                                    style="background-color: {color}"
                                ></button>
                            {/each}
                        </div>
                        <Input type="color" bind:value={formColor} class="mt-2 h-10 w-20" />
                    </div>
                </div>
                <DialogFooter>
                    <Button variant="outline" onclick={() => (dialogOpen = false)}>Batal</Button>
                    <Button onclick={submit} disabled={!formName.trim()}>
                        {editingLabel ? 'Simpan' : 'Buat'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>

    <div class="space-y-2">
        {#each labels as label (label.id)}
            <div class="flex items-center gap-3 rounded-lg border border-border p-3">
                <div class="h-4 w-4 rounded-full" style="background-color: {label.color}"></div>
                <span class="flex-1 font-medium">{label.name}</span>
                <Badge variant="secondary">{label.notes_count} catatan</Badge>
                <button type="button" onclick={() => openEdit(label)} class="text-muted-foreground hover:text-foreground">
                    <Pencil class="h-4 w-4" />
                </button>
                <button type="button" onclick={() => destroy(label)} class="text-muted-foreground hover:text-destructive">
                    <Trash2 class="h-4 w-4" />
                </button>
            </div>
        {/each}

        {#if labels.length === 0}
            <p class="py-8 text-center text-muted-foreground">Belum ada label.</p>
        {/if}
    </div>
</div>
