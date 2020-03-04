# README #

## Pre-launch steps

### Install dependencies 
```bash 
composer install
```

### Configuration
- make a copy of .env to .env.local and enter your db credentials to `DATABASE_URL`
- create db and run migrations
```bash
php bin/console doctrine:migrations:migrate
```
- you can use any parser (ecb, cbr) you want by switching dependency in `config/services.yaml` for CurrencyImportCommand. cbr_parser is set by default

### Launching app
- to import currencies use console command:
```bash
php bin/console import:currency
```
- REST API has the only end-point `/api/v1/convert/{from}/{to}/{amount}` where
`from` and `to` are currency codes (e.g. USD), `amount` is a float value for converting

### Tests
- run unit tests for parsers (run it on clear db)
```bash
vendor/bin/phpunit tests/Unit/
```
- run functional tests for API (be sure to run import command first)
```bash
vendor/bin/phpunit tests/Functional/
```