<?php
declare(strict_types=1);

namespace Lfc\Retail\Controller;

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

        if (strpos($identifier, 'lfc-retail/m2-test') !== false) {
            $request->setRouteName('lfc');
            $request->setControllerName('form');
            $request->setActionName('form');
        }
    }
}
