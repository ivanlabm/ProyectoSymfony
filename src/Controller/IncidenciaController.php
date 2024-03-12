<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Incidencia;
use App\Entity\Cliente;
use App\Form\FormularioIncidenciaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;


class IncidenciaController extends AbstractController
{
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/incidencia', name: 'incidencias')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $incidencias = $entityManager->getRepository(Incidencia::class)->findAll();
        return $this->render('incidencias/index.html.twig', [
            'incidencias' => $incidencias,
        ]);
    }

    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/incidencia/add/{cliente_id}', name: 'addIncidencia')]
    public function addCliente(EntityManagerInterface $entityManager, Request $request,int $cliente_id): Response
    {
     
        $usuario = $this->getUser();


        $incidencia =new Incidencia();
        $cliente = $entityManager->getRepository(Cliente::class)->find($cliente_id);
        $formularioIncidencia = $this->createForm(FormularioIncidenciaType::class, $incidencia);

        $formularioIncidencia->handleRequest($request);
        if ($formularioIncidencia->isSubmitted() && $formularioIncidencia->isValid()) {
            
            $incidencia = $formularioIncidencia->getData();
            $incidencia->setUsuario($usuario);
            $incidencia->setCliente($cliente);
            $fechaActual = new \DateTime();
            $incidencia->setFechaCreacion($fechaActual);
            $entityManager->persist($incidencia);
            $entityManager->flush();
            $this->addFlash('success', 'No se ha añdido la incidencia');
            return $this->redirectToRoute('incidencias');
           }


        return $this->render('incidencias/addIncidencia.html.twig', ['formularioIncidencia'=>$formularioIncidencia]);

        
    }
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/incidencia/delete/{id}', name: 'deleteIncidencia')]
    public function deleteincidencia(Incidencia $incidencia, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($incidencia);
        $entityManager->flush();
        $this->addFlash('success', 'No se ha añdido la incidencia');
        return $this->redirectToRoute('incidencias');
        
    }
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/incidencias/{id}', name: 'verIncidencia')]
    public function verCliente(Incidencia $incidencia): Response
    {
       
        return $this->render('incidencias/verIncidencias.html.twig', [
            'incidencia' => $incidencia,
            
            
        ]);


    }



    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/incidencia/editar/{id}', name: 'editarIncidencia')]
    public function editarCliente(Incidencia $incidencia,EntityManagerInterface $entityManager, Request $request): Response
    {
        $cliente=$incidencia->getCliente();
        $usuario = $this->getUser();

       
        $formularioIncidencia = $this->createForm(FormularioIncidenciaType::class, $incidencia);

        $formularioIncidencia->handleRequest($request);
        if ($formularioIncidencia->isSubmitted() && $formularioIncidencia->isValid()) {
            
            $incidencia = $formularioIncidencia->getData();
            $incidencia->setUsuario($usuario);
            $incidencia->setCliente($cliente);
            $fechaActual = new \DateTime();
            $incidencia->setFechaCreacion($fechaActual);
            $entityManager->persist($incidencia);
            $entityManager->flush();
            $this->addFlash('success', 'Se ha editado la incidencia');
            return $this->redirectToRoute('incidencias');
           }


        return $this->render('incidencias/addIncidencia.html.twig', [
        'formularioIncidencia'=>$formularioIncidencia->createView(),
        ]);
    }

 
}
