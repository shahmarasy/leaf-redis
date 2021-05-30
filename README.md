<!-- markdownlint-disable no-inline-html -->
<p align="center">
    <br><br>
    <img src="https://leaf-docs.netlify.app/images/logo.png" height="100"/>
    <h1 align="center">Leaf Redis Helper</h1>
    <br><br>
</p>

# Leaf Redis

<!-- [![Latest Stable Version](https://poser.pugx.org/leafs/leaf/v/stable)](https://packagist.org/packages/leafs/leaf)
[![Total Downloads](https://poser.pugx.org/leafs/leaf/downloads)](https://packagist.org/packages/leafs/leaf)
[![License](https://poser.pugx.org/leafs/leaf/license)](https://packagist.org/packages/leafs/leaf) -->

This is a new addition to Leaf's collection of packages. Unlike other packages, this one doesn't come pre-packaged with Leaf by default and so needs to be installed separately.

## Installation

You can quickly and simply install Leaf Redis through composer.

```sh
composer require leafs/redis
```

**NOTE:** Leaf redis is a separate package and so can be used outside of Leaf.

## Getting Started

To get started with Leaf Redis, you simply need to call the `init` method and pass in any configuration you need.

### Aloe CLI

Although Leaf Redis can be used outside the Leaf environment, there's more support for Leaf based frameworks. Leaf Redis comes with out of the box support for Aloe CLI which is used in Leaf MVC and Leaf API. To get started, head over to the `leaf` file in the root directory of your Leaf API/Leaf MVC app or wherever aloe CLI is registered and register a new command.

```php
$console->register(\Leaf\Redis::commands());
```

From there you should have access to a bunch of new commands from Leaf redis. The available commands are:

```sh
redis
  redis:install     Create leaf redis config and .env variables
```

## View Leaf's docs [here](https://leafphp.netlify.app/#/)

Built with ❤ by [**Mychi Darko**](https://mychi.netlify.app)
