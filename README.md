# currency_module
Drupal 9: custom module to manage currency with the help of Fixer API.

#### MODULE CREATES:
1. Database table 'currency' to store retrieved currency codes and rates
2. Menu group 'Currency module settings' with 2 links linked to pages below
         `path: /admin/config/development/currency`
3. Pages:
   1. Currency Form - A page to add or update currency value in Database
         `path : /currency/form`
   2. Currency List - A page displays all currency values stored in Database
          `path: /currency/list`
4. Fixer API Client to consume Fixer API endpoint
5. Drupal service to convert amount from one currency to another one via currency provider API (fixer.io).
   Ex:
   ```
   $converter = \Drupal::service('currency_service.provider');
   $converter->convert($amount, $from, $to);
   ```
6. Cron job that runs once a day: retrieves currency rates via Fixer API and updates currency values stored in DB
7. A page with hard-coded amount to test converting which is provided by Drupal service (№5) `path: /test_service/`

#### PRE-INSTALL
1. Go to **[Fixer web-site](https://fixer.io/)**
2. Create an account and generate API_KEY
3. Go to sites/default/settings.php and add a line:
    ```
   $settings['currency_provider.fixer_api_key'] = GENERATED_FIXER_API_KEY;
    ```
5. Install module & flush caches


