<?php
class ApiClient {
    
    // Метод для получения случайного рецепта
    public function getRandomRecipe(): array {
        try {
            $url = 'https://www.themealdb.com/api/json/v1/1/random.php';
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'ignore_errors' => true
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ]);
            
            $response = file_get_contents($url, false, $context);
            
            if ($response === false) {
                return $this->getTestRecipe();
            }
            
            $data = json_decode($response, true);
            
            if (!is_array($data) || !isset($data['meals'][0]) || json_last_error() !== JSON_ERROR_NONE) {
                return $this->getTestRecipe();
            }
            
            return $this->formatRecipeData($data['meals'][0]);
            
        } catch (\Exception $e) {
            return $this->getTestRecipe();
        }
    }

    // Метод для поиска рецепта по названию
    public function getRecipeInfo(string $recipeName): array {
        try {
            $url = 'https://www.themealdb.com/api/json/v1/1/search.php?s=' . urlencode($recipeName);
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'ignore_errors' => true
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ]);
            
            $response = file_get_contents($url, false, $context);
            
            if ($response === false) {
                return ['error' => 'Не удалось получить данные о рецепте'];
            }
            
            $data = json_decode($response, true);
            
            if (!is_array($data) || !isset($data['meals'][0]) || json_last_error() !== JSON_ERROR_NONE) {
                return ['error' => 'Рецепт не найден'];
            }
            
            return $this->formatRecipeData($data['meals'][0]);
            
        } catch (\Exception $e) {
            return ['error' => 'Ошибка при поиске рецепта: ' . $e->getMessage()];
        }
    }

    // Метод строго по заданию
    public function request($url): array {
        try {
            // Для задания используем API случайного рецепта
            $apiUrl = 'https://www.themealdb.com/api/json/v1/1/random.php';
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'ignore_errors' => true
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ]);
            
            $response = file_get_contents($apiUrl, false, $context);
            
            if ($response === false) {
                return ['error' => 'Не удалось получить данные от API'];
            }
            
            $data = json_decode($response, true);
            
            if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
                return ['error' => 'Неверный формат данных от API'];
            }
            
            return $data;
            
        } catch (\Exception $e) {
            return ['error' => 'Ошибка: ' . $e->getMessage()];
        }
    }

    // Форматирование данных рецепта
    private function formatRecipeData(array $recipe): array {
        // Собираем ингредиенты
        $ingredients = [];
        for ($i = 1; $i <= 20; $i++) {
            $ingredient = $recipe['strIngredient' . $i] ?? '';
            $measure = $recipe['strMeasure' . $i] ?? '';
            
            if (!empty($ingredient)) {
                $ingredients[] = trim($measure . ' ' . $ingredient);
            }
        }

        return [
            'name' => $recipe['strMeal'] ?? 'Неизвестно',
            'category' => $recipe['strCategory'] ?? 'Не указана',
            'area' => $recipe['strArea'] ?? 'Не указана',
            'instructions' => $recipe['strInstructions'] ?? 'Инструкция не указана',
            'ingredients' => $ingredients,
            'image' => $recipe['strMealThumb'] ?? '',
            'youtube' => $recipe['strYoutube'] ?? '',
            'tags' => $recipe['strTags'] ?? ''
        ];
    }

    // Тестовые данные на случай если API не доступно
    private function getTestRecipe(): array {
        return [
            'name' => 'Спагетти Карбонара',
            'category' => 'Pasta',
            'area' => 'Italian',
            'instructions' => '1. Приготовьте спагетти согласно инструкции на упаковке. 2. Обжарьте бекон до хрустящей корочки. 3. Взбейте яйца с тертым сыром. 4. Смешайте все ингредиенты с горячими спагетти.',
            'ingredients' => [
                '400g Спагетти',
                '200g Гуанчиале',
                '4 яйца',
                '100g Пекорино Романо',
                'Черный перец по вкусу'
            ],
            'image' => 'https://www.themealdb.com/images/media/meals/llcbn01574260722.jpg',
            'youtube' => 'https://www.youtube.com/watch?v=3A2ZNY1F6-c',
            'tags' => 'Pasta,Italian'
        ];
    }
}