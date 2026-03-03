# Shared Hosting Deployment Guide
## library.msit.com.ng (PHP 8.3)

### Prerequisites
- cPanel access
- MySQL database created
- SSH access (optional but recommended)

---

## Step 1: Prepare Database

**Using SQLite** - No database setup required! The database file will be created automatically.

Skip to Step 2.

---

## Step 2: Upload Files

### Option A: Via SSH (Recommended)
```bash
ssh your-username@library.msit.com.ng
cd public_html
git clone https://github.com/Ibagwai/elibrary-api.git api
cd api
```

### Option B: Via cPanel File Manager
1. Download: https://github.com/Ibagwai/elibrary-api/archive/refs/heads/main.zip
2. Upload to `public_html/api`
3. Extract files

---

## Step 3: Install Dependencies

### If Composer is available in cPanel:
```bash
cd public_html/api
composer install --optimize-autoloader --no-dev
```

### If no Composer access:
1. Run locally: `composer install --optimize-autoloader --no-dev`
2. Upload the entire `vendor` folder via FTP

---

## Step 4: Configure Environment

```bash
cd public_html/api
cp .env.shared-hosting .env
nano .env  # or edit via cPanel File Manager
```

Update these values in `.env`:
```env
APP_URL=https://library.msit.com.ng

SANCTUM_STATEFUL_DOMAINS=your-vercel-app.vercel.app
CORS_ALLOWED_ORIGINS=https://your-vercel-app.vercel.app
```

**Note:** Database is SQLite - no credentials needed!

Generate app key:
```bash
php artisan key:generate
```

---

## Step 5: Create Database & Run Migrations

```bash
# Create SQLite database file
touch database/database.sqlite

# Run migrations
php artisan migrate --seed --force
```

This creates:
- Admin: admin@k7library.com / password
- Faculty: faculty@k7library.com / password
- Student: student@k7library.com / password

---

## Step 6: Set Permissions

```bash
chmod -R 755 storage bootstrap/cache
chown -R your-user:your-user storage bootstrap/cache
```

Or via cPanel File Manager:
- Right-click `storage` → Change Permissions → 755
- Right-click `bootstrap/cache` → Change Permissions → 755

---

## Step 7: Configure Web Root

### Option A: Point domain to /public
In cPanel → Domains → library.msit.com.ng
- Document Root: `/home/username/public_html/api/public`

### Option B: Create .htaccess redirect
If you can't change document root, create `.htaccess` in `public_html`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ api/public/$1 [L]
</IfModule>
```

---

## Step 8: Test API

Visit: https://library.msit.com.ng/api/health

Should return:
```json
{
  "status": "ok",
  "timestamp": "2026-03-03T16:00:00.000000Z"
}
```

---

## Step 9: Update Vercel Frontend

In Vercel dashboard:
1. Go to your project settings
2. Environment Variables
3. Update: `NEXT_PUBLIC_API_URL` = `https://library.msit.com.ng/api`
4. Redeploy

---

## Step 10: Configure CORS

After Vercel deployment, update `.env`:
```env
SANCTUM_STATEFUL_DOMAINS=your-actual-vercel-url.vercel.app
CORS_ALLOWED_ORIGINS=https://your-actual-vercel-url.vercel.app
```

Then:
```bash
php artisan config:cache
```

---

## Troubleshooting

### 500 Error
- Check storage permissions: `chmod -R 755 storage`
- Check `.env` file exists and is configured
- Check error logs in cPanel

### Database Connection Error
- SQLite database file should be at `database/database.sqlite`
- Check file permissions: `chmod 664 database/database.sqlite`
- Check DB_CONNECTION=sqlite in `.env`

### CORS Error
- Update `CORS_ALLOWED_ORIGINS` in `.env`
- Run `php artisan config:cache`
- Clear browser cache

### File Upload Issues
- Check `storage/app/public` permissions
- Run: `php artisan storage:link`

---

## Maintenance

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Update Code
```bash
cd public_html/api
git pull origin main
composer install --no-dev
php artisan migrate --force
php artisan config:cache
```

---

## Security Checklist

- ✅ APP_DEBUG=false in production
- ✅ APP_KEY generated
- ✅ HTTPS enabled
- ✅ storage/ not publicly accessible
- ✅ .env not publicly accessible
- ✅ database/database.sqlite not publicly accessible

---

## Support

API Endpoints: https://library.msit.com.ng/api/documentation
Health Check: https://library.msit.com.ng/api/health
