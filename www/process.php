<?php
// ВКЛЮЧАЕМ БУФЕРИЗАЦИЮ вывода
ob_start();

session_start();
require_once 'ApiClient.php';
require_once 'UserInfo.php';

// Получаем данные формы
$name = htmlspecialchars($_POST['name'] ?? '');
$recipe = htmlspecialchars($_POST['recipe'] ?? '');

// Сохраняем в сессию
$_SESSION['username'] = $name;
$_SESSION['recipe'] = $recipe;

// ПО ЗАДАНИЮ: получаем данные из API через метод request()
$api = new ApiClient();
$url = 'https://www.themealdb.com/api/json/v1/1/random.php';
$_SESSION['api_data'] = $api->request($url);

// ПО ЗАДАНИЮ: устанавливаем куки
setcookie("last_submission", date('Y-m-d H:i:s'), time() + 3600, "/");

// Дополнительно: сохраняем информацию о найденном рецепте
$_SESSION['recipe_info'] = $api->getRecipeInfo($recipe);

// Сохраняем в файл
$dataLine = $name . ";" . $recipe . ";" . date('Y-m-d H:i:s') . "\n";
file_put_contents("data.txt", $dataLine, FILE_APPEND);

// ОЧИЩАЕМ буфер и перенаправляем
ob_end_clean();
header("Location: index.php");
exit();
?>