<?php

namespace App\Entity;

use App\Repository\PromotionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PromotionRepository::class)]
class Promotion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $code = null;

    #[ORM\Column]
    private ?int $percentage = null;

    #[ORM\Column]
    private ?bool $ative = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $valid_until = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getPercentage(): ?int
    {
        return $this->percentage;
    }

    public function setPercentage(int $percentage): static
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function isAtive(): ?bool
    {
        return $this->ative;
    }

    public function setAtive(bool $ative): static
    {
        $this->ative = $ative;

        return $this;
    }

    public function getValidUntil(): ?\DateTime
    {
        return $this->valid_until;
    }

    public function setValidUntil(?\DateTime $valid_until): static
    {
        $this->valid_until = $valid_until;

        return $this;
    }
}
