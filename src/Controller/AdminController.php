<?php

namespace App\Controller;

use App\Entity\HardSkill;
use App\Entity\SoftSkill;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin/about/text', name: 'admin_about_text', methods: ['POST'])]
    public function updateAboutText(Request $req, EntityManagerInterface $em): JsonResponse
    {
        $text = trim((string) $req->request->get('text'));
        // TODO: persister dans votre entité (Profile/Setting). Ici, on renvoie juste OK.
        return new JsonResponse(['status' => 'ok', 'text' => $text]);
    }

    #[Route('/admin/photo/upload', name: 'admin_photo_upload', methods: ['POST'])]
    public function uploadPhoto(Request $req): JsonResponse
    {
        /** @var UploadedFile|null $file */
        $file = $req->files->get('photo');
        if (!$file) { return new JsonResponse(['status' => 'error', 'msg' => 'no file'], 400); }

        $name = 'profile_'.time().'.'.$file->guessExtension();
        $file->move($this->getParameter('kernel.project_dir').'/public/uploads', $name);

        return new JsonResponse(['status' => 'ok', 'path' => 'uploads/'.$name]);
    }

    #[Route('/admin/skill/add/{type}', name: 'admin_skill_add', methods: ['POST'])]
    public function addSkill(string $type, Request $req, EntityManagerInterface $em): JsonResponse
    {
        $value = trim((string) $req->request->get('value'));
        if ($type === 'hard') {
            $s = new HardSkill(); $s->setLanguage($value);
        } else {
            $s = new SoftSkill(); method_exists($s, 'setName') ? $s->setName($value) : $s->setLabel($value);
        }
        $em->persist($s); $em->flush();
        return new JsonResponse(['status' => 'ok', 'id' => $s->getId(), 'value' => $value]);
    }

    #[Route('/admin/skill/update/{type}/{id}', name: 'admin_skill_update', methods: ['POST'])]
    public function updateSkill(string $type, int $id, Request $req, EntityManagerInterface $em): JsonResponse
    {
        $value = trim((string) $req->request->get('value'));
        $repo = $type === 'hard' ? $em->getRepository(HardSkill::class) : $em->getRepository(SoftSkill::class);
        $s = $repo->find($id);
        if (!$s) { return new JsonResponse(['status' => 'error', 'msg' => 'not found'], 404); }
        $type === 'hard' ? $s->setLanguage($value) : (method_exists($s, 'setName') ? $s->setName($value) : $s->setLabel($value));
        $em->flush();
        return new JsonResponse(['status' => 'ok']);
    }

    #[Route('/admin/skill/delete/{type}/{id}', name: 'admin_skill_delete', methods: ['DELETE'])]
    public function deleteSkill(string $type, int $id, EntityManagerInterface $em): JsonResponse
    {
        $repo = $type === 'hard' ? $em->getRepository(HardSkill::class) : $em->getRepository(SoftSkill::class);
        $s = $repo->find($id);
        if (!$s) { return new JsonResponse(['status' => 'error', 'msg' => 'not found'], 404); }
        $em->remove($s); $em->flush();
        return new JsonResponse(['status' => 'ok']);
    }
}