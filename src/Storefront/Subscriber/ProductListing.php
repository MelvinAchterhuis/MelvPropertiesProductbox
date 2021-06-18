<?php declare(strict_types=1);

namespace Melv\PropertiesProductbox\Storefront\Subscriber;

use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductListing implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ProductListingCriteriaEvent::class => 'handleRequest',
        ];
    }

    public function handleRequest(ProductListingCriteriaEvent $event)
    {
        $event->getCriteria()->addAssociation('properties.group');
    }
}
