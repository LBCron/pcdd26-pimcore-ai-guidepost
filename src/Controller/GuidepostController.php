<?php

declare(strict_types=1);

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Renders the Pimcore AI Guidepost page (the repo's AGENTS.md as a CMS page).
 * The body content lives in wysiwyg editables on the Document, so a rich text
 * editor integration can edit exactly this content.
 */
class GuidepostController extends FrontendController
{
    public function guidepostAction(Request $request): Response
    {
        return $this->render('guidepost/guidepost.html.twig');
    }
}
