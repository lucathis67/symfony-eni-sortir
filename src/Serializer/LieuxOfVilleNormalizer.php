<?php

namespace App\Serializer;

use App\Entity\Ville;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class LieuxOfVilleNormalizer implements ContextAwareNormalizerInterface
{
    /**
     * @inheritDoc
     */
    public function normalize($ville, string $format = null, array $context = [])
    {
        $data = array();
        $datas = array();

        foreach ($ville->getLieux() as $lieu) {
            $data["id"] = $lieu->getId();
            $data["nom"] = $lieu->getNom();
            $data["rue"] = $lieu->getRue();
            $data["longitude"] = $lieu->getLongitude();
            $data["latitude"] = $lieu->getLatitude();
            $data["ville"]["id]"] = $lieu->getVille()->getId();
            $data["ville"]["codePostal"] = $lieu->getVille()->getCodePostal();
            $datas[] = $data;
        }
        return $datas;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Ville;
    }
}