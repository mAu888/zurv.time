# Test Coverage Analysis — zurv.time

## Current State

**Test coverage: 0%** — The project has no tests, no test framework, no test configuration, and no CI/CD pipeline.

---

## Areas Requiring Test Coverage

### 1. Backend PHP API Handlers (Critical Priority)

These are the core data paths of the application and carry the highest risk.

#### `ProjectHandler` — `projects.php`
| Method | What to test |
|--------|-------------|
| `get_xhr()` | Returns all projects sorted by name as JSON |
| `post_xhr()` | Creates a project, returns JSON with `id` and `name` |
| `delete_xhr($id)` | Deletes a project by ID |

**Key risks without tests:**
- No input validation on `$_POST['name']` — empty or excessively long names are accepted
- Deleting a project does not cascade-delete or soft-delete associated tracks (orphaned rows)
- No authentication or authorization checks

#### `TracksHandler` / `TrackHandler` — `tracks.php`
| Method | What to test |
|--------|-------------|
| `TracksHandler::get_xhr($projectId)` | Lists tracks, optionally filtered by project; default excludes soft-deleted |
| `TracksHandler::post_xhr()` | Creates a track with description, date, rate, minutes, project_id |
| `TrackHandler::put_xhr($id)` | Updates a track's fields including paid status |
| `TrackHandler::delete_xhr($id)` | Soft-deletes a track (sets `deleted = 1`) |

**Key risks without tests:**
- Date conversion (`$_POST['date']/1000`) — division by 1000 assumes JS millisecond timestamps; no validation if input is malformed
- No validation on `rate` or `minutes` (could insert negative values, zero, or non-numeric data)
- `put_xhr` uses `$_POST['id']` in the WHERE clause but receives `$id` from the route — mismatch could update the wrong record
- `get_xhr` with `$projectId = -1` default — the `$projectId > 0` branch uses a prepared statement but returns different columns (`project_id AS project`) compared to the else branch (which also includes `UNIX_TIMESTAMP`)
- Soft delete is only used for tracks, but the GET query for project-filtered tracks doesn't filter by `deleted = 0`

**Suggested test framework:** PHPUnit

**Suggested approach:** Create integration tests against a test SQLite/MySQL database, mocking `$_POST`, `$_SERVER`, and output buffering to capture JSON responses.

---

### 2. Toro Micro-Framework — `library/toro.php` (High Priority)

The routing framework is custom-built and entirely untested.

| Component | What to test |
|-----------|-------------|
| `ToroApplication::serve()` | Route matching (exact and regex), 404 handling, method dispatch |
| `ToroHook` | Hook registration, firing order, parameter passing |
| Request type detection | `xhr_request()`, `ipad_request()`, `mobile_request()` |
| Route parameter extraction | Regex capture groups passed as method arguments |
| Method fallback | `ToroHandler::__call()` returns 404 for undefined methods |

**Key risks without tests:**
- `ipad_request()` and `mobile_request()` access `$_SERVER['HTTP_USER_AGENT']` without checking if it's set — will throw notices
- `var_dump($discovered_handler)` in the 404 path leaks internal state to users
- Route matching order matters (first match wins) — no tests verify correct precedence

---

### 3. Registry Singleton — `library/core.php` (Medium Priority)

| What to test |
|-------------|
| `getInstance()` always returns the same instance |
| `__get` returns `null` for unset keys |
| `__set` stores and retrieves values correctly |

---

### 4. Frontend JavaScript — `js/script.js` (High Priority)

The frontend contains significant business logic that should be unit tested.

#### Pure utility functions (easy wins)
| Function | What to test |
|----------|-------------|
| `isSameDay(date1, date2)` | Same day/different day, edge cases (midnight, year boundaries) |
| `isSameMonth(date1, date2)` | Same month/different month, year-crossing |
| `Track.dateSort(a, b)` | Sort stability, paid vs unpaid ordering, same-day tiebreaking |

