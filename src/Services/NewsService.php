<?php

// src/Services/NewsService.php

namespace App\Services;

use App\Entity\Comment;
use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

readonly class NewsService
{
    public function __construct(
        private NewsRepository $repo,
        private FormFactoryInterface $formFactory,
        private EntityManagerInterface $em,
        private ParameterBagInterface $params,
        private CsrfTokenManagerInterface $csrfTokenManager
    ) {}

    public function findAllOrdered(): array
    {
        return $this->repo->findBy([], ['insertDate' => 'DESC']);
    }

    public function createForm(?News $news = null, bool $isEdit = false): FormInterface
    {
        return $this->formFactory->create(NewsType::class, $news ?? new News(), ['is_edit' => $isEdit]);
    }

    public function handleForm(FormInterface $form): bool
    {
        $news = $form->getData();
        $uploadPath = $this->params->get('kernel.project_dir') . '/public/uploads';

        if (!$form->isSubmitted() || !$form->isValid()) {
            return false;
        }

        /** @var UploadedFile|null $file */
        $file = $form->get('pictureFile')->getData();

        if ($file) {
            // Remove old image
            if ($news->getPicture()) {
                $oldPath = $uploadPath . $news->getPicture();
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $filename = uniqid() . '.' . $file->guessExtension();
            $file->move($uploadPath . '/news', $filename);
            $news->setPicture($filename);
        }

        $this->em->persist($news);
        $this->em->flush();

        return true;
    }

    public function deleteNews(News $news, string $csrfToken): bool
    {
        if (!$this->isValidCsrfToken('delete' . $news->getId(), $csrfToken)) {
            return false;
        }

        $uploadPath = $this->params->get('kernel.project_dir');

        if ($news->getPicture()) {
            $path = $uploadPath . '/news/' . $news->getPicture();
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $this->em->remove($news);
        $this->em->flush();

        return true;
    }

    private function isValidCsrfToken(string $id, string $token): bool
    {
        return $this->csrfTokenManager->isTokenValid(new CsrfToken($id, $token));
    }

    public function validateRequest(Request $request, FormInterface $form): bool
    {
        $form->handleRequest($request);

        if ($this->handleForm($form)) {
            return true;
        }
        return false;
    }

}
