<?php


namespace App\Entity;

use App\Repository\JournalFrontendSettingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JournalFrontendSettingRepository::class)]
#[ORM\Table(name: 'JOURNAL_SETTING')]
class JournalFrontendSetting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'ID')]
    private ?int $id = null;

    #[ORM\Column(name: 'RVID', type: 'integer', unique: true)]
    private ?int $rvid = null;

    /** @var array<string, mixed> */
    #[ORM\Column(name: 'SETTING', type: 'json')]
    private array $setting = [];

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

    /**
     * @return array<string, mixed>
     */
    public function getSetting(): array
    {
        return $this->setting;
    }

    /**
     * @param array<string, mixed> $setting
     */
    public function setSetting(array $setting): static
    {
        $this->setting = $setting;
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