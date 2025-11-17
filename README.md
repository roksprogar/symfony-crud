Should just work with:

```
docker compose up --build -d
docker exec -it symfony bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

Play around with CRUD and refresh https://localhost/order/ 10 times to hit the rate limit (this should be on the new page, but easier to test like this).