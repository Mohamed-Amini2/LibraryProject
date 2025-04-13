<?php 

namespace App\DTOs;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as ASSERT;

class LoginUserDTO{
    #[ASSERT\NotBlank(message: "Email Is Required to fill")]
    #[ASSERT\Email(message: "Your must Respect The Email form of writing")]
    public string $email;

    #[ASSERT\NotBlank(message: "Password Is required")]
    #[ASSERT\Length(min:8 , minMessage: "The Password Must at least be 8 Characters")]
    public string $password;
}