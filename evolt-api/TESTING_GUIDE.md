# EVolt API - Testing Guide

## 🚀 Quick Start

### 1. Setup Environment
```bash
# Start Laravel server
php artisan serve --host=127.0.0.1 --port=8000

# Run migrations and seed data
php artisan migrate:fresh --seed
```

### 2. Import Postman Collection
1. Open Postman
2. Click "Import" → Select `POSTMAN_COLLECTION.json`
3. Set up environment variables:
   - `baseUrl`: `http://localhost:8000`

## 📋 Challenge Testing Checklist

### **CHALLENGE 1 - Authentication ✅**
- [ ] Register new user
- [ ] Register admin user  
- [ ] Login as user
- [ ] Login as admin
- [ ] Get current user info
- [ ] Logout

**Expected Results:**
- Tokens should be stored in environment variables
- User role should be correctly set

### **CHALLENGE 2 - Create Reservation ✅**
- [ ] Login as user
- [ ] Create reservation with status = 'en_cours'

**Expected Results:**
- Reservation should be created automatically with 'en_cours' status
- User ID should be automatically set from authenticated user

### **CHALLENGE 3 - List My Reservations ✅**
- [ ] Login as user
- [ ] Get "mes-reservations"

**Expected Results:**
- Should only show reservations for authenticated user
- Should include charging station details

### **CHALLENGE 4 - Pay Reservation ✅**
- [ ] Create a reservation
- [ ] Pay the reservation

**Expected Results:**
- Status should change from 'en_cours' to 'payee'
- Only reservation owner can pay

### **CHALLENGE 5 - Cancel Reservation ✅**
- [ ] Create a reservation
- [ ] Cancel the reservation

**Expected Results:**
- Status should change to 'annulee'
- Should still appear in "mes-reservations" with new status

### **CHALLENGE 6 - Admin Dashboard ✅**
- [ ] Login as admin
- [ ] Get dashboard stats

**Expected Results:**
- Should show total, payee, en_cours counts
- Should show last 10 reservations with user and station details

## 🔧 Manual Testing Steps

### 1. Using curl commands:

```bash
# Register user
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}'

# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'

# Create reservation (replace TOKEN and station_id)
curl -X POST http://localhost:8000/api/reservations \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"charging_station_id":1,"start_time":"2026-03-25T10:00:00","end_time":"2026-03-25T12:00:00"}'
```

### 2. Using Browser:
- Navigate to `http://localhost:8000/api/charging-stations` (with auth header)
- Check JSON responses directly

## 📊 Expected Database State After Testing

### Users:
- 1 admin user (admin@evolt.com)
- 1 regular user (user@evolt.com) 
- Test users created during testing

### Charging Stations:
- 3 initial stations (Casablanca, Rabat, Marrakech)
- Any stations created during admin testing

### Reservations:
- Mix of statuses: 'en_cours', 'payee', 'annulee'
- Each reservation linked to user and charging station

## 🐛 Common Issues & Solutions

### 1. "Token not provided" error
- Make sure Authorization header is set: `Bearer YOUR_TOKEN`
- Check environment variables in Postman

### 2. "Station not available" error
- Check if station has overlapping reservations
- Verify start_time is in the future
- Try different time slots

### 3. "Admin access required" error
- Make sure user has role='admin'
- Use admin token for admin endpoints

### 4. Database connection issues
- Run `php artisan migrate:fresh --seed`
- Check .env file database settings

## 📝 Test Results Template

Copy this for your submission:

```
📅 JOUR 1 ☑️ TERMINÉ
Problème rencontré : ____________________
Endpoint testé Postman : ☑️ OUI
Status visible en base : ☑️ OUI
Nombre de réservations créées : ___

📅 JOUR 2 ☑️ TERMINÉ  
Problème rencontré : ____________________
Endpoint testé Postman : ☑️ OUI
Status visible en base : ☑️ OUI
Nombre de réservations créées : ___

📅 JOUR 3 ☑️ TERMINÉ
Problème rencontré : ____________________
Endpoint testé Postman : ☑️ OUI
Status visible en base : ☑️ OUI
Nombre de réservations créées : ___

📅 JOUR 4 ☑️ TERMINÉ
Problème rencontré : ____________________
Endpoint testé Postman : ☑️ OUI
Status visible en base : ☑️ OUI
Nombre de réservations créées : ___

📅 JOUR 5 ☑️ TERMINÉ
Problème rencontré : ____________________
Endpoint testé Postman : ☑️ OUI
Status visible en base : ☑️ OUI
Nombre de réservations créées : ___

📅 JOUR 6 ☑️ TERMINÉ
Problème rencontré : ____________________
Endpoint testé Postman : ☑️ OUI
Status visible en base : ☑️ OUI
Nombre de réservations créées : ___
```

## ✅ Final Deliverables Checklist

- [ ] UML diagram validated
- [ ] 5 endpoints functional
- [ ] 3 different reservation statuses (en_cours, payee, annulee)
- [ ] Admin dashboard with stats
- [ ] Complete Postman collection
- [ ] All tests passing
