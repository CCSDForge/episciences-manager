<?php
namespace App\Entity;

use App\Enum\IndexingDatabaseStatus;
use App\Repository\IndexingDatabaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IndexingDatabaseRepository::class)]
#[ORM\Table(name: 'INDEXING_DATABASE')]
class IndexingDatabase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(type: 'indexing_database_status')]
    private ?IndexingDatabaseStatus $status = null;

    #[ORM\Column(name: 'CREATED_AT', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'UPDATED_AT', type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'CREATED_BY', referencedColumnName: 'UID', nullable: true)]
    private ?User $createdBy = null;

    /** @var Collection<int, Review> */
    #[ORM\ManyToMany(targetEntity: Review::class, inversedBy: 'indexingDatabases')]
    #[ORM\JoinTable(name: 'REVIEW_INDEXING_DATABASE')]
    #[ORM\JoinColumn(name: 'INDEXING_DATABASE_ID', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'RVID', referencedColumnName: 'rvid')]
    private Collection $reviews;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->status = IndexingDatabaseStatus::PENDING;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;
        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;
        return $this;
    }

    public function getStatus(): ?IndexingDatabaseStatus
    {
        return $this->status;
    }

    public function setStatus(IndexingDatabaseStatus $status): static
    {
        $this->status = $status;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /** @return Collection<int, Review> */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
        }
        return $this;
    }

    public function removeReview(Review $review): static
    {
        $this->reviews->removeElement($review);
        return $this;
    }

    public function isValidated(): bool
    {
        return $this->status === IndexingDatabaseStatus::VALIDATED;
    }
}
