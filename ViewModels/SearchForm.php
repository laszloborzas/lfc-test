<?php
declare(strict_types=1);

namespace Lfc\Retail\ViewModels;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class SearchForm implements ArgumentInterface
{
    public const LFC_RETAIL_FORM_SUBMIT_ROUTE = 'lfc-retail/m2-test';

    /**
     * @return string
     */
    public function getFormActionRoute(): string
    {
        return self::LFC_RETAIL_FORM_SUBMIT_ROUTE;
    }
}
