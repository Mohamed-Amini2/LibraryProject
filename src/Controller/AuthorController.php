<?php

namespace App\Controller;

use App\DTOs\AuthorDTO;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\BooksRepository;
use App\Services\AuthorService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


//Todo:: Remvoing this shitty shit so that it can be comaptible with the Service DTO AND CONTROLLER structure ) 

#[Route('/api/authors', name: 'app_author')]
final class AuthorController extends AbstractController
{
    public function __construct(       
    private SerializerInterface $serializer,
    private ValidatorInterface $validator,
    private AuthorService $authorService)
    {
    }

    #[Route('/', name: 'app_author_all' , methods:['GET'])]
    public function ShowAllAuthors()
    {
        try {
            $authors = $this->authorService->GetAllAuthors();

            return $this->json($authors, Response::HTTP_OK);
        }
        catch(\Exception $e){
            return $this->json(['Error' => $e->getMessage()] , Response::HTTP_BAD_REQUEST);
        }
        catch(\InvalidArgumentException $e){
            return $this->json(['error' => $e->getMessage()] , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}' , methods:['GET'] , name: 'app_author_showbyid')]
    public function showbyId(int $id): JsonResponse 
    {
        try{
            $author = $this->authorService->GetAuthorById($id);

            return $this->json($author , Response::HTTP_OK);
        }
        catch(\Exception $e){
            return $this->json(['Error' => $e->getMessage()] , Response::HTTP_BAD_REQUEST);
        }
        catch(\InvalidArgumentException $e){
            return $this->json(['error' => $e->getMessage()] , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/delete/{id}' , methods:['DELETE'] , name:'app_author_delete')]
    public function deleteAuthor(int $id)
    {
        try {
            $this->authorService->DeleteAuthorById($id);

            return $this->json(['Message' => "The Author Has been Deleted Successfully."], Response::HTTP_NO_CONTENT);
        }
        catch(\Exception $e){
            return $this->json(['Error' => $e->getMessage()] , Response::HTTP_BAD_REQUEST);
        }
        catch(\InvalidArgumentException $e){
            return $this->json(['error' => $e->getMessage()] , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/edit/{id}' , methods:['PATCH'] , name: 'app_author_edit')]
    public function editauthor(int $id, Request $request)
    {
        try {
            $dto = $this->serializer->deserialize(
                $request->getContent(),
                AuthorDTO::class,
                'json'
            );

            $errors = $this->validator->validate($dto , groups:['update']);
            if($errors->count() > 0){
                return $this->json($errors , Response::HTTP_BAD_REQUEST);
            }

            $author = $this->authorService->EditAuthorById($id, $dto);
            return $this->json($author ,Response::HTTP_OK);
        }
        catch(\Exception $e){
            return $this->json(['Error' => $e->getMessage()] , Response::HTTP_BAD_REQUEST);
        }
        catch(\InvalidArgumentException $e){
            return $this->json(['error' => $e->getMessage()] , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/addAuthor' , methods:['POST'] , name: 'app_author_add')]
    public function AddingAuthor(Request $request):JsonResponse
    {
        try { 
            $dto = $this->serializer->deserialize(
                $request->getContent(),
                AuthorDTO::class,
                'json'
            );
            $errors = $this->validator->validate($dto, groups:['create']);
            if($errors->count() > 0) {
                return $this->json($errors , Response::HTTP_BAD_REQUEST);
            }

            $author = $this->authorService->AddAuthor($dto);
            
            return $this->json($author, Response::HTTP_OK);
        }
        catch(\Exception $e){
            return $this->json(['Error' => $e->getMessage()] , Response::HTTP_BAD_REQUEST);
        }
        catch(\InvalidArgumentException $e){
            return $this->json(['error' => $e->getMessage()] , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
