## Environment Setup:

```bash
cp .env.example .env
php artisan key:generate
Update your .env file with your database credentials.
```

### Run Migrations:

```Bash
php artisan migrate
```

### Start the Development Server:
```Bash
php artisan serve
```

## Project Structure Highlights
`app/Models`: Contains core data models (Admin, Booking, Line, Station, ServiceWindow).

`app/Http/Controllers/Api`: Dedicated API controllers for Lines, Bookings, and Admin functions.

`routes/api.php`: API endpoints with middleware protection for admin routes.