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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


//Todo:: Remvoing this shitty shit so that it can be comaptible with the Service DTO AND CONTROLLER structure ) 

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

    /**
     * @param is the fucking $data and the fucking $author
     * 
     * here we do the POST or ADD author
     */
    #[Route('/delete/{id}' , methods:['DELETE'] , name:'app_author_delete')]
    public function deleteAuthor($id): JsonResponse
    {
        $author = $this->AuthorRepo->find($id);
        if(!$author){
            return new JsonResponse(['error' => 'The Author Was not Found To Delete'],Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($author);
        $this->em->flush();

        return new JsonResponse(['Sucess' => 'The Author Was Succesfuly Delete'], Response::HTTP_OK);
    }

    #[Route('/edit/{id}' , methods:['PATCH'] , name: 'app_author_edit')]
    public function editauthor($id , Request $request): JsonResponse
    {
        $author = $this->AuthorRepo->find($id);
        if(!$author){
            return new JsonResponse(['error' => 'The Author Was not Found To Delete'],Response::HTTP_NOT_FOUND);
        }

        try {
            $this->serializer->deserialize(
                $request->getContent(),
                Author::class,
                'json',
                ['object_to_populate' => $author]
            );

        $this->em->flush();
        }catch(\JsonException $e) { 
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        return new JsonResponse($author , 200);
    }
}
