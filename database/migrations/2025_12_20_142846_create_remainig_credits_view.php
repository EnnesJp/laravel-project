<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('
            CREATE VIEW remaining_credits AS
            SELECT
                c.id AS credit_id,
                t.payee_user_id AS user_id,
                c.original_amount - IFNULL((SELECT SUM(d.amount) FROM debits d WHERE d.credit_id = c.id), 0) AS remaining
            FROM credits c
            JOIN transactions t ON t.id = c.transaction_id
            HAVING remaining > 0;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS remaining_credits;');
    }
};
