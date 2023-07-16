<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Tag;
use App\Entity\User;
use App\Service\FileUploader;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;


class ArticleFixtures extends BaseFixtures implements DependentFixtureInterface
{
    private static $titles = [
        'Как стать котилионером на клавиатурах',
        'Любая книга Кафки как модель механической клавы',
        'Мембранная или же механика, кто победит кофе',
        'Купить дешевую замену клве!!!',
    ];

    private static $images = [
        'article-1.jpeg',
        'article-2.jpeg',
        'article-3.jpeg',
    ];

    private $articleFileUploader;

    public function __construct(FileUploader $articleFileUploader)
    {

        $this->articleFileUploader = $articleFileUploader;
    }

    public function loadData(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        $this->createMany(Article::class, 25, function (Article $article) use ($manager) {
            $article
                ->setTitle($this->faker->randomElement(self::$titles))
                ->setBody('Какой-то текст с **маркдаун** разметкой' . ' '
                    . $this->faker->paragraphs($this->faker->numberBetween(2, 5), true));
            $article->setPublishedAt($this->faker->dateTimeBetween('-1 week', '-1 day'));

            $fileName = $this->faker->randomElement(self::$images);

            $article->setAuthor($this->getRandomReference(User::class))
                ->setImageFilename($this->articleFileUploader->uploadFile(new File(dirname(dirname(__DIR__)) . '/public/images'. DIRECTORY_SEPARATOR . $fileName, $fileName)))
                ->setVoteCount($this->faker->numberBetween(0, 10));

            $tags = [];
            for ($i = 0; $i < $this->faker->numberBetween(0, 5); $i++) {
                $tags[] = $this->getRandomReference(Tag::class);
            }

            foreach ($tags as $tag) {
                $article->addTag($tag);
            }
        });
    }

    public function getDependencies()
    {
        return [
            TagFixtures::class,
            UserFixtures::class,
        ];
    }
}
