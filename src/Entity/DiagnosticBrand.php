<?php

namespace App\Entity;

use App\Repository\DiagnosticBrandRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DiagnosticBrandRepository::class)
 */
class DiagnosticBrand
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PriceBrand::class, inversedBy="diagnostic")
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=Content::class, inversedBy="diagnostic")
     */
    private $article;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?PriceBrand
    {
        return $this->brand;
    }

    public function setBrand(?PriceBrand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getArticle(): ?Content
    {
        return $this->article;
    }

    public function setArticle(?Content $article): self
    {
        $this->article = $article;

        return $this;
    }
}
