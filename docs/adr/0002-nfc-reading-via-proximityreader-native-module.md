# 2. NFC pass reading via a ProximityReader native module

Date: 2026-06-08

## Status
Accepted

## Context
The mobile app must read Apple Wallet NFC passes presented on another iPhone/iPad. NFC pass reading on iOS depends on Apple's ProximityReader framework, which is only reachable through native (Swift) code and the appropriate entitlements. JS-only or third-party React Native NFC libraries do not expose ProximityReader and cannot meet the requirement; relying on them would also obscure the project's central technical risk (whether ProximityReader can read a third-party-issued pass at all).

## Decision
All NFC pass reading must go through a custom Expo native module (Swift) that wraps the ProximityReader framework. We will not use JS-only or third-party NFC libraries for pass reading.

## Consequences
- The Expo app requires a custom development client / native build (it cannot run NFC reading in Expo Go).
- ProximityReader entitlements and supported pass types must be configured and validated early, directly exercising the project's riskiest unknown.
- A clear native ⇄ JS boundary: the Swift module decodes the pass and returns structured data (e.g. serial number) to React Native.
- Adding NFC capabilities later (e.g. additional pass types) means extending the native module, not swapping in a JS library.
