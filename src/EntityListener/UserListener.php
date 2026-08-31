<?php 
namespace App\EntityListener;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserListener
 {
    private UserPasswordHasherInterface $Hasher;


 public function __construct( UserPasswordHasherInterface $passwordHasher)
    {
        $this->Hasher = $passwordHasher;
    }   
  
    public function prePersist(\App\Entity\User $user)
    {
       
        $this->encodePassword($user);
    }

    public function preUpdate(\App\Entity\User $user)
    {
        $this->encodePassword($user);
    }

    /**encode password based on the plain password if it is not null
     * @param \App\Entity\User $user
     * @return void 
     */
   public function encodePassword(User $user): void
{
    if ($user->getPlainPassword() === null) {
        return;
    }

    $user->setPassword(
        $this->Hasher->hashPassword(
            $user,
            $user->getPlainPassword()
        )
    );

    $user->setPlainPassword(null);
}
     
}

?>