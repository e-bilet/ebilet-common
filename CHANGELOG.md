# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.2] - 2024-12-19

### Added
- **Service Communication System**: Tamamen yeni servisler arası iletişim sistemi
- **SOLID Architecture**: Interface, Factory, Manager pattern'leri ile temiz mimari
- **ServiceClientInterface**: Servis client'ları için contract
- **ServiceClientFactory**: Factory pattern ile client oluşturma
- **ServiceCommunicationProvider**: Ayrı provider ile service registration
- **ServiceConfigurationException**: Özel exception handling
- **ServiceManager**: Service yönetimi ve health check
- **User & Order Facades**: Basit facade kullanımı (`User::get()`, `Order::post()`)
- **Configuration Driven**: Config'den service URL'leri okuma
- **Error Handling**: Kapsamlı error handling ve logging
- **Dependency Injection**: Container üzerinden yönetim
- **Health Check**: Service sağlık kontrolü
- **Method Chaining**: `withToken()`, `withHeaders()`, `timeout()` chaining

### Changed
- **Architecture**: LoggingServiceProvider'dan service communication ayrıldı
- **SOLID Principles**: Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, Dependency Inversion
- **Clean Code**: Separation of Concerns, Dependency Injection, Error Handling
- **Configuration**: Service URL'leri config'den okuma
- **Facade Pattern**: Basitleştirilmiş facade kullanımı

### Technical Details
- **Interface Segregation**: `ServiceClientInterface` ile contract
- **Factory Pattern**: `ServiceClientFactory` ile loose coupling
- **Provider Separation**: Service communication ayrı provider'da
- **Exception Handling**: `ServiceConfigurationException` ile özel error handling
- **Configuration Management**: `ebilet-common.services` config'den yönetim
- **Health Monitoring**: Service sağlık kontrolü ve monitoring
- **Logging Integration**: Service request'leri için otomatik logging

### Usage Examples
```php
// User Service
User::get('/v1/users/123');
User::post('/v1/users', ['name' => 'John']);

// Order Service
Order::post('/v1/orders', ['user_id' => 123]);

// With Token & Headers
User::withToken('jwt-token')->get('/v1/users/123/profile');
Order::withHeaders(['X-Custom' => 'value'])->post('/v1/orders');

// Method Chaining
User::withToken('token')->timeout(30)->post('/v1/users', $data);
```

## [1.2.1] - 2024-12-19

### Added
- **ConfigManager**: Merkezi config yönetimi
- **Config Facade**: Kolay config erişimi
- **Modular Configuration**: Genişletilmiş config yapısı
- **Documentation**: Kapsamlı dokümantasyon
- **Security Configuration**: Güvenlik ayarları
- **Business Events**: İş olayları konfigürasyonu
- **Error Handling**: Hata yönetimi ayarları
- **Performance Configuration**: Performans ayarları

### Changed
- **Configuration Structure**: Daha modüler ve genişletilebilir
- **Environment Variables**: Daha fazla env desteği
- **Documentation**: Kapsamlı kullanım örnekleri

## [1.2.0] - 2024-12-19

### Added
- **Centralized Logger**: Merkezi loglama sistemi
- **Queue Manager**: RabbitMQ entegrasyonu
- **HTTP Logging Middleware**: HTTP request/response loglama
- **Config Publishing**: Config dosyalarını publish etme
- **Facade Support**: Log ve Queue facade'ları

### Changed
- **Package Structure**: Daha organize dosya yapısı
- **Service Provider**: Laravel service provider entegrasyonu

## [1.1.0] - 2024-12-19

### Added
- **Basic Logging**: Temel loglama fonksiyonları
- **Queue Support**: Kuyruk sistemi desteği

## [1.0.0] - 2024-12-19

### Added
- **Initial Release**: İlk sürüm
- **Basic Structure**: Temel paket yapısı 