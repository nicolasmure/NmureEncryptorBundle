# NmureEncryptorBundle

[![Build Status](https://travis-ci.org/nicolasmure/NmureEncryptorBundle.svg?branch=master)](https://travis-ci.org/nicolasmure/NmureEncryptorBundle)
[![Coverage Status](https://coveralls.io/repos/github/nicolasmure/NmureEncryptorBundle/badge.svg?branch=master)](https://coveralls.io/github/nicolasmure/NmureEncryptorBundle?branch=master)

A Symfony Bundle for the [nmure/encryptor](https://github.com/nicolasmure/NmureEncryptor "PHP data encryptor using open_ssl") library.

**Warning:** This Bundle is still under development and shouldn't be used in production yet.

## Table of contents

- [Introduction](#introduction)
- [Installation](#installation)
    - [Step 1 : Download the Bundle](#step-1--download-the-bundle)
    - [Step 2 : Enable the Bundle](#step-2--enable-the-bundle)
    - [Step 3 : Configure the Bundle](#step-3--configure-the-bundle)
- [Usage](#usage)
- [Configuration](#configuration)
- [Formatters](#formatters)
- [Informations](#informations)
- [License](#license)
- [Issues / feature requests](#issues--feature-requests)
- [Changes](#changes)

## Introduction

This Bundle integrates the [nmure/encryptor](https://github.com/nicolasmure/NmureEncryptor "PHP data encryptor using open_ssl") library into Symfony.
It is **recommended** to read the lib's documentation before continuing here.

## Installation

### Step 1 : Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require nmure/encryptor-bundle "~0.3.0"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2 : Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
// app/AppKernel.php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Nmure\EncryptorBundle\NmureEncryptorBundle(),
            // ...
        );
    }
}
```

### Step 3 : Configure the Bundle

Add the following configuration to your `config.yml` file :
```yaml
# app/config/config.yml
nmure_encryptor:
    encryptors:
        my_encryptor:
            secret: 452F93C1A737722D8B4ED8DD58766D99 # should be a complex key defined in your parameters.yml file
        # you can add as many encryptors as you want
        my_other_encryptor:
            secret: 6A4E723D3F4AA81ACF776DCF2B6AEC45 # you should use one unique secret key by encryptor
```

## Usage
You can access to the encryptors defined in the `config.yml` file by specifying your encryptor's name, e.g. :
accessing to `nmure_encryptor.my_encryptor` will return the encryptor defined under the `my_encryptor` key.

All the encryptors are instances of the [`Nmure\Encryptor\Encryptor`](https://github.com/nicolasmure/NmureEncryptor/blob/master/src/Encryptor.php "Nmure\Encryptor\Encryptor") class.
To use them, call the `encrypt` / `decrypt` functions :
```php
// from a controller :
$encryptor = $this->get('nmure_encryptor.my_encryptor');
$encrypted = $encryptor->encrypt('hello world');
// ...
$decrypted = $encryptor->decrypt($encrypted);
```

## Configuration

Here is the list of all the confifuration options that you can use
in your `app/config.yml` file under the `nmure_encryptor` key:
- `encryptors` : array, **required**. The main array of encryptors.
Must contain at least one encryptor.
    - `my_encryptor` : creates a new encryptor service named `nmure_encryptor.my_encryptor`.
        - `secret` : string, **required**. The secret encryption key.
        - `cipher` : string, optional. The cipher method (default to `AES-256-CBC`).
        - `convert_hex_key_to_bin` : boolean, optional. Indicates if the hex secret key given below
        should be converted to a binary key. This could be useful when [sharing encrypted data
        with C# apps](https://github.com/nicolasmure/NmureEncryptor#using-the-hexformatter-with-a-c-app "Using the HexFormatter with a C# app")
        for instance.
        - `formatter` : string, optional. The service name of the [formatter](#formatters) to use with this encryptor.
        You can create your own formatter, it has to implement the [`FormatterInterface`](https://github.com/nicolasmure/NmureEncryptor/blob/master/src/Formatter/FormatterInterface.php "Nmure\Encryptor\Formatter\FormatterInterface").
        - `disable_auto_iv_update` : boolean, optional. Set it to true if you want to disable
        the automatic IV generation on the encryptor before each encryption.
        The automatic IV update is enabled by default.
    - `second_encryptor` : here comes an other encryptor ... :)

## Formatters

The bundle wraps the lib's [formatters](https://github.com/nicolasmure/NmureEncryptor#formatters)
into services that you can use when configuring your encryptors :
- [`Base64Formatter`](https://github.com/nicolasmure/NmureEncryptor#base64formatter "Nmure\Encryptor\Formatter\Base64Formatter") => `nmure_encryptor.formatters.base64_formatter`
- [`HexFormatter`](https://github.com/nicolasmure/NmureEncryptor#hexformatter "Nmure\Encryptor\Formatter\HexFormatter") => `nmure_encryptor.formatters.hex_formatter`

## Informations

Useful informations about:
- [PHP's openssl](http://thefsb.tumblr.com/post/110749271235/using-opensslendecrypt-in-php-instead-of "Using openssl_en/decrypt() in PHP instead of Mcrypt")
- [Initialization Vector usage](http://stackoverflow.com/questions/11821195/use-of-initialization-vector-in-openssl-encrypt "Use of Initialization Vector in openssl_encrypt")

## License

This Bundle is licensed under the MIT License.
More informations in the [LICENSE](/LICENSE) file.

## Issues / feature requests

Please use this Github repository page to report issues and to ask / propose features.

## Changes

See the [changelog](/CHANGELOG.md "changelog") for more details.
