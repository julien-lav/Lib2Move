<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\FileUploader;
// use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="user_profile", methods={"GET"})
     */
    public function profile(): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        return $this->render('profile/profile.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit", name="user_edit_profile", methods={"GET","POST"})
     */
    public function edit(Request $request, FileUploader $fileUploader): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getImage()) {
                $file = $user->getImage();
    	        $fileName = $fileUploader->upload($file, 'user_profile');
    	        $user->setImage($fileName);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}