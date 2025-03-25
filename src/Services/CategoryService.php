<?php

// src/Services/CategoryService.php

namespace App\Services;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

readonly class CategoryService
{
    public function __construct(
        private CategoryRepository $categoryRepo,
        private NewsRepository $newsRepo,
        private EntityManagerInterface $em,
        private FormFactoryInterface $formFactory,
        private PaginatorInterface $paginator,
    ) {}

    public function findAll(): array
    {
        return $this->categoryRepo->findAll();
    }

    public function getCategoryNewsPaginated(Category $category, Request $request)
    {
        $query = $this->newsRepo->createQueryBuilder('n')
            ->join('n.categories', 'c')
            ->where('c.id = :categoryId')
            ->setParameter('categoryId', $category->getId())
            ->orderBy('n.insertDate', 'DESC')
            ->getQuery();

        return $this->paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
    }

    public function createForm(?Category $category = null): FormInterface
    {
        return $this->formFactory->create(CategoryType::class, $category ?? new Category());
    }

    public function validateAndSave(Request $request, FormInterface $form): bool
    {
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return false;
        }

        $this->em->persist($form->getData());
        $this->em->flush();

        return true;
    }

    public function delete(Category $category): void
    {
        $this->em->remove($category);
        $this->em->flush();
    }
}

