export type NoteType = 'text' | 'checklist' | 'list' | 'code';

export type NoteContent = {
    html?: string;
    text?: string;
    items?: { text: string; checked: boolean }[] | string[];
    code?: string;
    language?: string;
};

export type Note = {
    id: number;
    title: string;
    content: NoteContent;
    excerpt: string;
    type: NoteType;
    is_pinned: boolean;
    is_archived: boolean;
    category: Category | null;
    category_id: number | null;
    labels: Label[];
    author: { id: number; name: string; email: string };
    created_at: string;
    updated_at: string;
};

export type Category = {
    id: number;
    name: string;
    color: string;
    icon: string | null;
    notes_count: number;
};

export type Label = {
    id: number;
    name: string;
    color: string;
    notes_count: number;
};

export type NotesFilters = {
    type?: NoteType;
    category_id?: number;
    label_id?: number;
    q?: string;
    archived?: boolean;
};

export type PaginatedNotes = {
    data: Note[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
};
