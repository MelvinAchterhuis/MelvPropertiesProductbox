<?php declare(strict_types=1);

namespace Melv\PropertiesProductbox\Storefront\Resolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Product\Cms\ProductSliderCmsElementResolver;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class SliderResolver
{
    /** @var ProductSliderCmsElementResolver $elementResolver */
    private $elementResolver;

    public function __construct(ProductSliderCmsElementResolver $elementResolver)
    {
        $this->elementResolver = $elementResolver;
    }

    public function getType(): string
    {
        return $this->elementResolver->getType();
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $criteriaCollection = new CriteriaCollection();

        $config = $slot->getFieldConfig();
        $products = $config->get('products');

        if (!$products || $products->isMapped() || $products->getValue() === null) {
            return null;
        }

        if ($products->isStatic() && $products->getValue()) {
            $criteria = new Criteria($products->getValue());
            $criteria->addAssociation('properties');
            $criteria->addAssociation('properties.group');
            $criteriaCollection->add('product-slider' . '_' . $slot->getUniqueIdentifier(), ProductDefinition::class, $criteria);
        }
        return $criteriaCollection->all() ? $criteriaCollection : null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $this->elementResolver->enrich($slot, $resolverContext, $result);
    }
}
