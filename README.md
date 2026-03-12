# Blog CMS

Система управления блогом, написанная на чистом PHP с использованием паттерна MVC без фреймворков.

## Описание

Blog CMS — это демонстрационный проект, реализующий полноценную систему управления контентом для блога. Проект построен на чистом PHP 8.2 с ручной реализацией MVC-архитектуры, включая маршрутизатор, базовый контроллер, обертку над PDO и простой шаблонизатор.

### Возможности

- **Статьи**: CRUD-операции, поддержка черновиков и публикаций, SEO-поля (title, description)
- **Категории**: создание, удаление, привязка к статьям
- **Теги**: система тегов с many-to-many связью через промежуточную таблицу
- **Комментарии**: отправка комментариев к статьям, модерация (одобрение/отклонение)
- **Админ-панель**: управление контентом, дашборд со статистикой, модерация комментариев
- **SEO**: настраиваемые мета-теги для каждой статьи
- **CSRF-защита**: токены для всех форм

## Стек технологий

- **Backend**: PHP 8.2 (чистый MVC, без фреймворков)
- **База данных**: MySQL 8.0
- **Веб-сервер**: Nginx
- **Контейнеризация**: Docker + Docker Compose
- **Автозагрузка**: Composer (PSR-4)
- **Окружение**: vlucas/phpdotenv

## Структура проекта

```
blog-cms/
├── app/
│   ├── Controllers/        # Контроллеры (Article, Admin, Error)
│   ├── Core/               # Ядро MVC (Router, Database, Controller)
│   ├── Models/             # Модели (Article, Category, Tag, Comment)
│   └── Views/              # Шаблоны представлений
│       ├── layouts/        # Макеты (main, admin)
│       ├── articles/       # Публичные страницы статей
│       ├── admin/          # Страницы админ-панели
│       └── errors/         # Страницы ошибок
├── database/
│   └── schema.sql          # Схема БД и начальные данные
├── nginx/
│   └── default.conf        # Конфигурация Nginx
├── public/
│   ├── index.php           # Точка входа
│   └── assets/css/         # Стили
├── docker-compose.yml
├── Dockerfile
├── composer.json
└── .env.example
```

## Запуск проекта

### Требования

- Docker и Docker Compose

### Шаги

1. **Клонирование репозитория:**
   ```bash
   git clone https://github.com/cfvbhgc/blog-cms.git
   cd blog-cms
   ```

2. **Создание файла окружения:**
   ```bash
   cp .env.example .env
   ```

3. **Запуск контейнеров:**
   ```bash
   docker-compose up -d --build
   ```

4. **Установка зависимостей** (если не установились автоматически):
   ```bash
   docker-compose exec php composer install
   ```

5. **Открытие в браузере:**
   - Блог: [http://localhost:8080](http://localhost:8080)
   - Админ-панель: [http://localhost:8080/admin](http://localhost:8080/admin)

### Вход в админ-панель

- **Логин**: `admin`
- **Пароль**: `admin123`

(можно изменить в файле `.env`)

## База данных

Схема базы данных создается автоматически при первом запуске MySQL-контейнера. Включает начальные данные:

- 5 категорий
- 10 статей (9 опубликованных, 1 черновик)
- 10 тегов
- 8 комментариев

### Подключение к MySQL

```bash
docker-compose exec mysql mysql -u blog_user -pblog_secret blog_cms
```

Или через внешний клиент: `localhost:3307`, пользователь `blog_user`, пароль `blog_secret`.

## API маршрутов

### Публичные

| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/` | Список статей |
| GET | `/article/{slug}` | Просмотр статьи |
| POST | `/article/{slug}/comment` | Отправка комментария |
| GET | `/category/{slug}` | Статьи по категории |
| GET | `/tag/{slug}` | Статьи по тегу |

### Админ-панель

| Метод | URL | Описание |
|-------|-----|----------|
| GET | `/admin` | Дашборд |
| GET/POST | `/admin/login` | Авторизация |
| GET | `/admin/articles` | Список статей |
| GET | `/admin/articles/create` | Форма создания |
| POST | `/admin/articles/store` | Сохранение статьи |
| GET | `/admin/articles/{id}/edit` | Форма редактирования |
| POST | `/admin/articles/{id}/update` | Обновление статьи |
| GET | `/admin/categories` | Управление категориями |
| GET | `/admin/tags` | Управление тегами |
| GET | `/admin/comments` | Модерация комментариев |

## Остановка проекта

```bash
docker-compose down
```

Для удаления данных базы:

```bash
docker-compose down -v
```
