<?php

namespace App\Controller;

use App\Entity\Books;
use App\Repository\AuthorRepository;
use App\Repository\BooksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/api/books', name: 'app_books')]
class BooksController extends AbstractController
{

    public function __construct(
        private BooksRepository $BooksRepo,
        private AuthorRepository $AuthorRepo,
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('/', name: 'app_books' , methods: ['GET'])]
    public function showall(): JsonResponse
    {

        $books = $this->BooksRepo->findAll();

        if(!$books){
            return new JsonResponse(['erorr'=> 'Books are currently not founded'], Response::HTTP_NOT_FOUND);
        }

        foreach ($books as $book){
            $booksdata[] = [
                'id' => $book->getId(),
                'Title' => $book->getTitle(),
                'Author' => $book->getAuthor()->getFirstName(),
                'Genre' => $book->getGenre(),
                'PublicationDate' => $book->getPublicationDate(),
            ];
        }
        return new JsonResponse($booksdata, Response::HTTP_OK);
    }

    
    #[Route('/{id}', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function showbyid($id): JsonResponse
    {
        $Book = $this->BooksRepo->find($id);

        if(!$Book){
            return new JsonResponse(['error' => 'Book was not found '] , Response::HTTP_NOT_FOUND);
        }

        $Bookdata = [
            'id' => $Book->getId(),
            'Title' => $Book->getTitle(),
            'Author' => $Book->getAuthor()->getFirstName(),
            'Genre' => $Book->getGenre(),
            'PublicationDate' => $Book->getPublicationDate(),
        ];

        return new JsonResponse($Bookdata , Response::HTTP_OK);
    }

        #[Route('/addbook', methods: ['POST'] , name:'app_books_add')]
        public function addBook(Request $request) : JsonResponse
        {

            // We do collect the infos and the data first so that we can see if we have it or no 
            $data = json_decode($request->getContent(), true);

            // and here basically cheking if yes or no
            if(!$data) {
                return new JsonResponse(['error' => 'data not found ', Response::HTTP_NOT_FOUND]);
            }
            // and here we going to do like a variable that tells use the requieremnt fields to complete or to fill 
            $RequirementFileds = ['Title' , 'ISBN' , 'PublicationDate' , 'Genre' , 'author', 'BookImage'];
            foreach ($RequirementFileds as $Field){
                if(!isset($data[$Field])){
                    return new JsonResponse(['error' => 'No Data Was Found'] , Response::HTTP_NOT_FOUND);
                }
            }
            // Now we checking for the Author if excist or no 
            $FieldAuthor = $this->AuthorRepo->find($data['author']);
            if(!$FieldAuthor){
                return new JsonResponse(['Erorr' => 'The Author was not found or there is another mistake in the code)']);
            } 
            
            try {
                $book = new Books();
                $book->setTitle($data['Title']);
                $book->setISBN($data['ISBN']);
                $book->setPublicationDate(new \DateTime($data['PublicationDate']));
                $book->setGenre($data['Genre']);
                $book->setAuthor($FieldAuthor);
                $book->setBookImage($data['BookImage']);

                $this->em->persist($book);
                $this->em->flush();

            // Return created book data
            return new JsonResponse([
                'id' => $book->getId(),
                'Title' => $book->getTitle(),
                'ISBN' => $book->getISBN(),
                'PublicationDate' => $book->getPublicationDate()->format('Y-m-d H:i:s'),
                'Genre' => $book->getGenre(),
                'Author' => $FieldAuthor->getFirstName() . ' ' . $FieldAuthor->getLastName(),
                'BookImage' => $book->getBookImage()
            ], Response::HTTP_CREATED);
            } catch(\Exception $e) {

                return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        #[Route('/delete/{id}' , methods:['GET'] , name:'app_del_books')]
        public function delbook($id): JsonResponse
        {
            $book = $this->BooksRepo->find($id);
            if(!$book)
            {
                return new JsonResponse(['erorr' => 'The Book(id) is not found'], Response::HTTP_NOT_FOUND);
            }
            $this->em->remove($book);
            $this->em->flush();
            return new JsonResponse(['Sucess' => 'The Book Has Been Delete succesfully'] , Response::HTTP_ACCEPTED);
        }

        /**
         * 
         * i still need to modify this shit shit since it still doesn't work for like changing the author
         */
        #[Route('/edit/{id}', methods:['POST' , 'PUT'] , name:'app_book_edit')]
        public function editBook($id , Request $request): JsonResponse
        {
            $book = $this->BooksRepo->find($id);
            if(!$book)
            {
                return new JsonResponse(['erorr' => 'The Book is not found'], Response::HTTP_NOT_FOUND);
            }
            try {
                $this->serializer->deserialize(
                    $request->getContent(), // the fucking information we going to get from your stupid fucking looking ugly bitch ass of a edit
                    Books::class, // here we specify the class that we dont want to create a new book but to just edit it since you stupid fuck
                    'json', // here we say we want it as json (input format)
                    ['object_to_populate' => $book] // here we say just marge it with the already existing book that we have using the specify id we give :)))
                );
        
                $errors = $this->validator->validate($book);
                if ($errors->count() > 0) {
                    return new JsonResponse(['errors' => (string) $errors], 422);
                }

                $this->em->flush();
            } catch (\JsonException $e) {
                return new JsonResponse(['error' => 'Invalid JSON'], 400);
            }
            return new JsonResponse(['sucess' => 'the entity was edited'], 200);
        }

}
