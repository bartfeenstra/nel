# Nel

![Test status](https://github.com/bartfeenstra/nel/workflows/Test/badge.svg?branch=main)

Nel is an expression parser that turns a string into an [AST](https://en.wikipedia.org/wiki/Abstract_syntax_tree)

## Requirements
- PHP 8.1+
- `ext-mbstring`

## Syntax
- whitespace-insensitive
- literals
  - boolean: `true` and `false`
  - null: `null`
  - strings: `'string'` or `"string"`
  - integers: `123`
  - lists: `[123, 456]`
- unary operators
  - `not`
- binary operators
  - `starts with`
  - `ends with`
  - `contains`
  - `in`
  - `and`
  - `or`
  - `le`
  - `lt`
  - `eq`
  - `gt`
  - `ge`
  - `is not`
  - `is`
  - `add`
  - `sub`
  - `mul`
- data access
  - `.` for the root data (a scalar or a struct)
  - `.foo` for the `foo` field on the root data struct
  - `.foo.bar` for the `bar` field on the struct contained by the `foo` field on the root data struct