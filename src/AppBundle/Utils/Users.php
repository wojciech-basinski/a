<?php

namespace AppBundle\Utils;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Users
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $password;

    /**
     * @var int
     */
    private $error = 0;

    public function __construct(EntityManagerInterface $em, SessionInterface $session, UserPasswordEncoderInterface $password)
    {
        $this->em = $em;
        $this->session = $session;
        $this->password = $password;
    }

    public function register(User $user)
    {
        $user->setPassword($this->password->encodePassword($user, $user->getPassword()));

        $this->em->persist($user);
        $this->em->flush();
    }

    public function getAllUsers()
    {
        return $this->em->getRepository('AppBundle:User')->findAll();
    }

    public function editUser(User $originalUser, User $user): int
    {
        if (!$this->checkPassword($user->getPassword())) {
            $user->setPassword($originalUser->getPassword());
        } else {
            $user->setPassword($this->password->encodePassword($user, $user->getPassword()));
        }
        if (!$this->checkUserName($user->getUsername(), $originalUser->getUsername())) {
            $user->setUsername($originalUser->getUsername());
        }
        if (!$this->error) {
            $this->session->getFlashBag()->add('success','Pomyślnie edytowano użytkownika.');
        }
        $this->em->merge($user);
        $this->em->flush();
        return $this->error;
    }

    public function deleteUser(int $id, int $userId)
    {
        if ($id == $userId) {
            $this->session->getFlashBag()->add('error', 'Nie możesz usunąć samego siebie');
            return;
        }
        $user = $this->em->find('AppBundle:User', $id);
        if ($user) {
            $this->em->remove($user);
            $this->em->flush();
            $this->session->getFlashBag()->add('success', 'Usunięto użytkownika');
            return;
        }
        $this->session->getFlashBag()->add('error', 'Nie znaleziono użytkownika');
    }

    private function checkPassword(?string $password): bool
    {
        if ($password == '' || $password == null) {
            return false;
        }
        if (strlen($password) < 8) {
            $this->session->getFlashBag()->add('error', 'Hasło zbyt krótkie');
            $this->error++;
            return false;
        }
        return true;
    }

    private function checkUserName(?string $username, string $originalUsername): bool
    {
        if (strlen($username) < 6 || strlen($username) > 20) {
            $this->session->getFlashBag()->add('error', 'Nazwa użytkownika zbyt krótka');
            $this->error++;
            return false;
        }
        if ($username != $originalUsername) {
            if ($this->em->getRepository('AppBundle:User')->findOneBy(['username' => $username])) {
                $this->session->getFlashBag()->add('error', 'Nazwa użytkownika już istnieje');
                $this->error++;
                return false;
            }
        }
        return true;
    }
}