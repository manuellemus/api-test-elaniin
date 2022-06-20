## Restful API to manage products and users.
RestFul API to manage a list of products and users, using JWT authentication, built in Laravel 9.x

## Requirements
* PHP>=8.0.12
* MySQL > 5.7
* Laravel 9.x
* Composer 

## Download for development mode
* clone to project
'''
https://github.com/manuellemus/api-test-elaniin.git
'''

## Instaling dependencies
```
composer update
```
## Generate key to laravel
* accesssing the project directory
```
cd api-test-elaniin/
```
* configure environment in .env file
```
cambiar el nombre .env.example a .env
```

* Configure credentials to connect to mysql
```
DB_CONNECTION=mysql
```
```
DB_HOST=127.0.0.1
```
```
DB_PORT=3306
```
```
DB_DATABASE=warehouse
```
```
DB_USERNAME=root
```
```
DB_PASSWORD=
```
* generating key

```
php artisan key:generate
```

## Generate key to JWT
```
php artisan jwt:secret
```

## Ejecuting migration
```
php artisan migrate --seed
```

## View documentation
* generating documentation
```
php artisan l5-swagger:generate
```
* view documentation
```
http://localhost:8000/api/documentation
```

## startin server
```
php artisan serve
```
## Detail endpoing
* server http://127.0.0.1:8000
```
GET /api​/products Show products with paginate (require jwt token authentication )
```
```
POST ​/api​/products/store Store new product (require jwt token authentication )
```
```
GET ​/api​/products/{id} Show on product finding by id (require jwt token authentication)
```
```
POST ​/api​/products/search/ Show on product finding by name and SKU (require jwt token authentication)
```
```
PUT ​/api​/products/update/{id} Update prtoduct finding by id (require jwt token authentication)
```
```
DELETE ​/api​/products/{id} Deleting a product (require jwt token authentication)
```
# detail endpoint auth
```
GET /api/register Register new user (no require authentication)
```
```
POST /api/login Login Session
```
```
POST /api/logout Logout session (require jwt authentication)
```
```
POST /api/refresh/ refresh token session (require jwt authentication)
```
```
POST /api/user/ get Authenticated User (require jwt authentication)
```
```
POST /api/reset-password-request/ send token from email for reset password (no require jwt authentication)
```
```
POST /api/change-password/ get Authenticated User (require jwt authentication send from email)
```
#  endpoint users
```
GET /api/users/ get all register users with paginate
```
```
POST /api/users/store (require jwt token authentication)
```
```
PUT /api/users/{id} update users (require jwt token authentication)
```
```
GET /api/users/{id} get one user by id (require jwt token authentication)
```
```
DELETE /api/users/{id} delete users (require jwt token authentication)
```
```
POST ​/api​/users/search/ Show on user finding by name or userName or email (require jwt token authentication)
```

** Collecton shared with postman
```
https://www.getpostman.com/collections/650c1cbf5f5595c33080
```