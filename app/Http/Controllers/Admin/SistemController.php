<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SistemController extends Controller
{
    public function index()
    {
        $dbSize = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'DB Size in MB' FROM information_schema.tables WHERE table_schema = DATABASE()")[0];
        
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_size' => $dbSize->{'DB Size in MB'} . ' MB',
            'storage_used' => $this->getStorageSize(),
            'last_backup' => $this->getLastBackupDate()
        ];
        
        return view('admin.laporan-sistem.sistem', compact('systemInfo'));
    }

    public function backup()
    {
        try {
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $path = storage_path('app/backups/' . $filename);
            
            // Create backup directory if not exists
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }
            
            // Simple backup command (adjust based on your system)
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                config('database.connections.mysql.host'),
                config('database.connections.mysql.database'),
                $path
            );
            
            exec($command);
            
            return redirect()->back()->with('success', 'Backup berhasil dibuat: ' . $filename);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    private function getStorageSize()
    {
        $size = 0;
        $path = storage_path('app');
        
        if (is_dir($path)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
                $size += $file->getSize();
            }
        }
        
        return round($size / 1024 / 1024, 2) . ' MB';
    }

    private function getLastBackupDate()
    {
        $backupPath = storage_path('app/backups');
        
        if (!is_dir($backupPath)) {
            return 'Belum ada backup';
        }
        
        $files = glob($backupPath . '/*.sql');
        
        if (empty($files)) {
            return 'Belum ada backup';
        }
        
        $latestFile = max($files);
        return date('d/m/Y H:i:s', filemtime($latestFile));
    }
}