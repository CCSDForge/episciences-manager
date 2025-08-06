<?php

namespace App\Entity;

use App\Repository\PageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PageRepository::class)]
#[ORM\Table(name: 'pages')]
class Page
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'uid', type: 'integer', nullable: false)]
    private ?int $uid = null;

    #[ORM\Column(name: 'page_code',length: 255, nullable: false)]
    private ?string $page_code = null;

    #[ORM\Column(name: 'code', length: 100, nullable: false, options: ['comment' => 'Journal code rvcode'])]
    private ?string $rvcode = null;

    #[ORM\Column(name: 'title', type: 'json', nullable: false)]
    private array $title = [];

    #[ORM\Column(name: 'content', type: 'json', nullable: false)]
    private array $content = [];

    #[ORM\Column(name: 'visibility', type: 'json', nullable: false)]
    private array $visibility = [];

    #[ORM\Column(name: 'date_creation', type: Types::DATETIME_MUTABLE,nullable: true)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(name: 'date_updated', type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $date_updated = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUid(): ?int
    {
        return $this->uid;
    }

    public function setUid(int $uid): static
    {
        $this->uid = $uid;

        return $this;
    }

    public function getPageCode(): ?string
    {
        return $this->page_code;
    }

    public function setPageCode(string $page_code): static
    {
        $this->page_code = $page_code;

        return $this;
    }


    public function getRvcode(): ?string
    {
        return $this->rvcode;
    }

    public function setRvcode(string $rvcode): static
    {
        $this->rvcode = $rvcode;

        return $this;
    }

    public function getTitle(): array
    {
        return $this->title;
    }

    public function setTitle(array $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function setContent(array $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getVisibility(): array
    {
        return $this->visibility;
    }

    public function setVisibility(array $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTime $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateUpdated(): ?\DateTime
    {
        return $this->date_updated;
    }

    public function setDateUpdated(\DateTime $date_updated): static
    {
        $this->date_updated = $date_updated;

        return $this;
    }
}
