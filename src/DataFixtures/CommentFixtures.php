<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends BaseFixtures implements DependentFixtureInterface
{
    function loadData(ObjectManager $manager)
    {
        $this->createMany(Comment::class, 100, function (Comment $comment) {
            $comment
                ->setAuthorName($this->faker->name)
                ->setContent(mb_substr($this->faker->paragraph,0,35))
                ->setCreatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'))
                ->setArticle($this->getRandomReference(Article::class));
            if ($this->faker->boolean) {
                $comment->setDeletedAt($this->faker->dateTimeThisMonth);
            }
        });
            $manager->flush();
        }

    public function getDependencies()
    {
        return [
            ArticleFixtures::class,
        ];
    }


}
