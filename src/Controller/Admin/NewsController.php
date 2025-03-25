<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Services\NewsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NewsController extends AbstractController{
    public function __construct(private readonly NewsService $newsService){

    }
    #[Route('/admin/news', name: 'admin_news_list')]
    public function index(): Response
    {
        $newsList = $this->newsService->findAllOrdered();
        return $this->render('admin/news/list.html.twig', [
            'news_list' => $newsList,
        ]);
    }

    #[Route('/admin/news/new', name: 'admin_news_new')]
    public function new(Request $request): Response
    {
        $form = $this->newsService->createForm();
        $isValid =  $this->newsService->validateRequest($request, $form);

        if ($isValid) {
            $this->addFlash('success', 'News created!');
            return $this->redirectToRoute('admin_news_list');
        }

        return $this->render('admin/news/newOrEdit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/news/{id}/edit', name: 'admin_news_edit')]
    public function edit(News $news, Request $request): Response
    {
        $form = $this->newsService->createForm($news, true);
        $isValid =  $this->newsService->validateRequest($request, $form);

        if ($isValid) {
            $this->addFlash('success', 'News updated successfully.');
            return $this->redirectToRoute('admin_news_list');
        }

        return $this->render('admin/news/newOrEdit.html.twig', [
            'form' => $form->createView(),
            'news' => $news,
        ]);
    }

    #[Route('/admin/news/{id}/comments', name: 'admin_news_comments')]
    public function commentsByNews(News $news): Response
    {
        return $this->render('admin/comment/list.html.twig', [
            'news' => $news,
            'comments' => $news->getComments(),
        ]);
    }

    #[Route('/admin/news/{id}/delete', name: 'admin_news_delete', methods: ['POST'])]
    public function deleteNews(Request $request, News $news): Response
    {
        if ($this->newsService->deleteNews($news, $request->request->get('_token'))) {
            $this->addFlash('success', 'News deleted successfully.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('admin_news_list');
    }
}
