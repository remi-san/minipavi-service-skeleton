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
machine with a local gateway and Minitel emulator, create it in the `services/` folder of
[minipavi-stack](https://github.com/remi-san/minipavi-stack) and start the stack:

```sh
composer create-project remi-san/minipavi-service-skeleton services/MyService --stability=dev
docker compose up -d --build services   # http://localhost:8083/MyService/ (the gateway: http://services/MyService/)
```

To reach it from the Minitel there, add it to `services/MiniPaviAcc/config/services.xml` (code
and URL `http://services/MyService/`), or make it the gateway's default service
(`MINIPAVI_DEFAULT_URL` in `.env`). Deploying it on a server with nginx and PHP-FPM is
described in minipavi-stack's README ("Deploying a service").
