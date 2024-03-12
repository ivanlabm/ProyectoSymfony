<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/registro', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $usuario = new Usuario();
        $usuario->setRoles(['ROLE_USER']);
        $form = $this->createForm(RegistrationFormType::class, $usuario);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $usuario->setPassword(
                $userPasswordHasher->hashPassword(
                    $usuario,
                    $form->get('plainPassword')->getData()
                )
            );
            /** @var UploadedFile $fotoFile */
            $fotoFile = $form['foto']->getData();
            if ($fotoFile) {
                $newFilename = md5(uniqid()) . '.' . $fotoFile->guessExtension();

                
                $fotoFile->move(
                    "imagenes/",
                    $newFilename
                );
                $usuario->setFoto($newFilename);
            }

            $entityManager->persist($usuario);
            $entityManager->flush();
            $this->addFlash('success', 'Se ha completado el registro');

            return $this->redirectToRoute('app_login');
        }
        $this->addFlash('success', 'Halgo ha ido mal');
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
