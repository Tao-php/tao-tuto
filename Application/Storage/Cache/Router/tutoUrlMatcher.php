<?php

use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * tutoUrlMatcher
 *
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class tutoUrlMatcher extends Symfony\Component\Routing\Matcher\UrlMatcher
{
    /**
     * Constructor.
     */
    public function __construct(RequestContext $context)
    {
        $this->context = $context;
    }

    public function match($pathinfo)
    {
        $allow = array();
        $pathinfo = rawurldecode($pathinfo);
        $context = $this->context;
        $request = $this->request;

        // home
        if ($pathinfo === '/') {
            return array (  '_controller' => 'Home::show',  '_route' => 'home',);
        }

        // hello
        if (0 === strpos($pathinfo, '/hello') && preg_match('#^/hello(?:/(?P<name>[^/]++))?$#s', $pathinfo, $matches)) {
            return $this->mergeDefaults(array_replace($matches, array('_route' => 'hello')), array (  '_controller' => 'Hello::world',  'name' => 'world',));
        }

        if (0 === strpos($pathinfo, '/contact')) {
            // contact
            if ($pathinfo === '/contact') {
                if (!in_array($this->context->getMethod(), array('GET', 'HEAD'))) {
                    $allow = array_merge($allow, array('GET', 'HEAD'));
                    goto not_contact;
                }

                return array (  '_controller' => 'Contact::form',  '_route' => 'contact',);
            }
            not_contact:

            // contact_process
            if ($pathinfo === '/contact') {
                if ($this->context->getMethod() != 'POST') {
                    $allow[] = 'POST';
                    goto not_contact_process;
                }

                return array (  '_controller' => 'Contact::process',  '_route' => 'contact_process',);
            }
            not_contact_process:

        }

        throw 0 < count($allow) ? new MethodNotAllowedException(array_unique($allow)) : new ResourceNotFoundException();
    }
}
