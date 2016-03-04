# Json-schema bundle

[![Build Status](https://travis-ci.org/soyuka/JsonSchemaBundle.svg?branch=master)](https://travis-ci.org/soyuka/JsonSchemaBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2a280221-15d7-45e9-87b2-5b104267c91d/mini.png)](https://insight.sensiolabs.com/projects/2a280221-15d7-45e9-87b2-5b104267c91d)

Based on a fork of https://github.com/HadesArchitect/JsonSchemaBundle

## Features

- Json Schema generation through command (via Doctrine or Php)
- Json Schema validation annotation
- Json Schema service that accepts class instances

## Installation

Composer:

```bash
composer require soyuka/jsonschemabundle
```

Load the bundle:

```php
<?php
// app/AppKernel.php

use Soyuka\JsonSchemaBundle\JsonSchemaBundle;

public function registerBundles()
{
    $bundles = array(
        // ...
        new JsonSchemaBundle(),
    );
}
```

## Json Schema generation

The default is to parse files located in every `Entity` directory of every Bundle. The default strategy uses php to parse your class (see [php-to-json-schema](https://github.com/dunglas/php-to-json-schema)).

```bash
app/console jsonschema:generate
```

If you want to parse another directory, just specify it through the `--directory` option:

```bash
app/console jsonschema:generate -d Model
```

If you want to use Doctrine annotation to generate a json schema, specify the `doctrine` strategy. Note that the `doctrine/orm-bundle` will be needed:

```bash
app/console jsonschema:generate --strategy doctrine
```

Every json schema will be written to the configurable directory of your choice by keeping the following tree architecture:

```
BundleName/Entity.json
```

The default value is `%kernel.root_dir%/Resources/validators`, so assuming the Entity parsed is in `AcmeBundle/Entity/Product`, the schema will be written in `%kernel.root_dir%/Resources/validators/AcmeBundle/Product.json`.

## Validation annotation

This bundle adds a JsonSchema **class** annotation. When no option is given, the validator will look in the json path (same architecture than with schema generation).

The most basic example will be:

```php
<?php
use Soyuka\JsonSchemaBundle\Constraints as JsonSchemaAssert;

/**
 * @JsonSchemaAssert\JsonSchema
 */
class DefaultJsonSchema
{
}
```

[See here for the full class](https://github.com/soyuka/JsonSchemaBundle/blob/master/tests/Fixtures/DefaultJsonSchema.php).

If you want to use a json schema at a different location, or even remote, specify the path of your choice:

```php
<?php
use Soyuka\JsonSchemaBundle\Constraints as JsonSchemaAssert;

/**
 * @JsonSchemaAssert\JsonSchema(schema = "validators/specific.json")
 */
class SpecificJsonSchema
{
}
```

The schema option value is retrieved using the `JsonSchema\Uri\UriRetriever` (any value that works there works in the annotation).

## Configuration

```yaml
json_schema:
    path: 'foobar'
```

## Hack

If you want to handle jsonSchema validation yourself, but have issues using Entity instances with `justinrainbow/json-schema`, you may want to take a look at the `PropertyAccessorConstraint`. Here, it'll override the `ObjectConstraint` so that object properties are accessed through the PropertyAccessor. [See the Validator class](https://github.com/soyuka/JsonSchemaBundle/blob/master/src/Mapping/Validator/Validator.php#L16).

## Licence

```
The MIT License (MIT)

Copyright (c) 2015 Antoine Bluchet

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
```
