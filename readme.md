## Seek Simple Checkout
Simple checkout allow to apply discount / deal for set of customer group with different rules configured. Functionality such as buy X get Y and also fixed amount set off price.

## Requirements
- PHP >= 5.6.4
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## Development Setup
```
$ composer install
$ composer dump-autoload -o
```
No other complex development setup. Since, this code meant only for testing.

## Testing
```
vendor/bin/phpunit --process-isolation --debug
```
## License
The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
