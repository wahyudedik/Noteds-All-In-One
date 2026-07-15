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
    import type { Category } from '@/types/notes';

    let {
        categories = [],
        teamSlug,
    }: {
        categories?: Category[];
        teamSlug: string;
    } = $props();

    let dialogOpen = $state(false);
    let editingCategory = $state<Category | null>(null);
    let formName = $state('');
    let formColor = $state('#6366F1');

    const presetColors = ['#6366F1', '#8B5CF6', '#EC4899', '#EF4444', '#F97316', '#EAB308', '#22C55E', '#14B8A6', '#06B6D4', '#3B82F6'];

    function openCreate() {
        editingCategory = null;
        formName = '';
        formColor = '#6366F1';
        dialogOpen = true;
    }

    function openEdit(category: Category) {
        editingCategory = category;
        formName = category.name;
        formColor = category.color;
        dialogOpen = true;
    }

    function submit() {
        const data = { name: formName, color: formColor };

        if (editingCategory) {
            router.put(`/${teamSlug}/categories/${editingCategory.id}`, data, {
                onSuccess: () => { dialogOpen = false; },
            });
        } else {
            router.post(`/${teamSlug}/categories`, data, {
                onSuccess: () => { dialogOpen = false; },
            });
        }
    }

    function destroy(category: Category) {
        if (confirm(`Hapus kategori "${category.name}"?`)) {
            router.delete(`/${teamSlug}/categories/${category.id}`);
        }
    }
</script>

<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Kategori</h2>
        <Dialog bind:open={dialogOpen}>
            <DialogTrigger>
                {#snippet children(props)}
                    <Button {...props} onclick={openCreate}>
                        <Plus class="mr-2 h-4 w-4" />
                        Tambah Kategori
                    </Button>
                {/snippet}
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{editingCategory ? 'Edit Kategori' : 'Tambah Kategori'}</DialogTitle>
                    <DialogDescription>Buat kategori untuk mengorganisir catatan Anda.</DialogDescription>
                </DialogHeader>
                <div class="space-y-4 py-4">
                    <div>
                        <label class="text-sm font-medium" for="cat-name">Nama</label>
                        <Input id="cat-name" bind:value={formName} placeholder="Nama kategori..." class="mt-1" />
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
                        {editingCategory ? 'Simpan' : 'Buat'}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>

    <div class="space-y-2">
        {#each categories as category (category.id)}
            <div class="flex items-center gap-3 rounded-lg border border-border p-3">
                <div class="h-4 w-4 rounded-full" style="background-color: {category.color}"></div>
                <span class="flex-1 font-medium">{category.name}</span>
                <Badge variant="secondary">{category.notes_count} catatan</Badge>
                <button type="button" onclick={() => openEdit(category)} class="text-muted-foreground hover:text-foreground">
                    <Pencil class="h-4 w-4" />
                </button>
                <button type="button" onclick={() => destroy(category)} class="text-muted-foreground hover:text-destructive">
                    <Trash2 class="h-4 w-4" />
                </button>
            </div>
        {/each}

        {#if categories.length === 0}
            <p class="py-8 text-center text-muted-foreground">Belum ada kategori.</p>
        {/if}
    </div>
</div>
