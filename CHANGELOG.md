# Changelog

## v2.0.0 (2017-12-02)
Symfony 4 support.

**BC breaks** :
- The built-in [formatters](/README.md#formatters) services are now
declared as `private`.
- Symfony 4 is now the minimum requirement, as PHP 7.1.

## v1.0.0 (2016-08-07)
Integration of the [nmure/encryptor](https://github.com/nicolasmure/NmureEncryptor "PHP data encryptor using open_ssl") library.
First stable release. BC break with the previous dev versions.
Usable in production.

## v0.3.0 (2016-05-13)
Ability to choose the cipher method for each encryptors.
BC break with previous version due to the Encryptor's __constructor parameters changes.

## v0.2.0 (2016-04-07)
Ability to declare multiple encryptors.
BC break with previous version due to configuration declaration changes.

## v0.1.0 (2016-04-04)
Initial commit.
