<?php

namespace Melv\PropertiesProductbox\Storefront\Subscriber;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Storefront\Page\Wishlist\WishListPageProductCriteriaEvent;


class WishList implements EventSubscriberInterface
{
    /**
    * @var SystemConfigService
     */
    public function __construct(
        SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public static function getSubscribedEvents()
    {
        return [
            WishListPageProductCriteriaEvent::class => "handleRequest"
        ];
    }

    public function handleRequest(WishListPageProductCriteriaEvent $event): void
    {
        $salesChannelId =  $event->getSalesChannelContext()->getSalesChannel()->getId();
        $active = $this->systemConfigService->get('MelvPropertiesProductbox.config.showWishList', $salesChannelId);

        if(!$active) {
            return;
        }

        $event->getCriteria()->addAssociation('properties.group');
    }
}