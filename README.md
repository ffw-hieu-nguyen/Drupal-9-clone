# Drupal-9-clone
# Composer template for Drupal projects

# Installation:

## Docker

## Change `docker-compose.win.yml` to `docker-compose.yml`
## In `docker-compose.yml` uncomment mariadb service -> mariadb-init and copy init .sql file(s)
## Change `.env.sample` to `.env`

Start docker

```
docker-compose up
```

Login php container with root

```
docker-compose exec php sh
```

Run composer install for applying patch

```
composer install
```

## drupal.localhost

