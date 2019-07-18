<?php

namespace Stfalcon\Bundle\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Stfalcon\Bundle\BlogBundle\Entity\Post;

/**
 * PostController
 *
 * @author Stepan Tanasiychuk <ceo@stfalcon.com>
 */
class PostController extends AbstractController
{

    /**
     * List of posts for admin
     *
     * @Route("/blog/{title}/{page}", name="blog",
     *      requirements={"page"="\d+", "title"="page"},
     *      defaults={"page"="1", "title"="page"})
     * @Template()
     *
     * @param int $page Page number
     *
     * @return array
     */
    public function indexAction(int $page): array
    {
        $allPosts = $this->get('doctrine')->getEntityManager()
                ->getRepository("StfalconBlogBundle:Post")->getAllPosts();
        $posts= $this->get('knp_paginator')->paginate($allPosts, $page, 10);

        if ($this->has('application_default.menu.breadcrumbs')) {
            $breadcrumbs = $this->get('application_default.menu.breadcrumbs');
            $breadcrumbs->addChild('Блог')->setCurrent(true);
        }

        return $this->_getRequestDataWithDisqusShortname([
            'posts' => $posts
        ]);
    }

    /**
     * View post
     *
     * @Route("/blog/post/{slug}", name="blog_post_view")
     * @Template()
     *
     * @param Post $post
     *
     * @return array
     */
    public function viewAction(Post $post): array
    {
        if ($this->has('application_default.menu.breadcrumbs')) {
            $breadcrumbs = $this->get('application_default.menu.breadcrumbs');
            $breadcrumbs->addChild('Блог', array('route' => 'blog'));
            $breadcrumbs->addChild($post->getTitle())->setCurrent(true);
        }

        return $this->_getRequestDataWithDisqusShortname([
            'post' => $post
        ]);
    }

    /**
     * RSS feed
     *
     * @Route("/blog/rss", name="blog_rss")
     *
     * @return Response
     */
    public function rssAction()
    {
        $feed = new \Zend\Feed\Writer\Feed();

        $config = $this->container->getParameter('stfalcon_blog.config');

        $feed->setTitle($config['rss']['title']);
        $feed->setDescription($config['rss']['description']);
        $feed->setLink($this->generateUrl('blog_rss', [], true));

        $posts = $this->get('doctrine')->getEntityManager()
                ->getRepository("StfalconBlogBundle:Post")->getAllPosts();
        foreach ($posts as $post) {
            $entry = new \Zend\Feed\Writer\Entry();
            $entry->setTitle($post->getTitle());
            $entry->setLink($this->generateUrl('blog_post_view', ['slug' => $post->getSlug()], true));

            $feed->addEntry($entry);
        }

        return new Response($feed->export('rss'));
    }

    /**
     * Show last blog posts
     *
     * @Template()
     *
     * @param int $count A count of posts
     *
     * @return array()
     */
    public function lastAction(int $count = 1): array
    {
        $posts = $this->get('doctrine')->getEntityManager()
                ->getRepository("StfalconBlogBundle:Post")->getLastPosts($count);

        return ['posts' => $posts];
    }
}
