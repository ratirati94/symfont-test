<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Services\CommentsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommentController extends AbstractController{
    public function __construct(private readonly CommentsService $commentsService)
    {}

    #[Route('/admin/comment', name: 'app_admin_comment')]
    public function index(): Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'controller_name' => 'Admin/CommentController',
        ]);
    }

    #[Route('/admin/comment/{id}/delete', name: 'admin_comment_delete', methods: ['POST'])]
    public function deleteComment(Comment $comment, Request $request): Response
    {
        if (!$this->commentsService->deleteComment($comment, $request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        return $this->redirectToRoute('admin_news_comments', [
            'id' => $comment->getNews()->getId()
        ]);
    }
}
