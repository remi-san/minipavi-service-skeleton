# MiniPavi service skeleton

A Minitel service to start from, built on the
[MiniPavi service framework](https://github.com/remi-san/minipavi-service)
(`remi-san/minipavi-service`): a home page asking for the user's name and greeting them, with
its controller, messages, container and functional test.

## Creating a service

```sh
composer create-project remi-san/minipavi-service-skeleton MyService --stability=dev
```

It comes from [Packagist](https://packagist.org/packages/remi-san/minipavi-service-skeleton);
`--stability=dev` takes the skeleton's `main`.

The service needs PHP 8.4 or later (lazy objects), with the `mbstring` and `xml` extensions.

| Path | Role |
|---|---|
| `public/index.php` | entry point: the framework's `KernelBuilder`, with the logger, `CONTEXT_SECRET`, and the URLs and emulator of the environment |
| `config/container.php` | the routes and controllers (PHP-DI, lazy controllers), the messages, the environment |
| `config/pages/home.xml` | the home page (`type="template"`) |
| `config/messages.xml`, `src/AppMessage.php` | the texts shown on screen, and their enum |
| `src/Controller/HomeController.php` | the home screen's logic |
| `tests/HomeTest.php` | a user's session, with the framework's `ServiceTester` |

Rename it in `composer.json` (`name`, and the `App\` namespace if you wish). The framework's
README (`vendor/remi-san/minipavi-service/README.md`) documents the page XML format,
controllers, messages and the rest of its API.

## Checks

```sh
composer test      # functional tests (the framework's ServiceTester)
composer lint      # its pages (the framework's minipavi lint)
composer cs        # formatting (Mago); composer cs-fix formats
composer analyse   # Mago's linter and static analyzer (mago.toml)
```

## Running it

A MiniPavi gateway calls the service at the URL of its `public/` folder. To run it on your
machine with a local gateway and Minitel emulator, run it in its own Docker stack beside
[minipavi-stack](https://github.com/remi-san/minipavi-stack), which proxies it.

| Path | Role |
|---|---|
| `compose.yaml` | nginx (`web`) and PHP-FPM (`php`), joined to the stack's `minipavi` network as `my-service` |
| `Dockerfile` | the PHP-FPM image (PHP 8.4), which runs the service's folder, mounted as it is |
| `docker/nginx.conf` | sends every request to `public/index.php`, with the host and path the stack's proxy was asked for |

1. Rename the alias `my-service` in `compose.yaml`: each service on the network needs its own.
2. Start minipavi-stack first (it creates the `minipavi` network), then the service:
   ```sh
   echo "CONTEXT_SECRET=$(openssl rand -hex 32)" > .env   # key signing the context
   docker compose up -d --build
   ```
   The service's folder is mounted as it is, `vendor/` included: what you change (code,
   pages, `composer install`/`update`) is served right away, without rebuilding.
3. List it in the stack's `config/local-services.xml`, with a code and the alias, then restart
   the stack's `services` container (`docker compose up -d --force-recreate services`):
   ```xml
   <services>
   	<service code="MYSERVICE">
   		<description>My service</description>
   		<url>http://my-service/</url>
   	</service>
   </services>
   ```

The gateway then reaches it at `http://services/MYSERVICE/`, and a browser at
http://localhost:8083/MYSERVICE/ opens it in the local emulator. On the Minitel, type its code
on the home page, pick it in the directory (*Guide*), or make it the gateway's default service
(`MINIPAVI_DEFAULT_URL=http://services/MYSERVICE/` in the stack's `.env`).

The stack's proxy strips `/MYSERVICE` and sends the host and path it was asked for in
`X-Forwarded-Host` and `X-Forwarded-Prefix`. nginx gives them back to PHP (`HTTP_HOST`,
`PHP_SELF`, `REQUEST_URI`), so the next URL the service gives the gateway is
`http://services/MYSERVICE/index.php`. It trusts these headers only from Docker's private
networks. Errors go to `docker compose logs php`. minipavi-stack's README ("Your own
services") describes the proxy.
