<?php
declare(strict_types=1);

namespace Lfc\Retail\Model;

use Magento\Bundle\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\GroupedProduct\Model\Product\Type\Grouped;

/**
 *
 */
class ParentProvider
{
    /**
     * @var Configurable
     */
    private Configurable $configurable;

    /**
     * @param Configurable $configurable
     */
    public function __construct(
        Configurable $configurable
    ) {
        $this->configurable = $configurable;
    }

    /**
     * @param $childId
     * @return false|mixed
     */
    public function getParentId($childId)
    {
        $types = [$this->configurable];
        foreach ($types as $type) {
            $parents = $type->getParentIdsByChild($childId);
            if (isset($parents[0])) {
                return $parents[0];
            }
        }
        return false;
    }
}
