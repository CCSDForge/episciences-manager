<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FileExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('file_size', [$this, 'formatFileSize']),
            new TwigFilter('file_icon', [$this, 'getFileIcon']),
        ];
    }

    public function formatFileSize(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }

        $k = 1024;
        $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    public function getFileIcon(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $iconMap = [
            // Images
            'png' => 'fa-image',
            'jpg' => 'fa-image',
            'jpeg' => 'fa-image',
            'gif' => 'fa-image',
            'tif' => 'fa-image',
            'tiff' => 'fa-image',
            
            // Documents
            'pdf' => 'fa-file-pdf',
            'doc' => 'fa-file-word',
            'docx' => 'fa-file-word',
            'rtf' => 'fa-file-word',
            'odt' => 'fa-file-word',
            
            // Spreadsheets
            'xls' => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            'ods' => 'fa-file-excel',
            
            // Archives
            'zip' => 'fa-file-archive',
            'rar' => 'fa-file-archive',
            'gz' => 'fa-file-archive',
            '7z' => 'fa-file-archive',
            'tar' => 'fa-file-archive',
            'bz' => 'fa-file-archive',
            'bz2' => 'fa-file-archive',
            
            // Text files
            'txt' => 'fa-file-alt',
            'md' => 'fa-file-alt',
            
            // LaTeX
            'tex' => 'fa-file-code',
            'bib' => 'fa-file-code',
            'cls' => 'fa-file-code',
            'sty' => 'fa-file-code',
            'bbl' => 'fa-file-code',
            'bbx' => 'fa-file-code',
            'bst' => 'fa-file-code',
            'cbx' => 'fa-file-code',
            'def' => 'fa-file-code',
            'dbx' => 'fa-file-code',
            'dtx' => 'fa-file-code',
            'lbx' => 'fa-file-code',
        ];

        return $iconMap[$extension] ?? 'fa-file';
    }
}