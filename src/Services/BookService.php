<?php

namespace App\Services;

use App\DTOs\BookDTO;
use App\Repository\AuthorRepository;
use App\Entity\Books;
use App\Repository\BooksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


//! This BookService  Really neeeds An Update Specially In The Edit Not Efficent Enough And It So Shit Tbh But meh

class BookService{

    public function __construct(private AuthorRepository $authorRepo , private EntityManagerInterface $em , private BooksRepository $bookRepo)
    {}


    //* This literally makes us use A Specifique Form For books whcih consist of id title isbn publicationDAte And genre autheor id exc..
    //* Prevents exposing internal entity structure directly
    //* Allows full control over date formats, nested relationships
    //* Avoids accidentally exposing sensitive fields
    //* Ensures uniform response structure across all endpoints
    private function FormatBooks(Books $books){
        $author = $books->getAuthor();
        return [
            'id' => $books->getId(),
            'Title' => $books->getTitle(),
            'ISBN' => $books->getISBN(),
            'PublicationDate' => $books->getPublicationDate(),
            'Genre' => $books->getGenre(),
            'author' => [
                'id' => $author->getId(),
                'name' => $author->getFirstName() .''. $author->getLastName()
            ],
            'BooksImage' => $books->getBookImage()
        ];
    }
    public function CreateBook(BookDTO $dto): array
    {
        $author = $this->authorRepo->find($dto->authorId);

        //! and here we are checking if the author does exist
        if (!$author){
            throw new \InvalidArgumentException('The Fucking Author is not in the databse');
        }

        //! here we are upading one by one using the !== null To say if it didn't change keep it like that 
        $newBook = new Books();
        $newBook->setTitle($dto->title);
        $newBook->setISBN($dto->isbn);
        $newBook->setPublicationDate(new \DateTime($dto->publicationDate));
        $newBook->setGenre($dto->genre);
        $newBook->setAuthor($author);
        $newBook->setBookImage($dto->bookImage);

        $this->em->persist($newBook);
        $this->em->flush();


        return $this->FormatBooks($newBook);
    }


    //* We are updating our book in here we have an $id as the id of the book we want to edit  and the BookDTO $dto as the dto that we are using 
    public function updateBooks(int $id , BookDTO $dto): array
    {
        $Book = $this->bookRepo->find($id);
        //! here we are upading one by one using the !== null To say if it didn't change keep it like that

        //? i dont know if here is a better way to do this but for now i use this

        if (!$Book){
            throw new NotFoundHttpException('Book Not Found');
        }
        if ($dto->title !== null) $Book->setTitle($dto->title);
        if ($dto->isbn !== null) $Book->setISBN($dto->isbn);
        if ($dto->publicationDate !== null) $Book->setPublicationDate(new \DateTime($dto->publicationDate));
        if ($dto->genre !== null) $Book->setGenre($dto->genre);
        if ($dto->authorId) {
            $author = $this->authorRepo->find($dto->authorId);
            if(!$author){
                throw new NotFoundHttpException('Author Not Found');
            }
            $Book->setAuthor($author);
        }
        if ($dto->bookImage !== null) $Book->setBookImage($dto->bookImage);

        $this->em->flush();
        //* here we are returning the the format 
        return $this->FormatBooks($Book);
    }


    //* To show All the books that i have in the Repo of Books
    public function getAllBooks(): array
    {
        $books =    $this->bookRepo->findAll();
        return array_map([$this, 'FormatBooks'],$books);
    }
    
    //* to show the book By Id
    //* Wwe have the param of $id we catch it and we make the bookrepo search about the id convinient for it 
    //* if not found we throw an error
    public function getBookbyid(int $id): array {

        $book = $this->bookRepo->find($id);
        if(!$book){
            throw new NotFoundHttpException('The Books I not found ');
        }
        return $this->FormatBooks($book);
    }

    public function deleteBook(int $id) : void {
        $book = $this->bookRepo->find($id);

        if(!$book){
            throw new NotFoundHttpException(' the Book is not found To delete ');
        }

        $this->em->remove($book);
        $this->em->flush();
    }
};  