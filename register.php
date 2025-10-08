<?php
// Настройки подключения к БД
$servername = "localhost";  // обычно localhost
$username = "root";
$password = "";
$dbname = "dl";

// Создаем соединение
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверяем соединение
if ($conn->connect_error) {
    die("Ошибка соединения: " . $conn->connect_error);
}

// Проверяем, что данные пришли методом POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем и очищаем данные
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    // Базовая валидация
    if (empty($user) || empty($email) || empty($pass)) {
        echo "Пожалуйста, заполните все поля!";
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Неверный формат email!";
        exit;
    }

    // Хэшируем пароль (для безопасности)
    $passwordHash = password_hash($pass, PASSWORD_DEFAULT);

    // Подготовка запроса, чтобы избежать SQL-инъекций
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user, $email, $passwordHash);

    // Выполняем
    if ($stmt->execute()) {
        echo "Регистрация успешна! Можете <a href='login.html'>войти</a>";
    } else {
        // Если user или email уже заняты, запрос вернет ошибку
        if ($conn->errno == 1062) { 
            echo "Пользователь с таким именем или email уже существует.";
        } else {
            echo "Ошибка регистрации: " . $conn->error;
        }
    }

    $stmt->close();
}

$conn->close();
?>