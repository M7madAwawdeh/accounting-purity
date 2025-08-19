<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreateBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:create {--keep=30 : Number of backups to keep}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a database backup and clean old backups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup...');

        try {
            // Create backup
            $filename = $this->createBackup();
            
            if ($filename) {
                $this->info("Backup created successfully: {$filename}");
                
                // Clean old backups
                $this->cleanOldBackups();
                
                $this->info('Backup process completed successfully!');
                return 0;
            } else {
                $this->error('Failed to create backup');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Error creating backup: ' . $e->getMessage());
            return 1;
        }
    }

    private function createBackup()
    {
        $filename = 'backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';
        $backupPath = storage_path('app/backups/' . $filename);
        
        // Create backups directory if it doesn't exist
        if (!File::exists(storage_path('app/backups'))) {
            File::makeDirectory(storage_path('app/backups'), 0755, true);
        }

        // Get database configuration
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        // Check if mysqldump is available
        exec('which mysqldump', $output, $returnCode);
        if ($returnCode !== 0) {
            // Try alternative paths for Windows
            $mysqldumpPath = 'mysqldump';
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Try common Windows MySQL paths
                $possiblePaths = [
                    'C:\xampp\mysql\bin\mysqldump.exe',
                    'C:\wamp\bin\mysql\mysql5.7.36\bin\mysqldump.exe',
                    'C:\wamp64\bin\mysql\mysql8.0.31\bin\mysqldump.exe',
                    'mysqldump.exe'
                ];
                
                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $mysqldumpPath = $path;
                        break;
                    }
                }
            }
        } else {
            $mysqldumpPath = 'mysqldump';
        }

        // Create backup command with authentication fix for Windows
        $command = "{$mysqldumpPath} --host={$host} --user={$username}";
        if ($password) {
            $command .= " --password={$password}";
        }
        $command .= " --default-auth=mysql_native_password --single-transaction --routines --triggers {$database} > {$backupPath} 2>&1";

        // Execute backup
        exec($command, $output, $returnCode);

        if ($returnCode === 0 && File::exists($backupPath) && File::size($backupPath) > 0) {
            return $filename;
        }

        // Try alternative method if mysqldump fails
        $this->line('mysqldump failed, trying alternative method...');
        $alternativeResult = $this->createBackupAlternative($filename, $backupPath);
        if ($alternativeResult) {
            $this->line('Alternative backup method succeeded');
            return $filename;
        }

        return false;
    }

    private function cleanOldBackups()
    {
        $keepCount = max((int) $this->option('keep'), 3); // Ensure at least 3 backups are kept
        $backupsPath = storage_path('app/backups');
        
        if (!File::exists($backupsPath)) {
            return;
        }

        $files = File::files($backupsPath);
        $sqlFiles = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $sqlFiles[] = [
                    'path' => $file,
                    'modified' => File::lastModified($file)
                ];
            }
        }

        // Sort by modification time (newest first)
        usort($sqlFiles, function ($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        // Keep the newest backups and delete the rest
        if (count($sqlFiles) > $keepCount) {
            $toDelete = array_slice($sqlFiles, $keepCount);
            
            foreach ($toDelete as $file) {
                File::delete($file['path']);
                $this->line('Deleted old backup: ' . basename($file['path']));
            }
        }
    }

    private function createBackupAlternative($filename, $backupPath)
    {
        try {
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $backupContent = '';
            
            // Add header
            $backupContent .= "-- Database Backup\n";
            $backupContent .= "-- Generated: " . Carbon::now()->format('Y-m-d H:i:s') . "\n";
            $backupContent .= "-- Database: " . config('database.connections.mysql.database') . "\n\n";
            
            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                
                // Get table structure
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
                $backupContent .= "\n-- Table structure for table `{$tableName}`\n";
                $backupContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $backupContent .= $createTable->{'Create Table'} . ";\n\n";
                
                // Get table data
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $backupContent .= "-- Data for table `{$tableName}`\n";
                    $backupContent .= "INSERT INTO `{$tableName}` VALUES\n";
                    
                    $insertValues = [];
                    foreach ($rows as $row) {
                        $values = [];
                        foreach ((array) $row as $value) {
                            if ($value === null) {
                                $values[] = 'NULL';
                            } else {
                                $values[] = "'" . addslashes($value) . "'";
                            }
                        }
                        $insertValues[] = "(" . implode(', ', $values) . ")";
                    }
                    
                    $backupContent .= implode(",\n", $insertValues) . ";\n\n";
                }
            }
            
            // Write backup file
            File::put($backupPath, $backupContent);
            
            return File::exists($backupPath) && File::size($backupPath) > 0;
            
        } catch (\Exception $e) {
            return false;
        }
    }
}
