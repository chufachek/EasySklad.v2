# Easy Sklad (MVP Backend, PHP 5.6)

## Требования
- PHP 5.6+
- MySQL 5.7/8+
- Apache (желательно, для `.htaccess`)

## Быстрый старт

### 1) Создайте БД и импортируйте схему

```bash
mysql -u root -p < database.sql
```

> В `database.sql` уже есть `CREATE DATABASE easy_sklad` и тестовые данные.

### 2) Настройте переменные окружения

Скопируйте файл примера и обновите значения:

```bash
cp public_html/config/env.example .env
```

Минимально проверьте:
- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `JWT_SECRET`
- `MAX_COMPANIES_PER_OWNER`

### 3) Запуск

**Apache:**
- Укажите `DocumentRoot` на `public_html`.
- `.htaccess` уже настроен на роутинг.

**Если не Apache:**
- Прокиньте все запросы на `public_html/index.php` через nginx или встроенный сервер PHP.

Пример встроенного сервера PHP (для локальной разработки):

```bash
php -S localhost:8080 -t public_html
```

## Авторизация

JWT выдается на `POST /api/auth/register` и `POST /api/auth/login`.
Токен отправляйте в заголовке:

```
Authorization: Bearer <token>
```

## Единый формат ответа

**Успех**
```json
{ "ok": true, "data": { ... }, "meta": { ... } }
```

**Ошибка**
```json
{ "ok": false, "error": { "code": "VALIDATION_ERROR", "message": "...", "fields": { ... } } }
```

## Эндпоинты

### Auth
- `POST /api/auth/register` `{ name, email, password }`
- `POST /api/auth/login` `{ email, password }`
- `POST /api/auth/logout`
- `GET /api/me`

### Companies
- `GET /api/companies`
- `POST /api/companies` `{ name, inn?, address? }`
- `PUT /api/companies/:id` `{ name, inn?, address? }`

### Warehouses
- `GET /api/companies/:companyId/warehouses`
- `POST /api/companies/:companyId/warehouses` `{ name, address? }`
- `PUT /api/warehouses/:id` `{ name, address? }`
- `DELETE /api/warehouses/:id`

### Products
- `GET /api/warehouses/:warehouseId/products?search=&page=&limit=`
- `POST /api/warehouses/:warehouseId/products` `{ sku, name, price, cost?, unit?, min_stock? }`
- `PUT /api/products/:id` `{ sku, name, price, cost?, unit?, min_stock? }`
- `GET /api/products/search?q=&warehouseId=`

### Income
- `GET /api/warehouses/:warehouseId/income?page=&limit=`
- `POST /api/warehouses/:warehouseId/income`

Пример тела запроса:
```json
{
  "supplier": "Поставщик",
  "date": "2024-01-01",
  "items": [
    { "product_id": 1, "qty": 10, "cost": 50 }
  ]
}
```

### Orders
- `GET /api/warehouses/:warehouseId/orders?status=&page=&limit=`
- `POST /api/warehouses/:warehouseId/orders`
- `PUT /api/orders/:id/status` `{ status: "draft"|"paid"|"canceled" }`

Пример тела запроса:
```json
{
  "customer_name": "Иван",
  "payment_method": "cash",
  "discount": 0,
  "items": [
    { "product_id": 1, "qty": 2, "price": 100 }
  ],
  "services": [
    { "service_id": 1, "qty": 1, "price": 500 }
  ]
}
```

### Services
- `GET /api/companies/:companyId/services`
- `POST /api/companies/:companyId/services` `{ name, price, description? }`
- `PUT /api/services/:id` `{ name, price, description? }`
- `DELETE /api/services/:id`

## Тестовые данные
- Пользователь: `test@example.com`
- Пароль: `password123`

## Логи
Ошибки пишутся в `storage/logs/app.log`.
