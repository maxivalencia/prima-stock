<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GestionStocksController extends AbstractController
{
    /**
     * @Route("/gestion/stocks", name="gestion_stocks")
     */
    public function index()
    {
        return $this->render('gestion_stocks/index.html.twig', [
            'controller_name' => 'GestionStocksController',
        ]);
    }
}
