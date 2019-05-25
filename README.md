# SlimRouteGroups

[![Build Status](https://travis-ci.org/mangopeaches/SlimRouteGroups.svg?branch=master)](https://travis-ci.org/mangopeaches/SlimRouteGroups)

Wrapper for slim router which simplifies route definitions and organization.

## Installation
```bash
composrt install mangopeaches/slim-route-groups
```

## The Problem

If your organization is using the Slim Framework for any decent sized API project you likely have a lot of routes.

If you hadn't given it a lot of though you soon end up with a routes file the can look similar to the following.

```php
$app->group('/users', function($app) {
    $app->get('', 'Path\To\Users:getAll');
    $app->post('', 'Path\To\Users:create');
});

$app->group('/books', function($app) {
    $app->get('', 'Path\To\Books:getAll');
    $app->post('', 'Path\To\Books:create');
});
```

This is fine up to a point, but when you start developing overlapping sections, adding options routes, or have multiple developers changing routes.php this setup can start to cause some trouble.

## The Solution

A little planning goes a long way. The objective of this project is to separate your routes per resource or whatever your ideal of logical groups is to save conflict nightmare and keep your routes isolated and simple.

Let's do the same as above in this new way.

We'd create two separate route files.

UsersRoutes.php
```php
<?php
use SlimRouteGroups\Routes;

class UsersRoutes extends Routes
{
    /**
     * Define user routes.
     */
    public function __invoke()
    {
        $self = $this;
        $this->group('/users', function($app) use ($self) {
            $self->get('', 'Path\To\Users:getAll');
            $self->post('', 'Path\To\Users:create');
        });
    }
}

```

BooksRoutes.php
```php
<?php
use SlimRouteGroups\Routes;

class BooksRoutes extends Routes
{
    /**
     * Define books routes.
     */
    public function __invoke()
    {
        $self = $this;
        $this->group('/books', function($app) use ($self) {
            $self->get('', 'Path\To\Books:getAll');
            $self->post('', 'Path\To\Books:create');
        });
    }
}

```

Not really a crazy concept, but we avoid merg conflict right off the bat, and if you're using controllers your *Routes.php files easily mirror your controller structure.
