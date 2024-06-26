# Contributing

We welcome outside contributions to making this plugin and theme better.

If you have any questions or would like support while contributing, please submit an issue in [GitHub Issues](https://github.com/Automattic/wpcloud-station/issues).

Please make sure to abide by our [Code of Conduct](https://github.com/Automattic/wpcloud-station/blob/trunk/CODE-OF-CONDUCT.md) at all times to ensure a healthy and productive project.

## Installing Locally

We provide a development setup for both WP-Env and Docker.

### Docker

1. Navigate to the root directory.
2. Run `docker-compose up -d` to start the WordPress container, MariaDB container, and PHPMyAdmin container.
3. Open a web browser and navigate to <http://localhost:8282/> and set up your new WordPress Site.
4. Navigate to Plugins and activate WP Cloud Station.
5. Navigate to Themes and activate WP Cloud Station.
6. Navigate to `/wp-admin/admin.php?page=wpcloud_admin_settings`.
7. Save your WP Cloud Client Name and API Key, as well as a default primary Domain.
8. Make note of your IP Address at the bottom and contact your WP Cloud representative to add the IP's.

## wp-Env

[wp-env](https://www.npmjs.com/package/@wordpress/env) lets you easily set up a local WordPress environment for building and testing plugins and themes. It's simple to install and requires no configuration.

Please read through the above documentation for how to get started.

```sh
cd /path/to/wpcloud-station
npm -g i @wordpress/env
wp-env start
```

## XDebug

Debugging is included with the wp-env setup. A default `launch.json` for VS Code is included with the repository.

To get started with debugging, see the above wp-env documentation, install the [XDebug](https://marketplace.visualstudio.com/items?itemName=felixfbecker.php-debug) extension in VS Code, and start wp-env with xdebug:

```sh
wp-env start --xdebug
```
