# Inventory Shoe Application

## Overview
This is a Laravel 10 based inventory management system specifically designed for shoe tracking. It manages stock across Warehouses (Gudang) and Outlets, tracking individual items via serial numbers and reference codes.

## Key Features

### 1. Dashboard
- **Overview**: Displays total product count, total stock quantity, and alerts for low stock items (quantity < 5).
- **Access**: Accessible at `/dashboard` after login.

### 2. Inventory Management
- **Inbound (Barang Masuk)**:
    - Record new stock entering a warehouse.
    - Features: Select variant, warehouse rack, quantity, and optional serial numbers.
    - Auto-generated Reference Code: `PO-DD-MM-YYYY-XXXX`.
- **Outbound (Barang Keluar)**:
    - Transfer stock from Warehouse to Outlet.
    - Features: Select source rack (Warehouse) and destination rack (Outlet).
    - Auto-generated Reference Code: `INV-DD-MM-YYYY-XXXX`.
- **Transfer (Pindah Barang)**:
    - Move stock between locations (Warehouse to Warehouse, or general movements).
    - Auto-generated Reference Code: `TRF-DD-MM-YYYY-XXXX`.

### 3. Tracking & History
- **History**: View a log of all stock movements (Inbound, Outbound, Transfer) with details like user, timestamp, and locations.
- **Tracking**: Search for items using:
    - **Serial Number**: Finds the specific item's current status and history.
    - **Reference Code**: Finds the transaction (PO/INV/TRF) and all associated items.

### 4. Master Data Management
- **Products**: Manage shoe models and variants.
- **Warehouses**: Manage storage locations (Racks) for both Warehouses and Outlets.

### 5. Reporting
- **Print Surat Jalan**: Generate a "Surat Jalan" (Delivery Order) for movements, listing the reference code and serial numbers of items being moved.

## Technical Details
- **Framework**: Laravel 10
- **Database**: MySQL
- **Frontend**: Blade Templates using Tailwind CSS
- **Authentication**: Standard Laravel Auth (Breeze/UI)
- **Services**: Business logic encapsulated in `InventoryService`.

## User Roles
- **Admin/Staff**: Access to all inventory features (requires login).

## How to Run
1.  Clone repository.
2.  `composer install`
3.  `npm install && npm run dev`
4.  Copy `.env.example` to `.env` and configure database.
5.  `php artisan key:generate`
6.  `php artisan migrate --seed`
7.  `php artisan serve`
