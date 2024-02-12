Invoice Management Application
Description
The Invoice Management Application is a Laravel-based web application designed to efficiently handle invoice operations. It allows authenticated users to create, view, update, and delete invoices, along with the capability to add multiple line items to each invoice. The application also features user-specific invoice management by linking invoices to individual users.

Features
**User Authentication: Secure registration and login system for users.
**Invoice CRUD Operations: Users can create, read, update, and delete invoices.
**Line Item Management: Each invoice supports multiple line items, each with a description, quantity, and unit price.
**User Association: Invoices are associated with users, allowing for user-specific invoice management.
**Responsive UI: Utilizes Bootstrap for a clean and responsive user interface.
Installation
Prerequisites
PHP >= 8.1
Composer
Laravel >= 10.10
MySQL or a similar database system
Node.js and NPM
Setting Up
Clone the Repository
git clone https://github.com/DeriaIzzet/Invoice-System.git
cd invoices
Install PHP Dependencies
composer install
Install JavaScript Dependencies
npm install
Compile Assets
-npm run dev

Environment Configuration
cp .env.example .env

Generate Application Key
php artisan key:generate

Migrations
Migrate the database to set up the necessary tables.
cd invoices php artisan migrate

Start the Application
Serve the application on a development server.

php artisan serve Access the application at http://localhost:8000.

Usage
**Registration and Login: Users must register and log in to access the invoice management features.
**Managing Invoices: Navigate to the invoices section in the application to perform CRUD operations on invoices.
**Adding Line Items: While creating or editing an invoice, users can add multiple line items.
License
none
