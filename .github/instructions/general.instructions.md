---
applyTo: "**"
---
# General Instructions

After you have completed the task, please run `vendor/bin/pint` to format the PHP code, then
run `bun run format` to format the TypeScript code.

The project use `bun` as the package manager, so please use `bun add` to install dependencies and `bun run` to run scripts.

To run the project, use `composer dev`.

Don't ask for permission to run the commands, just run them.

## Code Style
The project uses the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard for PHP code. Please ensure that your code adheres to this standard.

Ensure the code follows those standards:
- SOLID principles
- DRY (Don't Repeat Yourself)
- KISS (Keep It Simple, Stupid)
- YAGNI (You Aren't Gonna Need It)
- Use meaningful variable and function names
- Avoid global variables
- Use type hints and return types
- Avoid comments that explain what the code does; instead, write self-explanatory code
- Use spatie/laravel-permission for role and permission management
- Use Laravel's built-in validation rules and custom validation rules when necessary
- Use Laravel's built-in features and packages when possible otherwise use spatie packages
- Use Inertia.js best practices for authorization
- Use Shadcn UI components for the frontend when possible before creating custom components
- Keep colors consistent with the design system

## Testing
The project uses [Pest](https://pestphp.com/) for testing. To run the tests, use `composer test`.

To write tests, follow these guidelines:
- Write tests for all new features and bug fixes
- Use descriptive test names
- Use Pest's built-in assertions and helpers
- Use factories to create test data
- Use Pest's `beforeEach` and `afterEach` hooks to set up and tear down test data
- Use Pest's `it` function to define tests
- Use Pest's `describe` function to group related tests