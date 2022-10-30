How to use the server:
-   Clone the project
-	In order to use Laravel, need to have composer first, then install Laravel
-	Modify .env file in project root folder to specify your SQL database connection
-	Open terminal and navigate to project root folder. Run the command to create database:
```
Php artisan migrate
```
-	Then run the following to bring up the server:
```
Php artisan serve
```
-	By default, the API server will be hosted at localhost:8000/api
-	If you use different port, please modify accordingly in the front end javascript to reflect the correct endpoints for API server.

