# Decorate PHP

Easily create decorators and proxies with a simple (composer) command.

<a href="https://packagist.org/packages/doekenorg/decorate-php" target="_blank"><img src="https://img.shields.io/packagist/v/doekenorg/decorate-php.svg?style=flat-square"/></a>

---

Do you enjoy using decorators in PHP, but hate having to implement one with large amounts of methods? Then this is the
plugin for you! You can now quickly create a (final or abstract) class from an interface with all the methods already
implemented and forwarded to the next instance. This little time saver lets you get on with the things you enjoy.

## Installation

```bash
composer global require doekenorg/decorate-php
```

*__Notice__: This is a global plugin, so don't forget `global` in the command!*

## Usage

The plugin adds a `decorate` command to your `composer` instance. It needs a source class (the interface you want to
decorate) and a target class. You can optionally provide the name of the variable it uses for the next class. Currently,
the plugin only works for composer projects.

### Create a new decorator

```bash
composer decorate "Package\Namespace\SomeInterface" "My\Namespace\DestinationClass"
```

This will create, and write, a file called `DestinationClass.php` in the appropriate folder mapped in your `psr-4`
autoloader configuration.

A file like this will be created:

```php
<?php

namespace My\Namespace;

use Package\Namespace\SomeInterface;

class DestinationClass implements SomeInterface {
    public function __construct(private SomeInterface $next) {
    }
    
    public function any_method(string $variable, ...$variadic_variable): void {
        $this->next->any_method($variable, ...$variadic_variable);
    }
}
```

### Create a new decorator with a specific variable name.

By default, the decorated instance is mapped to a variable called `$next`. You can overwrite this by providing it as
the third parameter. In the next example the variable will be called `$client`.

```bash
composer decorate "Package\Namespace\ClientInterface" "My\Namespace\MyClient" "client"
```

Here the output will be something like:

```php
<?php

namespace My\Namespace;

use Package\Namespace\ClientInterface;

class MyClient implements ClientInterface {
    public function __construct(private ClientInterface $client) {
    }

    // ...
}
```

*__Note:__ The command will not overwrite an existing file. Provide the `--overwrite` option to force write.*

### Options

The command comes with the following options:

- `--spaces` will replace the indentation from tabs to `4` spaces by default. If you want 2 spaces; use `--spaces=2`.
  You can also provide a default in the global configuration (see next section).
- `--output` will output the code to the console instead of writing it.
- `--overwrite` will force-overwrite the file if it already exists.
- `--abstract` will create an `abstract` class. The `next` variable will now be `protected` instead of `private`.
- `--final` will create a `final` class.

## Global configuration

You can use the following configuration in the `extra` key of your __global__ `composer.json` (usually
in `~/.composer`).

```json
{
  //...
  "extra": {
    "decorate-php": {
      "spaces": 4,
      "variable": "next",
      "use-property-promotion": true,
      "use-func-get-args": false,
      "use-final-class": true
    }
  }
}
```

- `spaces` will set the indentation to this amount of spaces by default; removing the need for `--spaces`.
- `variable` will overwrite the default of `next` with this value.
- `use-property-promotion` whether to use property promotion in the constructor (`true` by default).
- `use-func-get-args` whether to replace the actual arguments on the method call with `...func_get_args()`.
- `use-final-class` whether to create a `final` class by default.

## Other information

### PSR-4 only

Writing files is only supported for PSR-4 namespaces provided in the `autoload` key of your project. No PSR-0 support.

### No decorating `final` classes

Obviously, you cannot create a decorator for a `final` class.

### Decorating abstract classes

Although not common, you can also decorate `abstract` classes. In this case, any `final` methods are ignored when
creating the decorator.

*(Decorating non-abstract classes isn't really useful; but I kept the possibility for the _extenders_ among us.)*

### Constructors

When an `abstract` class or `interface` declares a `__construct` method; it will append the next instance as the first
argument. In case of an `abstract` class, it will also call the `parent::__construct()` method with the appropriate
arguments.

### Caveats

- Because `composer` uses some packages under the hood; it might use those interfaces, instead of the one in your
  project. For example: `Psr\Container` and `Psr\Log` namespaces. There might be a version discrepancy in that case.
