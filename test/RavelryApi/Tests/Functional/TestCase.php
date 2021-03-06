<?php

namespace RavelryApi\Tests\Functional;

use GuzzleHttp\Event\SubscriberInterface;
use RavelryApi\Authentication\BasicAuthentication;
use RavelryApi\Authentication\OauthAuthentication;
use RavelryApi\Authentication\OauthTokenStorage\FileTokenStorage;
use RavelryApi\Client;

/**
 * @group functional
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $client;
    private $clientListeners = [];
    private $serviceClientListeners = [];

    static public function createClient()
    {
        if (0 == strlen(getenv('RAVELRY_TEST_ACCESS_KEY'))) {
            throw new \LogicException('Environment variable RAVELRY_TEST_ACCESS_KEY must be defined.');
        }

        if (0 < strlen(getenv('RAVELRY_TEST_PERSONAL_KEY'))) {
            $auth = new BasicAuthentication(getenv('RAVELRY_TEST_ACCESS_KEY'), getenv('RAVELRY_TEST_PERSONAL_KEY'));
        } elseif (0 < strlen(getenv('RAVELRY_TEST_SECRET_KEY'))) {
            $auth = new OauthAuthentication(
                new FileTokenStorage('.ravelryapi', false),
                getenv('RAVELRY_TEST_ACCESS_KEY'),
                getenv('RAVELRY_TEST_SECRET_KEY')
            );
        } else {
            throw new \LogicException('Environment variable RAVELRY_TEST_PERSONAL_KEY or RAVELRY_TEST_SECRET_KEY must be defined.');
        }

        return new Client($auth);
    }

    public function setUp()
    {
        $this->client = self::createClient();

        foreach ($this->clientListeners as $clientListener) {
            $this->attachClientListener($clientListener);
        }

        $this->clientListeners = [];

        foreach ($this->serviceClientListeners as $serviceClientListener) {
            $this->attachServiceClientListener($serviceClientListener);
        }

        $this->serviceClientListeners = [];
    }

    public function tearDown()
    {
        $this->client = null;
    }

    public function attachClientListener(SubscriberInterface $clientListener)
    {
        if (null !== $this->client) {
            $this->client->getGuzzle()->getEmitter()->attach($clientListener);
        } else {
            $this->clientListeners[] = $clientListener;
        }

        return $this;
    }

    public function attachServiceClientListener(SubscriberInterface $serviceClientListener)
    {
        if (null !== $this->client) {
            $this->client->getServiceClient()->getEmitter()->attach($serviceClientListener);
        } else {
            $this->serviceClientListeners[] = $serviceClientListener;
        }

        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function retryTimes($callable, $delay = 10, $times = 12)
    {
        for ($try = 0; $try < $times; $try += 1) {
            if (null !== $result = call_user_func_array($callable, [ $this ])) {
                return $result;
            }

            sleep($delay);
        }

        throw new \RuntimeException('Failed to finish within ' . $times . ' tries.');
    }
}
