<?php

namespace App\Controller;

use App\DTOs\BookDTO;
use App\Entity\Books;
use App\Repository\AuthorRepository;
use App\Repository\BooksRepository;
use App\Services\BookService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/api/books', name: 'app_books')]
final class BooksController extends AbstractController
{

    public function __construct(
        private BooksRepository $BooksRepo,
        private AuthorRepository $AuthorRepo,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private BookService $bookService
    ) {
    }

    #[Route('/', name: 'app_books' , methods: ['GET'])]
    public function ShowAllBooks(): JsonResponse
    {
        try{
            $books = $this->bookService->getAllBooks();

            return $this->json($books , Response::HTTP_ACCEPTED);
        }
        catch(\Exception $e){
            return $this->json(['Error' => $e->getMessage()] , Response::HTTP_BAD_REQUEST);
        }
        catch(\InvalidArgumentException $e){
            return $this->json(['error' => $e->getMessage()] , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function ShowBookById(int $id): JsonResponse
    {
        try {
            $book = $this->bookService->getBookbyid($id);

            return $this->json($book , Response::HTTP_OK);
        }
        catch(\Exception $e){
            return $this->json(['Error' => $e->getMessage()] , Response::HTTP_BAD_REQUEST);
        }
        catch(\InvalidArgumentException $e){
            return $this->json(['error' => $e->getMessage()] , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

        #[Route('/addbook', methods: ['POST'] , name:'app_books_add')]
        public function AddBook(Request $request) : JsonResponse
        {
            try{
                $dto = $this->serializer->deserialize(
                    $request->getContent(),
                    BookDTO::class ,
                    'json'
                );


                $errors = $this->validator->validate($dto, groups:['create']);
                if ($errors->count() > 0 ){
                    return $this->json($errors , Response::HTTP_BAD_REQUEST);
                }

                $book = $this->bookService->CreateBook($dto);
                

                return $this->json($book , RESPONSE::HTTP_CREATED);
            }
            catch(\Exception $e){
                return  $this->json(['Error' => $e->getMessage()] , RESPONSE::HTTP_BAD_REQUEST);
            }
            catch(\InvalidArgumentException $e){
                return $this->json(['error' => $e->getMessage()] , Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        #[Route('/delete/{id}' , methods:['DELETE'] , name:'app_del_books')]
        public function DeleteBook(int $id): JsonResponse
        {
            try{
                
                $this->bookService->deleteBook($id);
                return $this->json(['Message Success ' => 'The Book Has been Deleted'] , Response::HTTP_ACCEPTED);

            }
            catch (\InvalidArgumentException $e){
                return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
            }
            catch (\Exception $e){
                return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        /**
         * 
         * i still need to modify this shit shit since it still doesn't work for like changing the author
         */
        #[Route('/edit/{id}', methods:['PUT'] , name:'app_book_edit')]
        public function EditBook(int $id , Request $request): JsonResponse
        {
            try {
                $dto = $this->serializer->deserialize(
                    $request->getContent(),
                    BookDTO::class,
                    'json'
                );

                $errors = $this->validator->validate($dto, groups:['create']);
                if ($errors->count() > 0 ){
                    return $this->json($errors , Response::HTTP_BAD_REQUEST);
                }

                $book = $this->bookService->updateBooks($id , $dto);
                return $this->json($book ,Response::HTTP_OK);
            }
            catch(\Exception $e){
                return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
            }
            catch(\InvalidArgumentException $e){
                return $this->json(['error' => $e->getMessage()] , Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

}
