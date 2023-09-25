<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ApiResource(
        operations: [
            new Get(),
            new Post(),
            new Patch(security: 'is_granted("ROLE_ADMIN")'),
            new Delete(security: 'is_granted("ROLE_ADMIN")'),
        ],
        normalizationContext: ['groups' => ['user:read']],
        denormalizationContext: ['groups' => ['user:write']],
    ),
    ApiResource(
        uriTemplate: '/reviews/{review}/cars',
        operations: [new Get()],
        uriVariables: [
            'id' => new Link(
                fromProperty: 'car',
                fromClass: Model::class
            )
        ]
    ),
    ApiFilter(
        SearchFilter::class,
        properties: [
            'username' => 'partial',
            'email' => 'partial',
        ]
    ),
    ORM\Table(name: 'users'),
    ORM\Entity(repositoryClass: UserRepository::class),
]
class User implements UserInterface
{
    #[
        ORM\Id,
        ORM\GeneratedValue,
        ORM\Column(type: 'integer'),

        Groups(['user:read'])
    ]
    private int $id;

    #[
        ORM\Column(
            length: 255,
            unique: true
        ),
        Assert\NotBlank(message: 'Username cannot be blank.'),
        Assert\Length(
            min: 3,
            max: 255,
            minMessage: 'Username must be at least {{ limit }} characters.',
            maxMessage: 'Username cannot be longer than {{ limit }} characters.'
        ),

        Groups(['user:read', 'user:write'])
    ]
    private string $username;

    #[
        ORM\Column(
            type: 'string',
            length: 255,
            unique: true
        ),
        Assert\NotBlank(message: 'Email cannot be blank.'),
        Assert\Email(message: 'Invalid email format.'),
        Assert\Length(
            min: 3,
            max: 255,
            minMessage: 'Email must be at least {{ limit }} characters.',
            maxMessage: 'Email cannot be longer than {{ limit }} characters.'
        ),

        Groups(['user:read', 'user:write'])
    ]
    private string $email;

    #[
        ORM\Column(
            type: 'string',
            length: 255
        ),
        Assert\NotBlank(message: 'Password cannot be blank.'),
        Assert\Length(
            min: 6,
            max: 255,
            minMessage: 'Password must be at least {{ limit }} characters.',
            maxMessage: 'Password cannot be longer than {{ limit }} characters.'
        ),

        Groups(['user:write'])
    ]
    private string $password;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[
        ORM\OneToMany(
            mappedBy: 'creator',
            targetEntity: Car::class,
            cascade: [
                'persist',
                'detach'
            ]
        ),

        Groups(['user:read'])
    ]
    private Collection $cars;

    #[
        ORM\OneToMany(
            mappedBy: 'user',
            targetEntity: Review::class,
            orphanRemoval: true
        ),

        Groups(['user:read'])
    ]
    private Collection $reviews;


    public function __construct()
    {
        $this->cars = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getCars(): Collection
    {
        return $this->cars;
    }

    public function addCar(Car $car): static
    {
        if (!$this->cars->contains($car)) {
            $this->cars->add($car);
            $car->setManufacturer($this);
        }

        return $this;
    }

    public function removeCar(Car $car): static
    {
        if ($this->cars->removeElement($car)) {
            if ($car->getManufacturer() === $this) {
                $car->setManufacturer(null);
            }
        }

        return $this;
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function getUserIdentifier(): string
    {
        return 'id';
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }
}
