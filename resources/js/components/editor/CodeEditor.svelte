<script lang="ts">
    import {
        Select,
        SelectContent,
        SelectItem,
        SelectTrigger,
        SelectValue,
    } from '@/components/ui/select';

    const languages = [
        { value: 'javascript', label: 'JavaScript' },
        { value: 'typescript', label: 'TypeScript' },
        { value: 'python', label: 'Python' },
        { value: 'php', label: 'PHP' },
        { value: 'html', label: 'HTML' },
        { value: 'css', label: 'CSS' },
        { value: 'json', label: 'JSON' },
        { value: 'sql', label: 'SQL' },
        { value: 'bash', label: 'Bash' },
        { value: 'rust', label: 'Rust' },
        { value: 'go', label: 'Go' },
        { value: 'java', label: 'Java' },
        { value: 'cpp', label: 'C++' },
        { value: 'ruby', label: 'Ruby' },
        { value: 'markdown', label: 'Markdown' },
        { value: 'yaml', label: 'YAML' },
        { value: 'plaintext', label: 'Plain Text' },
    ];

    let {
        code = $bindable(''),
        language = $bindable('javascript'),
        onUpdate,
    }: {
        code?: string;
        language?: string;
        onUpdate?: (data: { code: string; language: string }) => void;
    } = $props();

    function handleCodeInput(event: Event) {
        const target = event.target as HTMLTextAreaElement;
        code = target.value;
        onUpdate?.({ code, language });
    }

    function handleLanguageChange(value: string) {
        language = value;
        onUpdate?.({ code, language });
    }

    function handleTab(event: KeyboardEvent) {
        if (event.key === 'Tab') {
            event.preventDefault();
            const target = event.target as HTMLTextAreaElement;
            const start = target.selectionStart;
            const end = target.selectionEnd;
            code = code.substring(0, start) + '    ' + code.substring(end);
            onUpdate?.({ code, language });

            // Restore cursor position
            setTimeout(() => {
                target.selectionStart = target.selectionEnd = start + 4;
            }, 0);
        }
    }
</script>

<div class="space-y-2">
    <div class="flex items-center justify-between">
        <label class="text-sm font-medium text-muted-foreground">Bahasa</label>
        <Select value={language} onValueChange={handleLanguageChange}>
            <SelectTrigger class="w-[180px]">
                <SelectValue placeholder="Pilih bahasa" />
            </SelectTrigger>
            <SelectContent>
                {#each languages as lang (lang.value)}
                    <SelectItem value={lang.value}>{lang.label}</SelectItem>
                {/each}
            </SelectContent>
        </Select>
    </div>
    <textarea
        value={code}
        oninput={handleCodeInput}
        onkeydown={handleTab}
        placeholder="Tulis kode di sini..."
        class="w-full min-h-[250px] max-h-[500px] rounded-md border border-border bg-muted px-4 py-3 font-mono text-sm leading-relaxed focus:outline-none focus:ring-2 focus:ring-ring resize-y"
        spellcheck="false"
    ></textarea>
</div>
