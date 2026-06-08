# eticket-poc

A fullstack proof of concept: a Laravel backend creates Apple Wallet NFC passes via `spatie/laravel-mobile-pass`, and an Expo iOS app reads those passes through a custom native module wrapping the ProximityReader framework, decoding and displaying the pass data.

## Principles

### Think Before Coding
- Read the relevant source files before changing anything. Understand the current state — don't assume from memory or naming alone.
- Check `.memory/` and `docs/adr/` for existing design decisions. If a decision exists, follow it or propose a revision — don't silently diverge.
- Use `/plan-feature` for any feature touching multiple files. Skip planning only for isolated bug fixes or single-file changes.

### Simplicity First
- Solve the problem in front of you. Don't add abstractions, helpers, or config for hypothetical futures.
- Three similar lines beats a premature function. A flat `if` beats a strategy pattern.
- The codebase is small. Grep and read before inventing — the utility you need may already exist.

### Surgical Changes
- Touch only the files that need to change. A bug fix is not a refactoring opportunity.
- Don't reformat, rename, or reorganize code adjacent to your change.
- When adding a feature, match the patterns already in use. Don't introduce new patterns without an ADR.

### Separate Structural from Behavioral Changes (Tidy First)
- **Never mix structural and behavioral changes in the same PR.** Structural = renames, extractions, moves, reformatting, reorganizing imports, splitting files — changes that preserve behavior. Behavioral = anything that alters what the code *does* (new logic, bug fixes, formula tweaks, new actions).
- **Order:** if a change needs both, land the structural PR first (empty-diff in behavior, green tests), then the behavioral PR on top. This makes behavioral diffs small and reviewable, and keeps `git blame` honest.
- **One PR, one kind.** If you notice midway that you're doing both, stop and split the branch. Do not rationalize "it's small, I'll bundle it."
- **Red flag in review:** a PR titled as a bug fix with renamed symbols, moved files, or reformatted blocks. Call it out and request a split.
- Applies to commits within a PR too — prefer separate commits for structural vs behavioral work even when they ship together.

### Goal-Driven Execution
- Every change must pass `npm run check` (typecheck + lint + test). The post-edit hook enforces this automatically.
- Ship through PRs. Never push to `main`. Branch → implement → PR → review → merge.
- Present the PR link to the user after creating it. Wait for their review before merging.

## Architecture

Fullstack POC, leaning mobile. Two cooperating parts:

- **Backend (Laravel):** issues signed Apple Wallet passes containing an NFC payload. Uses `spatie/laravel-mobile-pass` for `.pkpass` generation and signing, backed by Postgres. Exposes an API endpoint that returns a `.pkpass` (with a serial number) ready to add to Apple Wallet.
- **Mobile (Expo / iOS):** an NFC reader app. A custom Expo native module (Swift) wraps Apple's ProximityReader framework. The user taps to read an NFC pass presented on another iPhone/iPad; the decoded fields (e.g. serial number) are surfaced to React Native JS and displayed.

Core state flow:

```
[Laravel API] --issues .pkpass (NFC payload + serial)--> [Apple Wallet on device A]
[device A] --presents NFC pass via tap--> [ProximityReader native module] --decoded data--> [Expo reader app on device B] --renders--> [user]
```

## Tech Stack

- **Runtime:** iOS (Expo dev client) + PHP runtime for the Laravel service
- **Language:** TypeScript (mobile), Swift (native module), PHP (backend)
- **Framework:** React Native (Expo) on the client; Laravel on the server
- **State:** React state (POC — no global store yet)
- **Persistence:** Postgres (backend); no significant client-side persistence
- **Test:** Jest (mobile); Pest (backend, light — POC)
- **Lint:** ESLint + Prettier (mobile); Laravel Pint (backend)

Key backend dependency: [`spatie/laravel-mobile-pass`](https://github.com/spatie/laravel-mobile-pass).

## Commands

The enforced project check (mobile, hook-gated):

- `npm run check` — typecheck + lint + test
- `npm test` — Jest
- `npm run typecheck` — TypeScript
- `npm run lint` — ESLint

Backend (Laravel) equivalents, run from the backend app directory:

- `./vendor/bin/pest` — tests
- `./vendor/bin/pint` — format/lint
- `php artisan test` — alternative test entry

## Conventions

- Ship through PRs. Never push to main. Record significant decisions in `docs/adr/`.

## Workflow

1. Branch from `main`
2. Consult `.memory/` and `docs/adr/` for relevant specs
3. Implement
4. Run the check command (must pass)
5. Push branch, create PR via `gh pr create`
6. Wait for review
7. On merge, switch to `main`, pull, delete local branch
