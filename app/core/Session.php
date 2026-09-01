<?php
namespace App\Core;

/**
 * Session Class
 * Manages user sessions, role checks, and flash messages.
 */
class Session {
    
    /**
     * Start session if not already active
     */
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set a Session Variable
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a Session Variable
     */
    public static function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * Remove a Session Variable
     */
    public static function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Set Flash Message (Temp message that is cleared after being displayed)
     */
    public static function setFlash($name, $message, $class = 'alert-danger') {
        $_SESSION['flash'][$name] = [
            'message' => $message,
            'class' => $class
        ];
    }

    /**
     * Display & clear Flash Message
     */
    public static function flash($name) {
        if (isset($_SESSION['flash'][$name])) {
            $flash = $_SESSION['flash'][$name];
            unset($_SESSION['flash'][$name]);
            
            return '
            <div class="alert ' . htmlspecialchars($flash['class']) . ' alert-dismissible fade show border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <div class="me-2">
                        ' . (strpos($flash['class'], 'success') !== false ? '<i class="fa-solid fa-circle-check fs-5"></i>' : '<i class="fa-solid fa-circle-exclamation fs-5"></i>') . '
                    </div>
                    <div>' . htmlspecialchars($flash['message']) . '</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
        return '';
    }

    /**
     * Destroy user session (Log out)
     */
    public static function destroy() {
        self::init();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Restrict page access based on user roles
     * Redirects to login if not logged in, or unauthorized if role mismatch.
     * 
     * @param array|string $roles Allowed role(s) (e.g. 'admin', ['employer', 'admin'])
     */
    public static function authorize($roles) {
        self::init();
        
        if (!self::isLoggedIn()) {
            self::setFlash('auth_error', 'Please log in to access this page.');
            header('Location: ' . URL_ROOT . '/login');
            exit();
        }

        $userRole = self::get('user_role');
        $allowedRoles = is_array($roles) ? $roles : [$roles];

        if (!in_array($userRole, $allowedRoles)) {
            // Unauthorized
            http_response_code(403);
            die("403 Unauthorized Access - You do not have permission to view this page.");
        }
    }
}
