# Simple Custom Post Type

Modern and professional WordPress plugin to create and manage Custom Post Types with an intuitive interface.

## 📋 Description

**Simple Custom Post Type** is a WordPress plugin that allows you to easily create and manage Custom Post Types (CPT) with all possible fields. It offers a modern interface, robust architecture, and respects all WordPress standards.

## ✨ Features

- 🎨 **Modern Tabbed Interface** - Design inspired by CPT UI with intuitive navigation
- 🔧 **Complete Configuration** - 8 organized tabs (General, Post Type, Labels, Options, Visibility, Permalinks, Capabilities, Rest API)
- 🛡️ **Enhanced Security** - Strict validation and sanitization
- 📊 **Field Management** - Support for all field types
- 🔍 **Logging System** - Complete action traceability
- 🌐 **Multilingual** - Translation ready (French 🇫🇷 / English 🇬🇧)
- ⚡ **Optimized Performance** - Cache and lazy loading
- 📱 **Responsive** - Mobile and tablet compatible (adaptive grid)
- 🔌 **REST API** - CPT exposure via REST API
- 📝 **Complete Documentation** - Documented and tested code

## 🚀 Installation

1. Download the plugin
2. Extract to `/wp-content/plugins/`
3. Activate from WordPress admin
4. Access the "Simple CPT" menu

### Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher

## 📁 Plugin Structure

```
simple-custom-post-type/
├── simple-custom-post-type.php # Main file
├── uninstall.php               # Uninstallation
├── README.md                   # Documentation
├── CHANGELOG.md                # Version history
├── includes/                   # PHP Classes
│   ├── Core/                   # Plugin core
│   │   ├── Plugin.php          # Main class (Singleton)
│   │   ├── Activator.php       # Activation
│   │   └── Deactivator.php     # Deactivation
│   ├── PostTypes/              # CPT Management
│   │   └── Manager.php         # CPT Manager
│   ├── Admin/                  # Admin interface
│   │   ├── AdminMenu.php       # Admin menus
│   │   └── Assets.php          # Assets loading
│   └── Utils/                  # Utilities
│       ├── Logger.php          # Logging system
│       ├── Validator.php       # Data validation
│       └── Cache.php           # Cache management
├── assets/                     # Front-end resources
│   ├── css/
│   │   └── admin.css           # Admin styles
│   └── js/
│       └── admin.js            # Admin scripts
├── languages/                  # Translations
│   └── simple-custom-post-type.pot
└── tests/                      # Unit tests
    └── phpunit.xml
```

## 🎯 Architecture

### Design Principles

