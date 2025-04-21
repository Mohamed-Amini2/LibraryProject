<?php

namespace App\DTOs;

use Symfony\Component\Validator\Constraints as Assert;

class AuthorDTO
{
    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 1, groups: ['update', 'create'])]
    public ?string $firstName = null;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 1, groups: ['update', 'create'])]
    public ?string $lastName = null;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Regex(
        pattern: '/^\d{4}$/',
        message: 'Year must be 4 digits',
        groups: ['create', 'update']
    )]
    public ?string $yearOfbirth = null;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Length(min: 10, max: 1000, groups: ['update', 'create'])]
    public ?string $biography = null;

    #[Assert\NotBlank(groups: ['create'])]
    #[Assert\Url(groups: ['update', 'create'])]
    public ?string $authorImage = null;

    #[Assert\All([
        new Assert\Positive,
        new Assert\Type('integer')
    ], groups: ['update'])]
    public ?array $bookIds = null;
}