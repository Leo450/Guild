<?php

namespace App\Controller;

use App\Service\History;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/")
 */
class FrontController extends Controller
{
    /**
     * @Route("", name="front_index")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_MEMBER')")
     */
    public function index(History $history)
    {

	    $history->clear();

        return $this->render('front/index.html.twig', [
        	'user' => $this->getUser()
        ]);

    }
}
