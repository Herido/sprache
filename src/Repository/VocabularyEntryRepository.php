<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\VocabularyEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class VocabularyEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VocabularyEntry::class);
    }

    /** @return VocabularyEntry[] */
    public function findByUserAndQuery(User $user, ?string $query): array
    {
        $qb = $this->createQueryBuilder('v')
            ->andWhere('v.user = :user')
            ->setParameter('user', $user)
            ->orderBy('v.word', 'ASC');

        if ($query) {
            $qb->andWhere('v.word LIKE :q OR v.translation LIKE :q')
                ->setParameter('q', '%'.$query.'%');
        }

        return $qb->getQuery()->getResult();
    }
}
