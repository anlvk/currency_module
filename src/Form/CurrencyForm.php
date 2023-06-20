<?php
/**
 * @file
 * Contains \Drupal\currency\Form\CurrencyForm
 * 
 * Creates a form to add currency to DataBase: table 'currency';
 */
namespace Drupal\currency\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;

class CurrencyForm extends FormBase {
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'currency_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['currency_code'] = array(
            '#type' => 'textfield',
            '#title' => t('Currency Code:'),
            '#required' => TRUE,
        );
        $form['currency_rate'] = [
            '#type' => 'number',
            '#step' => '.001',
            '#title' => t('Currency Rate'),
            '#default_value' => 0.0,
            '#required' => FALSE,
        ];
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Save'),
            '#button_type' => 'primary',
        );
        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {}

    public function submitForm(array &$form, FormStateInterface $form_state) {
        try{
            $field = $form_state->getValues();

            $fields['currency_code'] = $field['currency_code'];
            $fields['currency_rate'] = $field['currency_rate'];

            $connection = Database::getConnection();
            /**
             * Do upsert by unqiue key 'currency_code'.
             */
            $connection->upsert('currency')
                ->fields($fields)
                ->key('currency_code')
                ->execute();

            \Drupal::messenger()->addMessage(t("Currency has been succesfully saved."));
        }
        catch(\Exception $ex){
            \Drupal::logger('currency')->error($ex->getMessage());
        }
    }
}