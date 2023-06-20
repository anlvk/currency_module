<?php
/**
 * @file
 * Contains \Drupal\currency\Controller\CurrencyRouteController
 * Routes Controller.
 */
namespace Drupal\currency\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\currency\Controller\CurrencyMainController;


class CurrencyRouteController extends ControllerBase {
    /**
     * A page displays all currency (code: rate) stored in DB.
     */
    public function currency_list(): array {
        $items = [];
        $currency_values = CurrencyMainController::getDBCurrencyValues();

        if($currency_values) {
            foreach($currency_values as $value) {
                $items[] = "$value->currency_code : $value->currency_rate";
            }
        }

        $content = [
            '#theme' => 'item_list',
            '#list_type' => 'ul',
            '#title' => 'Currency List',
            '#items' => $items,
            '#attributes' => ['class' => 'currency_list'],
            '#wrapper_attributes' => ['class' => 'container'],
        ];

        return $content;
    }

    /**
     * Test Drupal service created by currency module: currency provider -> convert.
     * Hard-coded 45 USD to BYN.
     */
    public function test_currency_service_provider(): array {
        $converter = \Drupal::service('currency_service.provider');
        $amount = 45;
        $from = 'USD';
        $to = 'BYN';
        $r = $converter->convert($amount, $from, $to);

        $markup = "$amount $from has been converted to ($r) $to.";

        return array(
            '#type' => 'markup',
            '#markup' => $markup,
        );
    }

}