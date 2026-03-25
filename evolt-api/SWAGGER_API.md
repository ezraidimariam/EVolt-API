# EVolt API Documentation - Swagger/OpenAPI

## Base URL
```
http://localhost:8000/api
```

## Authentication
All protected endpoints require Bearer token authentication:
```
Authorization: Bearer {token}
```

---

## Authentication Endpoints

### POST /register
Register a new user.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "user"
}
```

**Response (201):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user",
    "created_at": "2024-03-24T10:00:00.000000Z",
    "updated_at": "2024-03-24T10:00:00.000000Z"
  },
  "token": "1|abc123...",
  "token_type": "Bearer"
}
```

### POST /login
Authenticate user and return token.

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user"
  },
  "token": "1|abc123...",
  "token_type": "Bearer"
}
```

### POST /logout
Logout user (requires authentication).

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

### GET /user
Get current authenticated user (requires authentication).

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "role": "user"
}
```

---

## Charging Stations Endpoints

### GET /charging-stations
Get all available charging stations.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
[
  {
    "id": 1,
    "name": "Station Casablanca Centre",
    "address": "Casablanca, Morocco",
    "latitude": 33.5731,
    "longitude": -7.5898,
    "connector_type": "Type 2",
    "power_kw": 22.0,
    "is_available": true,
    "created_at": "2024-03-24T10:00:00.000000Z",
    "updated_at": "2024-03-24T10:00:00.000000Z"
  }
]
```

### GET /charging-stations/search
Search charging stations by location and filters.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `latitude` (required, number): Latitude coordinate
- `longitude` (required, number): Longitude coordinate
- `radius` (optional, number): Search radius in kilometers (1-50)
- `connector_type` (optional, string): Filter by connector type (Type 1, Type 2, CHAdeMO, CCS)
- `min_power` (optional, number): Minimum power in kW

**Response (200):**
```json
[
  {
    "id": 1,
    "name": "Station Casablanca Centre",
    "address": "Casablanca, Morocco",
    "latitude": 33.5731,
    "longitude": -7.5898,
    "connector_type": "Type 2",
    "power_kw": 22.0,
    "is_available": true
  }
]
```

---

## Reservations Endpoints

### POST /reservations
Create a new reservation.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "charging_station_id": 1,
  "start_time": "2024-03-25T10:00:00",
  "end_time": "2024-03-25T12:00:00"
}
```

**Response (201):**
```json
{
  "id": 1,
  "user_id": 1,
  "charging_station_id": 1,
  "start_time": "2024-03-25T10:00:00.000000Z",
  "end_time": "2024-03-25T12:00:00.000000Z",
  "status": "en_cours",
  "created_at": "2024-03-24T10:00:00.000000Z",
  "updated_at": "2024-03-24T10:00:00.000000Z",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "chargingStation": {
    "id": 1,
    "name": "Station Casablanca Centre",
    "address": "Casablanca, Morocco"
  }
}
```

### GET /mes-reservations
Get current user's reservations.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
[
  {
    "id": 1,
    "user_id": 1,
    "charging_station_id": 1,
    "start_time": "2024-03-25T10:00:00.000000Z",
    "end_time": "2024-03-25T12:00:00.000000Z",
    "status": "en_cours",
    "chargingStation": {
      "id": 1,
      "name": "Station Casablanca Centre",
      "address": "Casablanca, Morocco",
      "connector_type": "Type 2",
      "power_kw": 22.0
    }
  }
]
```

### POST /reservations/{id}/pay
Mark a reservation as paid.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "user_id": 1,
  "charging_station_id": 1,
  "start_time": "2024-03-25T10:00:00.000000Z",
  "end_time": "2024-03-25T12:00:00.000000Z",
  "status": "payee",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "chargingStation": {
    "id": 1,
    "name": "Station Casablanca Centre"
  }
}
```

### POST /reservations/{id}/cancel
Cancel a reservation.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "user_id": 1,
  "charging_station_id": 1,
  "start_time": "2024-03-25T10:00:00.000000Z",
  "end_time": "2024-03-25T12:00:00.000000Z",
  "status": "annulee",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "chargingStation": {
    "id": 1,
    "name": "Station Casablanca Centre"
  }
}
```

---

## Admin Endpoints

### GET /admin/dashboard
Get admin dashboard statistics.

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
  "stats": {
    "total_reservations": 15,
    "payee_reservations": 8,
    "en_cours_reservations": 5,
    "annulee_reservations": 2
  },
  "last_reservations": [
    {
      "id": 15,
      "user_id": 1,
      "charging_station_id": 1,
      "start_time": "2024-03-25T10:00:00.000000Z",
      "end_time": "2024-03-25T12:00:00.000000Z",
      "status": "en_cours",
      "created_at": "2024-03-24T10:00:00.000000Z",
      "updated_at": "2024-03-24T10:00:00.000000Z",
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
      },
      "chargingStation": {
        "id": 1,
        "name": "Station Casablanca Centre",
        "address": "Casablanca, Morocco"
      }
    }
  ]
}
```

### POST /admin/charging-stations
Create a new charging station (admin only).

**Headers:**
```
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "New Station",
  "address": "New Address",
  "latitude": 33.5731,
  "longitude": -7.5898,
  "connector_type": "Type 2",
  "power_kw": 22.0,
  "is_available": true
}
```

**Response (201):**
```json
{
  "id": 4,
  "name": "New Station",
  "address": "New Address",
  "latitude": 33.5731,
  "longitude": -7.5898,
  "connector_type": "Type 2",
  "power_kw": 22.0,
  "is_available": true,
  "created_at": "2024-03-24T10:00:00.000000Z",
  "updated_at": "2024-03-24T10:00:00.000000Z"
}
```

### PUT /admin/charging-stations/{id}
Update a charging station (admin only).

**Headers:**
```
Authorization: Bearer {admin_token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "Updated Station Name",
  "power_kw": 25.0
}
```

**Response (200):**
```json
{
  "id": 1,
  "name": "Updated Station Name",
  "address": "Casablanca, Morocco",
  "latitude": 33.5731,
  "longitude": -7.5898,
  "connector_type": "Type 2",
  "power_kw": 25.0,
  "is_available": true,
  "updated_at": "2024-03-24T10:30:00.000000Z"
}
```

### DELETE /admin/charging-stations/{id}
Delete a charging station (admin only).

**Headers:**
```
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
  "message": "Charging station deleted successfully"
}
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "Admin access required"
}
```

### 422 Validation Error
```json
{
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password field is required."
    ]
  }
}
```

### 404 Not Found
```json
{
  "message": "Resource not found"
}
```

---

## Status Codes

- **200**: Success
- **201**: Created
- **400**: Bad Request
- **401**: Unauthorized
- **403**: Forbidden
- **404**: Not Found
- **422**: Validation Error
- **500**: Internal Server Error

---

## Reservation Status Values

- **en_cours**: Reservation is active/in progress
- **payee**: Reservation has been paid
- **annulee**: Reservation has been cancelled

---

## Connector Types

- **Type 1**: Type 1 connector
- **Type 2**: Type 2 connector
- **CHAdeMO**: CHAdeMO connector
- **CCS**: Combined Charging System connector
