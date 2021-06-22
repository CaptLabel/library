<?php

namespace App\Controller;

use App\Entity\Kind;
use App\Form\KindType;
use App\Repository\KindRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/kind")
 */
class KindController extends AbstractController
{
    /**
     * @Route("/{id}/books", name="kind_show_books", methods={"GET"})
     */
    public function show(Kind $kind): Response
    {
        return $this->render('kind/show.books.html.twig', [
            'kind' => $kind,
        ]);
    }
}
