<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("CREATE DEFINER=`root`@`localhost` PROCEDURE `sync_carnet_consultation`(IN p_consultation_id INT)
BEGIN
    INSERT INTO carnet_numerique (
        patient_id,
        type_enregistrement,
        description,
        date_enregistrement,
        medecin_id,
        hôpital,
        consultation_id
    )
    SELECT 
        patient_id,
        'consultation',
        CONCAT(diagnostic, ' - ', observations),
        date_consultation,
        medecin_id,
        centre_sante,
        consultation_id
    FROM consultations
    WHERE consultation_id = p_consultation_id
    AND NOT EXISTS (
        SELECT 1 FROM carnet_numerique 
        WHERE consultation_id = p_consultation_id
    );
END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sync_carnet_consultation");
    }
};
