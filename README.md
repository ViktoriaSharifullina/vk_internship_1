# REST API Сервис

Небольшой REST API сервис, учитывающий выполнение заданий пользователями. Система позволяет добавлять, просматривать и отмечать задания как выполненные, причём каждое задание имеет установленный уровень сложности. В зависимости от этого уровня, при выполнении задания пользователь получает баллы, рассчитываемые как базовая стоимость задания, умноженная на коэффициент сложности.

## Технологический стек

- Php
- Laravel
- Docker
- MySQL
- Nginx

## Требования

Перед началом работы убедитесь, что у вас установлены:

- Docker
- Docker Compose

## Запуск проекта

1. Клонируйте репозиторий:
   ```bash
   git clone git@github.com:ViktoriaSharifullina/vk_internship_1.git
   cd vk_internship_1

2. Соберите, создайте и запустите контейнер:
   ```bash
   docker-compose build
   docker-compose up -d

3. Выполните миграции базы данных:
   ```bash
   docker-compose exec app php artisan migrate
   
## Тестирование
Чтобы запустить unit тесты, используйте следующую команду:
  ```bash
  docker-compose exec app bash
  php artisan test
```
## API Endpoints
### Пользователи
#### Создание пользователя
URL: `http://localhost:8080/users`

Метод: POST

**Тело запроса:**
```json
{
    "name": "Test User",
    "balance": 0
}
```
**Тело ответа**
```json
{
    "name": "Test User",
    "balance": 0,
    "updated_at": "2024-03-18T10:59:41.000000Z",
    "created_at": "2024-03-18T10:59:41.000000Z",
    "id": 1
}
```

#### Получение данных пользователя
URL: `http://localhost:8080/users/{userId}`

Метод: Get

**Тело ответа**
```json
{
    "id": 1,
    "name": "Test User",
    "balance": "0.00",
    "created_at": "2024-03-18T10:59:41.000000Z",
    "updated_at": "2024-03-18T10:59:41.000000Z"
}
```

#### Получить задания, выполненные пользователем, и баланс
URL: `http://localhost:8080//users/{userId}/completed-quests`

Метод: Get

**Тело ответа**
```json
{
    "completedQuests": [
        {
            "id": 1,
            "user_id": 1,
            "quest_id": 1,
            "created_at": null,
            "updated_at": null,
            "quest": {
                "id": 7,
                "name": "Test Quest 1",
                "cost": "350.00",
                "created_at": "2024-03-18T13:34:55.000000Z",
                "updated_at": "2024-03-18T13:34:55.000000Z",
                "difficulty": "expert"
            }
        }
    ],
    "balance": "700.00"
}
```

### Задания

#### Получить список всех заданий
URL: `http://localhost:8080/quests`

Метод: Get

**Тело ответа**
```json
[
    {
        "id": 1,
        "name": "Test Quest 1",
        "cost": "350.00",
        "created_at": "2024-03-18T13:34:55.000000Z",
        "updated_at": "2024-03-18T13:34:55.000000Z",
        "difficulty": "expert"
    },
    {
        "id": 2,
        "name": "Test Quest 2",
        "cost": "50.00",
        "created_at": "2024-03-18T13:41:25.000000Z",
        "updated_at": "2024-03-18T13:41:25.000000Z",
        "difficulty": "easy"
    }
]
```

#### Создание задания
URL: `http://localhost:8080/quests`

Метод: POST

**Тело запроса:**
```json
{
  "name": "Test Quest 1",
  "cost": 350,
  "difficulty": "expert"
}
```
**Тело ответа**
```json
{
    "name": "Test Quest 1",
    "cost": 350,
    "difficulty": "expert",
    "updated_at": "2024-03-18T13:34:55.000000Z",
    "created_at": "2024-03-18T13:34:55.000000Z",
    "id": 1
}
```

#### Выполнение задания пользователем
URL: `http://localhost:8080/quests/complete`

Метод: POST

**Тело запроса:**
```json
{
    "user_id" : 7,
    "quest_id" : 7
}
```
**Тело ответа**
```json
{
    "message": "Quest completed successfully"
}
```


