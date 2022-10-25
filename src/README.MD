# Tasker
## Installation
Tasks are selected in runner
```
cd ShopwareCheckTool/
composer install
cp ./src/.credentials.php ./src/credentials.php
php /{location}/ShopwareCheckTool/src/runner.php
```

## Tasks
AttributeTask - Attributes from V1.0.9>
AttributeReworkTask - Attributes from V1.0.9<
CategoryTask - Category checks
DeliveryTask - Units checks
ManufacturerTask - Manufacturers checks
MeasurementTask - Measurements checks
PropertyTask - Property checks
TagTask - Tags checks
ImagesTask - Images checks
ImageDuplicateTask - Removes duplicates or unknown images
ImageDeepTask - Check images, media files
ImageDeepInvalidTask - Removes invalid media files, for re-upload
ProductVisibilityTask - Product visibility checks
ProductConfiguratorTask - Product configuration checks
ShopwareErrorDuplicateProductNumberTask - Checks Shopware errors file and removes product by product number
ProductConfiguratorDuplicateTask - In-development
