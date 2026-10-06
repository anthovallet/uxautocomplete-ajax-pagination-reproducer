<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemAutocompleteField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReproController extends AbstractController
{
    #[Route('/', name: 'repro')]
    public function __invoke(Request $request, EntityManagerInterface $em): Response
    {
        // Items 1 to 8 are pre-selected: 8 of the 10 results on page 1 will be hidden by TomSelect
        $preselected = $em->getRepository(Item::class)->findBy([], ['id' => 'ASC'], 8);

        $form = $this->createFormBuilder(['items' => $preselected])
            ->add('items', ItemAutocompleteField::class, ['multiple' => true])
            ->getForm();

        $form->handleRequest($request);

        return $this->render('repro.html.twig', ['form' => $form]);
    }
}
