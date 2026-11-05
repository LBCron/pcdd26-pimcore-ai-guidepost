<?php

declare(strict_types=1);

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Renders documents of the "Landing page" document type: a hero, three
 * feature sections and a call to action, all as editables.
 */
class LandingController extends FrontendController
{
    public function landingAction(Request $request): Response
    {
        return $this->render('landing/landing.html.twig');
    }
}
