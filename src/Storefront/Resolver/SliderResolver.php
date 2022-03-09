<?php declare(strict_types=1);

namespace Melv\PropertiesProductbox\Storefront\Resolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Core\Content\Cms\DataResolver\Element\CmsElementResolverInterface;

class SliderResolver
{
    /**
     * @param ProductSliderCmsElementResolver $elementResolver
     * @param SystemConfigService $systemConfigService
     */

    public function __construct(
        CmsElementResolverInterface $elementResolver,
        SystemConfigService $systemConfigService
    ){
        $this->elementResolver = $elementResolver;
        $this->systemConfigService = $systemConfigService;
    }

    public function getType(): string
    {
        return $this->elementResolver->getType();
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {

        $salesChannelId =  $resolverContext->getSalesChannelContext()->getSalesChannelId();
        $active = $this->systemConfigService->get('MelvPropertiesProductbox.config.showSlider', $salesChannelId);

        $criteriaCollection = new CriteriaCollection();

        $config = $slot->getFieldConfig();
        $products = $config->get('products');

        if (!$products || $products->isMapped() || $products->getValue() === null ) {
            return null;
        }

        if ($products->isStatic() && $products->getValue()) {
            $criteria = new Criteria($products->getValue());
            if ($active) {
                $criteria->addAssociation('properties.group');
            }
            $criteriaCollection->add('product-slider' . '_' . $slot->getUniqueIdentifier(), ProductDefinition::class, $criteria);
        }
        return $criteriaCollection->all() ? $criteriaCollection : null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $this->elementResolver->enrich($slot, $resolverContext, $result);
    }
}
