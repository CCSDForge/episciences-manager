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

    /** @var array<string, string> */
    #[ORM\Column(name: 'title', type: 'json', nullable: false)]
    private array $title = [];

    /** @var array<string, string> */
    #[ORM\Column(name: 'content', type: 'json', nullable: false)]
    private array $content = [];

    // Old column (JSON) - kept for backward compatibility with other projects
    #[ORM\Column(name: 'visibility', type: 'json', nullable: false)]
    private array $visibilityJson = [];

    // New column (SET) - optimized storage
    /** @var array<int, string> */
    #[ORM\Column(name: 'visibility_set', type: 'page_visibility', nullable: false)]
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

    /**
     * @return array<string, string>
     */
    public function getTitle(): array
    {
        return $this->title;
    }

    /**
     * @param array<string, string> $title
     */
    public function setTitle(array $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @param array<string, string> $content
     */
    public function setContent(array $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getVisibility(): array
    {
        return $this->visibility;
    }

    /**
     * @param array<int, string> $visibility
     */
    public function setVisibility(array $visibility): static
    {
        $this->visibility = $visibility;
        $this->visibilityJson = $visibility;  // Sync both columns
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateUpdated(): ?\DateTimeInterface
    {
        return $this->date_updated;
    }

    public function setDateUpdated(\DateTimeInterface $date_updated): static
    {
        $this->date_updated = $date_updated;

        return $this;
    }
}
