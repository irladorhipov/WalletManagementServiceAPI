# WalletManagementServiceAPI

## Технологии

- **PHP 8.2**
- **Postgres 16**
- **Symfony 7.2.4**

## Пакеты

Для работы с деньгами использован пакет: `"moneyphp/money"

## Инструкция по развороту

 ```bash
   1. make up
   2. docker exec -ti symfony_app bash
   3. php bin/console doctrine:database:create
   4. php bin/console doctrine:migrations:migrate
   5. php bin/console doctrine:fixtures:load
```

## Документация:
http://localhost:8080

## SQL query

```bash
SELECT currency, SUM(amount::numeric) AS total_refund_amount
FROM transaction
WHERE reason = 'refund'
AND created_at >= NOW() - INTERVAL '7 days'
GROUP BY currency;
```