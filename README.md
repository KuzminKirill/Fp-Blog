# Laravel Blog API

A minimalistic blog API built with Laravel, featuring user authentication, post CRUD operations, a tagging system, and localization (English and Italian). This project uses Laravel Sanctum for token-based authentication and supports automatic tag creation.

## Features

- **User Authentication**: Register, login, and logout via API.
- **Post Management**: Create, read, update, and delete posts with titles, content, and tags.
- **Tagging System**: Add tags by name; non-existent tags are created automatically.
- **Localization**: Supports English (`en`) and Italian (`it`) via `Accept-Language` header or `?lang` query parameter.
- **RESTful API**: JSON-based endpoints with proper status codes and error messages.

## Requirements

- PHP 8.4.1+
- Composer
- MySQL or SQLite (for local testing)
- macOS (Instructions will be for Intel-based as developer using, you should adjust paths for other systems)
- Git

## Installation

### Step 1: Clone the Repository

To get started, clone the repository to your local machine:

```bash
git clone <your-repo-url> blog-api
cd blog-api
```

## Step 2: Install Dependencies

Install the required PHP dependencies using Composer:

```bash
composer install
```

Ensure Composer is installed globally (`composer --version` to check).

## Step 3: Set Up Environment

### 3.1 Copy the Environment File

Create a `.env` file from the example:

```bash
cp .env.example .env
```

### 3.2 Configure the .env File

Edit `.env` with your database details:

#### For MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_api
DB_USERNAME=root
DB_PASSWORD=
```

#### For SQLite (simpler local setup):

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

Create the SQLite file if using SQLite:

```bash
touch /absolute/path/to/database.sqlite
```

### 3.3 Generate Application Key

Generate a unique application key:

```bash
php artisan key:generate
```

## Step 4: Run Migrations

Run database migrations to create tables:

```bash
php artisan migrate
```

This creates `users`, `posts`, `tags`, and `post_tag` tables.

## Step 5: Install Sanctum

Set up Laravel Sanctum for authentication:

### 5.1 Install Sanctum Package

```bash
composer require laravel/sanctum
```

### 5.2 Publish Sanctum Configuration

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 5.3 Run Additional Migrations

```bash
php artisan migrate
```

## Step 6: Start the Server

Launch the Laravel development server:

```bash
php artisan serve
```

Access the API at [http://127.0.0.1:8000](http://127.0.0.1:8000).

## API Usage

There is a ***Blog app.postman_collection.json*** in root of this app.
This is postman collection might be useful for testing.

### Authentication

#### Register a User

Create a new user account:

```bash
curl -X POST http://127.0.0.1:8000/api/register \
     -H "Content-Type: application/json" \
     -d '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}'
```

Response:

```json
{"token": "...", "user": {...}}
```

#### Login

Authenticate an existing user:

```bash
curl -X POST http://127.0.0.1:8000/api/login \
     -H "Content-Type: application/json" \
     -d '{"email":"test@example.com","password":"password123"}'
```

Response:

```json
{"token": "...", "user": {...}}
```

#### Logout

End the authenticated session (requires token):

```bash
curl -X POST http://127.0.0.1:8000/api/logout \
     -H "Authorization: Bearer <token>"
```

## Post Management

### Create a Post

Add a new post (requires token):

```bash
curl -X POST http://127.0.0.1:8000/api/posts \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <token>" \
     -d '{"title":"My First Post","content":"Lorem ipsum","tags":["Tag1","Tag2"]}'
```

Response:

```json
{"message": "Post created successfully", "post": {...}}
```

Tags are auto-created if they don’t exist.

### Get All Posts

Retrieve all posts (publicly accessible):

```bash
curl http://127.0.0.1:8000/api/posts
```

Response: Array of posts.

### Update a Post

Modify an existing post (requires token; owner only):

```bash
curl -X PUT http://127.0.0.1:8000/api/posts/1 \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -H "Authorization: Bearer <token>" \
     -d '{"title":"Updated Post","content":"Updated content","tags":["Tag3","NewTag"]}'
```

Response:

```json
{"message": "Post updated successfully", "post": {...}}
```

### Delete a Post

Remove a post (requires token; owner only):

```bash
curl -X DELETE http://127.0.0.1:8000/api/posts/1 \
     -H "Authorization: Bearer <token>"
```

## Localization

Switch response language using headers or query parameters:

### Using Accept-Language Header

```bash
curl http://127.0.0.1:8000/api/posts -H "Accept-Language: it"
```

### Using Query Parameter

```bash
curl "http://127.0.0.1:8000/api/posts?lang=it"
```

Responses use translations from `lang/en/messages.php` or `lang/it/messages.php`.

## Running Tests

### Set Up Test Environment

#### Copy Environment File for Testing

```bash
cp .env .env.testing
```

#### Configure .env.testing

Use an in-memory SQLite database:

```env
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### Run Tests

Execute tests:

```bash
./vendor/bin/phpunit tests/Feature/PostApiTest.php
```

Tests cover post creation, updates, and authorization.

## Design Decisions

- **Sanctum**: Used for simple token-based authentication.
- **Tag Creation**: Tags are dynamically created with `firstOrCreate`.
- **Localization**: Handled via `SetLocale` middleware.

## What can be updated to make project better
- ***Hide all envs!!!*** (they are open just to make preparation easy)
- Delete things that don't needed from laravel
- Add nginx or roadrunner to project like it done here https://github.com/KuzminKirill/Site-visit-counter-project
- Use docker for dev env to make installation easier
- Add error handling for more readable errors
- If project becomes bigger - separate logic (like DDD or different way)
- Clean up files that autogenerated
