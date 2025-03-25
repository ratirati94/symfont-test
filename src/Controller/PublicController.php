<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\News;
use App\Services\CategoryService;
use App\Services\CommentsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PublicController extends AbstractController
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly CommentsService $commentsService,
    )
    {
    }

    #[Route('/', name: 'public_home')]
    public function index(): Response
    {
        $categories = $this->categoryService->findAll();

        return $this->render('public/index.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/category/{id}', name: 'public_category_news')]
    public function categoryNews(
        Category $category,
        Request  $request,
    ): Response
    {
        $newsPagination = $this->categoryService
            ->getCategoryNewsPaginated($category, $request);

        return $this->render('public/category.html.twig', [
            'category' => $category,
            'newsPagination' => $newsPagination
        ]);
    }

    #[Route('/news/{id}', name: 'public_news_show')]
    public function show(News $news): Response
    {
        $commentForm = $this->commentsService->createCommentForm();

        return $this->render('public/news.html.twig', [
            'news' => $news,
            'form' => $commentForm->createView()
        ]);
    }

    #[Route('/news/{id}/comment', name: 'public_news_comment', methods: ['POST'])]
    public function postComment(
        Request                $request,
        News                   $news,
        EntityManagerInterface $em
    ): Response
    {
        $success = $this->commentsService->handleForm($request, $news);

        if ($success) {
            $this->addFlash('success', 'Your comment has been posted.');
        } else {
            $this->addFlash('error', 'There was a problem with your comment.');
        }

        return $this->redirectToRoute('public_news_show', ['id' => $news->getId()]);
    }
}
