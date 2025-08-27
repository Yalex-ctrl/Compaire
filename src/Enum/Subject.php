<?php

// src/Enum/Subject.php
namespace App\Enum;

enum Subject: string
{
    // Fondamentaux
    case FRANCAIS = 'Français';
    case MATHS = 'Maths';

    // Aide générale
    case AIDE_AUX_DEVOIRS = 'Aide aux devoirs';
    case SOUTIEN_SCOLAIRE = 'Soutien scolaire';

    // Langues vivantes
    case ANGLAIS = 'Anglais';
    case ESPAGNOL = 'Espagnol';
    case ALLEMAND = 'Allemand';
    case ITALIEN = 'Italien';
    case LATIN = 'Latin';

    // Sciences
    case PHYSIQUE_CHIMIE = 'Physique-Chimie';
    case SVT = 'Sciences de la Vie et de la Terre';
    case TECHNOLOGIE = 'Technologie';
    case INFORMATIQUE = 'Informatique';
    case NUMERIQUE_SCIENCES_INFORMATIQUES = 'Numérique et Sciences Informatiques';

    // Sciences humaines
    case HISTOIRE = 'Histoire';
    case GEOGRAPHIE = 'Géographie';
    case PHILOSOPHIE = 'Philosophie';

    // Économie & gestion
    case SES = 'Sciences Économiques et Sociales';

}

?>