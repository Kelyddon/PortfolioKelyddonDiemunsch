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
    // =====================
    // Liste des projets
    // =====================
    #[Route(name: 'app_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    // =====================
    // Création d'un projet
    // =====================
    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, HardSkillRepository $hardRepo): Response
    {
        $project = new Project();
        // Récupérer la liste des langages depuis HardSkill
        $choices = [];
        foreach ($hardRepo->findAll() as $hs) {
            $val = $hs->getLanguage();
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

            return $this->redirectToRoute('app_projects_public', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    // =====================
    // Édition d'un projet
    // =====================
    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager, HardSkillRepository $hardRepo): Response
    {
        // Préparer les choix de langages pour les cases à cocher
        $choices = [];
        foreach ($hardRepo->findAll() as $hs) {
            $val = $hs->getLanguage();
            if ($val !== null && $val !== '') {
                $choices[$val] = $val;
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

                // delete previous image file if exists
                $oldImage = $project->getImage();
                if ($oldImage) {
                    $oldPath = $this->getParameter('kernel.project_dir') . '/public/' . ltrim($oldImage, '/');
                    if (file_exists($oldPath)) {
                        try {
                            $fs->remove($oldPath);
                        } catch (\Exception $e) {
                            // ignore deletion errors
                        }
                    }
                }

                $ext = $uploaded->guessExtension() ?: 'bin';
                $filename = 'project_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $uploaded->move($uploadDir, $filename);
                if (method_exists($project, 'setImage')) {
                    $project->setImage('upload/' . $filename);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_projects_public', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    // =====================
    // Suppression d'un projet
    // =====================
    #[Route('/{id}', name: 'app_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->getPayload()->getString('_token'))) {
            // remove associated image file if present
            $fs = new Filesystem();
            $img = $project->getImage();
            if ($img) {
                $path = $this->getParameter('kernel.project_dir') . '/public/' . ltrim($img, '/');
                if (file_exists($path)) {
                    try {
                        $fs->remove($path);
                    } catch (\Exception $e) {
                        // ignore deletion errors
                    }
                }
            }

            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_projects_public', [], Response::HTTP_SEE_OTHER);
    }
}
