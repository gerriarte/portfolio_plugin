<?php
/**
 * Sistema de logging estructurado para el plugin Portfolio
 * 
 * @package Sabsfe_Portfolio
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Sabsfe_Portfolio_Logger {
    
    private static $instance = null;
    private $log_file = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $upload_dir = wp_upload_dir();
        $this->log_file = $upload_dir['basedir'] . '/sabsfe-portfolio-plugin-logs.log';
    }
    
    /**
     * Registrar log estructurado
     * 
     * @param string $level Nivel de log (info, warning, error, debug)
     * @param string $module Módulo que genera el log
     * @param string $function Función que genera el log
     * @param string $message Mensaje del log
     * @param array $context Contexto adicional
     */
    public static function log($level, $module, $function, $message, $context = array()) {
        $logger = self::get_instance();
        
        $log_entry = array(
            'timestamp' => current_time('Y-m-d H:i:s'),
            'level' => strtoupper($level),
            'module' => $module,
            'function' => $function,
            'message' => $message,
            'context' => $context,
            'user_id' => get_current_user_id(),
            'ip' => self::get_client_ip()
        );
        
        $log_line = sprintf(
            "[%s] %s | %s::%s | %s | User: %d | IP: %s",
            $log_entry['timestamp'],
            $log_entry['level'],
            $log_entry['module'],
            $log_entry['function'],
            $log_entry['message'],
            $log_entry['user_id'],
            $log_entry['ip']
        );
        
        if (!empty($context)) {
            $log_line .= " | Context: " . json_encode($context);
        }
        
        $log_line .= PHP_EOL;
        
        // Escribir al archivo de log
        error_log($log_line, 3, $logger->log_file);
        
        // También escribir al log de WordPress si está en modo debug
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log($log_line);
        }
    }
    
    /**
     * Log de información
     */
    public static function info($module, $function, $message, $context = array()) {
        self::log('info', $module, $function, $message, $context);
    }
    
    /**
     * Log de advertencia
     */
    public static function warning($module, $function, $message, $context = array()) {
        self::log('warning', $module, $function, $message, $context);
    }
    
    /**
     * Log de error
     */
    public static function error($module, $function, $message, $context = array()) {
        self::log('error', $module, $function, $message, $context);
    }
    
    /**
     * Log de debug
     */
    public static function debug($module, $function, $message, $context = array()) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            self::log('debug', $module, $function, $message, $context);
        }
    }
    
    /**
     * Obtener IP del cliente
     */
    private static function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
    }
    
    /**
     * Obtener logs recientes
     */
    public static function get_recent_logs($lines = 100) {
        $logger = self::get_instance();
        
        if (!file_exists($logger->log_file)) {
            return array();
        }
        
        $logs = array();
        $file_lines = file($logger->log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if ($file_lines) {
            $file_lines = array_slice($file_lines, -$lines);
            
            foreach ($file_lines as $line) {
                $logs[] = $line;
            }
        }
        
        return $logs;
    }
    
    /**
     * Limpiar logs antiguos
     */
    public static function clean_old_logs($days = 30) {
        $logger = self::get_instance();
        
        if (!file_exists($logger->log_file)) {
            return;
        }
        
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $lines = file($logger->log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $filtered_lines = array();
        
        foreach ($lines as $line) {
            // Extraer timestamp del log
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                if ($matches[1] >= $cutoff_date) {
                    $filtered_lines[] = $line;
                }
            }
        }
        
        file_put_contents($logger->log_file, implode(PHP_EOL, $filtered_lines) . PHP_EOL);
    }
    
    /**
     * Obtener estadísticas de logs
     */
    public static function get_log_stats() {
        $logger = self::get_instance();
        
        if (!file_exists($logger->log_file)) {
            return array(
                'total_logs' => 0,
                'error_count' => 0,
                'warning_count' => 0,
                'info_count' => 0,
                'file_size' => 0
            );
        }
        
        $lines = file($logger->log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $stats = array(
            'total_logs' => count($lines),
            'error_count' => 0,
            'warning_count' => 0,
            'info_count' => 0,
            'file_size' => filesize($logger->log_file)
        );
        
        foreach ($lines as $line) {
            if (strpos($line, '| ERROR |') !== false) {
                $stats['error_count']++;
            } elseif (strpos($line, '| WARNING |') !== false) {
                $stats['warning_count']++;
            } elseif (strpos($line, '| INFO |') !== false) {
                $stats['info_count']++;
            }
        }
        
        return $stats;
    }
}
