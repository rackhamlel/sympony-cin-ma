<?php

namespace App\Controller;

use App\Entity\Rates;
use App\Form\RatesType;
use App\Repository\RatesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    #[Route('/accueil2', name: 'app_home')]
    public function index(Request $term):  Response
    {
        
        $term = $_POST["term"];
        
        $themoviedbApiKey = $_ENV['TMDB_API_KEY'];
        // Création du endpoint de l'API (film recherché + clé API)
        $endPoint = 'https://api.themoviedb.org/3/search/movie?api_key=' . 
            $themoviedbApiKey . "&query=" . $term . "&language=fr-FR&page=1"; 
           
        // Lancement d'une requête HTTP
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endPoint);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
        $resultat_curl = curl_exec($ch);
        // On transforme le résultat de cURL en un objet JSON utilisable
        $json = json_decode ( $resultat_curl );
        
        
        return $this->render('movie/index.html.twig', [
            'movieList' => $json->results
            
            
             
        ]);
    }
    #[Route('/accueil', name: 'app_home2')]
    public function index2(): Response
    {
        return $this->render('movie/index.html.twig',
         [
            'movieList' => $this->search('sddgfvqggqfgrtrggrt')
            
        ]);
    }

    public function search ( string $term ): array
    {
        


        $themoviedbApiKey = $_ENV['TMDB_API_KEY'];
        // Création du endpoint de l'API (film recherché + clé API)
        $endPoint = 'https://api.themoviedb.org/3/search/movie?api_key=' . 
            $themoviedbApiKey . "&query=" . $term . "&language=fr-FR&page=1";      
        // Lancement d'une requête HTTP
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endPoint);
        curl_setopt($ch,  CURLOPT_RETURNTRANSFER, true);
        $resultat_curl = curl_exec($ch);
        // On transforme le résultat de cURL en un objet JSON utilisable
        $json = json_decode ( $resultat_curl );

        
        return $json->results;
    }


    #[Route('/fiche/{id}', name: 'fiche')]
    public function fiche(int $id): Response
    {
        $movieDetails = $this->getMovieDetails($id);
        $rating = new Rates();

        $rating-> setIdMovie($id);

        $ratingForm = $this->createForm(RatesType::class, $rating);


        return $this->render('movie/fiche.html.twig', [
            'movieDetails' => $movieDetails,

            'ratingForm' => $ratingForm->createView()
        ]);
    }/*
    public function show( int $id, RatesRepository $ratesRepository): Response
    {
        $movie = $this->searchById($id);

        $rates = $ratesRepository->findBy(['idMovie' => $id]);

        dd($rates);
    }*/
    
    private function getMovieDetails(int $id): array
    {
        $themoviedbApiKey = $_ENV['TMDB_API_KEY'];
            $endPoint = 'https://api.themoviedb.org/3/movie/' . $id . '?api_key=' . $themoviedbApiKey . '&language=fr-FR';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endPoint);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $resultat_curl = curl_exec($ch);
            $json = json_decode($resultat_curl, true);

            return [
                'title' => $json['title'],
                'poster_path' => $json['poster_path'],
                'release_date' => $json['release_date'],
                'original_language' => $json['original_language'],
                'vote_average' => $json['vote_average'],
                'vote_count' => $json['vote_count'],
                'overview' => $json['overview'],
                
            ];
    }


    
    
}

