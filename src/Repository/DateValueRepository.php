<?php

namespace App\Repository;

use App\Entity\Currency;
use App\Entity\DateValue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DateValue|null find($id, $lockMode = null, $lockVersion = null)
 * @method DateValue|null findOneBy(array $criteria, array $orderBy = null)
 * @method DateValue[]    findAll()
 * @method DateValue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DateValueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DateValue::class);
    }

    /**
     * @param Currency $currency
     * @param \DateTimeInterface $dateTime
     * @param float $value
     * @param string $parserType
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveDateValueForCurrency(
        Currency $currency,
        \DateTimeInterface $dateTime,
        float $value,
        string $parserType
    ): bool
    {
        $dateValue = $this->findOneBy([
            'currency' => $currency,
            'date' => $dateTime,
        ]);
        if ($dateValue !== null) {
            return false;
        }

        $dateValue = (new DateValue())
            ->setCurrency($currency)
            ->setValue($value)
            ->setDate($dateTime)
            ->setParserType($parserType)
        ;
        $this->_em->persist($dateValue);
        $this->_em->flush($dateValue);

        return true;
    }

    // /**
    //  * @return DateValue[] Returns an array of DateValue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DateValue
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
