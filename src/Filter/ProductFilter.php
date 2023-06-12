<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use ApiPlatform\Metadata\Operation;
use Symfony\Component\PropertyInfo\Type;

final class ProductFilter extends AbstractFilter
{

    private const PROPERTY_CHARACTERISTICS = 'characteristics';
    private const PROPERTY_DESCRIPTION = 'description';

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        // This filter will work with the 'characteristics'-query-parameter only.
        if ($property == self::PROPERTY_DESCRIPTION && !empty($value)) {
            $value = '%' . $value . '%';

            $queryBuilder
                ->andWhere("o.".self::PROPERTY_DESCRIPTION." LIKE :arg")
                ->setParameter('arg', $value);
        }
        if ($property == self::PROPERTY_CHARACTERISTICS) {
            $terms = explode(",", $value);
            foreach ($terms as $index => $term) {
                $val = '%' . $term . '%';
                $queryBuilder
                    ->andWhere("o.".self::PROPERTY_CHARACTERISTICS." LIKE :arg".$index)
                    ->setParameter('arg'.$index, $val);
            }
        }
    }

    // This function is only used to hook in documentation generators (supported by Swagger and Hydra)
    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description["$property"] = [
                'property' => $property,
                'required' => false,
                'type' => Type::BUILTIN_TYPE_STRING,
                // 'description' => 'FullCalendar filter!',
                'openapi' => [
                    // 'example' => 'string',
                    'allowReserved' => false, // if true, query parameters will be not percent-encoded
                    'allowEmptyValue' => true,
                    'explode' => false, // to be true, the type must be Type::BUILTIN_TYPE_ARRAY, ?product=blue,green will be ?product=blue&product=green
                ],
            ];
            switch ($property) {
                case self::PROPERTY_CHARACTERISTICS:
                    $description["$property"]['type'] = Type::BUILTIN_TYPE_ARRAY;
                    $description["$property"]['is_collection'] = true;
                    $description["$property"]['description'] = 'Format `"key":value`';
                    break;
                default:
                    break;
            }
        }

        return $description;
    }
}