1. **Separation of Concerns** - Each class has a single responsibility
2. **Singleton Pattern** - Main class as singleton
3. **PSR-4 Autoloading** - Automatic class loading
4. **Namespace** - Organization with `SimpleCustomPostType\`
5. **WordPress Hooks** - Use of actions and filters
6. **Security** - Validation, sanitization, nonces, capabilities

### Main Components

#### Core\Plugin
Main plugin class (Singleton). Handles initialization and coordinates other components.

#### PostTypes\Manager
Custom Post Types manager. Registers, saves, deletes, and retrieves CPTs.

#### Utils\Logger
Logging system with different levels (debug, info, warning, error, critical).

#### Utils\Validator
Validation and sanitization of incoming data.

#### Admin\AdminMenu
Administration menu management.

## 💻 Usage

### Create a Custom Post Type

1. Go to **Simple CPT > Add New**
2. Navigate between tabs to configure:
   - **General**: Function Name, Text Domain
   - **Post Type**: Slug (max 20 characters), Names (plural/singular), Description
   - **Labels**: Customize all labels (Menu Name, Add New, Edit Item, etc.)
   - **Options**: Supports (title, editor, image, etc.), Archives, Export
   - **Visibility**: Visibility in admin, menu, navigation
   - **Permalinks**: URL configuration
   - **Capabilities**: Base permissions
   - **Rest API**: REST API exposure
3. Click "Create Post Type"
4. Automatic redirect to list with success message

### Configuration

Access **Simple CPT > Settings** to configure:

- **Logs**: Enable/disable log recording
- **Retention**: Log retention duration (days)
- **REST API**: Expose CPTs via REST API
- **Gutenberg**: Enable Gutenberg editor
- **Uninstall**: Delete data on uninstall

### View Logs

Access **Simple CPT > Logs** to view action history.

## 🔒 Security

The plugin implements multiple security layers:

- **Nonces**: AJAX request verification
- **Capabilities**: User permission verification
- **Sanitization**: Cleaning of all incoming data
- **Validation**: Data validity verification
- **Prepared Statements**: SQL injection protection
- **Escaping**: XSS protection on outputs

## 📊 Database

The plugin creates 3 tables:

### wp_scpt_post_types
Stores Custom Post Type configurations.

```sql
id, slug, config, is_active, created_at, updated_at
```

### wp_scpt_fields
Stores custom fields.

```sql
id, post_type_slug, field_key, field_config, field_order, is_active, created_at, updated_at
```

### wp_scpt_logs
Stores activity logs.

```sql
id, level, message, context, user_id, ip_address, created_at
```

## 🔌 API

### WordPress Actions

```php
// Before registering a post type
do_action('scpt_before_register_post_type', $slug, $args);

// After registering a post type
do_action('scpt_after_register_post_type', $slug, $args);

// Before saving
do_action('scpt_before_save_post_type', $data);

// After saving
do_action('scpt_after_save_post_type', $data);
```

### WordPress Filters

```php
// Modify post type arguments
add_filter('scpt_post_type_args', function($args, $config) {
    // Modify $args
    return $args;
}, 10, 2);

// Modify reserved slugs
add_filter('scpt_reserved_slugs', function($slugs) {
    $slugs[] = 'my-reserved-slug';
    return $slugs;
});
```

### REST API

Available endpoints:

- `GET /wp-json/scpt/v1/post-types` - List post types
- `POST /wp-json/scpt/v1/post-types` - Create a post type
- `GET /wp-json/scpt/v1/post-types/{slug}` - Get a post type
- `PUT /wp-json/scpt/v1/post-types/{slug}` - Update a post type
- `DELETE /wp-json/scpt/v1/post-types/{slug}` - Delete a post type

## 🧪 Tests

### Unit Tests

```bash
# Install PHPUnit
composer install

# Run tests
./vendor/bin/phpunit
```

### Manual Tests

1. Create a post type
2. Check display in admin menu
3. Create a post of this type
4. Check public display
5. Edit the post type
6. Delete the post type

## 📝 Code Standards

The plugin follows:

- **WordPress Coding Standards** - PHPCS with WordPress rules
- **PSR-4** - Class autoloading
- **PSR-12** - Code style
- **Documentation** - PHPDoc for all functions
- **Security** - OWASP Top 10

## 🔄 Versioning

The plugin uses **Semantic Versioning** (SemVer):

- **MAJOR**: Incompatible changes
- **MINOR**: New compatible features
- **PATCH**: Bug fixes

## 👨‍💻 Development

### Contributing

1. Fork the project
2. Create a branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Commit Conventions

- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation
- `style:` Formatting
- `refactor:` Refactoring
- `test:` Tests
- `chore:` Maintenance

## 📄 License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## 👤 Author

**Akrem Belkahla**
- Email: contact@infinityweb.tn
- Website: https://infinityweb.tn

## 🏢 Agency

**InfinityWeb**
- Website: https://infinityweb.tn

## 📞 Support

For any questions or issues:
- Create an issue on GitHub
- Contact support: contact@infinityweb.tn

## 🙏 Acknowledgments

- WordPress Community
- Contributors

---

Made with ❤️ by InfinityWeb
