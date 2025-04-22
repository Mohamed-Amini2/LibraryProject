<?php

namespace App\Services;

use App\DTOs\AuthorDTO;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Repository\BooksRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthorService {

    public function __construct(
    private AuthorRepository $authorRepo ,
    private EntityManagerInterface $em ,
    private BooksRepository $bookRepo ,
    private AuthorDTO $dto)
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

    public function EditAuthorById(int $id , AuthorDTO $dto): array
    {
        $author = $this->authorRepo->find($id);

        if(!$author){
            throw new NotFoundHttpException("The Author($id) that you Want To Edit is not found");
        }
        if($dto->firstName !== null) $author->setFirstName($dto->firstName);
        if($dto->lastName !== null) $author->setLastName($dto->lastName);
        if($dto->yearOfbirth !== null) $author->setYearOfBirth(new \DateTime($dto->yearOfbirth));
        if($dto->biography !== null ) $author->setBiography($dto->biography);
        if($dto->authorImage !== null) $author->setAuthorImage($dto->authorImage);
        if($dto->bookIds !== null){
            $this->UpdateAuthorBooks($author , $dto->bookIds);
        }

        $this->em->flush();

        return $this->FormateAuthor($author);
    }

    public function     UpdateAuthorBooks(Author $author, array $newBooksIds): void
    {
        $currentBookCollection = $author->getBooks();
        $currentBooksIds = [];
        foreach ($currentBookCollection as $Book){
            $currentBooksIds[] = $Book->getId();
        }

        $toRemove = array_diff($currentBooksIds, $newBooksIds);
        $toAdd = array_diff($newBooksIds , $currentBooksIds);

        foreach($toRemove as $bookId){
            $book = $this->bookRepo->find($bookId);
            if($book){
                $author->removeBook($book);
            }
        }


        foreach($toAdd as $bookId){
            $book = $this->bookRepo->find($bookId);
            if(!$book){
                throw new NotFoundHttpException("The Book With This Id($bookId) is not found");
            }
            $author->addBook($bookId);
        }
    }
    

    public function GetAllAuthors():array
    {
        $authors = $this->authorRepo->findAll();

        return array_map([$this , 'FormatedAuthor'] , $authors);
    }

    public function DeleteAuthorById(int $id)
    {
        $author = $this->authorRepo->find($id);

        if (!$author){
            throw new NotFoundHttpException("The Author($id) that you Want To delete is not found");
        }
        
        $this->em->remove($author);
        $this->em->flush();
    }

    public function GetAuthorById(int $id): array
    {
        $author = $this->authorRepo->find($id);

        if (!$author){
            throw new NotFoundHttpException("Well this Author($id) Is Not Found :(");
        }

        return $this->FormateAuthor($author);
    }
    
}