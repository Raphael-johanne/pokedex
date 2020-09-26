<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use App\Controller\AbstractController;

class TypeController extends AbstractController
{
    /**
     * Get Types
     * 
     * @return void
     * 
     * @Get(
     *     path = "/api/pokedex/types"
     * )
     */
    public function getTypesAction()
    {
        $items  = $this->getTypes();
        $view   = $this->view($this->getSerialize($items), 200);

        return $this->handleView($view);
    }  
}
