<?php
/**
 * Category entity.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
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
     * Name.
     *
     * @var string
     *
     * @ORM\Column(
     *     type="string",
     *     length=255
     *     )
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @Assert\Length(
     *     min="3",
     *     max="255",
     *     )
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Posting::class, mappedBy="category")
     */
    private $postings;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->postings = new ArrayCollection();
    }

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
     * Getter for name.
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Setter for name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Getter for postings.
     *
     * @return Collection|Posting[]
     */
    public function getPostings(): Collection
    {
        return $this->postings;
    }

    /**
     * Add posting.
     *
     * @param Posting $posting
     *
     * @return $this
     */
    public function addPosting(Posting $posting): self
    {
        if (!$this->postings->contains($posting)) {
            $this->postings[] = $posting;
            $posting->setCategory($this);
        }

        return $this;
    }

    /**
     * Remove posting.
     *
     * @param Posting $posting
     *
     * @return $this
     */
    public function removePosting(Posting $posting): self
    {
        if ($this->postings->contains($posting)) {
            $this->postings->removeElement($posting);
            // set the owning side to null (unless already changed)
            if ($posting->getCategory() === $this) {
                $posting->setCategory(null);
            }
        }

        return $this;
    }
}
