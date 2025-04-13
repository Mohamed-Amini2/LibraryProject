<?php

namespace App\Services;

use App\DTOs\LoginUserDTO;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginService {
    public function __construct(
    private EntityManagerInterface $em ,
    private UserPasswordHasherInterface $passwordHasher,
    private UserRepository $userRepo,
    private JWTTokenManagerInterface $jwtManager
    ){}
    
    public function LoginAuthentification(LoginUserDTO $dto){
        //* now we going to search for the user by email
        $user = $this->userRepo->findOneBy(['email' => $dto->email]);

        //* in this case we must say that the invalid email or password since we can't tell if someone has this password or email
        if(!$user){
            throw new \Exception('Invalid Email Or Password' , Response::HTTP_UNAUTHORIZED);
        }

        if(!$this->passwordHasher->isPasswordValid($user, $dto->password)) {
            throw new \Exception('Invalid Emaill or Password' , Response::HTTP_UNAUTHORIZED);
        }

        return $this->jwtManager->create($user);

    }

}