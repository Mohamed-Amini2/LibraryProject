<?php

namespace App\Services;

use App\DTOs\RegisterUserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterService {


    //* we always put the construction that we going to use like the tools 
    public function __construct(
    private UserRepository $userRepo ,
    private EntityManagerInterface $em ,
    private UserPasswordHasherInterface $passwordHasher
    ){

    }

    public function RegisterUserService(RegisterUserDTO $dto){
        
        //! we first check if there is this email already in the database ! ")
        if ($this->userRepo->findOneBy(['email' => $dto->email])){
            return new \Exception('The Email is Already in use' , Response::HTTP_CONFLICT);
        }

        $user = new User;
        $user->setEmail($dto->email);

        //! we hashing our password first before we insert it into the database
        $hashedPassw = $this->passwordHasher->hashPassword($user , $dto->password);
        $user->setPassword($hashedPassw);
        try {
            $this->em->persist($user);
            $this->em->flush();

        } catch(\Exception $e){
            throw new \Exception('Registration Failed ' . $e->getMessage() , Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


}

