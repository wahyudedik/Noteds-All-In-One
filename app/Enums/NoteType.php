<?php

namespace App\Enums;

enum NoteType: string
{
    case Text = 'text';
    case Checklist = 'checklist';
    case List = 'list';
    case Code = 'code';

    /**
     * Get the display label for the note type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Text => 'Text',
            self::Checklist => 'Checklist',
            self::List => 'List',
            self::Code => 'Code',
        };
    }

    /**
     * Get the lucide icon name for the note type.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Text => 'file-text',
            self::Checklist => 'list-checks',
            self::List => 'list',
            self::Code => 'code',
        };
    }

    /**
     * Get the default content structure for the note type.
     */
    public function defaultContent(): array
    {
        return match ($this) {
            self::Text => ['html' => '', 'text' => ''],
            self::Checklist => ['items' => []],
            self::List => ['items' => []],
            self::Code => ['code' => '', 'language' => 'javascript'],
        };
    }
}
