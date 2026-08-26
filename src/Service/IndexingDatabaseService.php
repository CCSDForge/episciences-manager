<?php

namespace App\Service;

use App\Entity\IndexingDatabase;
use App\Entity\User;
use App\Enum\IndexingDatabaseStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class IndexingDatabaseService
{
    private const ALLOWED_LOGO_EXTENSIONS = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'];
    private const MAX_LOGO_SIZE = 2 * 1024 * 1024; // 2MB
    private const UPLOAD_DIR = 'data/indexing-databases';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SluggerInterface       $slugger,
        #[Autowire('%kernel.project_dir%')]
        private readonly string                 $projectDir,
    )
    {
    }

    /**
     * Create a new indexing database.
     *
     * @throws \InvalidArgumentException If the logo is invalid
     */
    public function create(
        string                 $name,
        ?string                $url,
        ?UploadedFile          $logo,
        User                   $createdBy,
        IndexingDatabaseStatus $status = IndexingDatabaseStatus::PENDING
    ): IndexingDatabase
    {
        $database = new IndexingDatabase();
        $database->setName($name);
        $database->setUrl($url);
        $database->setStatus($status);
        $database->setCreatedBy($createdBy);

        if ($logo !== null) {
            $logoPath = $this->uploadLogo($logo);
            $database->setLogo($logoPath);
        }

        $this->entityManager->persist($database);
        $this->entityManager->flush();

        return $database;
    }

    /**
     * Update an existing indexing database.
     *
     * @throws \InvalidArgumentException If the logo is invalid
     */
    public function update(
        IndexingDatabase $database,
        string           $name,
        ?string          $url,
        ?UploadedFile    $newLogo = null,
        bool             $removeLogo = false
    ): void
    {
        $database->setName($name);
        $database->setUrl($url);
        $database->setUpdatedAt(new \DateTime());

        if ($removeLogo && $database->getLogo()) {
            $this->deleteLogo($database->getLogo());
            $database->setLogo(null);
        } elseif ($newLogo !== null) {
            // Delete old logo if present
            if ($database->getLogo()) {
                $this->deleteLogo($database->getLogo());
            }
            $logoPath = $this->uploadLogo($newLogo, $database->getName());
            $database->setLogo($logoPath);
        }

        $this->entityManager->flush();
    }

    /**
     * Delete an indexing database and its logo.
     */
    public function delete(IndexingDatabase $database): void
    {
        if ($database->getLogo()) {
            $this->deleteLogo($database->getLogo());
        }

        $this->entityManager->remove($database);
        $this->entityManager->flush();
    }

    /**
     * Upload a logo and return the relative path.
     *
     * @throws \InvalidArgumentException If the file is invalid
     */
    public function uploadLogo(UploadedFile $file, ?string $name = null): string
    {
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('Logo upload failed');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_LOGO_EXTENSIONS, true)) {
            throw new \InvalidArgumentException(
                'Invalid logo format. Allowed: ' . implode(', ', self::ALLOWED_LOGO_EXTENSIONS)
            );
        }

        if ($file->getSize() > self::MAX_LOGO_SIZE) {
            throw new \InvalidArgumentException('Logo file too large. Maximum size: 2MB');
        }

        $slug = $this->slugger->slug($name ?: 'database')->lower();
        $newFilename = $slug . '-' . uniqid() . '.' . $extension;

        $uploadDir = $this->projectDir . '/' . self::UPLOAD_DIR;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file->move($uploadDir, $newFilename);

        return self::UPLOAD_DIR . '/' . $newFilename;
    }

    /**
     * Delete a logo file from the filesystem.
     */
    public function deleteLogo(string $logoPath): void
    {
        $fullPath = $this->projectDir . '/' . $logoPath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * Return allowed logo extensions.
     */
    public function getAllowedExtensions(): array
    {
        return self::ALLOWED_LOGO_EXTENSIONS;
    }

    /**
     * Return the maximum logo size in bytes.
     */
    public function getMaxLogoSize(): int
    {
        return self::MAX_LOGO_SIZE;
    }
}