#### Track model methods
| Method | What to test |
|--------|-------------|
| `Track.sum(asNumber)` | Calculation: `(minutes / 60) * rate` — verify with known values |
| `Track.formattedTime()` | Edge cases: 0 minutes, 59 minutes, 60 minutes, 90 minutes, 120+ minutes |
| `Track.formattedDate()` | Date formatting for `d-m` and `y-m-d` formats |
| `Track.industryTime()` | Decimal hour conversion accuracy |

**Key bugs discoverable by tests:**
- `Track.sum(asNumber)`: When `asNumber` is `false`, `sum.toFixed(2)` is called but the result is **not returned** — the unformatted number is always returned
- `Track.formattedDate()`: References a variable `format` that is never defined in scope — will always use the `d-m` branch
- `Track.industryTime()`: The formula `hours + ((minutes - 60*hours) / 60 * 100)` is incorrect for industry time — should divide remainder by 60, not multiply by 100/60

#### Form validation in `TracksApp.create()`
| What to test |
|-------------|
| Rate regex validation: `/^(([1-9]+[0-9]*)\|([1-9]+[0-9]*.[0-9]{1,2}))$/` |
| Time parsing: `HH:MM` format conversion to minutes |
| Minutes regex: `/^([1-9]+[0-9]*)$/` — rejects `0` as valid input |
| Comma-to-dot conversion in rate |

**Suggested test framework:** Jest or Mocha (with jsdom for DOM-dependent tests)

---

### 5. Request Lifecycle / Integration Tests (Medium Priority)

End-to-end flows that cross backend and frontend boundaries:

| Flow | What to test |
|------|-------------|
| Create project → add track → mark paid → delete track | Full CRUD lifecycle |
| Filter tracks by today / month / previous month | Date filtering accuracy |
| `before_request` hook | JSON body parsing into `$_POST`/`$_GET` superglobals |
| `after_request` hook | Database connection cleanup |
| Non-XHR requests | Ensure proper 404 behavior for non-AJAX calls to API routes |

---

## Prioritized Recommendations

### Phase 1 — Quick wins with highest value
1. **Add PHPUnit** and write integration tests for `ProjectHandler` and `TracksHandler`/`TrackHandler` — these are the data persistence layer and bugs here mean data loss or corruption
2. **Extract and test pure JS functions** (`isSameDay`, `isSameMonth`, `dateSort`, `sum`, `formattedTime`) — these have actual bugs that tests would immediately surface

### Phase 2 — Framework and routing
3. **Test `ToroApplication` routing** — verify that URL patterns map to the correct handlers and that regex parameters are extracted correctly
4. **Test `ToroHook`** — verify hooks fire in order and receive correct parameters

### Phase 3 — Form validation and UI logic
5. **Test `TracksApp.create()` form validation** — the regex patterns for rate and time input, and the HH:MM to minutes conversion
6. **Test `calcTotal` and `setAllPaid`** — financial calculations that affect what users see as totals

### Phase 4 — Security and edge cases
7. **Add input validation tests** — verify that the API rejects invalid/malicious input (SQL injection is mitigated by prepared statements, but missing validation could still cause data quality issues)
8. **Test the `put_xhr` route parameter vs body ID mismatch** — this is a potential bug where the URL `$id` and `$_POST['id']` could differ

---

## Bugs Identified During Analysis

| # | Location | Bug |
|---|----------|-----|
| 1 | `js/script.js:73` | `Track.sum(false)` calls `toFixed(2)` but doesn't return the result |
| 2 | `js/script.js:90` | `Track.formattedDate()` references undefined `format` variable |
| 3 | `tracks.php:9` | Project-filtered GET doesn't exclude soft-deleted tracks (`deleted = 0` missing) |
| 4 | `tracks.php:46` | `put_xhr` uses `$_POST['id']` instead of route param `$id` in WHERE clause |
| 5 | `library/toro.php:107` | 404 handler uses `var_dump()`, leaking internals |
| 6 | `library/toro.php:127-131` | `ipad_request()`/`mobile_request()` don't check if `HTTP_USER_AGENT` is set |
