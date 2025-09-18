<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Page>
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

//    /**
//     * @return Page[] Returns an array of Page objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Page
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findPagesWithHierarchy(string $rvcode): array
    {
        $entityManager = $this->getEntityManager();

        // Utiliser une requête SQL native pour joindre avec WEBSITE_NAVIGATION
        // Utiliser MIN pour éviter les doublons (prendre le premier PARENT_PAGEID)
        $sql = '
            SELECT p.*, MIN(wn.PARENT_PAGEID) as PARENT_PAGEID
            FROM pages p
            LEFT JOIN WEBSITE_NAVIGATION wn ON wn.PAGEID = p.id
            WHERE p.code = :rvcode
            GROUP BY p.id, p.uid, p.page_code, p.code, p.title, p.content, p.visibility, p.date_creation, p.date_updated
            ORDER BY PARENT_PAGEID ASC
        ';

        $stmt = $entityManager->getConnection()->prepare($sql);
        $result = $stmt->executeQuery(['rvcode' => $rvcode]);
        $rows = $result->fetchAllAssociative();

        $pages = [];
        foreach ($rows as $row) {
            // Créer un objet Page depuis les données
            $page = new \App\Entity\Page();
            $page->setUid($row['uid']);
            $page->setPageCode($row['page_code']);
            $page->setRvcode($row['code']);
            $page->setTitle(json_decode($row['title'], true));
            $page->setContent(json_decode($row['content'], true));
            $page->setVisibility(json_decode($row['visibility'], true));
            $page->setDateCreation($row['date_creation'] ? new \DateTime($row['date_creation']) : null);
            $page->setDateUpdated(new \DateTime($row['date_updated']));
            $page->setParentPageId($row['PARENT_PAGEID']);

            $pages[] = $page;
        }

        return $pages;
    }
}
