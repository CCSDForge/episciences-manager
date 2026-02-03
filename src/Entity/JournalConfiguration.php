<?php


namespace App\Entity;

use App\Repository\JournalConfigurationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JournalConfigurationRepository::class)]
#[ORM\Table(name: 'JOURNAL_CONFIGURATION')]
class JournalConfiguration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ID')]
    private ?int $id = null;

    #[ORM\Column(name: 'RVID', type: 'integer', unique: true)]
    private ?int $rvid = null;

    #[ORM\Column(name: 'CONFIGURATION', type: 'json')]
    private array $configuration = [];

    #[ORM\Column(name: 'CREATED_AT', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'UPDATED_AT', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRvid(): ?int
    {
        return $this->rvid;
    }

    public function setRvid(int $rvid): static
    {
        $this->rvid = $rvid;

        return $this;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): static
    {
        $this->configuration = $configuration;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}