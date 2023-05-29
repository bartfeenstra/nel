# Nel

![Test status](https://github.com/bartfeenstra/nel/workflows/Test/badge.svg?branch=main)

Nel is an expression parser that turns a string into an [AST](https://en.wikipedia.org/wiki/Abstract_syntax_tree)

## Requirements
- PHP 8.1+
- `ext-mbstring`

## Syntax
- whitespace-insensitive
- strict typing
  - `true` and `false`
  - `null`
  - strings: `'string'` or `"string"`
  - integers: `123`
  - lists: `[123, 456]`
  - structs with fields
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
- data and fields
  - `foo` for the global `foo` data
  - `foo.bar` for the `bar` field on the root data
  - `foo.bar.baz` for the `baz` field on the `bar` field on the root data