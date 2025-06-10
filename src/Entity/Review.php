<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ORM\Table('REVIEW')]
class Review
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $rvid = null;

    #[ORM\Column(length: 50)]
    private ?string $code = null;

    #[ORM\Column(length: 2000)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $status = null;

    #[ORM\Column]
    private ?\DateTime $creation = null;

    #[ORM\Column]
    private ?int $piwikid = null;

    #[ORM\Column]
    private ?bool $is_new_front_switched = null;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreation(): ?\DateTime
    {
        return $this->creation;
    }

    public function setCreation(\DateTime $creation): static
    {
        $this->creation = $creation;

        return $this;
    }

    public function getPiwikid(): ?int
    {
        return $this->piwikid;
    }

    public function setPiwikid(int $piwikid): static
    {
        $this->piwikid = $piwikid;

        return $this;
    }

    public function isNewFrontSwitched(): ?bool
    {
        return $this->is_new_front_switched;
    }

    public function setIsNewFrontSwitched(bool $is_new_front_switched): static
    {
        $this->is_new_front_switched = $is_new_front_switched;

        return $this;
    }
}
