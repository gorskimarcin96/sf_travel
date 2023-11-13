<?php

namespace App\Utils\Crawler\Model;

trait URLTrait
{
    /**
     * @param URLInterface[] $models
     *
     * @return URLInterface[]
     */
    public function uniqueByUrl(array $models): array
    {
        $uniqueModels = [];

        foreach ($models as $model) {
            $uniqueModels[$model->getUrl()] = $model;
        }

        return array_values($uniqueModels);
    }
}
