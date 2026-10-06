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

## String functions

The functions live in the `SergeYakovlev\Support\Str` namespace and are loaded by Composer's autoloader.
Import them with `use function`.

### `str_emoji_country_flag()`

```php
str_emoji_country_flag(string $countryIsoAlpha2): string
```

Converts an [ISO 3166-1 alpha-2](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2) country code to the flag emoji.
For example, `RU` becomes `🇷🇺`. A flag is a pair of Unicode regional indicator symbols.

The code must be exactly two Latin letters in the same case: `RU` or `ru`. For any other code the function
returns an empty string:

| Code                      | Result                |
|---------------------------|-----------------------|
| `'RU'`, `'ru'`            | `'🇷🇺'`               |
| `'Ru'`                    | `''` (mixed case)     |
| `'RUS'`, `'R'`, `''`      | `''` (wrong length)   |
| `'R1'`, `'РУ'`, `' RU'`   | `''` (not two letters) |

The code is not checked against the list of existing countries: any two letters give a flag sequence.
Whether it is shown as a flag depends on the platform, and some of them (notably Windows) show the two letters instead.

```php
use function SergeYakovlev\Support\Str\str_emoji_country_flag;

str_emoji_country_flag('RU');     // '🇷🇺'
str_emoji_country_flag('ru');     // '🇷🇺'
str_emoji_country_flag('Ru');     // ''
str_emoji_country_flag('XXX');    // ''
```

The function takes one argument, so it works in a pipe. A code in mixed case is invalid, so bring it to one case first:

```php
$flag = ' Ru '
    |> mb_trim(...)
    |> mb_strtoupper(...)
    |> str_emoji_country_flag(...); // '🇷🇺'
```

### `str_empty_to_null()`

```php
str_empty_to_null(?string $string): ?string
```

Returns the given string as is, or `null` if it is `null` or an empty string.

Only a zero-length string counts as empty. Strings such as `'0'` and `' '` are returned unchanged,
and nothing is trimmed.

| Input    | Result   |
|----------|----------|
| `null`   | `null`   |
| `''`     | `null`   |
| `'0'`    | `'0'`    |
| `' '`    | `' '`    |
| `'text'` | `'text'` |

The result is typed as `non-empty-string|null`, so static analysers know the string is not empty
once the `null` case is excluded.

Use it to replace an empty string with a default value:

```php
use function SergeYakovlev\Support\Str\str_empty_to_null;

$title = str_empty_to_null($request['title'] ?? null) ?? 'Untitled';
```

The function takes one argument, so it works in a pipe. To treat whitespace-only strings as empty,
trim first:

```php
$name = $input
    |> mb_trim(...)
    |> str_empty_to_null(...);
```

The function may return `null`, so put it at the end of a pipe chain: the next step would receive `null`
and fail with a `TypeError`.

### `str_ensure_starts_with()` and `str_ensure_ends_with()`

```php
str_ensure_starts_with(?string $string, string $prefix, bool $collapse = false): ?string
str_ensure_ends_with(?string $string, string $suffix, bool $collapse = false): ?string
```

Return a string that starts with `$prefix` (ends with `$suffix`). The text is added only if it is missing, so
applying a function twice gives the same result as applying it once.

The prefix and the suffix are whole substrings compared case-sensitively. They are not sets of characters,
as in `ltrim()` and `rtrim()`: `str_ensure_starts_with('baz', 'ab')` gives `'abbaz'`. The functions work on bytes,
so they are safe for multibyte text and do not fail on invalid UTF-8. An empty `$prefix` or `$suffix` changes nothing.

If `$string` is `null`, `null` is returned and nothing is added. An empty string is not `null`:
`str_ensure_starts_with('', '/')` gives `'/'`. The return type follows the input: a `string` gives a `string`,
and `null` gives `null`. Static analysers know this, so for a `string` input the result can be used anywhere in a pipe.

`$collapse` sets what happens when the string already has the text, repeated or not:

| `$collapse`       | Behaviour                                                       | Example                   |
|-------------------|-----------------------------------------------------------------|---------------------------|
| `false` (default) | Returned as is if the string already has the prefix (suffix).   | `'//foo'` gives `'//foo'` |
| `true`            | The repeats at the start (end) become a single prefix (suffix). | `'//foo'` gives `'/foo'`  |

Only the repeats that are next to each other at the edge are collapsed. The rest of the string is not touched.

```php
use function SergeYakovlev\Support\Str\str_ensure_ends_with;
use function SergeYakovlev\Support\Str\str_ensure_starts_with;

str_ensure_starts_with('foo', '/');           // '/foo'
str_ensure_starts_with('/foo', '/');          // '/foo'
str_ensure_starts_with('//foo', '/');         // '//foo'
str_ensure_starts_with('//foo', '/', true);   // '/foo'
str_ensure_starts_with('baz', 'ab');          // 'abbaz'
str_ensure_starts_with('ёлка', 'я');          // 'яёлка'
str_ensure_starts_with('', '/');              // '/'
str_ensure_starts_with(null, '/');            // null

str_ensure_ends_with('foo', '/');             // 'foo/'
str_ensure_ends_with('foo/', '/');            // 'foo/'
str_ensure_ends_with('foo//', '/');           // 'foo//'
str_ensure_ends_with('foo//', '/', true);     // 'foo/'
str_ensure_ends_with('image.jpg', '.png');    // 'image.jpg.png'
str_ensure_ends_with('report.txt', '.txt');   // 'report.txt'
str_ensure_ends_with(null, '/');              // null
```

Use them to join parts of a URL or a path regardless of how the parts were written:

```php
$url = str_ensure_ends_with('https://example.com', '/', true) . 'api/users';
// 'https://example.com/api/users'

$url = str_ensure_ends_with('https://example.com//', '/', true) . 'api/users';
// 'https://example.com/api/users'
```

The functions take more than one argument, so wrap the call in a closure to use it in a pipe:

```php
$path = $input
    |> mb_trim(...)
    |> (static fn(string $v): string => str_ensure_starts_with($v, '/', true));
```

### `str_normalize_newlines()`

```php
str_normalize_newlines(?string $string): ?string
```

Replaces every line break with `"\n"` (LF). If the input is `null`, `null` is returned.

| Line break      | Result |
|-----------------|--------|
| `"\r\n"` (CRLF) | `"\n"` |
| `"\r"` (CR)     | `"\n"` |
| `"\n"` (LF)     | `"\n"` |

Each line break is replaced on its own and none are merged or dropped, so the number of lines stays the same.
For example, `"\r\n\r\n"` becomes `"\n\n"`, and `"\n\r"` (LF followed by CR) becomes `"\n\n"`.

Only these three variants are recognized. Vertical tab, form feed and the Unicode separators
(`U+0085`, `U+2028`, `U+2029`) are not touched. The function works on bytes, so it is safe for any
encoding and does not fail on invalid UTF-8.

```php
use function SergeYakovlev\Support\Str\str_normalize_newlines;

$text = str_normalize_newlines("first\r\nsecond\rthird\nfourth");
// "first\nsecond\nthird\nfourth"
```

The return type follows the input: a `string` gives a `string`, and `null` gives `null`. Static analysers
know this, so for a `string` input the function can be used anywhere in a pipe:

```php
$lines = $input
    |> str_normalize_newlines(...)
    |> mb_trim(...)
    |> (static fn(string $v): array => explode("\n", $v));
```
