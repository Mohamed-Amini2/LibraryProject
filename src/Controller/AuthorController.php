<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\BooksRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/api/authors', name: 'app_author')]
final class AuthorController extends AbstractController
{
    public function __construct(       
    private BooksRepository $BooksRepo,
    private AuthorRepository $AuthorRepo,
    private EntityManagerInterface $em,
    private SerializerInterface $serializer,
    private ValidatorInterface $validator)
    {
    }

    #[Route('/', name: 'app_author_all' , methods:['GET'])]
    public function Authors(): JsonResponse
    {
        $authors = $this->AuthorRepo->findAll();

        if(!$authors){
            return new JsonResponse(['error' => 'The Author not found '] , Response::HTTP_NOT_FOUND);
        }

        foreach ($authors as $author){

            $bookTitles = [];
            foreach($author->getBooks() as $Books){
                $bookTitles[] = $Books->getTitle();
            }
            $authorData = [
                'id' => $author->getId(),
                'FirstName' => $author->getFirstName(),
                'LastName' => $author->getLastName(),
                'Books' => $bookTitles,
            ];
        }

        return new JsonResponse($authorData , Response::HTTP_OK);
    }

    #[Route('/{id}' , methods:['GET'] , name: 'app_author_showbyid')]
    public function showbyId($id): JsonResponse 
    {
        $author = $this->AuthorRepo->find($id);
        if(!$author){
            return new JsonResponse(['error'=> 'this Author is not found'], Response::HTTP_NOT_FOUND);
        }
        $bookTitles = [];
        foreach($author->getBooks() as $Books){
            $bookTitles[] = $Books->getTitle();
        }

        
        $authorData = [
            'id' => $author->getId(),
            'FirstName' => $author->getFirstName(),
            'LastName' => $author->getLastName(),
            'Books' => $bookTitles,
        ];

        
        return new JsonResponse($authorData , Response::HTTP_OK);
    }
}
