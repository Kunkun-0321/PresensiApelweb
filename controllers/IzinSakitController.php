<?php
// =============================================
// IzinSakitController: Fixed with proper integration
// =============================================

session_start();
require_once __DIR__ . '/../models/IzinSakit.php';
require_once __DIR__ . '/../models/User.php';

// Handle file upload
function handleFileUpload($file) {
    $upload_dir = __DIR__ . '/../uploads/bukti_izin/';
    
    // Create directory if not exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_types)) {
        throw new Exception('Tipe file tidak diizinkan. Gunakan PDF, JPG, atau PNG.');
    }
    
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        throw new Exception('Ukuran file terlalu besar. Maksimal 5MB.');
    }
    
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    } else {
        throw new Exception('Gagal mengupload file.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        if ($action === 'ajukan' && $_SESSION['user']['role'] === 'mahasiswa') {
            // Handle pengajuan izin/sakit
            $file_name = '';
            
            if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
                $file_name = handleFileUpload($_FILES['bukti']);
            }
            
            $data = [
                'user_id'     => $_SESSION['user']['id'],
                'tanggal'     => $_POST['tanggal'],
                'jenis'       => $_POST['jenis'],
                'alasan'      => $_POST['alasan'] ?? '',
                'file_bukti'  => $file_name
            ];
            
            if (!IzinSakit::sudahAjukanHariIni($data['user_id'], $data['tanggal'], $data['jenis'])) {
                if (IzinSakit::ajukan($data)) {
                    header('Location: ../views/mahasiswa/dashboard.php?success=izin_diajukan');
                } else {
                    throw new Exception('Gagal menyimpan pengajuan');
                }
            } else {
                header('Location: ../views/mahasiswa/ajukan_izin.php?error=sudah_ajukan');
            }
            
        } elseif ($action === 'verifikasi' && $_SESSION['user']['role'] === 'admin') {
            // Handle verifikasi oleh admin
            $id = $_POST['id'];
            $status = $_POST['status'];
            
            if (IzinSakit::verifikasi($id, $status, $_SESSION['user']['id'])) {
                header('Location: ../views/admin/verifikasi_izin.php?success=verifikasi_berhasil');
            } else {
                throw new Exception('Gagal memverifikasi pengajuan');
            }
            
        } else {
            throw new Exception('Action tidak valid atau tidak memiliki akses');
        }
        
    } catch (Exception $e) {
        $redirect_url = $_SESSION['user']['role'] === 'mahasiswa' 
            ? '../views/mahasiswa/ajukan_izin.php' 
            : '../views/admin/verifikasi_izin.php';
        header('Location: ' . $redirect_url . '?error=' . urlencode($e->getMessage()));
    }
    
    exit;
}
?>
