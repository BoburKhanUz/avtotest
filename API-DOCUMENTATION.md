# 📡 Avto Test Platform - API Documentation

Base URL: `https://yourdomain.com/api`

## 🔐 Authentication

API Sanctum token-based authentication ishlatadi.

### Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "language": "uz"
}
```

**Response:**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "language": "uz"
  },
  "token": "1|abc123..."
}
```

---

### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "user": {...},
  "token": "2|xyz789..."
}
```

---

### Get User
```http
GET /api/user
Authorization: Bearer {token}
```

**Response:**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "language": "uz",
  "is_admin": false
}
```

---

## 📚 Tests (Testlar)

### Get All Tests
```http
GET /api/tests
Authorization: Bearer {token}
Accept-Language: uz
```

**Response:**
```json
[
  {
    "id": 1,
    "name": "Yo'l harakati qoidalari",
    "category_id": 1,
    "questions": [
      {
        "id": 1,
        "content": "Yo'l belgisi nima?",
        "options": ["A", "B", "C", "D"],
        "correct_option": "A"
      }
    ],
    "category": {
      "id": 1,
      "name": "Nazariy bilimlar"
    }
  }
]
```

---

### Get Single Test
```http
GET /api/tests/{id}
Authorization: Bearer {token}
Accept-Language: uz
```

---

## 🎯 Test Sessions

### Start Test
```http
POST /api/test-sessions/start
Authorization: Bearer {token}
Content-Type: application/json

{
  "test_id": 1
}
```

**Response:**
```json
{
  "id": 1,
  "user_id": 1,
  "test_id": 1,
  "started_at": "2025-11-23 10:00:00",
  "time_limit": 30,
  "status": "in_progress"
}
```

---

### Submit Test
```http
POST /api/test-sessions/{session_id}/submit
Authorization: Bearer {token}
Content-Type: application/json

{
  "answers": {
    "1": "A",
    "2": "B",
    "3": "C"
  }
}
```

**Response:**
```json
{
  "session": {
    "id": 1,
    "score": 25,
    "total_questions": 30,
    "ended_at": "2025-11-23 10:30:00",
    "status": "completed"
  },
  "score": 25
}
```

---

## 💳 Payments (To'lovlar)

### Create Payme Payment
```http
POST /api/payments/payme/create
Authorization: Bearer {token}
Content-Type: application/json

{
  "plan_id": 1,
  "return_url": "https://myapp.com/payment/success"
}
```

**Response:**
```json
{
  "payment_url": "https://checkout.paycom.uz/...",
  "amount": 50000,
  "plan": "Premium Plan"
}
```

---

### Create Click Payment
```http
POST /api/payments/click/create
Authorization: Bearer {token}
Content-Type: application/json

{
  "plan_id": 1,
  "return_url": "https://myapp.com/payment/success"
}
```

**Response:**
```json
{
  "payment_url": "https://my.click.uz/services/pay?...",
  "amount": 50000,
  "plan": "Premium Plan"
}
```

---

## 🧠 Adaptive Learning

### Get Progress
```http
GET /api/learning/progress
Authorization: Bearer {token}
```

**Response:**
```json
{
  "overview": {
    "total_attempts": 150,
    "total_mastered": 45,
    "total_questions": 60,
    "average_success_rate": 75.5,
    "mastery_percentage": 75
  },
  "category_progress": [
    {
      "id": 1,
      "category_id": 1,
      "total_questions": 30,
      "mastered_questions": 25,
      "category_progress": 83.33,
      "average_success_rate": 80.5,
      "category": {
        "id": 1,
        "name": "Yo'l qoidalari"
      }
    }
  ],
  "weak_areas": [
    {
      "question_id": 5,
      "success_rate": 40,
      "total_attempts": 5,
      "question": {...}
    }
  ],
  "strong_areas": [...]
}
```

---

### Get Recommendations
```http
GET /api/learning/recommendations?limit=10
Authorization: Bearer {token}
```

