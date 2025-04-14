<?php

namespace App\Services;

use App\DTOs\LoginUserDTO;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginService {
    public function __construct(
    private EntityManagerInterface $em ,
    private UserPasswordHasherInterface $passwordHasher,
    private UserRepository $userRepo,
    private JWTTokenManagerInterface $jwtManager,
    private LoggerInterface $logger,
    ){}
    
    public function LoginAuthentification(LoginUserDTO $dto){
        //* now we going to search for the user by email
        $user = $this->userRepo->findOneBy(['email' => $dto->email]);
        

        //* in this case we must say that the invalid email or password since we can't tell if someone has this password or email
        if(!$user){
            $this->logger->warning('Login attempt failed: User not found');
            throw new \Exception('Invalid Email Or Password' , Response::HTTP_UNAUTHORIZED);
        }

        if(!$this->passwordHasher->isPasswordValid($user, $dto->password)) {
            $this->logger->warning('The Password Is not Correct');
            throw new \Exception('Invalid Emaill or Password' , Response::HTTP_UNAUTHORIZED);
        }

        //* here it means that we created a jwt since the user is already logged in

        $token = $this->jwtManager->create($user);

        //* our log 
        $this->logger->info('JWT TOKEN IS CREATED');
        return $token;
    }
}