<?php

/**
 * E-Bilet Service Communication Examples
 * 
 * Bu dosya, servisler arası iletişim için kullanım örneklerini içerir.
 */

use Ebilet\Common\Facades\User;
use Ebilet\Common\Facades\Order;
use Ebilet\Common\Facades\Log;

// ============================================================================
// USER SERVICE KULLANIMI
// ============================================================================

// 1. GET request - Kullanıcı bilgisi alma
$userResponse = User::get('/v1/users/123');
if ($userResponse->successful()) {
    $userData = $userResponse->json();
    Log::info('User data retrieved', $userData);
}

// 2. POST request - Yeni kullanıcı oluşturma
$createUserResponse = User::post('/v1/users', [
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// 3. PUT request - Kullanıcı güncelleme
$updateUserResponse = User::put('/v1/users/123', [
    'name' => 'John Updated'
]);

// 4. DELETE request - Kullanıcı silme
$deleteUserResponse = User::delete('/v1/users/123');

// 5. PATCH request - Kısmi güncelleme
$patchUserResponse = User::patch('/v1/users/123', [
    'status' => 'active'
]);

// ============================================================================
// ORDER SERVICE KULLANIMI
// ============================================================================

// 1. GET request - Sipariş bilgisi alma
$orderResponse = Order::get('/v1/orders/456');
if ($orderResponse->successful()) {
    $orderData = $orderResponse->json();
    Log::info('Order data retrieved', $orderData);
}

// 2. POST request - Yeni sipariş oluşturma
$createOrderResponse = Order::post('/v1/orders', [
    'user_id' => 123,
    'items' => [
        ['product_id' => 1, 'quantity' => 2],
        ['product_id' => 2, 'quantity' => 1]
    ],
    'total_amount' => 150.00
]);

// 3. PUT request - Sipariş güncelleme
$updateOrderResponse = Order::put('/v1/orders/456', [
    'status' => 'processing'
]);

// 4. DELETE request - Sipariş iptal etme
$cancelOrderResponse = Order::delete('/v1/orders/456');

// ============================================================================
// TOKEN VE HEADERS KULLANIMI
// ============================================================================

// 1. Token ile istek
$authenticatedUserRequest = User::withToken('your-jwt-token')->get('/v1/users/123/profile');

// 2. Custom headers ile istek
$customRequest = User::withHeaders([
    'X-Custom-Header' => 'value',
    'X-Request-ID' => uniqid()
])->get('/v1/users/123');

// 3. Timeout ayarlama
$timeoutRequest = User::timeout(60)->get('/v1/users/123');

// 4. Chaining ile kullanım
$result = User::withToken('your-jwt-token')
    ->timeout(30)
    ->withHeaders(['X-Request-ID' => uniqid()])
    ->post('/v1/users', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com'
    ]);

// ============================================================================
// ERROR HANDLING
// ============================================================================

try {
    $response = User::get('/v1/users/999');
    
    if ($response->successful()) {
        $userData = $response->json();
        Log::info('User data retrieved', $userData);
    } else {
        Log::warning('Failed to get user data', [
            'status_code' => $response->status(),
            'response' => $response->body()
        ]);
    }
} catch (\Exception $e) {
    Log::error('Service communication error', [
        'error' => $e->getMessage(),
        'service' => 'user-service'
    ]);
}

// ============================================================================
// CONDITIONAL REQUESTS
// ============================================================================

// Kullanıcı varsa sipariş oluştur
$userExists = User::get('/v1/users/123');
if ($userExists->successful()) {
    $userData = $userExists->json();
    
    // Kullanıcı aktifse sipariş oluştur
    if ($userData['status'] === 'active') {
        $orderResponse = Order::post('/v1/orders', [
            'user_id' => $userData['id'],
            'items' => [['product_id' => 1, 'quantity' => 1]],
            'total_amount' => 50.00
        ]);
        
        if ($orderResponse->successful()) {
            Log::info('Order created for user', [
                'user_id' => $userData['id'],
                'order_id' => $orderResponse->json()['id']
            ]);
        }
    }
}

// ============================================================================
// MONITORING AND LOGGING
// ============================================================================

// Service performance monitoring
$startTime = microtime(true);
$response = User::get('/v1/users/123');
$duration = (microtime(true) - $startTime) * 1000;

Log::info('Service request completed', [
    'service' => 'user-service',
    'endpoint' => '/v1/users/123',
    'duration_ms' => round($duration, 2),
    'status_code' => $response->status(),
    'success' => $response->successful()
]);

// ============================================================================
// CONFIGURATION EXAMPLES
// ============================================================================

// Environment variables for service configuration:
/*
USER_SERVICE_URL=http://host.docker.internal:8081
USER_SERVICE_TIMEOUT=30

ORDER_SERVICE_URL=http://host.docker.internal:8082
ORDER_SERVICE_TIMEOUT=30

AUTH_SERVICE_URL=http://host.docker.internal:8080
AUTH_SERVICE_TIMEOUT=30
*/

// ============================================================================
// YENİ SERVİS EKLEME
// ============================================================================

// Yeni bir servis eklemek için:
// 1. Config'e ekle:
/*
'payment' => [
    'url' => env('PAYMENT_SERVICE_URL', 'http://host.docker.internal:8083'),
    'timeout' => env('PAYMENT_SERVICE_TIMEOUT', 30),
],
*/

// 2. Facade oluştur:
/*
class Payment extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'ebilet.payment';
    }
}
*/

// 3. Service Provider'a ekle:
/*
$this->app->singleton('ebilet.payment', function ($app) {
    return new \Ebilet\Common\Services\ServiceClient('payment');
});

$this->app->alias('ebilet.payment', \Ebilet\Common\Facades\Payment::class);
*/

// 4. Kullanım:
/*
$paymentResponse = Payment::post('/v1/payments', [
    'amount' => 100,
    'currency' => 'TRY'
]);
*/ 