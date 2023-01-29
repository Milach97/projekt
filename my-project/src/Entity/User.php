<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Proszę wprowadzić adres E-mail.")]
    #[Assert\Email()]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(message: "Proszę wprowadzić imię.")]
    #[ORM\Column(length: 45)]
    private ?string $name = null;

    #[Assert\NotBlank(message: "Proszę wprowadzić nazwisko.")]
    #[ORM\Column(length: 45)]
    private ?string $last_name = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phoneNumber = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Protector $protector = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Protege $protege = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $usermobilePasswordCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $usermobileSessionId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getProtector(): ?Protector
    {
        return $this->protector;
    }

    public function setProtector(?Protector $protector): self
    {
        $this->protector = $protector;

        return $this;
    }

    public function getProtege(): ?Protege
    {
        return $this->protege;
    }

    public function setProtege(?Protege $protege): self
    {
        $this->protege = $protege;

        return $this;
    }

    public function getUsermobilePasswordCode(): ?string
    {
        return $this->usermobilePasswordCode;
    }

    public function setUsermobilePasswordCode(?string $usermobilePasswordCode): self
    {
        $this->usermobilePasswordCode = $usermobilePasswordCode;

        return $this;
    }

    public function getUsermobileSessionId(): ?string
    {
        return $this->usermobileSessionId;
    }

    public function setUsermobileSessionId(?string $usermobileSessionId): self
    {
        $this->usermobileSessionId = $usermobileSessionId;

        return $this;
    }
}
