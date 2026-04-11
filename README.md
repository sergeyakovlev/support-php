# Support

A support package.

## Installation

Install via Composer:

```shell
$ composer require sergeyakovlev/support
```

## DotNotation

Functions for reading and changing nested arrays by a dot-notation path such as `db.host`.

All the functions are pure: they never change the array you pass in. The ones that modify data return a new array.

The functions live in the `SergeYakovlev\Support\DotNotation` namespace and are loaded by Composer's autoloader.
Import them with `use function`:

```php
use function SergeYakovlev\Support\DotNotation\dot_notation_array_forget;
use function SergeYakovlev\Support\DotNotation\dot_notation_array_get;
use function SergeYakovlev\Support\DotNotation\dot_notation_array_has_path;
use function SergeYakovlev\Support\DotNotation\dot_notation_array_push;
use function SergeYakovlev\Support\DotNotation\dot_notation_array_set;
```

| Function                        | Description                                                      |
|---------------------------------|------------------------------------------------------------------|
| `dot_notation_array_get()`      | Returns the value at a path, or a default value                  |
| `dot_notation_array_set()`      | Returns a new array with a value set at a path                   |
| `dot_notation_array_push()`     | Returns a new array with a value appended to the array at a path |
| `dot_notation_array_forget()`   | Returns a new array without the element at a path                |
| `dot_notation_array_has_path()` | Checks whether a path exists                                     |

The examples below use this array:

```php
$config = [
    'app' => ['name' => 'Support', 'debug' => false, 'locale' => null],
    'db' => ['host' => 'localhost', 'ports' => [5432, 5433]],
];
```

### Paths

A path is a string of array keys separated by dots, for example `db.ports.0`.

- A path must not be empty, start or end with a dot, or contain an empty segment (`a..b`).
  Otherwise a `DotNotationException` is thrown.
- Numeric segments address integer keys, so `db.ports.0` is the first element of the `ports` list.
- There is no escaping. A path is always split on dots, so a key that contains a dot (such as `'a.b'`)
  cannot be addressed.

### Missing paths and non-array values

What a function does depends on whether the path exists and on the values along it.

| Function                        | Path does not exist           | A value on the way is not an array |
|---------------------------------|-------------------------------|------------------------------------|
| `dot_notation_array_get()`      | Returns the default value     | Returns the default value          |
| `dot_notation_array_set()`      | Creates the missing arrays    | Throws                             |
| `dot_notation_array_push()`     | Creates the missing arrays    | Throws                             |
| `dot_notation_array_forget()`   | Returns the array unchanged   | Returns the array unchanged        |
| `dot_notation_array_has_path()` | Returns `false`               | Returns `false`                    |

"A value on the way is not an array" means that the function has to go deeper into a value that is a scalar or `null`.
For example, `app.name.first` meets a string at `app.name`.

- `dot_notation_array_get()`, `dot_notation_array_has_path()` and `dot_notation_array_forget()` treat such a path as
  one that does not exist: they return the default value, `false` and the unchanged array.
- `dot_notation_array_set()` and `dot_notation_array_push()` throw a `DotNotationException`, because they cannot put
  a value into something that is not an array.

### `dot_notation_array_get()`

```php
dot_notation_array_get(array $array, DotNotationPath|string $path, mixed $default = null): mixed
```

Returns the value at the path, or `$default` if the path does not exist.

The default is used only when the path is missing. An existing `null`, `false`, `0`, `''` or `[]` is returned as is.

```php
dot_notation_array_get($config, 'db.host');              // 'localhost'
dot_notation_array_get($config, 'db.ports.1');           // 5433
dot_notation_array_get($config, 'db.user', 'root');      // 'root' (the path does not exist)
dot_notation_array_get($config, 'app.debug', true);      // false (the path exists)
dot_notation_array_get($config, 'app.locale', 'en');     // null (the path exists)
dot_notation_array_get($config, 'app.name.first', 'x');  // 'x' (app.name is a string, so the path does not exist)
```

### `dot_notation_array_set()`

```php
dot_notation_array_set(array $array, DotNotationPath|string $path, mixed $value): array
```

Returns a new array with the value set at the path. The missing arrays along the path are created.
An existing value, including a whole branch, is replaced.

```php
dot_notation_array_set([], 'cache.driver', 'redis');
// ['cache' => ['driver' => 'redis']]

dot_notation_array_set($config, 'app.name', 'New name');
// $config['app']['name'] is now 'New name', but $config itself is not changed

dot_notation_array_set($config, 'app.name.first', 'x');
// throws: app.name is a string
```

### `dot_notation_array_push()`

```php
dot_notation_array_push(array $array, DotNotationPath|string $path, mixed $value): array
```

Returns a new array with the value appended to the array at the path, like `$array[] = $value`.
The missing arrays along the path, including the target array itself, are created.

An array value is appended as a single element, it is not merged.
If the target exists but is not an array, an exception is thrown.

