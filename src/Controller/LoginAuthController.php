<?php

namespace App\Controller;

use App\DTOs\LoginUserDTO;
use App\Services\LoginService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


final class LoginAuthController extends AbstractController {
    public function __construct(
    private EntityManagerInterface $em ,
    private SerializerInterface $serializer ,
    private ValidatorInterface $validator,
    private LoginService $loginService,
    private LoggerInterface $logger,
    )
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
            $this->logger->warning('Failed To Authenticate: Try Again');
            return $this->json(
                ['error' => $e->getMessage()],
                $e->getCode() ?: Response::HTTP_UNAUTHORIZED
            );
        }
        $this->logger->info('User Authenticated Successfully');
        return $this->json(['message' => 'Has Been Logged in' , 'Token' => $token , Response::HTTP_OK]);
    }
    #[Route('/api/logout', name:'app_logout')]
    public function logout(Request $request, EventDispatcherInterface $eventDispatcher , TokenStorageInterface $tokenStorage)
    {
        $eventDispatcher->dispatch(new LogoutEvent($request, $tokenStorage->getToken()));

        dd($tokenStorage->getToken());

        return new JsonResponse(['Message' => 'You have been logged out'] , Response::HTTP_OK);
    }
}

