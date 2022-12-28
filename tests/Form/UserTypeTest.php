<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\Test\TypeTestCase;

class UserTypeTest extends TypeTestCase
{
  public function testSubmitValidDataUserType(): void
  {
    $formData = array(
      'username' => 'userNewUsername',
      'password' => ['first' => 'pw1234', 'second' => 'pw1234'],
      'email' => 'userNewUsername@gmail.fr',
      'roles' => 'ROLE_ADMIN'
    );

    $currentUser = new User();
    $form = $this->factory->create(UserType::class, $currentUser);

    $expectedUser = new User();
    $expectedUser->setUsername($formData['username']);
    $expectedUser->setPassword($formData['password']['first']);
    $expectedUser->setEmail($formData['email']);
    $expectedUser->setRoles(['ROLE_ADMIN']);

    $form->submit($formData);
    $this->assertTrue($form->isSynchronized());

    $this->assertEquals($expectedUser, $currentUser);
  }
}
