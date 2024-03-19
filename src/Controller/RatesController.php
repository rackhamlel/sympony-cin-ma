<?php

namespace App\Controller;

use App\Entity\Rates;
use App\Form\RatesType;
use App\Repository\RatesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rates')]
class RatesController extends AbstractController
{
    #[Route('/', name: 'app_rates_index', methods: ['GET'])]
    public function index(RatesRepository $ratesRepository): Response
    {
        return $this->render('rates/index.html.twig', [
            'rates' => $ratesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_rates_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rate = new Rates();
        $form = $this->createForm(RatesType::class, $rate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rate);
            $entityManager->flush();

            return $this->redirectToRoute('app_rates_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rates/new.html.twig', [
            'rate' => $rate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rates_show', methods: ['GET'])]
    public function show(Rates $rate): Response
    {
        return $this->render('rates/show.html.twig', [
            'rate' => $rate,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rates_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rates $rate, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RatesType::class, $rate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rates_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rates/edit.html.twig', [
            'rate' => $rate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rates_delete', methods: ['POST'])]
    public function delete(Request $request, Rates $rate, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rate->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rates_index', [], Response::HTTP_SEE_OTHER);
    }
}
