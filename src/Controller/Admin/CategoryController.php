<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Services\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    public function __construct(private readonly CategoryService $categoryService) {}

    #[Route('/admin/categories', name: 'admin_category_list')]
    public function list(): Response
    {
        return $this->render('admin/category/list.html.twig', [
            'categories' => $this->categoryService->findAll(),
        ]);
    }

    #[Route('/admin/category/new', name: 'admin_category_new')]
    public function new(Request $request): Response
    {
        $form = $this->categoryService->createForm();

        if ($this->categoryService->validateAndSave($request, $form)) {
            $this->addFlash('success', 'Category created!');
            return $this->redirectToRoute('admin_category_list');
        }

        return $this->render('admin/category/newOrEdit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/category/{id}/edit', name: 'admin_category_edit')]
    public function edit(Category $category, Request $request): Response
    {
        $form = $this->categoryService->createForm($category);

        if ($this->categoryService->validateAndSave($request, $form)) {
            $this->addFlash('success', 'Category updated!');
            return $this->redirectToRoute('admin_category_list');
        }

        return $this->render('admin/category/newOrEdit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    #[Route('/admin/category/{id}/delete', name: 'admin_category_delete')]
    public function delete(Category $category): Response
    {
        $this->categoryService->delete($category);

        $this->addFlash('danger', 'Category deleted!');
        return $this->redirectToRoute('admin_category_list');
    }
}
