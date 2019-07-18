<?php

namespace Stfalcon\Bundle\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @author Stepan Tanasiychuk <ceo@stfalcon.com>
 */
class AbstractController extends Controller
{
    protected function _getRequestDataWithDisqusShortname(array $requestData): array
    {
        $config = $this->container->getParameter('stfalcon_blog.config');
        $disqusShortnameKey = 'disqus_shortname';
        $requestDataWithDisqusShortname = array_merge(
            $requestData,
            [$disqusShortnameKey => $config[$disqusShortnameKey]]
        );
        
        return $requestDataWithDisqusShortname;
    }
}
