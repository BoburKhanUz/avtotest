# 📱💻 Avto Test - Mobile & Web Integration Guide

## ✅ JAVOB: HA, MOBILE VA WEB UCHUN TO'LIQ ISHLAYDI!

Platform **3 xil** foydalanish usulini qo'llab-quvvatlaydi:

1. **Web (Responsive)** - Har qanday brauzerda
2. **Mobile App (Native)** - React Native / Flutter
3. **PWA (Progressive Web App)** - Offline support

---

## 🌐 WEB (RESPONSIVE DESIGN)

### ✅ Qo'llab-quvvatlanadigan Brauzerlar:
- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Opera 76+
- Mobile browsers (Safari iOS, Chrome Android)

### ✅ Screen Sizlar:
```css
/* Mobile First Design */
320px+  : Mobile phones
768px+  : Tablets
1024px+ : Laptops
1920px+ : Desktop
```

### Responsive Features:
```html
<!-- Bootstrap 5 Grid -->
<div class="container">
  <div class="row">
    <div class="col-12 col-md-6 col-lg-4">
      <!-- Mobile: 100%, Tablet: 50%, Desktop: 33% -->
    </div>
  </div>
</div>
```

### Mobile Navigation:
- ✅ Hamburger menu
- ✅ Touch-friendly buttons (48x48px minimum)
- ✅ Swipe gestures support
- ✅ Bottom navigation (mobile)

---

## 📱 MOBILE APP (REACT NATIVE)

### 1. Setup

```bash
npx react-native init AvtoTestApp
cd AvtoTestApp
npm install axios @react-navigation/native react-native-async-storage
```

### 2. API Service

```javascript
// src/services/api.js
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_BASE_URL = 'https://yourdomain.com/api';

class ApiService {
  constructor() {
    this.api = axios.create({
      baseURL: API_BASE_URL,
      headers: {
        'Content-Type': 'application/json',
        'Accept-Language': 'uz',
      },
    });

    // Interceptor - har bir request ga token qo'shish
    this.api.interceptors.request.use(async (config) => {
      const token = await AsyncStorage.getItem('token');
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
      return config;
    });
  }

  // Authentication
  async login(email, password) {
    const { data } = await this.api.post('/login', { email, password });
    await AsyncStorage.setItem('token', data.token);
    await AsyncStorage.setItem('user', JSON.stringify(data.user));
    return data;
  }

  async register(name, email, password) {
    const { data } = await this.api.post('/register', {
      name,
      email,
      password,
      password_confirmation: password,
    });
    await AsyncStorage.setItem('token', data.token);
    return data;
  }

  async logout() {
    await AsyncStorage.removeItem('token');
    await AsyncStorage.removeItem('user');
  }

  // Tests
  async getTests() {
    const { data } = await this.api.get('/tests');
    return data;
  }

  async getTest(id) {
    const { data } = await this.api.get(`/tests/${id}`);
    return data;
  }

  // Test Sessions
  async startTest(testId) {
    const { data } = await this.api.post('/test-sessions/start', {
      test_id: testId,
    });
    return data;
  }

  async submitTest(sessionId, answers) {
    const { data } = await this.api.post(
      `/test-sessions/${sessionId}/submit`,
      { answers }
    );
    return data;
  }

  // Adaptive Learning
  async getProgress() {
    const { data } = await this.api.get('/learning/progress');
    return data;
  }

  async getRecommendations(limit = 10) {
    const { data } = await this.api.get(`/learning/recommendations?limit=${limit}`);
    return data;
  }

  async generatePersonalizedTest(questionCount = 20) {
    const { data } = await this.api.post('/learning/personalized-test', {
      question_count: questionCount,
    });
    return data;
  }

  // Payments
  async createPaymePayment(planId) {
    const { data } = await this.api.post('/payments/payme/create', {
      plan_id: planId,
    });
    return data;
  }
}

export default new ApiService();
```

