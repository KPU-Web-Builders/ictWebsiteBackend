  Test 1: User Registration

  curl -X POST http://localhost:8000/api/auth/register \
    -H "Content-Type: application/json" \
    -d '{
      "name": "Test User",
      "email": "test@example.com",
      "password": "password123",
      "password_confirmation": "password123"
    }'

  Expected Response:
  {
    "status": "success",
    "message": "User registered successfully",
    "user": {
      "name": "Test User",
      "email": "test@example.com",
      "id": 1
    }
  }

  Test 2: User Login

  curl -X POST http://localhost:8000/api/auth/login \
    -H "Content-Type: application/json" \
    -d '{
      "email": "test@example.com",
      "password": "password123"
    }'

  Expected Response:
  {
    "status": "success",
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "name": "Test User",
      "email": "test@example.com"
    }
  }

  Test 3: Get User Info (Protected Route)

  curl -X GET http://localhost:8000/api/auth/me \
    -H "Authorization: Bearer YOUR_TOKEN_HERE"

  Test 4: Refresh Token

  curl -X POST http://localhost:8000/api/auth/refresh \
    -H "Authorization: Bearer YOUR_TOKEN_HERE"

  Test 5: Logout

  curl -X POST http://localhost:8000/api/auth/logout \
    -H "Authorization: Bearer YOUR_TOKEN_HERE"

  Test 6: Protected Route Example

  curl -X GET http://localhost:8000/api/protected \
    -H "Authorization: Bearer YOUR_TOKEN_HERE"

  5. Testing with Postman

  1. Create Collection: "JWT Auth API"
  2. Set Base URL: http://localhost:8000/api
  3. Create requests for each endpoint above
  4. For protected routes: Add Authorization header with Bearer {token}

  6. Error Testing

  Test invalid scenarios:
  - Registration with existing email
  - Login with wrong credentials
  - Accessing protected routes without token
  - Using expired/invalid tokens

  7. Expected Status Codes

  - 200 - Success (login, me, logout, refresh)
  - 201 - Created (registration)
  - 401 - Unauthorized (invalid credentials, missing token)
  - 422 - Validation error (invalid data)
