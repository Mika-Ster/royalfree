<?php
// Configuration for auth (logic layer).
// Prefer loading sensitive values from environment variables in production.

// Admin email (change as needed or set ENV ADMIN_EMAIL)
$ADMIN_EMAIL = getenv('ADMIN_EMAIL') ?: 'admin@technikum-wien.at';

// Admin password hash: set ENV ADMIN_PASS_HASH to a password_hash(...) value.
// If empty, a demo hash for password "admin123" will be used (only for local/dev).
$ADMIN_PASS_HASH = getenv('ADMIN_PASS_HASH') ?: '';

// Other config placeholders (DB, etc.) can go here later.
