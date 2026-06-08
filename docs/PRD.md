# eticket-poc — PRD

## Overview
A fullstack proof of concept. A Laravel backend creates Apple Wallet NFC passes via `spatie/laravel-mobile-pass`, and an Expo iOS app reads those passes through a custom native module wrapping Apple's ProximityReader framework, decoding and displaying the pass data (e.g. serial number).

## Goals
TODO: list concrete success criteria

## Non-Goals
- No production-ready auth
- No payment / transaction processing
- No Android support
- No real ticket-validation backend logic

## Users
TODO: describe target user

## Core Loop
Open the reader app, tap to read an NFC pass presented on another iPhone/iPad, then decode and display its contents (serial number, etc.).

## Milestones
- **M1 (MVP):** Laravel issues a valid Wallet NFC pass; the Expo app with the native ProximityReader module reads and displays decoded pass data end-to-end.
- **M2 (Polish):** Robust error handling, pass verification / signature checks, and a cleaner UI for decoded fields.
- **M3 (Ship):** Demo-ready build with documentation, multiple pass types, and a deployable backend.

## Open Questions
- Whether Apple's ProximityReader framework can actually read a third-party-issued Wallet NFC pass at all (entitlements, supported pass types, NFC access restrictions).
