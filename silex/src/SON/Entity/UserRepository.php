<?php

namespace SON\Entity;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserProviderInterface {

    private $passwordEncoder;

    public function createAdminUser($username, $password) {
        $user = new User();
        $user->username = $username;
        $user->plainPassword = $password;
        $user->roles = 'ROLE_ADMIN';

        $this->insert($user);

        return $user;
    }

    public function createUser($username, $password) {
        $user = new User();
        $user->username = $username;
        $user->plainPassword = $password;
        $user->roles = 'ROLE_USER';
        $this->insert($user);

        return $user;
    }

    public function setPasswordEncoder(PasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function insert($user) {
        $this->encodePassword($user);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function objectToArray(User $user) {
        return array(
            'id' => $user->id,
            'username' => $user->username,
            'password' => $user->password,
            'roles' => implode(',', $user->roles),
            'created_at' => $user->createdAt->format(self::DATE_FORMAT),
        );
    }

    /**
     * Turns an array of data into a User object
     *
     * @param array $userArr
     * @param User $user
     * @return User
     */
    public function arrayToObject($userArr, $user = null) {
        // create a User, unless one is given
        if (!$user) {
            $user = new User();

            $user->id = isset($userArr['id']) ? $userArr['id'] : null;
        }

        $username = isset($userArr['username']) ? $userArr['username'] : null;
        $password = isset($userArr['password']) ? $userArr['password'] : null;
        $roles = isset($userArr['roles']) ? explode(',', $userArr['roles']) : array();
        $createdAt = isset($userArr['created_at']) ? \DateTime::createFromFormat(self::DATE_FORMAT, $userArr['created_at']) : null;

        if ($username) {
            $user->username = $username;
        }

        if ($password) {
            $user->password = $password;
        }

        if ($roles) {
            $user->roles = $roles;
        }

        if ($createdAt) {
            $user->createdAt = $createdAt;
        }

        return $user;
    }

    public function loadUserByUsername($username) {

        $user = $this->findOneByUsername($username);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $this->arrayToObject($user->toArray());
    }

    public function refreshUser(UserInterface $user) {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) {
        return $class === 'SON\Entity\User';
    }

    /**
     * Encodes the user's password if necessary
     *
     * @param User $user
     */
    private function encodePassword(User $user) {
        if ($user->plainPassword) {
            $user->password = $this->passwordEncoder->encodePassword($user->plainPassword, $user->getSalt());
        }
    }

}
