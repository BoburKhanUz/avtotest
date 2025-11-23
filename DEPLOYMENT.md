# 🚀 Avto Test Platform - To'liq Deployment Qo'llanma

## ✅ Barcha Funksiyalar Tayyor

### 1. Backend (Laravel 10)
- ✅ RESTful API (Sanctum authentication)
- ✅ Admin Panel (AdminLTE 4.0)
- ✅ Database Models (Users, Tests, Questions, Subscriptions, Payments)
- ✅ Services (Payme, Click, AdaptiveLearning)
- ✅ Migrations va Seeders

### 2. To'lov Tizimlari
- ✅ **Payme** - Merchant API (webhook support)
- ✅ **Click** - Prepare/Complete API
- ✅ **Stripe** - Xalqaro to'lovlar

### 3. Adaptive Learning (AI)
- ✅ Har bir savol bo'yicha statistika
- ✅ Zaif tomonlarni aniqlash
- ✅ Maxsus testlar yaratish (50% zaif, 30% o'xshash, 20% yangi)
- ✅ Progress tracking
- ✅ Tavsiyalar tizimi

### 4. Xavfsizlik
- ✅ SQL Injection himoyasi
- ✅ XSS himoyasi
- ✅ CSRF token
- ✅ Sanctum API authentication
- ✅ Rate limiting

---

## 🖥️ LOCAL KOMPYUTERDA ISHGA TUSHIRISH

### Talablar:
- PHP 8.1+
- Composer
- Node.js 16+
- MySQL/PostgreSQL/SQLite

### O'rnatish:

```bash
# 1. Repository clone
git clone https://github.com/BoburKhanUz/avtotest.git
cd avtotest

# 2. Dependencies o'rnatish
composer install
npm install

# 3. .env sozlash
cp .env.example .env
php artisan key:generate

# 4. Database sozlash (.env da)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=avtotest
DB_USERNAME=root
DB_PASSWORD=your_password

# 5. Database yaratish
php artisan migrate

# 6. Admin user yaratish
php artisan tinker
# Ichida:
User::create([
    'name' => 'Admin',
    'email' => 'admin@admin.com',
    'password' => bcrypt('password123'),
    'is_admin' => true
]);

# 7. Assets build
npm run build

# 8. Server ishga tushirish
php artisan serve
```

**Keyin brauzerda:**
- Frontend: `http://localhost:8000`
- Admin: `http://localhost:8000/login` (admin@admin.com / password123)
- API Docs: `http://localhost:8000/api/documentation`

---

## ☁️ PRODUCTION SERVERDA (VPS/HOSTING)

### Variant 1: cPanel/DirectAdmin

1. **GitHub import:**
   - Repository > Import from GitHub
   - Select: `BoburKhanUz/avtotest`

2. **Terminal orqali:**
   ```bash
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **.env sozlash:**
   - APP_ENV=production
   - APP_DEBUG=false
   - APP_URL=https://yourdomain.com

4. **Database yaratish:**
   - cPanel > MySQL Databases > Create
   - .env ga credentials yozish

---

### Variant 2: VPS (Ubuntu/Debian)

```bash
# 1. LEMP Stack o'rnatish
sudo apt update
sudo apt install nginx php8.1-fpm php8.1-mysql php8.1-cli \
    php8.1-curl php8.1-zip php8.1-gd php8.1-mbstring \
    php8.1-xml php8.1-bcmath mysql-server

# 2. Composer o'rnatish
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 3. Node.js o'rnatish
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# 4. Kod deploy
cd /var/www
sudo git clone https://github.com/BoburKhanUz/avtotest.git
cd avtotest
sudo chown -R www-data:www-data .
sudo chmod -R 755 storage bootstrap/cache

# 5. Dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 6. .env sozlash
cp .env.example .env
nano .env  # Edit qilish
php artisan key:generate
php artisan migrate --force

# 7. Nginx sozlash
sudo nano /etc/nginx/sites-available/avtotest
```

**Nginx konfiguratsiyasi:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/avtotest/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Nginx restart
sudo ln -s /etc/nginx/sites-available/avtotest /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

### Variant 3: Docker

```dockerfile
# Dockerfile
FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
```

```bash
# Ishga tushirish
docker build -t avtotest .
docker run -p 8000:8000 avtotest
```

---

## 🔧 TO'LOV TIZIMLARINI SOZLASH

### Payme

1. **Payme.uz ga ro'yxatdan o'tish**
2. **Merchant ID va Secret Key olish**
3. **.env ga qo'shish:**
   ```env
   PAYME_MERCHANT_ID=your_merchant_id
   PAYME_SECRET_KEY=your_secret_key
   ```
4. **Webhook URL sozlash (Payme dashboard):**
   ```
   https://yourdomain.com/api/payme/handle
   ```

### Click

1. **Click.uz ga ro'yxatdan o'tish**
2. **Merchant ID, Service ID va Secret Key olish**
3. **.env ga qo'shish:**
   ```env
   CLICK_MERCHANT_ID=your_merchant_id
   CLICK_SERVICE_ID=your_service_id
   CLICK_SECRET_KEY=your_secret_key
   ```
4. **Webhook URL'lar sozlash (Click dashboard):**
   ```
   Prepare: https://yourdomain.com/api/click/prepare
   Complete: https://yourdomain.com/api/click/complete
   ```

---

## 📱 MOBILE VA WEB SUPPORT

### ✅ Responsive Design
Loyiha **Bootstrap 5** va **AdminLTE 4.0** ishlatadi - to'liq responsive:
- ✅ Mobile phones (320px+)
- ✅ Tablets (768px+)
- ✅ Desktop (1024px+)
- ✅ Large screens (1920px+)

### Mobile App Uchun API

**Foydalanish:**
```javascript
// React Native / Flutter misol
const BASE_URL = 'https://yourdomain.com/api';

// 1. Login
const response = await fetch(`${BASE_URL}/login`, {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    email: 'user@example.com',
    password: 'password'
  })
});
const { token } = await response.json();

