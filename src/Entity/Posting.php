<?php

namespace App\Entity;

use App\Repository\PostingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostingRepository::class)
 */
class Posting
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    private $tags;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $img;

    /**
     * @ORM\Column(type="string", length=512)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="postings")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="posting", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_active;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getSeparatedTags()
    {
        return explode(',', $this->tags);
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getImgPath()
    {
        if ((!empty($this->img) && 0 === strpos($this->img, 'https')) &&
            (strpos($this->img, '.jpg') || strpos($this->img, '.jpeg') || strpos($this->img, '.png'))) {
            return $this->img;
        }

        return null;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPosting($this);
        }

        return $this;
    }

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

    public function getCommentsHeader()
    {
        $counter = count($this->getComments());

        $ending = strval($counter);
        $ending = intval($ending[strlen($ending) - 1]);

        if ((12 !== $counter && 13 !== $counter && 14 !== $counter) && (2 === $ending || 3 === $ending || 4 === $ending)) {
            return ' komentarze';
        } elseif (1 === $counter) {
            return ' komentarz';
        } else {
            return ' komentarzy';
        }
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }
}
