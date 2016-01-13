<?php

namespace Sainsburys\Classes;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\Yaml\Exception\RuntimeException;

class HttpClient {

    const URL = "http://hiring-tests.s3-website-eu-west-1.amazonaws.com/2015_Developer_Scrape/5_products.html";

    /** @var array */
    protected $response = [];
    /** @var Client */
    protected $client;
    /** @var string */
    protected $url;

    /**
     * Set the Guzzle HTTP Client
     *
     * @param Client $client
     * @return $this
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Get the client
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set the url used to make the request
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    /**
     * Get the url
     *
     * @return string
     */
    public function getUrl() {
        return (empty($this->url)) ? self::URL : $this->url;
    }

    public function __construct() {
        /** @var Client */
        $this->client = new Client();
    }

    /**
     * Make the http call to the url and return the response
     *
     * @return array
     */
    public function request()
    {
        try {
            $response = $this->client->get(
                $this->getUrl(),
                [
                    'request.options' => array(
                        'exceptions' => true,
                    )
                ]
            );
        } catch (ConnectException $e) {
            throw new RuntimeException('Unable to connect to the url');
        }

        return $this->buildResponse($response);
    }

    /**
     * Create an array of the response object
     *
     * @param Response $response
     * @return array
     */
    private function buildResponse(Response $response)
    {
        $this->response['body'] = $response->getBody()->getContents();
        $this->response['size'] = $response->getBody()->getSize();
        $this->response['status_code'] = $response->getStatusCode();

        //Build the response headers
        $headers = $response->getHeaders();
        foreach ($headers as $name => $value) {
            $this->response['headers'][$name] = $value;
        }

        return $this->response;
    }

}