<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Get;
use App\Repository\PokemonRepository;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Pokemon;
use App\Entity\Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class PokemonController extends AbstractController
{
    /**
     * $parameters array
     */
    protected $parameters = [
        'p',
        'dest',
        'fulltext',
        'desc',
        'type'
    ];

    /**
     * Put Pokemon
     * 
     * @param Request                   $request
     * @param ValidatorInterface        $validator
     * @param EntityManagerInterface    $entityManager
     * 
     * @return void
     * 
     * @Put(
     *     path = "/api/pokedex/pokemon"
     * )
     */
    public function addPokemonAction(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ) {
        try {
            $content    = json_decode($request->getContent());
            $item       = $this->build($content);
            
            $errors     = $validator->validate($item);

            if (count($errors) > 0) {
                $view   = $this->view(
                    sprintf(
                        'Errors occured during Pokemon creation: %s'
                        , $this->getSerialize($errors)
                ), parent::BAD_REQUEST_HTTP_CODE);
            } else {
                $entityManager->persist($item);
                $entityManager->flush();
                $view   = $this->view(
                    sprintf(
                        'The Pokemon %s has been created'
                        , $item->getName()
                ), parent::SUCCESS_HTTP_CODE);
            }
    
            return $this->handleView($view);
        } catch (\Exception $e) {
            $response = new Response(sprintf('Critical error during Pokemon creation %s', $this->getSerialize($e->getMessage())));
            $response->setStatusCode(parent::BAD_REQUEST_HTTP_CODE);
            return $response;
        }
    }  

    /**
     * Delete Pokemon
     * 
     * @param Request                   $request
     * @param PokemonRepository         $pokemonRepository
     * @param EntityManagerInterface    $entityManager
     * 
     * @return void
     * 
     * @Delete(
     *     path = "/api/pokedex/pokemon"
     * )
     */
    public function deletePokemonAction(
        Request $request,
        PokemonRepository $pokemonRepository,
        EntityManagerInterface $entityManager
    ) {
        try {
            $content    = json_decode($request->getContent());
            $item       = $pokemonRepository->findOneByName($content->name);

            if (is_null($item)) {
                throw new \Exception (sprintf(
                    'The pokemon %s does not exist', 
                    $content->name
                ));
            }

            $entityManager->remove($item);
            $entityManager->flush();

            $view   = $this->view(
                sprintf(
                    'The Pokemon %s has been removed'
                    , $content->name
            ), parent::SUCCESS_HTTP_CODE);
    
            return $this->handleView($view);
        } catch (\Exception $e) {
            $response = new Response(sprintf('Critical error during Pokemon deletion %s', $this->getSerialize($e->getMessage())));
            $response->setStatusCode(parent::BAD_REQUEST_HTTP_CODE);
            return $response;
        }
    }
    
    /**
     * Get Pokemon information
     * 
     * @param Request           $request
     * @param PokemonRepository $pokemonRepository
     * 
     * @return void
     * 
     * @Get(
     *     path = "/api/pokedex/pokemon"
     * )
     */
    public function pokemonAction(
        Request $request,
        PokemonRepository $pokemonRepository
    ) {
        try {
            $content    = json_decode($request->getContent());
            $item       = $pokemonRepository->findOneByName($content->name);

            if (is_null($item)) {
                throw new \Exception (sprintf(
                    'The Pokemon %s does not exist', 
                    $content->name
                ));
            }
            
            $view   = $this->view($this->getSerialize($item), parent::SUCCESS_HTTP_CODE);
            return $this->handleView($view);
        } catch (\Exception $e) {
            $response = new Response(sprintf('Critical error during getting Pokemon information: %s', $this->getSerialize($e->getMessage())));
            $response->setStatusCode(parent::BAD_REQUEST_HTTP_CODE);
            return $response;
        }
    }

    /**
     * Get Pokemon's list
     * 
     * @param Request           $request
     * @param PokemonRepository $pokemonRepository
     * 
     * @return void
     * 
     * @Get(
     *     path = "/api/pokedex/pokemon/search"
     * )
     */
    public function pokemonSearchAction(
        Request $request,
        PokemonRepository $pokemonRepository
    ) {
        try {
           $parameters = $request->query->all();

           /**
            * Check get parameters to be comform to the contract
            */
           foreach ($parameters as $key => $parameter) {
               if (!in_array($key, $this->parameters)) {
                throw new \Exception (sprintf(
                    'Parameters are not compatible with the contract', 
                    $this->getSerialize($parameters))
                );
               }
            }

            $items  = $pokemonRepository->search($parameters);
            
            $view   = $this->view($this->getSerialize($items), parent::SUCCESS_HTTP_CODE);
            return $this->handleView($view);
        } catch (\Exception $e) {
            $response = new Response(sprintf('Critical error during getting Pokemons informations: %s', $this->getSerialize($e->getMessage())));
            $response->setStatusCode(parent::BAD_REQUEST_HTTP_CODE);
            return $response;
        }
    }

    /**
     * Update Pokemon
     * 
     * @param Request                   $request
     * @param EntityManagerInterface    $entityManager
     * @param PokemonRepository         $pokemonRepository
     * 
     * @return void
     * 
     * @Patch(
     *     path = "/api/pokedex/pokemon"
     * )
     */
    public function updatePokemonAction(
        Request $request,
        EntityManagerInterface $entityManager,
        PokemonRepository $pokemonRepository
    ) {
        try {
            $content    = json_decode($request->getContent());
            $item       = $pokemonRepository->findOneByName($content->name);

            if (is_null($item)) {
                throw new \Exception (sprintf(
                    'The Pokemon %s does not exist', 
                    $content->name
                ));
            }

            $item = $this->build($content, $item);
     
            $entityManager->persist($item);
            $entityManager->flush();

            $view   = $this->view(
                sprintf(
                    'The Pokemon %s has been updated'
                    , $content->name
            ), parent::SUCCESS_HTTP_CODE);

            return $this->handleView($view);
        } catch (\Exception $e) {
            $response = new Response(sprintf('Critical error during Pokemon update %s', $this->getSerialize($e->getMessage())));
            $response->setStatusCode(parent::BAD_REQUEST_HTTP_CODE);
            return $response;
        }
    } 

    /**
     * Build Pokemon
     * 
     * @param $content
     * @param $item
     * 
     * @return Pokemon
     */
    protected function build($content, $item = null) {
        $date           = new \DateTime();
        $existingTypes  = $this->getTypes();

        if (is_null($item)) {
            $item   = new Pokemon();
        }
        
        $item->setName($content->name);
        $item->setHp($content->hp);
        $item->setAttack($content->attack);
        $item->setDefense($content->defense);
        $item->setSpAttack($content->sp_attack);
        $item->setSpDefense($content->sp_defense);
        $item->setSpeed($content->speed);
        $item->setGeneration($content->generation);
        $item->setLegendary($content->legendary);

        foreach($content->types as $typeInfo) {
            $type = new Type();
            $type->setName($typeInfo->name);
            $type->setDate($date);
            $item->addType($type);
        }

        $item->setDate($date);

        return $item;
    }
}