### 3. Login Screen

```javascript
// src/screens/LoginScreen.js
import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Alert } from 'react-native';
import api from '../services/api';

export default function LoginScreen({ navigation }) {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);

  const handleLogin = async () => {
    if (!email || !password) {
      Alert.alert('Xato', 'Email va parolni kiriting');
      return;
    }

    setLoading(true);
    try {
      await api.login(email, password);
      navigation.replace('Home');
    } catch (error) {
      Alert.alert('Xato', 'Email yoki parol noto\'g\'ri');
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Avto Test</Text>

      <TextInput
        style={styles.input}
        placeholder="Email"
        value={email}
        onChangeText={setEmail}
        keyboardType="email-address"
        autoCapitalize="none"
      />

      <TextInput
        style={styles.input}
        placeholder="Parol"
        value={password}
        onChangeText={setPassword}
        secureTextEntry
      />

      <TouchableOpacity
        style={styles.button}
        onPress={handleLogin}
        disabled={loading}
      >
        <Text style={styles.buttonText}>
          {loading ? 'Yuklanmoqda...' : 'Kirish'}
        </Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    padding: 20,
    backgroundColor: '#f5f5f5',
  },
  title: {
    fontSize: 32,
    fontWeight: 'bold',
    marginBottom: 30,
    textAlign: 'center',
    color: '#667eea',
  },
  input: {
    backgroundColor: 'white',
    padding: 15,
    borderRadius: 10,
    marginBottom: 15,
    fontSize: 16,
  },
  button: {
    backgroundColor: '#667eea',
    padding: 15,
    borderRadius: 10,
    marginTop: 10,
  },
  buttonText: {
    color: 'white',
    textAlign: 'center',
    fontSize: 16,
    fontWeight: 'bold',
  },
});
```

### 4. Test Screen

