<?php
/**
 * Classe Database moderne pour PHP8
 */
class Database {
    private static ?Database $instance = null;
    private PDO $pdo;
    private array $config;
    
    private function __construct() {
        $this->config = require dirname(dirname(__FILE__)) . '/config/database.php';
        $this->connect();
    }
    
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect(): void {
        try {
            $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']};charset={$this->config['charset']}";
            $this->pdo = new PDO($dsn, $this->config['username'], $this->config['password'], $this->config['options']);
             $this->pdo->exec("SET time_zone = 'Europe/Paris'");
        } catch (PDOException $e) {
            $this->handleError('Erreur de connexion à la base de données: ' . $e->getMessage());
        }
    }
    
    public function query(string $sql, array $params = []): array {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            $this->handleError('Erreur de requête: ' . $e->getMessage() . " SQL: $sql");
            return [];
        }
    }
    
    public function queryRow(string $sql, array $params = []): ?array {
        $result = $this->query($sql, $params);
        return $result[0] ?? null;
    }
    
    public function insert(string $sql, array $params = []): int {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            $this->handleError('Erreur d\'insertion: ' . $e->getMessage() . " SQL: $sql");
            return 0;
        }
    }
    
    public function update(string $sql, array $params = []): int {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->handleError('Erreur de mise à jour: ' . $e->getMessage() . " SQL: $sql");
            return 0;
        }
    }
    
    public function delete(string $sql, array $params = []): int {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            $this->handleError('Erreur de suppression: ' . $e->getMessage() . " SQL: $sql");
            return 0;
        }
    }
    
    public function count(string $sql, array $params = []): int {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            $this->handleError('Erreur de comptage: ' . $e->getMessage() . " SQL: $sql");
            return 0;
        }
    }
    
    private function handleError(string $message): void {
        error_log($message);
        
        $app_config = require dirname(dirname(__FILE__)) . '/config/app.php';
        if ($app_config['environment'] === 'dev') {
            die('<div style="color: red; padding: 20px; border: 1px solid red; margin: 20px;">' . htmlspecialchars($message) . '</div>');
        } else {
            die('Erreur système. Veuillez contacter l\'administrateur.');
        }
    }
    
    // Méthodes de compatibilité avec l'ancien code
    public function getMaxId(string $table, string $field): int {
        $sql = "SELECT MAX({$field}_num) as max_id FROM {$table}";
        $result = $this->queryRow($sql);
        return $result ? (int)$result['max_id'] + 1 : 1;
    }
    
    public function hasResults(string $sql, array $params = []): bool {
        return count($this->query($sql, $params)) > 0;
    }
    
    public function getPDO(): PDO {
        return $this->pdo;
    }

    public function quote($value): string {
        if ($value === null) {
            return 'NULL';
        }
        return $this->pdo->quote($value);
    }
}

// Fonctions de compatibilité pour l'ancien code
function DbMax(string $table, string $champ, int $test = 0): int {
    $db = Database::getInstance();
    return $db->getMaxId($table, $champ);
}

function DbSelect(string $sql, int $test = 0): array {
    $db = Database::getInstance();
    $result = $base->query($sql);
    
    // Format compatible avec l'ancien code
    return [
        'nbr' => count($result),
        'result' => $result
    ];
}

function DbTestResultat(string $sql, int $test = 0): bool {
    $db = Database::getInstance();
    return $db->hasResults($sql);
}

function DbNbrLigne(string $sql, int $test = 0): int {
    $db = Database::getInstance();
    return count($base->query($sql));
}
?>