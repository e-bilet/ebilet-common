# Changelog

Tüm önemli değişiklikler bu dosyada belgelenecektir.

## [1.2.1] - 2024-12-19

### ✨ Yeni Özellikler
- **ConfigManager sınıfı eklendi**: Config değerlerine esnek erişim sağlayan yeni sınıf
- **Config Facade eklendi**: Kolay kullanım için `Ebilet\Common\Facades\Config` facade'ı
- **Gelişmiş config yapısı**: Daha modüler ve organize config dosyaları
- **Environment'a özel config desteği**: Farklı ortamlar için özel konfigürasyon
- **Config doğrulama sistemi**: Config değerlerinin geçerliliğini kontrol eden sistem

### 🔧 İyileştirmeler
- **Service Provider güncellemeleri**: Config dosyalarını merge etme ve ayrı yayınlama seçenekleri
- **CentralizedLogger güncellemeleri**: ConfigManager entegrasyonu
- **Kapsamlı dokümantasyon**: `CONFIGURATION.md` ile detaylı kullanım kılavuzu
- **Örnek kullanım dosyası**: `examples/config-usage.php` ile pratik örnekler

### 📝 Yeni Config Bölümleri
- **Business Events Configuration**: İş olayları için konfigürasyon
- **Error Handling Configuration**: Hata yönetimi ayarları
- **Security Configuration**: Güvenlik ile ilgili loglama ayarları
- **Performance Monitoring**: Gelişmiş performans izleme ayarları

### 🚀 Yeni Environment Variables
- RabbitMQ SSL ayarları
- Queue retry mekanizması
- Performance metrik toplama ayarları
- Business events detay ayarları
- Security logging ayarları

### 📚 Dokümantasyon
- Kapsamlı konfigürasyon rehberi
- Environment variable örnekleri
- Güvenlik notları
- Sorun giderme rehberi
- Kullanım örnekleri

### 🔒 Güvenlik
- Hassas veri filtreleme geliştirmeleri
- SSL/TLS desteği
- Güvenlik odaklı config seçenekleri

## [1.2.0] - 2024-12-18

### ✨ Yeni Özellikler
- HTTP logging middleware eklendi
- Performance monitoring özellikleri
- Business events logging
- Queue management sistemi

### 🔧 İyileştirmeler
- RabbitMQ provider geliştirmeleri
- Logging facade'ları
- Error handling iyileştirmeleri

## [1.1.0] - 2024-12-17

### ✨ Yeni Özellikler
- Merkezi loglama sistemi
- RabbitMQ entegrasyonu
- Queue management
- Logging facade'ları

### 🔧 İyileştirmeler
- Service provider yapısı
- Config yönetimi
- Error handling

## [1.0.0] - 2024-12-16

### 🎉 İlk Sürüm
- Temel logging altyapısı
- RabbitMQ bağlantısı
- Queue sistemi
- Service provider yapısı 