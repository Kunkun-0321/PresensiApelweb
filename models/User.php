<?php
// ============================================
// Model User: Updated to use single database config
// ============================================

require_once __DIR__ . '/../config/database.php';

class User {
    public static function getByUsername($username) {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAllMahasiswa() {
        $conn = Database::getConnection();
        $stmt = $conn->query("
            SELECT * FROM users 
            WHERE role = 'mahasiswa' 
            ORDER BY tingkat ASC, nama ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getMahasiswaByTingkat($tingkat) {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("
            SELECT * FROM users 
            WHERE role = 'mahasiswa' AND tingkat = ? 
            ORDER BY nama ASC
        ");
        $stmt->execute([$tingkat]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function insertMahasiswa($data) {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("
            INSERT INTO users (nim, nama, kelas, tingkat, username, password, role)
            VALUES (?, ?, ?, ?, ?, ?, 'mahasiswa')
        ");
        return $stmt->execute([
            $data['nim'], 
            $data['nama'], 
            $data['kelas'], 
            $data['tingkat'],
            $data['username'], 
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
    }

    public static function updateMahasiswa($id, $data) {
        $conn = Database::getConnection();
        
        // Check if password needs to be updated
        if (!empty($data['password'])) {
            $stmt = $conn->prepare("
                UPDATE users 
                SET nama = ?, kelas = ?, tingkat = ?, username = ?, password = ? 
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['nama'], 
                $data['kelas'], 
                $data['tingkat'], 
                $data['username'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $id
            ]);
        } else {
            $stmt = $conn->prepare("
                UPDATE users 
                SET nama = ?, kelas = ?, tingkat = ?, username = ? 
                WHERE id = ?
            ");
            return $stmt->execute([
                $data['nama'], 
                $data['kelas'], 
                $data['tingkat'], 
                $data['username'], 
                $id
            ]);
        }
    }

    public static function deleteById($id) {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function validateLogin($username, $password) {
        $user = self::getByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public static function getTotalByRole($role) {
        $conn = Database::getConnection();
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE role = ?");
        $stmt->execute([$role]);
        return $stmt->fetchColumn();
    }

    public static function updatePassword($id, $newPassword) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    return $stmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $id]);
    }

    public static function insertUser($data) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO users (nim, nama, kelas, tingkat, username, password, role) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([
        $data['nim'], $data['nama'], $data['kelas'], $data['tingkat'],
        $data['username'], $data['password'], $data['role']
    ]);
}

}
?>
