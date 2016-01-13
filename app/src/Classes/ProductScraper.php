<?php

namespace Sainsburys\Classes;

use Symfony\Component\DomCrawler\Crawler;
use Sainsburys\Classes\HttpClient;


class ProductScraper {

    /** @var HttpClient */
    protected $httpClient;
    /** @var string */
    protected $html;
    /** @var array */
     protected $products = [
         'results' => [],
         'total' => 0
     ];

    public function __construct() {
        /** @var HttpClient */
        $this->httpClient = new HttpClient();
    }

    /**
     * Crawl the Ripe Fruits page and scrape the products
     *
     * @return string
     */
    public function scrape()
    {
        $request = $this->httpClient->request();
        $crawler = new Crawler($request['body']);
        // Loop through the products
        $crawler->filter('ul.productLister .product .productInner')->each(function (Crawler $node, $i) {
            $this->buildProduct($node, $i);
        });

        // Format the total to have 2 decimals
        $this->products['total'] = number_format($this->products['total'], 2);

        return $this->toJSON();
    }

    /**
     * Create an array with the product information
     *
     * @param Crawler $node
     * @param int $i
     */
    protected function buildProduct(Crawler $node, $i) {

        $title = $node->filter('h3 a');
        $url = $title->attr('href');
        $information = $this->getProductInformation($url);
        $unitPrice = $this->getUnitPrice($node->filter('p.pricePerUnit')->text());


        $this->products['results'][$i] = [
            'title' => trim($title->text()),
            'size' => $this->getSizeInKB($information['size']),
            'unit_price' => $unitPrice,
            'description' => $information['description']
        ];

        // Sum the total unit prices
        $this->products['total'] += $unitPrice;
    }

    /**
     * Make a call to the product page to crawl the information
     *
     * @param $url
     * @param array $information
     * @return array
     */
    protected function getProductInformation($url, $information = [])
    {
        $request = $this->httpClient->setUrl($url)->request();
        $crawler = new Crawler($request['body']);

        $information['description'] = trim($crawler->filter('div.productText')->first()->text());
        $information['size'] = $request['size'];

        return $information;
    }

    /**
     * Remove everything from the string except the unit price
     *
     * @param string $node
     * @return string
     */
    protected function getUnitPrice($text) {
        return preg_replace('/([^0-9\.,])/i', '', $text);
    }

    /**
     * Format the bytes to kilobytes
     *
     * @param int $bytes
     * @return string
     */
    protected function getSizeInKB($bytes) {
        return sprintf("%4.2fKB", $bytes/1024);
    }

    /**
     * Return a valid json object
     *
     * @return string
     */
    protected function toJSON() {
        return json_encode($this->products, JSON_PRETTY_PRINT);
    }



}