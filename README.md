# sf_travel

> [!NOTE]
> Easy API application for to getting travel data. 
> You can give information where you would like to go, 
> and the app should return for you information about for this place. 
> 
> Sf_travel returns information such as:
> 1. Trips
> 2. Hotels
> 3. Flights
> 4. Curiosities
> 5. Weathers
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
>    * Please, check available endpoints at this [link](http://localhost/) (You should first run the project).

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
docker-compose -f docker/docker-compose.yml build --compress
```

### Run docker containers

```sh
docker-compose -f docker/docker-compose.yml up -d
```

### Install packages

```sh
docker-compose -f docker/docker-compose.yml exec backend composer install
```

## Run migrations

```sh
docker-compose -f docker/docker-compose.yml exec backend ./bin/console doctrine:migration:migrate -n
```

## Frontend

> [!NOTE]
> If you want to see, how the frontend works. Please check [the frontend repository](https://github.com/gorskimarcin96/vue_travel).

## Tests

### Run all tests

```sh
docker-compose -f docker/docker-compose.yml exec backend composer tests
```

### Run phpunits

```sh
docker-compose -f docker/docker-compose.yml exec backend composer phpunit
```

### Run rector

```sh
docker-compose -f docker/docker-compose.yml exec backend composer rector
```

### Run phpstan

```sh
docker-compose -f docker/docker-compose.yml exec backend composer phpstan
```

### Run csfixer

```sh
docker-compose -f docker/docker-compose.yml exec backend composer csfix
```

### Run behat 

```sh
docker-compose -f docker/docker-compose.yml exec backend composer behat
```

#### Report test coverage
![coverage.png](public%2Fcoverage.png)
