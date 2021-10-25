<?php

namespace App\DataPersiter;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;

class PostPersister implements DataPersisterInterface
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    public function supports($data): bool
    {
        return $data instanceof Post;
    }

    public function persist($data)
    {
        // 1. Mettre une date de création sur l'article
        $data->setCreatedAt(new \DateTime());

        // 2. Demander à Doctrine de persister
        $this->em->persist($data);
        $this->em->flush();
    }

    public function remove($data)
    {
        // 1. Demandre à Doctrine de supprimer l'article.
        $this->em->remove($data);
        $this->em->flush();
    }

}