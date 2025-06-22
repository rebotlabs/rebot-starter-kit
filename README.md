# Laravel + React Starter Kit

A comprehensive, production-ready Laravel starter kit with React frontend, featuring multi-tenancy, authentication, billing, and more.

## 🚀 Features

### Authentication & Security
- **Complete Authentication System**: Login, registration, password reset
- **Two-Factor Authentication (2FA)**: TOTP-based with backup codes
- **Email Verification**: OTP-based email verification system
- **Role-Based Access Control**: Using Spatie Laravel Permission
- **Password Confirmation**: For sensitive operations

### Multi-Tenant Organization System
- **Organization Management**: Create, update, delete organizations
- **Member Management**: Invite, manage, and remove team members
- **Role & Permission System**: Granular control over user permissions
- **Organization Switching**: Users can belong to multiple organizations
- **Team Onboarding**: Guided setup for new organizations

### Billing & Subscriptions
- **Stripe Integration**: Complete billing system using Laravel Cashier
- **Subscription Management**: Plans, upgrades, downgrades
- **Payment Methods**: Manage credit cards and payment sources
- **Webhooks**: Automated subscription status updates
- **Billing Portal**: Customer portal for subscription management

### User Interface
- **Modern Design System**: Built with shadcn/ui and Radix UI
- **Responsive Design**: Mobile-first approach with Tailwind CSS
- **Dark/Light Mode**: User preference-based theming
- **Component Library**: Reusable UI components
- **Form Validation**: Client and server-side validation
- **Toast Notifications**: User feedback system

### Developer Experience
- **TypeScript**: Fully typed frontend with React 19
- **Inertia.js**: Modern SPA experience with server-side routing
- **Hot Module Replacement**: Fast development with Vite
- **Code Formatting**: Prettier and ESLint configuration
- **Testing Suite**: Comprehensive PHP testing with Pest
- **CI/CD Ready**: GitHub Actions workflow included

### Internationalization
- **Multi-language Support**: i18n integration with Inertia
- **Translation Management**: Organized translation files
- **Dynamic Language Switching**: Runtime language changes

## 🛠 Technology Stack

### Backend
- **Laravel 12**: Latest PHP framework
- **PHP 8.4**: Modern PHP features
- **MySQL/PostgreSQL**: Database support
- **Stripe**: Payment processing
- **Spatie Packages**: Permission management and utilities

### Frontend
- **React 19**: Latest React with concurrent features
- **TypeScript**: Type-safe development
- **Inertia.js**: Server-side routing with SPA experience
- **Tailwind CSS 4**: Utility-first CSS framework
- **shadcn/ui**: High-quality component library
- **Radix UI**: Accessible component primitives
- **Vite**: Lightning-fast build tool

### Testing
- **Pest**: Modern PHP testing framework
- **Architecture Tests**: Code quality assurance
- **Feature Tests**: Application behavior testing

## 📦 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/rebotlabs/rebot-starter-kit.git
   cd rebot-starter-kit
   ```

2. **Install dependencies**
   ```bash
   composer install
   bun install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate --seed
   ```

5. **Build assets**
   ```bash
   bun run build
   ```

6. **Start development server**
   ```bash
   composer dev
   ```

## 🧪 Testing

Run the complete test suite:

```bash
# PHP tests
composer test

# Type checking
bun run types

# Code formatting
vendor/bin/pint
bun run format
```

## 🏗 Project Structure

```
├── app/
│   ├── Http/Controllers/           # Application controllers
│   │   ├── Auth/                   # Authentication controllers
│   │   ├── Organization/           # Multi-tenant organization logic
│   │   ├── Settings/               # User and org settings
│   │   └── Stripe/                 # Billing and webhooks
│   ├── Models/                     # Eloquent models
│   └── Notifications/              # Email notifications
├── resources/
│   ├── js/
│   │   ├── components/             # React components
│   │   │   ├── auth/               # Authentication forms
│   │   │   ├── ui/                 # Reusable UI components
│   │   │   └── settings/           # Settings panels
│   │   ├── pages/                  # Inertia pages
│   │   └── layouts/                # Page layouts
│   └── lang/                       # Translation files
├── tests/
│   ├── Feature/                    # Feature tests
│   ├── Unit/                       # Unit tests
│   └── Architecture/               # Architecture tests
└── database/
    ├── migrations/                 # Database migrations
    ├── seeders/                    # Database seeders
    └── factories/                  # Model factories
```

## 🚀 Getting Started

After installation, you can:

1. **Create an account** at `/register`
2. **Set up an organization** through the onboarding flow
3. **Invite team members** from organization settings
4. **Configure billing** if using paid features
5. **Customize the application** to your needs

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](.github/CONTRIBUTING.md) for details.

## 📄 License

This starter kit is open-sourced software licensed under the [MIT license](LICENSE).

## 🆘 Support

- [Documentation](https://laravel.com/docs)
- [Community Discussions](https://github.com/rebotlabs/rebot-starter-kit/discussions)
- [Issue Tracker](https://github.com/rebotlabs/rebot-starter-kit/issues)
