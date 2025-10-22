<?php
session_start();
require_once 'UserInfo.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа №4 - API рецептов</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }
        
        .nav {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .nav a {
            display: inline-block;
            background: #ff7e5f;
            color: white;
            padding: 10px 20px;
            margin: 0 10px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .nav a:hover {
            background: #ff6b4a;
            transform: translateY(-2px);
        }
        
        .info-section {
            background: #fff5f2;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #ff7e5f;
        }
        
        .api-data {
            background: #fffaf0;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #ffa500;
        }
        
        .recipe-card {
            background: white;
            padding: 20px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #32cd32;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .recipe-image {
            max-width: 300px;
            border-radius: 8px;
            margin: 10px 0;
        }
        
        .ingredients-list {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        
        .instructions {
            background: #f0f8ff;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            white-space: pre-line;
            line-height: 1.6;
        }
        
        .success-badge {
            background: #32cd32;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin-left: 10px;
        }
        
        .api-status {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .api-icon {
            font-size: 24px;
        }
        
        .tag {
            display: inline-block;
            background: #ffeb3b;
            color: #333;
            padding: 3px 8px;
            margin: 2px;
            border-radius: 12px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🍳 Лабораторная работа №4 - API рецептов</h1>
        
        <div class="nav">
            <a href="form.html">📝 Форма поиска рецепта</a>
            <a href="recipes.php">📚 Случайные рецепты</a>
        </div>

        <!-- Информация о пользователе -->
        <div class="info-section">
            <h3>👤 Информация о пользователе:</h3>
            <?php
            $clientInfo = UserInfo::getClientInfo();
            foreach ($clientInfo as $key => $value) {
                echo '<p><strong>' . htmlspecialchars($key) . ':</strong> ' . htmlspecialchars($value) . '</p>';
            }
            ?>
            <p><strong>Последняя отправка формы:</strong> <?php echo htmlspecialchars(UserInfo::getLastSubmission()); ?></p>
        </div>

        <!-- ДАННЫЕ ИЗ API ПО ЗАДАНИЮ -->
        <?php if(isset($_SESSION['api_data'])): ?>
            <div class="api-data">
                <div class="api-status">
                    <span class="api-icon">✅</span>
                    <h3 style="margin: 0;">Данные из API успешно загружены</h3>
                    <span class="success-badge">The Meal DB API</span>
                </div>
                <p><strong>Получено записей:</strong> <?php echo is_array($_SESSION['api_data']) ? count($_SESSION['api_data']) : '0'; ?></p>
                <p><em>Данные получены в соответствии с требованиями задания</em></p>
                <?php unset($_SESSION['api_data']); ?>
            </div>
        <?php endif; ?>

        <!-- Информация о найденном рецепте -->
        <?php if(isset($_SESSION['recipe_info'])): ?>
            <div class="api-data">
                <h3>🍽️ Найденный рецепт:</h3>
                <?php if(isset($_SESSION['recipe_info']['error'])): ?>
                    <p style="color: red;"><?php echo htmlspecialchars($_SESSION['recipe_info']['error']); ?></p>
                <?php else: ?>
                    <div class="recipe-card">
                        <h4><?php echo htmlspecialchars($_SESSION['recipe_info']['name'] ?? 'Рецепт'); ?></h4>
                        
                        <?php if(!empty($_SESSION['recipe_info']['image'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['recipe_info']['image']); ?>" alt="Изображение блюда" class="recipe-image">
                        <?php endif; ?>
                        
                        <p><strong>Категория:</strong> <?php echo htmlspecialchars($_SESSION['recipe_info']['category'] ?? 'Не указана'); ?></p>
                        <p><strong>Кухня:</strong> <?php echo htmlspecialchars($_SESSION['recipe_info']['area'] ?? 'Не указана'); ?></p>
                        
                        <?php if(!empty($_SESSION['recipe_info']['tags'])): ?>
                            <p><strong>Теги:</strong> 
                                <?php 
                                $tags = explode(',', $_SESSION['recipe_info']['tags']);
                                foreach($tags as $tag): 
                                    if(trim($tag)): ?>
                                        <span class="tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                    <?php endif;
                                endforeach; ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if(!empty($_SESSION['recipe_info']['ingredients'])): ?>
                            <div class="ingredients-list">
                                <h5>🛒 Ингредиенты:</h5>
                                <ul>
                                    <?php foreach($_SESSION['recipe_info']['ingredients'] as $ingredient): ?>
                                        <li><?php echo htmlspecialchars($ingredient); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($_SESSION['recipe_info']['instructions'])): ?>
                            <div class="instructions">
                                <h5>👨‍🍳 Инструкция по приготовлению:</h5>
                                <?php echo nl2br(htmlspecialchars($_SESSION['recipe_info']['instructions'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($_SESSION['recipe_info']['youtube'])): ?>
                            <p><strong>🎥 Видео рецепт:</strong> <a href="<?php echo htmlspecialchars($_SESSION['recipe_info']['youtube']); ?>" target="_blank">Смотреть на YouTube</a></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php unset($_SESSION['recipe_info']); ?>
            </div>
        <?php endif; ?>

        <!-- Данные из формы -->
        <?php if(isset($_SESSION['username'])): ?>
            <div class="info-section" style="border-left-color: #32cd32;">
                <h3>✅ Данные из формы:</h3>
                <p><strong>Имя:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <p><strong>Искомый рецепт:</strong> <?php echo htmlspecialchars($_SESSION['recipe'] ?? 'Не указан'); ?></p>
                <?php
                unset($_SESSION['username']);
                unset($_SESSION['recipe']);
                ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>