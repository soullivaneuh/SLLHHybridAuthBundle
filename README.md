# SLLHHybridAuthBundle

Integrates [HybridAuth](https://github.com/hybridauth/hybridauth) library on Symfony.

**Please note: This bundle is not ready to use yet!**

I'm making a discussion with the HybridAuth's maintainers to make this project feasible.

Please see: https://github.com/hybridauth/hybridauth/issues/456

If it's accepted for HybridAuth 3.x, this bundle will supports this version.
Otherwise, it will be abandoned because HybridAuth not compatible with Symfony.

If you think I'm wrong or just want to add arguments on [this discussion](https://github.com/hybridauth/hybridauth/issues/456), feel free to add some comments! ;-)

[![Latest Stable Version](https://poser.pugx.org/sllh/hybridauth-bundle/v/stable)](https://packagist.org/packages/sllh/hybridauth-bundle)
[![Latest Unstable Version](https://poser.pugx.org/sllh/hybridauth-bundle/v/unstable)](https://packagist.org/packages/sllh/hybridauth-bundle)
[![License](https://poser.pugx.org/sllh/hybridauth-bundle/license)](https://packagist.org/packages/sllh/hybridauth-bundle)

[![Total Downloads](https://poser.pugx.org/sllh/hybridauth-bundle/downloads)](https://packagist.org/packages/sllh/hybridauth-bundle)
[![Monthly Downloads](https://poser.pugx.org/sllh/hybridauth-bundle/d/monthly)](https://packagist.org/packages/sllh/hybridauth-bundle)
[![Daily Downloads](https://poser.pugx.org/sllh/hybridauth-bundle/d/daily)](https://packagist.org/packages/sllh/hybridauth-bundle)

[![Build Status](https://travis-ci.org/Soullivaneuh/SLLHHybridAuthBundle.svg?branch=master)](https://travis-ci.org/Soullivaneuh/SLLHHybridAuthBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Soullivaneuh/SLLHHybridAuthBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Soullivaneuh/SLLHHybridAuthBundle/?branch=master)
[![Code Climate](https://codeclimate.com/github/Soullivaneuh/SLLHHybridAuthBundle/badges/gpa.svg)](https://codeclimate.com/github/Soullivaneuh/SLLHHybridAuthBundle)
[![Coverage Status](https://coveralls.io/repos/Soullivaneuh/SLLHHybridAuthBundle/badge.svg?branch=master)](https://coveralls.io/r/Soullivaneuh/SLLHHybridAuthBundle?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/158b027e-ef93-4130-bdfd-9e6902d316d9/mini.png)](https://insight.sensiolabs.com/projects/158b027e-ef93-4130-bdfd-9e6902d316d9)

## Prerequisites

This version of the bundle requires Symfony 2.3+.

## Installation

### Download using composer

``` bash
$ php composer.phar require sllh/hybridauth-bundle "~1.0"
```

### Enable the bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new SLLH\HybridAuthBundle\SLLHHybridAuthBundle(),
    );
}
```

## Usage

*TODO*

## License

This bundle is under the MIT license. See the complete license on the [LICENSE](https://github.com/Soullivaneuh/SLLHHybridAuthBundle/blob/master/LICENSE) file.

## Todo

 * Make it working
 * Unit test
 * Changelog file before pusing new stable version
