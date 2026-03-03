# API Documentation

## Swagger UI / OpenAPI Documentation

The K7 E-Library API is fully documented using OpenAPI 3.0 (Swagger) specification.

### Accessing the Documentation

Once the Laravel server is running, access the interactive API documentation at:

```
http://localhost:8747/api/documentation
```

### Features

- **Interactive API Testing**: Test all endpoints directly from the browser
- **Authentication**: Use the "Authorize" button to add your Bearer token
- **Request/Response Examples**: See example payloads for all endpoints
- **Schema Definitions**: View all data models and their properties

### API Endpoints Documented

#### Authentication
- `POST /api/v1/auth/register` - Register new user
- `POST /api/v1/auth/login` - Login user
- `POST /api/v1/auth/logout` - Logout user (requires auth)
- `GET /api/v1/auth/me` - Get current user (requires auth)

#### Content
- `GET /api/v1/content` - List all published content (with filters)
- `GET /api/v1/content/featured` - Get featured content
- `GET /api/v1/content/{slug}` - Get content by slug

#### Search
- `GET /api/v1/search` - Search content

#### Bookmarks (Authenticated)
- `GET /api/v1/bookmarks` - Get user bookmarks
- `POST /api/v1/bookmarks` - Add bookmark
- `DELETE /api/v1/bookmarks/{contentId}` - Remove bookmark

#### Media (Authenticated)
- `POST /api/v1/media/upload` - Upload file
- `GET /api/v1/media/{id}/download` - Download file

#### Admin (Authenticated)
- `GET /api/v1/admin/dashboard/stats` - Get dashboard statistics
- `GET /api/v1/admin/content` - List all content (admin view)
- `POST /api/v1/admin/content` - Create content
- `PUT /api/v1/admin/content/{id}` - Update content
- `POST /api/v1/admin/content/{id}/publish` - Publish content
- `DELETE /api/v1/admin/content/{id}` - Delete content

### Using Authentication in Swagger UI

1. Login via `/api/v1/auth/login` endpoint
2. Copy the `token` from the response
3. Click the "Authorize" button at the top of the page
4. Enter: `Bearer YOUR_TOKEN_HERE`
5. Click "Authorize"
6. Now you can test authenticated endpoints

### Regenerating Documentation

If you make changes to the API annotations, regenerate the documentation:

```bash
php artisan l5-swagger:generate
```

### JSON Specification

The raw OpenAPI JSON specification is available at:

```
http://localhost:8747/docs/api-docs.json
```

### Development

API documentation is defined using PHPDoc annotations in the controllers:

- Base configuration: `app/Http/Controllers/Controller.php`
- Auth endpoints: `app/Http/Controllers/API/V1/AuthController.php`
- Content endpoints: `app/Http/Controllers/API/V1/ContentController.php`
- Search endpoints: `app/Http/Controllers/API/V1/SearchController.php`
- Bookmark endpoints: `app/Http/Controllers/API/V1/BookmarkController.php`
- Media endpoints: `app/Http/Controllers/API/V1/MediaController.php`
- Admin endpoints: `app/Http/Controllers/API/V1/Admin/*.php`

### Configuration

Swagger configuration is located at: `config/l5-swagger.php`

Key settings:
- Documentation route: `/api/documentation`
- JSON file location: `storage/api-docs/api-docs.json`
- Scan paths: `app/` directory

---

**Package Used**: [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger) v9.x with [swagger-php](https://github.com/zircote/swagger-php) v5.x
