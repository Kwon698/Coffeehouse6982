<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars($_POST['name']);
    $phone = htmlspecialchars($_POST['phone']);
    $email = htmlspecialchars($_POST['email']);
    $address = htmlspecialchars($_POST['address']);
    $notes = htmlspecialchars($_POST['notes']);
    $items = $_POST['items'] ?? [];
    $total = htmlspecialchars($_POST['total']);

    // Сохраняем заказ в файл
    $order_line = date("Y-m-d H:i:s") . " - Имя: $name, Телефон: $phone, Email: $email, Адрес: $address, Сумма: $total, Примечания: $notes\n";
    file_put_contents(__DIR__ . "/orders/orders.txt", $order_line, FILE_APPEND);

    // Отправка письма через SMTP
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'bolkvadzemaksim@gmail.com'; // твой Gmail
        $mail->Password = 'aidv ntwk yywk fiaz'; // пароль приложения
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('your_gmail@gmail.com', 'Coffeehouse');
        $mail->addAddress('your_gmail@gmail.com'); // получатель

        $mail->Subject = 'Новый заказ с сайта';
        $body = "Имя: $name\nТелефон: $phone\nEmail: $email\nАдрес: $address\nПримечания: $notes\nСумма: $total руб.\n\nТовары:\n";
        foreach ($items as $item) {
            $body .= $item['name'] . " — " . $item['quantity'] . " шт. — " . $item['price'] . " руб.\n";
        }
        $mail->Body = $body;

        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Заказ отправлен']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $mail->ErrorInfo]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Неверный метод']);
}
?>
