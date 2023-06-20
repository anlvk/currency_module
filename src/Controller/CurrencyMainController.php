<?php
/**
 * @file
 * Contains Drupal\currency\Controller\CurrencyMainController
 */
namespace Drupal\currency\Controller;

use Drupal\Core\Database\Database;
use Drupal\currency\CurrencyProviderInterface;


class CurrencyMainController {
    protected CurrencyProviderInterface $currency_provider;

    public function __construct(CurrencyProviderInterface $provider) {
        $this->currency_provider = $provider;
    }

    /**
     * Get currency codes and rates from Database.
     * @return array
     *     Returns codes and rates as an array.
     */
    public static function getDBCurrencyValues(): array {
        $values = [];

        try {
            $connection = Database::getConnection();

            $result = $connection->select('currency', 'c')
                ->fields('c', ['currency_code', 'currency_rate'])
                ->execute()
                ->fetchAll();

            /**
             * If no values, may return bool.
             */
            if($result) {
                $values = $result;
            }
        }
        catch (\Exception $e) {
            \Drupal::logger('currency')->error($e->getMessage());
        }

        return $values;
    }

    /**
     * Update Database: update rates for each stored currency code.
     */
    public function updateDBRates(): void {
        $connection = Database::getConnection();

        /**
         * Retrieve currency codes stored in DB.
         */
        $select = $connection->select('currency', 'c')
            ->fields('c', ['currency_code'])
            ->execute();
        $codes = $select->fetchCol();

        if($codes) {
            try {
                /**
                 * Retrieve rates: 3rd-party.
                 */
                $rates = $this->getLatestRates();
            }
            catch (\Exception $e) {
                \Drupal::logger('currency')->error($e->getMessage());
            }

            if($rates) {
                foreach($codes as $code) {
                    /**
                     * Update currency values in DB with newly retrieved rates.
                     */
                    $rate = $rates->$code;
                    $fields['currency_rate'] = $rate;

                    $connection->update('currency')
                        ->fields($fields)
                        ->condition('currency_code', $code)
                        ->execute();
                }
                \Drupal::logger('currency')->info(t('Currency values have been updated.'));
            }
        }
        else {
            \Drupal::logger('currency')->info(t('No currency values to be updated.'));
        }
    }

    /**
     * Get currency rates from external provider.
     */
    public function getLatestRates() {
        return $this->currency_provider->getRates();
    }
}
