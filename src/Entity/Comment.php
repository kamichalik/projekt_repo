<?php
/**
 * Comment entity.
 */

namespace App\Entity;

/**
 * Class Comment.
 *
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment
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
     * Posting.
     *
     * @ORM\ManyToOne(targetEntity=Posting::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $posting;

    /**
     * Content.
     *
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="3",
     *     max="255",
     * )
     */
    private $content;

    /**
     * Date.
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * User.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=45)
     */
    private $user;

    /**
     * Getter for id.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for posting.
     *
     * @return Posting
     */
    public function getPosting(): ?Posting
    {
        return $this->posting;
    }

    /**
     * Setter for posting.
     *
     * @param Posting $posting
     *
     * @return $this
     */
    public function setPosting(?Posting $posting): self
    {
        $this->posting = $posting;

        return $this;
    }

    /**
     * Getter for content.
     *
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Setter for content.
     *
     * @param string $content
     *
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Getter for date.
     *
     * @return \DateTimeInterface
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * Setter for date.
     *
     * @param \DateTimeInterface $date
     *
     * @return $this
     */
    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Getter for user.
     *
     * @return string
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * Setter for user.
     *
     * @param string $user
     *
     * @return $this
     */
    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }
}
