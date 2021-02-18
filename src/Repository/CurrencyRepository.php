<?php

namespace App\Repository;

use App\Entity\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currency[]    findAll()
 * @method Currency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Currency::class);
    }

    /**
     * @param string $currencyCode
     * @param string|null $description
     * @return Currency
     * @throws \Doctrine\ORM\ORMException
     */
    public function createCurrency(string $currencyCode, ?string $description = null): Currency
    {
        $currency = $this->findOneBy(['code' => $currencyCode]);
        if ($currency === null) {
            $currency = new Currency();
            $currency
                ->setCode($currencyCode)
                ->setDescription($description)
                ->setCreatedAt(new \DateTime())
            ;
            $this->_em->persist($currency);
            $this->_em->flush($currency);
        }

        return $currency;
    }

    /**
     * @param string $code
     * @return Currency|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastValue(string $code): ?Currency
    {
        $query = $this
            ->createQueryBuilder('c')
            ->select('c')
            ->addSelect('v')
            ->join('c.values', 'v')
            ->where('c.code = :code')
            ->setParameter('code', $code)
            ->orderBy('v.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
        ;
        return $query->getOneOrNullResult();
    }

    // /**
    //  * @return Currency[] Returns an array of Currency objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Currency
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
