<?php

// No API routes are currently registered — this file is not wired into
// bootstrap/app.php's withRouting(), and previously contained a dead,
// unauthenticated route pointing at a controller method that no longer
// exists. If an API layer is added later, register it explicitly via
// withRouting(api: ...) and make sure every route carries appropriate
// auth/permission middleware from the start.
