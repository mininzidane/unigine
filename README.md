# README #

## Pre-launch steps

1. Run `docker-compose up -d --build`
    - Web is available by http://localhost:8013/
    - run `docker-compose exec php composer i`
    - run `docker-compose exec php bin/console doctrine:database:create`
    - run `docker-compose exec php bin/console doctrine:migrations:migrate`
2. There are unit tests. Execute by: `docker-compose exec php bin/phpunit tests`

### Configuration
- you can use any parser (cbr, ecb) you want by switching dependency in `config/services.yaml` for CurrencyImportCommand. cbr_parser is set by default

### Launching app
- to import currencies use console command:
```bash
docker-compose exec php bin/console import:currency
```
- REST API has the only end-point `http://localhost:8013/api/v1/convert/{from}/{to}/{amount}` where
`from` and `to` are currency codes (e.g. USD), `amount` is a float value for converting

### Tests
- run unit tests for parsers (run it on a clear db)
```bash
docker-compose exec php vendor/bin/phpunit tests/Unit/
```
- run functional tests for API (be sure to run import command first)
```bash
docker-compose exec php vendor/bin/phpunit tests/Functional/
```