# NmureEncryptorBundle

[![Build Status](https://travis-ci.org/nicolasmure/NmureEncryptorBundle.svg?branch=master)](https://travis-ci.org/nicolasmure/NmureEncryptorBundle)
[![Coverage Status](https://coveralls.io/repos/github/nicolasmure/NmureEncryptorBundle/badge.svg?branch=master)](https://coveralls.io/github/nicolasmure/NmureEncryptorBundle?branch=master)

A data encryptor Bundle for Symfony using PHP's openssl.

## Installation
### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```bash
$ composer require nmure/encryptor-bundle "dev-master"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle
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

### Step 3: Configure the Bundle
Add the following configuration to your `config.yml` file :
```yaml
# app/config/config.yml
nmure_encryptor:
    secret: theSecretKeyGoesHere # should be a complex key defined in your parameters.yml file
    prefer_base64: true # optional, default true. Indicates if the encrypted data should be converted to base64
```

## Usage
The encryptor is defined as a service under the `nmure_encryptor.encryptor` key.

Simply access to this service and call the `encrypt` / `decrypt` functions :
```php
// from a controller :
$encryptor = $this->get('nmure_encryptor.encryptor');
$encrypted = $encryptor->encrypt('hello world');
// ...
$decrypted = $encryptor->decrypt($encrypted);

```
**Warning:** the encryptor uses an initialization vector in addition to the secret key to encrypt data.
This initialization vector is randomly generated to be sure that 2 encryptions of the same data with the
same key won't produce the same encrypted output.

In order to decrypt data, **you'll have to use the same initialization vector as you used to encrypt this data**,
otherwise, your data won't be readable.

You can access to the initialization vector on the encryptor using these functions:
```php
string Encryptor::getIv();
void Encryptor::setIv(string $iv);
```
Be sure to store the initialization vector used to crypt data along side to the crypted data
to be able to decrypt it later.

If the `prefer_base64` config setting is set to `true`, the encrypted data will be converted to a MIME base64 string
instead of staying a binary string.
The itinialization vector will also be converted to a base64 string.

## Services
This Bundle exposes the following services which are returning a `Nmure\EncryptorBundle\Encryptor\EncryptorInterface`:
- `nmure_encryptor.encryptor`: alias for `nmure_encryptor.encryptor.original` or `nmure_encryptor.adapter.base64`
(depends on the value of the `prefer_base64` config parameter).
- `nmure_encryptor.encryptor.original`: the original encryptor which returns encrypted data as a binary string
(`Nmure\EncryptorBundle\Encryptor\Encryptor`).
- `nmure_encryptor.adapter.base64`: the base64 adapter which returns encrypted data as a MIME base64 string
(`Nmure\EncryptorBundle\Adapter\Base64Adapter`).

## Informations
Useful informations about:
- [PHP's openssl](http://thefsb.tumblr.com/post/110749271235/using-opensslendecrypt-in-php-instead-of "Using openssl_en/decrypt() in PHP instead of Mcrypt")
- [Initialization Vector usage](http://stackoverflow.com/questions/11821195/use-of-initialization-vector-in-openssl-encrypt "Use of Initialization Vector in openssl_encrypt")

## License
This Bundle is licensed under the MIT License.
More informations in the [LICENSE](/LICENSE) file.

## Issues / feature requests
Please use this Github repository page to report issues and to ask / propose feature.
