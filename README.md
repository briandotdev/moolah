# Moolah

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

A simple wrapper for the Braintree PHP library.

## Install

Via Composer

``` bash
$ composer require rdrnnr87/moolah
```

## Usage

Generating a token.

``` php
$config = [
    'environment' => 'sandbox',
    'merchantId' => 'yourMerchantId',
    'publicKey' => 'yourPublicKey',
    'privateKey' => 'yourPrivateKey'
];

$moolah = new Moolah($config);
$token = $moolah->getToken();
```

Making a one time charge.

``` php
$moolah = new Moolah($config);
$moolah->charge($amount, $nonce);
```

## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email brian.johnsonx@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/moolah/moolah.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/moolah/moolah/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/moolah/moolah.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/moolah/moolah.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/moolah/moolah.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/moolah/moolah
[link-travis]: https://travis-ci.org/moolah/moolah
[link-scrutinizer]: https://scrutinizer-ci.com/g/moolah/moolah/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/moolah/moolah
[link-downloads]: https://packagist.org/packages/moolah/moolah
[link-author]: https://github.com/rdrnnr87
[link-contributors]: ../../contributors
