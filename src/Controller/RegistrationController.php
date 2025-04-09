<?php

namespace App\Controller;

use App\DTOs\RegisterUserDTO;
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
    ){}
    #[Route('/api/register', name: 'app_register' , methods:['POST'])]
    public function Register(Request $request): JsonResponse
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
        return $this->json(
            ['errors' => $formattedErrors],
            JsonResponse::HTTP_BAD_REQUEST //* and for this one is just the 400
        );
    }
        //! must confirm that no one has the same email since it's like a unique identity
    if($this->userRepo->findOneBy(['email' => $dto->email])){
        return new JsonResponse(['error' => 'the email is already in use'],
        Response::HTTP_CONFLICT); //* or just tbh write like 409 instead
    }
        //* We are creating a user hashing it's password and then flushing the infos
    $user = new User;
    $user->setEmail($dto->email);
    $hashedpassword = $this->passwordHasher->hashPassword(
        $user,
        $dto->password
    );
    $user->setPassword($hashedpassword);

    try{
        $this->em->persist($user);
        $this->em->flush();
    } catch(\Exception $e){
        return $this->json(
            ['error' => 'Registration failed' . $e], 
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    return new JsonResponse(
        ['message' => 'You have been Registered Successfully'],
        201
    );
    }
}