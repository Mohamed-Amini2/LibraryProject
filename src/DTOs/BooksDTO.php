<?php

namespace App\DTOs;

use Symfony\Component\Validator\Constraints as Assert;

class BookDTO
{
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 1, groups: ['update'])]
    public ?string $title = null;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Isbn(groups: ['create', 'update'])]
    public ?string $isbn = null;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Date(groups: ['create', 'update'])]
    public ?string $publicationDate = null;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 2, groups: ['update'])]
    public ?string $genre = null;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Positive(groups: ['create', 'update'])]
    public ?int $authorId;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Url(groups: ['create', 'update'])]
    public ?string $bookImage = null;
}