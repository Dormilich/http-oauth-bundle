# http-oauth bundle

Symfony 5 bundle for `dormilich/http-oauth`, which is an extension to `dormilich/http-client`
with the purpose of adding OAuth 2.0 authorisation to request that require them. Requests
that do not require authorisation (resp. lack authorisation credentials) are run without
authorisation.

## Installation

This bundle requires Symfony 5 as well as a [PSR-16](https://www.php-fig.org/psr/psr-16/),
[PSR-17](https://www.php-fig.org/psr/psr-17/), and [PSR-18](https://www.php-fig.org/psr/psr-18/)
implementation.

- [PSR-16 libraries](https://packagist.org/providers/psr/simple-cache-implementation)
- [PSR-17 libraries](https://packagist.org/providers/psr/http-factory-implementation)
- [PSR-18 libraries](https://packagist.org/providers/psr/http-client-implementation)

However, it makes sense to use `symfony/cache` as PSR-16 and `symfony/http-client` as PSR-18
implementation in a Symfony project.

You can then install this bundle via composer:
```
composer require dormilich/http-oauth-bundle
```

To set up `dormilich/http-client`, also install `dormilich/http-client-bundle`.

## Configuration

The configuration is the standard way to set up the OAuth credentials. Without adding at least one
type of credentials the authorisation will be ignored.

### Single set of credentials for all requests

The most simple setup is when you only have a single set of credentials. This will be used on every
request done with the HTTP client (even when no authorisation is required).
```yaml
# note that the actual credentials should be loaded from the environment
dormilich_http_oauth:
  credentials:
    default:
      client: '%env(CLIENT_ID)%'
      secret: '%env(CLIENT_SECRET)%'
      server: https://example.com/oauth/token
```

### Credentials for specific resource domains

This is for associating a set of credentials with a set of specific domains (using Google APIs for
demonstration). Other requests will not get an Authorization header added.
```yaml
# this will only add authorisation to requests to googleapis.com (and its subdomains)
dormilich_http_oauth:
  credentials:
    domains:
      - client: '%env(CLIENT_ID)%'
        secret: '%env(CLIENT_SECRET)%'
        server: https://example.com/oauth/token
        hosts: googleapis.com
```

### Custom credentials strategy

If neither of these configuration strategies match your case, then you can create your own
credentials provider by creating a class that implements `CredentialsProviderInterface`. To make
the extension use this provider, redefine the `CredentialsProviderInterface` service with your class.
```yaml
# services.yaml
services:
  Dormilich\HttpOauth\Credentials\CredentialsProviderInterface:
    class: App\CustomCredentialsProvider
```
Alternatively, or if you need to use multiple providers, it suffices to tag these providers.
This also works in combination with configured providers.
```yaml
# services.yaml
services:
  App\CustomCredentialsProvider:
    tags: dormilich_http_oauth.credentials
```
