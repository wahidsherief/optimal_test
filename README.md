# 📚 Bookshelf API

A simple Laravel-based RESTful API to manage books and chapters.

## 🚀 Getting Started

Follow the steps below to install and run the Bookshelf API on your local machine.

### 🔧 Prerequisites

-   PHP >= 8.1
-   Composer
-   MySQL
-   Laravel CLI (optional but helpful)
-   Postman (for API testing)

## 📥 Installation Steps

### 1. Clone the Repository

git clone https://github.com/wahidsherief/optimal_test.git

### 2. Install PHP Dependencies

composer install

### 3. Copy the Environment File

cp .env.example .env

### 4. Generate Application Key

php artisan key:generate

### 5. Create the Database

-   Create a new MySQL database named `bookshelf_db`.

CREATE DATABASE bookshelf_db;

### 6. Configure `.env`

Edit your `.env` file and update the following lines:

DB_DATABASE=bookshelf_db
DB_USERNAME=your_mysql_username
DB_PASSWORD=your_mysql_password

### 7. Run Migrations

php artisan migrate

### 8. Seed the Database

php artisan db:seed

## 📮 Postman Setup

### 1. Import Postman Collection

-   Go to Postman.
-   Click Import.
-   Select the provided `.json` file: `bookshelf-api.postman_collection.json`.

### 2. Execute API Requests

-   Use the `login` route to obtain a Bearer token.
-   Set the Bearer token in the Authorization tab for protected routes.
-   Test endpoints like `GET /api/bookshelves`, `PUT /api/books/{bookId}/chapters/{chapterId}`, etc.

## 📄 API Documentation

Visit the Swagger UI at:

http://localhost:8000/api/documentation

## 🔄 CI/CD with GitHub Actions

Here’s the complete `CI/CD with GitHub Actions` section to add to your `README.md`, assuming SSH setup is already done on both GitHub and the target server:

---

## 🔄 CI/CD with GitHub Actions

### ✅ Assumptions

The following SSH setup is **already completed**:

-   SSH key pair has been generated.
-   Public key is added to the server's `~/.ssh/authorized_keys`.
-   Private key is stored in your GitHub repository secrets as `SSH_PRIVATE_KEY`.
-   The server IP address is reachable from GitHub Actions.
-   The server is pre-configured with necessary Laravel dependencies (`PHP`, `Composer`, etc.).

### ⚙️ GitHub Actions Workflow

The deployment logic is defined in `.github/workflows/deploy.yml`. Here's a brief overview of what it does:

### deploy.yml

```
name: Deploy to Dev Server

on:
push:
branches: - main

jobs:
deploy:
runs-on: ubuntu-latest

    steps:
      - name: Checkout Code
        uses: actions/checkout@v3

      - name: Set up SSH
        uses: webfactory/ssh-agent@v0.5.3
        with:
          ssh-private-key: ${{ secrets.SSH_PRIVATE_KEY }}

      - name: Add Server to Known Hosts
        run: ssh-keyscan -t rsa [your-server-ip] >> ~/.ssh/known_hosts

      - name: Test SSH Connection
        run: ssh -v -o StrictHostKeyChecking=no root@[your-server-ip] 'echo "SSH connection successful"'

      - name: Deploy to Server
        run: ssh -o StrictHostKeyChecking=no root@[your-server-ip] '[your-app-path]'
```
