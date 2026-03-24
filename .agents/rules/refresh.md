---
trigger: always_on
---

dont forget to refresh if you just edit blade templates or widgets

sail artisan optimize:clear
sail artisan filament:clear-cached-components
sail artisan responsecache:clear
vendor/bin/sail artisan scribe:generate
