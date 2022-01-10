<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\Instrument;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }
    
    public function getInstrumentTransactionsForUser(User $user, Instrument $instrument)
    {
        $q = $this->_em->createQueryBuilder()
            ->select(
                't.id AS transaction',
                't.time AS time',
                't.notes AS notes',
                'e.volume AS volume',
                'e.price AS price',
                'e.price * e.volume AS total',
                '-1 * (COALESCE(t.tax, 0) + COALESCE(t.commission, 0) + COALESCE(t.interest, 0)) AS costs',
                'e.direction AS direction',
                't.external_id AS external_id',
                'a.name AS accountname',
                'a.id AS accountid',
            )
            ->from('App\Entity\Transaction', 't')
            ->leftJoin('App\Entity\Execution', 'e', Join::WITH, 'e.transaction = t.id')
            ->innerJoin('App\Entity\Account', 'a', Join::WITH, 't.account = a.id')
            ->where('a.owner = :user')
            ->andWhere('t.instrument = :instrument')
            ->setParameter('user', $user)
            ->setParameter('instrument', $instrument)
            ->getQuery();
        return $q->getResult();
    }

    public function getAccountTransactions(Account $account)
    {
        $qb = $this->_em->createQueryBuilder(); 
        $q = $qb
            ->select('t')
            ->from('App\Entity\Transaction', 't')
            ->where('t.account = :account')
            ->andWhere($qb->expr()->orX('t.cash IS NOT NULL', 't.consolidation IS NOT NULL'))
            ->setParameter('account', $account)
            ->getQuery();
        return $q->getResult();
    }


    // /**
    //  * @return Transaction[] Returns an array of Transaction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
