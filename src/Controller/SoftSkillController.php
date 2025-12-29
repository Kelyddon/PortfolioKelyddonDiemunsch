<?php

namespace App\Controller;

use App\Entity\SoftSkill;
use App\Form\SoftSkillType;
use App\Repository\SoftSkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/soft/skill')]
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/soft-skills')]
final class SoftSkillController extends AbstractController
{
    #[Route(name: 'app_soft_skill_index', methods: ['GET'])]
    public function index(SoftSkillRepository $softSkillRepository): Response
    {
        return $this->render('soft_skill/index.html.twig', [
            'soft_skills' => $softSkillRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_soft_skill_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $softSkill = new SoftSkill();
        $form = $this->createForm(SoftSkillType::class, $softSkill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($softSkill);
            $entityManager->flush();

            return $this->redirectToRoute('app_soft_skill_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('soft_skill/new.html.twig', [
            'soft_skill' => $softSkill,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_soft_skill_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SoftSkill $softSkill, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SoftSkillType::class, $softSkill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_soft_skill_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('soft_skill/edit.html.twig', [
            'soft_skill' => $softSkill,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_soft_skill_delete', methods: ['POST'])]
    public function delete(Request $request, SoftSkill $softSkill, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$softSkill->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($softSkill);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_soft_skill_index', [], Response::HTTP_SEE_OTHER);
    }
}
