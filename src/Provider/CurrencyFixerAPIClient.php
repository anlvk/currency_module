<?php
/**
 * @file
 * Contains Drupal\currency\Provider\CurrencyFixerAPIClient
 * 
 * Client for Fixer API.
 */

namespace Drupal\currency\Provider;

use Drupal\Core\Site\Settings;
use Drupal\currency\CurrencyProviderInterface;

class CurrencyFixerAPIClient implements CurrencyProviderInterface {
    private string $api_key;
    private $http_client;

    const BASE_URI = 'http://data.fixer.io/api/';
    const API_REQUEST_RATES_ENDPOINT = 'latest';
    const API_REQUEST_CONVERT_ENDPOINT = 'convert';

    public function __construct() {
        /**
         * Read value from the settings file.
         */
        $this->api_key = Settings::get('currency_provider.fixer_api_key', '');

        $this->http_client = \Drupal::service('http_client_factory')->fromOptions([
            'base_uri' => self::BASE_URI,
        ]);
    }

    /**
     * API request endpoint: rates endpoint.
     * 
     * Retrieve latest currency rates.
     * Returns object of code->rate if success.
     */
    public function getRates() {
        try {
            $request = $this->http_client->get(self::API_REQUEST_RATES_ENDPOINT, [
                'query' => [
                    'access_key' => $this->api_key,
                ]
            ]);

            $response = $request->getBody();
            $response_object = json_decode($response);

            if($response_object->success) {
                return $response_object->rates;
            }
            else {
                \Drupal::messenger()->addMessage($response_object->error->info, 'error');
                \Drupal::logger('currency')->error($response);
            }
        }
        catch (\Exception $e) {
            \Drupal::logger('currency')->error($e->getMessage());
        }

    }

    /**
     * API request endpoint: convert currency endpoint.
     * 
     * Convert currency of defined amount.
     * @param int $amount
     * @param string $from
     * @param string $to
     */
    public function convert(int $amount, string $from, string $to) {
        try {
            $request = $this->http_client->post(self::API_REQUEST_CONVERT_ENDPOINT, [
                'query' => [
                    'access_key'=> $this->api_key,
                    'from' => $from,
                    'to' => $to,
                    'amount' => $amount,
                ]
            ]);

            $response = $request->getBody();
            $response_object = json_decode($response);
            if($response_object->success) {
                return $response_object->result;
            }
            else {
                \Drupal::messenger()->addMessage($response_object->error->info, 'error');
                \Drupal::logger('currency')->error($response);
            }
        }
        catch (\Exception $e) {
            \Drupal::logger('currency')->error($e->getMessage());
        }
    }
}