```javascript
// src/screens/TestScreen.js
import React, { useState, useEffect } from 'react';
import { View, Text, ScrollView, TouchableOpacity, StyleSheet } from 'react';
import api from '../services/api';

export default function TestScreen({ route, navigation }) {
  const { testId } = route.params;
  const [session, setSession] = useState(null);
  const [test, setTest] = useState(null);
  const [answers, setAnswers] = useState({});
  const [timeLeft, setTimeLeft] = useState(null);

  useEffect(() => {
    loadTest();
  }, []);

  useEffect(() => {
    if (timeLeft === null || timeLeft <= 0) return;

    const timer = setInterval(() => {
      setTimeLeft((prev) => prev - 1);
    }, 1000);

    return () => clearInterval(timer);
  }, [timeLeft]);

  const loadTest = async () => {
    try {
      const [testData, sessionData] = await Promise.all([
        api.getTest(testId),
        api.startTest(testId),
      ]);
      setTest(testData);
      setSession(sessionData);
      setTimeLeft(sessionData.time_limit * 60); // minutdan sekundga
    } catch (error) {
      Alert.alert('Xato', 'Testni yuklashda xatolik');
    }
  };

  const handleAnswer = (questionId, answer) => {
    setAnswers({ ...answers, [questionId]: answer });
  };

  const handleSubmit = async () => {
    try {
      const result = await api.submitTest(session.id, answers);
      navigation.replace('Result', { result });
    } catch (error) {
      Alert.alert('Xato', error.response?.data?.error || 'Xatolik yuz berdi');
    }
  };

  if (!test) {
    return <View style={styles.container}><Text>Yuklanmoqda...</Text></View>;
  }

  const formatTime = (seconds) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs < 10 ? '0' : ''}${secs}`;
  };

  return (
    <View style={styles.container}>
      {/* Timer */}
      <View style={styles.timer}>
        <Text style={styles.timerText}>
          ⏱ {formatTime(timeLeft)}
        </Text>
      </View>

      {/* Questions */}
      <ScrollView style={styles.questionsContainer}>
        {test.questions.map((question, index) => (
          <View key={question.id} style={styles.questionCard}>
            <Text style={styles.questionNumber}>Savol {index + 1}</Text>
            <Text style={styles.questionText}>{question.content}</Text>

            {question.options.map((option, optIndex) => (
              <TouchableOpacity
                key={optIndex}
                style={[
                  styles.optionButton,
                  answers[question.id] === option && styles.optionButtonSelected,
                ]}
                onPress={() => handleAnswer(question.id, option)}
              >
                <Text style={styles.optionText}>{option}</Text>
              </TouchableOpacity>
            ))}
          </View>
        ))}
      </ScrollView>

      {/* Submit Button */}
      <TouchableOpacity style={styles.submitButton} onPress={handleSubmit}>
        <Text style={styles.submitButtonText}>Testni Yakunlash</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f5f5',
  },
  timer: {
    backgroundColor: '#667eea',
    padding: 15,
    alignItems: 'center',
  },
  timerText: {
    color: 'white',
    fontSize: 24,
    fontWeight: 'bold',
  },
  questionsContainer: {
    flex: 1,
    padding: 15,
  },
  questionCard: {
    backgroundColor: 'white',
    padding: 20,
    borderRadius: 10,
    marginBottom: 15,
  },
  questionNumber: {
    fontSize: 14,
    color: '#666',
    marginBottom: 10,
  },
  questionText: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 15,
  },
  optionButton: {
    backgroundColor: '#f5f5f5',
    padding: 15,
    borderRadius: 8,
    marginBottom: 10,
    borderWidth: 2,
    borderColor: 'transparent',
  },
  optionButtonSelected: {
    backgroundColor: '#e6eeff',
    borderColor: '#667eea',
  },
  optionText: {
    fontSize: 16,
  },
  submitButton: {
    backgroundColor: '#667eea',
    padding: 18,
    margin: 15,
    borderRadius: 10,
  },
  submitButtonText: {
    color: 'white',
    textAlign: 'center',
    fontSize: 18,
    fontWeight: 'bold',
  },
});
```

---

## 🎯 FLUTTER (DART)

### 1. Setup

```yaml
# pubspec.yaml
dependencies:
  flutter:
    sdk: flutter
  http: ^1.1.0
  shared_preferences: ^2.2.2
  provider: ^6.1.1
```

### 2. API Service

```dart
// lib/services/api_service.dart
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  final String baseUrl = 'https://yourdomain.com/api';
  String? token;

  ApiService() {
    _loadToken();
  }

  Future<void> _loadToken() async {
    final prefs = await SharedPreferences.getInstance();
    token = prefs.getString('token');
  }

  Future<Map<String, String>> _getHeaders() async {
    return {
      'Content-Type': 'application/json',
      'Accept-Language': 'uz',
      if (token != null) 'Authorization': 'Bearer $token',
    };
  }

  Future<Map<String, dynamic>> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('$baseUrl/login'),
      headers: await _getHeaders(),
      body: json.encode({'email': email, 'password': password}),
    );

    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      token = data['token'];
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('token', token!);
      return data;
    } else {
      throw Exception('Login failed');
    }
  }

  Future<List<dynamic>> getTests() async {
    final response = await http.get(
      Uri.parse('$baseUrl/tests'),
      headers: await _getHeaders(),
    );

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      throw Exception('Failed to load tests');
    }
  }

  Future<Map<String, dynamic>> startTest(int testId) async {
    final response = await http.post(
      Uri.parse('$baseUrl/test-sessions/start'),
      headers: await _getHeaders(),
      body: json.encode({'test_id': testId}),
    );

    if (response.statusCode == 201) {
      return json.decode(response.body);
    } else {
      throw Exception('Failed to start test');
    }
  }

  Future<Map<String, dynamic>> submitTest(
    int sessionId,
    Map<int, String> answers,
  ) async {
    final response = await http.post(
      Uri.parse('$baseUrl/test-sessions/$sessionId/submit'),
      headers: await _getHeaders(),
      body: json.encode({'answers': answers}),
    );

    if (response.statusCode == 200) {
      return json.decode(response.body);
    } else {
      throw Exception('Failed to submit test');
    }
  }
}
```

---

## 🌐 PWA (PROGRESSIVE WEB APP)

### 1. Manifest.json

```json
{
  "name": "Avto Test Platform",
  "short_name": "Avto Test",
  "description": "Professional haydovchilik imtihon platformasi",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#667eea",
  "icons": [
    {
      "src": "/images/icon-192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/images/icon-512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ]
}
```

### 2. Service Worker

```javascript
// public/sw.js
const CACHE_NAME = 'avto-test-v1';
const urlsToCache = [
  '/',
  '/css/app.css',
  '/js/app.js',
  '/images/logo.png',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      return response || fetch(event.request);
    })
  );
});
```

### 3. Register Service Worker

```javascript
// resources/js/app.js
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker
      .register('/sw.js')
      .then((reg) => console.log('SW registered'))
      .catch((err) => console.log('SW error:', err));
  });
}
```

---

## 📊 PERFORMANCE OPTIMIZATION

### Mobile Optimization:
```javascript
// Lazy loading images
<img loading="lazy" src="image.jpg" alt="Image">

