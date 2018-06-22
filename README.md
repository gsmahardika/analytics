# Retrieve data from Google Analytics

Inspired by [spatie/laravel-analytics](https://github.com/spatie/laravel-analytics) with some modification.

If I violate something, please let me know. Thank you.

## How to obtain the credentials to communicate with Google Analytics

### Getting credentials

The first thing you’ll need to do is to get some credentials to use Google API’s. I’m assuming that you’ve already created a Google account and are signed in. Head over to [Google API’s site](https://console.developers.google.com/apis) and click "Select a project" in the header.

Next up we must specify which API’s the project may consume. In the list of available API’s click "Google Analytics API". On the next screen click "Enable".

Now that you’ve created a project that has access to the Analytics API it’s time to download a file with these credentials. Click "Credentials" in the sidebar. You’ll want to create a "Service account key".

On the next screen you can give the service account a name. You can name it anything you’d like. In the service account id you’ll see an email address. We’ll use this email address later on in this guide. Select "JSON" as the key type and click "Create" to download the JSON file.

Save the json inside your project at the location specified in the `credentials` key of the config file. Because the json file contains potentially sensitive information I don't recommend committing it to your git repository.

### Granting permissions to your Analytics property

I'm assuming that you've already created a Analytics account on the [Analytics site](https://analytics.google.com/analytics). Go to "User management" in the Admin-section of the property.

On this screen you can grant access to the email address found in the `client_email` key from the json file you download in the previous step. Read only access is enough.

### Getting the view id

The last thing you'll have to do is fill in the `view_id` in the config file. You can get the right value on the [Analytics site](https://analytics.google.com/analytics). Go to "View setting" in the Admin-section of the property.

## Usage

See [index.php](index.php) file.

## Todo

- [x] Cache results

## Testing

Run the tests with:

``` bash
vendor/bin/phpunit
```

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](https://github.com/spatie/laravel-analytics/graphs/contributors)

## License

The MIT License (MIT). Please see [License](LICENSE) file for more information.
