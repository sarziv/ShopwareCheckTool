<?php

namespace ShopwareCheckTool\Download;

use RuntimeException;
use ShopwareCheckTool\FileManagement\Log;

abstract class Download extends Log
{
    public const CONFIGURATION = 'Configuration';
    public const ATTRIBUTE = 'Attribute';
    public const ATTRIBUTE_REWORK = 'AttributeRework';
    public const CATEGORY = 'Category';
    public const MANUFACTURER = 'Manufacturer';
    public const MEASUREMENT = 'Measurement';
    public const DELIVERY = 'Delivery';
    public const PROPERTY = 'Property';
    public const PROPERTY_DYNAMIC = 'PropertyDynamic';
    public const IMAGES = 'Images';
    public const TAG = 'Tag';
    public const PRODUCT_CONFIGURATION = 'ProductConfigurator';
    public const PRODUCT_VISIBILITY = 'ProductVisibility';
    public const SHOPWARE_ERROR = 'ShopwareError';
    public const REFERRER = 'Referrer';

    public const ATTRIBUTE_LOG = 'AttributeTask';
    public const ATTRIBUTE_REWORK_LOG = 'AttributeReworkTask';
    public const CATEGORY_LOG = 'CategoryTask';
    public const MANUFACTURER_LOG = 'ManufacturerTask';
    public const MEASUREMENT_LOG = 'MeasurementTask';
    public const DELIVERY_LOG = 'DeliveryTask';
    public const PROPERTY_LOG = 'PropertyTask';
    public const PROPERTY_DYNAMIC_LOG = 'PropertyDynamicTask';
    public const IMAGES_LOG = 'ImagesTask';
    public const TAG_LOG = 'TagTask';
    public const PRODUCT_CONFIGURATION_LOG = 'ProductConfiguratorTask';
    public const PRODUCT_VISIBILITY_LOG = 'ProductVisibilityTask';

    public const NAMES = [
        self::CONFIGURATION,
        self::ATTRIBUTE,
        self::ATTRIBUTE_REWORK,
        self::CATEGORY,
        self::MANUFACTURER,
        self::MEASUREMENT,
        self::DELIVERY,
        self::PROPERTY,
        self::PROPERTY_DYNAMIC,
        self::IMAGES,
        self::TAG,
        self::PRODUCT_CONFIGURATION,
        self::PRODUCT_VISIBILITY,
        self::SHOPWARE_ERROR,
        self::REFERRER
    ];

    protected function save(string $name, string $json): void
    {
        if (!in_array($name, self::NAMES)) {
            return;
        }
        $dir = __DIR__ . "/../Logs/Downloaded/";
        $file = "$dir/$name.json";

        if (!file_exists($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        if (file_exists($file)) {
            unlink($file);
        }
        file_put_contents($file, $json);
    }
}