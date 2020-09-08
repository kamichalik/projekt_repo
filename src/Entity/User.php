<?php
/**
 * User entity.
 */

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 *
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * Primary key.
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Email.
     *
     * @var string
     *
     * @ORM\Column(
     *     type="string",
     *     length=180,
     *     unique=true
     *)
     */
    private $email;

//* @Assert\NotBlank
//* @Assert\Email(
//*     message = "The email '{{ value }}' is not a valid email."
//* )
//* @Assert\Length(
//*      min="3",
//*      max="180",
//* )

    /**
     * Roles.
     *
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * Password.
     *
     * @var string The hashed password
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * Getter for id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for email.
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Setter for email.
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     *
     * @return string
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * Getter for roles.
     *
     * @see UserInterface
     *
     * @return array
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Setter for roles.
     *
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Getter for password.
     *
     * @see UserInterface
     *
     * @return string
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * Setter for password.
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Getter for salt.
     *
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * Erase credentials.
     *
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
