<?php

namespace SON\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Role\Role;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="SON\Entity\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $username;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $password;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $plainPassword;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $roles = array('ROLE_USER');

    /**
     * @ORM\Column(type="datetime")
     */
    public $createdAt;

    public function __construct()
    {
        $this->createdAt = new \Datetime();
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function __toString()
    {
        return $this->getUsername();
    }

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'username' => $this->getUsername(),
            'salt' => $this->getSalt(),
            'roles' => $this->getRoles(),
            'password'=>$this->getPassword()
        );
    }
}