<?php

namespace App\DTOs;

use Symfony\Component\Validator\Constraints as ASSERT;

//! always remmeber to validate with this "" Symfony\Component\Validator\Constraints as ASSERT; ""
class RegisterUserDTO {
     
    #[ASSERT\NotBlank(message: "Email musn't be blank")]
    #[ASSERT\Email(message: "Invalid email Format.")]
    public string $email;


    #[ASSERT\NotBlank(message : "You cannot leave your password Blank")]
    #[ASSERT\Length(
        min:10,
        minMessage: "Your Password must be at least {{limite}} characters",
        max: 4096,
        maxMessage:"Your Password Exceded the limite which is {{ limite }} characters"
        )]
    public string $password;

    #[Assert\NotBlank]
    #[Assert\EqualTo(
        propertyPath: "password",
        message: "Passwords do not match."
    )]
    public string $confirmPassword;


}
