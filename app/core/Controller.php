<?php
namespace App\Core;

/**
 * Base Controller Class
 * Loads models and views, and provides helper functions.
 */
class Controller {
    
    /**
     * Load Model
     * Instantiates and returns the requested model.
     * 
     * @param string $model Name of the model
     * @return object Instance of the model class
     */
    public function model($model) {
        $modelClass = "App\\Models\\" . $model;
        if (class_exists($modelClass)) {
            return new $modelClass();
        } else {
            die("Model class '{$modelClass}' not found.");
        }
    }

    /**
     * Load View
     * Renders a view file and passes data to it.
     * 
     * @param string $view Path of the view relative to the views folder (e.g., 'jobs/index')
     * @param array $data Associative array of data to pass to the view
     */
    public function view($view, $data = []) {
        $viewFile = APP_ROOT . '/views/' . str_replace('.', '/', $view) . '.php';
        
        if (file_exists($viewFile)) {
            // Extract the associative array into variables for the view
            extract($data);
            require_once $viewFile;
        } else {
            die("View file '{$viewFile}' not found.");
        }
    }

    /**
     * Redirect Utility
     * Redirects to a relative route.
     * 
     * @param string $url Relative URL route (e.g., 'jobs/dashboard')
     */
    public function redirect($url) {
        header('Location: ' . URL_ROOT . '/' . ltrim($url, '/'));
        exit();
    }
}
