<?php
// Quick update script — run this via XAMPP terminal or browser to update the coupon
// Place this at the root and access via: http://localhost/SampleEcomm/update_coupon.php
// DELETE this file after running!

$host = '127.0.0.1';
$dbname = 'sampleecomm';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Update existing WELCOME10 to WELCOME20 (20% percent)
    $stmt = $pdo->prepare("UPDATE coupons SET code='WELCOME20', discount_amount=20, discount_type='percent', updated_at=NOW() WHERE code='WELCOME10'");
    $affected = $stmt->execute();
    $rows = $stmt->rowCount();
    
    if ($rows > 0) {
        echo "✅ Success! Updated WELCOME10 → WELCOME20 (20% discount). Rows affected: $rows<br>";
    } else {
        // Insert if doesn't exist yet
        $stmt2 = $pdo->prepare("INSERT INTO coupons (code, discount_amount, discount_type, min_spend, is_active, created_at, updated_at) VALUES ('WELCOME20', 20, 'percent', 30, 1, NOW(), NOW()) ON DUPLICATE KEY UPDATE discount_amount=20, discount_type='percent', min_spend=30, updated_at=NOW()");
        $stmt2->execute();
        echo "✅ Inserted/updated WELCOME20 coupon (20% off, min RM30).<br>";
    }
    
    // Show current coupons
    echo "<br><b>Current Coupons:</b><br>";
    foreach ($pdo->query("SELECT * FROM coupons") as $row) {
        echo "ID: {$row['id']} | Code: {$row['code']} | Discount: {$row['discount_amount']} {$row['discount_type']} | Min: RM{$row['min_spend']} | Active: {$row['is_active']}<br>";
    }
    
    echo "<br>⚠️ PLEASE DELETE THIS FILE (update_coupon.php) after running!";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
