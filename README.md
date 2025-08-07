# Dual Optimus

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mostafaaminflakes/dual-optimus.svg?style=flat-square)](https://packagist.org/packages/mostafaaminflakes/dual-optimus)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mostafaaminflakes/dual-optimus/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mostafaaminflakes/dual-optimus/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mostafaaminflakes/dual-optimus/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mostafaaminflakes/dual-optimus/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mostafaaminflakes/dual-optimus.svg?style=flat-square)](https://packagist.org/packages/mostafaaminflakes/dual-optimus)
[![License](https://img.shields.io/packagist/l/mostafaaminflakes/dual-optimus.svg?style=flat-square)](https://packagist.org/packages/mostafaaminflakes/dual-optimus)
[![PHP Version Require](https://img.shields.io/packagist/php-v/mostafaaminflakes/dual-optimus?style=flat-square)](https://packagist.org/packages/mostafaaminflakes/dual-optimus)

A powerful PHP package that extends [Jenssegers\Optimus](https://github.com/jenssegers/optimus) to support both 64-bit and 32-bit ID obfuscation with intelligent auto-detection, multiple connections, and seamless Laravel integration.

## ✨ Features

- **🔧 Built on Jenssegers\Optimus**: Leverages the proven and battle-tested 32-bit implementation
- **🚀 Dual Bit Support**: Seamlessly handles both 32-bit and 64-bit integers with automatic detection
- **🔄 100% Backward Compatible**: Drop-in replacement for existing Jenssegers\Optimus implementations
- **🔗 Multiple Connections**: Configure and use multiple Optimus instances for different use cases
- **🧠 Intelligent Auto-Detection**: Automatically selects the appropriate bit size based on input value
- **🎯 Laravel Ready**: Complete Laravel integration with service provider, facade, and configuration
- **⚡ High Performance**: Optimized for speed with minimal overhead
- **🧪 Thoroughly Tested**: Comprehensive PHPUnit test suite with Orchestra Testbench
- **📦 Easy Installation**: Simple Composer installation with auto-discovery

## 📋 Requirements

- **PHP**: 8.1 or higher
- **Extensions**: `ext-gmp` (for 64-bit operations)
- **Dependencies**: `jenssegers/optimus ^1.1`
- **Laravel**: 9.0+ (optional, for Laravel integration)

## 📦 Installation

Install the package via Composer:

```bash
composer require mostafaaminflakes/dual-optimus
```

### Laravel Integration

The package automatically registers itself in Laravel 5.5+. For older versions, manually add to `config/app.php`:

```php
'providers' => [
    MostafaAminFlakes\DualOptimus\DualOptimusServiceProvider::class,
],

'aliases' => [
    'DualOptimus' => MostafaAminFlakes\DualOptimus\Facades\DualOptimus::class,
],
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="MostafaAminFlakes\DualOptimus\DualOptimusServiceProvider"
```

## ⚙️ Configuration

### Environment Variables

Add these variables to your `.env` file:

```env
# 64-bit configuration (recommended for new projects)
DUAL_OPTIMUS_PRIME_64=9223372036854775783
DUAL_OPTIMUS_INVERSE_64=9223372036854775783
DUAL_OPTIMUS_RANDOM_64=4611686018427387904

# 32-bit configuration (for backward compatibility)
DUAL_OPTIMUS_PRIME_32=1580030173
DUAL_OPTIMUS_INVERSE_32=59260789
DUAL_OPTIMUS_RANDOM_32=1163945558
```

### Configuration File

The published `config/dual-optimus.php` file contains:

```php
return [
    'default' => 'main',
    
    'connections' => [
        'main' => [
            'prime'   => env('DUAL_OPTIMUS_PRIME_64', 9223372036854775783),
            'inverse' => env('DUAL_OPTIMUS_INVERSE_64', 9223372036854775783),
            'random'  => env('DUAL_OPTIMUS_RANDOM_64', 4611686018427387904),
            'size'    => 64,
        ],
        
        'legacy' => [
            'prime'   => env('DUAL_OPTIMUS_PRIME_32', 1580030173),
            'inverse' => env('DUAL_OPTIMUS_INVERSE_32', 59260789),
            'random'  => env('DUAL_OPTIMUS_RANDOM_32', 1163945558),
            'size'    => 32,
        ],
    ],
];
```

## 🚀 Usage

### Basic Usage

```php
use MostafaAminFlakes\DualOptimus\Facades\DualOptimus;

// Automatic bit-size detection
$encoded = DualOptimus::encode(123);           // Uses 32-bit (legacy connection)
$decoded = DualOptimus::decode($encoded);      // Returns: 123

// Large values automatically use 64-bit
$bigEncoded = DualOptimus::encode(9876543210); // Uses 64-bit (main connection)
$bigDecoded = DualOptimus::decode($bigEncoded); // Returns: 9876543210
```

### Force 64-bit Operations

```php
// Force 64-bit encoding for any value
$encoded64 = DualOptimus::encode64(123);
$decoded64 = DualOptimus::decode64($encoded64);
```

### Multiple Connections

```php
// Use specific connections
$mainConnection = DualOptimus::connection('main');     // 64-bit
$legacyConnection = DualOptimus::connection('legacy'); // 32-bit

$mainEncoded = $mainConnection->encode(123);
$legacyEncoded = $legacyConnection->encode(123);

// List available connections
$connections = DualOptimus::getConnections(); // ['main', 'legacy']
```

### Direct Manager Usage

```php
$manager = app('dual-optimus');

// Use default connection
$encoded = $manager->encode(123);
$decoded = $manager->decode($encoded);

// Get specific connection
$connection = $manager->connection('main');
$encoded = $connection->encode(123);
```

### Access Underlying Optimus

```php
// Access the Jenssegers\Optimus instance for 32-bit operations
$optimus32 = DualOptimus::getOptimus32();
$encoded = $optimus32->encode(123);

// Use Optimus utilities
$prime = \Jenssegers\Optimus\Optimus::generateRandomPrime();
$inverse = \Jenssegers\Optimus\Optimus::calculateInverse($prime);
```

## 🔧 Artisan Commands

Generate new Optimus keys:

```bash
# Generate 32-bit keys
php artisan dual-optimus:generate 32

# Generate 64-bit keys  
php artisan dual-optimus:generate 64
```

## 🧪 Testing

Run the complete test suite:

```bash
composer test
```

Run tests with coverage report:

```bash
composer test-coverage
```

Run specific test suites:

```bash
# Unit tests only
vendor/bin/phpunit tests/Unit

# Feature tests only  
vendor/bin/phpunit tests/Feature
```

## 🔄 Migration from Jenssegers\Optimus

Dual Optimus is a **100% drop-in replacement** for Jenssegers\Optimus:

```php
// Before (Jenssegers\Optimus)
$optimus = new \Jenssegers\Optimus\Optimus($prime, $inverse, $random);
$encoded = $optimus->encode(123);

// After (Dual Optimus) - same result!
$encoded = DualOptimus::encode(123);

// Access original Optimus if needed
$originalOptimus = DualOptimus::getOptimus32();
```

**All existing encoded values will decode correctly** - no data migration required!

## 📊 Performance

Dual Optimus adds minimal overhead while providing significant functionality:

- **32-bit operations**: Identical performance to Jenssegers\Optimus (uses it directly)
- **64-bit operations**: Optimized GMP operations with caching
- **Auto-detection**: Simple integer comparison with negligible cost
- **Memory usage**: Minimal additional memory footprint

## 🛡️ Security Considerations

- **Cryptographically Secure**: Uses the same proven algorithms as Jenssegers\Optimus
- **No Data Leakage**: Values are obfuscated, not encrypted (reversible by design)
- **Key Management**: Store your prime/inverse/random values securely
- **Environment Variables**: Use `.env` files and never commit keys to version control

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Add tests** for new functionality
4. **Ensure** all tests pass (`composer test`)
5. **Commit** your changes (`git commit -m 'Add amazing feature'`)
6. **Push** to the branch (`git push origin feature/amazing-feature`)
7. **Open** a Pull Request

### Development Setup

```bash
git clone https://github.com/mostafaaminflakes/dual-optimus.git
cd dual-optimus
composer install
composer test
```

## 📝 Changelog

### v1.0.0 - Initial Release
- ✅ 64-bit ID support with automatic detection
- ✅ Full backward compatibility with 32-bit IDs  
- ✅ Multiple connections support
- ✅ Laravel service provider and facade
- ✅ Comprehensive test suite with 100% coverage
- ✅ Artisan command for key generation

## 📄 License

This package is open-sourced software licensed under the [MIT License](LICENSE).

## 🙏 Credits

- **[Mostafa Amin](https://github.com/mostafaaminflakes)** - Creator and maintainer
- **[Jens Segers](https://github.com/jenssegers)** - Original Optimus package
- **All contributors** who help improve this package

## 🆘 Support

- **Documentation**: [GitHub Wiki](https://github.com/mostafaaminflakes/dual-optimus/wiki)
- **Issues**: [GitHub Issues](https://github.com/mostafaaminflakes/dual-optimus/issues)
- **Discussions**: [GitHub Discussions](https://github.com/mostafaaminflakes/dual-optimus/discussions)

---

<p align="center">
<strong>Made with ❤️ for the PHP community</strong>
</p>