<?php

namespace App\Services;

use App\DTOs\AuthorDTO;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Repository\BooksRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\Date;

class AuthorService {

    public function __construct(private AuthorRepository $authorRepo , private EntityManagerInterface $em , private BooksRepository $bookRepo , private AuthorDTO $dto)
    {}

    private function FormateAuthor(Author $author){

        foreach($author->getBooks() as $book){
            $FormatedBooks[] =
            [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'isbn' => $book->getISBN(),
                'PublicationDate' => $book->getPublicationDate()
            ];
        }

        return [
            'id' => $author->getId(),
            'FirstName' => $author->getFirstName(),
            'LastName' => $author->getLastName(),
            'Year Of Birth' => $author->getYearOfBirth(),
            'Biography' => $author->getBiography(),
            'Author Image' => $author->getAuthorImage(),
            'Books of the Author' => $FormatedBooks
        ];
    }

    public function AddAuthor(AuthorDTO $dto):array 
    {
        $author = new Author;
        $author->setFirstName($dto->firstName);
        $author->setLastName($dto->lastName);
        $author->setYearOfBirth(new \DateTime($dto->yearOfbirth));
        $author->setBiography($dto->biography);
        $author->setAuthorImage($dto->authorImage);

        if(!empty($dto->bookIds))
        {
            foreach($dto->bookIds as $bookId)
            {
                $book = $this->bookRepo->find($bookId);
                $author->addBook($book);
            }
        }
        $this->em->persist($author);
        $this->em->flush();

        return $this->FormateAuthor($author);
    }

    public function EditAuthor(){

    }
    

    public function GetAllAuthors():array
    {
        $authors = $this->authorRepo->findAll();

        return array_map([$this , 'FormatedAuthor'] , $authors);
    }

    public function DeleteAuthorById(int $id)
    {

    }
    
}