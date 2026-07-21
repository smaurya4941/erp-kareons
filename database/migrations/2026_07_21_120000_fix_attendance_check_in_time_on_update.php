<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix the attendance check-in time being silently overwritten on every update.
 *
 * `check_in_time` was declared as the first NOT NULL TIMESTAMP column. With MySQL's
 * `explicit_defaults_for_timestamp = 0`, such a column implicitly receives
 * `DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP`. As a result, whenever the
 * attendance row was updated (i.e. at check-out) the database reset `check_in_time`
 * to the current time, making it equal to `check_out_time`.
 *
 * Converting the columns to DATETIME removes this special-case behaviour entirely
 * (DATETIME never gets the implicit ON UPDATE clause). We then reconstruct the
 * corrupted check-in times: `working_minutes` was calculated from the true check-in
 * at check-out, so `check_in_time = check_out_time - working_minutes`.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Stop future corruption: DATETIME has no implicit ON UPDATE CURRENT_TIMESTAMP.
        DB::statement("ALTER TABLE attendances MODIFY check_in_time DATETIME NOT NULL");
        DB::statement("ALTER TABLE attendances MODIFY check_out_time DATETIME NULL DEFAULT NULL");

        // 2. Repair rows whose check-in was overwritten. The stored working_minutes
        //    was derived from the original check-in, so it is authoritative.
        DB::statement("
            UPDATE attendances
            SET check_in_time = DATE_SUB(check_out_time, INTERVAL working_minutes MINUTE)
            WHERE check_out_time IS NOT NULL
              AND working_minutes IS NOT NULL
              AND working_minutes > 0
        ");
    }

    public function down(): void
    {
        // Revert column types. Note: this reintroduces MySQL's implicit ON UPDATE
        // behaviour on check_in_time, which is the bug this migration fixes.
        DB::statement("ALTER TABLE attendances MODIFY check_out_time TIMESTAMP NULL DEFAULT NULL");
        DB::statement("ALTER TABLE attendances MODIFY check_in_time TIMESTAMP NOT NULL");
    }
};
