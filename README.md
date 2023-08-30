# sf_travel

## Run project

### Set .env file for docker

```sh
cp docker/.env.dist docker/.env
```

### Run docker containers

```sh
cd docker && docker-compose up -d
```

## Tests

### Run all tests

```sh
cd docker && docker-compose exec php composer tests
```

### Run phpunits

```sh
cd docker && docker-compose exec php composer phpunit
```

### Run rector

```sh
cd docker && docker-compose exec php composer rector
```

### Run phpstan

```sh
cd docker && docker-compose exec php composer phpstan
```

### Run csfixer

```sh
cd docker && docker-compose exec php composer csfix
```
