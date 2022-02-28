<?php

namespace App\Data;


class SearchData
{
    public null|string $contient = null;
    public null|string $campus = null;
    public null|\DateTime $dateHeureDebut = null;
    public null|\DateTime $dateLimiteInscription = null;
    public bool $organisee = false;
    public bool $inscrit = false;
    public bool $nonInscrit = false;
    public bool $passees = false;
}