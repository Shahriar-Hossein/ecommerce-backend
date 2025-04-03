<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://repository-images.githubusercontent.com/145153231/6af073b6-c320-4289-a0e1-40ee647b7f35" width="400" alt="DevSpace Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Devspace E-commerce Backend

This is the backend of the Devspace E-commerce project. It is built with Laravel 11 and uses the Laravel Sanctum package for API authentication.

- [Laravel](https://laravel.com)
- [Laravel Sanctum](https://laravel.com/docs/8.x/sanctum)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v5/introduction)
- [Spatie Laravel Media Library](https://spatie.be/docs/laravel-medialibrary/v9/introduction)
- [DarkaOnLine L5 Swagger Laravel API Documentation Generator](https://github.com/DarkaOnLine/L5-Swagger)

The frontend of the project is built with Next.js and can be found [here](https://github.com/Parameter-IT/devspace-ecom-frontend).
This project is still in development and is not yet ready for production.

## Installation

1. Clone the repository
```
git@github.com:Parameter-IT/devspace-ecom-backend.git
```

2. change to project directory
```
cd devspace-ecom-backend
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

