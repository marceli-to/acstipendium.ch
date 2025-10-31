# AC Stipendium

Official website for AC Stipendium, a scholarship program supporting emerging artists.

## Tech Stack

- **CMS:** Statamic 5.x
- **Framework:** Laravel 10.x
- **Frontend:** Alpine.js, Tailwind CSS, Vue.js
- **Build Tool:** Vite
- **PHP:** 8.2+

## Key Features

- Bilingual content management (German/French)
- Online application system for scholarship submissions
- Exhibition archives with interactive accordion interface
- Scholarship recipient showcase
- Responsive design with custom header animations
- Content export functionality

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL/PostgreSQL (or other supported database)

## Installation

1. Clone the repository
```bash
git clone <repository-url>
cd acstipendium.ch
```

2. Install PHP dependencies
```bash
composer install
```

3. Install JavaScript dependencies
```bash
npm install
```

4. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your database in `.env`

6. Run migrations
```bash
php artisan migrate
```

7. Create a Statamic user
```bash
php please make:user
```

## Development

Start the development server:

```bash
npm run dev
```

In a separate terminal, start the Laravel server:

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Building for Production

```bash
npm run build
```

## Project Structure

```
├── app/
│   └── Http/Controllers/
│       └── Api/              # API controllers
├── content/
│   └── collections/          # Statamic collections
│       ├── pages/            # Site pages (DE/FR)
│       ├── exhibitions/      # Exhibition entries
│       └── applications/     # Scholarship applications
├── resources/
│   ├── js/
│   │   └── modules/          # Alpine.js modules
│   │       ├── accordion.js  # Exhibition accordion
│   │       └── header.js     # Animated header
│   └── views/
│       └── partials/         # Reusable Antlers templates
└── routes/
    └── api.php               # API routes
```

## Key Packages

### PHP
- `statamic/cms` - Flat-file CMS
- `livewire/livewire` - Dynamic components
- `jacksleight/statamic-bard-texstyle` - Text styling for Bard editor
- `rias/statamic-collection-groups` - Collection organization

### JavaScript
- `alpinejs` - Lightweight reactive framework
- `maska` - Input masking
- `@vitejs/plugin-vue` - Vue 3 support

## Content Management

Access the Statamic control panel at `/cp` after creating a user.

### Collections

- **Pages:** Main site pages in German and French
- **Exhibitions:** Annual exhibition archives
- **Applications:** Scholarship application submissions

## Deployment

1. Ensure environment variables are configured for production
2. Build assets: `npm run build`
3. Optimize application: `php artisan optimize`
4. Clear and cache config: `php artisan config:cache`
5. Link storage: `php artisan storage:link`

## License

Proprietary - All rights reserved
