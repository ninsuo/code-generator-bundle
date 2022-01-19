<?php

namespace Bundles\CodeGeneratorBundle\Repository;

use Bundles\CodeGeneratorBundle\Base\BaseRepository;
use Bundles\CodeGeneratorBundle\Entity\SnippetFile;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SnippetFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method SnippetFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method SnippetFile[]    findAll()
 * @method SnippetFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SnippetFileRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SnippetFile::class);
    }
}
