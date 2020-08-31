<?php
/**
 * Category entity.
 */

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
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
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
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
     * @return Collection|Posting[]
     */
    public function getPostings(): Collection
    {
        return $this->postings;
    }

    /**
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
