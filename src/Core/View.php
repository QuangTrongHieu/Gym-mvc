<?php

namespace Core;

class View
{
    public static function render($view, $data = [])
    {
        extract($data);
        
        $viewPath = ROOT_PATH . "/src/App/Views/{$view}.php";
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: {$view}");
        }
        
        ob_start();
        require $viewPath;
        return ob_get_clean();
    }

    public static function renderWithLayout($view, $data = [], $layout = 'default')
    {
        $content = self::render($view, $data);
        
        $layoutPath = ROOT_PATH . "/src/App/Views/layouts/{$layout}.php";
        
        if (!file_exists($layoutPath)) {
            throw new \Exception("Layout not found: {$layout}");
        }
        
        ob_start();
        require $layoutPath;
        return ob_get_clean();
    }
}
