# thovex
Technical test for second-stage interview


# Set-up instructions

1) Run `npm install` on the root
2) From `app` folder, run `composer install`
3) Update `app/config/app_local.php` to update the `Datasources` with DB credentials and add the google maps api key at the end (see code structure below)
```
    'EmailTransport' => [
        ...
    ],

    // Add this array to the config and update the API key
    'GoogleMaps' => [
        'api' => [
            'key' => "YOUR_API_KEY",
        ],
    ],
```
4) Update `app/phinx.php` to update the DB credentials
    - This is Cake's internal database migration tool
5) From `app` folder, run `vendor/bin/phinx migrate`
    - This will run the database migrations, creating the database structure for the project.

Website should load now and all functionality should work.


For running tests:
- From the `app` folder run `vendor/bin/phinx migrate -e testing`
    - This will run the database migrations on the test database
- From the `app` folder run `vendor/bin/phpunit`
