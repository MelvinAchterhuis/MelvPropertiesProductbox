<?php declare(strict_types=1);

namespace Melv\PropertiesProductbox\Storefront\Subscriber;

use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Content\Product\Events\ProductSearchCriteriaEvent;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductListing implements EventSubscriberInterface
{
    public function __construct
    (
        SystemConfigService $systemConfigService
    ) {
        $this->systemConfigService = $systemConfigService;
    }

    public static function getSubscribedEvents()
    {
        return [
            ProductListingCriteriaEvent::class => 'handleRequest',
            ProductSearchCriteriaEvent::class => 'handleRequest'
        ];
    }

    public function handleRequest(ProductListingCriteriaEvent $event): void
    {
        $salesChannelId =  $event->getSalesChannelContext()->getSalesChannel()->getId();
        $active = $this->systemConfigService->get('MelvPropertiesProductbox.config.showListing', $salesChannelId);

        if(!$active) {
            return;
        }

        $event->getCriteria()->addAssociation('properties.group');
    }
}
