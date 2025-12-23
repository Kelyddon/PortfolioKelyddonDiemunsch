<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\HardSkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/project')]
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/projets')]
final class ProjectController extends AbstractController
{
    #[Route(name: 'app_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, HardSkillRepository $hardRepo): Response
    {
        $project = new Project();
        // Récupérer la liste des langages depuis HardSkill
        $choices = [];
        foreach ($hardRepo->findAll() as $hs) {
            $val = null;
            if (method_exists($hs, 'getLanguage')) {
                $val = $hs->getLanguage();
            } elseif (method_exists($hs, 'getName')) {
                $val = $hs->getName();
            } elseif (method_exists($hs, 'getLabel')) {
                $val = $hs->getLabel();
            }
            if ($val !== null && $val !== '') {
                $choices[$val] = $val; // label => value
            }
        }

        $form = $this->createForm(ProjectType::class, $project, [
            'langage_choices' => $choices,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $uploaded */
            $uploaded = $form->get('image')->getData();
            if ($uploaded instanceof UploadedFile) {
                $fs = new Filesystem();
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/upload';
                $fs->mkdir($uploadDir);
                $ext = $uploaded->guessExtension() ?: 'bin';
                $filename = 'project_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $uploaded->move($uploadDir, $filename);
                // Stocker un chemin relatif depuis public
                if (method_exists($project, 'setImage')) {
                    $project->setImage('upload/' . $filename);
                }
            }

            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager, HardSkillRepository $hardRepo): Response
    {
        // Préparer les choix de langages pour les cases à cocher
        $choices = [];
        foreach ($hardRepo->findAll() as $hs) {
            $val = null;
            if (method_exists($hs, 'getLanguage')) {
                $val = $hs->getLanguage();
            } elseif (method_exists($hs, 'getName')) {
                $val = $hs->getName();
            } elseif (method_exists($hs, 'getLabel')) {
                $val = $hs->getLabel();
            }
            if ($val !== null && $val !== '') {
                $choices[$val] = $val;
            }
        }

        $form = $this->createForm(ProjectType::class, $project, [
            'langage_choices' => $choices,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }
}
