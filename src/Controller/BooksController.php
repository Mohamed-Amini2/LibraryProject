<?php

namespace App\Controller;

use App\Entity\Books;
use App\Repository\BooksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/api/books', name: 'app_books')]
class BooksController extends AbstractController
{

    public function __construct(
        private BooksRepository $BooksRepo,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('/', name: 'app_books' , methods: ['GET'])]
    public function index(): JsonResponse
    {

        $books = $this->BooksRepo->findAll();

        $bookdata = [];
        foreach ($books as $book){
            $bookdata[] = [
                'id' => $book->getId(),
                'Title' => $book->getTitle(),
                'Author' => $book->getAuthor()->getFirstName(),
                'Genre' => $book->getGenre(),
                'PublicationDate' => $book->getPublicationDate(),
            ];
        }
        return new JsonResponse($bookdata, Response::HTTP_OK);
    }
    

    #[Route('/{id}', methods: ['GET'])]
    public function show(Books $book): JsonResponse
    {
        return $this->json($book, 200, [], ['groups' => 'book:read']);
    }
}
