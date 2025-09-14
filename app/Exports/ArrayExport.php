<?php

namespace App\Exports;

// This class is only used when maatwebsite/excel is installed
if (interface_exists('Maatwebsite\\Excel\\Concerns\\FromArray')) {
    use Maatwebsite\Excel\Concerns\FromArray;
    use Maatwebsite\Excel\Concerns\WithTitle;

    class ArrayExport implements FromArray, WithTitle
    {
        public function __construct(private array $rows, private string $title = 'Sheet1') {}

        public function array(): array
        {
            return $this->rows;
        }

        public function title(): string
        {
            return $this->title;
        }
    }
} else {
    // Fallback dummy to avoid autoload errors when the package is not installed
    class ArrayExport {}
}

