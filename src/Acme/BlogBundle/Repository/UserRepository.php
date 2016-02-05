<?php

namespace Acme\BlogBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class UserRepository extends DocumentRepository implements UserLoaderInterface
{
    public function loadUserByUsername($username)
    {

        $user = $this->createQueryBuilder('u')
            ->field('email')
            ->equals($username)
            ->getQuery()
            ->getSingleResult();

        if (null === $user) {
            $message = sprintf(
                'Unable to find an active admin AppBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message);
        }

        return $user;
    }
}

