<?php
require_once 'ApiClient.php';
require_once 'UserInfo.php';

$apiClient = new ApiClient();
$recipes = [];

// Получаем 5 случайных рецептов
for ($i = 0; $i < 5; $i++) {
    $recipe = $apiClient->getRandomRecipe();
    if (!isset($recipe['error'])) {
        $recipes[] = $recipe;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Случайные рецепты</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
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
        
        .recipes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .recipe-card {
            background: #fffaf0;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #32cd32;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .recipe-image {
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .recipe-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        
        .recipe-info {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .ingredients-preview {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            font-size: 13px;
        }
        
        .stats {
            background: #fff5f2;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .error {
            background: #ffe6e6;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #ff4444;
        }
        
        .success {
            background: #e6ffe6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #28a745;
        }
        
        .tag {
            display: inline-block;
            background: #ffeb3b;
            color: #333;
            padding: 3px 8px;
            margin: 2px;
            border-radius: 12px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 Случайные рецепты</h1>
        
        <div class="nav">
            <a href="index.php">← На главную</a>
            <a href="form.html">🔍 Поиск рецептов</a>
        </div>

        <div class="stats">
            <h3>🍽️ Загружено случайных рецептов: <?php echo count($recipes); ?></h3>
            <p>Данные предоставлены The Meal DB API</p>
        </div>

        <?php if (empty($recipes)): ?>
            <div class="error">
                <p>Не удалось загрузить рецепты. Попробуйте обновить страницу.</p>
            </div>
        <?php else: ?>
            <div class="success">
                <p>✅ Случайные рецепты успешно загружены!</p>
            </div>
        <?php endif; ?>

        <div class="recipes-grid">
            <?php foreach($recipes as $recipe): ?>
                <?php if (is_array($recipe) && isset($recipe['name'])): ?>
                    <div class="recipe-card">
                        <?php if(!empty($recipe['image'])): ?>
                            <img src="<?php echo htmlspecialchars($recipe['image']); ?>" alt="<?php echo htmlspecialchars($recipe['name']); ?>" class="recipe-image">
                        <?php endif; ?>
                        
                        <div class="recipe-name"><?php echo htmlspecialchars($recipe['name']); ?></div>
                        
                        <div class="recipe-info">
                            <strong>Категория:</strong> <?php echo htmlspecialchars($recipe['category']); ?><br>
                            <strong>Кухня:</strong> <?php echo htmlspecialchars($recipe['area']); ?>
                        </div>
                        
                        <?php if(!empty($recipe['tags'])): ?>
                            <div class="recipe-info">
                                <strong>Теги:</strong> 
                                <?php 
                                $tags = explode(',', $recipe['tags']);
                                foreach(array_slice($tags, 0, 3) as $tag): 
                                    if(trim($tag)): ?>
                                        <span class="tag"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                    <?php endif;
                                endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($recipe['ingredients'])): ?>
                            <div class="ingredients-preview">
                                <strong>🛒 Ингредиенты (первые 5):</strong><br>
                                <?php 
                                $previewIngredients = array_slice($recipe['ingredients'], 0, 5);
                                foreach($previewIngredients as $ingredient): ?>
                                    • <?php echo htmlspecialchars($ingredient); ?><br>
                                <?php endforeach; ?>
                                <?php if(count($recipe['ingredients']) > 5): ?>
                                    <em>... и еще <?php echo count($recipe['ingredients']) - 5; ?> ингредиентов</em>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($recipe['youtube'])): ?>
                            <div class="recipe-info">
                                <a href="<?php echo htmlspecialchars($recipe['youtube']); ?>" target="_blank" style="color: #ff7e5f; text-decoration: none;">
                                    🎥 Смотреть видео рецепт
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>