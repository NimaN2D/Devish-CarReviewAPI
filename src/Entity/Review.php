<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        normalizationContext: ['groups' => ['review:read']],
        denormalizationContext: ['groups' => ['review:write']],
    ),
    ApiResource(
        uriTemplate: '/cars/{car}/reviews',
        operations: [new Get()],
        uriVariables: [
            'id' => new Link(
                fromProperty: 'reviews',
                fromClass: Car::class
            )
        ]
    ),
    ApiResource(
        uriTemplate: '/users/{user}/reviews',
        operations: [new Get()],
        uriVariables: [
            'id' => new Link(
                fromProperty: 'reviews',
                fromClass: Car::class
            )
        ]
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'rating' => 'exact',
            'review' => 'partial',
        ]
    ),
    ApiFilter(
        OrderFilter::class,
        properties: [
            'rating',
            'createdAt',
        ]
    ),

    ORM\Table(name: 'reviews'),
    ORM\Entity(repositoryClass: ReviewRepository::class),
]
class Review
{
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column,

        Groups(['review:read'])
    ]
    private int $id;

    #[
        ORM\Column,
        Assert\NotBlank(message: 'Rating cannot be blank.'),
        Assert\Range(notInRangeMessage: 'Rating must be between {{ min }} and {{ max }}.', min: 0, max: 10),

        Groups(['review:read', 'review:write'])
    ]
    private int $rating = 0;

    #[
        ORM\Column(
            type: Types::TEXT,
            nullable: true
        ),
        Assert\Length(
            max: 255,
            maxMessage: 'Review cannot be longer than {{ limit }} characters.'
        ),

        Groups(['review:read', 'review:write'])
    ]
    private ?string $review = null;

    #[
        ORM\Column(
            type: Types::DATETIME_MUTABLE,
            options: [
                'default' => 'CURRENT_TIMESTAMP'
            ]
        ),

        Groups(['review:read'])
    ]
    private \DateTimeInterface $createdAt;

    #[
        ORM\ManyToOne(inversedBy: 'reviews'),
        ORM\JoinColumn(nullable: false),
        Assert\NotBlank(message: 'User cannot be blank.'),

        Groups(['review:read', 'review:write', 'user:read'])
    ]
    private User $user;

    #[
        ORM\ManyToOne(inversedBy: 'reviews'),
        ORM\JoinColumn(nullable: false),
        Assert\NotBlank(message: 'Car cannot be blank.'),

        Groups(['review:read', 'review:write', 'car:read'])
    ]
    private Car $car;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getReview(): ?string
    {
        return $this->review;
    }

    public function setReview(?string $review): static
    {
        $this->review = $review;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): static
    {
        $this->car = $car;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
