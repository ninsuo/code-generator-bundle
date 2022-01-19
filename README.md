# Code generator

### What is it?

Check out a few details and screenshots [here](docs/index.md).

### Standalone install

You can install this generator standalone if needed, just download a new symfony project.

Ensure [symfony cli](https://symfony.com/download) is installed and run:

```
symfony new codegen --full
```

You'll need to set up a storage, in `.env` you may uncomment:

```
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

### Bundle install

#### Use composer

```shell
composer require --dev ninsuo/code-generator-bundle
```

#### Add the bundle

The PHP enricher is a beautiful RCE vulnerability, so please enable this bundle in development environment only!

In `config/bundles.php`:

```php
<?php

return [
    // ...
    Bundles\CodeGeneratorBundle\CodeGeneratorBundle::class => ['dev' => true],
];
```

#### Add the routing

In `config/routes/annotations.yaml`:

```yaml
snippet:
  resource: '@CodeGeneratorBundle/Controller/'
  type: annotation
```

#### Generate schema

Run the following command:

```shell
php bin/console make:migration
```

Edit the generated file to ensure migration will only run on dev environment.

```php
    public function up(Schema $schema): void
    {
        if ('dev' !== $_ENV['APP_ENV']) {
            return;
        }

        // ...
    }

    public function down(Schema $schema): void
    {
        if ('dev' !== $_ENV['APP_ENV']) {
            return;
        }

        // ...
    }
```

Then, run:

```
php bin/console doctrine:migrations:migrate
```

#### Run

Go to `/snippet` route
