<?php

namespace OroCRM\Bundle\ZendeskBundle\Model;

use Guzzle\Http\Client;

use OroCRM\Bundle\ZendeskBundle\Provider\ConfigurationProvider;

class RestClientFactory
{
    /**
     * @var ConfigurationProvider
     */
    protected $provider;

    /**
     * @var Client
     */
    protected $client = null;

    /**
     * @param ConfigurationProvider $provider
     */
    public function __construct(ConfigurationProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return RestClient
     */
    public function getRestClient()
    {
        $settings = array(
            'api_token'  => $this->provider->getApiToken(),
            'email'      => $this->provider->getEmail(),
            'sub_domain' => $this->provider->getSubDomain()
        );
        return new RestClient($this->getClient(), $settings);
    }

    /**
     * @return Client
     */
    protected function getClient()
    {
        if (!$this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }
}