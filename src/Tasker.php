<?php

namespace ShopwareCheckTool;
require_once('../vendor/autoload.php');

use ShopwareCheckTool\Download\DownloadMarketplace;
use ShopwareCheckTool\Models\Marketplace;
use ShopwareCheckTool\Requests\Shopware;
use ShopwareCheckTool\Task\AttributeTask;
use ShopwareCheckTool\Task\CategoryTask;
use ShopwareCheckTool\Task\DeliveryTask;
use ShopwareCheckTool\Task\ImagesTask;
use ShopwareCheckTool\Task\ManufacturerTask;
use ShopwareCheckTool\Task\MeasurementTask;
use ShopwareCheckTool\Task\PropertyTask;

class Tasker
{
    public function downloadMarketplace(string $domain, string $token, bool $skip = false): void
    {
        if (!$skip) {
            $marketplace = new Marketplace();
            $marketplace->setDomain($domain);
            $marketplace->setToken($token);
            (new DownloadMarketplace($marketplace))->download();
        }
    }

    public function start(int $configurationId): void
    {
        $shopware = new Shopware($configurationId);
        //(new AttributeTask($shopware))->check();
        //(new CategoryTask($shopware))->check();
        //(new DeliveryTask($shopware))->check();
        //(new ManufacturerTask($shopware))->check();
        //(new MeasurementTask($shopware))->check();
        //(new PropertyTask($shopware))->check();
        (new ImagesTask($shopware))->check();
    }
}

$tasker = new Tasker();
$tasker->downloadMarketplace(
    "https://sw.trendbereich.com",
    "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiOTY4NTBhODk4M2ExMDhhMTdhZTlmYWVhYjRiMjNlYjAzZTdiZjRjMTFkNGFlODc0MDM2YmU0MzQ5ZWRhMDJkMThlMTgxOGNjMjY3MjUwNGMiLCJpYXQiOjE2NTIzNjAwMDkuMzMyMiwibmJmIjoxNjUyMzYwMDA5LjMzMjIsImV4cCI6MTY1MjQ0NjQwOS4zMjgsInN1YiI6IjUzIiwic2NvcGVzIjpbIioiXX0.Tz2x126CMOj8WjlAoEmc-cwZo-dm87YlFxvUfVr5YHnZdTwqSco1HdFMI6XzlrQf-kGONYez35EjZZ9JY8qfl6r1t4tT8G4OOqBrq3tV168cWB0-b41IdtphO85CtfbNWlAHkh8kjLXp1hITOOw0dLXaJsoMTL4YtrpQcZY2BSVMBszrUGb2mL2H-a3Cjyc9kkMRfpuhuqIQdn6gI1pwkYRKj5HK6zL5aZ_eW9Xki_vkcn4659kgxmLs9r39304eyBayxWNJ4Ef7O5EDAmedP2714Gmj8DCBKoT57RrEqRJmkJ_F2CKuJjNt6HBBPuej2Zhysqkpqsl3VUyC5SK7Gbqg0XSfi8khshiE4L_ksrqD3InF3q0AM2HYgwWtxtUzjoplLBJuRKhU-u2hJ0T3pa1YPxoYyH8sSRH9CEc8YG2D6rR7hWr3QW4KOmKUGoHmNhwIIaNmYnT_15zeQF7tHypEuRpVng5fga3viRilCWuZLpUSu8RFhmGAbqtvpgWjpHqn1BeGGj0q2GvJ2gVD54B8w5--JfOauZZB50UbbQ3QEF62yOCiroBFlBJzIW00zQ48SxM7qcgylrFf3gbkq9t2RCWUecc0KawEZ0NiEUW1RVBi39QBJkLzW4VUlUTDtT9e-0wGy6odH4UwaRWqmzCYuChG6pJhW-0YVTpMbsE",
    true
);
$tasker->start(1);
