# sf_travel

> [!NOTE]
> Easy API application for to getting travel data. 
> You can give information where you would like to go, 
> and the app should return for you information about for this place. 
> 
> Sf_travel returns information such as:
> 1. Trips
> 2. Hotels
> 3. Flights (in the future)
> 4. Last minute offers (in the future)
> 5. Curiosities
> 
> The application doesn't require authorization.
> 
> What are you must do: 
> 1. You should send payload to **POST /search**.
>    * Payload requires a nation and a place.
>    * Application has been created for the polish user. You should use Polish words for using searcher.
> 2. You should wait for getting data by queuer.
>    * You can follow field **finished**.
> 3. And you can get data from other endpoints.
>    * Please, check available endpoints at this [link](http://localhost/).

> [!WARNING]
> This project has been creating for practise programming skills, fun and private goals. 
> It doesn't use for commercial purposes.


## Run project

### Set .env file for docker

```sh
cp docker/.env.dist docker/.env
```

### Build docker images

```sh
cd docker && docker-compose build --compress
```

### Run docker containers

```sh
cd docker && docker-compose up -d
```

### Install packages

```sh
cd docker && docker-compose exec backend composer install
```

## Frontend

> [!NOTE]
> If you want to see, how the frontend works, click [this link](https://github.com/gorskimarcin96/vue_travel).

### Run migrations

```sh
cd docker && docker-compose exec backend ./bin/console doctrine:migration:migrate -n
```

## Tests

### Run all tests

```sh
cd docker && docker-compose exec backend composer tests
```

### Run phpunits

```sh
cd docker && docker-compose exec backend composer phpunit
```

### Run rector

```sh
cd docker && docker-compose exec backend composer rector
```

### Run phpstan

```sh
cd docker && docker-compose exec backend composer phpstan
```

### Run csfixer

```sh
cd docker && docker-compose exec backend composer csfix
```
