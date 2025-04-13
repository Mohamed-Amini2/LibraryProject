<?php

namespace App\Controller;

use App\DTOs\RegisterUserDTO;
use App\Services\RegisterService;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;



final class RegistrationController extends AbstractController
{
    //? i dont think i need somthing else
    public function __construct(
        private UserRepository $userRepo ,
        private EntityManagerInterface $em ,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private UserPasswordHasherInterface $passwordHasher,
        private RegisterService $registerService,
    ){}
    #[Route('/api/register', name: 'app_register' , methods:['POST'])]
    public function Register(Request $request)
    {

        //* Well her we actially deserializing our Json that we getting its content into DTO
    $dto = $this->serializer->deserialize($request->getContent(),
    RegisterUserDTO::class, 'json' );

    
    $errors = $this->validator->validate($dto);
    if (count($errors) > 0) {
        $formattedErrors = [];
        foreach ($errors as $error) {
            $formattedErrors[$error->getPropertyPath()][] = $error->getMessage();
        }
        return $this->json(['errors' => $formattedErrors] , response::HTTP_BAD_REQUEST);
    }
        try {
            $this->registerService->RegisterUserService($dto);
        }catch (\Exception $e){
                    if ($e->getCode() === Response::HTTP_CONFLICT)
                    {
                        return $this->json(['error' => $e->getMessage()], $e->getCode());
                    }
                    //* And here We are providing a message that says the we have createdd the user _
                    return $this->json(
                        ['error' => 'Registration failed. Please try again.'],
                        Response::HTTP_INTERNAL_SERVER_ERROR
                    );
        }
        return $this->json(
            ['message' => 'You have been registered successfully!'],
            Response::HTTP_CREATED
        );
    }
        
}