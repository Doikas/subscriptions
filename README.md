# Subscriptions

A Laravel subscription management application with Orchid dashboard support, Excel import workflows, and automated Gmail OAuth2 email reminders.

## Features

- Subscription management using Orchid admin UI
- Customer and service records with searchable filters
- Automatic email reminders for expiring subscriptions
- Gmail OAuth2 integration for secure SMTP delivery
- Excel import support for customers, services, and subscriptions
- Email log tracking and scheduled cleanup
- Artisan commands for import and automation

## Built With

- Laravel 9
- Orchid Platform
- Google API Client + OAuth2
- Maatwebsite Excel
- Symfony Mailer
- Vite

## Requirements

- PHP ^8.0.2
- Composer
- Node.js / npm
- A database supported by Laravel
- Gmail account with Gmail API enabled
- Google OAuth credentials

## Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/Doikas/subscriptions.git
    cd subscriptions
    ```
2. Install PHP dependencies:
    ```bash
    composer install
    ```
3. Install frontend dependencies:
    ```bash
    npm install
    ```
4. Copy the example environment file and generate an application key:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
5. Configure `.env` values for your database, mail, and Google OAuth settings.

## Environment Configuration

Add or update the following values in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_FROM_ADDRESS=your@email.com
MAIL_FROM_NAME="Subscriptions App"
MAIL_USERNAME=your_gmail_address

GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/oauth2callback
GOOGLE_REFRESH_TOKEN=
```

> The app uses Google OAuth2 to send email reminders securely through Gmail.

## Google OAuth Setup

1. Create a Google Cloud project.
2. Enable the Gmail API.
3. Create OAuth 2.0 credentials.
4. Set the redirect URI to `http://localhost:8000/oauth2callback`.
5. Start your app and visit:
    ```bash
    php artisan serve
    ```
6. Open in your browser:
    ```url
    http://localhost:8000/auth/google
    ```
7. Complete authorization and let the app save `GOOGLE_REFRESH_TOKEN` to your `.env` file.

## Database Setup

Run migrations:

```bash
php artisan migrate
```

If Orchid publishes are needed:

```bash
php artisan orchid:publish
```

## Importing Data

Import data from XLSX files using Artisan commands:

- `php artisan import:customers`
- `php artisan import:services`
- `php artisan import:subscriptions`

Make sure the source files exist in the expected paths before running imports:

- `app/customerimp.xlsx`
- `app/servicesimp.xlsx`
- `app/subscriptionsimp.xlsx`

## Scheduling and Automation

The scheduler is configured in `app/Console/Kernel.php`:

- `auto:subscription-expiration-reminder` runs every minute
- `email-logs:delete-old` runs daily

To run the scheduler manually:

```bash
php artisan schedule:run
```

## Artisan Commands

- `php artisan auto:subscription-expiration-reminder`
- `php artisan email-logs:delete-old`
- `php artisan import:customers`
- `php artisan import:services`
- `php artisan import:subscriptions`

## Routes

- `/auth/google` — start Google OAuth authorization
- `/oauth2callback` — Google OAuth callback handler

## Running the App

Start the Laravel development server:

```bash
php artisan serve
```

Start Vite for local assets:

```bash
npm run dev
```

## Notes

- Email delivery is handled using OAuth2 access tokens for Gmail.
- The app stores email logs and cleans older logs automatically.
- Orchid provides a polished admin UI for managing subscriptions, customers, services, and logs.

## License

MIT
