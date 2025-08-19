# Backup System and Cron Setup

## Database Backup System

This application includes a comprehensive backup system that allows you to:

1. **Manual Backups**: Create backups through the admin interface
2. **Automated Backups**: Schedule automatic backups using cron jobs
3. **Backup Management**: View, download, and manage existing backups

## Manual Backup

To create a backup manually:

1. Go to Settings page in the admin panel
2. Click "Create Backup" button
3. The backup will be created and stored in `storage/app/backups/`

## Automated Backup Setup

### Using Artisan Command

You can run the backup command manually:

```bash
php artisan backup:create
```

### Cron Job Setup in cPanel

Follow these steps to set up automated backups:

1. **Login to cPanel**
2. **Find Cron Jobs** (usually under "Advanced" section)
3. **Add New Cron Job**
4. **Set the schedule** (recommended: daily at 2 AM)
5. **Enter the command**:

```bash
/usr/local/bin/php /home/username/public_html/artisan schedule:run
```

**Important Notes:**
- Replace `username` with your actual cPanel username
- Replace `public_html` with your actual application path if different
- The command runs Laravel's scheduler which will execute the backup command

### Cron Schedule Examples

- **Every minute**: `* * * * *`
- **Every 5 minutes**: `*/5 * * * *`
- **Every hour**: `0 * * * *`
- **Daily at 2 AM**: `0 2 * * *`
- **Weekly on Sunday at 2 AM**: `0 2 * * 0`

## Backup Configuration

### Number of Backups to Keep

By default, the system keeps the last 30 backups. You can change this:

```bash
php artisan backup:create --keep=10
```

### Backup Location

Backups are stored in: `storage/app/backups/`

### Backup Naming

Backups are named with timestamp: `backup_YYYY-MM-DD_HH-MM-SS.sql`

## Troubleshooting

### Common Issues

1. **Permission Denied**: Make sure the `storage/app/backups/` directory is writable
2. **mysqldump not found**: Ensure mysqldump is installed and accessible
3. **Database connection failed**: Check your database configuration in `.env`

### Testing Cron

To test if your cron job is working:

1. Create a test cron job that runs every minute
2. Check the Laravel logs: `storage/logs/laravel.log`
3. Verify backups are being created in the backup directory

### Manual Testing

Test the backup command manually:

```bash
php artisan backup:create --verbose
```

## Security Considerations

1. **Backup files contain sensitive data** - ensure proper file permissions
2. **Consider encrypting backups** for additional security
3. **Store backups off-site** for disaster recovery
4. **Regularly test backup restoration** to ensure backups are valid

## Restoring Backups

To restore a backup:

```bash
mysql -u username -p database_name < backup_file.sql
```

Replace:
- `username` with your database username
- `database_name` with your database name
- `backup_file.sql` with the path to your backup file 