// 2. Testlar olish
const tests = await fetch(`${BASE_URL}/tests`, {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept-Language': 'uz'
  }
});

// 3. Test boshlash
const session = await fetch(`${BASE_URL}/test-sessions/start`, {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({ test_id: 1 })
});

// 4. Progress ko'rish
const progress = await fetch(`${BASE_URL}/learning/progress`, {
  headers: { 'Authorization': `Bearer ${token}` }
});
```

---

## 🔍 TESTING

```bash
# Unit tests
php artisan test

# Feature tests
php artisan test --filter=TestSessionTest

# API tests
php artisan test --filter=ApiTest
```

---

## 📊 MONITORING

### Logs:
```bash
tail -f storage/logs/laravel.log
```

### Performance:
```bash
php artisan route:cache
php artisan config:cache
php artisan view:cache
php artisan optimize
```

---

## 🔒 XAVFSIZLIK

### SSL Certificate (Let's Encrypt):
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

### Firewall:
```bash
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

---

## 📈 BACKUP

```bash
# Database backup
php artisan backup:run

# Avtomatik backup (cron)
0 2 * * * cd /var/www/avtotest && php artisan backup:run
```

---

## ❓ MUAMMOLAR VA YECHIMLAR

### 1. "Class not found" xatosi:
```bash
composer dump-autoload
```

### 2. Permission xatolar:
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 3. Assets yuklanmayapti:
```bash
npm run build
php artisan cache:clear
```

### 4. Database error:
```bash
php artisan migrate:fresh --seed
```

---

## 🎯 PRODUCTION CHECKLIST

- [ ] APP_ENV=production
- [ ] APP_DEBUG=false
- [ ] HTTPS enabled
- [ ] Database backups sozlangan
- [ ] Logs monitoring
- [ ] .env file xavfsiz joyda
- [ ] API rate limiting enabled
- [ ] Cache enabled
- [ ] Queue worker running
- [ ] Cron jobs sozlangan

---

**Support:** GitHub Issues yoki email: support@yourdomain.com
