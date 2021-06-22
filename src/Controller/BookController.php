<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Kind;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/book")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/", name="book_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="book_show", methods={"GET"})
     */
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/year/{year}", name="book_year", methods={"GET"}, requirements={"year"="\d+"})
     * @param string $year
     */
    public function year(string $year): Response
    {
        $books = $this->getDoctrine()->getRepository(Book::class)->findBy([
           'release_date' => $year
        ]);

        return $this->render('book/year.html.twig', [
            'year' => $year,
            'books' => $books,
        ]);
    }
}
