<?php
declare(strict_types=1);

namespace Lfc\Retail\Controller\Form;

use Lfc\Retail\Model\ParentProvider;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Type\Simple;
use Magento\Catalog\Model\Product\Visibility;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\Redirect;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
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
    public const FORM_URL = 'lfc-retail/m2-test';
    /**
     * @var PageFactory
     */
    private PageFactory $pageFactory;
    private RequestInterface $request;
    private ProductRepositoryInterface $productRepository;
    private ManagerInterface $messageManager;
    private Http $response;
    private ParentProvider $parentProvider;
    private ActionFactory $actionFactory;

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
        Http $response,
        ParentProvider $parentProvider,
        ActionFactory $actionFactory
    ) {
        $this->pageFactory = $pageFactory;
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->messageManager = $messageManager;
        $this->response = $response;
        $this->parentProvider = $parentProvider;
        $this->actionFactory = $actionFactory;
    }

    /**
     * @return ActionInterface|\Magento\Framework\App\ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $productSku = $this->request->getParam('product_sku', false);
        if ($productSku === '') {
            $this->messageManager->addErrorMessage(__('Please provide sku.'));
        }
        if ($productSku) {
            try {
                $product = $this->productRepository->get($productSku);
                if ($product->getId() && (int) $product->getStatus() === Status::STATUS_ENABLED) {
                    if ((int) $product->getVisibility() === Visibility::VISIBILITY_NOT_VISIBLE) {
                        $parentUrl = $this->getParentProductUrl(
                            $product->getId()
                        );
                        if ($parentUrl) {
                            $this->response->setRedirect($parentUrl);
                            $this->request->setDispatched(true);
                            return $this->actionFactory->create(Redirect::class);
                        }
                    } else {
                        $this->response->setRedirect($product->getProductUrl());
                        $this->request->setDispatched(true);
                        return $this->actionFactory->create(Redirect::class);
                    }
                }
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('Product not found.'));
            }
        }
        return $this->pageFactory->create();
    }

    /**
     * @param $productId
     * @return string|null
     */
    public function getParentProductUrl($productId): ?string
    {
        $parentId = $this->parentProvider->getParentId($productId);
        if ($parentId !== false) {
            try {
                $parent = $this->productRepository->getById($parentId);
                if ($parent->getId() &&
                    (int)$parent->getStatus() === Status::STATUS_ENABLED &&
                    (int)$parent->getVisibility() !== Visibility::VISIBILITY_NOT_VISIBLE
                ) {
                    return $parent->getUrlModel()->getUrl($parent, ['_ignore_category' => true]);
                }
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addErrorMessage(__('Product not found.'));
                return null;
            }
        }
        return null;
    }
}