// Code splitting
const Component = lazy(() => import('./Component'));

// Compression
npm install compression
app.use(compression());
```

### API Caching:
```javascript
// React Native - AsyncStorage
import AsyncStorage from '@react-native-async-storage/async-storage';

const getCachedTests = async () => {
  const cached = await AsyncStorage.getItem('tests');
  if (cached) {
    const { data, timestamp } = JSON.parse(cached);
    if (Date.now() - timestamp < 3600000) { // 1 hour
      return data;
    }
  }

  const tests = await api.getTests();
  await AsyncStorage.setItem('tests', JSON.stringify({
    data: tests,
    timestamp: Date.now(),
  }));
  return tests;
};
```

---

## 🔔 PUSH NOTIFICATIONS

### Firebase Cloud Messaging

```javascript
// React Native
import messaging from '@react-native-firebase/messaging';

// Request permission
const requestPermission = async () => {
  const authStatus = await messaging().requestPermission();
  return authStatus === messaging.AuthorizationStatus.AUTHORIZED;
};

// Get FCM token
const getFCMToken = async () => {
  const token = await messaging().getToken();
  // Send to server
  await api.updateFCMToken(token);
};

// Handle notifications
messaging().onMessage(async (remoteMessage) => {
  Alert.alert('Yangi xabar', remoteMessage.notification.body);
});
```

---

## ✅ TESTING

### Jest (React Native)

```javascript
// __tests__/api.test.js
import api from '../src/services/api';

describe('API Service', () => {
  it('should login successfully', async () => {
    const result = await api.login('test@test.com', 'password');
    expect(result.token).toBeDefined();
  });

  it('should get tests', async () => {
    const tests = await api.getTests();
    expect(Array.isArray(tests)).toBe(true);
  });
});
```

---

## 📱 DEPLOYMENT

### React Native (Android)

```bash
# Build APK
cd android
./gradlew assembleRelease

# APK joylashuvi:
# android/app/build/outputs/apk/release/app-release.apk
```

### React Native (iOS)

```bash
# Xcode bilan
open ios/AvtoTestApp.xcworkspace
# Product > Archive
```

### Flutter

```bash
# Android
flutter build apk --release

# iOS
flutter build ios --release
```

---

**XULOSA:**

✅ **WEB:** To'liq responsive, barcha qurilmalarda ishlaydi
✅ **MOBILE:** React Native / Flutter uchun tayyor API
✅ **PWA:** Offline support bilan
✅ **DOCS:** To'liq API documentation

**Barcha kod GitHub'da:**
```
https://github.com/BoburKhanUz/avtotest
```
