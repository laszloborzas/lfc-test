<?php
declare(strict_types=1);

namespace Lfc\Retail\Controller\Form;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Type\Simple;
use Magento\Catalog\Model\Product\Visibility;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\GroupedProduct\Model\ResourceModel\Product\Link;

/**
 * Class Index
 */
class Form implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    private PageFactory $pageFactory;
    private RequestInterface $request;
    private ProductRepositoryInterface $productRepository;
    private ManagerInterface $messageManager;
    private Http $response;

    /**
     * @param PageFactory $pageFactory
     * @param RequestInterface $request
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        PageFactory $pageFactory,
        RequestInterface $request,
        ProductRepositoryInterface  $productRepository,
        ManagerInterface $messageManager,
        Http $response
    ) {
        $this->pageFactory = $pageFactory;
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->messageManager = $messageManager;
        $this->response = $response;
    }

    /**
     * @return Page|ResultInterface
     */
    public function execute()
    {
        $productSku = $this->request->getParam('product_sku', false);
        if ($productSku) {
            try {
                $product = $this->productRepository->get($productSku);
                if ($product->getId() && $product->getVisibility() !== Visibility::VISIBILITY_NOT_VISIBLE) {
                    if ($product->getTypeId() === Type::TYPE_SIMPLE) {
                        $this->response->setRedirect($product->getProductUrl());
                    }
                    if (in_array($product->getTypeId(), [Type::TYPE_BUNDLE, Link::LINK_TYPE_GROUPED, Configurable::TYPE_CODE])) {
                        $this->response->setRedirect($product->getProductUrl(), 302);
                        //$parentIds = $product->getParentIdsByChild($product->getId());
                    }
                }
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('Product not found.'));
            }
        }
        return $this->pageFactory->create();
    }
}
