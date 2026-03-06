# Настройка сервера inWork

## 1. Apache VirtualHost

Создайте или отредактируйте конфиг сайта:

```bash
sudo nano /etc/apache2/sites-available/in-work.conf
```

**Содержимое (подставьте свой путь к проекту):**

```apache
<VirtualHost *:80>
    ServerName in-work.krg-ktsk.kz
    Redirect permanent / https://in-work.krg-ktsk.kz/
</VirtualHost>

<VirtualHost *:443>
    ServerName in-work.krg-ktsk.kz
    
    # Корень проекта (НЕ public!)
    DocumentRoot /var/www/in-work
    
    <Directory /var/www/in-work>
        AllowOverride All
        Require all granted
    </Directory>
    
    # SSL (если используете Let's Encrypt)
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/in-work.krg-ktsk.kz/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/in-work.krg-ktsk.kz/privkey.pem
</VirtualHost>
```

**Важно:** `DocumentRoot` — корень проекта (`/var/www/in-work`), не `public`!

---

## 2. Включить сайт и mod_rewrite

```bash
# Включить mod_rewrite
sudo a2enmod rewrite

# Включить сайт
sudo a2ensite in-work.conf

# Проверить конфиг
sudo apache2ctl configtest

# Перезагрузить Apache
sudo systemctl reload apache2
```

---

## 3. Путь к проекту

Узнайте, где лежит проект на сервере:

```bash
# Например
ls -la /var/www/
# или
ls -la /home/sh/in-work/
```

Подставьте этот путь в `DocumentRoot` и `<Directory>`.

---

## 4. Проверка

| URL | Ожидание |
|-----|----------|
| https://in-work.krg-ktsk.kz/ | Главная |
| https://in-work.krg-ktsk.kz/login | Страница входа |
| https://in-work.krg-ktsk.kz/assets/css/app.css | CSS загружается |

---

## 5. Если @ вместо точки в домене

Ошибка `in-work@krg.ktsk.kz` обычно из-за опечатки в конфиге. Проверьте:

```bash
grep -r "in-work" /etc/apache2/
```

Везде должно быть `in-work.krg-ktsk.kz` (точка перед krg).
