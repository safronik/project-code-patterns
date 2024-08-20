<h1 align="center">safronik/code-patterns</h1>
<p align="center">
    <strong>A PHP library contains code patterns ready to use.</strong>
</p>
<p align="center">
    <!--a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/ramsey/uuid.svg?style=flat-square&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/ramsey/uuid/blob/4.x/LICENSE"><img src="https://img.shields.io/packagist/l/ramsey/uuid.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://github.com/ramsey/uuid/actions/workflows/continuous-integration.yml"><img src="https://img.shields.io/github/actions/workflow/status/ramsey/uuid/continuous-integration.yml?branch=4.x&logo=github&style=flat-square" alt="Build Status"></a>
    <a href="https://app.codecov.io/gh/ramsey/uuid/branch/4.x"><img src="https://img.shields.io/codecov/c/github/ramsey/uuid/4.x?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
    <a href="https://shepherd.dev/github/ramsey/uuid"><img src="https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2Framsey%2Fuuid%2Fcoverage" alt="Psalm Type Coverage"></a-->
</p>

## About

This package contains few main code patterns you can use to build your own project. You are free to use anywhere in any way you want (MIT License).

## Installation

The preferred method of installation is via Composer. Run the following
command to install the package and add it as a requirement to your project's
`composer.json`:

```bash
composer require safronik/code-patterns
```

## Content

Patterns grouped by their destination:

### Generative:

Patterns which are create or store objects/values

- [AbstractFabric.php](src%2FGenerative%2FAbstractFabric.php)
- [Builder.php](src%2FGenerative%2FBuilder.php)
- [Container.php](src%2FGenerative%2FContainer.php)
- [Fabric.php](src%2FGenerative%2FFabric.php)
- [Multiton.php](src%2FGenerative%2FMultiton.php)
- [Prototype.php](src%2FGenerative%2FPrototype.php)
- [RAII.php](src%2FGenerative%2FRAII.php)
- [Registry.php](src%2FGenerative%2FRegistry.php)
- [Singleton.php](src%2FGenerative%2FSingleton.php)

### Structural:

Patterns which help to organize and build solid code structure

- [Decorator.php](src%2FStructural%2FDecorator.php)
- [DTO.php](src%2FStructural%2FDTO.php)
- [Hydrator.php](src%2FStructural%2FHydrator.php)

### Behavioral:

These helps you to organize the code in dynamic, its behaviour.

- [Event.php](src%2FBehavioral%2FEvent.php)
- [EventBroker.php](src%2FBehavioral%2FEventBroker.php)
- [EventManager.php](src%2FBehavioral%2FEventManager.php)

### Custom:

Patterns from any previous group with some additional changes like lazy loads or specific keys 

- [MultitonByClassname.php](src%2FCustom%2FMultitonByClassname.php)

### Contributing

Feel free to contribute!
