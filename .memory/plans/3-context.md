# Context package for issue #3: Scaffold Expo app with iOS dev client

**Generated:** 2026-06-09T00:00:00Z
**Contract version:** 2

## Issue summary
Create the Expo mobile app configured to run on a physical iOS device via a custom dev client, with TypeScript and linting wired up.

## Linked issues
- #1 Scaffold Laravel backend with laravel-mobile-pass — closed — backend foundation dependency
- #2 Generate a signed Apple Wallet NFC pass via API — closed — backend API that mobile app will consume
- #4 Implement ProximityReader native module — not yet created — depends on this scaffolding

## Relevant ADRs
### ADR-0001: Record architecture decisions in ADRs
All non-obvious architectural decisions recorded in `docs/adr/` with explicit rationale. Agents and reviewers should grep ADRs before proposing patterns.

### ADR-0002: NFC pass reading via a ProximityReader native module
**Decision:** All NFC pass reading must go through a custom Expo native module (Swift) wrapping ProximityReader. We will NOT use JS-only or third-party NFC libraries. Consequences: Expo app requires dev client / native build (not Expo Go), and the Swift module must be developed and integrated early.

## Relevant PRD sections
### Core Loop
Open the reader app, tap to read an NFC pass presented on another iPhone/iPad, then decode and display its contents (serial number, etc.).

### Milestones
- **M1 (MVP):** Laravel issues a valid Wallet NFC pass; the Expo app with the native ProximityReader module reads and displays decoded pass data end-to-end.

## Glossary terms
- **NFC Pass** — an Apple Wallet pass with an NFC payload, presented by tapping a device.
- **ProximityReader** — Apple's iOS framework for reading contactless passes/cards on supported iPhones.
- **.pkpass** — the signed Apple Wallet pass bundle format produced by the backend.
- **Native Module** — the custom Expo/Swift bridge exposing ProximityReader to React Native JS.
- **Serial Number** — the unique per-pass identifier decoded and displayed by the reader app.

## Symbol references in codebase
- No existing mobile codebase yet (apps/mobile will be scaffolded under this issue).

## Recent related PRs
None — this is the first mobile scaffolding PR.

## Issue comments
No comments on the issue.

## Library documentation
### expo-dev-client
A framework for building and running custom Expo development clients. Allows native code integration without ejecting from Expo. Documentation: https://docs.expo.dev/development/create-development-builds/

### react-native
The core framework for iOS/Android app development via JavaScript. Documentation: https://reactnative.dev/

### TypeScript
Static type checking for JavaScript. Configuration via tsconfig.json. https://www.typescriptlang.org/

### ESLint + Prettier
Linting and code formatting. Configuration via .eslintrc.js and .prettierrc. https://eslint.org/ and https://prettier.io/

### Jest
JavaScript testing framework. Configuration via jest.config.js. https://jestjs.io/

## Gathering notes
- Codebase is new with no existing mobile files; `apps/` directory structure will be created.
- No existing package.json dependencies to inherit — Expo scaffolding will define all mobile dependencies.
- Library docs are summaries; full documentation available at the linked URLs and should be consulted during implementation.
