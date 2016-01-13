<?php

namespace Sainsburys\Tests;

use ReflectionClass;
use Sainsburys\Classes\ProductScraper;

class ProductScraperTest extends \PHPUnit_Framework_TestCase {

    /**
     * Test the product scraper
     */
    public function testScrape() {
        $productScraper = new ProductScraper();

        $responseJSON = $productScraper->scrape();

        $this->assertJSON($responseJSON);
        $this->assertTrue($this->isValidJSON($responseJSON));

        $responseArray = json_decode($responseJSON, true);
        $results = $responseArray['results'];

        $this->assertArrayHasKey('results', $responseArray);
        $this->assertArrayHasKey('total', $responseArray);
        $this->assertArrayHasKey('title', $results[0]);
        $this->assertArrayHasKey('size', $results[0]);
        $this->assertArrayHasKey('unit_price', $results[0]);
        $this->assertArrayHasKey('description', $results[0]);
    }


    /**
     * Test the product scrapers protected getUnitSize method
     */
    public function testGetUnitSize() {
        $unitPrice = '£1.50/unit';

        $reflector = new ReflectionClass(ProductScraper::class);
        $method = $reflector->getMethod('getUnitPrice');
        $method->setAccessible(true);

        $result = $method->invokeArgs(new ProductScraper(), [$unitPrice]);

        $this->assertEquals(1.50, $result);
    }

    /**
     * Test the product scrapers protected getSizeInKB method
     */
    public function testGetSizeInKB() {
        $size = 1000;

        $reflector = new ReflectionClass(ProductScraper::class);
        $method = $reflector->getMethod('getSizeInKB');
        $method->setAccessible(true);

        $result = $method->invokeArgs(new ProductScraper(), [$size]);

        $this->assertEquals("0.98KB", $result);
    }

    /**
     * Check if the response is a valid json format
     *
     * @param $response
     * @return bool
     */
    protected function isValidJSON($response) {
        json_decode($response);
        return (json_last_error() === JSON_ERROR_NONE);
    }
}