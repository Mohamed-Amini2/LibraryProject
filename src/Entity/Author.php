<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $FirstName = null;

    #[ORM\Column(length: 255)]
    private ?string $LastName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $YearOfBirth = null;

    /**
     * @var Collection<int, Books>
     */
    #[ORM\OneToMany(targetEntity: Books::class, mappedBy: 'author' , 
    orphanRemoval: true)]
    private Collection $Books;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Biography = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $AuthorImage = null;

    public function __construct()
    {
        $this->Books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->FirstName;
    }

    public function setFirstName(string $FirstName): static
    {
        $this->FirstName = $FirstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->LastName;
    }

    public function setLastName(string $LastName): static
    {
        $this->LastName = $LastName;

        return $this;
    }

    public function getYearOfBirth(): ?\DateTimeInterface
    {
        return $this->YearOfBirth;
    }

    public function setYearOfBirth(\DateTimeInterface $YearOfBirth): static
    {
        $this->YearOfBirth = $YearOfBirth;

        return $this;
    }

    /**
     * @return Collection<int, Books>
     */
    public function getBooks(): Collection
    {
        return $this->Books;
    }

    public function addBook(Books $book): static
    {
        if (!$this->Books->contains($book)) {
            $this->Books[] = $book;
            $book->setAuthor($this);
        }

        return $this;
    }

    public function removeBook(Books $book): static
    {
        if ($this->Books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->Biography;
    }

    public function setBiography(string $Biography): static
    {
        $this->Biography = $Biography;

        return $this;
    }

    public function getAuthorImage(): ?string
    {
        return $this->AuthorImage;
    }

    public function setAuthorImage(?string $AuthorImage): static
    {
        $this->AuthorImage = $AuthorImage;

        return $this;
    }
}
