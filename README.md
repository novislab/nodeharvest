<p align="center">
  <img src="public/assets/logo.webp" width="280" alt="NodeHarvest Logo">
</p>

<h1 align="center">
  <span style="color: #059669;">Node</span>Harvest
</h1>

<p align="center">
  <strong>Automated Node Manager for Passive Income Generation</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire">
  <img src="https://img.shields.io/badge/Tailwind-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/version-1.0.0-059669?style=flat-square" alt="Version">
  <img src="https://img.shields.io/badge/license-MIT-059669?style=flat-square" alt="License">
  <img src="https://img.shields.io/badge/status-active-059669?style=flat-square" alt="Status">
</p>

---

## About NodeHarvest

**NodeHarvest** is a powerful web application designed to automate the creation and management of cryptocurrency nodes for earning passive income. Built with Laravel 12 and Livewire 4, it provides a seamless interface for deploying, monitoring, and scaling your node infrastructure.

### Key Features

- **Auto-Create Nodes** 🤖
  - One-click node deployment
  - Automated configuration
  - Multi-chain support

- **Node Management** ⚡
  - Real-time monitoring dashboard
  - Performance analytics
  - Health checks & alerts

- **Earnings Tracker** 💰
  - Live earnings dashboard
  - Historical data visualization
  - Profit optimization suggestions

- **Auto-Scale** 📈
  - Smart resource allocation
  - Automatic node creation based on profitability
  - Load balancing across regions

---

## Tech Stack

| Component | Technology |
|-----------|-----------|
| **Framework** | Laravel 12 |
| **Frontend** | Livewire 4 + Flux UI |
| **Styling** | Tailwind CSS v4 |
| **Testing** | Pest PHP |
| **Icons** | Heroicons + Lucide |

---

## Getting Started

### Prerequisites

- PHP 8.4+
- Node.js 20+
- Composer 2+
- MySQL 8+ or PostgreSQL 15+

### Installation

```bash
# Clone the repository
git clone https://github.com/yourusername/nodeharvest.git
cd nodeharvest

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Build assets
npm run build

# Start development server
composer run dev
```

### Environment Configuration

```env
APP_NAME="NodeHarvest"
APP_ENV=local
APP_DEBUG=true

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nodeharvest
DB_USERNAME=root
DB_PASSWORD=

# Node Configuration
NODE_AUTO_CREATE=true
NODE_MONITORING_INTERVAL=300
NODE_EARNINGS_THRESHOLD=0.01
```

---

## Usage

### Dashboard

Access the main dashboard at `/dashboard` to view:
- Active nodes status
- Total earnings
- Node performance metrics
- Auto-creation settings

### Creating a Node

1. Navigate to **Nodes** → **Create New**
2. Select your preferred chain/network
3. Configure auto-scaling options
4. Click **Deploy Node**

### Auto-Create Settings

Configure automatic node creation:
- **Minimum Earnings Threshold**: Auto-create when earnings exceed X
- **Max Nodes**: Limit concurrent nodes
- **Regions**: Select deployment regions
- **Auto-Restart**: Enable automatic node restart on failure

---

## Development

### Running Tests

```bash
# Run all tests
php artisan test --compact

# Run specific test
php artisan test --filter=NodeCreationTest
```

### Code Style

```bash
# Format PHP code
composer format

# Check code style
vendor/bin/pint --test
```

### Development Commands

```bash
# Start Vite dev server
npm run dev

# Start Laravel dev server
php artisan serve

# Watch for changes
npm run watch
```

---

## Architecture

```
app/
├── Actions/          # Invokable action classes
├── Console/          # Artisan commands
├── Http/
│   └── Controllers/  # HTTP controllers
├── Models/           # Eloquent models
├── Services/         # Business logic services
└── View/
    └── Components/   # Blade components

resources/
├── views/
│   ├── components/   # UI components
│   ├── layouts/      # Page layouts
│   ├── pages/        # Livewire pages
│   └── partials/     # Shared partials
└── css/
    └── app.css       # Tailwind styles

routes/
├── web.php           # Web routes
└── console.php       # Console routes
```

---

## Security

- CSRF protection on all forms
- Encrypted session storage
- Rate limiting on API endpoints
- Input validation via Form Requests
- SQL injection protection via Eloquent

---

## Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## License

NodeHarvest is open-sourced software licensed under the [MIT license](LICENSE).

---

<p align="center">
  <span style="color: #059669;">Built with ❤️ using Laravel & Livewire</span>
</p>

<p align="center">
  <sub>© 2026 NodeHarvest. All rights reserved.</sub>
</p>
