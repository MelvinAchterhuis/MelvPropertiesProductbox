<?php declare(strict_types=1);

namespace Melv\PropertiesProductbox\Storefront\Subscriber;

use Shopware\Core\Content\Product\Events\ProductSuggestCriteriaEvent;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SearchSuggest implements EventSubscriberInterface
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
            ProductSuggestCriteriaEvent::class => 'handleRequest'
        ];
    }

    public function handleRequest(ProductSuggestCriteriaEvent $event): void
    {
        $salesChannelId =  $event->getSalesChannelContext()->getSalesChannel()->getId();
        $active = $this->systemConfigService->get('MelvPropertiesProductbox.config.showSuggest', $salesChannelId);

        if(!$active) {
            return;
        }

        $event->getCriteria()->addAssociation('properties.group');
    }
}
