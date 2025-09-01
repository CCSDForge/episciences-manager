<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

class UploadDirectoryService
{
    private string $projectDir;
    private Filesystem $filesystem;

    public function __construct(
        #[Autowire(param: 'kernel.project_dir')] string $projectDir,
        Filesystem $filesystem
    ) {
        $this->projectDir = $projectDir;
        $this->filesystem = $filesystem;
    }

    public function getUploadDirectory(string $journalCode): string
    {
        // Structure Zend: data/{journal_code}/public/
        $directory = $this->projectDir . '/data/' . $journalCode . '/public';

        if (!$this->filesystem->exists($directory)) {
            $this->filesystem->mkdir($directory, 0755);
        }

        return $directory;
    }

    public function getJournalDataPath(string $journalCode): string
    {
        return $this->projectDir . '/data/' . $journalCode;
    }

    public function getJournalTmpPath(string $journalCode): string
    {
        $directory = $this->projectDir . '/data/' . $journalCode . '/tmp';
        
        if (!$this->filesystem->exists($directory)) {
            $this->filesystem->mkdir($directory, 0755);
        }
        
        return $directory;
    }

    public function getJournalFilesPath(string $journalCode): string
    {
        $directory = $this->projectDir . '/data/' . $journalCode . '/files';
        
        if (!$this->filesystem->exists($directory)) {
            $this->filesystem->mkdir($directory, 0755);
        }
        
        return $directory;
    }
}