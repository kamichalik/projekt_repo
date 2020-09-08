<?php
/**
 * Posting entity.
 */

namespace App\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=PostingRepository::class)
 */
class Posting
{
    /**
     * Primary key.
     *
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Date.
     *
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="\DateTimeInterface")
     *
     */
    private $date;

    /**
     * Description.
     *
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     *
     */
    private $description;

    /**
     * Tags.
     *
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank()
     */
    private $tags;

    /**
     * Image.
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\Url(message="Dany url nie jest obrazkiem")
     */
    private $img;

    /**
     * Title.
     *
     * @ORM\Column(type="string", length=512)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *     min="3",
     *     max="512",
     * )
     */
    private $title;

    /**
     * Category.
     *
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="postings")
     */
    private $category;

    /**
     * Comments.
     *
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="posting", orphanRemoval=true)
     */
    private $comments;

    /**
     * Is Active.
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * Posting constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

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
     * Getter for date.
     *
     * @return \DateTimeInterface|null
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
     * Getter for description.
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Setter for description.
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Getter for tags.
     *
     * @return string|null
     */
    public function getTags(): ?string
    {
        return $this->tags;
    }

    /**
     * Setter for tags.
     *
     * @param string $tags
     *
     * @return $this
     */
    public function setTags(string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Getter for separated tags.
     *
     * @return array
     */
    public function getSeparatedTags()
    {
        return explode(',', $this->tags);
    }

    /**
     * Getter for image.
     *
     * @return string|null
     */
    public function getImg(): ?string
    {
        return $this->img;
    }

    /**
     * Setter for image.
     *
     * @param string $img
     *
     * @return $this
     */
    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Getter for img path.
     *
     * @return null
     */
    public function getImgPath()
    {
        if ((!empty($this->img) && 0 === strpos($this->img, 'https')) &&
            (strpos($this->img, '.jpg') || strpos($this->img, '.jpeg') || strpos($this->img, '.png'))) {
            return $this->img;
        }

        return null;
    }

    /**
     * Getter for title.
     *
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Getter for category.
     *
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Setter for category.
     *
     * @param Category|null $category
     *
     * @return $this
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Getter for comments.
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * Add comment.
     *
     * @param Comment $comment
     *
     * @return $this
     */
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPosting($this);
        }

        return $this;
    }

    /**
     * Remove comment.
     *
     * @param Comment $comment
     *
     * @return $this
     */
    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPosting() === $this) {
                $comment->setPosting(null);
            }
        }

        return $this;
    }

    /**
     * Getter for comments header.
     *
     * @return string
     */
    public function getCommentsHeader()
    {
        $counter = count($this->getComments());

        $ending = strval($counter);
        $ending = intval($ending[strlen($ending) - 1]);

        if ((12 !== $counter && 13 !== $counter && 14 !== $counter) && (2 === $ending || 3 === $ending || 4 === $ending)) {
            echo ' komentarze';
        } elseif (1 === $counter) {
            echo ' komentarz';
        } else {
            echo ' komentarzy';
        }
    }

    /**
     * Getter for is active.
     *
     * @return bool|null
     */
    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * Setter for is active.
     *
     * @param bool $isActive
     *
     * @return $this
     */
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