```php
dot_notation_array_push($config, 'db.ports', 5434);
// $config['db']['ports'] is now [5432, 5433, 5434]

dot_notation_array_push([], 'tags.list', 'a');
// ['tags' => ['list' => ['a']]]

dot_notation_array_push($config, 'db.ports', [1, 2]);
// $config['db']['ports'] is now [5432, 5433, [1, 2]]

dot_notation_array_push($config, 'db.host', 'x');
// throws: db.host is a string
```

### `dot_notation_array_forget()`

```php
dot_notation_array_forget(array $array, DotNotationPath|string $path): array
```

Returns a new array without the element at the path.

If the element does not exist, the array is returned unchanged. That includes a path that goes through a value
that is not an array. Only the last segment is removed:
the parent array stays even if it becomes empty, and the keys of a list are not renumbered.

```php
dot_notation_array_forget($config, 'app.debug');
// $config['app'] is now ['name' => 'Support', 'locale' => null]

dot_notation_array_forget($config, 'db.ports.0');
// $config['db']['ports'] is now [1 => 5433]

dot_notation_array_forget($config, 'cache.driver');
// unchanged: there is nothing to remove

dot_notation_array_forget($config, 'app.name.first');
// unchanged: app.name is a string, so the path does not exist
```

### `dot_notation_array_has_path()`

```php
dot_notation_array_has_path(array $array, DotNotationPath|string $path): bool
```

Checks whether the path exists. The value does not matter: an element with the value `null` exists.

```php
dot_notation_array_has_path($config, 'db.host');         // true
dot_notation_array_has_path($config, 'app.locale');      // true (the value is null)
dot_notation_array_has_path($config, 'db.user');         // false
dot_notation_array_has_path($config, 'app.name.first');  // false (app.name is a string)
```

### `DotNotationPath`

Every function accepts either a string or a `DotNotationPath`. The object validates the path once and caches its
segments, so create it once and reuse it when the same path is used many times.

```php
use SergeYakovlev\Support\DotNotation\DotNotationPath;

$path = new DotNotationPath('db.ports.0'); // throws a DotNotationException if the path is invalid

$path->path;            // 'db.ports.0'
$path->allSegments;     // ['db', 'ports', '0']
$path->parentSegments;  // ['db', 'ports']
$path->lastSegment;     // '0'

foreach ($servers as $server) {
    $port = dot_notation_array_get($server, $path);
}
```

The properties are read-only.

- `DotNotationPath::from(DotNotationPath|string $path)` creates a path from a string, or returns the given
  instance as is.
- `DotNotationPath::validateOrThrowException(string $path)` only validates a path string.

### `DotNotationException`

`DotNotationException` extends `Exception`. Every function throws it when a path is invalid.
In addition, `dot_notation_array_set()` and `dot_notation_array_push()` throw it when a value on the way is not
an array. The message tells which case it is:

| Case                                               | Message                                                                 |
|----------------------------------------------------|-------------------------------------------------------------------------|
| The path is empty                                  | `Invalid path (is empty).`                                              |
| The path has an empty segment                      | `Invalid path "a..b" (one of the path segments is empty).`              |
| A value on the way is not an array (`set`, `push`) | `Value of the segment "name" of path "app.name.first" is not an array.` |

In the last message the segment is the one whose value is not an array.

## Number functions

Functions for formatting numbers and choosing plural forms.

The functions live in the `SergeYakovlev\Support\Number` namespace and are loaded by Composer's autoloader.
Import them with `use function`:

```php
use function SergeYakovlev\Support\Number\html_float_format;
use function SergeYakovlev\Support\Number\html_int_format;
use function SergeYakovlev\Support\Number\number_float_format;
use function SergeYakovlev\Support\Number\number_int_format;
use function SergeYakovlev\Support\Number\number_ru_plural_things;
```

| Function                    | Description                                                            |
|-----------------------------|------------------------------------------------------------------------|
| `number_float_format()`     | Formats a float number as plain text                                   |
| `number_int_format()`       | Formats an integer as plain text                                       |
| `html_float_format()`       | The same as `number_float_format()`, with the defaults suited for HTML |
| `html_int_format()`         | The same as `number_int_format()`, with the defaults suited for HTML   |
| `number_ru_plural_things()` | Chooses the Russian plural form of a noun for a number                 |

### Plain text and HTML

The `html_` functions take the same arguments as the `number_` functions and simply call them. Only the default
values are different:

| Argument              | `number_*` (plain text)           | `html_*` (HTML) |
|-----------------------|-----------------------------------|-----------------|
| `$decimalsSeparator`  | `,`                               | `,`             |
| `$thousandsSeparator` | `''` (the digits are not grouped) | `&nbsp;`        |
| `$signPlus`           | `''` (no sign)                    | `''` (no sign)  |
| `$signMinus`          | `-`                               | `&minus;`       |

`$decimalsSeparator` exists only in the float functions. Any default can be replaced by passing the argument.

The `html_` defaults are constants of the same namespace, so they can be used when the arguments are given explicitly:

