<?php
declare(strict_types=1);

/**
 * Blog demo for the CKEditor Studio bundle: listing and detail pages rendering the
 * BlogPost data objects (content model defined in the database, see
 * App\Command\InstallBlogDemoCommand). PCDD 2026 demo code.
 */

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends FrontendController
{
    #[Route('/blog', name: 'blog_list')]
    public function listAction(): Response
    {
        $posts = new DataObject\BlogPost\Listing();
        $posts->setOrderKey('publishDate');
        $posts->setOrder('DESC');

        return $this->render('blog/list.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/blog/{slug}', name: 'blog_detail')]
    public function detailAction(string $slug): Response
    {
        $list = new DataObject\BlogPost\Listing();
        $list->setCondition('slug = ?', [$slug]);
        $list->setLimit(1);

        $post = $list->current();
        if (!$post instanceof DataObject\BlogPost) {
            throw $this->createNotFoundException('Blog post not found');
        }

        return $this->render('blog/detail.html.twig', [
            'post' => $post,
        ]);
    }
}
