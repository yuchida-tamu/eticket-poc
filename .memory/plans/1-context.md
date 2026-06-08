# Context package for issue #1: Scaffold Laravel backend with laravel-mobile-pass

**Generated:** 2026-06-09T00:00:00Z
**Contract version:** 2

## Issue summary
Stand up the Laravel backend backed by Postgres with the spatie/laravel-mobile-pass package installed and configured, ready to issue Apple Wallet passes.

## Linked issues
None

## Relevant ADRs
### ADR-0002: NFC pass reading via a ProximityReader native module
The backend must issue signed Apple Wallet NFC passes. The mobile app will later read these via a custom Expo native module wrapping ProximityReader. Backend exposes an API endpoint returning `.pkpass` (with serial number) ready to add to Apple Wallet.

## Relevant PRD sections
**Backend (Laravel):** issues signed Apple Wallet passes containing an NFC payload. Uses `spatie/laravel-mobile-pass` for `.pkpass` generation and signing, backed by Postgres. Exposes an API endpoint that returns a `.pkpass` (with a serial number) ready to add to Apple Wallet.

**Tech Stack:**
- **Runtime:** PHP runtime for the Laravel service
- **Language:** PHP (backend)
- **Framework:** Laravel on the server
- **Persistence:** Postgres (backend)
- **Test:** Pest (backend, light — POC)
- **Lint:** Laravel Pint (backend)
- **Key dependency:** `spatie/laravel-mobile-pass`

**Commands (run from backend app directory):**
- `./vendor/bin/pest` — tests
- `./vendor/bin/pint` — format/lint
- `php artisan test` — alternative test entry

## Glossary terms
- **.pkpass** — the signed Apple Wallet pass bundle format produced by the backend
- **NFC Pass** — an Apple Wallet pass with an NFC payload, presented by tapping a device
- **Serial Number** — the unique per-pass identifier decoded and displayed by the reader app

## Relevant conventions
From CLAUDE.md:
- Monorepo layout: Laravel backend lives in `apps/backend/`. Create ALL backend files under `apps/backend/`.
- Check command (gate before commit): from inside `apps/backend/` — `./vendor/bin/pest` (tests) and `./vendor/bin/pint` (format). Both must pass.
- Toolchain: PHP 8.5, Composer 2.9, Node 24
- Postgres may not be running locally — if no live DB reachable, make `GET /api/health` return `{"status":"ok"}` 200 WITHOUT requiring a DB connection
- Never use `git add .` or `git add -A` — use scoped adds only (e.g., `git add apps/backend`)

## Acceptance criteria
1. Laravel project boots and connects to Postgres
2. `spatie/laravel-mobile-pass` is installed and configured
3. A health endpoint returns HTTP 200

## Symbol references in codebase
None yet (fresh backend scaffold).

## Recent related PRs
None

## Issue comments
None

## Library documentation
### spatie/laravel-mobile-pass
A Laravel package for generating signed Apple Wallet passes (.pkpass files). Requires:
- Certificate signing infrastructure for .pkpass generation
- Configuration of certificate paths in Laravel config
- Installation via Composer: `composer require spatie/laravel-mobile-pass`
- Key interface: PassGenerator or similar for creating pass objects
- Output: signed .pkpass binary file ready for distribution

## Gathering notes
- No linked issues or recent PRs found
- Library docs are public but details will be gathered during implementation
- Project toolchain is PHP 8.5, Composer 2.9, Node 24
- DB assumption: Postgres configured but may not be running locally; health endpoint can bypass DB requirement if needed