**Response:**
```json
[
  {
    "id": 1,
    "question_id": 5,
    "recommendation_type": "weak_area",
    "priority": 15,
    "reason": "Bu savolda sizning natijangiz 40%. Qo'shimcha mashq qilish tavsiya etiladi.",
    "question": {...}
  }
]
```

---

### Generate Personalized Test
```http
POST /api/learning/personalized-test
Authorization: Bearer {token}
Content-Type: application/json

{
  "question_count": 20
}
```

**Response:**
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

---

### Get Question Stats
```http
GET /api/learning/question-stats/{question_id}
Authorization: Bearer {token}
```

**Response:**
```json
{
  "attempted": true,
  "correct_count": 3,
  "incorrect_count": 2,
  "total_attempts": 5,
  "success_rate": 60,
  "mastered": false,
  "last_attempt_at": "2025-11-23 09:00:00"
}
```

---

## 📊 Reports

### User Stats
```http
GET /api/reports/user-stats
Authorization: Bearer {token}
```

---

### Test Results
```http
GET /api/reports/test-results
Authorization: Bearer {token}
```

---

## 🔔 Webhooks (Payme/Click)

### Payme Webhook
```http
POST /api/payme/handle
Authorization: Basic {base64(Paycom:secret_key)}
Content-Type: application/json

{
  "method": "CheckPerformTransaction",
  "params": {...},
  "id": 1
}
```

### Click Webhooks
```http
POST /api/click/prepare
POST /api/click/complete
```

---

## 📝 Error Responses

### 400 Bad Request
```json
{
  "error": "Validation error message"
}
```

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "error": "Unauthorized"
}
```

### 404 Not Found
```json
{
  "message": "Resource not found"
}
```

### 500 Server Error
```json
{
  "message": "Server error",
  "error": "..."
}
```

---

## 🌐 Language Support

API supports 3 languages via `Accept-Language` header:
- `uz` - O'zbekcha (default)
- `ru` - Русский
- `en` - English

Example:
```http
GET /api/tests
Accept-Language: ru
```

---

## 🔒 Rate Limiting

- **API calls:** 60 requests per minute
- **Authentication:** 5 login attempts per minute

Response headers:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
Retry-After: 60
```

---

## 📱 Mobile App Integration Examples

### React Native
```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'https://yourdomain.com/api',
  headers: {
    'Accept-Language': 'uz',
    'Content-Type': 'application/json'
  }
});

// Login
const login = async (email, password) => {
  const { data } = await api.post('/login', { email, password });
  api.defaults.headers.Authorization = `Bearer ${data.token}`;
  return data;
};

// Get tests
const getTests = () => api.get('/tests');

// Start test
const startTest = (testId) =>
  api.post('/test-sessions/start', { test_id: testId });

// Submit answers
const submitTest = (sessionId, answers) =>
  api.post(`/test-sessions/${sessionId}/submit`, { answers });
```

---

### Flutter (Dart)
```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class ApiService {
  final String baseUrl = 'https://yourdomain.com/api';
  String? token;

  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: {'Content-Type': 'application/json'},
      body: json.encode({'email': email, 'password': password}),
    );

    final data = json.decode(response.body);
    token = data['token'];
    return data;
  }

  Future<List> getTests() async {
    final response = await http.get(
      Uri.parse('$baseUrl/tests'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept-Language': 'uz',
      },
    );
    return json.decode(response.body);
  }
}
```

---

## 🧪 Testing API

### Postman Collection

Import qilish uchun:
```json
{
  "info": {
    "name": "Avto Test API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [...]
}
```

### cURL Examples

```bash
# Login
curl -X POST https://yourdomain.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Get tests
curl https://yourdomain.com/api/tests \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept-Language: uz"

# Start test
curl -X POST https://yourdomain.com/api/test-sessions/start \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"test_id":1}'
```

---

## 📚 Swagger/OpenAPI Documentation

Auto-generated API docs:
```
https://yourdomain.com/api/documentation
```

---

**Last Updated:** 2025-11-23
**Version:** 1.0.0
**Support:** api@yourdomain.com
