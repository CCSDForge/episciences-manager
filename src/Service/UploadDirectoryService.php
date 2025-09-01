<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;

class UploadDirectoryService
{
    private string $projectDir;
    private Filesystem $filesystem;
    private JournalContextService $journalContext;

    public function __construct(
        #[Autowire(param: 'kernel.project_dir')] string $projectDir,
        Filesystem $filesystem,
        JournalContextService $journalContext
    ) {
        $this->projectDir = $projectDir;
        $this->filesystem = $filesystem;
        $this->journalContext = $journalContext;
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

    // Méthodes automatiques utilisant le contexte journal
    public function getCurrentJournalUploadDirectory(): string
    {
        $journalCode = $this->journalContext->getCurrentJournalCode();
        return $this->getUploadDirectory($journalCode);
    }

    public function getCurrentJournalDataPath(): string
    {
        $journalCode = $this->journalContext->getCurrentJournalCode();
        return $this->getJournalDataPath($journalCode);
    }

    public function getCurrentJournalTmpPath(): string
    {
        $journalCode = $this->journalContext->getCurrentJournalCode();
        return $this->getJournalTmpPath($journalCode);
    }

    public function getCurrentJournalFilesPath(): string
    {
        $journalCode = $this->journalContext->getCurrentJournalCode();
        return $this->getJournalFilesPath($journalCode);
    }
}