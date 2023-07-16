<?php

namespace App\Controller;

use App\Actions\CreateArticleAction;
use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method User|null getUser()
 */
class PageController extends AbstractController
{

    #[Route('/admin/articles', name: 'app_admin_articles', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN_ARTICLE')]
    public function index(ArticleRepository $articleRepository, Request $request, PaginatorInterface $paginator)
    {

        $pagination = $paginator->paginate(
            $articleRepository->findAllWithSearchQuery($request->query->get('q')),
            $request->query->getInt('page', 1), /*page number*/
            $request->query->get('limit')
        );

        return $this->render('/admin/article/index.html.twig',
            [
                'pagination' => $pagination,
            ]);
    }

    #[Route('/admin/articles/{id}/edit', name: 'app_admin_articles_edit', methods: ['GET', 'POST'])]
    #[IsGranted('MANAGE', subject: 'article')]
    public function edit(Article $article, Request $request, EntityManagerInterface $em, FileUploader $articleFileUploader)
    {
        $form = $this->createForm(ArticleFormType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var Article $article
             */
            $article = $form->getData();

            /**
             * @var  UploadedFile|null $image
             */
            $image = $form->get('image')->getData();

            if ($image) {
                $article->setImageFilename($articleFileUploader->uploadFile($image, $article->getImageFilename()));
            }

            $em->persist($article);
            $em->flush();

            $this->addFlash('flash_message', 'Статья успешно изменена');

            return $this->redirectToRoute('app_admin_articles_edit', ['id' => $article->getId()]);
        }

        return $this->render('admin/article/edit.html.twig', [
            'articleForm' => $form->createView(),
            'showError' => $form->isSubmitted(),
        ]);
    }

    #[Route('/admin', name: 'app_admin_panel', methods: ['GET'])]
    public function adminPage()
    {
        return $this->render('admin.html.twig');
    }

    #[Route('/', name: 'app_homepage', methods: ['GET'])]
    public function showHomePage(ArticleRepository $repository, CommentRepository $commentRepository)
    {
        $articles = $repository->getAllArticlesInOrder();
        $comments = $commentRepository->findBy(array(), ['createdAt' => 'desc'], 3);

        return $this->render('articles/home.html.twig', ['articles' => $articles, 'comments' => $comments]);
    }

    /*    #[Route('/api/test', name: 'app_test')]
        public function test()
        {
             dd('rabotaet');
        }*/

    #[Route('/article/create', name: 'app_article_create', methods: ['GET', 'POST'])]
    public function createArticlePage(Request $request, EntityManagerInterface $em, FileUploader $articleFileUploader)
    {
        $form = $this->createForm(ArticleFormType::class, new Article());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var Article $article
             */
            $article = $form->getData();

            /**
             * @var  UploadedFile|null $image
             */
            $image = $form->get('image')->getData();

            if ($image) {
                $article->setImageFilename($articleFileUploader->uploadFile($image, $article->getImageFilename()));
            }

            $em->persist($article);
            $em->flush();

            $this->addFlash('flash_message', 'Статья успешно опубликована');

            return $this->redirectToRoute('app_admin_articles');
        }

        return $this->render('admin/article/create.html.twig', [
            'articleForm' => $form->createView(),
            'showError' => $form->isSubmitted(),
        ]);
    }

    #[Route('/article/{slug}', name: 'app_article_show', methods: ['GET'])]
    public function showArticlePage(ArticleRepository $articleRepository, $slug)
    {
        $OneArticle = $articleRepository->findOneBySlug($slug);
        return $this->render('articles/article.html.twig', ['article' => $OneArticle]);
    }

    #[Route('/article/{slug}/vote/up', name: 'app_article_vote_up', methods: ['POST'])]
    public function articleVoteUp($slug, ArticleRepository $repository)
    {
        $count = $repository->voteUp($slug);
        return new JsonResponse(['count' => $count]);
    }

    #[Route('/article/{slug}/vote/down', name: 'app_article_vote_down', methods: ['POST'])]
    public function articleVoteDown($slug, ArticleRepository $repository)
    {
        $count = $repository->voteDown($slug);
        return new JsonResponse(['count' => $count]);
    }
}