<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        normalizationContext: ['groups' => ['car:read']],
        denormalizationContext: ['groups' => ['car:write']],
    ),
    ApiFilter(
        OrderFilter::class,
        properties: [
            'name',
            'year',
        ]
    ),

    ORM\Table(name: 'cars'),
    ORM\Entity(repositoryClass: CarRepository::class),
]
class Car
{
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column,

        Groups(['car:read'])
    ]
    private int $id;

    #[
        ORM\Column(length: 255),
        Assert\NotBlank(message: 'Brand cannot be blank.'),

        Groups(['car:read','car:write'])
    ]
    private string $brand;

    #[
        ORM\Column(length: 255),
        Assert\NotBlank(message: 'Model cannot be blank.'),

        Groups(['car:read','car:write'])
    ]
    private string $model;

    #[
        ORM\Column(length: 255),
        Assert\NotBlank(message: 'Color cannot be blank.'),

        Groups(['car:read','car:write'])
    ]
    private string $color;

    #[
        ORM\OneToMany(mappedBy: 'car', targetEntity: Review::class, orphanRemoval: true),
        ORM\OrderBy(['createdAt' => 'DESC']),

        Groups(['car:read'])
    ]
    private Collection $reviews;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setCar($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getCar() === $this) {
                $review->setCar(null);
            }
        }

        return $this;
    }
}
