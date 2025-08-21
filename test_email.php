<?php
/**
 * Test Email Configuration
 * 
 * File ini untuk menguji konfigurasi email sebelum digunakan di aplikasi utama
 * Hapus file ini setelah testing selesai untuk keamanan
 */

require_once 'connect/email_config.php';

// Test email configuration
$test_email = 'test@example.com'; // Ganti dengan email Anda untuk testing
$subject = 'Test Email - PT Waindo Specterra';
$message = "Halo,\n\nIni adalah email test untuk memverifikasi konfigurasi email PT Waindo Specterra.\n\nJika Anda menerima email ini, berarti konfigurasi email sudah benar.\n\nTerima kasih,\nSistem PT Waindo Specterra";

echo "<h2>Testing Email Configuration</h2>";
echo "<p>Mengirim email test ke: <strong>$test_email</strong></p>";

try {
    $result = sendEmail($test_email, $subject, $message);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Email berhasil dikirim!</p>";
        echo "<p>Silakan cek inbox email Anda.</p>";
    } else {
        echo "<p style='color: red;'>❌ Gagal mengirim email.</p>";
        echo "<p>Periksa error log untuk detail lebih lanjut.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Konfigurasi Email Saat Ini:</h3>";
echo "<ul>";
echo "<li><strong>Host:</strong> " . EMAIL_HOST . "</li>";
echo "<li><strong>Port:</strong> " . EMAIL_PORT . "</li>";
echo "<li><strong>Username:</strong> " . EMAIL_USERNAME . "</li>";
echo "<li><strong>From Name:</strong> " . EMAIL_FROM_NAME . "</li>";
echo "<li><strong>From Address:</strong> " . EMAIL_FROM_ADDRESS . "</li>";
echo "</ul>";

echo "<p><strong>Catatan:</strong> Hapus file ini setelah testing selesai untuk keamanan.</p>";
?>
