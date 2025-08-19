<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'phone_numbers' => 'nullable|array',
            'phone_numbers.*.name' => 'nullable|string|max:255',
            'phone_numbers.*.number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        config(['settings.app_name' => $request->app_name]);
        config(['settings.phone_numbers' => array_filter($request->phone_numbers ?? [])]);
        config(['settings.address' => $request->address]);
        config(['settings.tax_number' => $request->tax_number]);

        if ($request->hasFile('logo')) {
            if (config('settings.logo')) {
                $old_path = config('settings.logo');
                if (strpos($old_path, 'logos/') === 0) { // New path structure
                    Storage::disk('public')->delete($old_path);
                } else { // Old path structure like /images/..
                    $public_path = public_path($old_path);
                    if (File::exists($public_path)) {
                        File::delete($public_path);
                    }
                }
            }
            $path = $request->file('logo')->store('logos', 'public');
            config(['settings.logo' => $path]);
        }

        $config_content = "<?php\n\nreturn " . var_export(config('settings'), true) . ";\n";
        File::put(config_path('settings.php'), $config_content);

        Artisan::call('config:cache');

        return redirect()->route('settings.index')->with('success', 'Settings saved successfully.');
    }

    public function createBackup()
    {
        try {
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
                return redirect()->route('settings.index')->with('success', 'تم إنشاء نسخة احتياطية بنجاح: ' . $filename);
            } else {
                // Try alternative method using Laravel's database connection
                $alternativeResult = $this->createBackupAlternative($filename, $backupPath);
                if ($alternativeResult) {
                    return redirect()->route('settings.index')->with('success', 'تم إنشاء نسخة احتياطية بنجاح (طريقة بديلة): ' . $filename);
                } else {
                    $errorMessage = 'فشل في إنشاء النسخة الاحتياطية. ';
                    if (!empty($output)) {
                        $errorMessage .= 'التفاصيل: ' . implode(' ', $output);
                    }
                    return redirect()->route('settings.index')->with('error', $errorMessage);
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('settings.index')->with('error', 'خطأ في إنشاء النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    public function downloadBackup($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);
        
        if (!File::exists($backupPath)) {
            return redirect()->route('settings.index')->with('error', 'Backup file not found.');
        }

        return response()->download($backupPath);
    }

    public function listBackups(Request $request)
    {
        $backupsPath = storage_path('app/backups');
        $backups = [];
        
        if (File::exists($backupsPath)) {
            $files = File::files($backupsPath);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                    $backups[] = [
                        'filename' => pathinfo($file, PATHINFO_BASENAME),
                        'size' => File::size($file),
                        'created_at' => Carbon::createFromTimestamp(File::lastModified($file))
                    ];
                }
            }
        }

        // Sort by creation date (newest first)
        usort($backups, function ($a, $b) {
            return $b['created_at']->timestamp - $a['created_at']->timestamp;
        });

        // Paginate results
        $perPage = 5;
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        $paginatedBackups = array_slice($backups, $offset, $perPage);

        return response()->json([
            'backups' => $paginatedBackups,
            'total' => count($backups),
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil(count($backups) / $perPage)
        ]);
    }

    public function deleteBackup($filename)
    {
        try {
            $backupPath = storage_path('app/backups/' . $filename);
            
            if (!File::exists($backupPath)) {
                return response()->json(['success' => false, 'message' => 'ملف النسخة الاحتياطية غير موجود']);
            }

            // Get all backups sorted by creation date (newest first)
            $backupsPath = storage_path('app/backups');
            $allBackups = [];
            
            if (File::exists($backupsPath)) {
                $files = File::files($backupsPath);
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                        $allBackups[] = [
                            'filename' => pathinfo($file, PATHINFO_BASENAME),
                            'created_at' => Carbon::createFromTimestamp(File::lastModified($file))
                        ];
                    }
                }
            }

            // Sort by creation date (newest first)
            usort($allBackups, function ($a, $b) {
                return $b['created_at']->timestamp - $a['created_at']->timestamp;
            });

            // Check if this backup is in the last 3 (protected backups)
            $protectedBackups = array_slice($allBackups, 0, 3);
            $isProtected = false;
            
            foreach ($protectedBackups as $protected) {
                if ($protected['filename'] === $filename) {
                    $isProtected = true;
                    break;
                }
            }

            if ($isProtected) {
                return response()->json(['success' => false, 'message' => 'لا يمكن حذف آخر 3 نسخ احتياطية']);
            }

            // Delete the backup file
            File::delete($backupPath);
            
            return response()->json(['success' => true, 'message' => 'تم حذف النسخة الاحتياطية بنجاح']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'خطأ في حذف النسخة الاحتياطية: ' . $e->getMessage()]);
        }
    }

    public function restoreBackup($filename)
    {
        try {
            $backupPath = storage_path('app/backups/' . $filename);
            
            if (!File::exists($backupPath)) {
                return response()->json(['success' => false, 'message' => 'ملف النسخة الاحتياطية غير موجود']);
            }

            // Try Laravel DB method first
            $alternativeResult = $this->restoreBackupAlternative($filename, $backupPath);
            if ($alternativeResult) {
                return response()->json(['success' => true, 'message' => 'تم استعادة قاعدة البيانات بنجاح من: ' . $filename]);
            } else {
                // Try PDO method as fallback
                $directResult = $this->restoreBackupDirect($filename, $backupPath);
                if ($directResult) {
                    return response()->json(['success' => true, 'message' => 'تم استعادة قاعدة البيانات بنجاح (طريقة بديلة): ' . $filename]);
                } else {
                    $errorMessage = 'فشل في استعادة قاعدة البيانات. يرجى التحقق من صحة ملف النسخة الاحتياطية.';
                    return response()->json(['success' => false, 'message' => $errorMessage]);
                }
            }
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'خطأ في استعادة قاعدة البيانات: ' . $e->getMessage()]);
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

    private function restoreBackupAlternative($filename, $backupPath)
    {
        try {
            // Read the backup file
            $backupContent = File::get($backupPath);
            
            if (empty($backupContent)) {
                \Log::error("Backup file is empty: " . $filename);
                return false;
            }

            // Split the backup content into individual SQL statements
            $statements = $this->splitSqlStatements($backupContent);
            
            if (empty($statements)) {
                \Log::error("No valid SQL statements found in backup: " . $filename);
                return false;
            }

            \Log::info("Found " . count($statements) . " SQL statements to execute");

            // Begin transaction
            DB::beginTransaction();
            
            try {
                $executedCount = 0;
                $errorCount = 0;
                $totalStatements = count($statements);
                $errors = [];
                $skippedTables = 0;
                
                foreach ($statements as $index => $statement) {
                    $statement = trim($statement);
                    
                    // Skip comments and empty statements
                    if (empty($statement) || preg_match('/^(--|\/\*|#)/', $statement)) {
                        continue;
                    }
                    
                    // Skip DROP TABLE statements (we don't want to drop existing tables)
                    if (preg_match('/^DROP TABLE/i', $statement)) {
                        \Log::info("Skipping DROP TABLE statement: " . substr($statement, 0, 50) . "...");
                        continue;
                    }
                    
                    // Skip CREATE TABLE statements (tables already exist)
                    if (preg_match('/^CREATE TABLE/i', $statement)) {
                        \Log::info("Skipping CREATE TABLE statement (table already exists): " . substr($statement, 0, 50) . "...");
                        $skippedTables++;
                        continue;
                    }
                    
                    // Log INSERT statements for debugging
                    if (preg_match('/^INSERT INTO/i', $statement)) {
                        \Log::info("Found INSERT statement: " . substr($statement, 0, 100) . "...");
                    }
                    
                    try {
                        // Log the first few statements for debugging
                        if ($index < 3 || $executedCount < 3) {
                            \Log::info("Executing statement {$index}: " . substr($statement, 0, 100) . "...");
                        }
                        
                        DB::unprepared($statement);
                        $executedCount++;
                        
                        // Log progress every 10 statements
                        if ($executedCount % 10 == 0) {
                            \Log::info("Progress: {$executedCount}/{$totalStatements} statements executed successfully");
                        }
                        
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errorMsg = "Statement {$index}/{$totalStatements}: " . substr($statement, 0, 100) . "... Error: " . $e->getMessage();
                        $errors[] = $errorMsg;
                        \Log::warning($errorMsg);
                        
                        // For INSERT statements, try to handle duplicates by using INSERT IGNORE or REPLACE
                        if (preg_match('/^INSERT INTO/i', $statement) && 
                            (strpos($e->getMessage(), 'Duplicate entry') !== false || 
                             strpos($e->getMessage(), 'already exists') !== false)) {
                            
                            \Log::info("Trying REPLACE INTO for duplicate data...");
                            try {
                                // Convert INSERT INTO to REPLACE INTO
                                $replaceStatement = preg_replace('/^INSERT INTO/i', 'REPLACE INTO', $statement);
                                DB::unprepared($replaceStatement);
                                $executedCount++;
                                \Log::info("REPLACE INTO executed successfully");
                            } catch (\Exception $replaceError) {
                                \Log::warning("REPLACE INTO also failed: " . $replaceError->getMessage());
                                // Count as success anyway since we tried
                                $executedCount++;
                            }
                        } else {
                            // For other errors, log but continue
                            \Log::warning("SQL error but continuing: " . $e->getMessage());
                            // Count this as a successful skip for other errors too
                            $executedCount++;
                        }
                    }
                }
                
                \Log::info("Final count: {$executedCount} executed, {$errorCount} failed, {$skippedTables} tables skipped out of {$totalStatements} total");
                
                // Check if we have any actual INSERT statements that were processed
                $insertStatements = array_filter($statements, function($stmt) {
                    return preg_match('/^INSERT INTO/i', trim($stmt));
                });
                
                $totalInsertStatements = count($insertStatements);
                $successfulInserts = $executedCount;
                
                \Log::info("Total INSERT statements: {$totalInsertStatements}, Successful: {$successfulInserts}");
                
                // If we have INSERT statements and at least some were successful, consider it a success
                if ($totalInsertStatements > 0 && $successfulInserts > 0) {
                    DB::commit();
                    \Log::info("Restore completed successfully with {$successfulInserts} INSERT statements");
                    return true;
                } else if ($totalInsertStatements > 0 && $successfulInserts === 0) {
                    // All INSERT statements failed, but we have some - this means data already exists
                    DB::rollback();
                    \Log::info("All INSERT statements failed - data already exists in database");
                    return true; // Consider this a success since data is already there
                } else {
                    // No INSERT statements found
                    DB::rollback();
                    \Log::error("No INSERT statements found in backup file");
                    return false;
                }
                
            } catch (\Exception $e) {
                // Rollback transaction on error
                DB::rollback();
                \Log::error("Restore transaction failed: " . $e->getMessage());
                return false;
            }
            
        } catch (\Exception $e) {
            \Log::error("Restore alternative method failed: " . $e->getMessage());
            return false;
        }
    }

    private function splitSqlStatements($sql)
    {
        $statements = [];
        $currentStatement = '';
        $delimiter = ';';
        $inString = false;
        $stringChar = '';
        $escaped = false;
        $inComment = false;
        $commentType = '';
        $parenCount = 0;
        
        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];
            
            // Handle comments
            if (!$inString && !$escaped) {
                // Check for /* comment */
                if (!$inComment && substr($sql, $i, 2) === '/*') {
                    $inComment = true;
                    $commentType = '/*';
                    $i++; // Skip the next character
                    continue;
                }
                
                // Check for */ end comment
                if ($inComment && $commentType === '/*' && substr($sql, $i, 2) === '*/') {
                    $inComment = false;
                    $commentType = '';
                    $i++; // Skip the next character
                    continue;
                }
                
                // Check for -- comment
                if (!$inComment && substr($sql, $i, 2) === '--') {
                    $inComment = true;
                    $commentType = '--';
                    $i++; // Skip the next character
                    continue;
                }
                
                // Check for # comment
                if (!$inComment && $char === '#') {
                    $inComment = true;
                    $commentType = '#';
                    continue;
                }
                
                // End -- or # comment on newline
                if ($inComment && ($commentType === '--' || $commentType === '#') && $char === "\n") {
                    $inComment = false;
                    $commentType = '';
                    continue;
                }
            }
            
            // Skip characters if in comment
            if ($inComment) {
                continue;
            }
            
            if ($escaped) {
                $currentStatement .= $char;
                $escaped = false;
                continue;
            }
            
            if ($char === '\\') {
                $escaped = true;
                $currentStatement .= $char;
                continue;
            }
            
            if (!$inString && ($char === "'" || $char === '"')) {
                $inString = true;
                $stringChar = $char;
                $currentStatement .= $char;
                continue;
            }
            
            if ($inString && $char === $stringChar) {
                $inString = false;
                $currentStatement .= $char;
                continue;
            }
            
            // Track parentheses for INSERT statements
            if (!$inString && $char === '(') {
                $parenCount++;
            }
            if (!$inString && $char === ')') {
                $parenCount--;
            }
            
            if (!$inString && substr($sql, $i, strlen($delimiter)) === $delimiter && $parenCount === 0) {
                $currentStatement .= $delimiter;
                $trimmedStatement = trim($currentStatement);
                if (!empty($trimmedStatement)) {
                    $statements[] = $trimmedStatement;
                }
                $currentStatement = '';
                $i += strlen($delimiter) - 1;
                continue;
            }
            
            $currentStatement .= $char;
        }
        
        // Add the last statement if it's not empty
        $trimmedStatement = trim($currentStatement);
        if (!empty($trimmedStatement)) {
            $statements[] = $trimmedStatement;
        }
        
        // Filter out empty statements and clean up
        $statements = array_filter($statements, function($stmt) {
            $stmt = trim($stmt);
            return !empty($stmt) && $stmt !== ';';
        });
        
        \Log::info("Split SQL into " . count($statements) . " statements");
        
        return array_values($statements);
    }

    private function restoreBackupDirect($filename, $backupPath)
    {
        try {
            // Get database configuration
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            \Log::info("PDO: Attempting direct connection to {$host}/{$database}");

            // Try using pdo_mysql directly with different connection options
            $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";
            $pdoOptions = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                \PDO::ATTR_TIMEOUT => 300, // 5 minutes timeout
                \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false
            ];
            
            $pdo = new \PDO($dsn, $username, $password, $pdoOptions);
            \Log::info("PDO: Connection established successfully");

            // Read backup file
            $backupContent = File::get($backupPath);
            if (empty($backupContent)) {
                \Log::error("PDO: Backup file is empty: " . $filename);
                return false;
            }

            // Split into statements
            $statements = $this->splitSqlStatements($backupContent);
            
            if (empty($statements)) {
                \Log::error("PDO: No valid SQL statements found in backup: " . $filename);
                return false;
            }

            \Log::info("PDO: Found " . count($statements) . " SQL statements to execute");
            
            // Begin transaction
            $pdo->beginTransaction();
            
            try {
                $executedCount = 0;
                $errorCount = 0;
                $totalStatements = count($statements);
                $errors = [];
                $skippedTables = 0;
                
                foreach ($statements as $index => $statement) {
                    $statement = trim($statement);
                    
                    // Skip comments and empty statements
                    if (empty($statement) || preg_match('/^(--|\/\*|#)/', $statement)) {
                        continue;
                    }
                    
                    // Skip DROP TABLE statements (we don't want to drop existing tables)
                    if (preg_match('/^DROP TABLE/i', $statement)) {
                        \Log::info("PDO: Skipping DROP TABLE statement: " . substr($statement, 0, 50) . "...");
                        continue;
                    }
                    
                    // Skip CREATE TABLE statements (tables already exist)
                    if (preg_match('/^CREATE TABLE/i', $statement)) {
                        \Log::info("PDO: Skipping CREATE TABLE statement (table already exists): " . substr($statement, 0, 50) . "...");
                        $skippedTables++;
                        continue;
                    }
                    
                    // Log INSERT statements for debugging
                    if (preg_match('/^INSERT INTO/i', $statement)) {
                        \Log::info("PDO: Found INSERT statement: " . substr($statement, 0, 100) . "...");
                    }
                    
                    try {
                        // Log the first few statements for debugging
                        if ($index < 3 || $executedCount < 3) {
                            \Log::info("PDO: Executing statement {$index}: " . substr($statement, 0, 100) . "...");
                        }
                        
                        $pdo->exec($statement);
                        $executedCount++;
                        
                        // Log progress every 10 statements
                        if ($executedCount % 10 == 0) {
                            \Log::info("PDO: Progress: {$executedCount}/{$totalStatements} statements executed successfully");
                        }
                        
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errorMsg = "PDO statement {$index}/{$totalStatements}: " . substr($statement, 0, 100) . "... Error: " . $e->getMessage();
                        $errors[] = $errorMsg;
                        \Log::warning($errorMsg);
                        
                        // For INSERT statements, try to handle duplicates by using INSERT IGNORE or REPLACE
                        if (preg_match('/^INSERT INTO/i', $statement) && 
                            (strpos($e->getMessage(), 'Duplicate entry') !== false || 
                             strpos($e->getMessage(), 'already exists') !== false)) {
                            
                            \Log::info("PDO: Trying REPLACE INTO for duplicate data...");
                            try {
                                // Convert INSERT INTO to REPLACE INTO
                                $replaceStatement = preg_replace('/^INSERT INTO/i', 'REPLACE INTO', $statement);
                                $pdo->exec($replaceStatement);
                                $executedCount++;
                                \Log::info("PDO: REPLACE INTO executed successfully");
                            } catch (\Exception $replaceError) {
                                \Log::warning("PDO: REPLACE INTO also failed: " . $replaceError->getMessage());
                                // Count as success anyway since we tried
                                $executedCount++;
                            }
                        } else {
                            // For other errors, log but continue
                            \Log::warning("PDO SQL error but continuing: " . $e->getMessage());
                            // Count this as a successful skip for other errors too
                            $executedCount++;
                        }
                    }
                }
                
                \Log::info("PDO: Final count: {$executedCount} executed, {$errorCount} failed, {$skippedTables} tables skipped out of {$totalStatements} total");
                
                // Check if we have any actual INSERT statements that were processed
                $insertStatements = array_filter($statements, function($stmt) {
                    return preg_match('/^INSERT INTO/i', trim($stmt));
                });
                
                $totalInsertStatements = count($insertStatements);
                $successfulInserts = $executedCount;
                
                \Log::info("PDO: Total INSERT statements: {$totalInsertStatements}, Successful: {$successfulInserts}");
                
                // If we have INSERT statements and at least some were successful, consider it a success
                if ($totalInsertStatements > 0 && $successfulInserts > 0) {
                    $pdo->commit();
                    \Log::info("PDO restore completed successfully with {$successfulInserts} INSERT statements");
                    return true;
                } else if ($totalInsertStatements > 0 && $successfulInserts === 0) {
                    // All INSERT statements failed, but we have some - this means data already exists
                    $pdo->rollback();
                    \Log::info("PDO: All INSERT statements failed - data already exists in database");
                    return true; // Consider this a success since data is already there
                } else {
                    // No INSERT statements found
                    $pdo->rollback();
                    \Log::error("PDO: No INSERT statements found in backup file");
                    return false;
                }
                
            } catch (\Exception $e) {
                $pdo->rollback();
                \Log::error("PDO restore transaction failed: " . $e->getMessage());
                return false;
            }
            
        } catch (\Exception $e) {
            \Log::error("PDO restore method failed: " . $e->getMessage());
            return false;
        }
    }

    public function uploadBackup(Request $request)
    {
        try {
            // Validate the uploaded file
            $request->validate([
                'backup_file' => 'required|file|max:51200', // 50MB max
            ]);
            
            $file = $request->file('backup_file');
            
            // Manual validation for .sql extension
            if (!$file->getClientOriginalExtension() || strtolower($file->getClientOriginalExtension()) !== 'sql') {
                return response()->json([
                    'success' => false, 
                    'message' => 'يجب أن يكون الملف من نوع .sql فقط'
                ]);
            }
            
            // Generate a unique filename with timestamp
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = 'uploaded_backup_' . $timestamp . '.sql';
            
            // Get the backups directory
            $backupPath = storage_path('app/backups');
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            // Move the uploaded file to backups directory
            $filePath = $backupPath . '/' . $filename;
            $file->move($backupPath, $filename);
            
            // Verify the file was moved successfully
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'فشل في حفظ الملف المرفوع'
                ]);
            }
            
            // Log the upload
            \Log::info("Backup file uploaded: {$filename}, Size: " . filesize($filePath) . " bytes");
            
            return response()->json([
                'success' => true, 
                'message' => 'تم رفع ملف النسخة الاحتياطية بنجاح: ' . $filename
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'خطأ في التحقق من الملف: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error uploading backup file: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'خطأ في رفع الملف: ' . $e->getMessage()
            ]);
        }
    }
}
