<?php

namespace App\Services;

use App\Entity\Comment;
use App\Entity\News;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

readonly class CommentsService
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EntityManagerInterface $em,
        private CsrfTokenManagerInterface $csrfTokenManager
    )
    {

    }

    private function isValidCsrfToken(string $id, string $token): bool
    {
        return $this->csrfTokenManager->isTokenValid(new CsrfToken($id, $token));
    }

    public function createCommentForm(): FormInterface
    {
        $comment = new Comment();
        return $this->formFactory->create(CommentType::class, $comment);
    }

    public function handleForm(Request $request, News $news): bool
    {
        $comment = new Comment();
        $form = $this->formFactory->create(CommentType::class, $comment);
        $form->handleRequest($request);

        $success = false;
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setNews($news);
            $this->em->persist($comment);
            $this->em->flush();
            $success = true;
        }

        return $success;
    }

    public function deleteComment(Comment $comment, string $csrfToken): bool
    {
        if (!$this->isValidCsrfToken('delete' . $comment->getId(), $csrfToken)) {
            return false;
        }

        $this->em->remove($comment);
        $this->em->flush();

        return true;
    }
}
