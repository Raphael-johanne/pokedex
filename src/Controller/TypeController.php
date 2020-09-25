<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use App\Repository\TypeRepository;
use App\Controller\AbstractController;

class TypeController extends AbstractController
{
    /**
     * Get Types
     * 
     * @param  TypeRepository $repository
     * 
     * @return void
     * 
     * @Get(
     *     path = "/api/pokedex/types"
     * )
     */
    public function getTypesAction(TypeRepository $repository)
    {
        $items  = $repository->findAll();
        $view   = $this->view($this->getSerialize($items), 200);

        return $this->handleView($view);
    }  
}