| Constant     | Value     | Meaning                    |
|--------------|-----------|----------------------------|
| `HTML_NBSP`  | `&nbsp;`  | The no-break space entity  |
| `HTML_MINUS` | `&minus;` | The minus sign entity      |

There are also `UTF8_NBSP` (U+00A0) and `UTF8_MINUS` (U+2212): the same characters as plain UTF-8 text. The `number_`
functions use neither of them by default, but they are handy as explicit arguments:

```php
use const SergeYakovlev\Support\Number\UTF8_MINUS;
use const SergeYakovlev\Support\Number\UTF8_NBSP;

number_int_format(-1234567, UTF8_NBSP, '', UTF8_MINUS);   // "\u{2212}1\u{00A0}234\u{00A0}567"
```

### `number_float_format()`

```php
number_float_format(
    float $value,
    int $decimals,
    string $decimalsSeparator = ',',
    string $thousandsSeparator = '',
    string $signPlus = '',
    string $signMinus = '-',
): string
```

Formats a float number with the given number of decimal digits, digit grouping and optional signs.

```php
number_float_format(1234.56, 2);                    // '1234,56'
number_float_format(-1234.56, 2);                   // '-1234,56'
number_float_format(1234567.891, 2, '.', ',');      // '1,234,567.89'
number_float_format(1234.56, 0, '.', ' ', '+');     // '+1 235'
number_float_format(1234.5, -1);                    // '1230' (a negative number of decimals rounds the integer part)
```

The sign follows the number that is shown, not the original value. A number that is rounded to zero gets no sign,
neither `-0,00` nor `+0,00`:

```php
number_float_format(-0.001, 2);                     // '0,00'
number_float_format(0.001, 2, ',', '', '+');        // '0,00'
number_float_format(-0.005, 2);                     // '-0,01' (it is rounded to -0.01)
```

`NAN` and infinity cannot be formatted: a `NumberException` is thrown.

### `number_int_format()`

```php
number_int_format(
    int $value,
    string $thousandsSeparator = '',
    string $signPlus = '',
    string $signMinus = '-',
): string
```

Formats an integer with digit grouping and optional signs. It works for the whole range of `int`.

```php
number_int_format(1234567);                         // '1234567'
number_int_format(1234567, ' ');                    // '1 234 567'
number_int_format(123, ',', '+');                   // '+123'
number_int_format(PHP_INT_MIN, ' ');                // '-9 223 372 036 854 775 808'
```

### `html_float_format()` and `html_int_format()`

```php
html_float_format(
    float $value,
    int $decimals,
    string $decimalsSeparator = ',',
    string $thousandsSeparator = '&nbsp;',
    string $signPlus = '',
    string $signMinus = '&minus;',
): string

html_int_format(
    int $value,
    string $thousandsSeparator = '&nbsp;',
    string $signPlus = '',
    string $signMinus = '&minus;',
): string
```

The same functions, but the digits are grouped with the `&nbsp;` entity (a no-break space, so a number is never
split between lines) and the minus sign is the `&minus;` entity.

```php
html_float_format(1234.56, 2);                      // '1&nbsp;234,56'
html_float_format(-1234.56, 2);                     // '&minus;1&nbsp;234,56'
html_int_format(1234567);                           // '1&nbsp;234&nbsp;567'
html_int_format(-1234567);                          // '&minus;1&nbsp;234&nbsp;567'
html_int_format(1234567, ',', '+');                 // '+1,234,567'
```

The result is not escaped, so it is meant for HTML only. A `NumberException` is thrown for `NAN` and infinity
in `html_float_format()`, as in `number_float_format()`.

### `number_ru_plural_things()`

```php
number_ru_plural_things(int $value, array $forms): string
```

Chooses the Russian plural form of a noun for a number. `$forms` has three forms in this order:
the plural form (for `0`, `5`-`20`, `25`-`30`, ...), the singular form (for `1`, `21`, `31`, ...) and the paucal
form (for `2`-`4`, `22`-`24`, `32`-`34`, ...). There must be exactly these three elements, with the keys `0`, `1`
and `2`.

```php
$apples = ['яблок', 'яблоко', 'яблока'];

number_ru_plural_things(1, $apples);                // 'яблоко'
number_ru_plural_things(2, $apples);                // 'яблока'
number_ru_plural_things(5, $apples);                // 'яблок'
number_ru_plural_things(11, $apples);               // 'яблок'
number_ru_plural_things(21, $apples);               // 'яблоко'

echo $count, ' ', number_ru_plural_things($count, $apples);
```

A `NumberException` is thrown if the number is negative or if `$forms` is not exactly the three forms: fewer or more
than three elements, or keys other than `0`, `1` and `2`.

### `NumberException`

`NumberException` extends `Exception`. It is thrown by `number_float_format()` and `html_float_format()` for `NAN`
and infinity, and by `number_ru_plural_things()` for a negative number or when `$forms` is not exactly three forms.
