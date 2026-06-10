<?php

namespace App\Entity;

use App\Repository\JournalBackofficeSettingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JournalBackofficeSettingRepository::class)]
#[ORM\Table(name: 'REVIEW_SETTING')]
class JournalBackofficeSetting
{
    #[ORM\Id]
    #[ORM\Column(name: 'RVID', type: 'integer')]
    private ?int $rvid = null;

    #[ORM\Id]
    #[ORM\Column(name: 'SETTING', type: 'string', length: 200)]
    private ?string $setting = null;

    #[ORM\Column(name: 'VALUE', type: 'text', nullable: true)]
    private ?string $value = null;

    public function getRvid(): ?int
    {
        return $this->rvid;
    }

    public function setRvid(int $rvid): static
    {
        $this->rvid = $rvid;
        return $this;
    }

    public function getSetting(): ?string
    {
        return $this->setting;
    }

    public function setSetting(string $setting): static
    {
        $this->setting = $setting;
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;
        return $this;
    }
}