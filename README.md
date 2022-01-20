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

or in `composer.json`:

```json
{
  "require": {
    "ninsuo/code-generator-bundle": "dev-main"
  }
}
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

### Sharing snippets

Use the import / export feature at the bottom to share snippets with crewmates.

If you are using this bundle inside a project, you can also use fixtures to share snippets:

```php
<?php

namespace App\Test\DataFixtures;

use Bundles\CodeGeneratorBundle\Repository\SnippetRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SnippetFixtures extends Fixture
{
    private SnippetRepository $snippetRepository;

    public function __construct(SnippetRepository $snippetRepository)
    {
        $this->snippetRepository = $snippetRepository;
    }

    public function load(ObjectManager $manager)
    {
        $this->snippetRepository->import(
            'eyJuIjoiSGVsbG8sIHdvcmxkIiwiYyI6ImZvbzogYmFyIiwiZSI6IiRjb250ZXh0WydiYXonXSA9IHN0cnRvdXBwZXIoJGNvbnRleHRbJ2ZvbyddKTsiLCJmIjpbeyJkIjoiaWYgeW91IHNlYXJjaCBmb3IgbWUsIGknbSBhdCB0aGUge3sgYmF6IH19IiwidCI6IkhlbGxvLCB7eyBmb28gfX0ifV19+e3sgY2FtZWxDYXNlTmFtZSB9fVJlcG9zaXRvcnkgPSAke3sgY2FtZWxDYXNlTmFtZSB9fVJlcG9zaXRvcnk7XHJcbiAgICB9XHJcblxyXG59In0seyJkIjoic3JjXC9SZXBvc2l0b3J5XC97eyBuYW1lIH19UmVwb3NpdG9yeS5waHAiLCJ0IjoiPD9waHBcclxuXHJcbm5hbWVzcGFjZSBBcHBcXFJlcG9zaXRvcnk7XHJcblxyXG51c2UgQXBwXFxCYXNlXFxCYXNlUmVwb3NpdG9yeTtcclxudXNlIEFwcFxcRW50aXR5XFx7eyBuYW1lIH19O1xyXG51c2UgRG9jdHJpbmVcXFBlcnNpc3RlbmNlXFxNYW5hZ2VyUmVnaXN0cnk7XHJcblxyXG5cLyoqXHJcbiAqIEBtZXRob2Qge3sgbmFtZSB9fXxudWxsIGZpbmQoJGlkLCAkbG9ja01vZGUgPSBudWxsLCAkbG9ja1ZlcnNpb24gPSBudWxsKVxyXG4gKiBAbWV0aG9kIHt7IG5hbWUgfX18bnVsbCBmaW5kT25lQnkoYXJyYXkgJGNyaXRlcmlhLCBhcnJheSAkb3JkZXJCeSA9IG51bGwpXHJcbiAqIEBtZXRob2Qge3sgbmFtZSB9fVtdICAgIGZpbmRBbGwoKVxyXG4gKiBAbWV0aG9kIHt7IG5hbWUgfX1bXSAgICBmaW5kQnkoYXJyYXkgJGNyaXRlcmlhLCBhcnJheSAkb3JkZXJCeSA9IG51bGwsICRsaW1pdCA9IG51bGwsICRvZmZzZXQgPSBudWxsKVxyXG4gKlwvXHJcbmNsYXNzIHt7IG5hbWUgfX1SZXBvc2l0b3J5IGV4dGVuZHMgQmFzZVJlcG9zaXRvcnlcclxue1xyXG4gICAgcHVibGljIGZ1bmN0aW9uIF9fY29uc3RydWN0KE1hbmFnZXJSZWdpc3RyeSAkcmVnaXN0cnkpXHJcbiAgICB7XHJcbiAgICAgICAgcGFyZW50OjpfX2NvbnN0cnVjdCgkcmVnaXN0cnksIHt7IG5hbWUgfX06OmNsYXNzKTtcclxuICAgIH1cclxufSJ9XX0='
        );
    }
}
```