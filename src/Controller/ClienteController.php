<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Cliente;
use App\Entity\Incidencia;
use App\Form\FormularioClienteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ClienteController extends AbstractController
{   
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/clientes/{id}', name: 'verCliente')]
    public function verCliente(Cliente $cliente): Response
    {
        $incidencias=$cliente->getIncidencias();
        return $this->render('cliente/verCliente.html.twig', [
            'cliente' => $cliente,
            'incidencia'=>$incidencias,
            
        ]);
    }
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/', name: 'verTodosClientes')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $clientes = $entityManager->getRepository(Cliente::class)->findAll();

        return $this->render('cliente/index.html.twig', [
            'clientes' => $clientes
        ]);
    }
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/cliente/add/', name: 'addCliente')]
    public function addCliente(EntityManagerInterface $entityManager, Request $request): Response
    {
        
        $cliente =new Cliente();
        
        $formularioCliente = $this->createForm(FormularioClienteType::class, $cliente);

        $formularioCliente->handleRequest($request);
        if ($formularioCliente->isSubmitted() && $formularioCliente->isValid()) {
            
            $cliente = $formularioCliente->getData();
   
            $entityManager->persist($cliente);
            $entityManager->flush();
            $this->addFlash('success', 'Se ha añdido el cliente');   
            return $this->redirectToRoute('verTodosClientes');
           }

           $this->addFlash('success', 'No se ha añdido el cliente');
        return $this->render('cliente/addCliente.html.twig', ['formularioCliente'=>$formularioCliente]);

        
    }
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the admin dashboard.')]
    #[Route('/cliente/delete/{id}', name: 'deleteCliente')]
    public function deleteCliente(Cliente $cliente, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($cliente);
        $entityManager->flush();
        $this->addFlash('success', 'Se ha borrado el cliente');
        return $this->redirectToRoute('verTodosClientes');
        
    }
}
