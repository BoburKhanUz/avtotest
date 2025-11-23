# 🚗 Avto Test - Professional Haydovchilik Imtihon Platformasi

Rasmiy avto imtihonga tayyorlov platformasi. Laravel 10 + Vue.js/React + AdminLTE 4.0

## ✨ Asosiy Funksiyalar

### 🧠 Adaptive Learning (Aqlli O'qitish Tizimi)
- **Zaif tomonlarni aniqlash**: Har bir savol bo'yicha statistika va tahlil
- **Maxsus testlar**: Foydalanuvchining natijalariga qarab maxsus testlar yaratish
  - 50% - Zaif tomonlar (kam ball olgan savollar)
  - 30% - O'xshash savollar (noto'g'ri javob bergan mavzular)
  - 20% - Yangi savollar
- **Progress Tracking**: Kategoriya va savol bo'yicha progress kuzatuvi
- **Tavsiyalar**: Noto'g'ri javoblar uchun avtomatik tavsiyalar

### 💳 To'lov Tizimlari
- **Payme** - to'liq integratsiya (merchant API)
- **Click** - to'liq integratsiya (prepare/complete)
- **Stripe** - mavjud (xalqaro to'lovlar uchun)

### 📊 Test Tizimi
- Ko'p tillilik (O'zbek, Rus, Ingliz)
- Vaqt cheklovi
- Real-time timer
- Avtomatik natija hisoblash
- Tarixiy natijalar

### 👨‍💼 Admin Panel
- Savol boshqaruvi
- Test yaratish va tahrirlash
- Kategoriya boshqaruvi
- Foydalanuvchi statistikasi
- Hisobotlar (Excel export)

## 🚀 O'rnatish

### 1. Loyihani yuklab olish

```bash
git clone https://github.com/BoburKhanUz/avtotest.git
cd avtotest
```

### 2. Dependencies o'rnatish

```bash
composer install
npm install
```

### 3. Environment sozlash

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Ma'lumotlar bazasini sozlash

`.env` faylida:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=avtotest
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. To'lov tizimlarini sozlash

**Payme:**
```env
PAYME_MERCHANT_ID=your_merchant_id
PAYME_SECRET_KEY=your_secret_key
```

**Click:**
```env
CLICK_MERCHANT_ID=your_merchant_id
CLICK_SERVICE_ID=your_service_id
CLICK_SECRET_KEY=your_secret_key
```

### 6. Migration va Seed

```bash
php artisan migrate
php artisan db:seed
```

### 7. Loyihani ishga tushirish

```bash
php artisan serve
npm run dev
```

## 📡 API Endpoints

### Authentication
```
POST /api/register - Ro'yxatdan o'tish
POST /api/login - Kirish
GET  /api/user - Foydalanuvchi ma'lumotlari
```

### Testlar
```
GET  /api/tests - Barcha testlar
GET  /api/tests/{id} - Bitta test
POST /api/test-sessions/start - Testni boshlash
POST /api/test-sessions/{id}/submit - Testni topshirish
```

### Adaptive Learning
```
GET  /api/learning/progress - Progress ko'rish
GET  /api/learning/recommendations - Tavsiyalar olish
POST /api/learning/personalized-test - Maxsus test yaratish
GET  /api/learning/question-stats/{id} - Savol statistikasi
```

### To'lovlar
```
POST /api/payments/payme/create - Payme to'lov yaratish
POST /api/payments/click/create - Click to'lov yaratish

# Webhooks (Payme va Click uchun)
POST /api/payme/handle
POST /api/click/prepare
POST /api/click/complete
```

## 🎯 Adaptive Learning Qanday Ishlaydi?

### 1. Javoblarni Qayd Qilish
Har safar test topshirilganda, har bir javob tahlil qilinadi:
- To'g'ri javoblar soni
- Noto'g'ri javoblar soni
- Umumiy urinishlar
- Muvaffaqiyat foizi

### 2. Mastery (O'zlashtirish)
Savol 80% va undan yuqori natija ko'rsatganda "o'zlashtirilgan" deb belgilanadi (kamida 3 marta urinish).

### 3. Zaif Tomonlarni Aniqlash
50% dan past natija ko'rsatgan savollar "zaif tomonlar" ro'yxatiga qo'shiladi.

### 4. Tavsiyalar Generatsiyasi
Noto'g'ri javob berilganda:
- Bir xil testdan o'xshash savollar tavsiya qilinadi
- Zaif tomonlar uchun qo'shimcha mashqlar taklif qilinadi

### 5. Maxsus Test Yaratish
```php
POST /api/learning/personalized-test
{
  "question_count": 20
}
```

Javob:
```json
{
  "questions": [...],
  "total_count": 20,
  "composition": {
    "weak_areas": "50%",
    "similar_questions": "30%",
    "new_questions": "20%"
  }
}
```

## 🗄️ Database Tuzilmasi

### Adaptive Learning Jadvallari

#### user_question_analytics
Har bir foydalanuvchi uchun savol bo'yicha statistika:
- correct_count - To'g'ri javoblar
- incorrect_count - Noto'g'ri javoblar
- total_attempts - Jami urinishlar
- success_rate - Muvaffaqiyat foizi
- mastered - O'zlashtirilganligi

#### user_category_analytics
Kategoriya bo'yicha progress:
- total_questions - Jami savollar
- mastered_questions - O'zlashtirilgan savollar
- category_progress - Progress foizi
- average_success_rate - O'rtacha muvaffaqiyat

#### recommended_questions
Tavsiya etilgan savollar:
- recommendation_type - Tavsiya turi (weak_area, similar_question, review)
- priority - Muhimlik darajasi
- reason - Tavsiya sababi

### To'lov Jadvallari

#### payme_transactions
```
- payme_transaction_id
- amount
- state (1: created, 2: completed, -1: cancelled)
- perform_time
- cancel_time
```

#### click_transactions
```
- click_trans_id
- merchant_trans_id
- amount
- status (0: prepared, 1: completed, -1: failed)
```

## 🔐 Xavfsizlik

- SQL Injection himoyasi
- XSS himoyasi
- CSRF token
- Sanctum API authentication
- Password hashing (bcrypt)
- Rate limiting

## 🛠️ Texnologiyalar

- **Backend**: Laravel 10, PHP 8.1+
- **Frontend**: AdminLTE 4.0, Bootstrap 5, jQuery
- **Database**: MySQL/PostgreSQL
- **Payment**: Payme, Click, Stripe
- **Excel**: PhpSpreadsheet 5.2

## 📝 Keyingi Rejalar

- [ ] Mobile app (Flutter/React Native)
- [ ] Video darslar integratsiyasi
- [ ] Real-time proctoring (kamera nazorati)
- [ ] AI chatbot yordam
- [ ] Gamification (badges, leaderboard)
- [ ] PWA support
- [ ] Redis caching
- [ ] Queue system (RabbitMQ)

## 👨‍💻 Ishlab Chiqish

```bash
# Development
npm run dev
php artisan serve

# Production build
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📄 Litsenziya

MIT License

## 🤝 Hissa Qo'shish

Pull requestlar qabul qilinadi! Katta o'zgarishlar uchun avval issue oching.

## 📧 Bog'lanish

GitHub: [@BoburKhanUz](https://github.com/BoburKhanUz)

---

Made with ❤️ for Uzbekistan
