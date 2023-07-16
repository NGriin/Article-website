<?php

namespace App\Actions;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Symfony\Component\String\u;

#[IsGranted('ROLE_ADMIN')]
class CreateArticleAction
{
 /*   private ArticleRepository $repository;
    private SluggerInterface $slugger;

    public function __construct(ArticleRepository $repository, SluggerInterface $slugger)
    {
        $this->repository = $repository;
        $this->slugger = $slugger;
    }

    public function create($data)
    {
        $slug = $this->generateSlug($data['title']);
        $article = new Article();
        $article
            ->setAuthor($data['author'])
            ->setSlug($slug)
            ->setBody($data['body'])
            ->setDescription($data['description'] ?? '')
            ->setPublishedAt(new \DateTime())
            ->setTitle($data['title'])
            ->setKeywords(explode($data['keywords'], true))
            ->setVoteCount(0);
        $this->repository->getEntityManager()->persist($article);
        $this->repository->getEntityManager()->flush();
        return $slug;
    }

    protected function generateSlug($title)
    {
        $slug = $this->slugger->slug(u($title)->ascii());
        $postfix = '';
        for ($index = 1; $index < 1000; $index++) {
            if ($this->repository->findOneBy(['slug' => $slug . $postfix])) {
                $postfix = sprintf('-%s', $index);
            } else {
                return $slug . $postfix;
            }
        }
        throw new \Exception("Слишком много статей с одинаковым названием");
    }*/
}