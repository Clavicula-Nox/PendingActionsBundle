<?php

namespace CNPendingActionsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CNPendingActionsBundle:Default:index.html.twig');
    }
}
