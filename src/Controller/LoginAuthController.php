<?php

namespace App\Controller;

use App\DTOs\LoginUserDTO;
use App\Services\LoginService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class LoginAuthController extends AbstractController {
    public function __construct(
    private EntityManagerInterface $em ,
    private SerializerInterface $serializer ,
    private ValidatorInterface $validator,
    private LoginService $loginService)
    {}
    #[Route('/api/login' , methods:['POST'] , name:'app_login')]
    public function LoginAuth(Request $request){

            //* We are now desiralizing the Request(content of it ) from JSON TO DTO
        $dto = $this->serializer->deserialize($request->getContent()
        , LoginUserDTO::class , 'json');

        $errors = $this->validator->validate($dto);
        if(count($errors) > 0 ) {
            $formattedErorrs = [];
            foreach($errors as $error){
                $formattedErorrs[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json(['error' => $formattedErorrs] , Response::HTTP_BAD_REQUEST);
        }

        try{
            //* Delegate authentication to the service
            $token = $this->loginService->LoginAuthentification($dto);
        } catch (\Exception $e){
            return $this->json(
                ['error' => $e->getMessage()],
                $e->getCode() ?: Response::HTTP_UNAUTHORIZED
            );
        }

        return $this->json(['message' => 'Has Been Logged in' , 'Token' => $token , Response::HTTP_OK]);
    }
}