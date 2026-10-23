# Filament 4 sürüm hattı

Bu değişiklik `codex/filament-4` dalında hazırlanır ve `2.x` bakım dalına PR ile alınır. İlk hedef etiket `v2.0.0` olur; etiket yalnız PR kabul edildikten sonra oluşturulur.

- PHP: 8.3 ve üzeri; doğrulama ortamı PHP 8.3.
- Filament: 4.x, Laravel: 11.28 ve üzeri 11.x.
- `main` ve mevcut 1.x etiketleri Filament 3 kullanan uygulamalar içindir. Eski etiketler değiştirilmez, geriye uyumluluk bu ayrı sürüm hattıyla korunur.
- Filament 4 uygulamaları PR kabul edilene kadar `dev-codex/filament-4` kullanabilir; Composer kilidi incelenen commit'i sabitler. Etiket sonrası `^2.0` aralığına geçilir.
- CI bu dalda Filament 4 / Laravel 11 / PHP 8.3'ü doğrular. Eski dalın Laravel 10/11 matrisi kendi dalında kalır.
- Laravel 11'in destek süresi bittiğinden çözümlemede kullanılan `--no-blocking` yalnız yerel/CI komutuna aittir. Kalıcı Composer ayarı eklenmez.

## Varlıklar

Node 22.12+ ile `npm ci && npm run build` çalıştırılır. Vite, dağıtılan `resources/dist/plugin.js` ve `plugin.css` dosyalarını üretir; Mix kaldırılmıştır. Tailwind 4 paket CSS'i preflight içermez. Özel panel teması kullanan uygulamalar temalarına `@source "../../vendor/ryangjchandler/filament-navigation/resources/views";` eklemelidir.
