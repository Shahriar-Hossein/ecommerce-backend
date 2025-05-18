# E-commerce Backend

A robust Laravel 11 backend for an e-commerce platform, featuring RESTful APIs, role-based access control, secure media handling, queued mailing, and interactive Swagger documentation.

<p align="center">
  <img src="docs/images/api-endpoints-1.png" alt="API Endpoint Screenshot 1" width="48%" />
  <img src="docs/images/api-endpoints-2.png" alt="API Endpoint Screenshot 2" width="48%" />
</p>

---

## 🔧 Tech Stack

- [Laravel 11](https://laravel.com) – PHP web framework
- [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum) – API authentication
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v5/introduction) – Role & permission management
- [Spatie Laravel Media Library](https://spatie.be/docs/laravel-medialibrary/v9/introduction) – Media upload & association
- [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger) – API documentation generator

> ⚠️ This project is in development and not ready for production use.

## Installation

1. Clone the repository
```
https://github.com/Shahriar-Hossein/ecommerce-backend.git
```

2. change to project directory
```
cd ecommerce-backend
```

3. Install dependencies
```
composer install
```

4. Create a `.env` file
```
cp .env.example .env
```

5. Generate an application key
```
php artisan key:generate
```

6. Create a database and update the `.env` file with your database credentials

7. Run the migrations
```
php artisan migrate
```

8. Seed the database
```
php artisan db:seed
```

9. Permit storage directory
```
php artisan storage:link
```

10. Generate api documentation
```
php artisan l5-swagger:generate
```

11. Start the server
```
php artisan serve
```

## API Documentation

The API documentation is generated using the DarkaOnLine L5 Swagger Laravel API Documentation Generator package. You can access the documentation by visiting `http://localhost:8000/api/documentation`.

## Testing

To run the tests, run the following command
```
php artisan test
```

## Contributing

If you would like to contribute to this project, please fork the repository and submit a pull request.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

