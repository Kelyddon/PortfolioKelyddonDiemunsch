<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\HardSkillRepository;
use App\Repository\SoftSkillRepository;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ContactMessage;
use App\Form\ContactType;
use App\Entity\HardSkill;   // ajouté
use App\Entity\SoftSkill;   // ajouté
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        ProjectRepository $projectsRepo,
        HardSkillRepository $hardSkillsRepo,
        SoftSkillRepository $softSkillsRepo
    ): Response {
        $projects = $projectsRepo->findBy([], ['createAt' => 'DESC'], 3);

        $message = new ContactMessage();
        $form = $this->createForm(ContactType::class, $message);
        $form->handleRequest($request);

        // Ajout skills (admin uniquement)
        if ($request->isMethod('POST') && $this->isGranted('ROLE_ADMIN')) {
            $hard = trim((string) $request->request->get('new_hard_skill', ''));
            $soft = trim((string) $request->request->get('new_soft_skill', ''));

            if ($hard !== '') {
                $entity = new HardSkill();
                $this->setHardSkillValue($entity, $hard);
                $em->persist($entity);
                $em->flush();
                return $this->redirectToRoute('app_home');
            }

            if ($soft !== '') {
                $entity = new SoftSkill();
                $entity->setSkill($soft); // SoftSkill OK
                $em->persist($entity);
                $em->flush();
                return $this->redirectToRoute('app_home');
            }
        }

        $hardSkills = $hardSkillsRepo->findAll();
        $softSkills = $softSkillsRepo->findAll();

        return $this->render('base.html.twig', [
            'partial'     => 'home/_home.html.twig',
            'projects'    => $projects,
            'hardSkills'  => $hardSkills,
            'softSkills'  => $softSkills,
            'contactForm' => $form->createView(),
        ]);
    }
     #[Route('/admin/skills/save', name: 'skills_save', methods: ['POST'])]
    public function saveSkills(
        Request $request,
        EntityManagerInterface $em,
        HardSkillRepository $hardRepo,
        SoftSkillRepository $softRepo
    ): JsonResponse {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['ok' => false, 'error' => 'forbidden'], 403);
        }

        $data = json_decode($request->getContent(), true) ?? [];
        if (!is_array($data)) {
            return new JsonResponse(['ok' => false, 'error' => 'bad_request'], 400);
        }

        // Updates
        foreach ($data['hardUpdates'] ?? [] as $u) {
            $e = isset($u['id']) ? $hardRepo->find((int)$u['id']) : null;
            if ($e && isset($u['value'])) { $e->setLanguage((string)$u['value']); }
        }
        foreach ($data['softUpdates'] ?? [] as $u) {
            $e = isset($u['id']) ? $softRepo->find((int)$u['id']) : null;
            if ($e && isset($u['value'])) { $e->setSkill((string)$u['value']); }
        }

        // Deletes (empty text)
        foreach ($data['hardDeletes'] ?? [] as $id) {
            $e = $hardRepo->find((int)$id);
            if ($e) { $em->remove($e); }
        }
        foreach ($data['softDeletes'] ?? [] as $id) {
            $e = $softRepo->find((int)$id);
            if ($e) { $em->remove($e); }
        }

        $em->flush();
        return new JsonResponse(['ok' => true]);
    }
    #[Route('/admin/cv/upload', name: 'admin_cv_upload', methods: ['POST'])]
public function uploadCv(Request $request, KernelInterface $kernel): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    $file = $request->files->get('cv_file');
    if (!$file) {
        $this->addFlash('error', 'Aucun fichier envoyé.');
        return $this->redirectToRoute('app_home');
    }
    $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: '');
    if ($ext !== 'pdf') {
        $this->addFlash('error', 'Seuls les PDF sont acceptés.');
        return $this->redirectToRoute('app_home');
    }

    $targetDir = $kernel->getProjectDir().'/public/cv';
    (new Filesystem())->mkdir($targetDir);
    $file->move($targetDir, 'CV.pdf');

    $this->addFlash('success', 'CV mis à jour.');
    return $this->redirectToRoute('app_home');
}

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->redirectToRoute('app_home', [], 301);
    }
}