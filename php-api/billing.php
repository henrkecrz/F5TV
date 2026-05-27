<?php
require __DIR__ . '/_bootstrap.php';

try {
    $pdo = db();
    $action = $_GET['action'] ?? 'plans';

    if ($action === 'plans') {
        $stmt = $pdo->query('SELECT id, name, price, features, active, max_screens, quality FROM plans WHERE active = 1 ORDER BY price ASC');
        json_response(['plans' => $stmt->fetchAll()]);
    }

    if ($action === 'validate-coupon') {
        $body = input_json();
        $code = strtoupper(trim($body['code'] ?? ''));
        $planId = $body['planId'] ?? null;

        $stmt = $pdo->prepare('SELECT * FROM coupons WHERE UPPER(code) = ? LIMIT 1');
        $stmt->execute([$code]);
        $coupon = $stmt->fetch();

        if (!$coupon) json_response(['valid' => false, 'message' => 'Cupom não encontrado.'], 404);
        if ($coupon['status'] !== 'active') json_response(['valid' => false, 'message' => 'Cupom inativo.'], 400);
        if (strtotime($coupon['expires_at']) < strtotime(date('Y-m-d'))) json_response(['valid' => false, 'message' => 'Cupom expirado.'], 400);
        if ((int)$coupon['usage_count'] >= (int)$coupon['usage_limit']) json_response(['valid' => false, 'message' => 'Limite esgotado.'], 400);

        $plans = json_decode($coupon['applicable_plans'] ?? '[]', true) ?: [];
        if ($planId && count($plans) > 0 && !in_array($planId, $plans, true)) {
            json_response(['valid' => false, 'message' => 'Cupom não aplicável ao plano selecionado.'], 400);
        }

        json_response(['valid' => true, 'coupon' => $coupon]);
    }

    if ($action === 'checkout') {
        $body = input_json();
        $userId = $body['userId'] ?? 'user-assinante';
        $planId = $body['planId'] ?? 'plano-premium';
        $paymentMethod = $body['paymentMethod'] ?? 'pix';
        $couponCode = strtoupper(trim($body['couponCode'] ?? ''));

        $pdo->beginTransaction();

        $planStmt = $pdo->prepare('SELECT * FROM plans WHERE id = ? AND active = 1 LIMIT 1');
        $planStmt->execute([$planId]);
        $plan = $planStmt->fetch();
        if (!$plan) {
            $pdo->rollBack();
            json_response(['error' => 'PLAN_NOT_FOUND'], 404);
        }

        $total = (float)$plan['price'];

        if ($couponCode) {
            $couponStmt = $pdo->prepare('SELECT * FROM coupons WHERE UPPER(code) = ? AND status = "active" LIMIT 1');
            $couponStmt->execute([$couponCode]);
            $coupon = $couponStmt->fetch();
            if ($coupon && (int)$coupon['usage_count'] < (int)$coupon['usage_limit']) {
                $discount = $coupon['discount_type'] === 'percent'
                    ? $total * ((float)$coupon['discount_value'] / 100)
                    : (float)$coupon['discount_value'];
                $total = max(0, $total - $discount);
                $updateCoupon = $pdo->prepare('UPDATE coupons SET usage_count = usage_count + 1 WHERE id = ?');
                $updateCoupon->execute([$coupon['id']]);
            }
        }

        $subscriptionId = make_id('sub');
        $paymentId = make_id('pay');
        $nextBilling = date('Y-m-d', strtotime('+30 days'));

        $sub = $pdo->prepare('INSERT INTO subscriptions (id, user_id, plan_id, status, start_date, next_billing_date, payment_method) VALUES (?, ?, ?, "active", CURDATE(), ?, ?)');
        $sub->execute([$subscriptionId, $userId, $planId, $nextBilling, $paymentMethod]);

        $pay = $pdo->prepare('INSERT INTO payments (id, user_id, subscription_id, value, date, status, payment_method, gateway_reference) VALUES (?, ?, ?, ?, CURDATE(), "paid", ?, ?)');
        $pay->execute([$paymentId, $userId, $subscriptionId, $total, $paymentMethod, 'hostinger-sandbox-' . time()]);

        $userUpdate = $pdo->prepare('UPDATE users SET plan_id = ?, status = "active" WHERE id = ?');
        $userUpdate->execute([$planId, $userId]);

        $pdo->commit();

        json_response([
            'subscriptionId' => $subscriptionId,
            'paymentId' => $paymentId,
            'total' => $total,
            'status' => 'paid',
        ], 201);
    }

    json_response(['error' => 'ACTION_NOT_FOUND'], 404);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_response(['error' => 'SERVER_ERROR', 'message' => $e->getMessage()], 500);
}
