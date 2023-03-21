<?php
declare(strict_types=1);

namespace Lfc\Retail\Controller;

use Lfc\Retail\Controller\Form\Form;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;

/**
 *  Custom router just for the hyphen
 */
class Router implements RouterInterface
{
    /**
     * Match corresponding URL Rewrite and modify request.
     *
     * @param RequestInterface|HttpRequest $request
     */
    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');

        if (strpos($identifier, Form::FORM_URL) !== false) {
            $request->setRouteName('lfc');
            $request->setControllerName('form');
            $request->setActionName('form');
        }
    }
}
