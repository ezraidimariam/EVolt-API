# EVolt API - Electric Vehicle Charging Stations Management

A Laravel REST API for managing electric vehicle charging stations, allowing users to search for available stations, make reservations, and track charging sessions.

## 🚀 Features

### User Features
- 🔐 **Authentication** with Laravel Sanctum
- ⚡ **Search** charging stations by location and connector type
- 📅 **Create** charging reservations
- 🔄 **Modify** reservations (pay/cancel)
- 📊 **View** personal charging history

### Admin Features
- 🔧 **CRUD** operations for charging stations
- 📈 **Dashboard** with reservation statistics
- 👥 **User** management capabilities

## 📋 API Endpoints

### Authentication
```
POST /api/register     - Register new user
POST /api/login        - User login
POST /api/logout       - User logout
GET  /api/user         - Get current user
```

### Charging Stations
```
GET  /api/charging-stations              - List all available stations
GET  /api/charging-stations/search      - Search stations by location
POST /api/admin/charging-stations       - [Admin] Create station
PUT  /api/admin/charging-stations/{id}  - [Admin] Update station
DELETE /api/admin/charging-stations/{id} [Admin] Delete station
```

### Reservations
```
POST /api/reservations                  - Create reservation
GET  /api/my-reservations              - Get my reservations
POST /api/reservations/{id}/pay         - Pay reservation
POST /api/reservations/{id}/cancel      - Cancel reservation
```

### Admin Dashboard
```
GET /api/admin/dashboard                - Get statistics and recent reservations
```

## 🛠️ Installation

1. **Clone repository**
```bash
git clone <repository-url>
cd evolt-api
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database setup**
```bash
php artisan migrate:fresh --seed
```

5. **Start server**
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

## 📊 Database Schema

### Users
- `id`, `name`, `email`, `password`, `role` (user/admin)

### Charging Stations  
- `id`, `name`, `address`, `latitude`, `longitude`, `connector_type`, `power_kw`, `is_available`

### Reservations
- `id`, `user_id`, `charging_station_id`, `start_time`, `end_time`, `status` (en_cours/payee/annulee)

## 🧪 Testing

### Postman Collection
Import `POSTMAN_COLLECTION.json` for complete API testing.

### Manual Testing
See `TESTING_GUIDE.md` for detailed testing instructions.

### Test Users (from seeder)
- **Admin**: admin@evolt.com / password
- **User**: user@evolt.com / password

## 📝 Project Challenges Completed

✅ **Challenge 1** - UML + Project Setup  
✅ **Challenge 2** - Create Reservation  
✅ **Challenge 3** - List My Reservations  
✅ **Challenge 4** - Pay Reservation  
✅ **Challenge 5** - Cancel Reservation  
✅ **Challenge 6** - Admin Dashboard  

## 🔧 Technologies Used

- **Backend**: Laravel 12
- **Authentication**: Laravel Sanctum  
- **Database**: SQLite
- **API Documentation**: Postman Collection
- **Testing**: PHPUnit + Postman

## 📄 License

This project is for educational purposes as part of the EVolt API development challenge.
