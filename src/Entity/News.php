<?php


namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NewsRepository::class)]
#[ORM\Table(name: 'news')]

class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'news')]
    #[ORM\JoinColumn(name: 'UID', referencedColumnName: 'UID', nullable: false)]
    private ?User $creator = null;

    #[ORM\Column(name: 'legacy_id', type: 'integer', nullable: true, options: ['unsigned' => true, 'comment' => 'Legacy News id'])]
    private ?int $legacy_id = null;
    #[ORM\Column(name: 'code', length: 100, nullable: false, options: ['comment' => 'Journal code rvcode'])]
    private ?string $rvcode = null;

    #[ORM\Column(name: 'uid', type: 'integer', nullable: false, options: ['unsigned' => true])]
    private ?int $uid = null;

    #[ORM\Column(name: 'title', type: 'json', nullable: false, options: ['comment' => 'Page title'])]
    private array $title = [];

    #[ORM\Column(name: 'content', type: 'json', nullable: true)]
    private ?array $content = null;

    #[ORM\Column(name: 'link', type: 'json', nullable: true)]
    private ?array $link = null;

    #[ORM\Column(name: 'date_creation', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(name: 'date_updated', type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $dateUpdated = null;

    #[ORM\Column(name: 'visibility', type: 'json', nullable: false)]
    private array $visibility = [];


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLegacyId(): ?int
    {
        return $this->legacy_id;
    }

    public function setLegacyId(?int $legacy_id): static
    {
        $this->legacy_id = $legacy_id;

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

    public function getUid(): ?int
    {
        return $this->uid;
    }

    public function setUid(int $uid): static
    {
        $this->uid = $uid;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(?\DateTimeInterface $date_creation): static
    {
        $this->dateCreation = $date_creation;

        return $this;
    }

    public function getDateUpdated(): ?\DateTimeInterface
    {
        return $this->dateUpdated;
    }

    public function setDateUpdated(\DateTimeInterface $date_updated): static
    {
        $this->dateUpdated = $date_updated;

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

    public function getContent(): ?array
    {
        return $this->content;
    }

    public function setContent(?array $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getLink(): ?array
    {
        return $this->link;
    }

    public function setLink(?array $link): static
    {
        $this->link = $link;

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

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator(?User $creator): static
    {
        $this->creator = $creator;

        return $this;
    }
}
