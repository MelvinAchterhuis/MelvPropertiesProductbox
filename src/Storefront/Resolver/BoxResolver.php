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

class BoxResolver
{
    /**
     * @param CmsElementResolverInterface $elementResolver
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
        $active = $this->systemConfigService->get('MelvPropertiesProductbox.config.showBox', $salesChannelId);

        $config = $slot->getFieldConfig();
        $productConfig = $config->get('product');

        if (!$productConfig || $productConfig->isMapped() || $productConfig->getValue() === null) {
          return null;
        }

        $criteria = new Criteria([$productConfig->getValue()]);

        $criteriaCollection = new CriteriaCollection();
        if($active) {
            $criteria->addAssociation('properties.group');
        }
        $criteriaCollection->add('product_' . $slot->getUniqueIdentifier(), ProductDefinition::class, $criteria);

        return $criteriaCollection->all() ? $criteriaCollection : null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $this->elementResolver->enrich($slot, $resolverContext, $result);
    }
}
