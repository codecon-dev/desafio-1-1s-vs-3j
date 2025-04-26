# User Management API

A high-performance REST API for managing and analyzing user data with advanced caching mechanisms and parallel processing capabilities.

## Features

- User data management and analysis
- Superuser filtering and insights
- Team performance metrics
- Active users tracking
- Automatic caching with memoization
- Parallel endpoint evaluation
- Performance timing for all operations

## Prerequisites

- Node.js (v14 or higher)
- npm or yarn

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd <project-directory>
```

2. Install dependencies:
```bash
npm install
```

## Running the Application

Start the server:
```bash
npm start
```

The server will run on port 8011 by default. You can change this by setting the `PORT` environment variable.

## API Endpoints

### Data Management

#### POST /users
Upload user data to the system.
- **Content-Type**: multipart/form-data
- **File Field**: file
- **Response**: 
  ```json
  {
    "status": 201,
    "timestamp": "2024-03-21T10:00:00.000Z",
    "execution_time_ms": 0.123,
    "data": {
      "users_count": 100
    }
  }
  ```

### User Analysis

#### GET /superusers
Get all superusers (users with score >= 900 and active status).
- **Response**:
  ```json
  {
    "status": 200,
    "timestamp": "2024-03-21T10:00:00.000Z",
    "execution_time_ms": 0.123,
    "data": [
      {
        "id": "1",
        "score": 950,
        "ativo": true,
        // ... other user fields
      }
    ]
  }
  ```

#### GET /top-countries
Get top 5 countries by superuser count.
- **Response**:
  ```json
  {
    "status": 200,
    "timestamp": "2024-03-21T10:00:00.000Z",
    "execution_time_ms": 0.123,
    "data": [
      {
        "country": "Brazil",
        "count": 25
      }
      // ... up to 5 countries
    ]
  }
  ```

#### GET /team-insights
Get detailed insights about each team's performance.
- **Response**:
  ```json
  {
    "status": 200,
    "timestamp": "2024-03-21T10:00:00.000Z",
    "execution_time_ms": 0.123,
    "data": [
      {
        "team": "Team A",
        "total_members": 10,
        "active_members": 8,
        "leaders": 1,
        "completed_projects": {
          "Project X": 5,
          "Project Y": 3
        },
        "active_percentage": 80.00
      }
      // ... other teams
    ]
  }
  ```

#### GET /active-users-per-day
Get daily active user counts.
- **Response**:
  ```json
  {
    "status": 200,
    "timestamp": "2024-03-21T10:00:00.000Z",
    "execution_time_ms": 0.123,
    "data": [
      {
        "date": "2024-03-21",
        "count": 150
      }
      // ... other dates
    ]
  }
  ```

#### GET /evaluation
Test all endpoints in parallel and get performance metrics.
- **Response**:
  ```json
  {
    "status": 200,
    "timestamp": "2024-03-21T10:00:00.000Z",
    "execution_time_ms": 0.123,
    "data": {
      "/superusers": {
        "status": 200,
        "timestamp": "2024-03-21T10:00:00.000Z",
        "execution_time_ms": 0.123,
        "valid_response": true
      }
      // ... other endpoints
    }
  }
  ```

## Algorithm Behaviors

### Superuser Filtering
- Users are considered superusers if they have:
  - Score >= 900
  - Active status (ativo = true)
- Results are cached using Lodash memoization
- Cache is invalidated when new user data is uploaded

### Team Insights Calculation
1. Groups users by team name
2. For each team:
   - Calculates total and active member counts
   - Identifies team leaders
   - Counts completed projects
   - Calculates active member percentage
3. Uses Lodash chain for efficient data processing
4. Results are cached for better performance

### Active Users Tracking
- Tracks user login actions
- Groups by date
- Orders results by date in descending order
- Uses efficient Lodash operations for data processing

### Performance Optimization
- All expensive computations are memoized
- Parallel processing for endpoint evaluation
- Automatic cache invalidation on data updates
- Response timing for all operations

## Error Handling

The API includes comprehensive error handling:
- Invalid JSON data
- Missing or malformed files
- Processing errors
- All errors are caught and returned with appropriate status codes

## Performance Considerations

- Uses in-memory storage for fast data access
- Implements caching for expensive computations
- Parallel processing where applicable
- Efficient data structures and algorithms
- Response timing for monitoring

## Dependencies

- express: Web framework
- body-parser: Request body parsing
- multer: File upload handling
- lodash: Utility functions and data processing

## Storage Strategy and Performance Implications

### In-Memory Storage Design

The application uses a hybrid storage approach combining Multer's memory storage with a shared in-memory variable (`__STORAGE__`):

```javascript
const __STORAGE__ = {};
const getStorageForFile = (filename) => multer({ storage: multer.memoryStorage() }).single(filename);
```

#### Why Memory Storage?

1. **Fast File Processing**:
   - Multer's memory storage keeps uploaded files in memory as `Buffer` objects
   - Eliminates disk I/O operations during file upload
   - Provides immediate access to file contents for processing

2. **Simplified Data Flow**:
   - File contents are directly available in `req.file.buffer`
   - No need for temporary file management
   - Reduces complexity in file handling code

#### Shared Memory Variable (`__STORAGE__`)

The `__STORAGE__` object serves as a shared in-memory cache with several benefits:

1. **Performance Optimization**:
   - Eliminates database queries
   - Provides instant data access
   - Reduces latency for all endpoints

2. **Computation Efficiency**:
   - Cached results of expensive operations
   - Memoized function results stored in memory
   - Quick access to frequently used data

### Performance Impact

#### Positive Impacts

1. **Response Time**:
   - Sub-millisecond data access
   - No network latency from external storage
   - Immediate data availability for computations

2. **Resource Utilization**:
   - Reduced CPU usage through caching
   - Lower memory footprint compared to database connections
   - Efficient memory management through JavaScript's garbage collection

3. **Scalability for Small to Medium Datasets**:
   - Excellent performance for datasets that fit in memory
   - Quick data updates and invalidations
   - Fast cache rebuilding when needed

#### Limitations and Considerations

1. **Memory Constraints**:
   - Limited by available system memory
   - Not suitable for very large datasets
   - Not accept multiple uploads because could have an override of the file contents

2. **Data Persistence**:
   - Data lost on server restart
   - No built-in data recovery
   - Requires external backup strategy if needed

3. **Concurrency Handling**:
   - Single-threaded Node.js model prevents race conditions
   - Memory operations are atomic
   - Cache invalidation is straightforward

