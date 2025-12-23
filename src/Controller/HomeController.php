<?php
namespace App\Controller;

use App\Entity\HardSkill;
use App\Entity\SoftSkill;
use App\Entity\PresentationText;
use App\Form\ContactType;
use App\Repository\HardSkillRepository;
use App\Repository\ProjectRepository;
use App\Repository\SoftSkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET','POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        ProjectRepository $projectsRepo,
        HardSkillRepository $hardSkillsRepo,
        SoftSkillRepository $softSkillsRepo
    ): Response {
        // Ajout rapide de skills par formulaire (admin)
        if ($request->isMethod('POST') && $this->isGranted('ROLE_ADMIN')) {
            $hard = trim((string) $request->request->get('new_hard_skill', ''));
            $soft = trim((string) $request->request->get('new_soft_skill', ''));

            if ($hard !== '') {
                $h = new HardSkill();
                $this->setHardSkillValue($h, $hard);
                $em->persist($h);
                $em->flush();
                return $this->redirectToRoute('app_home');
            }
            if ($soft !== '') {
                $s = new SoftSkill();
                if (method_exists($s, 'setSkill')) {
                    $s->setSkill($soft);
                } else {
                    throw new \LogicException('SoftSkill doit avoir setSkill(string).');
                }
                $em->persist($s);
                $em->flush();
                return $this->redirectToRoute('app_home');
            }
        }

        $projects   = $projectsRepo->findBy([], ['createAt' => 'DESC'], 3);
        $hardSkills = $hardSkillsRepo->findAll();
        $softSkills = $softSkillsRepo->findAll();

        // Charger le texte "À propos" depuis PresentationText.slug = 'about'
        $aboutRow  = $em->getRepository(PresentationText::class)->findOneBy(['slug' => 'about']);
        $aboutText = $aboutRow?->getContent();

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var \App\Entity\ContactMessage $msg */
            $msg = $form->getData();
            if (method_exists($msg, 'setCreatedAt')) {
                $msg->setCreatedAt(new \DateTimeImmutable());
            }
            $em->persist($msg);
            $em->flush();

            $this->addFlash('success', 'Votre message a bien été envoyé. Merci !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('base.html.twig', [
            'partial'     => 'home/_home.html.twig',
            'projects'    => $projects,
            'hardSkills'  => $hardSkills,
            'softSkills'  => $softSkills,
            'aboutText'   => $aboutText,
            'contactForm' => $form->createView(),
        ]);
    }

    // Confirmer les modifications (update/suppression si vide + texte à propos)
    #[Route('/admin/skills/save', name: 'skills_save', methods: ['POST'])]
    public function saveSkills(
        Request $request,
        EntityManagerInterface $em,
        HardSkillRepository $hardRepo,
        SoftSkillRepository $softRepo
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $data = json_decode($request->getContent(), true) ?? [];
        if (!is_array($data)) {
            return new JsonResponse(['ok' => false, 'error' => 'bad_request'], 400);
        }

        // Updates
        foreach (($data['hardUpdates'] ?? []) as $u) {
            $e = isset($u['id']) ? $hardRepo->find((int) $u['id']) : null;
            if ($e && isset($u['value'])) {
                $this->setHardSkillValue($e, (string) $u['value']);
            }
        }
        foreach (($data['softUpdates'] ?? []) as $u) {
            $e = isset($u['id']) ? $softRepo->find((int) $u['id']) : null;
            if ($e && isset($u['value']) && method_exists($e, 'setSkill')) {
                $e->setSkill((string) $u['value']);
            }
        }

        // Deletes si texte vide
        foreach (($data['hardDeletes'] ?? []) as $id) {
            if ($obj = $hardRepo->find((int) $id)) {
                $em->remove($obj);
            }
        }
        foreach (($data['softDeletes'] ?? []) as $id) {
            if ($obj = $softRepo->find((int) $id)) {
                $em->remove($obj);
            }
        }

        // About text
        if (array_key_exists('aboutText', $data)) {
            $about = $em->getRepository(PresentationText::class)->findOneBy(['slug' => 'about']);
            if (!$about) {
                $about = (new PresentationText())->setSlug('about');
            }
            $about->setContent($data['aboutText'] !== null ? (string)$data['aboutText'] : null);
            $em->persist($about);
        }

        $em->flush();
        return new JsonResponse(['ok' => true]);
    }

    // Upload du CV (remplace public/cv/CV.pdf)
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

        $targetDir = $kernel->getProjectDir() . '/public/cv';
        (new Filesystem())->mkdir($targetDir);
        $file->move($targetDir, 'CV.pdf');

        $this->addFlash('success', 'CV mis à jour.');
        return $this->redirectToRoute('app_home');
    }

    

    // Setter générique HardSkill (ex: language)
    private function setHardSkillValue(HardSkill $entity, string $value): void
    {
        foreach (['setLanguage','setName','setLabel','setTitle','setSkill','setValue'] as $m) {
            if (method_exists($entity, $m)) {
                $entity->$m($value);
                return;
            }
        }
        throw new \LogicException('HardSkill: ajoute un champ texte (ex: language) avec son setter.');
    }
}