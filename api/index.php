<?php

// Forward the request to the Laravel application entry point.
// This file is required by Vercel's serverless functions to route
// all incoming requests through Laravel's public/index.php.

require __DIR__ . '/../public/index.php';
