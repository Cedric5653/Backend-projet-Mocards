<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // public function up(): void
    // {
    //     DB::statement("CREATE VIEW `v_patient_info` AS select `p`.`patient_id` AS `patient_id`,`p`.`nom` AS `nom`,`p`.`prenom` AS `prenom`,`p`.`date_naissance` AS `date_naissance`,`p`.`adresse` AS `adresse`,`p`.`telephone` AS `telephone`,`p`.`email` AS `email`,`p`.`groupe_sanguin` AS `groupe_sanguin`,`p`.`allergies` AS `allergies`,`p`.`maladies_chroniques` AS `maladies_chroniques`,`p`.`created_at` AS `created_at`,`p`.`updated_at` AS `updated_at`,`p`.`personne_contact` AS `personne_contact`,`p`.`contact_urgence` AS `contact_urgence`,`p`.`assurance_medicale` AS `assurance_medicale`,`p`.`numero_securite_sociale` AS `numero_securite_sociale`,`p`.`localisation_id` AS `localisation_id`,`p`.`lieu_naissance` AS `lieu_naissance`,`p`.`profession` AS `profession`,`p`.`electrophorese` AS `electrophorese`,`p`.`handicap` AS `handicap`,`p`.`vaccination_status` AS `vaccination_status`,`p`.`donneur_organes` AS `donneur_organes`,`l`.`region` AS `region`,`l`.`province` AS `province`,`l`.`ville` AS `ville`,`l`.`district_sanitaire` AS `district_sanitaire`,count(distinct `c`.`consultation_id`) AS `nb_consultations`,count(distinct `e`.`examen_id`) AS `nb_examens` from (((`db_sante`.`patients` `p` left join `db_sante`.`localisation` `l` on(`p`.`localisation_id` = `l`.`localisation_id`)) left join `db_sante`.`consultations` `c` on(`p`.`patient_id` = `c`.`patient_id`)) left join `db_sante`.`examens_laboratoire` `e` on(`p`.`patient_id` = `e`.`patient_id`)) group by `p`.`patient_id`");
    // }
    
    public function up()
    {
        DB::statement("
            CREATE VIEW `v_patient_info` AS 
            SELECT 
                p.patient_id,
                p.nom,
                p.prenom,
                p.date_naissance,
                p.adresse,
                p.telephone,
                p.email,
                p.groupe_sanguin,
                p.allergies,
                p.maladies_chroniques,
                p.created_at,
                p.updated_at,
                p.personne_contact,
                p.contact_urgence,
                p.assurance_medicale,
                p.numero_securite_sociale,
                p.localisation_id,
                p.lieu_naissance,
                p.profession,
                p.electrophorese,
                p.handicap,
                p.vaccination_status,
                p.donneur_organes,
                l.region,
                l.province,
                l.ville,
                l.district_sanitaire,
                COUNT(DISTINCT c.consultation_id) as nb_consultations,
                COUNT(DISTINCT e.examen_id) as nb_examens
            FROM patients p
            LEFT JOIN localisation l ON p.localisation_id = l.localisation_id
            LEFT JOIN consultations c ON p.patient_id = c.patient_id
            LEFT JOIN examens_laboratoire e ON p.patient_id = e.patient_id
            GROUP BY 
                p.patient_id, p.nom, p.prenom, p.date_naissance, p.adresse,
                p.telephone, p.email, p.groupe_sanguin, p.allergies, 
                p.maladies_chroniques, p.created_at, p.updated_at,
                p.personne_contact, p.contact_urgence, p.assurance_medicale,
                p.numero_securite_sociale, p.localisation_id, p.lieu_naissance,
                p.profession, p.electrophorese, p.handicap, p.vaccination_status,
                p.donneur_organes, l.region, l.province, l.ville, l.district_sanitaire
        ");
    }

    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `v_patient_info`");
    }
};
