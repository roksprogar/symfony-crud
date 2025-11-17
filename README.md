Should just work with:

```
docker compose up --build

```
docker exec -it symfony bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

Visit https://localhost/order/ and refresh the show order page 10 time to hit the rate limit.