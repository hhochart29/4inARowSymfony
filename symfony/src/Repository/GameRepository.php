<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * @return Game[] Returns an array of Game objects
     */
    public function findByPlayer(Player $player)
    {
        $qb = $this->createQueryBuilder('g');
        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->eq('g.player1', ':player'),
                $qb->expr()->eq('g.player2', ':player')
            )
        );

        return $qb
            ->setParameter('player', $player)
            ->getQuery()
            ->getResult();
    }

    public function findByPlayers(Player $player1, Player $player2)
    {
        $qb = $this->createQueryBuilder('g');
        $qb->andWhere(
            $qb->expr()->andX(
                $qb->expr()->eq('g.player1', ':player1'),
                $qb->expr()->eq('g.player2', ':player2')
            )
        );
        $qb->setMaxResults(1);

        try {
            return $qb->setParameter('player1', $player1)->setParameter('player2', $player2)->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
        } catch (NonUniqueResultException $e) {
        }
    }

    /*
    public function findOneBySomeField($value): ?Game
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
