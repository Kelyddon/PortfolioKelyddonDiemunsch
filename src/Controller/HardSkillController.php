<?php

namespace App\Controller;

use App\Entity\HardSkill;
use App\Form\HardSkillType;
use App\Repository\HardSkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/hard/skill')]
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/hard-skills')]
final class HardSkillController extends AbstractController
{
    #[Route(name: 'app_hard_skill_index', methods: ['GET'])]
    public function index(HardSkillRepository $hardSkillRepository): Response
    {
        return $this->render('hard_skill/index.html.twig', [
            'hard_skills' => $hardSkillRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_hard_skill_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $hardSkill = new HardSkill();
        $form = $this->createForm(HardSkillType::class, $hardSkill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($hardSkill);
            $entityManager->flush();

            return $this->redirectToRoute('app_hard_skill_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hard_skill/new.html.twig', [
            'hard_skill' => $hardSkill,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_hard_skill_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HardSkill $hardSkill, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HardSkillType::class, $hardSkill);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_hard_skill_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('hard_skill/edit.html.twig', [
            'hard_skill' => $hardSkill,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_hard_skill_delete', methods: ['POST'])]
    public function delete(Request $request, HardSkill $hardSkill, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hardSkill->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($hardSkill);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_hard_skill_index', [], Response::HTTP_SEE_OTHER);
    }
}
