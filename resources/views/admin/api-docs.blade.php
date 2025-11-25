@extends('layouts.admin')

@section('title', 'API Dokumentatsiya')

@push('styles')
<style>
    .api-sidebar {
        position: sticky;
        top: 70px;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
    }

    .api-endpoint {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e0e0e0;
        transition: all 0.3s;
    }

    .api-endpoint:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .method-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.85rem;
        font-family: 'Courier New', monospace;
    }

    .method-GET { background: #10b981; color: white; }
    .method-POST { background: #3b82f6; color: white; }
    .method-PUT { background: #f59e0b; color: white; }
    .method-DELETE { background: #ef4444; color: white; }

    .endpoint-url {
        font-family: 'Courier New', monospace;
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 4px;
        margin: 10px 0;
        font-size: 0.9rem;
        word-break: break-all;
    }

    .code-block {
        background: #1e293b;
        color: #e2e8f0;
        padding: 15px;
        border-radius: 6px;
        overflow-x: auto;
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        line-height: 1.6;
    }

    .code-block .key { color: #7dd3fc; }
    .code-block .string { color: #86efac; }
    .code-block .number { color: #fbbf24; }
    .code-block .boolean { color: #f472b6; }

    .nav-pills .nav-link {
        border-radius: 6px;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }

    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .try-it-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 8px 20px;
        border-radius: 6px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .try-it-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .test-interface {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }

    .response-viewer {
        background: #1e293b;
        color: #e2e8f0;
        padding: 15px;
        border-radius: 6px;
        min-height: 200px;
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
    }

    .auth-token-input {
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="row">
    <!-- Sidebar -->
    <div class="col-md-3">
        <div class="api-sidebar">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3"><i class="bi bi-book"></i> Mundarija</h5>
                    <nav class="nav nav-pills flex-column">
                        <a class="nav-link active" href="#overview">
                            <i class="bi bi-info-circle"></i> Umumiy Ma'lumot
                        </a>
                        <a class="nav-link" href="#authentication">
                            <i class="bi bi-shield-lock"></i> Autentifikatsiya
                        </a>
                        <a class="nav-link" href="#auth-endpoints">
                            <i class="bi bi-person-check"></i> Auth API
                        </a>
                        <a class="nav-link" href="#test-endpoints">
                            <i class="bi bi-file-earmark-text"></i> Test API
                        </a>
                        <a class="nav-link" href="#test-session-endpoints">
                            <i class="bi bi-clock-history"></i> Test Session API
                        </a>
                        <a class="nav-link" href="#subscription-endpoints">
                            <i class="bi bi-star"></i> Subscription API
                        </a>
                        <a class="nav-link" href="#payment-endpoints">
                            <i class="bi bi-credit-card"></i> Payment API
                        </a>
                        <a class="nav-link" href="#adaptive-learning">
                            <i class="bi bi-brain"></i> Adaptive Learning
                        </a>
                        <a class="nav-link" href="#errors">
                            <i class="bi bi-exclamation-triangle"></i> Xatolar
                        </a>
                        <a class="nav-link" href="#testing">
                            <i class="bi bi-play-circle"></i> API Testing
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9">
        <!-- Overview -->
        <section id="overview">
            <div class="section-header">
                <h3><i class="bi bi-info-circle"></i> API Umumiy Ma'lumot</h3>
            </div>
            <div class="api-endpoint">
                <h5>Base URL</h5>
                <div class="endpoint-url">{{ url('/api') }}</div>

                <h5 class="mt-3">Response Format</h5>
                <p>Barcha API javoblari JSON formatda qaytariladi:</p>
                <div class="code-block">
{
    <span class="key">"success"</span>: <span class="boolean">true</span>,
    <span class="key">"message"</span>: <span class="string">"Success"</span>,
    <span class="key">"data"</span>: { ... },
    <span class="key">"timestamp"</span>: <span class="string">"2024-01-01T12:00:00.000000Z"</span>
}
                </div>

                <h5 class="mt-3">Headers</h5>
                <div class="code-block">
Content-Type: application/json
Accept: application/json
Accept-Language: uz  <span class="key">// uz, ru, en</span>
Authorization: Bearer {token}  <span class="key">// Himoyalangan endpointlar uchun</span>
                </div>
            </div>
        </section>

        <!-- Authentication -->
        <section id="authentication">
            <div class="section-header">
                <h3><i class="bi bi-shield-lock"></i> Autentifikatsiya</h3>
            </div>
            <div class="api-endpoint">
                <p>API Laravel Sanctum orqali himoyalangan. Token olish uchun:</p>
                <ol>
                    <li>POST /api/login orqali kirish</li>
                    <li>Response'dan token olish</li>
                    <li>Keyingi so'rovlarda Authorization header qo'shish</li>
                </ol>

                <h5 class="mt-3">Token Example</h5>
                <div class="code-block">
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz1234567890
                </div>
            </div>
        </section>

        <!-- Auth Endpoints -->
        <section id="auth-endpoints">
            <div class="section-header">
                <h3><i class="bi bi-person-check"></i> Authentication API</h3>
            </div>

            <!-- Register -->
            <div class="api-endpoint">
                <h5>
                    <span class="method-badge method-POST">POST</span>
                    Ro'yxatdan o'tish
                </h5>
                <div class="endpoint-url">/api/register</div>

                <h6 class="mt-3">Request Body:</h6>
                <div class="code-block">
{
    <span class="key">"name"</span>: <span class="string">"John Doe"</span>,
    <span class="key">"email"</span>: <span class="string">"john@example.com"</span>,
    <span class="key">"password"</span>: <span class="string">"password123"</span>,
    <span class="key">"password_confirmation"</span>: <span class="string">"password123"</span>,
    <span class="key">"phone"</span>: <span class="string">"+998901234567"</span>,
    <span class="key">"language"</span>: <span class="string">"uz"</span>
}
                </div>

                <h6 class="mt-3">Response (201):</h6>
                <div class="code-block">
{
    <span class="key">"success"</span>: <span class="boolean">true</span>,
    <span class="key">"message"</span>: <span class="string">"Ro'yxatdan muvaffaqiyatli o'tdingiz"</span>,
    <span class="key">"data"</span>: {
        <span class="key">"user"</span>: {
            <span class="key">"id"</span>: <span class="number">1</span>,
            <span class="key">"name"</span>: <span class="string">"John Doe"</span>,
            <span class="key">"email"</span>: <span class="string">"john@example.com"</span>
        },
        <span class="key">"token"</span>: <span class="string">"1|abcdefg..."</span>
    }
}
                </div>
            </div>

            <!-- Login -->
            <div class="api-endpoint">
                <h5>
                    <span class="method-badge method-POST">POST</span>
                    Kirish
                </h5>
                <div class="endpoint-url">/api/login</div>

                <h6 class="mt-3">Request Body:</h6>
                <div class="code-block">
{
    <span class="key">"email"</span>: <span class="string">"john@example.com"</span>,
    <span class="key">"password"</span>: <span class="string">"password123"</span>
}
                </div>

                <h6 class="mt-3">Response (200):</h6>
                <div class="code-block">
{
    <span class="key">"success"</span>: <span class="boolean">true</span>,
    <span class="key">"message"</span>: <span class="string">"Kirish muvaffaqiyatli"</span>,
    <span class="key">"data"</span>: {
        <span class="key">"user"</span>: { ... },
        <span class="key">"token"</span>: <span class="string">"1|abcdefg..."</span>
    }
}
                </div>
            </div>

            <!-- Get User -->
            <div class="api-endpoint">
                <h5>
                    <span class="method-badge method-GET">GET</span>
                    Foydalanuvchi ma'lumotlari
                </h5>
                <div class="endpoint-url">/api/user</div>
                <p class="text-muted"><i class="bi bi-lock"></i> Requires Authentication</p>

                <h6 class="mt-3">Response (200):</h6>
                <div class="code-block">
{
    <span class="key">"success"</span>: <span class="boolean">true</span>,
    <span class="key">"data"</span>: {
        <span class="key">"id"</span>: <span class="number">1</span>,
        <span class="key">"name"</span>: <span class="string">"John Doe"</span>,
        <span class="key">"email"</span>: <span class="string">"john@example.com"</span>,
        <span class="key">"phone"</span>: <span class="string">"+998901234567"</span>,
        <span class="key">"language"</span>: <span class="string">"uz"</span>,
        <span class="key">"is_admin"</span>: <span class="boolean">false</span>
    }
}
                </div>
            </div>
        </section>

        <!-- Test Endpoints -->
        <section id="test-endpoints">
            <div class="section-header">
                <h3><i class="bi bi-file-earmark-text"></i> Test API</h3>
            </div>

            <div class="api-endpoint">
                <h5>
                    <span class="method-badge method-GET">GET</span>
                    Barcha testlar
                </h5>
                <div class="endpoint-url">/api/tests</div>
                <p class="text-muted"><i class="bi bi-lock"></i> Requires Authentication</p>

                <h6 class="mt-3">Query Parameters:</h6>
                <ul>
                    <li><code>category_id</code> - Kategoriya bo'yicha filter</li>
                    <li><code>language</code> - Til (uz, ru, en)</li>
                </ul>

                <h6 class="mt-3">Response (200):</h6>
                <div class="code-block">
{
    <span class="key">"success"</span>: <span class="boolean">true</span>,
    <span class="key">"data"</span>: [
        {
            <span class="key">"id"</span>: <span class="number">1</span>,
            <span class="key">"name"</span>: <span class="string">"Yo'l qoidalari"</span>,
            <span class="key">"description"</span>: <span class="string">"Yo'l harakati qoidalari"</span>,
            <span class="key">"time_limit"</span>: <span class="number">30</span>,
            <span class="key">"question_count"</span>: <span class="number">20</span>,
            <span class="key">"category"</span>: {
                <span class="key">"id"</span>: <span class="number">1</span>,
                <span class="key">"name"</span>: <span class="string">"Yo'l qoidalari"</span>
            }
        }
    ]
}
                </div>
            </div>

            <div class="api-endpoint">
                <h5>
                    <span class="method-badge method-GET">GET</span>
                    Test ma'lumotlari
                </h5>
                <div class="endpoint-url">/api/tests/{id}</div>
                <p class="text-muted"><i class="bi bi-lock"></i> Requires Authentication</p>

                <h6 class="mt-3">Response (200):</h6>
                <div class="code-block">
{
    <span class="key">"success"</span>: <span class="boolean">true</span>,
    <span class="key">"data"</span>: {
        <span class="key">"id"</span>: <span class="number">1</span>,
        <span class="key">"name"</span>: <span class="string">"Yo'l qoidalari"</span>,
        <span class="key">"questions"</span>: [
            {
                <span class="key">"id"</span>: <span class="number">1</span>,
                <span class="key">"content"</span>: <span class="string">"Yo'lda tezlik chegarasi?"</span>,
                <span class="key">"options"</span>: [<span class="string">"60 km/h"</span>, <span class="string">"80 km/h"</span>, <span class="string">"100 km/h"</span>],
                <span class="key">"correct_option"</span>: <span class="string">"A"</span>
            }
        ]
    }
}
                </div>
            </div>
        </section>

        <!-- Test Session Endpoints -->
        <section id="test-session-endpoints">
            <div class="section-header">
                <h3><i class="bi bi-clock-history"></i> Test Session API</h3>
            </div>

            <div class="api-endpoint">
                <h5>
                    <span class="method-badge method-POST">POST</span>
                    Testni boshlash
                </h5>
                <div class="endpoint-url">/api/test-sessions/start</div>
                <p class="text-muted"><i class="bi bi-lock"></i> Requires Authentication</p>

                <h6 class="mt-3">Request Body:</h6>
                <div class="code-block">
{
    <span class="key">"test_id"</span>: <span class="number">1</span>
}
                </div>

                <h6 class="mt-3">Response (201):</h6>
                <div class="code-block">
{
    <span class="key">"success"</span>: <span class="boolean">true</span>,
    <span class="key">"message"</span>: <span class="string">"Test muvaffaqiyatli boshlandi"</span>,
    <span class="key">"data"</span>: {
        <span class="key">"session"</span>: {
            <span class="key">"id"</span>: <span class="number">123</span>,
            <span class="key">"started_at"</span>: <span class="string">"2024-01-01T12:00:00Z"</span>,
            <span class="key">"time_limit"</span>: <span class="number">30</span>,
            <span class="key">"remaining_time"</span>: <span class="number">1800</span>,
            <span class="key">"status"</span>: <span class="string">"in_progress"</span>
        },
        <span class="key">"test"</span>: {
            <span class="key">"id"</span>: <span class="number">1</span>,
            <span class="key">"name"</span>: <span class="string">"Yo'l qoidalari"</span>,
            <span class="key">"total_questions"</span>: <span class="number">20</span>
        }
    }
}
                </div>
            </div>

            <div class="api-endpoint">
                <h5>
                    <span class="method-badge method-GET">GET</span>
                    Session status (Real-time timer)
                </h5>
                <div class="endpoint-url">/api/test-sessions/{session}/status</div>
                <p class="text-muted"><i class="bi bi-lock"></i> Requires Authentication</p>
                <p class="text-info"><i class="bi bi-lightning"></i> Real-time: Har 1 sekundda so'rash mumkin</p>

                <h6 class="mt-3">Response (200):</h6>
                <div class="code-block">
{
    <span class="key">"success"</span>: <span class="boolean">true</span>,
    <span class="key">"data"</span>: {
        <span class="key">"session"</span>: {
            <span class="key">"id"</span>: <span class="number">123</span>,
            <span class="key">"status"</span>: <span class="string">"in_progress"</span>,
            <span class="key">"remaining_time"</span>: <span class="number">1200</span>,
            <span class="key">"is_expired"</span>: <span class="boolean">false</span>
        }
    }
}
                </div>
            </div>

            <div class="api-endpoint">
                <h5>
                    <span class="method-badge method-POST">POST</span>
                    Testni yakunlash
                </h5>
                <div class="endpoint-url">/api/test-sessions/{session}/submit</div>
                <p class="text-muted"><i class="bi bi-lock"></i> Requires Authentication</p>

                <h6 class="mt-3">Request Body:</h6>
                <div class="code-block">
{
    <span class="key">"answers"</span>: {
        <span class="key">"1"</span>: <span class="string">"A"</span>,
        <span class="key">"2"</span>: <span class="string">"B"</span>,
        <span class="key">"3"</span>: <span class="string">"C"</span>
    }
}
                </div>

                <h6 class="mt-3">Response (200):</h6>
                <div class="code-block">
{
    <span class="key">"success"</span>: <span class="boolean">true</span>,
    <span class="key">"message"</span>: <span class="string">"Test muvaffaqiyatli yakunlandi"</span>,
    <span class="key">"data"</span>: {
        <span class="key">"result"</span>: {
            <span class="key">"score"</span>: <span class="number">18</span>,
            <span class="key">"total_questions"</span>: <span class="number">20</span>,
            <span class="key">"percentage"</span>: <span class="number">90</span>,
            <span class="key">"passed"</span>: <span class="boolean">true</span>
        },
        <span class="key">"incorrect_details"</span>: [
            {
                <span class="key">"question_id"</span>: <span class="number">5</span>,
                <span class="key">"user_answer"</span>: <span class="string">"B"</span>,
                <span class="key">"correct_answer"</span>: <span class="string">"A"</span>
            }
        ]
    }
}
                </div>
            </div>
        </section>

        <!-- Adaptive Learning -->
        <section id="adaptive-learning">
            <div class="section-header">
                <h3><i class="bi bi-brain"></i> Adaptive Learning AI</h3>
            </div>

            <div class="api-endpoint">
                <h5>
                    <span class="method-badge method-GET">GET</span>
                    Learning Progress
                </h5>
                <div class="endpoint-url">/api/learning/progress</div>
                <p class="text-muted"><i class="bi bi-lock"></i> Requires Authentication</p>

                <h6 class="mt-3">Response (200):</h6>
                <div class="code-block">
{
    <span class="key">"success"</span>: <span class="boolean">true</span>,
    <span class="key">"data"</span>: {
        <span class="key">"weak_areas"</span>: [
            {
                <span class="key">"category"</span>: <span class="string">"Belgilar"</span>,
                <span class="key">"success_rate"</span>: <span class="number">45</span>
            }
        ],
        <span class="key">"strong_areas"</span>: [
            {
                <span class="key">"category"</span>: <span class="string">"Yo'l qoidalari"</span>,
                <span class="key">"success_rate"</span>: <span class="number">92</span>
            }
        ]
    }
}
                </div>
            </div>

            <div class="api-endpoint">
                <h5>
                    <span class="method-badge method-POST">POST</span>
                    Personalized Test
                </h5>
                <div class="endpoint-url">/api/learning/personalized-test</div>
                <p class="text-muted"><i class="bi bi-lock"></i> Requires Authentication</p>
                <p class="text-info"><i class="bi bi-stars"></i> AI algoritmiga asoslangan shaxsiy test</p>

                <h6 class="mt-3">Request Body:</h6>
                <div class="code-block">
{
    <span class="key">"question_count"</span>: <span class="number">20</span>
}
                </div>
            </div>
        </section>

        <!-- Errors -->
        <section id="errors">
            <div class="section-header">
                <h3><i class="bi bi-exclamation-triangle"></i> Xato Kodlari</h3>
            </div>

            <div class="api-endpoint">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kod</th>
                            <th>Ma'nosi</th>
                            <th>Misol</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>200</code></td>
                            <td>Muvaffaqiyatli</td>
                            <td>So'rov muvaffaqiyatli bajarildi</td>
                        </tr>
                        <tr>
                            <td><code>201</code></td>
                            <td>Yaratildi</td>
                            <td>Yangi resurs yaratildi</td>
                        </tr>
                        <tr>
                            <td><code>400</code></td>
                            <td>Noto'g'ri so'rov</td>
                            <td>Vaqt tugadi, yoki noto'g'ri ma'lumot</td>
                        </tr>
                        <tr>
                            <td><code>401</code></td>
                            <td>Ruxsat yo'q</td>
                            <td>Token yaroqsiz yoki yo'q</td>
                        </tr>
                        <tr>
                            <td><code>403</code></td>
                            <td>Taqiqlangan</td>
                            <td>Obuna kerak</td>
                        </tr>
                        <tr>
                            <td><code>404</code></td>
                            <td>Topilmadi</td>
                            <td>Resurs mavjud emas</td>
                        </tr>
                        <tr>
                            <td><code>422</code></td>
                            <td>Validatsiya xatosi</td>
                            <td>Ma'lumotlar noto'g'ri formatda</td>
                        </tr>
                        <tr>
                            <td><code>500</code></td>
                            <td>Server xatosi</td>
                            <td>Ichki server xatosi</td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="mt-3">Error Response Format</h5>
                <div class="code-block">
{
    <span class="key">"success"</span>: <span class="boolean">false</span>,
    <span class="key">"message"</span>: <span class="string">"Xato xabari"</span>,
    <span class="key">"errors"</span>: {
        <span class="key">"field_name"</span>: [<span class="string">"Xato tavsifi"</span>]
    },
    <span class="key">"timestamp"</span>: <span class="string">"2024-01-01T12:00:00Z"</span>
}
                </div>
            </div>
        </section>

        <!-- API Testing Interface -->
        <section id="testing">
            <div class="section-header">
                <h3><i class="bi bi-play-circle"></i> API Testing Interface</h3>
            </div>

            <div class="test-interface">
                <h5>Test API Requests</h5>
                <p class="text-muted">API'ni bevosita bu yerdan test qiling</p>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Method</label>
                        <select class="form-select" id="test-method">
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Endpoint</label>
                        <input type="text" class="form-control" id="test-endpoint" placeholder="/api/tests" value="/api/tests">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Authorization Token (optional)</label>
                    <input type="text" class="form-control auth-token-input" id="test-token" placeholder="Bearer token...">
                </div>

                <div class="mb-3">
                    <label class="form-label">Request Body (JSON)</label>
                    <textarea class="form-control" id="test-body" rows="5" placeholder='{"key": "value"}'></textarea>
                </div>

                <button class="btn try-it-btn" onclick="sendTestRequest()">
                    <i class="bi bi-send"></i> Yuborish
                </button>

                <h5 class="mt-4">Response:</h5>
                <div class="response-viewer" id="test-response">
Javob bu yerda ko'rinadi...
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });

            // Update active nav link
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            this.classList.add('active');
        }
    });
});

// API Testing
async function sendTestRequest() {
    const method = document.getElementById('test-method').value;
    const endpoint = document.getElementById('test-endpoint').value;
    const token = document.getElementById('test-token').value;
    const body = document.getElementById('test-body').value;
    const responseDiv = document.getElementById('test-response');

    responseDiv.textContent = 'Yuborilmoqda...';

    const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    };

    if (token) {
        headers['Authorization'] = token.startsWith('Bearer ') ? token : `Bearer ${token}`;
    }

    const options = {
        method: method,
        headers: headers
    };

    if (method !== 'GET' && body) {
        try {
            options.body = JSON.stringify(JSON.parse(body));
        } catch (e) {
            responseDiv.textContent = 'Xato: JSON format noto\'g\'ri';
            return;
        }
    }

    try {
        const response = await fetch('{{ url("") }}' + endpoint, options);
        const data = await response.json();

        responseDiv.textContent = JSON.stringify(data, null, 2);

        // Syntax highlighting
        responseDiv.innerHTML = syntaxHighlight(JSON.stringify(data, null, 2));
    } catch (error) {
        responseDiv.textContent = 'Xato: ' + error.message;
    }
}

function syntaxHighlight(json) {
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        let cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}
</script>
@endpush
@endsection
