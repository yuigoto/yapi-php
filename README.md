YX : PHP : YAPI
===============

> A simple base API project based on Slim and Doctrine. This is very basic, but provides some authentication and token validation.
> 
> Use it to bootstrap simple API projects! ;)

### How To

Basically, all you need to do is create entities and build them controllers. But there are a few things that you might want to do to customize the API's behavior, so here are some instructions.

#### `index.php`

When you first open this file, you'll notice that it only requires the `Api.php` from `src`, runs the `Api` class and uses its `get()` method to retrieve the `\Slim\App` instance and `run()` it.

Well, if you're thinking "but, is that everything?", then YES! That's everything that it needs to do for the purpose of this project. For real, no kidding! (Sorry for this one, next files will be a bit more serious)

#### `src\Api.php`

This is probably the file you'll tinker with first when using this project. It basically holds a class that:

- Loads the environment variables from the `.env` file in the project's source;
    - Well, when you clone this project you'll see that this `.env` file doesn't exist! That's because it's YOUR job to create it, based on the `.env.example` file, and update every information in it :wink:;
        - This is because `.env` contains sensitive data about your application, like the database authentication information, and it's safer to put these information on a _not-so-easily shared_ way when working with public projects;
- Fires an instance of `\Slim\Container` and defines some of the application dependencies in it;
- Fires an instance of `\Slim\App` and the `RouteHandler`, which receives the app instance in its constructor;
- Defines the `TrailingSlash` and `authentication` middlewares;

The places you might tinker with, in this file are probably the contents in the `dependencies()` and the `authentication()` methods, mostly because of some different dependencies, translating error messages or just changing the way the app uses authentication middleware.

Outside of these methods, there's not much to tinker in this file, which leaves us to the next _most probable file to tinker with_...

#### `src\api\RouterHandler.php`

The `RouterHandler` receives an instance of `\Slim\App` in its constructor and then applies the route callbacks using the controller objects in `src\api\controllers`.

Whenever you build a new controller, it's here that you'll come to properly make use of it.

And that's it for this file! Next...

#### Controllers

We'll use `src\api\controllers\DummyController.php` as an example.

Basically, a controller does this:

- Receives the `\Slim\App` instance from the `RouteHandler`;
- Declares the routes this controller it's related to and then applies its internal public methods a callback for each route used;
    - Callbacks use the _Slim Framework_ callback method, you can see more about these callbacks [here](https://www.slimframework.com/docs/v3/tutorial/first-app.html);
- Also, the controllers keep a reference to the application and container instances in its `$app` and `$container` properties, in case the methods need to use them;

It's really simple to use the controllers this way, but remember to make all methods used in routes `public`!

#### Entities

The entities in `src\api\models\entity\*` follow _Doctrine's_ entity model. So please refer to the [Basic Mapping](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/basic-mapping.html) article on Doctrine's documentation for more details on it.

But there are a few things that I do instead of declaring the whole entity here.

You'll see that most (or all) entities extend the `BaseEntity` class located in `src\yapi\core\BaseEntity.php`. The Base Entity is an entity without the `@Entity` and `@Table` annotations, but it does declare some annotated properties and getter/setter methods.

> While you **CAN** declare this inheritable entity as an `@Entity` with a `@Table`, we don't do this so it won't cause problems further when we use Doctrine's _QueryBuilder_ to select entities from the database, as these annotations will cause a bit of a mess in select operations with any extended child by adding some unknown aliases.

The properties the `BaseEntity` define are:

- `id`: an integer that serves as primary key;
- `uuid`: a string, that holds a UUID for the entity, using the UUID v4 generator;
- `created_at`/`updated_at`: creation and updated dates for this entity;
- `deleted`: a boolean that serves a a _soft delete_ flag, since I don't like to delete things so easily;

The `uuid`, `created_at` and `updated_at` are created by methods that have `@PrePersist` and `@PreUpdate` annotations. Because of this, **ALL** classes that extend the `BaseEntity` must have the `@HasLifecycleCallbacks` annotation.

#### Other Files of Interest

The files and instruction above are most of the things you'll have to tinker with when using this project. But there are some other files that you can customize too, which are:

- `src\core\constants.php`: this file is autoloaded by `autoload.php`, and it declares constants used globally;
- `src\yapi\core\BaseEntity.php`: the base entity, that declares some basic properties for entities which can be inherited;
- `src\yapi\core\ClientInformation.php`: a simple, plain PHP object that fetches some information from the client requesting information;
- `src\yapi\core\ResponseError.php`: a simple template for error responses for the API;
- `src\yapi\core\ResponseTemplate.php`: a simple template for JSON Responses used by the API;
- `src\yapi\core\Utilities.php`: contains some public static methods used throughout the API, like a password hasher and security salt definitions;

And that's basically it! :wink:

### Author

- **Fabio Y. Goto** ([lab@yuiti.com.br](mailto:lab@yuiti.com.br));

### License

This project is licensed under the `MIT License`, please check the `LICENSE.md` file for more information.

-----

_©2018 Fabio Y. Goto_