![Nova Packages- Discover new packages. Build amazing things.](novapackages-banner.png?version=1)

# Nova Packages

## Setting up Passport

1. `php artisan passport:keys`
2. `php artisan passport:client --personal`

## Setting up Scout with Typesense

1. Set up a Typesense instance locally or on a server. For local development, you can use https://github.com/tighten/takeout to run Typesense.
2. Update the `.env` variables for your Typesense instance.
3. After seeding your database, run `php artisan scout:import "App\Package"`

## Seeding the Database

1. `php artisan db:seed`

## Setting up GitHub Authentication

1. Make a new [GitHub OAuth application](https://github.com/settings/developers)
2. Set `http://novapackages.test/login/github/callback` as the Authorized Callback URL
3. Copy the GitHub app id and secret to `GITHUB_CLIENT_ID` and `GITHUB_CLIENT_SECRET` in the `.env` file
4. Create a new [Personal Access Token](https://github.com/settings/tokens) with the `public_repo` scope enabled
5. Copy the token to `GITHUB_TOKEN` in the `.env` file.

## Setting up Slack Test Webhook

1. Add the `SLACK_URL` variable to your `.env` to post to a Slack channel of your choosing.

**Note:** This webhook is hit when certain events are fired. If you are not testing this webhook specifically, you may want to consider commenting it out to avoid sending unnecessary Slack notifications.

## Setting up the Filesystem for Screenshots

1. Run `php artisan storage:link`

## Testing

Some of the tests in this suite depend on an active internet connection, and will run by default. `tests/Feature/RepoTest.php` provides coverage for the ReadMe import feature, and `tests/Feature/CheckPackageUrlsCommandTest.php` provides coverage for the command that periodically validates package urls.

For convenience, these tests have been added to the `integration` group. If you would like to exlude these tests from running, you may do so by using phpunit's `--exclude-group` option:

```
phpunit --exclude-group=integration
```
