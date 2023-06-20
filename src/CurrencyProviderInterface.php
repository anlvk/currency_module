<?php
/**
 * @file
 * Contains Drupal\currency\CurrencyProviderInterface
 */
namespace Drupal\currency;

interface CurrencyProviderInterface {
    /**
     * Retrieves rates.
     */
    public function getRates();

    /**
     * Converts amount from one currency code to another one.
     * @param int $amount
     * @param string $from
     * @param string $to
     */
    public function convert(int $amount, string $from, string $to);
}